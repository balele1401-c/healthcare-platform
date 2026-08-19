"use strict";
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
const express_1 = __importDefault(require("express"));
const cors_1 = __importDefault(require("cors"));
const env_js_1 = require("./config/env.js");
const errorHandler_js_1 = require("./middleware/errorHandler.js");
const response_js_1 = require("./utils/response.js");
const migrate_js_1 = require("./db/migrate.js");
const auth_routes_js_1 = __importDefault(require("./routes/auth.routes.js"));
const dailyLogs_routes_js_1 = __importDefault(require("./routes/dailyLogs.routes.js"));
const healthMetrics_routes_js_1 = __importDefault(require("./routes/healthMetrics.routes.js"));
const medications_routes_js_1 = __importDefault(require("./routes/medications.routes.js"));
const analytics_routes_js_1 = __importDefault(require("./routes/analytics.routes.js"));
const app = (0, express_1.default)();
// Middleware
app.use((0, cors_1.default)());
app.use(express_1.default.json());
app.use(express_1.default.urlencoded({ extended: true }));
// Health Check
app.get('/health', (_req, res) => {
    (0, response_js_1.sendSuccess)(res, { status: 'healthy', timestamp: new Date().toISOString() }, 'System is healthy');
});
// Run DB Migrations automatically on startup
(0, migrate_js_1.runMigrations)();
// Routes
app.use('/api/auth', auth_routes_js_1.default);
app.use('/api/logs', dailyLogs_routes_js_1.default);
app.use('/api/metrics', healthMetrics_routes_js_1.default);
app.use('/api/medications', medications_routes_js_1.default);
app.use('/api/analytics', analytics_routes_js_1.default);
// 404 & Error handlers
app.use(errorHandler_js_1.notFoundHandler);
app.use(errorHandler_js_1.errorHandler);
if (process.env.NODE_ENV !== 'test') {
    app.listen(env_js_1.config.port, () => {
        console.log(`🚀 PulseTrack Server running on http://localhost:${env_js_1.config.port}`);
    });
}
exports.default = app;
