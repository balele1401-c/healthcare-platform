"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
const express_1 = require("express");
const analytics_controller_js_1 = require("../controllers/analytics.controller.js");
const auth_js_1 = require("../middleware/auth.js");
const router = (0, express_1.Router)();
router.get('/weekly', auth_js_1.authenticateToken, analytics_controller_js_1.AnalyticsController.getWeeklySummary);
exports.default = router;
