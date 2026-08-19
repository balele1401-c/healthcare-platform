import { Response } from 'express';
import { AuthenticatedRequest } from '../middleware/auth.js';
import { MedicationsService } from '../services/medications.service.js';
import { sendSuccess, sendError } from '../utils/response.js';

export class MedicationsController {
  static async getTodayMedications(req: AuthenticatedRequest, res: Response) {
    try {
      const userId = req.user!.userId;
      const medications = MedicationsService.getMedicationsForToday(userId);
      return sendSuccess(res, medications, 'Today medications fetched successfully');
    } catch (error: any) {
      return sendError(res, error.message || 'Failed to fetch medications', 400);
    }
  }

  static async createMedication(req: AuthenticatedRequest, res: Response) {
    try {
      const userId = req.user!.userId;
      const medication = MedicationsService.createMedication(userId, req.body);
      return sendSuccess(res, medication, 'Medication created successfully', 201);
    } catch (error: any) {
      return sendError(res, error.message || 'Failed to create medication', 400);
    }
  }

  static async checkMedication(req: AuthenticatedRequest, res: Response) {
    try {
      const userId = req.user!.userId;
      const { id } = req.params;
      const { status } = req.body;
      const medicationId = Array.isArray(id) ? id[0] : id;
      const updated = MedicationsService.checkMedication(userId, medicationId, status);
      return sendSuccess(res, updated, 'Medication status updated successfully');
    } catch (error: any) {
      return sendError(res, error.message || 'Failed to update medication status', 400);
    }
  }
}
