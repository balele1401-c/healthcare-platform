import { Router } from 'express';
import { HealthMetricsController } from '../controllers/healthMetrics.controller.js';
import { authenticateToken } from '../middleware/auth.js';
import { validate } from '../middleware/validate.js';
import { createMetricSchema } from '../schemas/index.js';

const router = Router();

router.get('/', authenticateToken, HealthMetricsController.getMetrics);
router.post('/', authenticateToken, validate(createMetricSchema), HealthMetricsController.createMetric);

export default router;
