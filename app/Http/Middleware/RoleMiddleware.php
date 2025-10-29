<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Role Hierarchy
     *
     * Higher numbers mean more privileges
     * Admin can access everything, customer has least access
     *
     * This is flexible - you can adjust based on your needs
     */
    protected array $roleHierarchy = [
        'admin'          => 100,  // Can access everything
        'manager'        => 80,   // Can access manager routes and some shared
        'costing_officer' => 60,  // Can access costing officer specific routes
        'data_capturer'  => 50,   // Can access data capturer specific routes
        'technician'     => 40,   // Can access technician specific routes
        'customer'       => 30,   // Can access customer specific routes (lowest)
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
        $userLevel = $this->roleHierarchy[$userRole] ?? 0;

        // Option 1: Strict matching (exact role required)
        // Uncomment below and comment out Option 2 if you want strict role matching
        /*
        if (!in_array($userRole, $roles)) {
            abort(403, 'Access denied. Required role(s): ' . implode(', ', $roles) . '. Your role: ' . $userRole);
        }
        return $next($request);
        */

        // Option 2: Hierarchy matching (user with higher role can access lower roles' routes)
        // This is FLEXIBLE - allows role hierarchy
        foreach ($roles as $role) {
            $requiredLevel = $this->roleHierarchy[$role] ?? 0;

            // User's role matches OR user has higher privilege level
            if ($userRole === $role || $userLevel >= $requiredLevel) {
                return $next($request);
            }
        }

        // If we get here, user is not authorized
        abort(403, 'Access denied. Required role(s): ' . implode(', ', $roles) . '. Your role: ' . $userRole);
    }

    /**
     * Check if user has a specific role
     *
     * @param string $role
     * @return bool
     */
    public function userHasRole(string $role): bool
    {
        return auth()->check() && auth()->user()->role === $role;
    }

    /**
     * Check if user's role level is at least the required level
     *
     * @param string $requiredRole
     * @return bool
     */
    public function userHasMinimumRole(string $requiredRole): bool
    {
        if (!auth()->check()) {
            return false;
        }

        $userLevel = $this->roleHierarchy[auth()->user()->role] ?? 0;
        $requiredLevel = $this->roleHierarchy[$requiredRole] ?? 0;

        return $userLevel >= $requiredLevel;
    }

    /**
     * Get all roles that a user can access
     *
     * @param string $userRole
     * @return array
     */
    public function getAccessibleRoles(string $userRole): array
    {
        $userLevel = $this->roleHierarchy[$userRole] ?? 0;
        $accessible = [];

        foreach ($this->roleHierarchy as $role => $level) {
            if ($userLevel >= $level || $userRole === $role) {
                $accessible[] = $role;
            }
        }

        return $accessible;
    }

    /**
     * Get role hierarchy (for admin settings)
     *
     * @return array
     */
    public function getRoleHierarchy(): array
    {
        return $this->roleHierarchy;
    }
}
