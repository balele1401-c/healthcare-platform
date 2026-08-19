<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminRole
{
    /**
     * Handle an incoming request for Admin Portal.
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

            return redirect()->guest(route('admin.login'));
        }

        if ($request->user()->role !== UserRole::ADMIN) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access forbidden. Administrator privileges required.',
                ], 403);
            }

            abort(403, 'Access denied. You must be an Administrator to access the Admin Management Portal.');
        }

        return $next($request);
    }
}
