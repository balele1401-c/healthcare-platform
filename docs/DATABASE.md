# Database Architecture & Entity Specifications

## 1. Overview & Principles

The database layer serves as the single source of truth for all clinical, administrative, and financial data in the **HealthCare Integrated Medical Platform**.

### Core Tenets
- **Relational Integrity**: Strict foreign key constraints with indexed lookup columns.
- **Tenant & Ownership Segregation**: Every medical and private record references a primary owner (`patient_id` or `user_id`).
- **Auditability**: Immutable audit logs capturing who created, viewed, or modified sensitive medical data.
- **Soft Deletes**: Critical healthcare tables (records, prescriptions, appointments) utilize soft deletion (`deleted_at`) to preserve medical history for legal compliance.

---

## 2. Entity Relationship Diagram (ERD Overview)

```
[users] ──┬── (1:1) ──> [patients] ──┬── (1:N) ──> [appointments] <── (1:N) ── [doctors] <── (1:1) ── [users]
          │                          ├── (1:N) ──> [medical_records] ── (1:1) ──> [vital_signs]
          │                          ├── (1:N) ──> [prescriptions] ── (1:N) ──> [prescription_items] ──> [medicines]
          │                          ├── (1:N) ──> [health_metrics]
          │                          └── (1:N) ──> [payments]
          └── (1:1) ──> [staff]

[specialties] <── (1:N) ── [doctors] ── (1:N) ──> [doctor_schedules]

[chat_conversations] ── (1:N) ──> [chat_messages]

[users] ── (1:N) ──> [notifications]
[users] ── (1:N) ──> [audit_logs]
```

---

## 3. Detailed Entity Specifications (18 Core Entities)

### 1. `users`
- **Purpose**: Central authentication and identity table for all platform users.
- **Key Fields**:
  - `id` (BIGINT, PK, Auto Increment / UUID)
  - `name` (VARCHAR 255)
  - `email` (VARCHAR 255, Unique, Indexed)
  - `phone_number` (VARCHAR 30, Nullable, Indexed)
  - `password` (VARCHAR 255, Hashed via Bcrypt / Argon2)
  - `role` (ENUM: `'patient'`, `'doctor'`, `'staff'`, `'admin'`)
  - `avatar_url` (VARCHAR 500, Nullable)
  - `email_verified_at` (TIMESTAMP, Nullable)
  - `phone_verified_at` (TIMESTAMP, Nullable)
  - `is_active` (BOOLEAN, Default: true)
  - `timestamps` (`created_at`, `updated_at`, `deleted_at`)
- **Relationships**: 1:1 with `patients`, `doctors`, or `staff`; 1:N with `notifications`, `audit_logs`.
- **Security**: Passwords never exposed in queries; rate limited on login; soft delete support.

---

### 2. `patients`
- **Purpose**: Extended demographic and personal medical profile for patient users.
- **Key Fields**:
  - `id` (BIGINT, PK)
  - `user_id` (BIGINT, FK -> `users.id`, Unique)
  - `date_of_birth` (DATE)
  - `gender` (ENUM: `'male'`, `'female'`, `'other'`)
  - `blood_type` (ENUM: `'A+'`, `'A-'`, `'B+'`, `'B-'`, `'AB+'`, `'AB-'`, `'O+'`, `'O-'`, Nullable)
  - `height_cm` (DECIMAL 5,2, Nullable)
  - `weight_kg` (DECIMAL 5,2, Nullable)
  - `address` (TEXT, Nullable)
  - `emergency_contact_name` (VARCHAR 255, Nullable)
  - `emergency_contact_phone` (VARCHAR 50, Nullable)
  - `emergency_contact_relation` (VARCHAR 100, Nullable)
  - `allergies` (TEXT, Nullable)
  - `medical_history_summary` (TEXT, Nullable)
  - `timestamps`
- **Ownership**: Belongs to `user_id`. Only accessible by the patient, authorized doctors, and admins.

---

