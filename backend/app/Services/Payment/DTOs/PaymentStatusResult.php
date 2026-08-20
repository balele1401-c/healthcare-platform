<?php

namespace App\Services\Payment\DTOs;

use App\Enums\PaymentStatus;
use Carbon\Carbon;

class PaymentStatusResult
{
    public function __construct(
        public readonly bool $success,
        public readonly PaymentStatus $status,
        public readonly ?string $providerStatus = null,
        public readonly ?Carbon $paidAt = null,
        public readonly array $rawPayload = [],
        public readonly ?string $errorMessage = null,
    ) {}

    public static function successful(
        PaymentStatus $status,
        ?string $providerStatus = null,
        ?Carbon $paidAt = null,
        array $rawPayload = []
    ): self {
        return new self(
            success: true,
            status: $status,
            providerStatus: $providerStatus,
            paidAt: $paidAt,
            rawPayload: $rawPayload,
        );
    }

    public static function failed(string $errorMessage, array $rawPayload = []): self
    {
        return new self(
            success: false,
            status: PaymentStatus::FAILED,
            rawPayload: $rawPayload,
            errorMessage: $errorMessage,
        );
    }
}
