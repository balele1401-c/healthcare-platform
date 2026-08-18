"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.checkMedicationSchema = exports.createMedicationSchema = exports.createMetricSchema = exports.updateStepsSchema = exports.updateSleepSchema = exports.updateWaterSchema = exports.loginSchema = exports.registerSchema = void 0;
const zod_1 = require("zod");
exports.registerSchema = zod_1.z.object({
    name: zod_1.z.string().min(2, 'Name must be at least 2 characters'),
    email: zod_1.z.string().email('Invalid email address'),
    password: zod_1.z.string().min(6, 'Password must be at least 6 characters'),
    birth_date: zod_1.z.string().optional(),
    height_cm: zod_1.z.number().positive().optional(),
    water_goal_ml: zod_1.z.number().positive().optional(),
    sleep_goal_hours: zod_1.z.number().positive().optional(),
});
exports.loginSchema = zod_1.z.object({
    email: zod_1.z.string().email('Invalid email address'),
    password: zod_1.z.string().min(1, 'Password is required'),
});
exports.updateWaterSchema = zod_1.z.object({
    delta_ml: zod_1.z.number().int('Delta must be an integer'),
});
exports.updateSleepSchema = zod_1.z.object({
    start_time: zod_1.z.string().datetime({ message: 'Invalid ISO start time' }).or(zod_1.z.string().min(1)),
    end_time: zod_1.z.string().datetime({ message: 'Invalid ISO end time' }).or(zod_1.z.string().min(1)),
});
exports.updateStepsSchema = zod_1.z.object({
    steps: zod_1.z.number().min(0, 'Steps must be a non-negative integer'),
});
exports.createMetricSchema = zod_1.z.object({
    metric_type: zod_1.z.enum(['weight', 'blood_pressure', 'glucose']),
    value_primary: zod_1.z.number().positive('Primary value must be positive'),
    value_secondary: zod_1.z.number().positive().optional(),
    measured_at: zod_1.z.string().optional(),
});
exports.createMedicationSchema = zod_1.z.object({
    name: zod_1.z.string().min(1, 'Medication name is required'),
    dosage: zod_1.z.string().optional(),
    schedule_time: zod_1.z.string().regex(/^([0-1]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/, 'Invalid time format (HH:MM or HH:MM:SS)'),
});
exports.checkMedicationSchema = zod_1.z.object({
    status: zod_1.z.enum(['taken', 'skipped', 'pending']),
});
