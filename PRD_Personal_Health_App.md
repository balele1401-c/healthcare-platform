# Product Requirement Document (PRD)
## Personal Health & Habit Tracker App (Vibe-Coded with OpenCode)

---

### 1. Document Overview
* **Product Name:** PulseTrack (Personal Health & Habit Tracker)
* **Version:** 1.0 (MVP)
* **Target Release:** Q3 2026
* **Status:** In Development (Vibe Coding with OpenCode CLI)
* **Author / Tech Lead:** Project Lead

---

### 2. Product Vision & Goals
* **Vision:** Membantu pengguna memantau kesehatan harian, melacak metrik vital, dan mempertahankan kebiasaan sehat secara konsisten melalui aplikasi yang ringan, responsif, dan mudah digunakan.
* **Goals:**
  * Menyediakan antarmuka pencatatan harian yang cepat (*under 5 seconds logging*).
  * Memberikan ringkasan analitik mingguan untuk evaluasi pola hidup.
  * Membangun arsitektur modular yang ramah pengembang untuk *iterative vibe coding* menggunakan OpenCode.

---

### 3. Target User & Persona
* **Demografi:** Usia 18–45 tahun, individu yang ingin mengontrol gaya hidup, pola tidur, hidrasi, dan konsumsi obat/suplemen.
* **Pain Points:**
  * Aplikasi kesehatan yang ada terlalu rumit, penuh iklan, dan lambat saat dibuka.
  * Sulit melihat korelasi antara pola tidur, hidrasi, dan kebugaran tubuh.
  * Sering lupa minum obat rutin atau mencatat metrik vital secara berkala.

---

### 4. System Architecture & Tech Stack

| Layer | Teknologi Rekomendasi | Justifikasi |
| :--- | :--- | :--- |
| **Frontend** | React / Next.js / Tailwind CSS (PWA) | Responsif mobile-first, mudah di-prompt via OpenCode, mendukung offline caching. |
| **Backend** | Node.js (Express / TypeScript) atau Laravel | RESTful API terstruktur, routing modular, mudah di-scaffold. |
| **Database** | SQLite (Local/Dev) / PostgreSQL (Production) | Ringan untuk local testing, handal untuk integritas data relasional. |
| **State & Charts** | Zustand / Context API & Chart.js / Recharts | Ringan, cepat render grafik tren kesehatan mingguan. |

---

### 5. Feature Scope & Requirements

#### 5.1 Authentication & Profile (Module A)
* **A-1:** Registrasi dan Login (Email/Password, JWT / Session Auth).
* **A-2:** Profil Pengguna: Nama, Tanggal Lahir, Gender, Tinggi Badan (cm), Target Harian (Air, Langkah, Tidur).

#### 5.2 Daily Health Tracker (Module B)
* **B-1 Water Intake:** Input volume air minum dengan tombol aksi cepat (+250ml, +500ml) dan progress bar menuju target harian (default: 2000ml).
* **B-2 Sleep Log:** Input jam tidur (waktu mulai tidur, waktu bangun) dan kalkulasi otomatis durasi tidur (jam/menit).
* **B-3 Activity / Steps:** Input langkah harian dan estimasi pembakaran kalori dasar.

#### 5.3 Vital Signs & Medical Metrics (Module C)
* **C-1 Weight & BMI:** Input berat badan harian/mingguan dengan kalkulasi BMI otomatis ($BMI = \frac{\text{weight (kg)}}{\text{height (m)}^2}$).
* **C-2 Blood Pressure & Glucose (Optional Log):** Pencatatan Sistol/Diastol (mmHg) dan level gula darah (mg/dL).
* **C-3 History List:** Tampilan riwayat pencatatan metrik vital terurut waktu.

#### 5.4 Medication & Supplement Reminder (Module D)
* **D-1 Medicine List:** Tambah/edit nama obat, dosis, frekuensi harian, dan waktu pengingat.
* **D-2 Daily Checklist:** Tandai status obat harian (*Taken / Skipped / Pending*).

#### 5.5 Analytics & Weekly Insights (Module E)
* **E-1 Trend Chart:** Grafik garis/batang untuk konsumsi air, durasi tidur, dan langkah selama 7 hari terakhir.
* **E-2 Summary Metric:** Rata-rata tidur mingguan, total capaian hidrasi, dan konsistensi minum obat (persentase).

