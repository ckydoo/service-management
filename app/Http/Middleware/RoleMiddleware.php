<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Check if user has required role
        if (!in_array(auth()->user()->role, $roles)) {
            abort(403, 'Unauthorized - insufficient permissions');
        }

        return $next($request);
    }
}
