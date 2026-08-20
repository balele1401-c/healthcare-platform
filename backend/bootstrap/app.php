<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Attach security headers globally
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        // Prepare global and API middleware stack
        $middleware->statefulApi();
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('doctor*')) {
                return route('doctor.login');
            }
            if ($request->is('staff*')) {
                return route('staff.login');
            }
            return route('admin.login');
        });
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureAdminRole::class,
            'doctor' => \App\Http\Middleware\EnsureDoctorRole::class,
            'staff' => \App\Http\Middleware\EnsureStaffRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Standardized JSON API exception handling
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated access. Please provide a valid Bearer token.',
                ], 401);
            }
        });

        $exceptions->render(function (AuthorizationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access forbidden. You do not have permission for this clinical resource.',
                ], 403);
            }
        });

        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error occurred.',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resource or endpoint not found.',
                ], 404);
            }
        });

        $exceptions->render(function (TooManyRequestsHttpException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rate limit exceeded. Too many requests.',
                ], 429);
            }
        });

        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
                $status = ($status >= 400 && $status < 600) ? $status : 500;
                $isDebug = config('app.debug', false);

                $message = ($status === 500 && ! $isDebug)
                    ? 'Internal server error occurred.'
                    : $e->getMessage();

                return response()->json([
                    'success' => false,
                    'message' => $message ?: 'An unexpected server error occurred.',
                ], $status);
            }
        });
    })->create();
