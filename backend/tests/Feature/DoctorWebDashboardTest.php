<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Enums\ConsultationType;
use App\Enums\PrescriptionStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Appointment;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
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

class DoctorWebDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $doctorUser;
    protected Doctor $doctor;

    protected User $otherDoctorUser;
    protected Doctor $otherDoctor;

    protected User $patientUser;
    protected Patient $patient;

    protected User $unassignedPatientUser;
    protected Patient $unassignedPatient;

    protected Specialty $specialty;

    protected function setUp(): void
    {
        parent::setUp();

        $pass = Hash::make('Password123!');

        $this->specialty = Specialty::create(['name' => 'Cardiology', 'slug' => 'cardiology']);

        // Doctor A
        $this->doctorUser = User::create([
            'name' => 'Sarah Connor',
            'email' => 'sarah@healthcare.local',
            'password' => $pass,
            'role' => UserRole::DOCTOR,
            'status' => UserStatus::ACTIVE,
        ]);
        $this->doctor = Doctor::create([
            'user_id' => $this->doctorUser->id,
            'specialty_id' => $this->specialty->id,
            'license_number' => 'DOC-1001',
            'consultation_fee' => 150.00,
            'experience_years' => 12,
            'status' => 'active',
        ]);

        // Doctor B
        $this->otherDoctorUser = User::create([
            'name' => 'Gregory House',
            'email' => 'house@healthcare.local',
            'password' => $pass,
            'role' => UserRole::DOCTOR,
            'status' => UserStatus::ACTIVE,
        ]);
        $this->otherDoctor = Doctor::create([
            'user_id' => $this->otherDoctorUser->id,
            'specialty_id' => $this->specialty->id,
            'license_number' => 'DOC-1002',
            'consultation_fee' => 200.00,
            'experience_years' => 20,
            'status' => 'active',
        ]);

        // Assigned Patient
        $this->patientUser = User::create([
            'name' => 'John Patient',
            'email' => 'john@healthcare.local',
            'password' => $pass,
            'role' => UserRole::PATIENT,
            'status' => UserStatus::ACTIVE,
        ]);
        $this->patient = Patient::create([
            'user_id' => $this->patientUser->id,
            'blood_type' => 'A+',
        ]);

        // Unassigned Patient (no clinical relationship with Doctor A)
        $this->unassignedPatientUser = User::create([
            'name' => 'Stranger Patient',
            'email' => 'stranger@healthcare.local',
            'password' => $pass,
            'role' => UserRole::PATIENT,
            'status' => UserStatus::ACTIVE,
        ]);
        $this->unassignedPatient = Patient::create([
            'user_id' => $this->unassignedPatientUser->id,
            'blood_type' => 'B-',
        ]);

        // Create an appointment linking Doctor A to Assigned Patient
        Appointment::create([
            'booking_code' => 'APT-DOC-001',
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'appointment_date' => now()->toDateString(),
            'appointment_time' => '10:00:00',
            'consultation_type' => ConsultationType::IN_PERSON,
            'status' => AppointmentStatus::CONFIRMED,
            'consultation_fee' => 150.00,
            'service_fee' => 5.00,
            'total_amount' => 155.00,
        ]);
    }

    public function test_doctor_login_screen_can_be_rendered(): void
    {
        $response = $this->get(route('doctor.login'));
        $response->assertStatus(200);
        $response->assertSee('Doctor Sign In');
    }

    public function test_doctor_can_authenticate_and_redirect_to_dashboard(): void
    {
        $response = $this->post(route('doctor.login.submit'), [
            'email' => 'sarah@healthcare.local',
            'password' => 'Password123!',
        ]);

        $response->assertRedirect(route('doctor.dashboard'));
        $this->assertAuthenticatedAs($this->doctorUser);
    }

    public function test_patient_cannot_authenticate_via_doctor_login(): void
    {
        $response = $this->post(route('doctor.login.submit'), [
            'email' => 'john@healthcare.local',
            'password' => 'Password123!',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_unauthenticated_user_is_redirected_to_doctor_login(): void
    {
        $response = $this->get(route('doctor.dashboard'));
        $response->assertRedirect(route('doctor.login'));
    }

    public function test_patient_is_forbidden_from_doctor_routes(): void
    {
        $response = $this->actingAs($this->patientUser)->get(route('doctor.dashboard'));
        $response->assertStatus(403);
    }

    public function test_doctor_can_view_dashboard_with_metrics(): void
    {
        $response = $this->actingAs($this->doctorUser)->get(route('doctor.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Doctor Consultation Cockpit');
        $response->assertSee('APT-DOC-001');
    }

    public function test_doctor_can_view_assigned_appointments_and_update_status(): void
    {
        $appointment = Appointment::where('booking_code', 'APT-DOC-001')->first();

        $indexResponse = $this->actingAs($this->doctorUser)->get(route('doctor.appointments.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('APT-DOC-001');

        $showResponse = $this->actingAs($this->doctorUser)->get(route('doctor.appointments.show', $appointment->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('APT-DOC-001');

        $updateResponse = $this->actingAs($this->doctorUser)->post(route('doctor.appointments.status', $appointment->id), [
            'status' => 'completed',
            'notes' => 'Patient consultation concluded satisfactorily',
        ]);
        $updateResponse->assertSessionHas('success');

        $this->assertEquals(AppointmentStatus::COMPLETED, $appointment->fresh()->status);
    }

    public function test_doctor_cannot_view_or_update_other_doctors_appointments(): void
    {
        $otherAppointment = Appointment::create([
            'booking_code' => 'APT-HOUSE-999',
            'patient_id' => $this->unassignedPatient->id,
            'doctor_id' => $this->otherDoctor->id,
            'appointment_date' => now()->toDateString(),
            'appointment_time' => '14:00:00',
            'consultation_type' => ConsultationType::IN_PERSON,
            'status' => AppointmentStatus::CONFIRMED,
            'consultation_fee' => 200.00,
            'service_fee' => 5.00,
            'total_amount' => 205.00,
        ]);

        $showResponse = $this->actingAs($this->doctorUser)->get(route('doctor.appointments.show', $otherAppointment->id));
        $showResponse->assertStatus(403);

        $updateResponse = $this->actingAs($this->doctorUser)->post(route('doctor.appointments.status', $otherAppointment->id), [
            'status' => 'completed',
        ]);
        $updateResponse->assertStatus(403);
    }

    public function test_doctor_can_view_assigned_patient_chart(): void
    {
        $indexResponse = $this->actingAs($this->doctorUser)->get(route('doctor.patients.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('John Patient');

        $showResponse = $this->actingAs($this->doctorUser)->get(route('doctor.patients.show', $this->patient->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('John Patient');
        $showResponse->assertSee('A+');
    }

    public function test_doctor_cannot_view_unassigned_patient_chart(): void
    {
        $showResponse = $this->actingAs($this->doctorUser)->get(route('doctor.patients.show', $this->unassignedPatient->id));
        $showResponse->assertStatus(403);
    }

    public function test_doctor_can_create_and_view_medical_record(): void
    {
        $createResponse = $this->actingAs($this->doctorUser)->get(route('doctor.medical-records.create'));
        $createResponse->assertStatus(200);

        $storeResponse = $this->actingAs($this->doctorUser)->post(route('doctor.medical-records.store'), [
            'patient_id' => $this->patient->id,
            'visit_date' => now()->toDateString(),
            'chief_complaint' => 'Palpitations',
            'diagnosis' => 'Sinus Tachycardia',
            'treatment' => 'Beta-blocker trial',
            'systolic' => 125,
            'diastolic' => 82,
            'heart_rate' => 95,
        ]);

        $storeResponse->assertSessionHas('success');

        $record = MedicalRecord::where('patient_id', $this->patient->id)->where('doctor_id', $this->doctor->id)->first();
        $this->assertNotNull($record);

        $showResponse = $this->actingAs($this->doctorUser)->get(route('doctor.medical-records.show', $record->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Sinus Tachycardia');
    }

    public function test_doctor_cannot_view_other_doctors_medical_record(): void
    {
        $otherRecord = MedicalRecord::create([
            'record_number' => 'REC-OTHER-01',
            'patient_id' => $this->unassignedPatient->id,
            'doctor_id' => $this->otherDoctor->id,
            'visit_date' => now()->toDateString(),
            'chief_complaint' => 'Lupus check',
            'diagnosis' => 'Autoimmune test',
        ]);

        $response = $this->actingAs($this->doctorUser)->get(route('doctor.medical-records.show', $otherRecord->id));
        $response->assertStatus(403);
    }

    public function test_doctor_can_create_and_view_prescription(): void
    {
        $createResponse = $this->actingAs($this->doctorUser)->get(route('doctor.prescriptions.create'));
        $createResponse->assertStatus(200);

        $storeResponse = $this->actingAs($this->doctorUser)->post(route('doctor.prescriptions.store'), [
            'patient_id' => $this->patient->id,
            'prescription_date' => now()->toDateString(),
            'notes' => 'Take with water',
            'items' => [
                [
                    'medicine_name' => 'Metoprolol',
                    'dosage' => '25mg',
                    'dosage_form' => 'Tablet',
                    'frequency' => 'Once daily',
                    'duration' => '30 days',
                    'quantity' => 30,
                    'refills_available' => 2,
                    'instructions' => 'Take in the morning',
                ],
            ],
        ]);

        $storeResponse->assertSessionHas('success');

        $rx = Prescription::where('patient_id', $this->patient->id)->where('doctor_id', $this->doctor->id)->first();
        $this->assertNotNull($rx);

        $showResponse = $this->actingAs($this->doctorUser)->get(route('doctor.prescriptions.show', $rx->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Metoprolol');
    }

    public function test_doctor_cannot_view_other_doctors_prescription(): void
    {
        $otherRx = Prescription::create([
            'prescription_code' => 'RX-OTHER-01',
            'patient_id' => $this->unassignedPatient->id,
            'doctor_id' => $this->otherDoctor->id,
            'prescription_date' => now()->toDateString(),
            'status' => PrescriptionStatus::ACTIVE,
        ]);

        $response = $this->actingAs($this->doctorUser)->get(route('doctor.prescriptions.show', $otherRx->id));
        $response->assertStatus(403);
    }

    public function test_doctor_can_view_health_metrics(): void
    {
        HealthMetric::create([
            'patient_id' => $this->patient->id,
            'metric_type' => 'heart_rate',
            'value' => 78.0,
            'unit' => 'bpm',
            'measured_at' => now(),
        ]);

        $response = $this->actingAs($this->doctorUser)->get(route('doctor.health-metrics.index'));
        $response->assertStatus(200);
        $response->assertSee('78');
    }

    public function test_doctor_can_manage_schedules_and_toggle(): void
    {
        $indexResponse = $this->actingAs($this->doctorUser)->get(route('doctor.schedules.index'));
        $indexResponse->assertStatus(200);

        $storeResponse = $this->actingAs($this->doctorUser)->post(route('doctor.schedules.store'), [
            'day_of_week' => 1,
            'start_time' => '08:00',
            'end_time' => '12:00',
            'consultation_type' => 'in_person',
            'slot_duration_minutes' => 30,
            'max_patients' => 8,
        ]);
        $storeResponse->assertSessionHas('success');

        $schedule = DoctorSchedule::where('doctor_id', $this->doctor->id)->first();
        $this->assertNotNull($schedule);

        $toggleResponse = $this->actingAs($this->doctorUser)->post(route('doctor.schedules.toggle', $schedule->id));
        $toggleResponse->assertSessionHas('success');
        $this->assertFalse($schedule->fresh()->is_available);
    }

    public function test_doctor_can_chat_and_send_messages(): void
    {
        $conv = ChatConversation::create([
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'last_message_at' => now(),
        ]);

        $chatIndex = $this->actingAs($this->doctorUser)->get(route('doctor.chat.index'));
        $chatIndex->assertStatus(200);

        $chatShow = $this->actingAs($this->doctorUser)->get(route('doctor.chat.show', $conv->id));
        $chatShow->assertStatus(200);

        $sendMsg = $this->actingAs($this->doctorUser)->post(route('doctor.chat.send', $conv->id), [
            'message' => 'Hello John, please rest and report back if symptoms persist.',
        ]);
        $sendMsg->assertRedirect(route('doctor.chat.show', $conv->id));

        $msg = ChatMessage::where('conversation_id', $conv->id)->first();
        $this->assertNotNull($msg);
        $this->assertEquals('Hello John, please rest and report back if symptoms persist.', $msg->message);
    }

    public function test_doctor_can_view_notifications_and_mark_all_read(): void
    {
        Notification::create([
            'user_id' => $this->doctorUser->id,
            'title' => 'Shift Reminder',
            'message' => 'Your shift begins in 1 hour',
            'notification_type' => 'clinical',
            'read_at' => null,
        ]);

        $notifIndex = $this->actingAs($this->doctorUser)->get(route('doctor.notifications.index'));
        $notifIndex->assertStatus(200);
        $notifIndex->assertSee('Shift Reminder');

        $readAll = $this->actingAs($this->doctorUser)->post(route('doctor.notifications.read-all'));
        $readAll->assertSessionHas('success');
    }

    public function test_doctor_can_update_profile(): void
    {
        $profileResponse = $this->actingAs($this->doctorUser)->get(route('doctor.profile'));
        $profileResponse->assertStatus(200);
        $profileResponse->assertSee('DOC-1001');

        $updateResponse = $this->actingAs($this->doctorUser)->post(route('doctor.profile.update'), [
            'name' => 'Sarah Connor MD',
            'phone' => '+15550199',
            'experience_years' => 14,
            'consultation_fee' => 165.00,
            'facility' => 'Metropolitan Heart Center',
            'status' => 'active',
            'biography' => 'Specializing in adult cardiology and interventional care.',
        ]);

        $updateResponse->assertSessionHas('success');
        $this->assertEquals('Sarah Connor MD', $this->doctorUser->fresh()->name);
        $this->assertEquals(14, $this->doctor->fresh()->experience_years);
    }

    public function test_doctor_can_logout_securely(): void
    {
        $response = $this->actingAs($this->doctorUser)->post(route('doctor.logout'));
        $response->assertRedirect(route('doctor.login'));
        $this->assertGuest();
    }
}
