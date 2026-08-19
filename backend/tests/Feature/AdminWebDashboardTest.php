<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Enums\ConsultationType;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Appointment;
use App\Models\Doctor;
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

class AdminWebDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $patientUser;
    protected User $doctorUser;

    protected function setUp(): void
    {
        parent::setUp();

        $pass = Hash::make('Password123!');

        // Admin User
        $this->adminUser = User::create([
            'name' => 'Super Administrator',
            'email' => 'admin@healthcare.local',
            'password' => $pass,
            'role' => UserRole::ADMIN,
            'status' => UserStatus::ACTIVE,
        ]);

        // Patient User
        $this->patientUser = User::create([
            'name' => 'Alice Patient',
            'email' => 'alice@healthcare.local',
            'password' => $pass,
            'role' => UserRole::PATIENT,
            'status' => UserStatus::ACTIVE,
        ]);
        Patient::create([
            'user_id' => $this->patientUser->id,
            'blood_type' => 'O+',
        ]);

        // Doctor User
        $this->doctorUser = User::create([
            'name' => 'Dr. Robert Specialist',
            'email' => 'robert@healthcare.local',
            'password' => $pass,
            'role' => UserRole::DOCTOR,
            'status' => UserStatus::ACTIVE,
        ]);
        $specialty = Specialty::create(['name' => 'Neurology', 'slug' => 'neurology']);
        Doctor::create([
            'user_id' => $this->doctorUser->id,
            'specialty_id' => $specialty->id,
            'license_number' => 'MED-12345',
            'consultation_fee' => 120.0,
            'experience_years' => 10,
            'is_available' => true,
        ]);
    }

    public function test_admin_login_screen_can_be_rendered(): void
    {
        $response = $this->get(route('admin.login'));
        $response->assertStatus(200);
        $response->assertSee('Admin Sign In');
        $response->assertSee('HealthCare Portal');
    }

    public function test_admin_can_authenticate_and_redirect_to_dashboard(): void
    {
        $response = $this->post(route('admin.login.submit'), [
            'email' => 'admin@healthcare.local',
            'password' => 'Password123!',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->adminUser);
    }

    public function test_patient_cannot_authenticate_via_admin_login(): void
    {
        $response = $this->post(route('admin.login.submit'), [
            'email' => 'alice@healthcare.local',
            'password' => 'Password123!',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_unauthenticated_user_is_redirected_to_admin_login(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_patient_is_forbidden_from_admin_routes(): void
    {
        $response = $this->actingAs($this->patientUser)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    public function test_admin_can_view_dashboard_overview(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Operational Overview');
        $response->assertSee('Total Patients');
        $response->assertSee('Total Doctors');
    }

    public function test_admin_can_view_patients_and_patient_detail(): void
    {
        $patient = Patient::first();

        $indexResponse = $this->actingAs($this->adminUser)->get(route('admin.patients.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Alice Patient');

        $showResponse = $this->actingAs($this->adminUser)->get(route('admin.patients.show', $patient->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Alice Patient');
        $showResponse->assertSee('O+');
    }

    public function test_admin_can_view_doctors_and_doctor_detail(): void
    {
        $doctor = Doctor::first();

        $indexResponse = $this->actingAs($this->adminUser)->get(route('admin.doctors.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Dr. Robert Specialist');

        $showResponse = $this->actingAs($this->adminUser)->get(route('admin.doctors.show', $doctor->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Neurology');
    }

    public function test_admin_can_view_specialties_overview(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.specialties.index'));
        $response->assertStatus(200);
        $response->assertSee('Neurology');
    }

    public function test_admin_can_view_and_cancel_appointment(): void
    {
        $patient = Patient::first();
        $doctor = Doctor::first();

        $appointment = Appointment::create([
            'booking_code' => 'APT-TEST-001',
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => now()->addDays(2)->toDateString(),
            'appointment_time' => '10:00:00',
            'consultation_type' => ConsultationType::IN_PERSON,
            'status' => AppointmentStatus::CONFIRMED,
            'consultation_fee' => 120.0,
            'service_fee' => 5.0,
            'total_amount' => 125.0,
        ]);

        $indexResponse = $this->actingAs($this->adminUser)->get(route('admin.appointments.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('APT-TEST-001');

        $showResponse = $this->actingAs($this->adminUser)->get(route('admin.appointments.show', $appointment->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('APT-TEST-001');

        $cancelResponse = $this->actingAs($this->adminUser)->post(route('admin.appointments.cancel', $appointment->id), [
            'reason' => 'Doctor unavailable due to emergency surgery',
        ]);
        $cancelResponse->assertSessionHas('success');

        $this->assertEquals(AppointmentStatus::CANCELLED, $appointment->fresh()->status);
    }

    public function test_admin_can_view_prescriptions_and_medical_records(): void
    {
        $patient = Patient::first();
        $doctor = Doctor::first();

        MedicalRecord::create([
            'record_number' => 'REC-TEST-99',
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'visit_date' => now()->toDateString(),
            'chief_complaint' => 'Migraine',
            'diagnosis' => 'Chronic Migraine',
        ]);

        Prescription::create([
            'prescription_code' => 'RX-TEST-99',
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'prescription_date' => now()->toDateString(),
            'status' => \App\Enums\PrescriptionStatus::ACTIVE,
        ]);

        $recResponse = $this->actingAs($this->adminUser)->get(route('admin.medical-records.index'));
        $recResponse->assertStatus(200);
        $recResponse->assertSee('REC-TEST-99');

        $rxResponse = $this->actingAs($this->adminUser)->get(route('admin.prescriptions.index'));
        $rxResponse->assertStatus(200);
        $rxResponse->assertSee('RX-TEST-99');
    }

    public function test_admin_can_view_payments_and_notifications(): void
    {
        $patient = Patient::first();

        Notification::create([
            'user_id' => $this->adminUser->id,
            'title' => 'Platform Notice',
            'message' => 'System maintenance scheduled',
            'notification_type' => 'system',
            'read_at' => null,
        ]);

        $payResponse = $this->actingAs($this->adminUser)->get(route('admin.payments.index'));
        $payResponse->assertStatus(200);
        $payResponse->assertSee('Billing & Payments');

        $notifResponse = $this->actingAs($this->adminUser)->get(route('admin.notifications.index'));
        $notifResponse->assertStatus(200);
        $notifResponse->assertSee('Platform Notice');

        $readAllResponse = $this->actingAs($this->adminUser)->post(route('admin.notifications.read-all'));
        $readAllResponse->assertSessionHas('success');
    }

    public function test_admin_can_view_audit_logs_and_profile(): void
    {
        $logResponse = $this->actingAs($this->adminUser)->get(route('admin.audit-logs.index'));
        $logResponse->assertStatus(200);
        $logResponse->assertSee('System Audit Trail');

        $profileResponse = $this->actingAs($this->adminUser)->get(route('admin.profile'));
        $profileResponse->assertStatus(200);
        $profileResponse->assertSee('Super Administrator');
    }

    public function test_admin_can_logout_securely(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.logout'));
        $response->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }
}
