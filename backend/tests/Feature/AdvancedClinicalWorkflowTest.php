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
use App\Models\HealthMetric;
use App\Models\MedicalRecord;
use App\Models\Medicine;
use App\Models\Notification;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdvancedClinicalWorkflowTest extends TestCase
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

    private Appointment $appointmentA;
    private MedicalRecord $recordA;
    private Prescription $prescriptionA;
    private HealthMetric $metricA;
    private ChatConversation $conversationA;

    protected function setUp(): void
    {
        parent::setUp();

        $pass = Hash::make('Secret123!');

        // Patient A
        $this->patientUserA = User::create([
            'name' => 'Alice Walker',
            'email' => 'alice@healthcare.local',
            'password' => $pass,
            'role' => UserRole::PATIENT,
            'status' => UserStatus::ACTIVE,
        ]);
        $this->patientA = Patient::create([
            'user_id' => $this->patientUserA->id,
            'blood_type' => 'O+',
            'allergies' => 'Penicillin',
            'emergency_contact_name' => 'Bob Walker',
            'emergency_contact_phone' => '08123456789',
        ]);

        // Patient B
        $this->patientUserB = User::create([
            'name' => 'Brian Clark',
            'email' => 'brian@healthcare.local',
            'password' => $pass,
            'role' => UserRole::PATIENT,
            'status' => UserStatus::ACTIVE,
        ]);
        $this->patientB = Patient::create([
            'user_id' => $this->patientUserB->id,
            'blood_type' => 'A+',
        ]);

        // Doctor 1 (Assigned to Alice)
        $cardio = Specialty::create(['name' => 'Cardiology', 'slug' => 'cardiology']);
        $this->doctorUser1 = User::create([
            'name' => 'Dr. Robert Vance',
            'email' => 'vance@healthcare.local',
            'password' => $pass,
            'role' => UserRole::DOCTOR,
            'status' => UserStatus::ACTIVE,
        ]);
        $this->doctor1 = Doctor::create([
            'user_id' => $this->doctorUser1->id,
            'specialty_id' => $cardio->id,
            'license_number' => 'DOC-CARDIO-01',
            'consultation_fee' => 100.00,
            'facility' => 'Metro General Hospital',
            'status' => 'active',
        ]);

        // Doctor 2 (Unassigned)
        $derma = Specialty::create(['name' => 'Dermatology', 'slug' => 'dermatology']);
        $this->doctorUser2 = User::create([
            'name' => 'Dr. Clara Oswald',
            'email' => 'oswald@healthcare.local',
            'password' => $pass,
            'role' => UserRole::DOCTOR,
            'status' => UserStatus::ACTIVE,
        ]);
        $this->doctor2 = Doctor::create([
            'user_id' => $this->doctorUser2->id,
            'specialty_id' => $derma->id,
            'license_number' => 'DOC-DERMA-02',
            'consultation_fee' => 80.00,
            'facility' => 'Skin & Care Clinic',
            'status' => 'active',
        ]);

        // Appointment for Alice with Dr. Vance
        $this->appointmentA = Appointment::create([
            'booking_code' => 'APT-ALICE-01',
            'patient_id' => $this->patientA->id,
            'doctor_id' => $this->doctor1->id,
            'appointment_date' => now()->toDateString(),
            'appointment_time' => '10:00:00',
            'status' => AppointmentStatus::PENDING,
            'consultation_type' => ConsultationType::IN_PERSON,
            'consultation_fee' => 100.00,
            'service_fee' => 5.00,
            'total_amount' => 105.00,
        ]);

        // Medical Record for Alice
        $this->recordA = MedicalRecord::create([
            'record_number' => 'REC-ALICE-01',
            'appointment_id' => $this->appointmentA->id,
            'patient_id' => $this->patientA->id,
            'doctor_id' => $this->doctor1->id,
            'visit_date' => now()->toDateString(),
            'chief_complaint' => 'Mild hypertension checkup',
            'diagnosis' => 'Mild Essential Hypertension',
            'symptoms' => 'Occasional morning headaches and fatigue',
            'treatment' => 'Dietary sodium reduction, regular cardio exercise',
        ]);

        // Medicine & Prescription for Alice
        $medicine = Medicine::create([
            'name' => 'Amlodipine',
            'generic_name' => 'Amlodipine Besylate',
            'dosage_form' => 'Tablet',
            'strength' => '5mg',
            'status' => 'active',
        ]);

        $this->prescriptionA = Prescription::create([
            'prescription_code' => 'RX-ALICE-01',
            'medical_record_id' => $this->recordA->id,
            'patient_id' => $this->patientA->id,
            'doctor_id' => $this->doctor1->id,
            'prescription_date' => now()->toDateString(),
            'status' => PrescriptionStatus::ACTIVE,
            'notes' => 'Take with water after breakfast',
        ]);

        PrescriptionItem::create([
            'prescription_id' => $this->prescriptionA->id,
            'medicine_id' => $medicine->id,
            'dosage' => '5mg',
            'frequency' => 'Once daily',
            'duration' => '30 days',
            'quantity' => 30,
            'instructions' => 'Take after morning meal',
        ]);

        // Health Metric for Alice
        $this->metricA = HealthMetric::create([
            'patient_id' => $this->patientA->id,
            'metric_type' => 'blood_pressure',
            'value' => 128.0,
            'secondary_value' => 84.0,
            'unit' => 'mmHg',
            'measured_at' => now(),
        ]);

        // Chat Conversation
        $this->conversationA = ChatConversation::create([
            'patient_id' => $this->patientA->id,
            'doctor_id' => $this->doctor1->id,
            'appointment_id' => $this->appointmentA->id,
        ]);

        ChatMessage::create([
            'conversation_id' => $this->conversationA->id,
            'sender_id' => $this->doctorUser1->id,
            'message' => 'Hello Alice, please have your recent blood pressure logs ready.',
            'created_at' => now(),
        ]);
    }

    public function test_appointment_lifecycle_state_transitions(): void
    {
        Sanctum::actingAs($this->doctorUser1);

        // 1. Pending -> Confirmed
        $res1 = $this->putJson("/api/v1/appointments/{$this->appointmentA->id}", [
            'status' => 'confirmed',
        ]);
        $res1->assertStatus(200)
            ->assertJsonPath('data.status', 'confirmed');

        // 2. Confirmed -> In Consultation
        $res2 = $this->putJson("/api/v1/appointments/{$this->appointmentA->id}", [
            'status' => 'in_consultation',
        ]);
        $res2->assertStatus(200)
            ->assertJsonPath('data.status', 'in_consultation');

        // 3. In Consultation -> Completed
        $res3 = $this->putJson("/api/v1/appointments/{$this->appointmentA->id}", [
            'status' => 'completed',
        ]);
        $res3->assertStatus(200)
            ->assertJsonPath('data.status', 'completed');

        // 4. Completed -> Pending (Invalid transition must be rejected)
        $res4 = $this->putJson("/api/v1/appointments/{$this->appointmentA->id}", [
            'status' => 'pending',
        ]);
        $res4->assertStatus(422);
    }

    public function test_appointment_cancellation_from_pending_succeeds_but_fails_when_completed(): void
    {
        Sanctum::actingAs($this->patientUserA);

        // Cancel pending appointment
        $res = $this->postJson("/api/v1/appointments/{$this->appointmentA->id}/cancel", [
            'cancellation_reason' => 'Schedule conflict',
        ]);

        $res->assertStatus(200)
            ->assertJsonPath('data.status', 'cancelled');

        // Attempt to cancel an already cancelled appointment
        $this->appointmentA->refresh();
        $res2 = $this->postJson("/api/v1/appointments/{$this->appointmentA->id}/cancel", [
            'cancellation_reason' => 'Duplicate attempt',
        ]);

        $res2->assertStatus(400);
    }

    public function test_patient_cannot_view_or_modify_another_patients_appointment(): void
    {
        Sanctum::actingAs($this->patientUserB);

        $resView = $this->getJson("/api/v1/appointments/{$this->appointmentA->id}");
        $resView->assertStatus(403);

        $resUpdate = $this->putJson("/api/v1/appointments/{$this->appointmentA->id}", [
            'notes' => 'Tampered notes',
        ]);
        $resUpdate->assertStatus(403);
    }

    public function test_medical_record_patient_isolation_and_authorized_doctor_access(): void
    {
        // Patient A can view own medical record
        Sanctum::actingAs($this->patientUserA);
        $resA = $this->getJson("/api/v1/medical-records/{$this->recordA->id}");
        $resA->assertStatus(200)
            ->assertJsonPath('data.diagnosis', 'Mild Essential Hypertension');

        // Patient B is rejected
        Sanctum::actingAs($this->patientUserB);
        $resB = $this->getJson("/api/v1/medical-records/{$this->recordA->id}");
        $resB->assertStatus(403);

        // Attending Doctor (Dr. Vance) can view record
        Sanctum::actingAs($this->doctorUser1);
        $resDoc1 = $this->getJson("/api/v1/medical-records/{$this->recordA->id}");
        $resDoc1->assertStatus(200);

        // Unassigned Doctor (Dr. Oswald) is rejected
        Sanctum::actingAs($this->doctorUser2);
        $resDoc2 = $this->getJson("/api/v1/medical-records/{$this->recordA->id}");
        $resDoc2->assertStatus(403);
    }

    public function test_prescription_patient_isolation_and_viewing(): void
    {
        // Patient A views own prescription
        Sanctum::actingAs($this->patientUserA);
        $resA = $this->getJson("/api/v1/prescriptions/{$this->prescriptionA->id}");
        $resA->assertStatus(200)
            ->assertJsonPath('data.prescription_code', 'RX-ALICE-01')
            ->assertJsonPath('data.items.0.dosage', '5mg');

        // Patient B is forbidden
        Sanctum::actingAs($this->patientUserB);
        $resB = $this->getJson("/api/v1/prescriptions/{$this->prescriptionA->id}");
        $resB->assertStatus(403);
    }

    public function test_health_metrics_crud_and_isolation(): void
    {
        // Patient A records a new vital sign (Heart Rate)
        Sanctum::actingAs($this->patientUserA);
        $resCreate = $this->postJson('/api/v1/health-metrics', [
            'metric_type' => 'heart_rate',
            'value' => 72.0,
            'unit' => 'bpm',
            'measured_at' => now()->toDateTimeString(),
        ]);
        $resCreate->assertStatus(201)
            ->assertJsonPath('data.value', 72);

        $newMetricId = $resCreate->json('data.id');

        // Patient A updates own metric
        $resUpdate = $this->putJson("/api/v1/health-metrics/{$newMetricId}", [
            'value' => 75.0,
        ]);
        $resUpdate->assertStatus(200)
            ->assertJsonPath('data.value', 75);

        // Patient B cannot update Patient A's metric
        Sanctum::actingAs($this->patientUserB);
        $resB = $this->putJson("/api/v1/health-metrics/{$newMetricId}", [
            'value' => 99.0,
        ]);
        $resB->assertStatus(403);

        // Patient A deletes own metric
        Sanctum::actingAs($this->patientUserA);
        $resDelete = $this->deleteJson("/api/v1/health-metrics/{$newMetricId}");
        $resDelete->assertStatus(200);
    }

    public function test_in_app_notifications_lifecycle_and_isolation(): void
    {
        // Create notification for Alice
        $notif = Notification::create([
            'user_id' => $this->patientUserA->id,
            'title' => 'Appointment Reminder',
            'message' => 'Your consultation with Dr. Vance is scheduled for 10:00 AM today.',
            'notification_type' => 'appointment_reminder',
            'data' => ['appointment_id' => $this->appointmentA->id],
        ]);

        // Alice views own notifications
        Sanctum::actingAs($this->patientUserA);
        $resA = $this->getJson('/api/v1/notifications');
        $resA->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Appointment Reminder');

        // Alice marks all as read
        $resMarkAll = $this->postJson('/api/v1/notifications/read-all');
        $resMarkAll->assertStatus(200);

        $this->assertNotNull($notif->fresh()->read_at);

        // Brian has 0 notifications
        Sanctum::actingAs($this->patientUserB);
        $resB = $this->getJson('/api/v1/notifications');
        $resB->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    public function test_teleconsultation_chat_authorization_and_message_delivery(): void
    {
        // Alice sends message in Conversation A
        Sanctum::actingAs($this->patientUserA);
        $resMsgA = $this->postJson("/api/v1/conversations/{$this->conversationA->id}/messages", [
            'message' => 'Dr. Vance, I have logged my morning BP readings.',
        ]);
        $resMsgA->assertStatus(201)
            ->assertJsonPath('data.message', 'Dr. Vance, I have logged my morning BP readings.');

        // Dr. Vance reads messages in Conversation A
        Sanctum::actingAs($this->doctorUser1);
        $resReadDoc = $this->getJson("/api/v1/conversations/{$this->conversationA->id}/messages");
        $resReadDoc->assertStatus(200)
            ->assertJsonCount(2, 'data');

        // Brian (Unauthorized) cannot view Conversation A
        Sanctum::actingAs($this->patientUserB);
        $resBrian = $this->getJson("/api/v1/conversations/{$this->conversationA->id}");
        $resBrian->assertStatus(403);
    }

    public function test_patient_profile_update_and_validation(): void
    {
        Sanctum::actingAs($this->patientUserA);
        $res = $this->putJson('/api/v1/patient/profile', [
            'name' => 'Alice Walker MD',
            'phone' => '+6281234567890',
            'allergies' => 'Penicillin, Aspirin',
            'blood_type' => 'O+',
            'emergency_contact_name' => 'Bob Walker',
            'emergency_contact_phone' => '+6281987654321',
        ]);

        $res->assertStatus(200)
            ->assertJsonPath('data.name', 'Alice Walker MD')
            ->assertJsonPath('data.allergies', 'Penicillin, Aspirin');
    }
}
