"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.DailyLogsService = void 0;
const uuid_1 = require("uuid");
const index_js_1 = __importDefault(require("../db/index.js"));
class DailyLogsService {
    static getTodayLog(userId) {
        const today = new Date().toISOString().split('T')[0];
        let log = index_js_1.default
            .prepare('SELECT * FROM daily_logs WHERE user_id = ? AND log_date = ?')
            .get(userId, today);
        if (!log) {
            const logId = (0, uuid_1.v4)();
            index_js_1.default.prepare(`
        INSERT INTO daily_logs (id, user_id, log_date, water_intake_ml, sleep_hours, steps_count)
        VALUES (?, ?, ?, 0, 0, 0)
      `).run(logId, userId, today);
            log = index_js_1.default.prepare('SELECT * FROM daily_logs WHERE id = ?').get(logId);
        }
        return log;
    }
    static updateWater(userId, deltaMl) {
        const today = new Date().toISOString().split('T')[0];
        let log = this.getTodayLog(userId);
        const newWater = Math.max(0, (log.water_intake_ml || 0) + deltaMl);
        index_js_1.default.prepare('UPDATE daily_logs SET water_intake_ml = ? WHERE user_id = ? AND log_date = ?').run(newWater, userId, today);
        return index_js_1.default.prepare('SELECT * FROM daily_logs WHERE user_id = ? AND log_date = ?').get(userId, today);
    }
    static updateSleep(userId, startTime, endTime) {
        const today = new Date().toISOString().split('T')[0];
        const start = new Date(startTime);
        const end = new Date(endTime);
        const durationMs = end.getTime() - start.getTime();
        const durationHours = durationMs / (1000 * 60 * 60);
        let log = this.getTodayLog(userId);
        index_js_1.default.prepare('UPDATE daily_logs SET sleep_hours = ? WHERE user_id = ? AND log_date = ?').run(Math.max(0, durationHours), userId, today);
        return index_js_1.default.prepare('SELECT * FROM daily_logs WHERE user_id = ? AND log_date = ?').get(userId, today);
    }
    static updateSteps(userId, steps) {
        const today = new Date().toISOString().split('T')[0];
        this.getTodayLog(userId);
        index_js_1.default.prepare('UPDATE daily_logs SET steps_count = ? WHERE user_id = ? AND log_date = ?').run(Math.max(0, steps), userId, today);
        return index_js_1.default.prepare('SELECT * FROM daily_logs WHERE user_id = ? AND log_date = ?').get(userId, today);
    }
}
exports.DailyLogsService = DailyLogsService;
