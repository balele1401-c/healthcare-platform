<?php

namespace App\Services\Payment\DTOs;

use App\Enums\PaymentStatus;

class RefundResult
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $refundId = null,
        public readonly float $amount = 0.0,
        public readonly PaymentStatus $status = PaymentStatus::REFUNDED,
        public readonly array $rawPayload = [],
        public readonly ?string $errorMessage = null,
    ) {}

    public static function successful(
        string $refundId,
        float $amount,
        array $rawPayload = []
    ): self {
        return new self(
            success: true,
            refundId: $refundId,
            amount: $amount,
            status: PaymentStatus::REFUNDED,
            rawPayload: $rawPayload,
        );
    }

    public static function failed(string $errorMessage, array $rawPayload = []): self
    {
        return new self(
            success: false,
            rawPayload: $rawPayload,
            errorMessage: $errorMessage,
        );
    }
}
