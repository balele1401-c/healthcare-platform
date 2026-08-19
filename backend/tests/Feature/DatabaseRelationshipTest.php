<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Enums\ConsultationType;
use App\Enums\HealthMetricType;
use App\Enums\PaymentStatus;
use App\Enums\PrescriptionStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\HealthMetric;
use App\Models\MedicalRecord;
use App\Models\Medicine;
use App\Models\Notification;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Specialty;
use App\Models\Staff;
use App\Models\User;
use App\Models\VitalSign;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DatabaseRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_18_model_relationships_function_correctly(): void
    {
        $pass = Hash::make('Password123!');

        // 1. Specialty
        $specialty = Specialty::create([
            'name' => 'Cardiology',
            'slug' => 'cardiology',
        ]);

        // 2. Doctor User & Doctor Profile
        $docUser = User::create([
            'name' => 'Dr. Emily Chen',
            'email' => 'dr.chen@example.com',
            'password' => $pass,
            'role' => UserRole::DOCTOR,
            'status' => UserStatus::ACTIVE,
        ]);
        $doctor = Doctor::create([
            'user_id' => $docUser->id,
            'specialty_id' => $specialty->id,
            'license_number' => 'MD-1001',
        ]);

        // 3. Patient User & Patient Profile
        $patientUser = User::create([
            'name' => 'Sarah Jenkins',
            'email' => 'sarah.jenkins@example.com',
            'password' => $pass,
            'role' => UserRole::PATIENT,
            'status' => UserStatus::ACTIVE,
        ]);
        $patient = Patient::create([
            'user_id' => $patientUser->id,
            'date_of_birth' => '1992-04-15',
        ]);

        // 4. Staff User & Staff Profile
        $staffUser = User::create([
            'name' => 'Staff Admin',
            'email' => 'staff@example.com',
            'password' => $pass,
            'role' => UserRole::STAFF,
            'status' => UserStatus::ACTIVE,
        ]);
        $staff = Staff::create([
            'user_id' => $staffUser->id,
            'department' => 'Admissions',
            'employee_number' => 'EMP-100',
        ]);

        // Verify User HasOne relationships
        $this->assertEquals($docUser->doctor->id, $doctor->id);
        $this->assertEquals($doctor->user->id, $docUser->id);
        $this->assertEquals($patientUser->patient->id, $patient->id);
        $this->assertEquals($patient->user->id, $patientUser->id);
        $this->assertEquals($staffUser->staff->id, $staff->id);
        $this->assertEquals($staff->user->id, $staffUser->id);
        $this->assertEquals($doctor->specialty->id, $specialty->id);
        $this->assertTrue($specialty->doctors->contains($doctor));

        // 5. DoctorSchedule
        $schedule = DoctorSchedule::create([
            'doctor_id' => $doctor->id,
            'day_of_week' => 1,
            'start_time' => '09:00:00',
            'end_time' => '12:00:00',
            'consultation_type' => ConsultationType::IN_PERSON,
        ]);
        $this->assertEquals($schedule->doctor->id, $doctor->id);
        $this->assertTrue($doctor->schedules->contains($schedule));

        // 6. Appointment
        $appointment = Appointment::create([
            'booking_code' => 'APT-001',
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'doctor_schedule_id' => $schedule->id,
            'appointment_date' => now()->addDay()->toDateString(),
            'appointment_time' => '09:30:00',
            'status' => AppointmentStatus::CONFIRMED,
            'consultation_type' => ConsultationType::IN_PERSON,
            'consultation_fee' => 75.00,
            'total_amount' => 75.00,
        ]);
        $this->assertEquals($appointment->patient->id, $patient->id);
        $this->assertEquals($appointment->doctor->id, $doctor->id);
        $this->assertEquals($appointment->doctorSchedule->id, $schedule->id);
        $this->assertTrue($patient->appointments->contains($appointment));
        $this->assertTrue($doctor->appointments->contains($appointment));

        // 7. Payment
        $payment = Payment::create([
            'payment_reference' => 'PAY-001',
            'appointment_id' => $appointment->id,
            'patient_id' => $patient->id,
            'amount' => 75.00,
            'payment_method' => 'Credit Card',
            'status' => PaymentStatus::PAID,
        ]);
        $this->assertEquals($payment->appointment->id, $appointment->id);
        $this->assertEquals($payment->patient->id, $patient->id);
        $this->assertEquals($appointment->payment->id, $payment->id);

        // 8. MedicalRecord
        $record = MedicalRecord::create([
            'record_number' => 'MR-001',
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_id' => $appointment->id,
            'visit_date' => now()->toDateString(),
            'chief_complaint' => 'Hypertension follow-up',
            'diagnosis' => 'Essential Hypertension',
        ]);
        $this->assertEquals($record->patient->id, $patient->id);
        $this->assertEquals($record->doctor->id, $doctor->id);
        $this->assertEquals($record->appointment->id, $appointment->id);
        $this->assertEquals($appointment->medicalRecord->id, $record->id);
        $this->assertTrue($patient->medicalRecords->contains($record));
        $this->assertTrue($doctor->medicalRecords->contains($record));

        // 9. VitalSign
        $vital = VitalSign::create([
            'medical_record_id' => $record->id,
            'systolic_blood_pressure' => 120,
            'diastolic_blood_pressure' => 80,
            'heart_rate' => 72,
            'measured_at' => now(),
        ]);
        $this->assertEquals($vital->medicalRecord->id, $record->id);
        $this->assertEquals($record->vitalSigns->id, $vital->id);

        // 10. Medicine & Prescription & PrescriptionItem
        $medicine = Medicine::create([
            'name' => 'Amlodipine',
            'generic_name' => 'Amlodipine Besylate',
            'strength' => '5mg',
        ]);

        $prescription = Prescription::create([
            'prescription_code' => 'RX-001',
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'medical_record_id' => $record->id,
            'prescription_date' => now()->toDateString(),
            'status' => PrescriptionStatus::ACTIVE,
        ]);

        $rxItem = PrescriptionItem::create([
            'prescription_id' => $prescription->id,
            'medicine_id' => $medicine->id,
            'dosage' => '5mg',
            'frequency' => 'Once daily',
            'duration' => '30 Days',
            'quantity' => 30,
        ]);

        $this->assertEquals($prescription->patient->id, $patient->id);
        $this->assertEquals($prescription->doctor->id, $doctor->id);
        $this->assertEquals($prescription->medicalRecord->id, $record->id);
        $this->assertTrue($prescription->items->contains($rxItem));
        $this->assertEquals($rxItem->prescription->id, $prescription->id);
        $this->assertEquals($rxItem->medicine->id, $medicine->id);
        $this->assertTrue($medicine->prescriptionItems->contains($rxItem));

        // 11. HealthMetric
        $metric = HealthMetric::create([
            'patient_id' => $patient->id,
            'metric_type' => HealthMetricType::HEART_RATE,
            'value' => 72.0,
            'unit' => 'bpm',
            'measured_at' => now(),
        ]);
        $this->assertEquals($metric->patient->id, $patient->id);
        $this->assertTrue($patient->healthMetrics->contains($metric));

        // 12. Notification
        $notification = Notification::create([
            'user_id' => $patientUser->id,
            'title' => 'Appointment Reminder',
            'message' => 'Visit tomorrow at 9:30 AM',
            'notification_type' => 'appointment',
        ]);
        $this->assertEquals($notification->user->id, $patientUser->id);
        $this->assertTrue($patientUser->notifications->contains($notification));

        // 13. ChatConversation & ChatMessage
        $conversation = ChatConversation::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_id' => $appointment->id,
            'status' => 'active',
        ]);

        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $patientUser->id,
            'message' => 'Hello doctor',
        ]);

        $this->assertEquals($conversation->patient->id, $patient->id);
        $this->assertEquals($conversation->doctor->id, $doctor->id);
        $this->assertTrue($conversation->messages->contains($message));
        $this->assertEquals($message->conversation->id, $conversation->id);
        $this->assertEquals($message->sender->id, $patientUser->id);

        // 14. AuditLog
        $audit = AuditLog::create([
            'user_id' => $patientUser->id,
            'action' => 'VIEW_RECORD',
            'entity_type' => 'MedicalRecord',
            'entity_id' => $record->id,
            'created_at' => now(),
        ]);
        $this->assertEquals($audit->user->id, $patientUser->id);
        $this->assertTrue($patientUser->auditLogs->contains($audit));
    }
}
