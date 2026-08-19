<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    public function view(User $user, Appointment $appointment): bool
    {
        if ($user->isAdmin() || $user->isStaff()) {
            return true;
        }

        if ($user->isDoctor()) {
            return $appointment->doctor && $appointment->doctor->user_id === $user->id;
        }

        if ($user->isPatient()) {
            return $appointment->patient && $appointment->patient->user_id === $user->id;
        }

        return false;
    }

    public function update(User $user, Appointment $appointment): bool
    {
        if ($user->isAdmin() || $user->isStaff()) {
            return true;
        }

        if ($user->isDoctor()) {
            return $appointment->doctor && $appointment->doctor->user_id === $user->id;
        }

        if ($user->isPatient()) {
            return $appointment->patient && $appointment->patient->user_id === $user->id;
        }

        return false;
    }

    public function cancel(User $user, Appointment $appointment): bool
    {
        return $this->update($user, $appointment);
    }
}
