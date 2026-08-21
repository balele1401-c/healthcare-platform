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

        // 1. Standard Multi-Role Test Accounts
        // A. Owner Test User
        User::firstOrCreate(
            ['email' => 'owner@healthcare.test'],
            [
                'name' => 'Platform Executive Owner',
                'phone' => '+15550000099',
                'password' => $defaultPassword,
                'role' => UserRole::OWNER,
                'status' => UserStatus::ACTIVE,
                'email_verified_at' => now(),
            ]
        );

        // B. Admin Test User
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@healthcare.test'],
            [
                'name' => 'System Administrator',
                'phone' => '+15550000001',
                'password' => $defaultPassword,
                'role' => UserRole::ADMIN,
                'status' => UserStatus::ACTIVE,
                'email_verified_at' => now(),
            ]
        );

        // Legacy admin fallback
        User::firstOrCreate(
            ['email' => 'admin@healthcare.local'],
            [
                'name' => 'System Administrator (Local)',
                'phone' => '+15550000002',
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

        // 2. Specialties Catalog
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

        // 3. Doctor Accounts & Profiles
        // C. Doctor Test User
        $testDocUser = User::firstOrCreate(
            ['email' => 'doctor@healthcare.test'],
            [
                'name' => 'Dr. Alexander Wright',
                'phone' => '+15550192800',
                'password' => $defaultPassword,
                'role' => UserRole::DOCTOR,
                'status' => UserStatus::ACTIVE,
                'avatar_url' => 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&w=400&q=80',
                'email_verified_at' => now(),
            ]
        );

        $testDoctor = Doctor::firstOrCreate(
            ['user_id' => $testDocUser->id],
            [
                'specialty_id' => $familyMedicine->id,
                'license_number' => 'MD-GEN-2018-1190',
                'biography' => 'Dr. Alexander Wright is an experienced family physician specializing in preventive medicine and patient wellness.',
                'education' => 'Columbia University Vagelos College of Physicians and Surgeons (2018)',
                'experience_years' => 7,
                'consultation_fee' => 50.00,
                'facility' => 'Central Health Family Clinic',
                'profile_photo' => $testDocUser->avatar_url,
                'rating' => 4.85,
                'review_count' => 64,
                'status' => 'active',
            ]
        );

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
        foreach ([$testDoctor, $doctor1] as $doc) {
            foreach ([1, 2, 3, 4, 5] as $day) {
                DoctorSchedule::firstOrCreate(
                    [
                        'doctor_id' => $doc->id,
                        'day_of_week' => $day,
                        'consultation_type' => ConsultationType::IN_PERSON,
                    ],
                    [
                        'start_time' => '09:00:00',
                        'end_time' => '13:00:00',
                        'facility' => $doc->facility ?? 'Metropolitan Medical Center',
                        'slot_duration_minutes' => 30,
                        'max_patients' => 8,
                        'is_available' => true,
                    ]
                );

                DoctorSchedule::firstOrCreate(
                    [
                        'doctor_id' => $doc->id,
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
            }
        }

        // 4. Staff Accounts & Profiles
        // D. Staff Test User
        $testStaffUser = User::firstOrCreate(
            ['email' => 'staff@healthcare.test'],
            [
                'name' => 'Jessica Parker (Operations)',
                'phone' => '+15550193000',
                'password' => $defaultPassword,
                'role' => UserRole::STAFF,
                'status' => UserStatus::ACTIVE,
                'email_verified_at' => now(),
            ]
        );

        Staff::firstOrCreate(
            ['user_id' => $testStaffUser->id],
            [
                'department' => 'Patient Care Coordination & Admissions',
                'employee_number' => 'EMP-STF-001',
                'facility' => 'Central Metropolitan Hospital',
                'status' => 'active',
            ]
        );

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

        // 5. Patient Accounts & Profiles
        // E. Patient Test User
        $testPatientUser = User::firstOrCreate(
            ['email' => 'patient@healthcare.test'],
            [
                'name' => 'Alex Turner',
                'phone' => '+15550192899',
                'password' => $defaultPassword,
                'role' => UserRole::PATIENT,
                'status' => UserStatus::ACTIVE,
                'email_verified_at' => now(),
            ]
        );

        Patient::firstOrCreate(
            ['user_id' => $testPatientUser->id],
            [
                'date_of_birth' => '1995-06-20',
                'gender' => 'male',
                'blood_type' => 'O+',
                'height_cm' => 178.00,
                'weight_kg' => 74.00,
                'address' => '12 Innovation Way, Tech Park',
                'emergency_contact_name' => 'Emma Turner',
                'emergency_contact_phone' => '+15550195899',
                'emergency_contact_relation' => 'Spouse',
                'allergies' => 'None',
                'medical_history_summary' => 'Routine annual fitness evaluations.',
            ]
        );

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

        // 8. Medical Records
        $record1 = MedicalRecord::firstOrCreate(
            ['record_number' => 'MR-2026-00481'],
            [
                'patient_id' => $primaryPatient->id,
                'doctor_id' => $doctor1->id,
                'appointment_id' => $upcomingAppt->id,
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

        // 9. Prescriptions
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
    }
}
