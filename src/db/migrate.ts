import db from './index.js';

export function runMigrations() {
  console.log('Running database migrations...');

  const migrationQueries = `
    -- Users Table
    CREATE TABLE IF NOT EXISTS users (
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
    CREATE TABLE IF NOT EXISTS daily_logs (
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
    CREATE TABLE IF NOT EXISTS health_metrics (
        id VARCHAR(36) PRIMARY KEY,
        user_id VARCHAR(36) REFERENCES users(id) ON DELETE CASCADE,
        metric_type VARCHAR(50) NOT NULL, -- 'weight', 'blood_pressure', 'glucose'
        value_primary REAL NOT NULL,      -- e.g. weight (kg), systolic (mmHg), glucose (mg/dL)
        value_secondary REAL,             -- e.g. diastolic (mmHg)
        measured_at TIMESTAMP NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    -- Medications
    CREATE TABLE IF NOT EXISTS medications (
        id VARCHAR(36) PRIMARY KEY,
        user_id VARCHAR(36) REFERENCES users(id) ON DELETE CASCADE,
        name VARCHAR(100) NOT NULL,
        dosage VARCHAR(50),
        schedule_time TIME NOT NULL,
        is_active BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    -- Medication Intake Logs
    CREATE TABLE IF NOT EXISTS medication_logs (
        id VARCHAR(36) PRIMARY KEY,
        medication_id VARCHAR(36) REFERENCES medications(id) ON DELETE CASCADE,
        log_date DATE NOT NULL,
        status VARCHAR(20) DEFAULT 'taken', -- 'taken', 'skipped'
        logged_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    -- Indexes for Performance
    CREATE INDEX IF NOT EXISTS idx_daily_logs_user_date ON daily_logs(user_id, log_date);
    CREATE INDEX IF NOT EXISTS idx_health_metrics_user ON health_metrics(user_id, measured_at);
    CREATE INDEX IF NOT EXISTS idx_medications_user ON medications(user_id);
    CREATE INDEX IF NOT EXISTS idx_medication_logs_med ON medication_logs(medication_id, log_date);
  `;

  db.exec(migrationQueries);
  console.log('Database migrations completed successfully.');
}

const isDirectRun =
  process.argv[1]?.endsWith('migrate.ts') || process.argv[1]?.endsWith('migrate.js');

if (isDirectRun) {
  try {
    runMigrations();
    process.exit(0);
  } catch (error) {
    console.error('Migration failed:', error);
    process.exit(1);
  }
}
