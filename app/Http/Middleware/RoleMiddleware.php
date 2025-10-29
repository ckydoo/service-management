<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Managers bypass all role restrictions and see everything.
     * Other roles must have the required role.
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $userRole = $user->role ?? 'guest';

        // MANAGER ALWAYS HAS ACCESS TO EVERYTHING
        if ($userRole === 'manager') {
            return $next($request);
        }

        // If no roles specified, allow all authenticated users
        if (empty($roles)) {
            return $next($request);
        }

        // Check if user has required role
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // User doesn't have required role - deny access
        abort(403, "Access denied. Required role(s): " . implode(', ', $roles) . ". Your role: {$userRole}");
    }
}
