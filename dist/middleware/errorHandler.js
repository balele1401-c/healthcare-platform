"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.notFoundHandler = exports.errorHandler = void 0;
const response_js_1 = require("../utils/response.js");
const errorHandler = (err, _req, res, _next) => {
    console.error('[Error Middleware]:', err.stack || err.message);
    return (0, response_js_1.sendError)(res, process.env.NODE_ENV === 'production'
        ? 'Internal server error'
        : err.message || 'Internal server error', 500);
};
exports.errorHandler = errorHandler;
const notFoundHandler = (_req, res) => {
    return (0, response_js_1.sendError)(res, 'Route or resource not found', 404);
};
exports.notFoundHandler = notFoundHandler;
