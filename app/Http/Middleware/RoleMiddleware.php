<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Simple Role-Based Access Control
     *
     * No hierarchy confusion - each role can only access their specific routes
     * Admin can optionally access all routes if needed
     */
    protected array $roles = [
        'admin',
        'manager',
        'costing_officer',
        'data_capturer',
        'technician',
        'customer',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $userRole = auth()->user()->role;

        // Admin has access to everything (optional, remove if not needed)
        if ($userRole === 'admin') {
            return $next($request);
        }

        // Simple check: does user's role match any of the allowed roles?
        if (in_array($userRole, $roles, true)) {
            return $next($request);
        }

        // Access denied
        abort(403, 'Unauthorized. Your role: ' . $userRole . ' cannot access this resource.');
    }
}
