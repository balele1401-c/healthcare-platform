<?php

namespace App\Services\Payment\DTOs;

use App\Enums\PaymentStatus;
use Carbon\Carbon;

class WebhookVerificationResult
{
    public function __construct(
        public readonly bool $isValid,
        public readonly ?string $paymentReference = null,
        public readonly ?string $providerPaymentId = null,
        public readonly PaymentStatus $status = PaymentStatus::PENDING,
        public readonly ?float $amount = null,
        public readonly ?Carbon $paidAt = null,
        public readonly array $payload = [],
        public readonly ?string $errorMessage = null,
    ) {}

    public static function valid(
        string $paymentReference,
        ?string $providerPaymentId,
        PaymentStatus $status,
        ?float $amount = null,
        ?Carbon $paidAt = null,
        array $payload = []
    ): self {
        return new self(
            isValid: true,
            paymentReference: $paymentReference,
            providerPaymentId: $providerPaymentId,
            status: $status,
            amount: $amount,
            paidAt: $paidAt ?? now(),
            payload: $payload,
        );
    }

    public static function invalid(string $errorMessage, array $payload = []): self
    {
        return new self(
            isValid: false,
            status: PaymentStatus::FAILED,
            payload: $payload,
            errorMessage: $errorMessage,
        );
    }
}
