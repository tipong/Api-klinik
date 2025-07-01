<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Check if user is authenticated
        if (!$request->user()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Please login first.',
            ], 401);
        }

        $user = $request->user();

        // Check if user has required role
        if (!in_array($user->role, $roles)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Forbidden. You do not have permission to access this resource.',
                'required_roles' => $roles,
                'user_role' => $user->role,
            ], 403);
        }

        // Check if user is active
        if ($user->status !== 'aktif') {
            return response()->json([
                'status' => 'error',
                'message' => 'Your account is inactive. Please contact administrator.',
            ], 403);
        }

        return $next($request);
    }
}
