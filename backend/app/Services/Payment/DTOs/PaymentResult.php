<?php

namespace App\Services\Payment\DTOs;

use App\Enums\PaymentStatus;

class PaymentResult
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $providerPaymentId = null,
        public readonly ?string $checkoutUrl = null,
        public readonly ?string $qrString = null,
        public readonly ?string $virtualAccount = null,
        public readonly PaymentStatus $status = PaymentStatus::PENDING,
        public readonly array $metadata = [],
        public readonly ?string $errorMessage = null,
    ) {}

    public static function successful(
        string $providerPaymentId,
        ?string $checkoutUrl = null,
        ?string $qrString = null,
        ?string $virtualAccount = null,
        array $metadata = []
    ): self {
        return new self(
            success: true,
            providerPaymentId: $providerPaymentId,
            checkoutUrl: $checkoutUrl,
            qrString: $qrString,
            virtualAccount: $virtualAccount,
            status: PaymentStatus::PENDING,
            metadata: $metadata,
        );
    }

    public static function failed(string $errorMessage, array $metadata = []): self
    {
        return new self(
            success: false,
            status: PaymentStatus::FAILED,
            metadata: $metadata,
            errorMessage: $errorMessage,
        );
    }
}
