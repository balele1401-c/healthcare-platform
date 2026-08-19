import { v4 as uuidv4 } from 'uuid';
import db from '../db/index.js';

export class HealthMetricsService {
  static getMetrics(userId: string, limit: number = 50) {
    return db
      .prepare(`
        SELECT * FROM health_metrics
        WHERE user_id = ?
        ORDER BY measured_at DESC
        LIMIT ?
      `)
      .all(userId, limit);
  }

  static createMetric(userId: string, data: {
    metric_type: 'weight' | 'blood_pressure' | 'glucose';
    value_primary: number;
    value_secondary?: number;
    measured_at?: string;
  }) {
    const metricId = uuidv4();
    const measuredAt = data.measured_at || new Date().toISOString();

    let bmiValue = null;
    if (data.metric_type === 'weight') {
      const user = db.prepare('SELECT height_cm FROM users WHERE id = ?').get(userId) as any;
      if (user && user.height_cm) {
        const heightM = user.height_cm / 100;
        bmiValue = data.value_primary / (heightM * heightM);
      }
    }

    db.prepare(`
      INSERT INTO health_metrics (id, user_id, metric_type, value_primary, value_secondary, measured_at)
      VALUES (?, ?, ?, ?, ?, ?)
    `).run(
      metricId,
      userId,
      data.metric_type,
      data.value_primary,
      data.value_secondary || null,
      measuredAt
    );

    const metric = db
      .prepare('SELECT * FROM health_metrics WHERE id = ?')
      .get(metricId) as Record<string, unknown>;

    return {
      ...metric,
      ...(bmiValue && data.metric_type === 'weight' && { bmi: bmiValue.toFixed(2) }),
    };
  }
}
