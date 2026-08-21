<?php

namespace App\Enums;

enum UserRole: string
{
    case PATIENT = 'patient';
    case DOCTOR = 'doctor';
    case STAFF = 'staff';
    case ADMIN = 'admin';
    case OWNER = 'owner';

    public function label(): string
    {
        return match ($this) {
            self::PATIENT => 'Patient',
            self::DOCTOR => 'Doctor',
            self::STAFF => 'Staff',
            self::ADMIN => 'Administrator',
            self::OWNER => 'Owner',
        };
    }
}
