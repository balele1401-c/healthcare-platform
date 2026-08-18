"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.MedicationsService = void 0;
const uuid_1 = require("uuid");
const index_js_1 = __importDefault(require("../db/index.js"));
class MedicationsService {
    static getMedicationsForToday(userId) {
        const today = new Date().toISOString().split('T')[0];
        const medications = index_js_1.default
            .prepare(`
        SELECT m.*, 
               COALESCE(ml.status, 'pending') as status,
               ml.logged_at
        FROM medications m
        LEFT JOIN medication_logs ml ON m.id = ml.medication_id AND ml.log_date = ?
        WHERE m.user_id = ? AND m.is_active = 1
        ORDER BY m.schedule_time ASC
      `)
            .all(today, userId);
        return medications;
    }
    static createMedication(userId, data) {
        const medId = (0, uuid_1.v4)();
        index_js_1.default.prepare(`
      INSERT INTO medications (id, user_id, name, dosage, schedule_time, is_active)
      VALUES (?, ?, ?, ?, ?, 1)
    `).run(medId, userId, data.name, data.dosage || null, data.schedule_time);
        return index_js_1.default.prepare('SELECT * FROM medications WHERE id = ?').get(medId);
    }
    static checkMedication(userId, medicationId, status) {
        const today = new Date().toISOString().split('T')[0];
        const med = index_js_1.default.prepare('SELECT * FROM medications WHERE id = ? AND user_id = ?').get(medicationId, userId);
        if (!med) {
            throw new Error('Medication not found');
        }
        const existingLog = index_js_1.default
            .prepare('SELECT id FROM medication_logs WHERE medication_id = ? AND log_date = ?')
            .get(medicationId, today);
        if (existingLog) {
            index_js_1.default.prepare('UPDATE medication_logs SET status = ?, logged_at = ? WHERE medication_id = ? AND log_date = ?')
                .run(status, new Date().toISOString(), medicationId, today);
        }
        else {
            const logId = (0, uuid_1.v4)();
            index_js_1.default.prepare(`
        INSERT INTO medication_logs (id, medication_id, log_date, status, logged_at)
        VALUES (?, ?, ?, ?, ?)
      `).run(logId, medicationId, today, status, new Date().toISOString());
        }
        return index_js_1.default
            .prepare(`
        SELECT m.*, ml.status, ml.logged_at
        FROM medications m
        LEFT JOIN medication_logs ml ON m.id = ml.medication_id AND ml.log_date = ?
        WHERE m.id = ?
      `)
            .get(today, medicationId);
    }
}
exports.MedicationsService = MedicationsService;
