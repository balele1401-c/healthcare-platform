<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDoctorRole
{
    /**
     * Handle an incoming request for Doctor Portal.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated access.',
                ], 401);
            }

            return redirect()->guest(route('doctor.login'));
        }

        if ($request->user()->role !== UserRole::DOCTOR || ! $request->user()->doctor) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access forbidden. Doctor credentials required.',
                ], 403);
            }

            abort(403, 'Access denied. You must be an authorized Medical Doctor to access this clinical dashboard.');
        }

        return $next($request);
    }
}
