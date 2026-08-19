# PRD — HealthCare Integrated Medical Platform

## 1. Document Control

| Attribute | Detail |
|---|---|
| Product Name | HealthCare Integrated Medical Platform |
| Product Type | Digital Healthcare Platform |
| Version | 1.0.0 |
| Status | Initial Product Requirements |
| Target Platforms | Android Mobile App + Responsive Web |
| Primary Mobile Technology | Flutter / Dart |
| Backend Technology | Laravel REST API |
| Database | PostgreSQL (recommended) |
| Design Tool | Google Stitch |
| Development Environment | VS Code / Antigravity |
| Design System | Clinical Clarity |
| Primary Users | Patient, Doctor, Staff, Admin |

---

## 2. Product Overview

HealthCare Integrated Medical Platform is a digital healthcare ecosystem designed to connect patients, doctors, staff, and administrators through a unified platform.

The platform provides patients with access to doctor discovery, appointment booking, consultations, medical records, prescriptions, health tracking, notifications, and an AI-powered health information assistant.

Healthcare providers receive tools to manage appointments, patient information, medical records, consultations, schedules, and prescriptions.

Administrators receive centralized tools for managing users, doctors, appointments, medicines, payments, reports, and platform operations.

The system is designed as a scalable foundation that can later integrate payment gateways, push notifications, realtime chat, third-party healthcare services, and AI services.

---

## 3. Product Goals

### Primary Goals

1. Provide a simple digital healthcare experience for patients.
2. Make doctor discovery and appointment booking easier.
3. Centralize patient medical information.
4. Help doctors manage consultations and patient records.
5. Provide administrators with operational visibility.
6. Provide basic health monitoring features.
7. Provide an AI assistant for general health information.
8. Build a secure and scalable architecture suitable for future production deployment.

### Success Criteria

- Patients can register and manage their profiles.
- Patients can search and view doctors.
- Patients can book appointments.
- Patients can view appointment history.
- Patients can access their medical records.
- Patients can view prescriptions.
- Patients can track basic health metrics.
- Doctors can manage appointments and clinical records.
- Administrators can manage platform data.
- All sensitive healthcare data is protected by role-based access control.
- Mobile UI consistently follows the Google Stitch design system.
- Backend APIs are documented and testable.

---

## 4. Target Users

### 4.1 Patient

Patients use the mobile application to:

- Register and log in.
- Create a health profile.
- Search for doctors.
- View doctor profiles.
- Book appointments.
- Manage appointments.
- Communicate with doctors.
- View medical records.
- View prescriptions.
- Track health metrics.
- Receive notifications.
- Manage account settings.
- Use the AI Health Assistant.

### 4.2 Doctor

Doctors use the web dashboard to:

- Manage their profile.
- Manage availability.
- View appointments.
- View patient information.
- Conduct consultations.
- Create medical records.
- Create prescriptions.
- Review patient history.
- Communicate with patients.
- Receive notifications.

### 4.3 Staff

Staff can:

- Manage patient registrations.
- Assist with appointments.
- Manage schedules.
- Manage administrative healthcare workflows.
- View permitted patient information.
- Assist doctors and patients.

### 4.4 Administrator

Administrators can:

- Manage users.
- Manage doctors.
- Manage patients.
- Manage appointments.
- Manage medicines.
- Monitor payments.
- View reports.
- Manage system settings.
- Monitor platform activity.

---

# 5. Platform Scope

## 5.1 Patient Mobile App

Technology:

- Flutter
- Dart
- Android

Main modules:

- Authentication
- Home
- Doctor Discovery
- Appointments
- Chat
- Medical Records
- Prescriptions
- Health Tracker
- Notifications
- Profile
- Settings
- AI Health Assistant

## 5.2 Web Dashboard

Technology:

- Laravel
- Responsive web UI
- Tailwind CSS or equivalent modern UI system

Roles:

- Admin
- Doctor
- Staff

Main modules:

- Dashboard
- Patients
- Doctors
- Appointments
- Medical Records
- Prescriptions
- Medicines
- Payments
- Reports
- Notifications
- Settings

## 5.3 Backend

Technology:

- Laravel
- REST API
- PostgreSQL recommended

Responsibilities:

- Authentication
- Authorization
- Business logic
- Data validation
- Appointment management
- Medical record management
- Prescription management
- Notifications
- Chat infrastructure
- AI service integration
- Audit logging

---

# 6. Patient Mobile App Requirements

## 6.1 Authentication

Screens:

1. Splash
2. Onboarding
3. Login
4. Register
5. Forgot Password
6. OTP Verification
7. Create Health Profile

Requirements:

- Email/phone authentication.
- Password validation.
- OTP verification.
- Session management.
- Secure token storage.
- Logout.
- Authentication route protection.

---

## 6.2 Home Dashboard

The Home screen should display:

- Greeting.
- Patient name.
- Profile avatar.
- Doctor search.
- Upcoming appointment.
- Quick actions.
- Recommended doctors.
- Health summary.
- Recent medical records.

Quick actions may include:

- Find Doctor
- Book Appointment
- Medical Records
- Prescriptions
- Health Tracker
- AI Assistant

---

## 6.3 Doctor Discovery

Features:

- Search doctor.
- Search specialty.
- Filter by specialty.
- Filter availability.
- View doctor list.
- View doctor profile.

Doctor profile:

- Photo.
- Name.
- Specialty.
- Experience.
- Education.
- Rating.
- Biography.
- Consultation fee.
- Clinic/facility.
- Availability.
- Book appointment.

---

# 7. Appointment Management

## 7.1 Booking Flow

1. Select doctor.
2. Select date.
3. Select available time.
4. Confirm appointment.
5. Select payment method.
6. Complete/mock payment.
7. Display appointment success.
8. Add appointment to appointment history.

## 7.2 Appointment Status

- Pending
- Confirmed
- In Consultation
- Completed
- Cancelled

## 7.3 Appointment Management

Patients can:

- View upcoming appointments.
- View past appointments.
- View appointment details.
- Reschedule where allowed.
- Cancel where allowed.
- Contact doctor.
- Start consultation when available.

---

# 8. Payment

Supported architecture:

- Bank Transfer
- E-wallet
- Virtual Account
- Card

Phase 1 may use a mock payment service.

The architecture must allow future integration with a real payment provider.

Security requirements:

- Never store raw card credentials.
- Never store payment secrets in source code.
- Use secure backend payment processing.

---

# 9. Doctor-Patient Chat

Features:

- One-to-one doctor/patient chat.
- Text messages.
- Timestamps.
- Online/offline status.
- Typing indicator.
- Attachments.
- Message history.
- Consultation context.

Future enhancement:

- Realtime WebSocket communication.

---

# 10. Medical Records

Patients can view their own medical records.

Medical record fields:

- Visit date.
- Doctor.
- Specialty.
- Facility.
- Chief complaint.
- Symptoms.
- Vital signs.
- Diagnosis information.
- Treatment notes.
- Follow-up date.
- Allergies.
- Medical history.
- Prescription reference.

Patients cannot edit clinical records.

Doctors can create and update records according to their authorization.

---

# 11. Prescriptions

Prescription list:

- Doctor.
- Prescription date.
- Prescription ID.
- Medicine count.
- Status.

Prescription detail:

- Medicine name.
- Dosage.
- Frequency.
- Duration.
- Instructions.
- Notes.

Statuses:

- Active
- Completed
- Expired

---

# 12. Health Tracker

Supported metrics:

- Weight
- Height
- BMI
- Blood Pressure
- Heart Rate
- Blood Oxygen
- Blood Glucose
- Body Temperature

Features:

- Current measurement.
- Measurement history.
- Trend.
- Last updated.
- Simple charts.
- Add measurement.

Health tracking is informational and must not be presented as a diagnosis.

---

# 13. Notifications

Notification categories:

- Appointment reminder.
- Appointment confirmation.
- Doctor message.
- Prescription update.
- Health reminder.
- System notification.

Features:

- Read/unread state.
- Timestamp.
- Mark as read.
- Notification history.
- Empty state.

Future:

- Firebase Cloud Messaging or another push notification provider.

---

# 14. Patient Profile

Profile information:

- Profile photo.
- Full name.
- Email.
- Phone number.
- Date of birth.
- Gender.
- Blood type.
- Address.
- Emergency contact.

Profile functions:

- View profile.
- Edit profile.
- Change password.
- Manage notification preferences.
- Manage privacy settings.
- Logout.

---

# 15. AI Health Assistant

The AI Health Assistant provides general health information and wellness education.

Possible functions:

- General symptom information.
- Health education.
- Wellness suggestions.
- Appointment preparation.
- Explanation of health metrics.
- Guidance on when to seek professional care.

Example prompts:

- How can I prepare for my appointment?
- What should I do if I have a fever?
- How can I improve my sleep?
- What does my health metric mean?

## Safety Requirements

The AI must:

- Clearly identify itself as an AI assistant.
- Not claim to be a doctor.
- Not provide definitive medical diagnosis.
- Not replace professional medical care.
- Encourage professional evaluation for concerning symptoms.
- Display a healthcare disclaimer.

Required disclaimer:

> AI Health Assistant provides general health information and does not replace professional medical diagnosis or treatment.

---

# 16. Doctor Dashboard

## Dashboard

Display:

- Today's appointments.
- Upcoming appointments.
- Completed consultations.
- Patient count.
- Schedule overview.
- Recent activity.

## Patient Management

Doctors can view authorized patient information.

Patient profile includes:

- Personal information.
- Medical history.
- Allergies.
- Appointments.
- Medical records.
- Prescriptions.

## Consultation

Fields:

- Chief complaint.
- Symptoms.
- Vital signs.
- Clinical notes.
- Diagnosis information.
- Treatment.
- Follow-up date.
- Prescription.

---

# 17. Admin Dashboard

Dashboard metrics:

- Total patients.
- Total doctors.
- Today's appointments.
- Completed consultations.
- Revenue.
- Appointment statistics.
- Patient growth.
- Recent appointments.
- Recent patients.
- System activity.

Admin modules:

- Patients
- Doctors
- Appointments
- Medical Records
- Prescriptions
- Medicines
- Payments
- Notifications
- Reports
- Settings

---

# 18. Staff Dashboard

Staff functions:

- Patient registration assistance.
- Appointment management.
- Schedule management.
- Administrative support.
- Patient search.
- Appointment status management.

Staff access must be restricted according to permissions.

---

# 19. Design System

The Google Stitch design is the primary UI reference.

Design system name:

**Clinical Clarity**

Visual principles:

- Modern.
- Clean.
- Professional.
- Calm.
- Trustworthy.
- Spacious.
- Accessible.

## Colors

Primary:

`#0050CB`

Strong Primary:

`#0066FF`

Secondary:

`#006A6A`

Background:

`#F9F9FF`

On Surface:

`#161C27`

On Surface Variant:

`#424656`

Outline:

`#727687`

Outline Variant:

`#C2C6D8`

Error:

`#BA1A1A`

## Typography

Primary font:

Inter

Spacing:

8px design grid.

Card radius:

12–16px.

Input/button radius:

8px.

Status badges:

Pill style.

Use subtle shadows.

Avoid excessive gradients and decoration.

---

# 20. Technical Architecture

## Mobile

Flutter architecture:

```text
mobile/
└── healthcare_patient/
    ├── lib/
    │   ├── core/
    │   │   ├── constants/
    │   │   ├── theme/
    │   │   ├── routing/
    │   │   ├── network/
    │   │   ├── storage/
    │   │   └── errors/
    │   │
    │   ├── features/
    │   │   ├── auth/
    │   │   ├── home/
    │   │   ├── doctors/
    │   │   ├── appointments/
    │   │   ├── chat/
    │   │   ├── medical_records/
    │   │   ├── prescriptions/
    │   │   ├── health_tracker/
    │   │   ├── notifications/
    │   │   ├── profile/
    │   │   └── ai_assistant/
    │   │
    │   ├── shared/
    │   │   ├── widgets/
    │   │   └── models/
    │   │
    │   └── main.dart
    │
    ├── assets/
    ├── test/
    └── pubspec.yaml
```

Recommended packages:

- flutter_riverpod
- go_router
- dio
- flutter_secure_storage
- intl

Additional packages should only be added when justified.

---

# 21. Backend Architecture

Recommended Laravel structure:

```text
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Requests/
│   │   └── Resources/
│   ├── Models/
│   ├── Services/
│   └── Policies/
│
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
│
├── routes/
│   ├── api.php
│   └── web.php
│
└── tests/
```

Backend responsibilities:

- Authentication.
- Authorization.
- Validation.
- CRUD.
- Business rules.
- API responses.
- Audit logging.
- Notifications.
- AI service integration.

---

# 22. User Roles and Authorization

Roles:

```text
ADMIN
DOCTOR
STAFF
PATIENT
```

Permission examples:

| Feature | Admin | Doctor | Staff | Patient |
|---|---:|---:|---:|---:|
| Manage Users | Yes | No | No | No |
| Manage Doctors | Yes | No | Limited | No |
| View Patients | Yes | Authorized | Authorized | Own data |
| Manage Appointments | Yes | Own | Yes | Own |
| Create Medical Record | Yes/Authorized | Yes | No | No |
| View Medical Record | Yes | Authorized | Limited | Own |
| Create Prescription | Yes/Authorized | Yes | No | No |
| View Prescription | Yes | Authorized | Limited | Own |
| Health Tracker | Yes | Authorized | No | Own |
| AI Assistant | Yes | Yes | Yes | Yes |

All authorization must be enforced on the backend.

Frontend role checks are not sufficient for security.

---

# 23. Core Database Entities

Initial entities:

```text
users
patients
doctors
staff
specialties
doctor_schedules
appointments
medical_records
vital_signs
prescriptions
prescription_items
medicines
health_metrics
notifications
chat_conversations
chat_messages
payments
audit_logs
```

Additional entities can be introduced when required by implementation.

---

# 24. API Modules

Planned API modules:

```text
/api/auth
/api/patients
/api/doctors
/api/specialties
/api/appointments
/api/medical-records
/api/prescriptions
/api/medicines
/api/health-metrics
/api/notifications
/api/chat
/api/payments
/api/ai
```

API requirements:

- JSON responses.
- Authentication tokens.
- Validation.
- Pagination.
- Filtering.
- Consistent error format.
- HTTP status codes.
- Authorization middleware.

---

# 25. Security Requirements

Because the platform handles sensitive healthcare information:

- Use HTTPS in production.
- Hash passwords using secure framework defaults.
- Use secure authentication tokens.
- Protect API routes.
- Implement role-based authorization.
- Validate all input.
- Prevent unauthorized patient record access.
- Avoid logging sensitive medical information.
- Avoid exposing secrets in source code.
- Use environment variables for secrets.
- Implement audit logs for sensitive operations.
- Apply rate limiting where appropriate.
- Secure file uploads.
- Follow applicable privacy and healthcare regulations for the deployment jurisdiction.

The platform must not claim regulatory compliance until the required legal, security, and operational controls have actually been implemented and verified.

---

# 26. Error Handling

Every data-driven screen must support:

- Loading state.
- Success state.
- Empty state.
- Error state.
- Retry action where appropriate.

API errors should use consistent response structures.

User-facing errors should be understandable and should not expose stack traces or internal implementation details.

---

# 27. Testing Requirements

## Mobile

Test:

- Authentication.
- Navigation.
- Form validation.
- Appointment validation.
- Health metric calculations.
- Repository behavior.
- State management.
- UI critical flows.

Run:

```bash
flutter analyze
flutter test
```

## Backend

Test:

- Authentication.
- Authorization.
- API validation.
- Appointment rules.
- Medical record access.
- Prescription permissions.
- Patient data isolation.
- API responses.

Use Laravel feature and unit tests.

---

# 28. Development Roadmap

## Phase 1 — Product Foundation

- Project setup.
- Stitch design inspection.
- Flutter foundation.
- Design system.
- Navigation.
- Reusable components.

## Phase 2 — Patient Mobile UI

- Authentication.
- Home.
- Doctor discovery.
- Appointment booking.
- Appointments.
- Medical records.
- Prescriptions.
- Health tracker.
- Notifications.
- Profile.
- Chat.
- AI Assistant.

