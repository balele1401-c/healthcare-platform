"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.AuthController = void 0;
const auth_service_js_1 = require("../services/auth.service.js");
const response_js_1 = require("../utils/response.js");
class AuthController {
    static async register(req, res) {
        try {
            const result = await auth_service_js_1.AuthService.register(req.body);
            return (0, response_js_1.sendSuccess)(res, result, 'User registered successfully', 201);
        }
        catch (error) {
            return (0, response_js_1.sendError)(res, error.message || 'Registration failed', 400);
        }
    }
    static async login(req, res) {
        try {
            const result = await auth_service_js_1.AuthService.login(req.body);
            return (0, response_js_1.sendSuccess)(res, result, 'Login successful', 200);
        }
        catch (error) {
            return (0, response_js_1.sendError)(res, error.message || 'Login failed', 401);
        }
    }
}
exports.AuthController = AuthController;
