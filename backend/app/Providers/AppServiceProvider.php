<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\ChatConversation;
use App\Models\HealthMetric;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Prescription;
use App\Policies\AppointmentPolicy;
use App\Policies\ChatConversationPolicy;
use App\Policies\HealthMetricPolicy;
use App\Policies\MedicalRecordPolicy;
use App\Policies\PatientPolicy;
use App\Policies\PrescriptionPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Appointment::class, AppointmentPolicy::class);
        Gate::policy(MedicalRecord::class, MedicalRecordPolicy::class);
        Gate::policy(Prescription::class, PrescriptionPolicy::class);
        Gate::policy(HealthMetric::class, HealthMetricPolicy::class);
        Gate::policy(ChatConversation::class, ChatConversationPolicy::class);
        Gate::policy(Patient::class, PatientPolicy::class);
    }
}
