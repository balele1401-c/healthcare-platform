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
    private Patient $otherPatient;
    private string $otherToken;

    private User $billingStaffUser;
    private string $billingStaffToken;

    private User $doctorUser;
    private string $doctorToken;

    private User $adminUser;
    private string $adminToken;

    private Doctor $doctor;
    private Appointment $appointment;
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
        $this->otherPatient = Patient::create(['user_id' => $this->otherPatientUser->id]);
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
        $this->doctorUser = User::create([
            'name' => 'Dr. Chen',
            'email' => 'chen@example.com',
            'password' => $pass,
            'role' => UserRole::DOCTOR,
            'status' => UserStatus::ACTIVE,
        ]);
        $this->doctor = Doctor::create([
            'user_id' => $this->doctorUser->id,
            'specialty_id' => $specialty->id,
            'license_number' => 'MD-100',
            'consultation_fee' => 75.00,
        ]);
        $this->doctorToken = $this->doctorUser->createToken('token')->plainTextToken;

        // Admin
        $this->adminUser = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@healthcare.local',
            'password' => $pass,
            'role' => UserRole::ADMIN,
            'status' => UserStatus::ACTIVE,
        ]);
        $this->adminToken = $this->adminUser->createToken('token')->plainTextToken;

        // Appointment & Initial Payment
        $this->appointment = Appointment::create([
            'booking_code' => 'APT-PAY-01',
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'appointment_date' => now()->toDateString(),
            'appointment_time' => '11:00:00',
            'status' => AppointmentStatus::PENDING,
            'consultation_type' => ConsultationType::IN_PERSON,
            'consultation_fee' => 75.00,
            'service_fee' => 5.00,
            'total_amount' => 80.00,
        ]);

        $this->payment = Payment::create([
            'payment_reference' => 'PAY-TEST-001',
            'appointment_id' => $this->appointment->id,
            'patient_id' => $this->patient->id,
            'user_id' => $this->patientUser->id,
            'amount' => 80.00,
            'currency' => 'USD',
            'payment_method' => 'qris',
            'status' => PaymentStatus::PAID,
            'paid_at' => now(),
            'provider' => 'sandbox',
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

    public function test_doctor_cannot_view_billing_ledger(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->doctorToken)
            ->getJson('/api/v1/payments');

        $response->assertStatus(403);
    }

    public function test_patient_can_create_payment_for_own_appointment(): void
    {
        $newAppointment = Appointment::create([
            'booking_code' => 'APT-PAY-02',
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'appointment_date' => now()->addDays(2)->toDateString(),
            'appointment_time' => '14:00:00',
            'status' => AppointmentStatus::PENDING,
            'consultation_type' => ConsultationType::ONLINE,
            'consultation_fee' => 75.00,
            'service_fee' => 5.00,
            'total_amount' => 80.00,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->patientToken)
            ->postJson('/api/v1/payments', [
                'appointment_id' => $newAppointment->id,
                'payment_method' => 'qris',
                'provider' => 'sandbox',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.amount', 80)
            ->assertJsonPath('data.status', 'pending');

        $this->assertDatabaseHas('payments', [
            'appointment_id' => $newAppointment->id,
            'patient_id' => $this->patient->id,
            'status' => 'pending',
        ]);
    }

    public function test_patient_cannot_create_payment_for_another_patients_appointment(): void
    {
        $otherAppointment = Appointment::create([
            'booking_code' => 'APT-PAY-OTHER',
            'patient_id' => $this->otherPatient->id,
            'doctor_id' => $this->doctor->id,
            'appointment_date' => now()->addDays(2)->toDateString(),
            'appointment_time' => '14:00:00',
            'status' => AppointmentStatus::PENDING,
            'consultation_type' => ConsultationType::ONLINE,
            'total_amount' => 80.00,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->patientToken)
            ->postJson('/api/v1/payments', [
                'appointment_id' => $otherAppointment->id,
                'payment_method' => 'qris',
            ]);

        $response->assertStatus(403);
    }

    public function test_duplicate_payment_creation_returns_existing_payment(): void
    {
        $newAppointment = Appointment::create([
            'booking_code' => 'APT-PAY-03',
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'appointment_date' => now()->addDays(3)->toDateString(),
            'appointment_time' => '15:00:00',
            'status' => AppointmentStatus::PENDING,
            'consultation_type' => ConsultationType::IN_PERSON,
            'total_amount' => 80.00,
        ]);

        // First creation
        $res1 = $this->withHeader('Authorization', 'Bearer ' . $this->patientToken)
            ->postJson('/api/v1/payments', [
                'appointment_id' => $newAppointment->id,
                'payment_method' => 'qris',
            ]);
        $res1->assertStatus(201);
        $reference1 = $res1->json('data.payment_reference');

        // Second creation should return existing payment (idempotent)
        $res2 = $this->withHeader('Authorization', 'Bearer ' . $this->patientToken)
            ->postJson('/api/v1/payments', [
                'appointment_id' => $newAppointment->id,
                'payment_method' => 'qris',
            ]);
        $res2->assertStatus(201);
        $reference2 = $res2->json('data.payment_reference');

        $this->assertEquals($reference1, $reference2);
        $this->assertEquals(1, Payment::where('appointment_id', $newAppointment->id)->count());
    }

    public function test_sandbox_webhook_successfully_marks_payment_paid_and_confirms_appointment(): void
    {
        $pendingAppointment = Appointment::create([
            'booking_code' => 'APT-PAY-WH',
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'appointment_date' => now()->addDays(1)->toDateString(),
            'appointment_time' => '10:00:00',
            'status' => AppointmentStatus::PENDING,
            'consultation_type' => ConsultationType::IN_PERSON,
            'total_amount' => 80.00,
        ]);

        $pendingPayment = Payment::create([
            'payment_reference' => 'PAY-WH-001',
            'appointment_id' => $pendingAppointment->id,
            'patient_id' => $this->patient->id,
            'user_id' => $this->patientUser->id,
            'amount' => 80.00,
            'currency' => 'USD',
            'payment_method' => 'qris',
            'status' => PaymentStatus::PENDING,
            'provider' => 'sandbox',
        ]);

        $webhookPayload = [
            'payment_reference' => 'PAY-WH-001',
            'provider_payment_id' => 'SANDBOX-TX-999',
            'status' => 'paid',
            'amount' => 80.00,
            'paid_at' => now()->toIso8601String(),
        ];

        $response = $this->postJson('/api/v1/payments/webhook/sandbox', $webhookPayload);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertEquals(PaymentStatus::PAID, $pendingPayment->fresh()->status);
        $this->assertEquals(AppointmentStatus::CONFIRMED, $pendingAppointment->fresh()->status);
    }

    public function test_duplicate_webhook_is_idempotent(): void
    {
        $webhookPayload = [
            'payment_reference' => 'PAY-TEST-001',
            'status' => 'paid',
            'amount' => 80.00,
        ];

        $response = $this->postJson('/api/v1/payments/webhook/sandbox', $webhookPayload);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Payment already settled (idempotent).');
    }

    public function test_webhook_with_invalid_signature_is_rejected(): void
    {
        $payload = ['payment_reference' => 'PAY-TEST-001', 'status' => 'paid'];
        $raw = json_encode($payload);

        $response = $this->withHeaders([
            'X-Sandbox-Signature' => 'invalid_tampered_signature_hash',
        ])->postJson('/api/v1/payments/webhook/sandbox', $payload);

        $response->assertStatus(400);
    }

    public function test_paid_status_cannot_revert_to_pending_via_webhook(): void
    {
        $webhookPayload = [
            'payment_reference' => 'PAY-TEST-001',
            'status' => 'pending',
        ];

        $response = $this->postJson('/api/v1/payments/webhook/sandbox', $webhookPayload);
        $response->assertStatus(200);

        // Payment status must still remain PAID
        $this->assertEquals(PaymentStatus::PAID, $this->payment->fresh()->status);
    }

    public function test_admin_can_issue_refund_for_paid_payment(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->postJson("/api/v1/payments/{$this->payment->id}/refund", [
                'reason' => 'Patient requested appointment cancellation with notice',
                'amount' => 80.00,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'refunded');

        $this->assertEquals(PaymentStatus::REFUNDED, $this->payment->fresh()->status);
        $this->assertEquals('Patient requested appointment cancellation with notice', $this->payment->fresh()->refund_reason);
    }

    public function test_patient_cannot_issue_refund(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->patientToken)
            ->postJson("/api/v1/payments/{$this->payment->id}/refund", [
                'reason' => 'Unauthorized self refund attempt',
            ]);

        $response->assertStatus(403);
    }
}
