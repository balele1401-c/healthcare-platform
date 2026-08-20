<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\PaymentResource;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Payment;
use App\Services\Payment\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * List payment transaction records for the authenticated patient or authorized staff/admin.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->isDoctor()) {
            return $this->errorResponse('Practitioners are not authorized to access billing ledger.', 403);
        }

        if ($user->isStaff()) {
            $staff = $user->staff;
            if (! $staff || ! str_contains(strtolower($staff->department ?? ''), 'billing')) {
                return $this->errorResponse('Unauthorized to access financial payment records.', 403);
            }
        }

        $query = Payment::query()->with(['appointment.doctor.specialty', 'patient.user']);

        if ($user->isPatient()) {
            $patient = $user->patient ?? Patient::create(['user_id' => $user->id]);
            $query->where('patient_id', $patient->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        $perPage = min((int) $request->query('per_page', 15), 50);
        $payments = $query->latest()->paginate($perPage);

        return $this->paginatedResponse(
            PaymentResource::collection($payments),
            'Payment records retrieved.'
        );
    }

    /**
     * Create a new payment checkout order for an appointment.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'appointment_id' => ['required', 'integer', 'exists:appointments,id'],
            'payment_method' => ['nullable', 'string', Rule::in(['qris', 'virtual_account', 'e_wallet', 'bank_transfer', 'credit_card'])],
            'provider' => ['nullable', 'string', Rule::in(['sandbox', 'mayar', 'komerce'])],
        ]);

        $appointment = Appointment::with(['doctor', 'patient'])->findOrFail($validated['appointment_id']);
        $paymentMethod = $validated['payment_method'] ?? 'qris';
        $provider = $validated['provider'] ?? null;
        $idempotencyKey = $request->header('X-Idempotency-Key');

        try {
            $payment = $this->paymentService->createPaymentForAppointment(
                $appointment,
                $request->user(),
                $paymentMethod,
                $provider,
                $idempotencyKey
            );

            return $this->successResponse(
                new PaymentResource($payment),
                'Payment order created successfully.',
                201
            );
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());
        } catch (\Throwable $e) {
            return $this->errorResponse('Unable to initialize payment checkout.', 500);
        }
    }

    /**
     * Retrieve a specific payment receipt record.
     */
    public function show(Payment $payment, Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->isDoctor()) {
            return $this->errorResponse('Practitioners are not authorized to view financial transaction data.', 403);
        }

        if ($user->isPatient()) {
            $patient = $user->patient;
            if (! $patient || $payment->patient_id !== $patient->id) {
                return $this->errorResponse('Unauthorized to access this payment receipt.', 403);
            }
        }

        if ($user->isStaff()) {
            $staff = $user->staff;
            if (! $staff || ! str_contains(strtolower($staff->department ?? ''), 'billing')) {
                return $this->errorResponse('Unauthorized to view this billing record.', 403);
            }
        }

        $payment->load(['appointment.doctor.specialty', 'patient.user']);

        return $this->successResponse(
            new PaymentResource($payment),
            'Payment record retrieved.'
        );
    }

    /**
     * Issue a refund for a paid transaction (Admin / Billing Staff only).
     */
    public function refund(Payment $payment, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:255'],
            'amount' => ['nullable', 'numeric', 'min:0.01'],
        ]);

        try {
            $refundedPayment = $this->paymentService->processRefund(
                $payment,
                $request->user(),
                $validated['amount'] ?? null,
                $validated['reason']
            );

            return $this->successResponse(
                new PaymentResource($refundedPayment),
                'Payment refunded successfully.'
            );
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());
        } catch (\Throwable $e) {
            return $this->errorResponse('Unable to process refund.', 500);
        }
    }

    /**
     * Provider-neutral payment webhook endpoint.
     */
    public function webhook(string $provider, Request $request): JsonResponse
    {
        return $this->paymentService->processWebhook($provider, $request);
    }
}
