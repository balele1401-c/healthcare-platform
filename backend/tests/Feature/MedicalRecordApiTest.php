<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Specialty;
use App\Models\Staff;
use App\Models\User;
use App\Models\VitalSign;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MedicalRecordApiTest extends TestCase
{
    use RefreshDatabase;

    private User $patientUser;
    private Patient $patient;
    private string $patientToken;

    private User $otherPatientUser;
    private string $otherPatientToken;

    private User $docUser;
    private Doctor $doctor;
    private string $doctorToken;

    private User $staffUser;
    private string $staffToken;

    private MedicalRecord $record;

    protected function setUp(): void
    {
        parent::setUp();

        $pass = Hash::make('Password123!');

        // Patient A
        $this->patientUser = User::create([
            'name' => 'Sarah Jenkins',
            'email' => 'sarah@example.com',
            'password' => $pass,
            'role' => UserRole::PATIENT,
            'status' => UserStatus::ACTIVE,
        ]);
        $this->patient = Patient::create(['user_id' => $this->patientUser->id]);
        $this->patientToken = $this->patientUser->createToken('token')->plainTextToken;

        // Patient B
        $this->otherPatientUser = User::create([
            'name' => 'David Miller',
            'email' => 'david@example.com',
            'password' => $pass,
            'role' => UserRole::PATIENT,
            'status' => UserStatus::ACTIVE,
        ]);
        Patient::create(['user_id' => $this->otherPatientUser->id]);
        $this->otherPatientToken = $this->otherPatientUser->createToken('token')->plainTextToken;

        // Doctor
        $specialty = Specialty::create(['name' => 'Cardiology', 'slug' => 'cardiology']);
        $this->docUser = User::create([
            'name' => 'Dr. Emily Chen',
            'email' => 'chen@example.com',
            'password' => $pass,
            'role' => UserRole::DOCTOR,
            'status' => UserStatus::ACTIVE,
        ]);
        $this->doctor = Doctor::create([
            'user_id' => $this->docUser->id,
            'specialty_id' => $specialty->id,
            'license_number' => 'MD-100',
        ]);
        $this->doctorToken = $this->docUser->createToken('token')->plainTextToken;

        // Staff
        $this->staffUser = User::create([
            'name' => 'Staff Reception',
            'email' => 'staff@example.com',
            'password' => $pass,
            'role' => UserRole::STAFF,
            'status' => UserStatus::ACTIVE,
        ]);
        Staff::create([
            'user_id' => $this->staffUser->id,
            'department' => 'Reception',
            'employee_number' => 'EMP-01',
        ]);
        $this->staffToken = $this->staffUser->createToken('token')->plainTextToken;

        // Medical Record
        $this->record = MedicalRecord::create([
            'record_number' => 'MR-2026-001',
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'visit_date' => now()->toDateString(),
            'chief_complaint' => 'Mild dizziness',
            'diagnosis' => 'Stage 1 Hypertension',
        ]);

        VitalSign::create([
            'medical_record_id' => $this->record->id,
            'systolic_blood_pressure' => 130,
            'diastolic_blood_pressure' => 85,
            'measured_at' => now(),
        ]);
    }

    public function test_patient_can_view_own_medical_record(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->patientToken)
            ->getJson("/api/v1/medical-records/{$this->record->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.record_number', 'MR-2026-001')
            ->assertJsonPath('data.diagnosis', 'Stage 1 Hypertension')
            ->assertJsonPath('data.vital_signs.blood_pressure_formatted', '130/85 mmHg');
    }

    public function test_other_patient_cannot_view_medical_record(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->otherPatientToken)
            ->getJson("/api/v1/medical-records/{$this->record->id}");

        $response->assertStatus(403);
    }

    public function test_doctor_can_view_assigned_patient_medical_record(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->doctorToken)
            ->getJson("/api/v1/medical-records/{$this->record->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.diagnosis', 'Stage 1 Hypertension');
    }

    public function test_staff_cannot_view_clinical_medical_records(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->staffToken)
            ->getJson('/api/v1/medical-records');

        $response->assertStatus(403);
    }
}
