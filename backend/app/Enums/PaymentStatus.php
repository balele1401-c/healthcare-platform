<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case PAID = 'paid';
    case FAILED = 'failed';
    case EXPIRED = 'expired';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending Payment',
            self::PROCESSING => 'Processing Payment',
            self::PAID => 'Paid & Settled',
            self::FAILED => 'Payment Failed',
            self::EXPIRED => 'Payment Expired',
            self::CANCELLED => 'Cancelled',
            self::REFUNDED => 'Refunded',
        };
    }

    /**
     * Determine if payment is finalized and settled.
     */
    public function isFinalized(): bool
    {
        return in_array($this, [self::PAID, self::REFUNDED, self::CANCELLED, self::EXPIRED], true);
    }
}
