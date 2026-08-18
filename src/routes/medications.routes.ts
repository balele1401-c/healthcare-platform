import { Router } from 'express';
import { MedicationsController } from '../controllers/medications.controller.js';
import { authenticateToken } from '../middleware/auth.js';
import { validate } from '../middleware/validate.js';
import { createMedicationSchema, checkMedicationSchema } from '../schemas/index.js';

const router = Router();

router.get('/today', authenticateToken, MedicationsController.getTodayMedications);
router.post('/', authenticateToken, validate(createMedicationSchema), MedicationsController.createMedication);
router.post('/:id/check', authenticateToken, validate(checkMedicationSchema), MedicationsController.checkMedication);

export default router;
