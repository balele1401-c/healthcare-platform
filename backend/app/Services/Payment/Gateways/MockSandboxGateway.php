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
use Illuminate\Support\Str;

class MockSandboxGateway implements PaymentGatewayInterface
{
    protected array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function getProviderName(): string
    {
        return 'sandbox';
    }

    public function isAvailable(): bool
    {
        return true;
    }

    public function createPayment(Payment $payment, array $options = []): PaymentResult
    {
        $providerPaymentId = 'SANDBOX-' . strtoupper(Str::random(12));
        $checkoutUrl = url("/sandbox/payments/checkout/{$payment->payment_reference}");
        $virtualAccount = '8800' . str_pad((string) $payment->id, 8, '0', STR_PAD_LEFT);
        $qrString = "00020101021226580016ID.HEALTHCARE.QR0118{$payment->payment_reference}5204541153033605802ID5914HEALTHCARE6007JAKARTA6304ABCD";

        return PaymentResult::successful(
            providerPaymentId: $providerPaymentId,
            checkoutUrl: $checkoutUrl,
            qrString: $qrString,
            virtualAccount: $virtualAccount,
            metadata: [
                'mode' => 'sandbox',
                'gateway' => 'MockSandboxGateway',
                'reference' => $payment->payment_reference,
            ]
        );
    }

    public function getPaymentStatus(string $providerPaymentId, Payment $payment): PaymentStatusResult
    {
        return PaymentStatusResult::successful(
            status: $payment->status,
            providerStatus: strtoupper($payment->status->value),
            paidAt: $payment->paid_at,
            rawPayload: [
                'provider_payment_id' => $providerPaymentId,
                'reference' => $payment->payment_reference,
                'status' => $payment->status->value,
            ]
        );
    }

    public function cancelPayment(Payment $payment, string $reason = ''): bool
    {
        return true;
    }

    public function refundPayment(Payment $payment, float $amount, string $reason = ''): RefundResult
    {
        $refundId = 'REFUND-SANDBOX-' . strtoupper(Str::random(10));

        return RefundResult::successful(
            refundId: $refundId,
            amount: $amount,
            rawPayload: [
                'refund_id' => $refundId,
                'payment_reference' => $payment->payment_reference,
                'amount' => $amount,
                'reason' => $reason,
            ]
        );
    }

    public function verifyWebhook(Request $request): WebhookVerificationResult
    {
        $payload = $request->all();
        $signature = $request->header('X-Sandbox-Signature') ?? $request->header('X-Webhook-Signature');
        $secret = $this->config['webhook_secret'] ?? config('payment.providers.sandbox.webhook_secret', 'sandbox_webhook_secret_key');

        // Optional signature verification if header is present
        if ($signature && $secret) {
            $computed = hash_hmac('sha256', $request->getContent(), $secret);
            if (! hash_equals($computed, $signature)) {
                return WebhookVerificationResult::invalid('Invalid sandbox webhook signature.', $payload);
            }
        }

        $paymentReference = $payload['payment_reference'] ?? $payload['reference'] ?? null;
        $providerPaymentId = $payload['provider_payment_id'] ?? $payload['id'] ?? null;
        $statusString = strtolower($payload['status'] ?? 'paid');

        if (! $paymentReference && ! $providerPaymentId) {
            return WebhookVerificationResult::invalid('Missing payment reference in webhook payload.', $payload);
        }

        $status = match ($statusString) {
            'paid', 'settled', 'success', 'completed' => PaymentStatus::PAID,
            'expired', 'timeout' => PaymentStatus::EXPIRED,
            'failed', 'denied' => PaymentStatus::FAILED,
            'refunded' => PaymentStatus::REFUNDED,
            'processing' => PaymentStatus::PROCESSING,
            default => PaymentStatus::PENDING,
        };

        $amount = isset($payload['amount']) ? (float) $payload['amount'] : null;
        $paidAt = isset($payload['paid_at']) ? Carbon::parse($payload['paid_at']) : now();

        return WebhookVerificationResult::valid(
            paymentReference: (string) $paymentReference,
            providerPaymentId: $providerPaymentId ? (string) $providerPaymentId : null,
            status: $status,
            amount: $amount,
            paidAt: $paidAt,
            payload: $payload
        );
    }
}
