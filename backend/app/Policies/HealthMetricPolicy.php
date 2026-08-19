<?php

namespace App\Policies;

use App\Models\HealthMetric;
use App\Models\User;

class HealthMetricPolicy
{
    public function view(User $user, HealthMetric $metric): bool
    {
        if ($user->isAdmin() || $user->isDoctor()) {
            return true;
        }

        if ($user->isPatient()) {
            return $metric->patient && $metric->patient->user_id === $user->id;
        }

        return false;
    }

    public function create(User $user, HealthMetric $metric): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isPatient() && $metric->patient && $metric->patient->user_id === $user->id;
    }

    public function delete(User $user, HealthMetric $metric): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isPatient() && $metric->patient && $metric->patient->user_id === $user->id;
    }
}
