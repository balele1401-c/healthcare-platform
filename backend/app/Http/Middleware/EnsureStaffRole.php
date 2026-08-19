<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStaffRole
{
    /**
     * Handle an incoming request for Staff Operational Portal.
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

            return redirect()->guest(route('staff.login'));
        }

        if ($request->user()->role !== UserRole::STAFF || ! $request->user()->staff) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access forbidden. Staff operational credentials required.',
                ], 403);
            }

            abort(403, 'Access denied. You must be an authorized Clinical Staff member to access the operational workspace.');
        }

        return $next($request);
    }
}
