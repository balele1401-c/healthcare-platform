<?php

namespace App\Services\Payment;

use App\Enums\AppointmentStatus;
use App\Enums\PaymentStatus;
use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaymentService
{
    protected PaymentGatewayManager $manager;

    public function __construct(PaymentGatewayManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Create or retrieve an active payment checkout for a patient's appointment.
     */
    public function createPaymentForAppointment(
        Appointment $appointment,
        User $user,
        string $paymentMethod = 'qris',
        ?string $providerName = null,
        ?string $idempotencyKey = null
    ): Payment {
        $patient = $user->patient;

        // 1. Authorization: Patient must own the appointment or user is Admin
        if ($user->isPatient()) {
            if (! $patient || $appointment->patient_id !== $patient->id) {
                throw new AccessDeniedHttpException('You are not authorized to initiate payment for this appointment.');
            }
        }

        // 2. Validate appointment status
        if ($appointment->status === AppointmentStatus::CANCELLED) {
            throw new BadRequestHttpException('Cannot initiate payment for a cancelled appointment.');
        }

        if ($appointment->status === AppointmentStatus::COMPLETED) {
            throw new BadRequestHttpException('This appointment has already been completed.');
        }

        // 3. Idempotency Check: Return existing paid or pending payment
        $existingPayment = Payment::where('appointment_id', $appointment->id)
            ->whereIn('status', [PaymentStatus::PAID, PaymentStatus::PENDING, PaymentStatus::PROCESSING])
            ->latest()
            ->first();

        if ($existingPayment) {
            if ($existingPayment->isPaid()) {
                return $existingPayment;
            }

            if (! $existingPayment->isExpired() && $existingPayment->checkout_url) {
                return $existingPayment;
            }
        }

        // 4. Server-Side Amount Calculation
        $amount = (float) $appointment->total_amount;
        if ($amount <= 0) {
            $amount = (float) $appointment->consultation_fee + (float) $appointment->service_fee;
        }

        // Safe development test override if configured (ignored in production)
        $testAmount = config('payment.test_amount');
        if ($testAmount !== null && config('payment.mode') !== 'production') {
            $amount = (float) $testAmount;
        }

        $provider = $providerName ?: config('payment.default', 'sandbox');
        $gateway = $this->manager->driver($provider);

        $reference = 'PAY-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        $expirationMinutes = config('payment.expiration_minutes', 60);
        $expiredAt = now()->addMinutes($expirationMinutes);

        return DB::transaction(function () use ($appointment, $patient, $user, $amount, $paymentMethod, $provider, $gateway, $reference, $expiredAt, $idempotencyKey) {
            $payment = Payment::create([
                'user_id' => $user->id,
                'payment_reference' => $reference,
                'appointment_id' => $appointment->id,
                'patient_id' => $patient ? $patient->id : $appointment->patient_id,
                'amount' => $amount,
                'currency' => config('payment.currency', 'USD'),
                'payment_method' => $paymentMethod,
                'status' => PaymentStatus::PENDING,
                'provider' => $provider,
                'idempotency_key' => $idempotencyKey,
                'expired_at' => $expiredAt,
                'metadata' => [
                    'consultation_type' => $appointment->consultation_type?->value ?? 'in_person',
                    'doctor_name' => $appointment->doctor?->user?->name ?? 'Doctor',
                    'facility' => $appointment->facility,
                ],
            ]);

            // Call provider gateway
            $result = $gateway->createPayment($payment);

            if ($result->success) {
                $payment->update([
                    'provider_payment_id' => $result->providerPaymentId,
                    'checkout_url' => $result->checkoutUrl,
                    'metadata' => array_merge($payment->metadata ?? [], $result->metadata, [
                        'qr_string' => $result->qrString,
                        'virtual_account' => $result->virtualAccount,
                    ]),
                ]);
            } else {
                Log::warning('Payment gateway returned non-success result', [
                    'provider' => $provider,
                    'reference' => $reference,
                    'message' => $result->errorMessage,
                ]);

                // In sandbox / unconfigured mode fallback, provide safe local checkout
                if (config('payment.mode') !== 'production' && ! $payment->checkout_url) {
                    $payment->update([
                        'checkout_url' => url("/payments/{$payment->payment_reference}/checkout"),
                        'provider_payment_id' => 'DEV-' . $payment->payment_reference,
                    ]);
                }
            }

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'CREATE_PAYMENT',
                'entity_type' => 'Payment',
                'entity_id' => $payment->id,
                'new_data' => [
                    'reference' => $payment->payment_reference,
                    'amount' => $payment->amount,
                    'provider' => $provider,
                    'status' => $payment->status->value,
                ],
                'created_at' => now(),
            ]);

            return $payment->fresh(['appointment.doctor.specialty', 'patient.user']);
        });
    }

    /**
     * Handle incoming payment webhooks from any configured provider.
     */
    public function processWebhook(string $providerName, Request $request): JsonResponse
    {
        Log::info("Payment webhook received from [{$providerName}]", [
            'ip' => $request->ip(),
            'headers' => $request->headers->all(),
        ]);

        try {
            $gateway = $this->manager->driver($providerName);
        } catch (\Throwable $e) {
            Log::error("Unknown payment webhook provider [{$providerName}]");
            return response()->json(['success' => false, 'message' => "Unknown provider: {$providerName}"], 400);
        }

        $verification = $gateway->verifyWebhook($request);

        if (! $verification->isValid) {
            Log::warning("Invalid webhook signature or payload from [{$providerName}]", [
                'error' => $verification->errorMessage,
            ]);

            return response()->json([
                'success' => false,
                'message' => $verification->errorMessage ?? 'Invalid webhook signature or payload.',
            ], 400);
        }

        $payment = Payment::where('payment_reference', $verification->paymentReference)
            ->orWhere('provider_payment_id', $verification->providerPaymentId)
            ->first();

        if (! $payment) {
            Log::warning("Payment not found for webhook reference [{$verification->paymentReference}]");
            return response()->json([
                'success' => false,
                'message' => 'Payment record not found.',
            ], 404);
        }

        // Idempotency: If payment is already settled, acknowledge immediately
        if ($payment->isPaid() && $verification->status === PaymentStatus::PAID) {
            return response()->json([
                'success' => true,
                'message' => 'Payment already settled (idempotent).',
                'reference' => $payment->payment_reference,
            ], 200);
        }

        // State Transition Guard: Disallow reverting PAID to PENDING
        if ($payment->isPaid() && $verification->status === PaymentStatus::PENDING) {
            return response()->json([
                'success' => true,
                'message' => 'Ignored pending event for finalized payment.',
            ], 200);
        }

        DB::transaction(function () use ($payment, $verification, $providerName) {
            if ($verification->status === PaymentStatus::PAID) {
                $payment->markAsPaid(
                    $verification->providerPaymentId,
                    ['webhook_verified' => true, 'provider' => $providerName]
                );

                // Confirm appointment
                $appointment = $payment->appointment;
                if ($appointment && $appointment->status === AppointmentStatus::PENDING) {
                    $appointment->update(['status' => AppointmentStatus::CONFIRMED]);
                }

                AuditLog::create([
                    'user_id' => $payment->user_id,
                    'action' => 'WEBHOOK_PAYMENT_PAID',
                    'entity_type' => 'Payment',
                    'entity_id' => $payment->id,
                    'new_data' => [
                        'reference' => $payment->payment_reference,
                        'provider' => $providerName,
                        'amount' => $payment->amount,
                        'status' => 'paid',
                    ],
                    'created_at' => now(),
                ]);
            } elseif ($verification->status === PaymentStatus::EXPIRED) {
                $payment->markAsExpired();

                AuditLog::create([
                    'user_id' => $payment->user_id,
                    'action' => 'WEBHOOK_PAYMENT_EXPIRED',
                    'entity_type' => 'Payment',
                    'entity_id' => $payment->id,
                    'new_data' => ['reference' => $payment->payment_reference, 'status' => 'expired'],
                    'created_at' => now(),
                ]);
            } elseif ($verification->status === PaymentStatus::FAILED) {
                $payment->markAsFailed('Provider marked payment failed.');

                AuditLog::create([
                    'user_id' => $payment->user_id,
                    'action' => 'WEBHOOK_PAYMENT_FAILED',
                    'entity_type' => 'Payment',
                    'entity_id' => $payment->id,
                    'new_data' => ['reference' => $payment->payment_reference, 'status' => 'failed'],
                    'created_at' => now(),
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Payment webhook processed successfully.',
            'status' => $payment->fresh()->status->value,
        ], 200);
    }

    /**
     * Issue a refund for a previously paid payment.
     */
    public function processRefund(Payment $payment, User $actor, float $amount = null, string $reason = 'Requested by administrator'): Payment
    {
        // 1. Authorization: Only Admin or Billing Staff
        if (! $actor->isAdmin() && ! $actor->isStaff()) {
            throw new AccessDeniedHttpException('Only authorized staff or administrators can issue refunds.');
        }

        if ($actor->isStaff()) {
            $staff = $actor->staff;
            if (! $staff || ! str_contains(strtolower($staff->department ?? ''), 'billing')) {
                throw new AccessDeniedHttpException('Unauthorized. Only billing department staff can issue refunds.');
            }
        }

        // 2. Validate payment status
        if (! $payment->canBeRefunded()) {
            throw new BadRequestHttpException('Only paid payments can be refunded.');
        }

        $refundAmount = $amount ?: (float) $payment->amount;
        if ($refundAmount > (float) $payment->amount || $refundAmount <= 0) {
            throw new BadRequestHttpException('Invalid refund amount specified.');
        }

        $gateway = $this->manager->driver($payment->provider ?? 'sandbox');
        $refundResult = $gateway->refundPayment($payment, $refundAmount, $reason);

        return DB::transaction(function () use ($payment, $actor, $refundAmount, $reason, $refundResult) {
            $payment->markAsRefunded($reason, $refundAmount);

            AuditLog::create([
                'user_id' => $actor->id,
                'action' => 'REFUND_PAYMENT',
                'entity_type' => 'Payment',
                'entity_id' => $payment->id,
                'new_data' => [
                    'reference' => $payment->payment_reference,
                    'amount' => $refundAmount,
                    'reason' => $reason,
                    'refund_id' => $refundResult->refundId ?? null,
                ],
                'created_at' => now(),
            ]);

            return $payment->fresh(['appointment', 'patient.user']);
        });
    }

    /**
     * Expire overdue pending payments.
     */
    public function expireOverduePayments(): int
    {
        $expiredCount = 0;
        $overdue = Payment::whereIn('status', [PaymentStatus::PENDING, PaymentStatus::PROCESSING])
            ->whereNotNull('expired_at')
            ->where('expired_at', '<', now())
            ->get();

        foreach ($overdue as $payment) {
            $payment->markAsExpired();
            $expiredCount++;
        }

        return $expiredCount;
    }
}
