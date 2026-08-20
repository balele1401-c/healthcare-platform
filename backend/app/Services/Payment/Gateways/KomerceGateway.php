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

class KomerceGateway implements PaymentGatewayInterface
{
    protected array $config;
    protected ?string $apiKey;
    protected string $baseUrl;
    protected ?string $webhookSecret;

    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->apiKey = $config['api_key'] ?? config('payment.providers.komerce.api_key');
        $this->baseUrl = rtrim($config['base_url'] ?? config('payment.providers.komerce.base_url', 'https://api.komerce.id/v1'), '/');
        $this->webhookSecret = $config['webhook_secret'] ?? config('payment.providers.komerce.webhook_secret');
    }

    public function getProviderName(): string
    {
        return 'komerce';
    }

    public function isAvailable(): bool
    {
        return ! empty($this->apiKey);
    }

    public function createPayment(Payment $payment, array $options = []): PaymentResult
    {
        if (! $this->isAvailable()) {
            Log::warning('Komerce gateway called without active credentials (pending provider approval).');
            return PaymentResult::failed('Payment service is currently unavailable.');
        }

        try {
            $appointment = $payment->appointment;
            $patient = $payment->patient;
            $customerName = $patient?->user?->name ?? 'Patient';
            $customerEmail = $patient?->user?->email ?? 'patient@healthcare.local';

            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(15)->post("{$this->baseUrl}/payments/create-link", [
                'order_id' => $payment->payment_reference,
                'gross_amount' => (int) round($payment->amount),
                'customer_details' => [
                    'name' => $customerName,
                    'email' => $customerEmail,
                ],
                'item_details' => [
                    [
                        'id' => "APT-{$payment->appointment_id}",
                        'price' => (int) round($payment->amount),
                        'quantity' => 1,
                        'name' => 'Doctor Consultation Service',
                    ],
                ],
                'expiry_duration' => 60,
                'callback_url' => url("/payments/{$payment->payment_reference}/callback"),
            ]);

            if ($response->successful()) {
                $data = $response->json('data') ?? $response->json();
                $providerPaymentId = $data['transaction_id'] ?? $data['id'] ?? $payment->payment_reference;
                $checkoutUrl = $data['payment_url'] ?? $data['checkout_url'] ?? null;
                $qrString = $data['qr_code'] ?? null;
                $virtualAccount = $data['va_number'] ?? null;

                return PaymentResult::successful(
                    providerPaymentId: (string) $providerPaymentId,
                    checkoutUrl: $checkoutUrl,
                    qrString: $qrString,
                    virtualAccount: $virtualAccount,
                    metadata: [
                        'provider' => 'komerce',
                        'raw_id' => $providerPaymentId,
                    ]
                );
            }

            Log::error('Komerce API error during payment creation', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            return PaymentResult::failed('Payment service is currently unavailable.');
        } catch (\Throwable $e) {
            Log::error('Komerce connection exception', ['error' => $e->getMessage()]);
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
                'X-API-Key' => $this->apiKey,
            ])->timeout(10)->get("{$this->baseUrl}/payments/{$providerPaymentId}/status");

            if ($response->successful()) {
                $data = $response->json('data') ?? $response->json();
                $komerceStatus = strtoupper($data['transaction_status'] ?? $data['status'] ?? '');
                $status = match ($komerceStatus) {
                    'PAID', 'SETTLEMENT', 'SUCCESS', 'COMPLETED' => PaymentStatus::PAID,
                    'EXPIRED' => PaymentStatus::EXPIRED,
                    'FAILED', 'DENY', 'CANCEL' => PaymentStatus::FAILED,
                    'REFUND' => PaymentStatus::REFUNDED,
                    default => PaymentStatus::PENDING,
                };

                $paidAt = ! empty($data['paid_at']) ? Carbon::parse($data['paid_at']) : null;

                return PaymentStatusResult::successful(
                    status: $status,
                    providerStatus: $komerceStatus,
                    paidAt: $paidAt,
                    rawPayload: $data
                );
            }

            return PaymentStatusResult::failed('Unable to verify payment status with Komerce.');
        } catch (\Throwable $e) {
            Log::error('Komerce status check exception', ['error' => $e->getMessage()]);
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
            return RefundResult::failed('Komerce payment gateway is not configured for automatic refunds.');
        }

        return RefundResult::failed('Komerce provider refund is currently operating in manual review mode.');
    }

    public function verifyWebhook(Request $request): WebhookVerificationResult
    {
        $payload = $request->all();
        $signature = $request->header('X-Komerce-Signature') ?? $request->header('x-komerce-signature');

        if ($this->webhookSecret && $signature) {
            $expectedSignature = hash_hmac('sha256', $request->getContent(), $this->webhookSecret);
            if (! hash_equals($expectedSignature, $signature)) {
                return WebhookVerificationResult::invalid('Invalid Komerce webhook signature.', $payload);
            }
        }

        $data = $payload['data'] ?? $payload;
        $paymentReference = $data['order_id'] ?? $data['payment_reference'] ?? $data['reference'] ?? null;
        $providerPaymentId = $data['transaction_id'] ?? $data['id'] ?? null;
        $eventStatus = strtoupper($data['transaction_status'] ?? $data['status'] ?? '');

        $status = match ($eventStatus) {
            'PAID', 'SETTLEMENT', 'SUCCESS', 'COMPLETED' => PaymentStatus::PAID,
            'EXPIRED' => PaymentStatus::EXPIRED,
            'FAILED', 'DENY', 'CANCEL' => PaymentStatus::FAILED,
            'REFUND' => PaymentStatus::REFUNDED,
            default => PaymentStatus::PENDING,
        };

        $amount = isset($data['gross_amount']) ? (float) $data['gross_amount'] : null;
        $paidAt = isset($data['paid_at']) ? Carbon::parse($data['paid_at']) : now();

        if (! $paymentReference && ! $providerPaymentId) {
            return WebhookVerificationResult::invalid('Missing order_id in Komerce payload.', $payload);
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
