<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Enums\ConsultationType;
use App\Enums\HealthMetricType;
use App\Enums\PrescriptionStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\HealthMetric;
use App\Models\MedicalRecord;
use App\Models\Notification;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PatientApiTest extends TestCase
{
    use RefreshDatabase;

    private User $patientUser;
    private Patient $patient;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $pass = Hash::make('Password123!');

        $this->patientUser = User::create([
            'name' => 'Sarah Jenkins',
            'email' => 'sarah@example.com',
            'password' => $pass,
            'role' => UserRole::PATIENT,
            'status' => UserStatus::ACTIVE,
        ]);

        $this->patient = Patient::create([
            'user_id' => $this->patientUser->id,
            'date_of_birth' => '1992-04-15',
            'gender' => 'female',
            'blood_type' => 'A+',
        ]);

        $this->token = $this->patientUser->createToken('test_token')->plainTextToken;
    }

    public function test_patient_can_view_and_update_profile(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/v1/patient/profile');

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Sarah Jenkins')
            ->assertJsonPath('data.blood_type', 'A+');

        $updateResponse = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson('/api/v1/patient/profile', [
                'name' => 'Sarah Jenkins Updated',
                'height_cm' => 169.5,
                'weight_kg' => 63.0,
                'allergies' => 'Sulfa, Penicillin',
            ]);

        $updateResponse->assertStatus(200)
            ->assertJsonPath('data.name', 'Sarah Jenkins Updated')
            ->assertJsonPath('data.height_cm', 169.5)
            ->assertJsonPath('data.allergies', 'Sulfa, Penicillin');
    }

    public function test_patient_can_view_own_clinical_data_via_patient_endpoints(): void
    {
        $specialty = Specialty::create(['name' => 'Cardiology', 'slug' => 'cardiology']);
        $docUser = User::create(['name' => 'Dr. Chen', 'email' => 'chen@example.com', 'password' => 'Pass123!', 'role' => UserRole::DOCTOR, 'status' => UserStatus::ACTIVE]);
        $doc = Doctor::create(['user_id' => $docUser->id, 'specialty_id' => $specialty->id, 'license_number' => 'MD-1']);

        // Appointment
        Appointment::create([
            'booking_code' => 'APT-01',
            'patient_id' => $this->patient->id,
            'doctor_id' => $doc->id,
            'appointment_date' => now()->toDateString(),
            'appointment_time' => '10:00:00',
            'status' => AppointmentStatus::CONFIRMED,
            'consultation_type' => ConsultationType::IN_PERSON,
        ]);

        // Medical Record
        MedicalRecord::create([
            'record_number' => 'MR-01',
            'patient_id' => $this->patient->id,
            'doctor_id' => $doc->id,
            'visit_date' => now()->toDateString(),
            'chief_complaint' => 'Checkup',
            'diagnosis' => 'Normal',
        ]);

        // Prescription
        Prescription::create([
            'prescription_code' => 'RX-01',
            'patient_id' => $this->patient->id,
            'doctor_id' => $doc->id,
            'prescription_date' => now()->toDateString(),
            'status' => PrescriptionStatus::ACTIVE,
        ]);

        // Health Metric
        HealthMetric::create([
            'patient_id' => $this->patient->id,
            'metric_type' => HealthMetricType::HEART_RATE,
            'value' => 72.0,
            'unit' => 'bpm',
            'measured_at' => now(),
        ]);

        // Notification
        Notification::create([
            'user_id' => $this->patientUser->id,
            'title' => 'Reminder',
            'message' => 'Drink water',
            'notification_type' => 'general',
        ]);

        $headers = ['Authorization' => 'Bearer ' . $this->token];

        $this->getJson('/api/v1/patient/appointments', $headers)->assertStatus(200)->assertJsonCount(1, 'data');
        $this->getJson('/api/v1/patient/medical-records', $headers)->assertStatus(200)->assertJsonCount(1, 'data');
        $this->getJson('/api/v1/patient/prescriptions', $headers)->assertStatus(200)->assertJsonCount(1, 'data');
        $this->getJson('/api/v1/patient/health-metrics', $headers)->assertStatus(200)->assertJsonCount(1, 'data');
        $this->getJson('/api/v1/patient/notifications', $headers)->assertStatus(200)->assertJsonCount(1, 'data');
    }
}
