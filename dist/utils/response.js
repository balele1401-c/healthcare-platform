"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.sendError = exports.sendSuccess = void 0;
const sendSuccess = (res, data, message = 'Success', statusCode = 200) => {
    const response = {
        success: true,
        message,
        data,
    };
    return res.status(statusCode).json(response);
};
exports.sendSuccess = sendSuccess;
const sendError = (res, message = 'An error occurred', statusCode = 400, errors = null) => {
    const response = {
        success: false,
        message,
        ...(errors && { errors }),
    };
    return res.status(statusCode).json(response);
};
exports.sendError = sendError;
