"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.AnalyticsController = void 0;
const analytics_service_js_1 = require("../services/analytics.service.js");
const response_js_1 = require("../utils/response.js");
class AnalyticsController {
    static async getWeeklySummary(req, res) {
        try {
            const userId = req.user.userId;
            const summary = analytics_service_js_1.AnalyticsService.getWeeklySummary(userId);
            return (0, response_js_1.sendSuccess)(res, summary, 'Weekly summary fetched successfully');
        }
        catch (error) {
            return (0, response_js_1.sendError)(res, error.message || 'Failed to fetch weekly summary', 400);
        }
    }
}
exports.AnalyticsController = AnalyticsController;
