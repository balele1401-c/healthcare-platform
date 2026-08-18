"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.HealthMetricsService = void 0;
const uuid_1 = require("uuid");
const index_js_1 = __importDefault(require("../db/index.js"));
class HealthMetricsService {
    static getMetrics(userId, limit = 50) {
        return index_js_1.default
            .prepare(`
        SELECT * FROM health_metrics
        WHERE user_id = ?
        ORDER BY measured_at DESC
        LIMIT ?
      `)
            .all(userId, limit);
    }
    static createMetric(userId, data) {
        const metricId = (0, uuid_1.v4)();
        const measuredAt = data.measured_at || new Date().toISOString();
        let bmiValue = null;
        if (data.metric_type === 'weight') {
            const user = index_js_1.default.prepare('SELECT height_cm FROM users WHERE id = ?').get(userId);
            if (user && user.height_cm) {
                const heightM = user.height_cm / 100;
                bmiValue = data.value_primary / (heightM * heightM);
            }
        }
        index_js_1.default.prepare(`
      INSERT INTO health_metrics (id, user_id, metric_type, value_primary, value_secondary, measured_at)
      VALUES (?, ?, ?, ?, ?, ?)
    `).run(metricId, userId, data.metric_type, data.value_primary, data.value_secondary || null, measuredAt);
        const metric = index_js_1.default
            .prepare('SELECT * FROM health_metrics WHERE id = ?')
            .get(metricId);
        return {
            ...metric,
            ...(bmiValue && data.metric_type === 'weight' && { bmi: bmiValue.toFixed(2) }),
        };
    }
}
exports.HealthMetricsService = HealthMetricsService;
