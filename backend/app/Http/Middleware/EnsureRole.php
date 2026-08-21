<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request for role-based authorization.
     *
     * @param Request $request
     * @param Closure $next
     * @param string ...$roles
     * @return Response
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated access.',
                ], 401);
            }

            return redirect()->guest(route('login'));
        }

        $userRoleValue = is_object($user->role) ? $user->role->value : (string) $user->role;

        if (! in_array($userRoleValue, $roles, true)) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access forbidden. You do not have permission for this resource.',
                ], 403);
            }

            abort(403, 'Access denied. You do not have permission to access this portal.');
        }

        return $next($request);
    }
}
