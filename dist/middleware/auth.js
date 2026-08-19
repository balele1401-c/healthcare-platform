"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.authenticateToken = void 0;
const jsonwebtoken_1 = __importDefault(require("jsonwebtoken"));
const env_js_1 = require("../config/env.js");
const response_js_1 = require("../utils/response.js");
const authenticateToken = (req, res, next) => {
    const authHeader = req.headers['authorization'];
    const token = authHeader && authHeader.split(' ')[1];
    if (!token) {
        return (0, response_js_1.sendError)(res, 'Access token required', 401);
    }
    try {
        const decoded = jsonwebtoken_1.default.verify(token, env_js_1.config.jwtSecret);
        req.user = decoded;
        next();
    }
    catch (error) {
        return (0, response_js_1.sendError)(res, 'Invalid or expired token', 403);
    }
};
exports.authenticateToken = authenticateToken;
