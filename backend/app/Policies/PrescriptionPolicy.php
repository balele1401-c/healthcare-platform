<?php

namespace App\Policies;

use App\Models\Prescription;
use App\Models\User;

class PrescriptionPolicy
{
    public function view(User $user, Prescription $prescription): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isDoctor()) {
            return $prescription->doctor && $prescription->doctor->user_id === $user->id;
        }

        if ($user->isPatient()) {
            return $prescription->patient && $prescription->patient->user_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isDoctor();
    }

    public function update(User $user, Prescription $prescription): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isDoctor() && $prescription->doctor && $prescription->doctor->user_id === $user->id;
    }
}
