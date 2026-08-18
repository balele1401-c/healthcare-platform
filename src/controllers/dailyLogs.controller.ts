import { Response } from 'express';
import { AuthenticatedRequest } from '../middleware/auth.js';
import { DailyLogsService } from '../services/dailyLogs.service.js';
import { sendSuccess, sendError } from '../utils/response.js';

export class DailyLogsController {
  static async getToday(req: AuthenticatedRequest, res: Response) {
    try {
      const userId = req.user!.userId;
      const log = DailyLogsService.getTodayLog(userId);
      return sendSuccess(res, log, 'Today log fetched successfully');
    } catch (error: any) {
      return sendError(res, error.message || 'Failed to fetch today log', 400);
    }
  }

  static async updateWater(req: AuthenticatedRequest, res: Response) {
    try {
      const userId = req.user!.userId;
      const { delta_ml } = req.body;
      const updatedLog = DailyLogsService.updateWater(userId, delta_ml);
      return sendSuccess(res, updatedLog, 'Water intake updated successfully');
    } catch (error: any) {
      return sendError(res, error.message || 'Failed to update water intake', 400);
    }
  }

  static async updateSleep(req: AuthenticatedRequest, res: Response) {
    try {
      const userId = req.user!.userId;
      const { start_time, end_time } = req.body;
      const updatedLog = DailyLogsService.updateSleep(userId, start_time, end_time);
      return sendSuccess(res, updatedLog, 'Sleep log updated successfully');
    } catch (error: any) {
      return sendError(res, error.message || 'Failed to update sleep log', 400);
    }
  }

  static async updateSteps(req: AuthenticatedRequest, res: Response) {
    try {
      const userId = req.user!.userId;
      const { steps } = req.body;
      const updatedLog = DailyLogsService.updateSteps(userId, steps);
      return sendSuccess(res, updatedLog, 'Steps count updated successfully');
    } catch (error: any) {
      return sendError(res, error.message || 'Failed to update steps count', 400);
    }
  }
}
