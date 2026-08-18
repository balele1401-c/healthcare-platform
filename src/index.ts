import express, { Express } from 'express';
import cors from 'cors';
import { config } from './config/env.js';
import { errorHandler, notFoundHandler } from './middleware/errorHandler.js';
import { sendSuccess } from './utils/response.js';
import { runMigrations } from './db/migrate.js';
import authRoutes from './routes/auth.routes.js';
import logsRoutes from './routes/dailyLogs.routes.js';
import metricsRoutes from './routes/healthMetrics.routes.js';
import medicationsRoutes from './routes/medications.routes.js';
import analyticsRoutes from './routes/analytics.routes.js';

const app: Express = express();

// Middleware
app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Health Check
app.get('/health', (_req, res) => {
  sendSuccess(res, { status: 'healthy', timestamp: new Date().toISOString() }, 'System is healthy');
});

// Run DB Migrations automatically on startup
runMigrations();

// Routes
app.use('/api/auth', authRoutes);
app.use('/api/logs', logsRoutes);
app.use('/api/metrics', metricsRoutes);
app.use('/api/medications', medicationsRoutes);
app.use('/api/analytics', analyticsRoutes);

// 404 & Error handlers
app.use(notFoundHandler);
app.use(errorHandler);

if (process.env.NODE_ENV !== 'test') {
  app.listen(config.port, () => {
    console.log(`🚀 PulseTrack Server running on http://localhost:${config.port}`);
  });
}

export default app;
