"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
const uuid_1 = require("uuid");
const bcryptjs_1 = __importDefault(require("bcryptjs"));
const index_js_1 = __importDefault(require("../db/index.js"));
async function seedDatabase() {
    console.log('🌱 Starting database seeding...\n');
    try {
        const userId = (0, uuid_1.v4)();
        const passwordHash = await bcryptjs_1.default.hash('password123', 10);
        index_js_1.default.prepare(`
      INSERT INTO users (id, name, email, password_hash, birth_date, height_cm, water_goal_ml, sleep_goal_hours)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    `).run(userId, 'John Doe', 'john@example.com', passwordHash, '1990-05-15', 175, 2000, 8.0);
        console.log(`✅ Created user: john@example.com`);
        const today = new Date();
        for (let i = 6; i >= 0; i--) {
            const date = new Date(today);
            date.setDate(date.getDate() - i);
            const dateStr = date.toISOString().split('T')[0];
            const logId = (0, uuid_1.v4)();
            index_js_1.default.prepare(`
        INSERT INTO daily_logs (id, user_id, log_date, water_intake_ml, sleep_hours, steps_count, notes)
        VALUES (?, ?, ?, ?, ?, ?, ?)
      `).run(logId, userId, dateStr, 1500 + Math.random() * 1000, 6 + Math.random() * 3, 5000 + Math.random() * 5000, `Log for ${dateStr}`);
        }
        console.log(`✅ Created 7-day daily logs`);
        const med1Id = (0, uuid_1.v4)();
        index_js_1.default.prepare(`
      INSERT INTO medications (id, user_id, name, dosage, schedule_time, is_active)
      VALUES (?, ?, ?, ?, ?, 1)
    `).run(med1Id, userId, 'Aspirin', '100mg', '08:00:00');
        const med2Id = (0, uuid_1.v4)();
        index_js_1.default.prepare(`
      INSERT INTO medications (id, user_id, name, dosage, schedule_time, is_active)
      VALUES (?, ?, ?, ?, ?, 1)
    `).run(med2Id, userId, 'Vitamin D', '1000IU', '12:00:00');
        console.log(`✅ Created 2 medications`);
        for (let i = 6; i >= 0; i--) {
            const date = new Date(today);
            date.setDate(date.getDate() - i);
            const dateStr = date.toISOString().split('T')[0];
            const log1Id = (0, uuid_1.v4)();
            index_js_1.default.prepare(`
        INSERT INTO medication_logs (id, medication_id, log_date, status, logged_at)
        VALUES (?, ?, ?, ?, ?)
      `).run(log1Id, med1Id, dateStr, i % 2 === 0 ? 'taken' : 'skipped', new Date().toISOString());
            const log2Id = (0, uuid_1.v4)();
            index_js_1.default.prepare(`
        INSERT INTO medication_logs (id, medication_id, log_date, status, logged_at)
        VALUES (?, ?, ?, ?, ?)
      `).run(log2Id, med2Id, dateStr, 'taken', new Date().toISOString());
        }
        console.log(`✅ Created 7-day medication logs`);
        for (let i = 0; i < 5; i++) {
            const date = new Date(today);
            date.setDate(date.getDate() - Math.floor(Math.random() * 7));
            const dateStr = date.toISOString().split('T')[0];
            const metricId = (0, uuid_1.v4)();
            const weight = 70 + Math.random() * 5;
            index_js_1.default.prepare(`
        INSERT INTO health_metrics (id, user_id, metric_type, value_primary, measured_at)
        VALUES (?, ?, ?, ?, ?)
      `).run(metricId, userId, 'weight', weight, dateStr);
        }
        console.log(`✅ Created weight metrics with BMI calculation ready`);
        console.log('\n✨ Database seeding completed successfully!');
        console.log(`\n📝 Test Credentials:\n   Email: john@example.com\n   Password: password123\n`);
        process.exit(0);
    }
    catch (error) {
        console.error('❌ Seeding failed:', error);
        process.exit(1);
    }
}
seedDatabase();
