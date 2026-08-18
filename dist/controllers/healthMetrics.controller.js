"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.HealthMetricsController = void 0;
const healthMetrics_service_js_1 = require("../services/healthMetrics.service.js");
const response_js_1 = require("../utils/response.js");
class HealthMetricsController {
    static async getMetrics(req, res) {
        try {
            const userId = req.user.userId;
            const limit = parseInt(req.query.limit) || 50;
            const metrics = healthMetrics_service_js_1.HealthMetricsService.getMetrics(userId, limit);
            return (0, response_js_1.sendSuccess)(res, metrics, 'Metrics fetched successfully');
        }
        catch (error) {
            return (0, response_js_1.sendError)(res, error.message || 'Failed to fetch metrics', 400);
        }
    }
    static async createMetric(req, res) {
        try {
            const userId = req.user.userId;
            const metric = healthMetrics_service_js_1.HealthMetricsService.createMetric(userId, req.body);
            return (0, response_js_1.sendSuccess)(res, metric, 'Metric created successfully', 201);
        }
        catch (error) {
            return (0, response_js_1.sendError)(res, error.message || 'Failed to create metric', 400);
        }
    }
}
exports.HealthMetricsController = HealthMetricsController;
