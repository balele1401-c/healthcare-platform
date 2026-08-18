import db from '../db/index.js';

export class AnalyticsService {
  static getWeeklySummary(userId: string) {
    const today = new Date();
    const sevenDaysAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
    const sevenDaysAgoStr = sevenDaysAgo.toISOString().split('T')[0];

    const dailyLogs = db
      .prepare(`
        SELECT log_date, water_intake_ml, sleep_hours, steps_count
        FROM daily_logs
        WHERE user_id = ? AND log_date >= ?
        ORDER BY log_date ASC
      `)
      .all(userId, sevenDaysAgoStr) as any[];

    const waterTrend = dailyLogs.map((log) => ({
      date: log.log_date,
      water_ml: log.water_intake_ml || 0,
    }));

    const sleepTrend = dailyLogs.map((log) => ({
      date: log.log_date,
      hours: log.sleep_hours || 0,
    }));

    const stepsTrend = dailyLogs.map((log) => ({
      date: log.log_date,
      steps: log.steps_count || 0,
    }));

    const avgSleep = dailyLogs.length
      ? (dailyLogs.reduce((sum, log) => sum + (log.sleep_hours || 0), 0) / dailyLogs.length).toFixed(2)
      : '0.00';

    const totalWater = dailyLogs.reduce((sum, log) => sum + (log.water_intake_ml || 0), 0);
    const totalSteps = dailyLogs.reduce((sum, log) => sum + (log.steps_count || 0), 0);

    const userMeds = db
      .prepare('SELECT COUNT(*) as count FROM medications WHERE user_id = ? AND is_active = 1')
      .get(userId) as any;

    let adherencePercentage: number = 0;
    if (userMeds.count > 0) {
      const takenCount = db
        .prepare(`
          SELECT COUNT(*) as count FROM medication_logs
          WHERE medication_id IN (
            SELECT id FROM medications WHERE user_id = ? AND is_active = 1
          ) AND log_date >= ? AND status = 'taken'
        `)
        .get(userId, sevenDaysAgoStr) as any;

      const totalExpected = userMeds.count * 7;
      adherencePercentage =
        totalExpected > 0 ? parseFloat(((takenCount.count / totalExpected) * 100).toFixed(2)) : 0;
    }

    return {
      period: `${sevenDaysAgoStr} to ${today.toISOString().split('T')[0]}`,
      water_trend: waterTrend,
      sleep_trend: sleepTrend,
      steps_trend: stepsTrend,
      summary: {
        avg_sleep_hours: parseFloat(avgSleep as string),
        total_water_ml: totalWater,
        total_steps: totalSteps,
        medication_adherence_percent: parseFloat(adherencePercentage as any),
      },
    };
  }
}
