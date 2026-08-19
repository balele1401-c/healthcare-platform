<?php

namespace App\Policies;

use App\Models\MedicalRecord;
use App\Models\User;

class MedicalRecordPolicy
{
    public function view(User $user, MedicalRecord $record): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isDoctor()) {
            return $record->doctor && $record->doctor->user_id === $user->id;
        }

        if ($user->isPatient()) {
            return $record->patient && $record->patient->user_id === $user->id;
        }

        // Staff members do NOT have clinical medical record access
        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isDoctor();
    }

    public function update(User $user, MedicalRecord $record): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isDoctor() && $record->doctor && $record->doctor->user_id === $user->id;
    }

    public function delete(User $user, MedicalRecord $record): bool
    {
        return $user->isAdmin();
    }
}