---

### 6. Database Schema Design (Entity Relationship)

```sql
-- Users Table
CREATE TABLE users (
    id VARCHAR(36) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    birth_date DATE,
    height_cm REAL,
    water_goal_ml INTEGER DEFAULT 2000,
    sleep_goal_hours REAL DEFAULT 8.0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Daily Logs (Aggregation per Day)
CREATE TABLE daily_logs (
    id VARCHAR(36) PRIMARY KEY,
    user_id VARCHAR(36) REFERENCES users(id) ON DELETE CASCADE,
    log_date DATE NOT NULL,
    water_intake_ml INTEGER DEFAULT 0,
    sleep_hours REAL DEFAULT 0,
    steps_count INTEGER DEFAULT 0,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(user_id, log_date)
);

-- Health Metrics (Time-series Vital Signs)
CREATE TABLE health_metrics (
    id VARCHAR(36) PRIMARY KEY,
    user_id VARCHAR(36) REFERENCES users(id) ON DELETE CASCADE,
    metric_type VARCHAR(50) NOT NULL, -- 'weight', 'blood_pressure', 'glucose'
    value_primary REAL NOT NULL,      -- e.g. weight (kg), systolic (mmHg), glucose (mg/dL)
    value_secondary REAL,             -- e.g. diastolic (mmHg)
    measured_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Medications
CREATE TABLE medications (
    id VARCHAR(36) PRIMARY KEY,
    user_id VARCHAR(36) REFERENCES users(id) ON DELETE CASCADE,
    name VARCHAR(100) NOT NULL,
    dosage VARCHAR(50),
    schedule_time TIME NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Medication Intake Logs
CREATE TABLE medication_logs (
    id VARCHAR(36) PRIMARY KEY,
    medication_id VARCHAR(36) REFERENCES medications(id) ON DELETE CASCADE,
    log_date DATE NOT NULL,
    status VARCHAR(20) DEFAULT 'taken', -- 'taken', 'skipped'
    logged_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

### 7. API Specification (Core Endpoints)

| Method | Endpoint | Deskripsi |
| :--- | :--- | :--- |
| `POST` | `/api/auth/register` | Pendaftaran akun baru |
| `POST` | `/api/auth/login` | Login dan penerbitan auth token |
| `GET` | `/api/logs/today` | Mengambil log kesehatan hari ini |
| `POST` | `/api/logs/water` | Update asupan air harian (+ delta ml) |
| `POST` | `/api/logs/sleep` | Simpan catatan durasi tidur |
| `GET` | `/api/metrics` | Ambil riwayat vital metrics pengguna |
| `POST` | `/api/metrics` | Tambah log metrik vital baru |
| `GET` | `/api/medications/today` | Daftar obat dan status checklist hari ini |
| `POST` | `/api/medications/:id/check` | Update status minum obat |
| `GET` | `/api/analytics/weekly` | Data agregasi 7 hari untuk chart |

---

### 8. OpenCode Prompting Roadmap (Vibe Coding Steps)

1. **Sprint 1 — Backend & DB Initialization:**
   * *"Scaffold an Express TypeScript / Laravel API project with SQLite/PostgreSQL. Implement DB migrations according to the schema in the PRD, and set up auth JWT middleware."*
2. **Sprint 2 — CRUD & Endpoints Logic:**
   * *"Create REST controllers for daily logs, health metrics, and medication tracking. Add input validation and weekly aggregation endpoint."*
3. **Sprint 3 — Mobile-Responsive UI & Dashboard:**
   * *"Build a mobile-first dashboard using Tailwind CSS and React/Blade. Include quick action buttons for water intake (+250ml) and a summary card for sleep and BMI."*
4. **Sprint 4 — Data Visualization & Polish:**
   * *"Integrate Chart.js/Recharts to visualize 7-day trends for hydration, sleep, and steps. Add error boundaries and smooth interactions."*

---

### 9. Success Metrics (KPI)
* Waktu pencatatan log harian kurang dari 5 detik.
* 0 crash pada cold startup aplikasi.
* Retensi pengguna harian (DAU/MAU) mencapai >40% dalam 14 hari pertama.
