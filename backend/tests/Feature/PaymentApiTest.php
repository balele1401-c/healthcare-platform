<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Enums\ConsultationType;
use App\Enums\PaymentStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Specialty;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PaymentApiTest extends TestCase
{
    use RefreshDatabase;

    private User $patientUser;
    private Patient $patient;
    private string $patientToken;

    private User $otherPatientUser;
    private string $otherToken;

    private User $billingStaffUser;
    private string $billingStaffToken;

    private Payment $payment;

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
        $this->otherToken = $this->otherPatientUser->createToken('token')->plainTextToken;

        // Billing Staff
        $this->billingStaffUser = User::create([
            'name' => 'Billing Officer',
            'email' => 'billing@example.com',
            'password' => $pass,
            'role' => UserRole::STAFF,
            'status' => UserStatus::ACTIVE,
        ]);
        Staff::create([
            'user_id' => $this->billingStaffUser->id,
            'department' => 'Medical Billing & Insurance',
            'employee_number' => 'EMP-BIL-01',
        ]);
        $this->billingStaffToken = $this->billingStaffUser->createToken('token')->plainTextToken;

        // Doctor
        $specialty = Specialty::create(['name' => 'Cardiology', 'slug' => 'cardiology']);
        $docUser = User::create(['name' => 'Dr. Chen', 'email' => 'chen@example.com', 'password' => $pass, 'role' => UserRole::DOCTOR, 'status' => UserStatus::ACTIVE]);
        $doctor = Doctor::create(['user_id' => $docUser->id, 'specialty_id' => $specialty->id, 'license_number' => 'MD-100']);

        // Appointment & Payment
        $appointment = Appointment::create([
            'booking_code' => 'APT-PAY-01',
            'patient_id' => $this->patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => now()->toDateString(),
            'appointment_time' => '11:00:00',
            'status' => AppointmentStatus::COMPLETED,
            'consultation_type' => ConsultationType::IN_PERSON,
            'total_amount' => 80.00,
        ]);

        $this->payment = Payment::create([
            'payment_reference' => 'PAY-TEST-001',
            'appointment_id' => $appointment->id,
            'patient_id' => $this->patient->id,
            'amount' => 80.00,
            'currency' => 'USD',
            'payment_method' => 'Credit Card',
            'status' => PaymentStatus::PAID,
            'paid_at' => now(),
        ]);
    }

    public function test_patient_can_view_own_payment_records(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->patientToken)
            ->getJson('/api/v1/payments');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.payment_reference', 'PAY-TEST-001')
            ->assertJsonPath('data.0.amount', 80);
    }

    public function test_other_patient_cannot_access_payment(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->otherToken)
            ->getJson("/api/v1/payments/{$this->payment->id}");

        $response->assertStatus(403);
    }

    public function test_billing_staff_can_view_payments(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->billingStaffToken)
            ->getJson('/api/v1/payments');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }
}
