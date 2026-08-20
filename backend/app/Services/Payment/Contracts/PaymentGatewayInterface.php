<?php

namespace App\Services\Payment\Contracts;

use App\Models\Payment;
use App\Services\Payment\DTOs\PaymentResult;
use App\Services\Payment\DTOs\PaymentStatusResult;
use App\Services\Payment\DTOs\RefundResult;
use App\Services\Payment\DTOs\WebhookVerificationResult;
use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    /**
     * Create a payment order/invoice with the payment provider.
     */
    public function createPayment(Payment $payment, array $options = []): PaymentResult;

    /**
     * Query the payment status from the external provider.
     */
    public function getPaymentStatus(string $providerPaymentId, Payment $payment): PaymentStatusResult;

    /**
     * Cancel an active or pending payment invoice.
     */
    public function cancelPayment(Payment $payment, string $reason = ''): bool;

    /**
     * Issue a refund for a previously settled payment.
     */
    public function refundPayment(Payment $payment, float $amount, string $reason = ''): RefundResult;

    /**
     * Verify and parse an incoming webhook request from the payment provider.
     */
    public function verifyWebhook(Request $request): WebhookVerificationResult;

    /**
     * Get the identifier name of this payment provider (e.g., 'mayar', 'komerce', 'sandbox').
     */
    public function getProviderName(): string;

    /**
     * Check if the gateway credentials and service are currently active and available.
     */
    public function isAvailable(): bool;
}