### 3. `doctors`
- **Purpose**: Professional credential, bio, and operational settings for medical practitioners.
- **Key Fields**:
  - `id` (BIGINT, PK)
  - `user_id` (BIGINT, FK -> `users.id`, Unique)
  - `specialty_id` (BIGINT, FK -> `specialties.id`, Indexed)
  - `license_number` (VARCHAR 100, Unique)
  - `experience_years` (INT, Default: 0)
  - `education` (TEXT)
  - `biography` (TEXT)
  - `clinic_facility_name` (VARCHAR 255)
  - `consultation_fee` (DECIMAL 12,2)
  - `rating` (DECIMAL 3,2, Default: 5.00)
  - `review_count` (INT, Default: 0)
  - `is_available` (BOOLEAN, Default: true)
  - `timestamps`
- **Relationships**: Belongs to `users` and `specialties`; 1:N with `doctor_schedules`, `appointments`, `medical_records`.

---

### 4. `staff`
- **Purpose**: Internal clinic/hospital administrative staff profiles.
- **Key Fields**:
  - `id` (BIGINT, PK)
  - `user_id` (BIGINT, FK -> `users.id`, Unique)
  - `employee_id` (VARCHAR 50, Unique)
  - `department` (VARCHAR 100)
  - `facility_name` (VARCHAR 255)
  - `timestamps`
- **Authorization**: Governed by staff role policies.

---

### 5. `specialties`
- **Purpose**: Medical categories/disciplines (Cardiology, Dermatology, Pediatrics, General Practice, etc.).
- **Key Fields**:
  - `id` (BIGINT, PK)
  - `name` (VARCHAR 100, Unique)
  - `slug` (VARCHAR 100, Unique)
  - `icon_url` (VARCHAR 500, Nullable)
  - `description` (TEXT, Nullable)
  - `is_active` (BOOLEAN, Default: true)
  - `timestamps`
- **Relationships**: 1:N with `doctors`.

---

### 6. `doctor_schedules`
- **Purpose**: Recurring availability slots and working hours for doctors.
- **Key Fields**:
  - `id` (BIGINT, PK)
  - `doctor_id` (BIGINT, FK -> `doctors.id`, Indexed)
  - `day_of_week` (TINYINT: 0=Sunday, 6=Saturday)
  - `start_time` (TIME)
  - `end_time` (TIME)
  - `slot_duration_minutes` (INT, Default: 30)
  - `max_patients` (INT, Default: 20)
  - `is_active` (BOOLEAN, Default: true)
  - `timestamps`
- **Constraints**: `UNIQUE(doctor_id, day_of_week, start_time)`.

---

### 7. `appointments`
- **Purpose**: Core appointment bookings between patients and doctors.
- **Key Fields**:
  - `id` (BIGINT, PK)
  - `booking_code` (VARCHAR 32, Unique, Indexed)
  - `patient_id` (BIGINT, FK -> `patients.id`, Indexed)
  - `doctor_id` (BIGINT, FK -> `doctors.id`, Indexed)
  - `appointment_date` (DATE, Indexed)
  - `appointment_time` (TIME)
  - `status` (ENUM: `'pending'`, `'confirmed'`, `'in_consultation'`, `'completed'`, `'cancelled'`, `'no_show'`)
  - `type` (ENUM: `'in_person'`, `'teleconsultation'`, Default: `'in_person'`)
  - `chief_complaint` (TEXT, Nullable)
  - `cancellation_reason` (TEXT, Nullable)
  - `cancelled_by` (BIGINT, FK -> `users.id`, Nullable)
  - `consultation_fee` (DECIMAL 12,2)
  - `timestamps`, `deleted_at`
- **Authorization**: Patient can only see their own appointments; Doctor can see assigned appointments; Staff/Admin can manage appointments across facility.

---

### 8. `medical_records`
- **Purpose**: Official clinical consultation records and diagnoses.
- **Key Fields**:
  - `id` (BIGINT, PK)
  - `record_number` (VARCHAR 50, Unique, Indexed)
  - `appointment_id` (BIGINT, FK -> `appointments.id`, Unique, Nullable)
  - `patient_id` (BIGINT, FK -> `patients.id`, Indexed)
  - `doctor_id` (BIGINT, FK -> `doctors.id`, Indexed)
  - `visit_date` (TIMESTAMP, Indexed)
  - `chief_complaint` (TEXT)
  - `symptoms` (TEXT)
  - `physical_examination` (TEXT, Nullable)
  - `diagnosis` (TEXT)
  - `diagnosis_icd10_code` (VARCHAR 20, Nullable)
  - `treatment_notes` (TEXT)
  - `follow_up_date` (DATE, Nullable)
  - `timestamps`, `deleted_at`
