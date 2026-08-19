# System Architecture — HealthCare Integrated Medical Platform

## 1. High-Level System Architecture

The **HealthCare Integrated Medical Platform** uses a distributed multi-tier architecture designed for high security, maintainability, and scalability.

```
                      ┌─────────────────────────────────────────┐
                      │          Flutter Mobile App             │
                      │         (Patient Android/iOS)           │
                      └────────────────────┬────────────────────┘
                                           │
                                           │ HTTPS / JSON REST API
                                           │ (Sanctum Bearer Token)
                                           ▼
                      ┌─────────────────────────────────────────┐
                      │            Laravel REST API             │
                      │     (Gateway, Auth, Business Logic)     │
                      └────────┬──────────────────────┬─────────┘
                               │                      │
               Database Access │                      │ AI Proxy / Webhooks
             (Eloquent / PDO)  │                      │ (Sanitized Requests)
                               ▼                      ▼
┌──────────────────────────────────────┐     ┌──────────────────────────────────┐
│          Relational Database         │     │         AI LLM Provider          │
│   (PostgreSQL / MySQL / MariaDB)     │     │ (Google Gemini API / Anthropic)  │
└──────────────────────────────────────┘     └──────────────────────────────────┘
                               ▲
                               │ Direct Web Sessions / Inertia / Blade
                               │ (Role-Based Access Control)
                               │
            ┌──────────────────┴──────────────────┐
            │                                     │
┌───────────┴─────────────┐             ┌─────────┴───────────────┐
│     Doctor Web Portal   │             │    Admin & Staff Portal │
│ (Clinical Consultations)│             │ (Operations & Mgmt)     │
└─────────────────────────┘             └─────────────────────────┘
```

---

## 2. Flutter Mobile Architecture (Patient App)

The mobile client follows a **Feature-First Clean Architecture** with unidirectional data flow powered by **Riverpod**.

```
lib/
├── core/
│   ├── constants/            # App dimensions, strings, asset keys, storage keys
│   ├── errors/               # Failure & AppException domain classes
│   ├── network/              # Dio HTTP client, AuthInterceptor, ErrorInterceptor
│   ├── routing/              # GoRouter configuration, AppRoutes, RouteGuards
│   ├── storage/              # FlutterSecureStorage & local persistent cache
│   ├── theme/                # Clinical Clarity theme: AppColors, AppTypography, AppTheme
│   └── utils/                # Date formatters, currency helpers, validators
│
├── features/
│   ├── auth/                 # Login, Register, Forgot Password, OTP, Profile Setup
│   ├── home/                 # Patient Home Dashboard, Quick Actions, Summary
│   ├── doctors/              # Doctor Search, Specialty Filter, Doctor Detail
│   ├── appointments/         # Date/Time Selection, Confirmation, Payment, History
│   ├── medical_records/      # Visit History, Clinical Summaries, Vital Signs
│   ├── prescriptions/        # Active/Past Prescriptions, Dosage, Instructions
│   ├── health_tracker/       # Vitals logging (BP, Glucose, Heart Rate, BMI)
│   ├── chat/                 # Doctor-Patient consultation messaging
│   ├── notifications/        # In-app alert history, Reminders
│   ├── profile/              # Personal Information, Emergency Contacts, Settings
│   └── ai_assistant/         # AI Health Information Assistant & Guardrails
│
├── shared/
│   ├── models/               # Shared pagination, user summary, base entity models
│   └── widgets/              # Buttons, inputs, cards, badges, dialogs, bottom sheets
│
└── main.dart                 # Application entry point with ProviderScope
```

### Feature Layering
Each feature module is structured into three clear layers:
1. **Data Layer**:
   - `data_sources/`: Remote API calls via Dio and Local Storage caching.
   - `repositories/`: Concrete repository implementations (e.g. `AppointmentRepositoryImpl`).
   - `dtos/`: JSON serialization/deserialization with `fromJson` / `toJson`.
