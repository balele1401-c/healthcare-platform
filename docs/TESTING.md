# Testing Strategy & Quality Assurance Plan

## 1. Overview & Quality Objectives

The **HealthCare Integrated Medical Platform** requires a robust, automated multi-layer testing strategy to ensure clinical safety, data integrity, and pixel-perfect adherence to the **Clinical Clarity** design system.

---

## 2. Flutter Mobile Testing Strategy

### 2.1 Unit Tests (`test/unit/`)
- **Target**: Pure Dart logic, models, calculators, and controllers.
- **Key Test Cases**:
  - `BMI Calculator`: Verify correct category calculations (`Underweight`, `Normal`, `Overweight`, `Obese`).
  - `Date & Slot Helpers`: Verify time slot generation and past-date validation.
  - `Form Validators`: Verify email, password strength, phone number, and OTP inputs.
  - `Riverpod Notifiers`: Test state transitions (`initial` -> `loading` -> `success` / `error`).

### 2.2 Widget Tests (`test/widget/`)
- **Target**: Reusable atomic UI components and full-screen layouts.
- **Key Test Cases**:
  - `AppButton`: Verify Primary, Secondary, and Disabled styles, loading spinner, and tap callbacks.
  - `AppTextField`: Verify error message rendering, obscure password toggle, and focus states.
  - `AppointmentCard`: Verify correct status badge color (Pending: Blue/Yellow, Confirmed: Green, Cancelled: Red).
  - `BottomNavBar`: Verify active tab highlight and tab switching.

### 2.3 Integration & Flow Tests (`test/integration/`)
- **Target**: Complete end-to-end user journeys.
- **Key Test Flows**:
  - **Auth Flow**: Login -> OTP Verification -> Health Profile Setup -> Home Dashboard.
  - **Booking Flow**: Search Doctor -> Select Doctor -> Select Date/Time -> Confirm -> Mock Payment -> Success Screen.
  - **Health Tracker Flow**: Add Heart Rate / BP Reading -> Metric Log Updates -> Summary Card Updates.

### 2.4 Mobile Test Commands
```bash
# Run static code analysis
flutter analyze

# Run all unit and widget tests
flutter test

# Run tests with code coverage report
flutter test --coverage
```

---

## 3. Laravel Backend Testing Strategy

### 3.1 Unit Tests (`tests/Unit/`)
- **Target**: Domain services and business logic classes.
- **Key Test Cases**:
  - `AppointmentService`: Double-booking prevention algorithm.
  - `ScheduleService`: Day of week availability slot generator.
  - `AiService`: Prompt sanitizer and PII stripper.

### 3.2 Feature & API Tests (`tests/Feature/`)
- **Target**: HTTP API endpoints, middleware, authentication, and database state.
- **Key Test Suites**:
  - `AuthApiTest`: Registration, login, invalid credentials, rate limiting.
  - `DoctorApiTest`: Public search, specialty filtering, schedule slot retrieval.
  - `AppointmentApiTest`: Booking creation, status transitions, cancellation rules.
  - `MedicalRecordApiTest`: Doctor creation, patient viewing, 24-hour edit window enforcement.
  - `PrescriptionApiTest`: Dosing instructions, medicine item validation.

### 3.3 Security & Data Isolation Tests (`tests/Feature/Security/`)
- **Target**: Prevent cross-tenant data leaks (OWASP BOLA/IDOR).
- **Mandatory Security Test**:
  - Create Patient A and Patient B.
  - Patient A attempts `GET /api/v1/medical-records/{patient_b_record_id}`.
  - Assert response is `HTTP 403 Forbidden` and `audit_logs` records the unauthorized attempt.

### 3.4 Backend Test Commands
```bash
# Run all backend unit and feature tests
php artisan test

# Run specific security isolation tests
php artisan test --filter=DataIsolationTest
```

---

## 4. UI/UX Acceptance Criteria (Stitch Alignment)

Every screen built in Phase 2 must satisfy the following checklist before phase sign-off:
- [ ] Colors match `clinical_clarity/DESIGN.md` tokens exactly (Primary: `#0050CB`, Secondary: `#006A6A`, Surface: `#F9F9FF`, On-Surface: `#161C27`).
- [ ] Typography uses **Inter** with designated scale (`headline-lg`, `body-md`, `label-md`).
- [ ] Spacing adheres to the **8px grid** (`8px`, `16px`, `24px`, `32px`).
- [ ] Card corners use 12–16px radius with subtle ambient shadow.
- [ ] Zero UI overflow errors (`RenderFlex overflowed`) across diverse screen sizes (360dp to 430dp widths).
- [ ] All data screens provide Loading, Empty, Success, and Error states with retry buttons.