- **Relationships**: 1:1 with `vital_signs`, 1:N with `prescriptions`.
- **Security**: Immutable by patient; Doctor can only edit within 24 hours of creation; all access logged in `audit_logs`.

---

### 9. `vital_signs`
- **Purpose**: Physical measurements taken during a clinical encounter.
- **Key Fields**:
  - `id` (BIGINT, PK)
  - `medical_record_id` (BIGINT, FK -> `medical_records.id`, Unique)
  - `systolic_bp` (INT, Nullable, e.g. 120)
  - `diastolic_bp` (INT, Nullable, e.g. 80)
  - `heart_rate_bpm` (INT, Nullable, e.g. 72)
  - `respiratory_rate` (INT, Nullable)
  - `body_temperature_c` (DECIMAL 4,2, Nullable, e.g. 36.6)
  - `oxygen_saturation_pct` (DECIMAL 4,2, Nullable, e.g. 98.0)
  - `weight_kg` (DECIMAL 5,2, Nullable)
  - `height_cm` (DECIMAL 5,2, Nullable)
  - `bmi` (DECIMAL 4,2, Nullable)
  - `timestamps`

---

### 10. `prescriptions`
- **Purpose**: Official medical prescriptions issued by an authorized doctor.
- **Key Fields**:
  - `id` (BIGINT, PK)
  - `prescription_code` (VARCHAR 50, Unique, Indexed)
  - `medical_record_id` (BIGINT, FK -> `medical_records.id`, Nullable)
  - `patient_id` (BIGINT, FK -> `patients.id`, Indexed)
  - `doctor_id` (BIGINT, FK -> `doctors.id`, Indexed)
  - `issued_at` (TIMESTAMP)
  - `valid_until` (DATE)
  - `status` (ENUM: `'active'`, `'completed'`, `'expired'`, `'cancelled'`)
  - `general_notes` (TEXT, Nullable)
  - `timestamps`, `deleted_at`
- **Relationships**: 1:N with `prescription_items`.

---

### 11. `prescription_items`
- **Purpose**: Specific line items and dosing instructions in a prescription.
- **Key Fields**:
  - `id` (BIGINT, PK)
  - `prescription_id` (BIGINT, FK -> `prescriptions.id`, Indexed)
  - `medicine_id` (BIGINT, FK -> `medicines.id`, Nullable)
  - `medicine_name` (VARCHAR 255)
  - `dosage` (VARCHAR 100, e.g. "500 mg")
  - `frequency` (VARCHAR 100, e.g. "3 times daily")
  - `duration` (VARCHAR 100, e.g. "5 days")
  - `quantity` (INT, Default: 1)
  - `instructions` (TEXT, e.g. "Take after meals")
  - `timestamps`

---

### 12. `medicines`
- **Purpose**: Master formulary / catalog of pharmaceutical items.
- **Key Fields**:
  - `id` (BIGINT, PK)
  - `name` (VARCHAR 255, Indexed)
  - `generic_name` (VARCHAR 255, Nullable)
  - `category` (VARCHAR 100, Nullable)
  - `form` (ENUM: `'tablet'`, `'capsule'`, `'syrup'`, `'injection'`, `'topical'`, `'inhaler'`, `'drops'`)
  - `strength` (VARCHAR 100, e.g. "500mg")
  - `manufacturer` (VARCHAR 255, Nullable)
  - `unit_price` (DECIMAL 12,2, Default: 0)
  - `is_active` (BOOLEAN, Default: true)
  - `timestamps`

---

### 13. `health_metrics`
- **Purpose**: Patient-logged personal health vital readings over time.
- **Key Fields**:
  - `id` (BIGINT, PK)
  - `patient_id` (BIGINT, FK -> `patients.id`, Indexed)
  - `metric_type` (ENUM: `'blood_pressure'`, `'heart_rate'`, `'blood_glucose'`, `'weight'`, `'height'`, `'oxygen_saturation'`, `'body_temperature'`, `'steps'`)
  - `value_numeric` (DECIMAL 8,2)
  - `secondary_value` (DECIMAL 8,2, Nullable, e.g. diastolic value)
  - `unit` (VARCHAR 20, e.g. `'bpm'`, `'mmHg'`, `'mg/dL'`, `'kg'`)
  - `notes` (VARCHAR 255, Nullable)
  - `measured_at` (TIMESTAMP, Indexed)
  - `timestamps`
