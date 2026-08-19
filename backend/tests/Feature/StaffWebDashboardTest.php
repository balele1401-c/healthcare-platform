<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Enums\ConsultationType;
use App\Enums\PaymentStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Notification;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Specialty;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffWebDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $staffUser;
    protected Staff $staff;
    protected User $patientUser;
    protected Patient $patient;
    protected User $doctorUser;
    protected Doctor $doctor;
    protected Specialty $specialty;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Staff User
        $this->staffUser = User::factory()->create([
            'email' => 'staff@healthcare.local',
            'role' => UserRole::STAFF,
            'status' => UserStatus::ACTIVE,
        ]);
        $this->staff = Staff::create([
            'user_id' => $this->staffUser->id,
            'department' => 'Operations',
            'employee_number' => 'STF-001',
            'facility' => 'Main Hospital',
            'status' => 'active',
        ]);

        // 2. Specialty & Doctor
        $this->specialty = Specialty::create([
            'name' => 'General Medicine',
            'slug' => 'general-medicine',
            'description' => 'General primary care and diagnostics.',
        ]);

        $this->doctorUser = User::factory()->create([
            'email' => 'doctor@healthcare.local',
            'role' => UserRole::DOCTOR,
            'status' => UserStatus::ACTIVE,
        ]);
        $this->doctor = Doctor::create([
            'user_id' => $this->doctorUser->id,
            'specialty_id' => $this->specialty->id,
            'license_number' => 'DOC-STAFF-TEST',
            'experience_years' => 7,
            'consultation_fee' => 120.00,
            'facility' => 'Main Hospital',
            'status' => 'active',
        ]);

        // 3. Patient
        $this->patientUser = User::factory()->create([
            'email' => 'patient@healthcare.local',
            'role' => UserRole::PATIENT,
            'status' => UserStatus::ACTIVE,
        ]);
        $this->patient = Patient::create([
            'user_id' => $this->patientUser->id,
            'blood_type' => 'O+',
            'emergency_contact_name' => 'Jane Doe',
            'emergency_contact_phone' => '+15559876543',
        ]);
    }

    public function test_staff_login_screen_can_be_rendered(): void
    {
        $response = $this->get(route('staff.login'));
        $response->assertStatus(200);
        $response->assertSee('Staff Operations Sign In');
    }

    public function test_staff_can_authenticate_and_redirect_to_dashboard(): void
    {
        $response = $this->post(route('staff.login.submit'), [
            'email' => 'staff@healthcare.local',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('staff.dashboard'));
        $this->assertAuthenticatedAs($this->staffUser);
    }

    public function test_patient_cannot_authenticate_via_staff_login(): void
    {
        $response = $this->post(route('staff.login.submit'), [
            'email' => 'patient@healthcare.local',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_doctor_cannot_authenticate_via_staff_login(): void
    {
        $response = $this->post(route('staff.login.submit'), [
            'email' => 'doctor@healthcare.local',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_unauthenticated_user_is_redirected_to_staff_login(): void
    {
        $response = $this->get(route('staff.dashboard'));
        $response->assertRedirect(route('staff.login'));
    }

    public function test_patient_is_forbidden_from_staff_routes(): void
    {
        $response = $this->actingAs($this->patientUser)->get(route('staff.dashboard'));
        $response->assertStatus(403);
    }

    public function test_doctor_is_forbidden_from_staff_routes(): void
    {
        $response = $this->actingAs($this->doctorUser)->get(route('staff.dashboard'));
        $response->assertStatus(403);
    }

    public function test_staff_can_view_dashboard_with_real_metrics(): void
    {
        Appointment::create([
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'booking_code' => 'APT-STF-001',
            'appointment_date' => today(),
            'appointment_time' => '10:00:00',
            'consultation_type' => ConsultationType::IN_PERSON,
            'status' => AppointmentStatus::PENDING,
            'total_fee' => 120.00,
        ]);

        $response = $this->actingAs($this->staffUser)->get(route('staff.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Operations Cockpit');
        $response->assertSee('APT-STF-001');
    }

    public function test_staff_can_view_and_filter_appointments(): void
    {
        $apt = Appointment::create([
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'booking_code' => 'APT-STF-002',
            'appointment_date' => today(),
            'appointment_time' => '11:00:00',
            'consultation_type' => ConsultationType::ONLINE,
            'status' => AppointmentStatus::CONFIRMED,
            'total_fee' => 120.00,
        ]);

        $response = $this->actingAs($this->staffUser)->get(route('staff.appointments.index', ['status' => 'confirmed']));
        $response->assertStatus(200);
        $response->assertSee('APT-STF-002');
    }

    public function test_staff_can_view_appointment_details_and_update_status(): void
    {
        $apt = Appointment::create([
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'booking_code' => 'APT-STF-003',
            'appointment_date' => today(),
            'appointment_time' => '14:00:00',
            'consultation_type' => ConsultationType::IN_PERSON,
            'status' => AppointmentStatus::PENDING,
            'total_fee' => 120.00,
        ]);

        $showResponse = $this->actingAs($this->staffUser)->get(route('staff.appointments.show', $apt->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('APT-STF-003');

        $updateResponse = $this->actingAs($this->staffUser)->post(route('staff.appointments.status', $apt->id), [
            'status' => 'confirmed',
            'reason' => 'Patient checked in at front desk',
        ]);

        $updateResponse->assertSessionHas('success');
        $this->assertEquals(AppointmentStatus::CONFIRMED, $apt->fresh()->status);
    }

    public function test_staff_can_view_patient_directory(): void
    {
        $response = $this->actingAs($this->staffUser)->get(route('staff.patients.index'));
        $response->assertStatus(200);
        $response->assertSee($this->patientUser->name);
    }

    public function test_staff_can_register_new_patient_account(): void
    {
        $createResponse = $this->actingAs($this->staffUser)->get(route('staff.patients.create'));
        $createResponse->assertStatus(200);

        $storeResponse = $this->actingAs($this->staffUser)->post(route('staff.patients.store'), [
            'name' => 'Alice Intake Patient',
            'email' => 'alice.intake@healthcare.local',
            'phone' => '+15552345678',
            'password' => 'SecurePass123!',
            'blood_type' => 'A+',
            'emergency_contact_name' => 'Bob Intake',
            'emergency_contact_phone' => '+15558765432',
        ]);

        $newPatient = Patient::whereHas('user', fn ($q) => $q->where('email', 'alice.intake@healthcare.local'))->first();
        $this->assertNotNull($newPatient);
        $storeResponse->assertRedirect(route('staff.patients.show', $newPatient->id));
    }

    public function test_staff_can_view_patient_demographic_profile(): void
    {
        $response = $this->actingAs($this->staffUser)->get(route('staff.patients.show', $this->patient->id));
        $response->assertStatus(200);
        $response->assertSee($this->patientUser->name);
        $response->assertSee('Clinical Access Restricted');
    }

    public function test_staff_can_view_doctors_directory_and_doctor_schedule(): void
    {
        DoctorSchedule::create([
            'doctor_id' => $this->doctor->id,
            'day_of_week' => 1,
            'start_time' => '09:00',
            'end_time' => '13:00',
            'consultation_type' => ConsultationType::IN_PERSON,
            'slot_duration_minutes' => 30,
            'max_patients' => 8,
            'is_active' => true,
        ]);

        $indexResponse = $this->actingAs($this->staffUser)->get(route('staff.doctors.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee($this->doctorUser->name);

        $showResponse = $this->actingAs($this->staffUser)->get(route('staff.doctors.show', $this->doctor->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('09:00');
    }

    public function test_staff_can_view_doctor_schedules_matrix(): void
    {
        DoctorSchedule::create([
            'doctor_id' => $this->doctor->id,
            'day_of_week' => 2,
            'start_time' => '14:00',
            'end_time' => '18:00',
            'consultation_type' => ConsultationType::ONLINE,
            'slot_duration_minutes' => 20,
            'max_patients' => 10,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->staffUser)->get(route('staff.schedules.index'));
        $response->assertStatus(200);
        $response->assertSee('Tuesday');
    }

    public function test_staff_can_view_payments_and_billing_statuses(): void
    {
        $apt = Appointment::create([
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'booking_code' => 'APT-PAY-001',
            'appointment_date' => today(),
            'appointment_time' => '09:30:00',
            'consultation_type' => ConsultationType::IN_PERSON,
            'status' => AppointmentStatus::CONFIRMED,
            'total_fee' => 120.00,
        ]);

        Payment::create([
            'appointment_id' => $apt->id,
            'patient_id' => $this->patient->id,
            'payment_reference' => 'TX-STF-9988',
            'amount' => 120.00,
            'currency' => 'USD',
            'payment_method' => 'card',
            'status' => PaymentStatus::PAID,
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($this->staffUser)->get(route('staff.payments.index'));
        $response->assertStatus(200);
        $response->assertSee('TX-STF-9988');
        $response->assertSee('120.00');
    }

    public function test_staff_can_view_operational_activity_logs(): void
    {
        AuditLog::create([
            'user_id' => $this->staffUser->id,
            'action' => 'STAFF_INTAKE_TEST',
            'entity_type' => 'Patient',
            'entity_id' => $this->patient->id,
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actingAs($this->staffUser)->get(route('staff.activity.index'));
        $response->assertStatus(200);
        $response->assertSee('STAFF_INTAKE_TEST');
    }

    public function test_staff_can_view_notifications_and_mark_all_read(): void
    {
        Notification::create([
            'user_id' => $this->staffUser->id,
            'title' => 'Patient Arrival Alert',
            'message' => 'Patient has arrived for 10:00 AM consultation.',
            'notification_type' => 'appointment',
        ]);

        $indexResponse = $this->actingAs($this->staffUser)->get(route('staff.notifications.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Patient Arrival Alert');

        $readResponse = $this->actingAs($this->staffUser)->post(route('staff.notifications.read-all'));
        $readResponse->assertSessionHas('success');
    }

    public function test_staff_can_update_profile(): void
    {
        $showResponse = $this->actingAs($this->staffUser)->get(route('staff.profile'));
        $showResponse->assertStatus(200);

        $updateResponse = $this->actingAs($this->staffUser)->post(route('staff.profile.update'), [
            'name' => 'Updated Staff Name',
            'phone' => '+15553334444',
            'department' => 'Emergency Intake',
            'facility' => 'North Wing Clinic',
        ]);

        $updateResponse->assertSessionHas('success');
        $this->assertEquals('Updated Staff Name', $this->staffUser->fresh()->name);
        $this->assertEquals('Emergency Intake', $this->staff->fresh()->department);
    }

    public function test_staff_can_logout_securely(): void
    {
        $response = $this->actingAs($this->staffUser)->post(route('staff.logout'));
        $response->assertRedirect(route('staff.login'));
        $this->assertGuest();
    }
}
