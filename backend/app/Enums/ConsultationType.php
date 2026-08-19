<?php

namespace App\Enums;

enum ConsultationType: string
{
    case IN_PERSON = 'in_person';
    case ONLINE = 'online';

    public function label(): string
    {
        return match ($this) {
            self::IN_PERSON => 'In-Person Clinic Visit',
            self::ONLINE => 'Online Video Consultation',
        };
    }
}
