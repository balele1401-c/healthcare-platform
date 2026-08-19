import { Request, Response, NextFunction } from 'express';
import { sendError } from '../utils/response.js';

export const errorHandler = (
  err: Error,
  _req: Request,
  res: Response,
  _next: NextFunction
): Response => {
  console.error('[Error Middleware]:', err.stack || err.message);
  return sendError(
    res,
    process.env.NODE_ENV === 'production'
      ? 'Internal server error'
      : err.message || 'Internal server error',
    500
  );
};

export const notFoundHandler = (_req: Request, res: Response): Response => {
  return sendError(res, 'Route or resource not found', 404);
};
