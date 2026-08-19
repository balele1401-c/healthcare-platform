<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Enums\ConsultationType;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AppointmentApiTest extends TestCase
{
    use RefreshDatabase;

    private User $patientUser;
    private Patient $patient;
    private string $patientToken;

    private User $otherPatientUser;
    private Patient $otherPatient;
    private string $otherPatientToken;

    private Doctor $doctor;

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
        $this->patient = Patient::create(['user_id' => $this->patientUser->id]);
        $this->patientToken = $this->patientUser->createToken('token')->plainTextToken;

        $this->otherPatientUser = User::create([
            'name' => 'David Miller',
            'email' => 'david@example.com',
            'password' => $pass,
            'role' => UserRole::PATIENT,
            'status' => UserStatus::ACTIVE,
        ]);
        $this->otherPatient = Patient::create(['user_id' => $this->otherPatientUser->id]);
        $this->otherPatientToken = $this->otherPatientUser->createToken('token')->plainTextToken;

        $specialty = Specialty::create(['name' => 'Cardiology', 'slug' => 'cardiology']);
        $docUser = User::create([
            'name' => 'Dr. Chen',
            'email' => 'chen@example.com',
            'password' => $pass,
            'role' => UserRole::DOCTOR,
            'status' => UserStatus::ACTIVE,
        ]);
        $this->doctor = Doctor::create([
            'user_id' => $docUser->id,
            'specialty_id' => $specialty->id,
            'license_number' => 'MD-100',
            'consultation_fee' => 80.00,
        ]);
    }

    public function test_patient_can_create_appointment(): void
    {
        $payload = [
            'doctor_id' => $this->doctor->id,
            'appointment_date' => now()->addDays(2)->format('Y-m-d'),
            'appointment_time' => '10:30',
            'consultation_type' => ConsultationType::ONLINE->value,
            'notes' => 'Checking hypertension.',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->patientToken)
            ->postJson('/api/v1/appointments', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.doctor_id', $this->doctor->id)
            ->assertJsonPath('data.consultation_fee', 80)
            ->assertJsonPath('data.status', AppointmentStatus::PENDING->value);

        $this->assertDatabaseHas('appointments', [
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
        ]);
    }

    public function test_patient_can_cancel_own_pending_appointment(): void
    {
        $appointment = Appointment::create([
            'booking_code' => 'APT-CANCEL-01',
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'appointment_date' => now()->addDays(2)->toDateString(),
            'appointment_time' => '10:00:00',
            'status' => AppointmentStatus::CONFIRMED,
            'consultation_type' => ConsultationType::ONLINE,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->patientToken)
            ->postJson("/api/v1/appointments/{$appointment->id}/cancel", [
                'cancellation_reason' => 'Schedule conflict.',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', AppointmentStatus::CANCELLED->value)
            ->assertJsonPath('data.cancellation_reason', 'Schedule conflict.');
    }

    public function test_patient_cannot_view_or_cancel_other_patients_appointment(): void
    {
        $appointment = Appointment::create([
            'booking_code' => 'APT-ISOL-01',
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'appointment_date' => now()->addDays(2)->toDateString(),
            'appointment_time' => '10:00:00',
            'status' => AppointmentStatus::CONFIRMED,
            'consultation_type' => ConsultationType::ONLINE,
        ]);

        // Patient B tries to view Patient A's appointment
        $viewResponse = $this->withHeader('Authorization', 'Bearer ' . $this->otherPatientToken)
            ->getJson("/api/v1/appointments/{$appointment->id}");

        $viewResponse->assertStatus(403);

        // Patient B tries to cancel Patient A's appointment
        $cancelResponse = $this->withHeader('Authorization', 'Bearer ' . $this->otherPatientToken)
            ->postJson("/api/v1/appointments/{$appointment->id}/cancel", [
                'cancellation_reason' => 'Unauthorized cancel attempt.',
            ]);

        $cancelResponse->assertStatus(403);
    }
}
