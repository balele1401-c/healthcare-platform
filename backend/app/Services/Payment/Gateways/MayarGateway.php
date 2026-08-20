<?php

namespace App\Services\Payment\Gateways;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use App\Services\Payment\DTOs\PaymentResult;
use App\Services\Payment\DTOs\PaymentStatusResult;
use App\Services\Payment\DTOs\RefundResult;
use App\Services\Payment\DTOs\WebhookVerificationResult;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MayarGateway implements PaymentGatewayInterface
{
    protected array $config;
    protected ?string $apiKey;
    protected string $baseUrl;
    protected ?string $webhookSecret;

    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->apiKey = $config['api_key'] ?? config('payment.providers.mayar.api_key');
        $this->baseUrl = rtrim($config['base_url'] ?? config('payment.providers.mayar.base_url', 'https://api.mayar.id/hl/v1'), '/');
        $this->webhookSecret = $config['webhook_secret'] ?? config('payment.providers.mayar.webhook_secret');
    }

    public function getProviderName(): string
    {
        return 'mayar';
    }

    public function isAvailable(): bool
    {
        return ! empty($this->apiKey);
    }

    public function createPayment(Payment $payment, array $options = []): PaymentResult
    {
        if (! $this->isAvailable()) {
            Log::warning('Mayar gateway called without active credentials (pending provider approval).');
            return PaymentResult::failed('Payment service is currently unavailable.');
        }

        try {
            $appointment = $payment->appointment;
            $patient = $payment->patient;
            $customerName = $patient?->user?->name ?? 'Patient';
            $customerEmail = $patient?->user?->email ?? 'patient@healthcare.local';
            $customerPhone = $patient?->emergency_contact_phone ?? '08123456789';

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(15)->post("{$this->baseUrl}/payment/create", [
                'amount' => (int) round($payment->amount),
                'name' => "Medical Consultation #{$payment->payment_reference}",
                'email' => $customerEmail,
                'mobile' => $customerPhone,
                'description' => "Consultation with Dr. " . ($appointment?->doctor?->user?->name ?? 'Specialist'),
                'redirectUrl' => url("/payments/{$payment->payment_reference}/callback"),
                'expiredAt' => $payment->expired_at?->toIso8601String() ?? now()->addHours(1)->toIso8601String(),
                'extraData' => [
                    'payment_reference' => $payment->payment_reference,
                    'appointment_id' => $payment->appointment_id,
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json('data') ?? [];
                $providerPaymentId = $data['id'] ?? $data['paymentId'] ?? $data['linkId'] ?? $payment->payment_reference;
                $checkoutUrl = $data['link'] ?? $data['url'] ?? $data['paymentUrl'] ?? null;
                $qrString = $data['qrCode'] ?? null;
                $virtualAccount = $data['vaNumber'] ?? null;

                return PaymentResult::successful(
                    providerPaymentId: (string) $providerPaymentId,
                    checkoutUrl: $checkoutUrl,
                    qrString: $qrString,
                    virtualAccount: $virtualAccount,
                    metadata: [
                        'provider' => 'mayar',
                        'raw_id' => $providerPaymentId,
                    ]
                );
            }

            Log::error('Mayar API error during payment creation', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            return PaymentResult::failed('Payment service is currently unavailable.');
        } catch (\Throwable $e) {
            Log::error('Mayar connection exception', ['error' => $e->getMessage()]);
            return PaymentResult::failed('Payment service is currently unavailable.');
        }
    }

    public function getPaymentStatus(string $providerPaymentId, Payment $payment): PaymentStatusResult
    {
        if (! $this->isAvailable()) {
            return PaymentStatusResult::failed('Payment service is currently unavailable.');
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->timeout(10)->get("{$this->baseUrl}/payment/{$providerPaymentId}");

            if ($response->successful()) {
                $data = $response->json('data') ?? [];
                $mayarStatus = strtoupper($data['status'] ?? '');
                $status = match ($mayarStatus) {
                    'PAID', 'SETTLED', 'SUCCESS' => PaymentStatus::PAID,
                    'EXPIRED' => PaymentStatus::EXPIRED,
                    'FAILED', 'DENIED' => PaymentStatus::FAILED,
                    'REFUNDED' => PaymentStatus::REFUNDED,
                    default => PaymentStatus::PENDING,
                };

                $paidAt = ! empty($data['paidAt']) ? Carbon::parse($data['paidAt']) : null;

                return PaymentStatusResult::successful(
                    status: $status,
                    providerStatus: $mayarStatus,
                    paidAt: $paidAt,
                    rawPayload: $data
                );
            }

            return PaymentStatusResult::failed('Unable to verify payment status with Mayar.');
        } catch (\Throwable $e) {
            Log::error('Mayar status check exception', ['error' => $e->getMessage()]);
            return PaymentStatusResult::failed('Payment service is currently unavailable.');
        }
    }

    public function cancelPayment(Payment $payment, string $reason = ''): bool
    {
        return true;
    }

    public function refundPayment(Payment $payment, float $amount, string $reason = ''): RefundResult
    {
        if (! $this->isAvailable()) {
            return RefundResult::failed('Mayar payment gateway is not configured for automatic refunds.');
        }

        return RefundResult::failed('Mayar provider refund is currently operating in manual review mode.');
    }

    public function verifyWebhook(Request $request): WebhookVerificationResult
    {
        $payload = $request->all();
        $signature = $request->header('X-Mayar-Signature') ?? $request->header('x-mayar-signature');

        if ($this->webhookSecret && $signature) {
            $expectedSignature = hash_hmac('sha256', $request->getContent(), $this->webhookSecret);
            if (! hash_equals($expectedSignature, $signature)) {
                return WebhookVerificationResult::invalid('Invalid Mayar webhook signature.', $payload);
            }
        }

        $data = $payload['data'] ?? $payload;
        $paymentReference = $data['extraData']['payment_reference'] ?? $data['payment_reference'] ?? $data['reference'] ?? null;
        $providerPaymentId = $data['id'] ?? $data['paymentId'] ?? null;
        $eventStatus = strtoupper($data['status'] ?? $payload['event'] ?? '');

        $status = match ($eventStatus) {
            'PAID', 'PAYMENT_SUCCESS', 'SETTLED' => PaymentStatus::PAID,
            'EXPIRED', 'PAYMENT_EXPIRED' => PaymentStatus::EXPIRED,
            'FAILED', 'PAYMENT_FAILED' => PaymentStatus::FAILED,
            'REFUNDED' => PaymentStatus::REFUNDED,
            default => PaymentStatus::PENDING,
        };

        $amount = isset($data['amount']) ? (float) $data['amount'] : null;
        $paidAt = isset($data['paidAt']) ? Carbon::parse($data['paidAt']) : now();

        if (! $paymentReference && ! $providerPaymentId) {
            return WebhookVerificationResult::invalid('Missing payment reference in Mayar payload.', $payload);
        }

        return WebhookVerificationResult::valid(
            paymentReference: (string) ($paymentReference ?? $providerPaymentId),
            providerPaymentId: $providerPaymentId ? (string) $providerPaymentId : null,
            status: $status,
            amount: $amount,
            paidAt: $paidAt,
            payload: $payload
        );
    }
}
