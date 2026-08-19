"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.DailyLogsController = void 0;
const dailyLogs_service_js_1 = require("../services/dailyLogs.service.js");
const response_js_1 = require("../utils/response.js");
class DailyLogsController {
    static async getToday(req, res) {
        try {
            const userId = req.user.userId;
            const log = dailyLogs_service_js_1.DailyLogsService.getTodayLog(userId);
            return (0, response_js_1.sendSuccess)(res, log, 'Today log fetched successfully');
        }
        catch (error) {
            return (0, response_js_1.sendError)(res, error.message || 'Failed to fetch today log', 400);
        }
    }
    static async updateWater(req, res) {
        try {
            const userId = req.user.userId;
            const { delta_ml } = req.body;
            const updatedLog = dailyLogs_service_js_1.DailyLogsService.updateWater(userId, delta_ml);
            return (0, response_js_1.sendSuccess)(res, updatedLog, 'Water intake updated successfully');
        }
        catch (error) {
            return (0, response_js_1.sendError)(res, error.message || 'Failed to update water intake', 400);
        }
    }
    static async updateSleep(req, res) {
        try {
            const userId = req.user.userId;
            const { start_time, end_time } = req.body;
            const updatedLog = dailyLogs_service_js_1.DailyLogsService.updateSleep(userId, start_time, end_time);
            return (0, response_js_1.sendSuccess)(res, updatedLog, 'Sleep log updated successfully');
        }
        catch (error) {
            return (0, response_js_1.sendError)(res, error.message || 'Failed to update sleep log', 400);
        }
    }
    static async updateSteps(req, res) {
        try {
            const userId = req.user.userId;
            const { steps } = req.body;
            const updatedLog = dailyLogs_service_js_1.DailyLogsService.updateSteps(userId, steps);
            return (0, response_js_1.sendSuccess)(res, updatedLog, 'Steps count updated successfully');
        }
        catch (error) {
            return (0, response_js_1.sendError)(res, error.message || 'Failed to update steps count', 400);
        }
    }
}
exports.DailyLogsController = DailyLogsController;
