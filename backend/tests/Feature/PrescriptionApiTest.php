<?php

namespace Tests\Feature;

use App\Enums\PrescriptionStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Doctor;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PrescriptionApiTest extends TestCase
{
    use RefreshDatabase;

    private User $patientUser;
    private Patient $patient;
    private string $patientToken;

    private User $otherUser;
    private string $otherToken;

    private Prescription $prescription;

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

        $this->otherUser = User::create([
            'name' => 'David Miller',
            'email' => 'david@example.com',
            'password' => $pass,
            'role' => UserRole::PATIENT,
            'status' => UserStatus::ACTIVE,
        ]);
        Patient::create(['user_id' => $this->otherUser->id]);
        $this->otherToken = $this->otherUser->createToken('token')->plainTextToken;

        $specialty = Specialty::create(['name' => 'Cardiology', 'slug' => 'cardiology']);
        $docUser = User::create(['name' => 'Dr. Chen', 'email' => 'chen@example.com', 'password' => $pass, 'role' => UserRole::DOCTOR, 'status' => UserStatus::ACTIVE]);
        $doctor = Doctor::create(['user_id' => $docUser->id, 'specialty_id' => $specialty->id, 'license_number' => 'MD-100']);

        $medicine = Medicine::create(['name' => 'Amlodipine', 'generic_name' => 'Amlodipine Besylate', 'strength' => '5mg']);

        $this->prescription = Prescription::create([
            'prescription_code' => 'RX-2026-001',
            'patient_id' => $this->patient->id,
            'doctor_id' => $doctor->id,
            'prescription_date' => now()->toDateString(),
            'status' => PrescriptionStatus::ACTIVE,
        ]);

        PrescriptionItem::create([
            'prescription_id' => $this->prescription->id,
            'medicine_id' => $medicine->id,
            'dosage' => '5mg',
            'frequency' => 'Once daily',
            'duration' => '30 Days',
            'quantity' => 30,
        ]);
    }

    public function test_patient_can_view_own_prescription(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->patientToken)
            ->getJson("/api/v1/prescriptions/{$this->prescription->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.prescription_code', 'RX-2026-001')
            ->assertJsonPath('data.items.0.dosage', '5mg');
    }

    public function test_other_patient_cannot_view_prescription(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->otherToken)
            ->getJson("/api/v1/prescriptions/{$this->prescription->id}");

        $response->assertStatus(403);
    }
}
