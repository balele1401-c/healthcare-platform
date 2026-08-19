<?php

namespace App\Enums;

enum PrescriptionStatus: string
{
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
    case EXPIRED = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active Prescription',
            self::COMPLETED => 'Completed Regimen',
            self::EXPIRED => 'Expired',
        };
    }
}
