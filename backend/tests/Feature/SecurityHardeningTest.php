<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Enums\ConsultationType;
use App\Enums\PaymentStatus;
use App\Enums\PrescriptionStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\HealthMetric;
use App\Models\MedicalRecord;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Prescription;
use App\Models\Specialty;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    private User $patientUserA;
    private Patient $patientA;

    private User $patientUserB;
    private Patient $patientB;

    private User $doctorUser1;
    private Doctor $doctor1;

    private User $doctorUser2;
    private Doctor $doctor2;

    private User $nursingStaffUser;
    private Staff $nursingStaff;

    private User $suspendedUser;

    private Appointment $appointmentA;
    private MedicalRecord $recordA;
    private Prescription $prescriptionA;
    private HealthMetric $metricA;
    private Payment $paymentA;

    protected function setUp(): void
    {
        parent::setUp();

        $pass = Hash::make('SecuritySecret123!');

        // Patient A
        $this->patientUserA = User::create([
            'name' => 'Security Patient A',
            'email' => 'patient.a@sec.local',
            'password' => $pass,
            'role' => UserRole::PATIENT,
            'status' => UserStatus::ACTIVE,
        ]);
        $this->patientA = Patient::create(['user_id' => $this->patientUserA->id, 'blood_type' => 'O+']);

        // Patient B
        $this->patientUserB = User::create([
            'name' => 'Security Patient B',
            'email' => 'patient.b@sec.local',
            'password' => $pass,
            'role' => UserRole::PATIENT,
            'status' => UserStatus::ACTIVE,
        ]);
        $this->patientB = Patient::create(['user_id' => $this->patientUserB->id, 'blood_type' => 'A+']);

        // Doctor 1 (Assigned to A)
        $cardio = Specialty::create(['name' => 'Cardiology', 'slug' => 'cardiology']);
        $this->doctorUser1 = User::create([
            'name' => 'Dr. Assigned',
            'email' => 'doc1@sec.local',
            'password' => $pass,
            'role' => UserRole::DOCTOR,
            'status' => UserStatus::ACTIVE,
        ]);
        $this->doctor1 = Doctor::create([
            'user_id' => $this->doctorUser1->id,
            'specialty_id' => $cardio->id,
            'license_number' => 'DOC-SEC-01',
            'consultation_fee' => 90.00,
        ]);

        // Doctor 2 (Unassigned)
        $this->doctorUser2 = User::create([
            'name' => 'Dr. Unassigned',
            'email' => 'doc2@sec.local',
            'password' => $pass,
            'role' => UserRole::DOCTOR,
            'status' => UserStatus::ACTIVE,
        ]);
        $this->doctor2 = Doctor::create([
            'user_id' => $this->doctorUser2->id,
            'specialty_id' => $cardio->id,
            'license_number' => 'DOC-SEC-02',
            'consultation_fee' => 85.00,
        ]);

        // Nursing Staff (Non-billing)
        $this->nursingStaffUser = User::create([
            'name' => 'Nurse Jackie',
            'email' => 'nurse@sec.local',
            'password' => $pass,
            'role' => UserRole::STAFF,
            'status' => UserStatus::ACTIVE,
        ]);
        $this->nursingStaff = Staff::create([
            'user_id' => $this->nursingStaffUser->id,
            'department' => 'Inpatient Nursing',
            'employee_number' => 'EMP-NURSE-01',
        ]);

        // Suspended User
        $this->suspendedUser = User::create([
            'name' => 'Suspended User',
            'email' => 'suspended@sec.local',
            'password' => $pass,
            'role' => UserRole::PATIENT,
            'status' => UserStatus::SUSPENDED,
        ]);

        // Clinical artifacts for Patient A
        $this->appointmentA = Appointment::create([
            'booking_code' => 'APT-SEC-01',
            'patient_id' => $this->patientA->id,
            'doctor_id' => $this->doctor1->id,
            'appointment_date' => now()->toDateString(),
            'appointment_time' => '09:00:00',
            'status' => AppointmentStatus::CONFIRMED,
            'consultation_type' => ConsultationType::IN_PERSON,
            'consultation_fee' => 90.00,
            'service_fee' => 5.00,
            'total_amount' => 95.00,
        ]);

        $this->recordA = MedicalRecord::create([
            'record_number' => 'MR-SEC-01',
            'patient_id' => $this->patientA->id,
            'doctor_id' => $this->doctor1->id,
            'appointment_id' => $this->appointmentA->id,
            'visit_date' => now()->toDateString(),
            'chief_complaint' => 'Confidential health complaint',
            'diagnosis' => 'Confidential Diagnosis',
        ]);

        $med = Medicine::create([
            'name' => 'Atorvastatin',
            'generic_name' => 'Atorvastatin Calcium',
            'dosage_form' => 'Tablet',
            'strength' => '20mg',
        ]);

        $this->prescriptionA = Prescription::create([
            'prescription_code' => 'RX-SEC-01',
            'patient_id' => $this->patientA->id,
            'doctor_id' => $this->doctor1->id,
            'medical_record_id' => $this->recordA->id,
            'prescription_date' => now()->toDateString(),
            'status' => PrescriptionStatus::ACTIVE,
        ]);

        $this->metricA = HealthMetric::create([
            'patient_id' => $this->patientA->id,
            'metric_type' => 'heart_rate',
            'value' => 74.0,
            'unit' => 'bpm',
            'measured_at' => now(),
        ]);

        $this->paymentA = Payment::create([
            'payment_reference' => 'PAY-SEC-01',
            'appointment_id' => $this->appointmentA->id,
            'patient_id' => $this->patientA->id,
            'user_id' => $this->patientUserA->id,
            'amount' => 95.00,
            'currency' => 'USD',
            'payment_method' => 'qris',
            'status' => PaymentStatus::PAID,
            'paid_at' => now(),
            'provider' => 'sandbox',
        ]);
    }

    public function test_security_headers_are_attached_to_responses(): void
    {
        $response = $this->getJson('/api/v1/health');

        $response->assertStatus(200)
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('X-XSS-Protection', '1; mode=block')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_suspended_user_cannot_login(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'suspended@sec.local',
            'password' => 'SecuritySecret123!',
        ]);

        $response->assertStatus(403)
            ->assertJsonPath('success', false);
    }

    public function test_public_registration_cannot_elevate_privileges_to_admin_or_doctor(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Privilege Escalation Attempt',
            'email' => 'attacker@sec.local',
            'password' => 'SecuritySecret123!',
            'password_confirmation' => 'SecuritySecret123!',
            'role' => 'admin',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.user.role', 'patient');

        $this->assertDatabaseHas('users', [
            'email' => 'attacker@sec.local',
            'role' => 'patient',
        ]);
    }

    public function test_doctor_cannot_view_unassigned_patient_medical_records(): void
    {
        Sanctum::actingAs($this->doctorUser2);

        $response = $this->getJson("/api/v1/medical-records/{$this->recordA->id}");
        $response->assertStatus(403);
    }

    public function test_patient_cannot_access_cross_patient_data(): void
    {
        Sanctum::actingAs($this->patientUserB);

        // Cannot view appointment
        $this->getJson("/api/v1/appointments/{$this->appointmentA->id}")->assertStatus(403);

        // Cannot cancel appointment
        $this->postJson("/api/v1/appointments/{$this->appointmentA->id}/cancel", ['cancellation_reason' => 'Unauthorized'])->assertStatus(403);

        // Cannot view medical record
        $this->getJson("/api/v1/medical-records/{$this->recordA->id}")->assertStatus(403);

        // Cannot view prescription
        $this->getJson("/api/v1/prescriptions/{$this->prescriptionA->id}")->assertStatus(403);

        // Cannot view health metric
        $this->getJson("/api/v1/health-metrics/{$this->metricA->id}")->assertStatus(403);

        // Cannot view payment
        $this->getJson("/api/v1/payments/{$this->paymentA->id}")->assertStatus(403);
    }

    public function test_staff_cannot_view_clinical_medical_records(): void
    {
        Sanctum::actingAs($this->nursingStaffUser);

        $this->getJson('/api/v1/medical-records')->assertStatus(403);
        $this->getJson("/api/v1/medical-records/{$this->recordA->id}")->assertStatus(403);
    }

    public function test_non_billing_staff_cannot_access_payments_or_issue_refunds(): void
    {
        Sanctum::actingAs($this->nursingStaffUser);

        // Non-billing staff cannot access payment ledger
        $this->getJson('/api/v1/payments')->assertStatus(403);

        // Non-billing staff cannot issue refunds
        $this->postJson("/api/v1/payments/{$this->paymentA->id}/refund", [
            'reason' => 'Unauthorized refund attempt',
        ])->assertStatus(403);
    }

    public function test_invalid_webhook_signature_is_rejected(): void
    {
        $payload = [
            'payment_reference' => 'PAY-SEC-01',
            'status' => 'paid',
        ];

        $response = $this->withHeaders([
            'X-Sandbox-Signature' => 'tampered_malicious_signature',
        ])->postJson('/api/v1/payments/webhook/sandbox', $payload);

        $response->assertStatus(400);
    }
}
