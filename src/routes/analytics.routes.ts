import { Router } from 'express';
import { AnalyticsController } from '../controllers/analytics.controller.js';
import { authenticateToken } from '../middleware/auth.js';

const router = Router();

router.get('/weekly', authenticateToken, AnalyticsController.getWeeklySummary);

export default router;
