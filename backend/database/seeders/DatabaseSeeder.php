<?php

namespace Database\Seeders;

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
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the production/development database idempotently with clinical, demographic, and transactional records.
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('Password123!');

        // 1. Admin User
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@healthcare.local'],
            [
                'name' => 'System Administrator',
                'phone' => '+15550000001',
                'password' => $defaultPassword,
                'role' => UserRole::ADMIN,
                'status' => UserStatus::ACTIVE,
                'email_verified_at' => now(),
            ]
        );

        AuditLog::firstOrCreate(
            ['user_id' => $adminUser->id, 'action' => 'SYSTEM_INIT'],
            [
                'entity_type' => 'System',
                'entity_id' => 1,
                'new_data' => ['note' => 'Healthcare platform database initialization'],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Seeder CLI',
                'created_at' => now(),
            ]
        );

        // 2. Specialties
        $cardiology = Specialty::firstOrCreate(
            ['slug' => 'cardiology'],
            [
                'name' => 'Cardiology',
                'description' => 'Heart, vascular system, and cardiovascular disease prevention.',
                'icon' => 'favorite',
                'status' => 'active',
            ]
        );

        $dermatology = Specialty::firstOrCreate(
            ['slug' => 'dermatology'],
            [
                'name' => 'Dermatology',
                'description' => 'Diagnosis and treatment of skin, hair, and nail conditions.',
                'icon' => 'healing',
                'status' => 'active',
            ]
        );

        $neurology = Specialty::firstOrCreate(
            ['slug' => 'neurology'],
            [
                'name' => 'Neurology',
                'description' => 'Brain, spinal cord, nerves, and neurological disorders.',
                'icon' => 'psychology',
                'status' => 'active',
            ]
        );

        $pediatrics = Specialty::firstOrCreate(
            ['slug' => 'pediatrics'],
            [
                'name' => 'Pediatrics',
                'description' => 'Medical care for infants, children, and adolescents.',
                'icon' => 'child_care',
                'status' => 'active',
            ]
        );

        $orthopedics = Specialty::firstOrCreate(
            ['slug' => 'orthopedics'],
            [
                'name' => 'Orthopedics',
                'description' => 'Musculoskeletal system, bones, joints, ligaments, and spine.',
                'icon' => 'accessibility_new',
                'status' => 'active',
            ]
        );

        $familyMedicine = Specialty::firstOrCreate(
            ['slug' => 'family-medicine'],
            [
                'name' => 'Family Medicine',
                'description' => 'Comprehensive healthcare for individuals and families across all ages.',
                'icon' => 'medical_services',
                'status' => 'active',
            ]
        );

        // 3. Doctors
        $docUser1 = User::firstOrCreate(
            ['email' => 'dr.chen@healthcare.local'],
            [
                'name' => 'Dr. Emily Chen',
                'phone' => '+15550192801',
                'password' => $defaultPassword,
                'role' => UserRole::DOCTOR,
                'status' => UserStatus::ACTIVE,
                'avatar_url' => 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=400&q=80',
                'email_verified_at' => now(),
            ]
        );

        $doctor1 = Doctor::firstOrCreate(
            ['user_id' => $docUser1->id],
            [
                'specialty_id' => $cardiology->id,
                'license_number' => 'MD-CARD-2012-8821',
                'biography' => 'Dr. Emily Chen is a board-certified cardiologist with over 12 years of experience in cardiovascular disease prevention and hypertension management.',
                'education' => 'Harvard Medical School, MD (2012); Johns Hopkins Cardiology Fellowship',
                'experience_years' => 12,
                'consultation_fee' => 75.00,
                'facility' => 'Metropolitan Heart & Vascular Institute',
                'profile_photo' => $docUser1->avatar_url,
                'rating' => 4.90,
                'review_count' => 128,
                'status' => 'active',
            ]
        );

        $docUser2 = User::firstOrCreate(
            ['email' => 'dr.vance@healthcare.local'],
            [
                'name' => 'Dr. Marcus Vance',
                'phone' => '+15550192802',
                'password' => $defaultPassword,
                'role' => UserRole::DOCTOR,
                'status' => UserStatus::ACTIVE,
                'avatar_url' => 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&w=400&q=80',
                'email_verified_at' => now(),
            ]
        );

        $doctor2 = Doctor::firstOrCreate(
            ['user_id' => $docUser2->id],
            [
                'specialty_id' => $dermatology->id,
                'license_number' => 'MD-DERM-2015-4412',
                'biography' => 'Dr. Marcus Vance specializes in clinical and surgical dermatology, eczema, acne therapies, and advanced laser dermatological procedures.',
                'education' => 'Stanford University School of Medicine (2015)',
                'experience_years' => 9,
                'consultation_fee' => 65.00,
                'facility' => 'Clarity Dermatology & Laser Clinic',
                'profile_photo' => $docUser2->avatar_url,
                'rating' => 4.80,
                'review_count' => 95,
                'status' => 'active',
            ]
        );

        // Doctor Schedules
        foreach ([1, 2, 3, 4, 5] as $day) {
            DoctorSchedule::firstOrCreate(
                [
                    'doctor_id' => $doctor1->id,
                    'day_of_week' => $day,
                    'consultation_type' => ConsultationType::IN_PERSON,
                ],
                [
                    'start_time' => '09:00:00',
                    'end_time' => '13:00:00',
                    'facility' => 'Metropolitan Heart Institute - Room 402',
                    'slot_duration_minutes' => 30,
                    'max_patients' => 8,
                    'is_available' => true,
                ]
            );

            DoctorSchedule::firstOrCreate(
                [
                    'doctor_id' => $doctor1->id,
                    'day_of_week' => $day,
                    'consultation_type' => ConsultationType::ONLINE,
                ],
                [
                    'start_time' => '14:00:00',
                    'end_time' => '17:00:00',
                    'facility' => 'Telehealth Virtual Room',
                    'slot_duration_minutes' => 30,
                    'max_patients' => 6,
                    'is_available' => true,
                ]
            );

            DoctorSchedule::firstOrCreate(
                [
                    'doctor_id' => $doctor2->id,
                    'day_of_week' => $day,
                    'consultation_type' => ConsultationType::IN_PERSON,
                ],
                [
                    'start_time' => '10:00:00',
                    'end_time' => '16:00:00',
                    'facility' => 'Clarity Dermatology - Suite 210',
                    'slot_duration_minutes' => 30,
                    'max_patients' => 12,
                    'is_available' => true,
                ]
            );
        }

        // 4. Staff Users
        $staffUser1 = User::firstOrCreate(
            ['email' => 'staff.reception@healthcare.local'],
            [
                'name' => 'Rachel Green (Reception)',
                'phone' => '+15550193001',
                'password' => $defaultPassword,
                'role' => UserRole::STAFF,
                'status' => UserStatus::ACTIVE,
                'email_verified_at' => now(),
            ]
        );

        Staff::firstOrCreate(
            ['user_id' => $staffUser1->id],
            [
                'department' => 'Front Desk & Patient Admissions',
                'employee_number' => 'EMP-REC-001',
                'facility' => 'Central Metropolitan Hospital',
                'status' => 'active',
            ]
        );

        $staffUser2 = User::firstOrCreate(
            ['email' => 'staff.billing@healthcare.local'],
            [
                'name' => 'Monica Geller (Billing)',
                'phone' => '+15550193002',
                'password' => $defaultPassword,
                'role' => UserRole::STAFF,
                'status' => UserStatus::ACTIVE,
                'email_verified_at' => now(),
            ]
        );

        Staff::firstOrCreate(
            ['user_id' => $staffUser2->id],
            [
                'department' => 'Medical Billing & Insurance Operations',
                'employee_number' => 'EMP-BIL-002',
                'facility' => 'Central Metropolitan Hospital',
                'status' => 'active',
            ]
        );

        // 5. Patients (5 Demo Patients)
        $patientUsers = [
            [
                'name' => 'Sarah Jenkins',
                'email' => 'sarah.jenkins@example.com',
                'phone' => '+15550192834',
                'dob' => '1992-04-15',
                'gender' => 'female',
                'blood_type' => 'A+',
                'height' => 168.00,
                'weight' => 64.50,
                'address' => '742 Evergreen Terrace, Springfield',
                'emergency_name' => 'David Jenkins',
                'emergency_phone' => '+15550195821',
                'emergency_rel' => 'Spouse',
                'allergies' => 'Penicillin, Shellfish',
                'medical_history' => 'Mild essential hypertension diagnosed 2024.',
            ],
            [
                'name' => 'David Miller',
                'email' => 'david.miller@example.com',
                'phone' => '+15550192835',
                'dob' => '1985-08-22',
                'gender' => 'male',
                'blood_type' => 'O+',
                'height' => 180.00,
                'weight' => 82.00,
                'address' => '104 North Lake Avenue, Pasadena',
                'emergency_name' => 'Laura Miller',
                'emergency_phone' => '+15550195822',
                'emergency_rel' => 'Sister',
                'allergies' => 'None reported',
                'medical_history' => 'Seasonal allergic rhinitis.',
            ],
            [
                'name' => 'Elena Rostova',
                'email' => 'elena.rostova@example.com',
                'phone' => '+15550192836',
                'dob' => '1998-11-03',
                'gender' => 'female',
                'blood_type' => 'B+',
                'height' => 165.00,
                'weight' => 58.00,
                'address' => '55 Ocean Boulevard, Santa Monica',
                'emergency_name' => 'Sergei Rostov',
                'emergency_phone' => '+15550195823',
                'emergency_rel' => 'Father',
                'allergies' => 'Aspirin',
                'medical_history' => 'Contact dermatitis on upper extremities.',
            ],
            [
                'name' => 'Marcus Aurelius',
                'email' => 'marcus.aurelius@example.com',
                'phone' => '+15550192837',
                'dob' => '1976-02-14',
                'gender' => 'male',
                'blood_type' => 'AB+',
                'height' => 175.00,
                'weight' => 76.50,
                'address' => '88 Roman Way, Glendale',
                'emergency_name' => 'Faustina Aurelius',
                'emergency_phone' => '+15550195824',
                'emergency_rel' => 'Spouse',
                'allergies' => 'Latex',
                'medical_history' => 'Occasional tension headaches.',
            ],
            [
                'name' => 'Olivia Wilde',
                'email' => 'olivia.wilde@example.com',
                'phone' => '+15550192838',
                'dob' => '1990-07-19',
                'gender' => 'female',
                'blood_type' => 'O-',
                'height' => 170.00,
                'weight' => 61.00,
                'address' => '210 Sunset Strip, West Hollywood',
                'emergency_name' => 'Jason Sudeikis',
                'emergency_phone' => '+15550195825',
                'emergency_rel' => 'Partner',
                'allergies' => 'Sulfa drugs',
                'medical_history' => 'Annual wellness checks.',
            ],
        ];

        $patients = [];
        foreach ($patientUsers as $pData) {
            $u = User::firstOrCreate(
                ['email' => $pData['email']],
                [
                    'name' => $pData['name'],
                    'phone' => $pData['phone'],
                    'password' => $defaultPassword,
                    'role' => UserRole::PATIENT,
                    'status' => UserStatus::ACTIVE,
                    'email_verified_at' => now(),
                ]
            );

            $p = Patient::firstOrCreate(
                ['user_id' => $u->id],
                [
                    'date_of_birth' => $pData['dob'],
                    'gender' => $pData['gender'],
                    'blood_type' => $pData['blood_type'],
                    'height_cm' => $pData['height'],
                    'weight_kg' => $pData['weight'],
                    'address' => $pData['address'],
                    'emergency_contact_name' => $pData['emergency_name'],
                    'emergency_contact_phone' => $pData['emergency_phone'],
                    'emergency_contact_relation' => $pData['emergency_rel'],
                    'allergies' => $pData['allergies'],
                    'medical_history_summary' => $pData['medical_history'],
                ]
            );

            $patients[] = $p;
        }

        $primaryPatient = $patients[0]; // Sarah Jenkins

        // 6. Medicines (Catalog)
        $amlodipine = Medicine::firstOrCreate(
            ['name' => 'Amlodipine Besylate'],
            [
                'generic_name' => 'Amlodipine',
                'description' => 'Calcium channel blocker used to treat high blood pressure and angina.',
                'dosage_form' => 'Oral Tablet',
                'strength' => '5 mg',
                'manufacturer' => 'Pfizer Laboratories',
                'status' => 'active',
            ]
        );

        $lisinopril = Medicine::firstOrCreate(
            ['name' => 'Lisinopril'],
            [
                'generic_name' => 'Lisinopril',
                'description' => 'ACE inhibitor used to treat high blood pressure and heart failure.',
                'dosage_form' => 'Oral Tablet',
                'strength' => '10 mg',
                'manufacturer' => 'AstraZeneca',
                'status' => 'active',
            ]
        );

        $metformin = Medicine::firstOrCreate(
            ['name' => 'Metformin HCl'],
            [
                'generic_name' => 'Metformin',
                'description' => 'Biguanide antidiabetic medication for glycemic control.',
                'dosage_form' => 'Oral Tablet',
                'strength' => '500 mg',
                'manufacturer' => 'Merck',
                'status' => 'active',
            ]
        );

        $hydrocortisone = Medicine::firstOrCreate(
            ['name' => 'Hydrocortisone Topical Cream'],
            [
                'generic_name' => 'Hydrocortisone',
                'description' => 'Mild topical corticosteroid for skin redness, swelling, and itchiness.',
                'dosage_form' => 'Topical Cream',
                'strength' => '1.0%',
                'manufacturer' => 'GlaxoSmithKline',
                'status' => 'active',
            ]
        );

        $ibuprofen = Medicine::firstOrCreate(
            ['name' => 'Ibuprofen'],
            [
                'generic_name' => 'Ibuprofen',
                'description' => 'Nonsteroidal anti-inflammatory drug (NSAID) for fever and pain relief.',
                'dosage_form' => 'Oral Capsule',
                'strength' => '400 mg',
                'manufacturer' => 'Bayer HealthCare',
                'status' => 'active',
            ]
        );

        $amoxicillin = Medicine::firstOrCreate(
            ['name' => 'Amoxicillin Trihydrate'],
            [
                'generic_name' => 'Amoxicillin',
                'description' => 'Broad-spectrum penicillin antibiotic.',
                'dosage_form' => 'Oral Capsule',
                'strength' => '500 mg',
                'manufacturer' => 'Novartis Sandoz',
                'status' => 'active',
            ]
        );

        $melatonin = Medicine::firstOrCreate(
            ['name' => 'Melatonin Extended Release'],
            [
                'generic_name' => 'Melatonin',
                'description' => 'Hormone supplement supporting healthy circadian rhythm and sleep onset.',
                'dosage_form' => 'Oral Tablet',
                'strength' => '3 mg',
                'manufacturer' => 'Nature Made Health',
                'status' => 'active',
            ]
        );

        $paracetamol = Medicine::firstOrCreate(
            ['name' => 'Paracetamol (Acetaminophen)'],
            [
                'generic_name' => 'Acetaminophen',
                'description' => 'Analgesic and antipyretic agent for mild to moderate pain.',
                'dosage_form' => 'Oral Tablet',
                'strength' => '500 mg',
                'manufacturer' => 'Johnson & Johnson',
                'status' => 'active',
            ]
        );

        // 7. Appointments for Sarah Jenkins
        $upcomingAppt = Appointment::firstOrCreate(
            ['booking_code' => 'APT-98214'],
            [
                'patient_id' => $primaryPatient->id,
                'doctor_id' => $doctor1->id,
                'appointment_date' => now()->addDays(2)->toDateString(),
                'appointment_time' => '10:30:00',
                'status' => AppointmentStatus::CONFIRMED,
                'consultation_type' => ConsultationType::ONLINE,
                'facility' => 'Metropolitan Telehealth Virtual Room 4',
                'consultation_fee' => 75.00,
                'service_fee' => 5.00,
                'total_amount' => 80.00,
                'notes' => 'Routine cardiology follow-up and blood pressure reading review.',
            ]
        );

        Payment::firstOrCreate(
            ['appointment_id' => $upcomingAppt->id],
            [
                'payment_reference' => 'PAY-' . strtoupper(Str::random(10)),
                'patient_id' => $primaryPatient->id,
                'amount' => 80.00,
                'currency' => 'USD',
                'payment_method' => 'Credit Card (•••• 4242)',
                'status' => PaymentStatus::PAID,
                'provider_reference' => 'STRIPE_CH_' . strtoupper(Str::random(12)),
                'paid_at' => now()->subHours(1),
            ]
        );

        $completedAppt = Appointment::firstOrCreate(
            ['booking_code' => 'APT-84192'],
            [
                'patient_id' => $primaryPatient->id,
                'doctor_id' => $doctor1->id,
                'appointment_date' => now()->subDays(14)->toDateString(),
                'appointment_time' => '11:00:00',
                'status' => AppointmentStatus::COMPLETED,
                'consultation_type' => ConsultationType::IN_PERSON,
                'facility' => 'Metropolitan Heart Institute - Room 402',
                'consultation_fee' => 75.00,
                'service_fee' => 5.00,
                'total_amount' => 80.00,
                'notes' => 'Initial consultation for mild hypertension.',
            ]
        );

        Payment::firstOrCreate(
            ['appointment_id' => $completedAppt->id],
            [
                'payment_reference' => 'PAY-' . strtoupper(Str::random(10)),
                'patient_id' => $primaryPatient->id,
                'amount' => 80.00,
                'currency' => 'USD',
                'payment_method' => 'Credit Card (•••• 4242)',
                'status' => PaymentStatus::PAID,
                'provider_reference' => 'STRIPE_CH_' . strtoupper(Str::random(12)),
                'paid_at' => now()->subDays(14),
            ]
        );

        // 8. Medical Records & Vital Signs
        $record1 = MedicalRecord::firstOrCreate(
            ['record_number' => 'MR-2026-00481'],
            [
                'patient_id' => $primaryPatient->id,
                'doctor_id' => $doctor1->id,
                'appointment_id' => $completedAppt->id,
                'visit_date' => now()->subDays(14)->toDateString(),
                'chief_complaint' => 'Mild episodic dizziness and elevated home blood pressure readings (130-135 mmHg systolic).',
                'symptoms' => 'Mild afternoon fatigue, occasional slight headache.',
                'diagnosis' => 'Stage 1 Essential Hypertension (ICD-10 I10), well-compensated.',
                'treatment' => 'Initiate Amlodipine 5mg oral daily in the morning. Sodium restriction <2000mg/day, regular aerobic walking.',
                'follow_up_date' => now()->addDays(2)->toDateString(),
                'allergies' => 'Penicillin, Shellfish',
                'medical_history' => 'No prior hospitalizations; family history of paternal hypertension.',
                'clinical_notes' => 'Cardiovascular auscultation normal. S1/S2 present, no murmurs or gallops. Lungs clear to auscultation bilaterally.',
                'facility' => 'Metropolitan Heart & Vascular Institute',
            ]
        );

        VitalSign::firstOrCreate(
            ['medical_record_id' => $record1->id],
            [
                'systolic_blood_pressure' => 128,
                'diastolic_blood_pressure' => 82,
                'heart_rate' => 74,
                'body_temperature' => 36.6,
                'blood_oxygen' => 99,
                'respiratory_rate' => 16,
                'weight' => 64.50,
                'height' => 168.00,
                'blood_glucose' => 92.0,
                'measured_at' => now()->subDays(14),
            ]
        );

        // 9. Prescriptions & Prescription Items
        $rx1 = Prescription::firstOrCreate(
            ['prescription_code' => 'RX-2026-9921'],
            [
                'patient_id' => $primaryPatient->id,
                'doctor_id' => $doctor1->id,
                'medical_record_id' => $record1->id,
                'prescription_date' => now()->subDays(14)->toDateString(),
                'status' => PrescriptionStatus::ACTIVE,
                'notes' => 'Take 1 tablet every morning with or without food. Monitor home blood pressure regularly.',
            ]
        );

        PrescriptionItem::firstOrCreate(
            ['prescription_id' => $rx1->id, 'medicine_id' => $amlodipine->id],
            [
                'dosage' => '5 mg',
                'frequency' => 'Once daily (Morning)',
                'duration' => '30 Days',
                'instructions' => 'Take with a glass of water after breakfast. Avoid grapefruit juice.',
                'quantity' => 30,
                'refills_available' => 2,
            ]
        );

        // 10. Health Metrics for Sarah Jenkins
        $metricsData = [
            ['type' => HealthMetricType::HEART_RATE, 'val' => 72.0, 'sec' => null, 'unit' => 'bpm', 'days' => 0],
            ['type' => HealthMetricType::HEART_RATE, 'val' => 69.0, 'sec' => null, 'unit' => 'bpm', 'days' => 1],
            ['type' => HealthMetricType::HEART_RATE, 'val' => 75.0, 'sec' => null, 'unit' => 'bpm', 'days' => 2],
            ['type' => HealthMetricType::BLOOD_PRESSURE, 'val' => 118.0, 'sec' => 76.0, 'unit' => 'mmHg', 'days' => 0],
            ['type' => HealthMetricType::BLOOD_PRESSURE, 'val' => 122.0, 'sec' => 80.0, 'unit' => 'mmHg', 'days' => 2],
            ['type' => HealthMetricType::BLOOD_PRESSURE, 'val' => 128.0, 'sec' => 82.0, 'unit' => 'mmHg', 'days' => 14],
            ['type' => HealthMetricType::WEIGHT, 'val' => 64.5, 'sec' => null, 'unit' => 'kg', 'days' => 0],
            ['type' => HealthMetricType::WEIGHT, 'val' => 64.8, 'sec' => null, 'unit' => 'kg', 'days' => 10],
            ['type' => HealthMetricType::BMI, 'val' => 22.9, 'sec' => null, 'unit' => 'kg/m²', 'days' => 0],
            ['type' => HealthMetricType::BLOOD_OXYGEN, 'val' => 99.0, 'sec' => null, 'unit' => '%', 'days' => 0],
            ['type' => HealthMetricType::BLOOD_GLUCOSE, 'val' => 92.0, 'sec' => null, 'unit' => 'mg/dL', 'days' => 0],
            ['type' => HealthMetricType::BODY_TEMPERATURE, 'val' => 36.6, 'sec' => null, 'unit' => '°C', 'days' => 0],
        ];

        foreach ($metricsData as $m) {
            HealthMetric::firstOrCreate(
                [
                    'patient_id' => $primaryPatient->id,
                    'metric_type' => $m['type'],
                    'value' => $m['val'],
                ],
                [
                    'secondary_value' => $m['sec'],
                    'unit' => $m['unit'],
                    'measured_at' => now()->subDays($m['days'])->subHours(2),
                    'notes' => 'Recorded via HealthCare Mobile App',
                ]
            );
        }

        // 11. Notifications
        Notification::firstOrCreate(
            ['user_id' => $primaryPatient->user_id, 'title' => 'Appointment Reminder'],
            [
                'message' => 'Your video consultation with Dr. Emily Chen is scheduled for ' . now()->addDays(2)->format('M d') . ' at 10:30 AM.',
                'notification_type' => 'appointment',
                'related_type' => 'Appointment',
                'related_id' => $upcomingAppt->id,
                'route_target' => '/my-appointments',
                'read_at' => null,
            ]
        );

        Notification::firstOrCreate(
            ['user_id' => $primaryPatient->user_id, 'title' => 'Prescription Refill Ready'],
            [
                'message' => 'Your Amlodipine Besylate 5mg refill has been authorized by Dr. Emily Chen.',
                'notification_type' => 'prescription',
                'related_type' => 'Prescription',
                'related_id' => $rx1->id,
                'route_target' => '/prescriptions',
                'read_at' => now()->subHours(6),
            ]
        );

        // 12. Chat Conversations & Messages
        $chatConv = ChatConversation::firstOrCreate(
            ['patient_id' => $primaryPatient->id, 'doctor_id' => $doctor1->id],
            [
                'appointment_id' => $upcomingAppt->id,
                'status' => 'active',
                'last_message_at' => now()->subMinutes(15),
            ]
        );

        ChatMessage::firstOrCreate(
            ['conversation_id' => $chatConv->id, 'sender_id' => $primaryPatient->user_id],
            [
                'message' => 'Hello Dr. Chen, I logged my morning blood pressure (118/76 mmHg). Feeling much better on the current dosage.',
                'message_type' => 'text',
                'read_at' => now()->subMinutes(12),
                'created_at' => now()->subMinutes(20),
            ]
        );

        ChatMessage::firstOrCreate(
            ['conversation_id' => $chatConv->id, 'sender_id' => $docUser1->id],
            [
                'message' => 'Excellent progress Sarah! Those readings are well within our target range. We will review trends during our video call.',
                'message_type' => 'text',
                'read_at' => now()->subMinutes(5),
                'created_at' => now()->subMinutes(15),
            ]
        );
    }
}