## Phase 3 — Backend

- Laravel setup.
- Database.
- Authentication.
- Role system.
- REST API.
- Core business logic.

## Phase 4 — API Integration

Connect Flutter to Laravel API.

Replace mock repositories with real repositories.

## Phase 5 — Admin Web

Build:

- Admin dashboard.
- User management.
- Doctor management.
- Patient management.
- Appointment management.
- Medicines.
- Payments.
- Reports.

## Phase 6 — Doctor Web

Build:

- Doctor dashboard.
- Schedule.
- Appointments.
- Patient records.
- Consultation.
- Prescriptions.
- Messages.

## Phase 7 — Staff Web

Build staff operational dashboard.

## Phase 8 — Production Readiness

- Security review.
- Automated tests.
- Performance optimization.
- Error monitoring.
- Backup strategy.
- Deployment.
- Documentation.

---

# 29. Non-Functional Requirements

## Performance

Mobile screens should load smoothly.

API responses should be optimized through:

- Pagination.
- Caching where appropriate.
- Efficient queries.
- Image optimization.

## Scalability

Architecture should support:

- Increasing patient volume.
- Increasing doctors.
- Multiple healthcare facilities.
- Additional services.
- Future mobile/web expansion.

## Maintainability

Code must be:

- Modular.
- Documented where necessary.
- Testable.
- Reusable.
- Consistent.

---

# 30. MVP Scope

The first MVP should prioritize:

### Patient

- Register/Login
- Profile
- Search doctor
- Doctor detail
- Book appointment
- Appointment history
- Medical records
- Prescriptions
- Health tracker
- Notifications

### Doctor

- Login
- Dashboard
- Schedule
- Appointments
- Patient detail
- Medical record
- Prescription

### Admin

- Login
- Dashboard
- Patient management
- Doctor management
- Appointment management

AI, realtime chat, advanced payment, and advanced analytics can initially be implemented as later-stage features.

---

# 31. Out of Scope for Initial MVP

Unless explicitly added later:

- Emergency dispatch.
- Ambulance tracking.
- Hospital bed management.
- Laboratory information system.
- Pharmacy inventory integration with external providers.
- Insurance claim processing.
- Wearable-device integration.
- Automated medical diagnosis.
- Autonomous prescription generation.
- Fully automated clinical decision-making.

---

# 32. AI Safety Boundary

The AI feature is an informational support feature.

It must not:

- Diagnose a patient.
- Claim certainty about a disease.
- Replace a doctor.
- Generate prescriptions independently.
- Tell users to ignore emergency symptoms.
- Present unverified medical information as fact.

The product should encourage professional medical evaluation when appropriate.

---

# 33. Acceptance Criteria

The platform is considered ready for MVP testing when:

- All primary user roles can authenticate.
- Patient can complete appointment flow.
- Doctor can manage appointments.
- Authorized doctor can create medical records.
- Patient can view their own medical records.
- Doctor can create prescriptions.
- Patient can view their own prescriptions.
- Patient can track health metrics.
- Notifications are functional.
- Role-based access is enforced by backend.
- Critical API endpoints have automated tests.
- Flutter analyzer passes.
- Backend tests pass.
- No critical UI overflow exists.
- Google Stitch visual design is consistently implemented.
- Sensitive data is protected from unauthorized access.

---

# 34. Future Enhancements

Potential future features:

- Video consultation.
- Realtime chat.
- Push notifications.
- Electronic payment gateway.
- Digital prescription verification.
- Wearable integration.
- Health reports.
- Multi-clinic support.
- Telemedicine.
- Family patient accounts.
- Insurance integration.
- Advanced analytics.
- AI health education.
- Appointment reminders.
- Multilingual support.

---

# 35. Project Principle

The HealthCare Integrated Medical Platform must be developed as a real maintainable software product.

Priorities:

1. Security
2. Patient safety
3. Privacy
4. Usability
5. Reliability
6. Maintainability
7. Scalability
8. Visual consistency

Do not optimize for generating the largest amount of code as quickly as possible.

Build incrementally, test each major feature, and keep the architecture clean.
