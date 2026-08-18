import { Response } from 'express';
import { AuthenticatedRequest } from '../middleware/auth.js';
import { AnalyticsService } from '../services/analytics.service.js';
import { sendSuccess, sendError } from '../utils/response.js';

export class AnalyticsController {
  static async getWeeklySummary(req: AuthenticatedRequest, res: Response) {
    try {
      const userId = req.user!.userId;
      const summary = AnalyticsService.getWeeklySummary(userId);
      return sendSuccess(res, summary, 'Weekly summary fetched successfully');
    } catch (error: any) {
      return sendError(res, error.message || 'Failed to fetch weekly summary', 400);
    }
  }
}
