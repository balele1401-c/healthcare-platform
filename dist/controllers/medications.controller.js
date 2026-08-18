"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.MedicationsController = void 0;
const medications_service_js_1 = require("../services/medications.service.js");
const response_js_1 = require("../utils/response.js");
class MedicationsController {
    static async getTodayMedications(req, res) {
        try {
            const userId = req.user.userId;
            const medications = medications_service_js_1.MedicationsService.getMedicationsForToday(userId);
            return (0, response_js_1.sendSuccess)(res, medications, 'Today medications fetched successfully');
        }
        catch (error) {
            return (0, response_js_1.sendError)(res, error.message || 'Failed to fetch medications', 400);
        }
    }
    static async createMedication(req, res) {
        try {
            const userId = req.user.userId;
            const medication = medications_service_js_1.MedicationsService.createMedication(userId, req.body);
            return (0, response_js_1.sendSuccess)(res, medication, 'Medication created successfully', 201);
        }
        catch (error) {
            return (0, response_js_1.sendError)(res, error.message || 'Failed to create medication', 400);
        }
    }
    static async checkMedication(req, res) {
        try {
            const userId = req.user.userId;
            const { id } = req.params;
            const { status } = req.body;
            const medicationId = Array.isArray(id) ? id[0] : id;
            const updated = medications_service_js_1.MedicationsService.checkMedication(userId, medicationId, status);
            return (0, response_js_1.sendSuccess)(res, updated, 'Medication status updated successfully');
        }
        catch (error) {
            return (0, response_js_1.sendError)(res, error.message || 'Failed to update medication status', 400);
        }
    }
}
exports.MedicationsController = MedicationsController;
