<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Enums\ConsultationType;
use App\Enums\HealthMetricType;
use App\Enums\PrescriptionStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Appointment;
use App\Models\ChatConversation;
use App\Models\Doctor;
use App\Models\HealthMetric;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\Specialty;
use App\Models\Staff;
use App\Models\User;
use App\Policies\AppointmentPolicy;
use App\Policies\ChatConversationPolicy;
use App\Policies\HealthMetricPolicy;
use App\Policies\MedicalRecordPolicy;
use App\Policies\PatientPolicy;
use App\Policies\PrescriptionPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthorizationPolicyTest extends TestCase
{
    use RefreshDatabase;

    private User $patientUserA;
    private Patient $patientA;

    private User $patientUserB;
    private Patient $patientB;

    private User $doctorUser;
    private Doctor $doctor;

    private User $otherDoctorUser;
    private Doctor $otherDoctor;

    private User $staffUser;
    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $pass = Hash::make('Password123!');

        // Patient A
        $this->patientUserA = User::create([
            'name' => 'Patient A',
            'email' => 'patient.a@example.com',
            'password' => $pass,
            'role' => UserRole::PATIENT,
            'status' => UserStatus::ACTIVE,
        ]);
        $this->patientA = Patient::create([
            'user_id' => $this->patientUserA->id,
            'date_of_birth' => '1990-01-01',
        ]);

        // Patient B
        $this->patientUserB = User::create([
            'name' => 'Patient B',
            'email' => 'patient.b@example.com',
            'password' => $pass,
            'role' => UserRole::PATIENT,
            'status' => UserStatus::ACTIVE,
        ]);
        $this->patientB = Patient::create([
            'user_id' => $this->patientUserB->id,
            'date_of_birth' => '1992-02-02',
        ]);

        // Specialty
        $specialty = Specialty::create([
            'name' => 'Cardiology',
            'slug' => 'cardiology',
        ]);

        // Doctor A
        $this->doctorUser = User::create([
            'name' => 'Dr. Alpha',
            'email' => 'dr.alpha@healthcare.local',
            'password' => $pass,
            'role' => UserRole::DOCTOR,
            'status' => UserStatus::ACTIVE,
        ]);
        $this->doctor = Doctor::create([
            'user_id' => $this->doctorUser->id,
            'specialty_id' => $specialty->id,
            'license_number' => 'LIC-ALPHA-01',
        ]);

        // Doctor B
        $this->otherDoctorUser = User::create([
            'name' => 'Dr. Beta',
            'email' => 'dr.beta@healthcare.local',
            'password' => $pass,
            'role' => UserRole::DOCTOR,
            'status' => UserStatus::ACTIVE,
        ]);
        $this->otherDoctor = Doctor::create([
            'user_id' => $this->otherDoctorUser->id,
            'specialty_id' => $specialty->id,
            'license_number' => 'LIC-BETA-02',
        ]);

        // Staff
        $this->staffUser = User::create([
            'name' => 'Staff Member',
            'email' => 'staff@healthcare.local',
            'password' => $pass,
            'role' => UserRole::STAFF,
            'status' => UserStatus::ACTIVE,
        ]);
        Staff::create([
            'user_id' => $this->staffUser->id,
            'department' => 'Admissions',
            'employee_number' => 'EMP-001',
        ]);

        // Admin
        $this->adminUser = User::create([
            'name' => 'Admin Super',
            'email' => 'admin@healthcare.local',
            'password' => $pass,
            'role' => UserRole::ADMIN,
            'status' => UserStatus::ACTIVE,
        ]);
    }

    public function test_patient_cannot_view_or_update_other_patient_profile(): void
    {
        $policy = new PatientPolicy();

        $this->assertTrue($policy->view($this->patientUserA, $this->patientA));
        $this->assertFalse($policy->view($this->patientUserA, $this->patientB));

        $this->assertTrue($policy->update($this->patientUserA, $this->patientA));
        $this->assertFalse($policy->update($this->patientUserA, $this->patientB));

        $this->assertTrue($policy->view($this->adminUser, $this->patientB));
    }

    public function test_medical_record_isolation_across_patients_and_staff(): void
    {
        $recordA = MedicalRecord::create([
            'record_number' => 'MR-TEST-001',
            'patient_id' => $this->patientA->id,
            'doctor_id' => $this->doctor->id,
            'visit_date' => now()->toDateString(),
            'chief_complaint' => 'Chest tightness',
            'diagnosis' => 'Angina Pectoris',
        ]);

        $policy = new MedicalRecordPolicy();

        // Patient A can view their own record
        $this->assertTrue($policy->view($this->patientUserA, $recordA));

        // Patient B CANNOT view Patient A's medical record
        $this->assertFalse($policy->view($this->patientUserB, $recordA));

        // Attending doctor can view
        $this->assertTrue($policy->view($this->doctorUser, $recordA));

        // Unaffiliated doctor cannot view
        $this->assertFalse($policy->view($this->otherDoctorUser, $recordA));

        // Staff member cannot view sensitive clinical records
        $this->assertFalse($policy->view($this->staffUser, $recordA));

        // Admin can view
        $this->assertTrue($policy->view($this->adminUser, $recordA));

        // Only doctors and admins can create medical records
        $this->assertTrue($policy->create($this->doctorUser));
        $this->assertTrue($policy->create($this->adminUser));
        $this->assertFalse($policy->create($this->patientUserA));
        $this->assertFalse($policy->create($this->staffUser));
    }

    public function test_health_metric_isolation_across_patients(): void
    {
        $metricA = HealthMetric::create([
            'patient_id' => $this->patientA->id,
            'metric_type' => HealthMetricType::BLOOD_PRESSURE,
            'value' => 120.0,
            'secondary_value' => 80.0,
            'unit' => 'mmHg',
            'measured_at' => now(),
        ]);

        $policy = new HealthMetricPolicy();

        $this->assertTrue($policy->view($this->patientUserA, $metricA));
        $this->assertFalse($policy->view($this->patientUserB, $metricA));
        $this->assertTrue($policy->create($this->patientUserA, $metricA));
        $this->assertFalse($policy->create($this->patientUserB, $metricA));
    }

    public function test_appointment_access_policy(): void
    {
        $appointmentA = Appointment::create([
            'booking_code' => 'APT-TEST-001',
            'patient_id' => $this->patientA->id,
            'doctor_id' => $this->doctor->id,
            'appointment_date' => now()->addDay()->toDateString(),
            'appointment_time' => '10:00:00',
            'status' => AppointmentStatus::CONFIRMED,
            'consultation_type' => ConsultationType::IN_PERSON,
        ]);

        $policy = new AppointmentPolicy();

        // Patient A can view
        $this->assertTrue($policy->view($this->patientUserA, $appointmentA));

        // Patient B cannot view
        $this->assertFalse($policy->view($this->patientUserB, $appointmentA));

        // Assigned doctor can view
        $this->assertTrue($policy->view($this->doctorUser, $appointmentA));

        // Unassigned doctor cannot view
        $this->assertFalse($policy->view($this->otherDoctorUser, $appointmentA));

        // Staff can view appointments for operational reception
        $this->assertTrue($policy->view($this->staffUser, $appointmentA));
    }

    public function test_prescription_policy_isolation(): void
    {
        $rxA = Prescription::create([
            'prescription_code' => 'RX-TEST-001',
            'patient_id' => $this->patientA->id,
            'doctor_id' => $this->doctor->id,
            'prescription_date' => now()->toDateString(),
            'status' => PrescriptionStatus::ACTIVE,
        ]);

        $policy = new PrescriptionPolicy();

        $this->assertTrue($policy->view($this->patientUserA, $rxA));
        $this->assertFalse($policy->view($this->patientUserB, $rxA));
        $this->assertTrue($policy->view($this->doctorUser, $rxA));
        $this->assertFalse($policy->view($this->otherDoctorUser, $rxA));
    }

    public function test_chat_conversation_policy_isolation(): void
    {
        $conv = ChatConversation::create([
            'patient_id' => $this->patientA->id,
            'doctor_id' => $this->doctor->id,
            'status' => 'active',
        ]);

        $policy = new ChatConversationPolicy();

        $this->assertTrue($policy->view($this->patientUserA, $conv));
        $this->assertTrue($policy->view($this->doctorUser, $conv));
        $this->assertFalse($policy->view($this->patientUserB, $conv));
        $this->assertFalse($policy->view($this->otherDoctorUser, $conv));
    }
}