- **Indexes**: Composite index `(patient_id, metric_type, measured_at)` for instant charting queries.

---

### 14. `notifications`
- **Purpose**: System, consultation, appointment, and medication reminders.
- **Key Fields**:
  - `id` (BIGINT, PK)
  - `user_id` (BIGINT, FK -> `users.id`, Indexed)
  - `title` (VARCHAR 255)
  - `body` (TEXT)
  - `category` (ENUM: `'appointment'`, `'prescription'`, `'chat'`, `'health_reminder'`, `'system'`)
  - `action_url` (VARCHAR 255, Nullable)
  - `read_at` (TIMESTAMP, Nullable, Indexed)
  - `timestamps`

---

### 15. `chat_conversations`
- **Purpose**: Active consultation chat sessions between patients and doctors.
- **Key Fields**:
  - `id` (BIGINT, PK)
  - `appointment_id` (BIGINT, FK -> `appointments.id`, Nullable, Indexed)
  - `patient_id` (BIGINT, FK -> `patients.id`, Indexed)
  - `doctor_id` (BIGINT, FK -> `doctors.id`, Indexed)
  - `status` (ENUM: `'open'`, `'closed'`, Default: `'open'`)
  - `last_message_at` (TIMESTAMP, Nullable)
  - `timestamps`

---

### 16. `chat_messages`
- **Purpose**: Individual messages within a consultation chat thread.
- **Key Fields**:
  - `id` (BIGINT, PK)
  - `conversation_id` (BIGINT, FK -> `chat_conversations.id`, Indexed)
  - `sender_id` (BIGINT, FK -> `users.id`, Indexed)
  - `message` (TEXT)
  - `attachment_url` (VARCHAR 500, Nullable)
  - `is_read` (BOOLEAN, Default: false)
  - `read_at` (TIMESTAMP, Nullable)
  - `timestamps`

---

### 17. `payments`
- **Purpose**: Payment transactions for appointments and consultations.
- **Key Fields**:
  - `id` (BIGINT, PK)
  - `transaction_code` (VARCHAR 50, Unique, Indexed)
  - `appointment_id` (BIGINT, FK -> `appointments.id`, Unique, Indexed)
  - `patient_id` (BIGINT, FK -> `patients.id`, Indexed)
  - `amount` (DECIMAL 12,2)
  - `payment_method` (ENUM: `'bank_transfer'`, `'virtual_account'`, `'e_wallet'`, `'credit_card'`, `'mock'`)
  - `payment_provider` (VARCHAR 50, Nullable, e.g. `'Midtrans'`, `'Stripe'`, `'Mock'`)
  - `status` (ENUM: `'pending'`, `'paid'`, `'failed'`, `'expired'`, `'refunded'`)
  - `paid_at` (TIMESTAMP, Nullable)
  - `payment_details` (JSON, Nullable)
  - `timestamps`

---

### 18. `audit_logs`
- **Purpose**: Non-repudiation audit trail for Protected Health Information (PHI) access and changes.
- **Key Fields**:
  - `id` (BIGINT, PK)
  - `user_id` (BIGINT, FK -> `users.id`, Nullable, Indexed)
  - `user_role` (VARCHAR 50)
  - `ip_address` (VARCHAR 45)
  - `user_agent` (VARCHAR 255)
  - `action` (VARCHAR 100, e.g. `'VIEW_MEDICAL_RECORD'`, `'CREATE_PRESCRIPTION'`, `'UPDATE_SCHEDULE'`)
  - `entity_type` (VARCHAR 100, e.g. `'MedicalRecord'`, `'Appointment'`, `'User'`)
  - `entity_id` (BIGINT, Nullable, Indexed)
  - `old_values` (JSON, Nullable)
  - `new_values` (JSON, Nullable)
  - `created_at` (TIMESTAMP, Indexed)
- **Constraint**: Strict append-only table. No update or delete operations allowed under any circumstances.
