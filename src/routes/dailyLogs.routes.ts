import { Router } from 'express';
import { DailyLogsController } from '../controllers/dailyLogs.controller.js';
import { authenticateToken } from '../middleware/auth.js';
import { validate } from '../middleware/validate.js';
import { updateWaterSchema, updateSleepSchema, updateStepsSchema } from '../schemas/index.js';

const router = Router();

router.get('/today', authenticateToken, DailyLogsController.getToday);
router.post('/water', authenticateToken, validate(updateWaterSchema), DailyLogsController.updateWater);
router.post('/sleep', authenticateToken, validate(updateSleepSchema), DailyLogsController.updateSleep);
router.post('/steps', authenticateToken, validate(updateStepsSchema), DailyLogsController.updateSteps);

export default router;
