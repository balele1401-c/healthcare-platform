import { Response } from 'express';
import { AuthenticatedRequest } from '../middleware/auth.js';
import { HealthMetricsService } from '../services/healthMetrics.service.js';
import { sendSuccess, sendError } from '../utils/response.js';

export class HealthMetricsController {
  static async getMetrics(req: AuthenticatedRequest, res: Response) {
    try {
      const userId = req.user!.userId;
      const limit = parseInt(req.query.limit as string) || 50;
      const metrics = HealthMetricsService.getMetrics(userId, limit);
      return sendSuccess(res, metrics, 'Metrics fetched successfully');
    } catch (error: any) {
      return sendError(res, error.message || 'Failed to fetch metrics', 400);
    }
  }

  static async createMetric(req: AuthenticatedRequest, res: Response) {
    try {
      const userId = req.user!.userId;
      const metric = HealthMetricsService.createMetric(userId, req.body);
      return sendSuccess(res, metric, 'Metric created successfully', 201);
    } catch (error: any) {
      return sendError(res, error.message || 'Failed to create metric', 400);
    }
  }
}
