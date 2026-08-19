# Security & Data Protection Architecture

## 1. Overview & Healthcare Compliance Principles

Healthcare software requires strict security controls to protect **Protected Health Information (PHI)** and prevent unauthorized data disclosure.

> [!IMPORTANT]
> The platform must not claim regulatory compliance (such as formal HIPAA / GDPR / PDP certification) until legal and operational audits are formally performed. However, the technical architecture implements the highest industry standards for privacy, encryption, and data isolation from day one.

---

## 2. Core Security Tenets

### 1. Mandatory Backend Authorization (Never Trust the Client)
- All authorization decisions are strictly enforced in the Laravel backend using **Laravel Policies** and **Form Requests**.
- Flutter UI route guards and role-based visibility toggles are strictly for user experience (UX) and are never relied upon for data security.

### 2. Strict Patient Data Isolation
- A patient can **only** query and view their own data (`WHERE patient_id = :auth_patient_id`).
- Attempting to access another patient's medical record, prescription, or appointment by modifying the URL or API payload will unconditionally trigger an `HTTP 403 Forbidden` response and write an entry to `audit_logs`.

### 3. Doctor Access Control
- Doctors can only view records for patients who:
  1. Have an active or past appointment with that doctor, or
  2. Have been explicitly referred or authorized.
- Unrestricted browsing of the entire patient database is prohibited for clinical staff.

---

## 3. Threat Model & Countermeasures

| Vulnerability / Threat | Risk Level | Mitigation Strategy |
|---|---|---|
| **Broken Object Level Authorization (BOLA / IDOR)** | CRITICAL | Eloquent Global Scopes + Laravel Policies (`can('view', $medicalRecord)`). |
| **Credential Exposure / Hardcoded Secrets** | CRITICAL | All secrets kept in `.env` (excluded by `.gitignore`). Automated pre-commit linting. |
| **Insecure Token Storage on Mobile** | HIGH | Use `flutter_secure_storage` (Android EncryptedSharedPreferences / Keystore, iOS Keychain). |
| **SQL Injection (SQLi)** | HIGH | 100% Parameterized queries via PDO / Eloquent ORM. Raw SQL queries prohibited. |
| **Cross-Site Scripting (XSS)** | HIGH | Auto-escaping in Blade templates, sanitization of user-submitted rich text. |
| **Brute Force / Credential Stuffing** | MEDIUM | Rate limiting on `/api/v1/auth/login` (5 requests/minute) and OTP verification (3 requests/minute). |
| **Sensitive Data in Logs** | HIGH | Laravel `config/logging.php` configured to redact `password`, `token`, `credit_card`, and medical notes. |
| **Unsafe AI Diagnosis** | HIGH | System prompt guardrails, mandatory disclaimer, refusal to provide conclusive diagnosis. |

---

## 4. Secure Mobile Storage Strategy

On Flutter Android and iOS clients:
- **Session Tokens & Keys**: Saved exclusively in `flutter_secure_storage`.
- **Never Store**: Passwords, raw payment numbers, or unencrypted clinical history in plain `SharedPreferences`.
- **Cache Invalidation**: On `logout`, the local secure storage and in-memory Riverpod state are completely wiped.

---

## 5. Audit Logging Architecture

Every state change or access to Protected Health Information (PHI) records an immutable event in the `audit_logs` table:
- **Logged Actions**:
  - `AUTH_LOGIN_SUCCESS`, `AUTH_LOGIN_FAILED`
  - `VIEW_MEDICAL_RECORD`, `EXPORT_MEDICAL_RECORD`
  - `CREATE_PRESCRIPTION`, `UPDATE_PRESCRIPTION`
  - `BOOK_APPOINTMENT`, `CANCEL_APPOINTMENT`
  - `UNAUTHORIZED_ACCESS_ATTEMPT`
- **Audit Record Content**: `timestamp`, `user_id`, `role`, `ip_address`, `action`, `entity_type`, `entity_id`, `old_values`, `new_values`.

---

## 6. AI Safety & Privacy Guardrails

When interacting with the AI Health Assistant:
1. **PII Stripping**: The backend proxy strips patient names, phone numbers, and identifying codes before dispatching queries to the LLM.
2. **System Prompt Constraint**: The AI is instructed:
   - "You are a health education assistant. You are not a medical doctor."
   - "Never provide definitive diagnoses or prescribe medications."
   - "Always instruct users to seek immediate medical attention for severe symptoms (chest pain, shortness of breath, severe bleeding)."
3. **Mandatory Disclaimer**:
   > *AI Health Assistant provides general health information and does not replace professional medical diagnosis or treatment.*