2. **Domain Layer**:
   - `models/`: Immutable business entities.
   - `repositories/`: Abstract repository contracts (e.g. `AppointmentRepository`).
3. **Presentation Layer**:
   - `controllers/`: Riverpod `AsyncNotifier` or `StateNotifier` managing UI state.
   - `views/`: Full-screen widgets matching Stitch designs.
   - `widgets/`: Feature-scoped reusable UI subcomponents.

---

## 3. Laravel Backend Architecture

The backend operates as an API-first service with dedicated web administration controllers.

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/          # REST Controllers (Auth, Appointment, Record, AI, etc.)
│   │   │   └── Web/          # Web Controllers (Admin, Doctor, Staff Dashboards)
│   │   ├── Middleware/       # RoleMiddleware, AuditLogMiddleware, SanitizeInput
│   │   ├── Requests/         # FormRequest validation for every endpoint
│   │   └── Resources/        # API Resources for consistent JSON response shape
│   │
│   ├── Models/               # Eloquent Models with explicit relations & casts
│   ├── Policies/             # Granular Authorization Policies for each entity
│   ├── Services/             # Domain Business Logic (AppointmentService, AiService)
│   ├── Repositories/         # Data access encapsulation
│   └── Events/ & Listeners/  # AppointmentBooked, PrescriptionCreated notifications
│
├── database/
│   ├── migrations/           # Versioned relational schema definitions
│   ├── seeders/              # Comprehensive mock & initial master data seeders
│   └── factories/            # Model factories for automated testing
│
├── routes/
│   ├── api.php               # Versioned REST API endpoints (/api/v1/...)
│   └── web.php               # Admin, Doctor, and Staff web routes
│
└── tests/
    ├── Feature/              # HTTP endpoint integration & authorization tests
    └── Unit/                 # Service & calculation unit tests
```

---

## 4. AI Assistant Integration Architecture

The AI Health Assistant acts as an educational and preparation tool. It is architecturally insulated to enforce patient privacy and medical safety.

```
┌─────────────────────────┐
│   Flutter Mobile App    │
│  (Patient Input Query)  │
└───────────┬─────────────┘
            │
            │ POST /api/ai/query (Sanctum Token)
            ▼
┌────────────────────────────────────────────────────────┐
│               Laravel AI Service Proxy                 │
│ 1. Rate Limiting & Abuse Prevention                    │
│ 2. PII Sanitization (Strip phone numbers, SSNs, IDs)  │
│ 3. System Prompt Injection:                            │
│    - Role: General Health Informational Assistant      │
│    - Strict prohibition of definitive clinical dx     │
│    - Mandatory recommendation of professional care    │
└───────────┬────────────────────────────────────────────┘
            │
            │ Encrypted API Call (HTTPS)
            ▼
┌────────────────────────────────────────────────────────┐
│             External AI LLM Provider API               │
│               (Google Gemini API)                      │
└───────────┬────────────────────────────────────────────┘
            │
            │ LLM Stream / Response
            ▼
┌────────────────────────────────────────────────────────┐
│             Laravel Post-Processing Layer              │
│ 1. Safety Filter Verification                          │
│ 2. Standard Healthcare Disclaimer Appended             │
│ 3. Contextual Doctor Specialty Recommendations         │
└───────────┬────────────────────────────────────────────┘
            │
            │ Standard JSON Response
            ▼
┌────────────────────────────────────────────────────────┐
│   Flutter Presentation (UI with Warning Disclaimer)    │
└────────────────────────────────────────────────────────┘
```

---

## 5. Web Portal Architecture (Admin, Doctor, Staff)

- **Admin Portal**: System governance, specialty and clinic configuration, doctor credentialing, billing oversight, and system-wide audit log inspection.
- **Doctor Portal**: Daily schedule overview, upcoming consultations, interactive consultation cockpit, electronic medical record (EMR) entry, and digital prescription builder.
- **Staff Portal**: Triage, walk-in patient registration, doctor schedule assistance, and front-desk appointment verification.
