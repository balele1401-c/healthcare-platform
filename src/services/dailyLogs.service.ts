import { v4 as uuidv4 } from 'uuid';
import db from '../db/index.js';

export interface DailyLogRow {
  id: string;
  user_id: string;
  log_date: string;
  water_intake_ml: number;
  sleep_hours: number;
  steps_count: number;
  notes: string | null;
  created_at: string;
}

export class DailyLogsService {
  static getTodayLog(userId: string): DailyLogRow {
    const today = new Date().toISOString().split('T')[0];
    let log = db
      .prepare('SELECT * FROM daily_logs WHERE user_id = ? AND log_date = ?')
      .get(userId, today) as DailyLogRow | undefined;

    if (!log) {
      const logId = uuidv4();
      db.prepare(`
        INSERT INTO daily_logs (id, user_id, log_date, water_intake_ml, sleep_hours, steps_count)
        VALUES (?, ?, ?, 0, 0, 0)
      `).run(logId, userId, today);

      log = db.prepare('SELECT * FROM daily_logs WHERE id = ?').get(logId) as DailyLogRow;
    }

    return log;
  }

  static updateWater(userId: string, deltaMl: number) {
    const today = new Date().toISOString().split('T')[0];
    let log = this.getTodayLog(userId);

    const newWater = Math.max(0, (log.water_intake_ml || 0) + deltaMl);
    db.prepare('UPDATE daily_logs SET water_intake_ml = ? WHERE user_id = ? AND log_date = ?').run(
      newWater,
      userId,
      today
    );

    return db.prepare('SELECT * FROM daily_logs WHERE user_id = ? AND log_date = ?').get(userId, today);
  }

  static updateSleep(userId: string, startTime: string, endTime: string) {
    const today = new Date().toISOString().split('T')[0];
    const start = new Date(startTime);
    const end = new Date(endTime);
    const durationMs = end.getTime() - start.getTime();
    const durationHours = durationMs / (1000 * 60 * 60);

    let log = this.getTodayLog(userId);
    db.prepare('UPDATE daily_logs SET sleep_hours = ? WHERE user_id = ? AND log_date = ?').run(
      Math.max(0, durationHours),
      userId,
      today
    );

    return db.prepare('SELECT * FROM daily_logs WHERE user_id = ? AND log_date = ?').get(userId, today);
  }

  static updateSteps(userId: string, steps: number) {
    const today = new Date().toISOString().split('T')[0];
    this.getTodayLog(userId);

    db.prepare('UPDATE daily_logs SET steps_count = ? WHERE user_id = ? AND log_date = ?').run(
      Math.max(0, steps),
      userId,
      today
    );

    return db.prepare('SELECT * FROM daily_logs WHERE user_id = ? AND log_date = ?').get(userId, today);
  }
}
