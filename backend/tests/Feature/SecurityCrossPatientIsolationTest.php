<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Enums\ConsultationType;
use App\Enums\HealthMetricType;
use App\Enums\PaymentStatus;
use App\Enums\PrescriptionStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\HealthMetric;
use App\Models\MedicalRecord;
use App\Models\Notification;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Prescription;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecurityCrossPatientIsolationTest extends TestCase
{
    use RefreshDatabase;

    private User $patientAUser;
    private Patient $patientA;
    private string $tokenA;

    private User $patientBUser;
    private Patient $patientB;
    private string $tokenB;

    private Doctor $doctor;

    protected function setUp(): void
    {
        parent::setUp();

        $pass = Hash::make('Password123!');

        // Patient A
        $this->patientAUser = User::create([
            'name' => 'Alice Patient',
            'email' => 'alice@healthcare.local',
            'password' => $pass,
            'role' => UserRole::PATIENT,
            'status' => UserStatus::ACTIVE,
        ]);
        $this->patientA = Patient::create([
            'user_id' => $this->patientAUser->id,
            'blood_type' => 'A+',
            'allergies' => 'Penicillin',
        ]);
        $this->tokenA = $this->patientAUser->createToken('token_a')->plainTextToken;

        // Patient B
        $this->patientBUser = User::create([
            'name' => 'Bob Patient',
            'email' => 'bob@healthcare.local',
            'password' => $pass,
            'role' => UserRole::PATIENT,
            'status' => UserStatus::ACTIVE,
        ]);
        $this->patientB = Patient::create([
            'user_id' => $this->patientBUser->id,
            'blood_type' => 'O-',
            'allergies' => 'Latex',
        ]);
        $this->tokenB = $this->patientBUser->createToken('token_b')->plainTextToken;

        // Doctor
        $specialty = Specialty::create(['name' => 'Cardiology', 'slug' => 'cardiology']);
        $docUser = User::create([
            'name' => 'Dr. House',
            'email' => 'house@healthcare.local',
            'password' => $pass,
            'role' => UserRole::DOCTOR,
            'status' => UserStatus::ACTIVE,
        ]);
        $this->doctor = Doctor::create([
            'user_id' => $docUser->id,
            'specialty_id' => $specialty->id,
            'license_number' => 'MD-HOUSE-01',
        ]);
    }

    public function test_complete_cross_patient_data_isolation_matrix(): void
    {
        // 1. Create Patient A's appointment
        $apptA = Appointment::create([
            'booking_code' => 'APT-A-01',
            'patient_id' => $this->patientA->id,
            'doctor_id' => $this->doctor->id,
            'appointment_date' => now()->addDays(3)->toDateString(),
            'appointment_time' => '09:00:00',
            'status' => AppointmentStatus::CONFIRMED,
            'consultation_type' => ConsultationType::ONLINE,
        ]);

        // 2. Create Patient A's medical record
        $recA = MedicalRecord::create([
            'record_number' => 'MR-A-01',
            'patient_id' => $this->patientA->id,
            'doctor_id' => $this->doctor->id,
            'visit_date' => now()->subDays(10)->toDateString(),
            'chief_complaint' => 'Alice Private Chief Complaint',
            'diagnosis' => 'Alice Private Diagnosis',
        ]);

        // 3. Create Patient A's prescription
        $rxA = Prescription::create([
            'prescription_code' => 'RX-A-01',
            'patient_id' => $this->patientA->id,
            'doctor_id' => $this->doctor->id,
            'prescription_date' => now()->toDateString(),
            'status' => PrescriptionStatus::ACTIVE,
        ]);

        // 4. Create Patient A's health metric
        $metricA = HealthMetric::create([
            'patient_id' => $this->patientA->id,
            'metric_type' => HealthMetricType::BLOOD_GLUCOSE,
            'value' => 95.0,
            'unit' => 'mg/dL',
            'measured_at' => now(),
        ]);

        // 5. Create Patient A's notification
        $notifA = Notification::create([
            'user_id' => $this->patientAUser->id,
            'title' => 'Alice Secret Alert',
            'message' => 'Your test results are confidential.',
            'notification_type' => 'clinical',
        ]);

        // 6. Create Patient A's payment
        $payA = Payment::create([
            'payment_reference' => 'PAY-A-01',
            'appointment_id' => $apptA->id,
            'patient_id' => $this->patientA->id,
            'amount' => 100.00,
            'currency' => 'USD',
            'payment_method' => 'Credit Card',
            'status' => PaymentStatus::PAID,
        ]);

        $headersB = ['Authorization' => 'Bearer ' . $this->tokenB];

        // Patient B asserts:
        // A. Cannot view Patient A appointment
        $this->getJson("/api/v1/appointments/{$apptA->id}", $headersB)->assertStatus(403);

        // B. Cannot cancel Patient A appointment
        $this->postJson("/api/v1/appointments/{$apptA->id}/cancel", ['cancellation_reason' => 'Unauthorized'], $headersB)->assertStatus(403);

        // C. Cannot view Patient A medical record
        $this->getJson("/api/v1/medical-records/{$recA->id}", $headersB)->assertStatus(403);

        // D. Cannot view Patient A prescription
        $this->getJson("/api/v1/prescriptions/{$rxA->id}", $headersB)->assertStatus(403);

        // E. Cannot view Patient A health metric
        $this->getJson("/api/v1/health-metrics/{$metricA->id}", $headersB)->assertStatus(403);

        // F. Cannot delete Patient A health metric
        $this->deleteJson("/api/v1/health-metrics/{$metricA->id}", [], $headersB)->assertStatus(403);

        // G. Cannot mark Patient A notification as read
        $this->postJson("/api/v1/notifications/{$notifA->id}/read", [], $headersB)->assertStatus(403);

        // H. Cannot view Patient A payment
        $this->getJson("/api/v1/payments/{$payA->id}", $headersB)->assertStatus(403);

        // I. Patient B list endpoints return 0 items from Patient A
        $this->getJson('/api/v1/patient/appointments', $headersB)->assertJsonCount(0, 'data');
        $this->getJson('/api/v1/patient/medical-records', $headersB)->assertJsonCount(0, 'data');
        $this->getJson('/api/v1/patient/prescriptions', $headersB)->assertJsonCount(0, 'data');
        $this->getJson('/api/v1/patient/health-metrics', $headersB)->assertJsonCount(0, 'data');
        $this->getJson('/api/v1/patient/notifications', $headersB)->assertJsonCount(0, 'data');
    }
}
