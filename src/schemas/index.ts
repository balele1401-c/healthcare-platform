import { z } from 'zod';

export const registerSchema = z.object({
  name: z.string().min(2, 'Name must be at least 2 characters'),
  email: z.string().email('Invalid email address'),
  password: z.string().min(6, 'Password must be at least 6 characters'),
  birth_date: z.string().optional(),
  height_cm: z.number().positive().optional(),
  water_goal_ml: z.number().positive().optional(),
  sleep_goal_hours: z.number().positive().optional(),
});

export const loginSchema = z.object({
  email: z.string().email('Invalid email address'),
  password: z.string().min(1, 'Password is required'),
});

export const updateWaterSchema = z.object({
  delta_ml: z.number().int('Delta must be an integer'),
});

export const updateSleepSchema = z.object({
  start_time: z.string().datetime({ message: 'Invalid ISO start time' }).or(z.string().min(1)),
  end_time: z.string().datetime({ message: 'Invalid ISO end time' }).or(z.string().min(1)),
});

export const updateStepsSchema = z.object({
  steps: z.number().min(0, 'Steps must be a non-negative integer'),
});

export const createMetricSchema = z.object({
  metric_type: z.enum(['weight', 'blood_pressure', 'glucose']),
  value_primary: z.number().positive('Primary value must be positive'),
  value_secondary: z.number().positive().optional(),
  measured_at: z.string().optional(),
});

export const createMedicationSchema = z.object({
  name: z.string().min(1, 'Medication name is required'),
  dosage: z.string().optional(),
  schedule_time: z.string().regex(/^([0-1]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/, 'Invalid time format (HH:MM or HH:MM:SS)'),
});

export const checkMedicationSchema = z.object({
  status: z.enum(['taken', 'skipped', 'pending']),
});
