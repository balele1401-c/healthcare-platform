<?php

namespace App\Enums;

enum AppointmentStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case IN_CONSULTATION = 'in_consultation';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case REJECTED = 'rejected';
    case EXPIRED = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending Confirmation',
            self::CONFIRMED => 'Confirmed',
            self::IN_CONSULTATION => 'In Consultation',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
            self::REJECTED => 'Rejected by Provider',
            self::EXPIRED => 'Expired',
        };
    }

    /**
     * Determine if a state transition from current status to target status is valid.
     */
    public function canTransitionTo(self $target): bool
    {
        if ($this->isTerminal()) {
            return false;
        }

        if ($this === $target) {
            return true;
        }

        return match ($this) {
            self::PENDING => in_array($target, [self::CONFIRMED, self::CANCELLED, self::REJECTED, self::EXPIRED], true),
            self::CONFIRMED => in_array($target, [self::IN_CONSULTATION, self::COMPLETED, self::CANCELLED], true),
            self::IN_CONSULTATION => in_array($target, [self::COMPLETED, self::CANCELLED], true),
            default => false,
        };
    }

    /**
     * Check if status is a finalized terminal state.
     */
    public function isTerminal(): bool
    {
        return in_array($this, [self::COMPLETED, self::CANCELLED, self::REJECTED, self::EXPIRED], true);
    }
}
