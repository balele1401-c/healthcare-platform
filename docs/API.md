# REST API Specification — HealthCare Integrated Medical Platform

## 1. Global API Standards

### Base URL
- Local / Staging: `http://127.0.0.1:8000/api/v1`
- Production: `https://api.healthcare.platform/api/v1`

### Authentication & Headers
- Standard Bearer token authentication via Laravel Sanctum:
  `Authorization: Bearer <sanctum_token>`
- Standard Headers:
  `Accept: application/json`
  `Content-Type: application/json`

### Standard Response Envelope

#### Success (Single Item) (HTTP 200 / 201)
```json
{
  "success": true,
  "message": "Request successful",
  "data": { ... }
}
```

#### Collection / Paginated (HTTP 200)
```json
{
  "success": true,
  "message": "Data retrieved successfully",
  "data": [ ... ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 75,
    "from": 1,
    "to": 15
  }
}
```

#### Error (HTTP 400 / 401 / 403 / 404 / 422 / 429 / 500)
```json
{
  "success": false,
  "message": "Request failed",
  "errors": {
    "email": ["The email has already been taken."]
  }
}
```

---

## 2. Implemented API Endpoints (Phase 5)

### 1. Health Probe
| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `GET` | `/health` | No | System health check and database connectivity probe |

### 2. Authentication (`/auth`)
| Method | Endpoint | Auth | Throttled | Description |
|---|---|---|---|---|
| `POST` | `/auth/register` | No | Yes | Register new patient user account with demographic profile |
| `POST` | `/auth/login` | No | Yes | Authenticate credentials & issue Sanctum bearer token |
| `POST` | `/auth/logout` | Yes | Yes | Revoke active access token |
| `GET` | `/auth/me` | Yes | Yes | Return authenticated user with linked patient/doctor/staff profile |

### 3. Patient Portal (`/patient`)
| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `GET` | `/patient/profile` | Patient | Retrieve current patient demographic and clinical profile |
| `PUT` | `/patient/profile` | Patient | Update personal details, contact, blood type, allergies |
| `GET` | `/patient/appointments` | Patient | List patient's appointments (supports `?status=`, pagination) |
| `GET` | `/patient/medical-records` | Patient | List patient's medical records with vitals & prescriptions |
| `GET` | `/patient/prescriptions` | Patient | List patient's prescriptions (supports `?status=`) |
| `GET` | `/patient/health-metrics` | Patient | List patient's biometric vitals (supports `?metric_type=`, `?from=`, `?to=`) |
| `GET` | `/patient/notifications` | Patient | List in-app notifications (supports `?read=0|1`) |

### 4. Doctors & Specialists (`/doctors`)
| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `GET` | `/doctors` | Public / Auth | Search and filter doctors (by `?specialty_id=`, `?specialty=`, `?search=`, pagination) |
| `GET` | `/doctors/{doctor}` | Public / Auth | View complete doctor profile, rating, bio, fees |
| `GET` | `/doctors/{doctor}/schedules` | Public / Auth | View available weekly consultation hours |

### 5. Medical Specialties (`/specialties`)
| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `GET` | `/specialties` | Public / Auth | List active specialties with active doctor counts (supports `?search=`) |
| `GET` | `/specialties/{specialty}` | Public / Auth | View specialty details with active doctors |

### 6. Appointments (`/appointments`)
| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `GET` | `/appointments` | Yes | List appointments (role-aware: Patient sees own; Doctor sees assigned; Staff sees clinic) |
| `POST` | `/appointments` | Patient | Book new appointment slot |
| `GET` | `/appointments/{appointment}` | Yes | View appointment details (enforces ownership/doctor policy) |
| `PUT` | `/appointments/{appointment}` | Yes | Update notes or reschedule slot |
| `POST` | `/appointments/{appointment}/cancel` | Yes | Cancel appointment with cancellation reason |

### 7. Medical Records (`/medical-records`)
| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `GET` | `/medical-records` | Patient, Doctor, Admin | List clinical records (Staff barred per clinical privacy) |
| `GET` | `/medical-records/{medicalRecord}` | Patient, Doctor, Admin | View full medical visit details, diagnosis, vitals, prescriptions |

### 8. Prescriptions (`/prescriptions`)
| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `GET` | `/prescriptions` | Patient, Doctor, Admin | List digital prescriptions (Staff barred) |
| `GET` | `/prescriptions/{prescription}` | Patient, Doctor, Admin | View prescription line items, dosage, quantity, refills |

### 9. Health Metrics (`/health-metrics`)
| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `GET` | `/health-metrics` | Patient, Doctor, Admin | List biometric vital entries (filters: `?metric_type=`, `?from=`, `?to=`) |
| `POST` | `/health-metrics` | Patient | Record new biometric reading (weight, bp, hr, spo2, glucose, temp, bmi) |
| `GET` | `/health-metrics/{healthMetric}` | Patient, Doctor, Admin | View individual reading |
| `PUT` | `/health-metrics/{healthMetric}` | Patient | Update reading values |
| `DELETE` | `/health-metrics/{healthMetric}` | Patient | Delete a personal metric entry |

### 10. Notifications (`/notifications`)
| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `GET` | `/notifications` | Any Auth | List user notifications (filter: `?read=0|1`) |
| `POST` | `/notifications/{notification}/read` | Any Auth | Mark individual notification as read |
| `POST` | `/notifications/read-all` | Any Auth | Mark all unread notifications as read |

### 11. Chat & Consultation Channels (`/conversations`)
| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `GET` | `/conversations` | Patient, Doctor | List active consultation channels |
| `POST` | `/conversations` | Patient | Initiate conversation with doctor |
| `GET` | `/conversations/{conversation}` | Patient, Doctor | View conversation channel details |
| `GET` | `/conversations/{conversation}/messages` | Patient, Doctor | View paginated conversation history |
| `POST` | `/conversations/{conversation}/messages` | Patient, Doctor | Send message in channel |

### 12. Payment Records (`/payments`)
| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `GET` | `/payments` | Patient, Billing Staff, Admin | List payment transaction invoices |
| `GET` | `/payments/{payment}` | Patient, Billing Staff, Admin | View payment receipt details |
