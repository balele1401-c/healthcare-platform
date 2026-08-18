import { v4 as uuidv4 } from 'uuid';
import db from '../db/index.js';

export class MedicationsService {
  static getMedicationsForToday(userId: string) {
    const today = new Date().toISOString().split('T')[0];

    const medications = db
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

  static createMedication(userId: string, data: {
    name: string;
    dosage?: string;
    schedule_time: string;
  }) {
    const medId = uuidv4();

    db.prepare(`
      INSERT INTO medications (id, user_id, name, dosage, schedule_time, is_active)
      VALUES (?, ?, ?, ?, ?, 1)
    `).run(
      medId,
      userId,
      data.name,
      data.dosage || null,
      data.schedule_time
    );

    return db.prepare('SELECT * FROM medications WHERE id = ?').get(medId);
  }

  static checkMedication(userId: string, medicationId: string, status: 'taken' | 'skipped' | 'pending') {
    const today = new Date().toISOString().split('T')[0];

    const med = db.prepare('SELECT * FROM medications WHERE id = ? AND user_id = ?').get(medicationId, userId);
    if (!med) {
      throw new Error('Medication not found');
    }

    const existingLog = db
      .prepare('SELECT id FROM medication_logs WHERE medication_id = ? AND log_date = ?')
      .get(medicationId, today);

    if (existingLog) {
      db.prepare('UPDATE medication_logs SET status = ?, logged_at = ? WHERE medication_id = ? AND log_date = ?')
        .run(status, new Date().toISOString(), medicationId, today);
    } else {
      const logId = uuidv4();
      db.prepare(`
        INSERT INTO medication_logs (id, medication_id, log_date, status, logged_at)
        VALUES (?, ?, ?, ?, ?)
      `).run(logId, medicationId, today, status, new Date().toISOString());
    }

    return db
      .prepare(`
        SELECT m.*, ml.status, ml.logged_at
        FROM medications m
        LEFT JOIN medication_logs ml ON m.id = ml.medication_id AND ml.log_date = ?
        WHERE m.id = ?
      `)
      .get(today, medicationId);
  }
}
