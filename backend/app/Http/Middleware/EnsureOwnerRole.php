<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOwnerRole
{
    /**
     * Handle an incoming request for Owner Portal & Executive Reporting.
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

        if ($request->user()->role !== UserRole::OWNER && $request->user()->role !== UserRole::ADMIN) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access forbidden. Owner or Executive privileges required.',
                ], 403);
            }

            abort(403, 'Access denied. Owner privileges required.');
        }

        return $next($request);
    }
}
