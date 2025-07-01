<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminPrivilegeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!$request->user()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Please login first.',
            ], 401);
        }

        $user = $request->user();

        // Check if user has admin privileges (admin or HRD)
        if (!$user->hasAdminPrivileges()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Forbidden. Admin or HRD access required.',
                'user_role' => $user->role,
                'required_privileges' => 'admin or hrd',
            ], 403);
        }

        // Skip status check since tb_user table doesn't have status field
        // All authenticated users are considered active
        
        return $next($request);
    }
}
