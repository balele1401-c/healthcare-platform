"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.validate = void 0;
const response_js_1 = require("../utils/response.js");
const validate = (schema) => {
    return (req, res, next) => {
        const result = schema.safeParse(req.body);
        if (!result.success) {
            const formattedErrors = result.error.issues.map((err) => ({
                field: err.path.join('.'),
                message: err.message,
            }));
            return (0, response_js_1.sendError)(res, 'Validation failed', 400, formattedErrors);
        }
        req.body = result.data;
        next();
    };
};
exports.validate = validate;
