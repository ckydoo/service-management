<?php

if (!function_exists('user_route')) {
    /**
     * Generate a route URL based on the authenticated user's role
     */
    function user_route($route, $parameters = [])
    {
        $user = auth()->user();

        if (!$user) {
            return route('login');
        }

        // Get role prefix from current URL or user's role
        $rolePrefix = request()->segment(1); // Gets 'manager', 'technician', etc. from URL

        // Validate it's a valid role prefix
        $validPrefixes = ['manager', 'costing-officer', 'data-capturer', 'technician'];

        if (!in_array($rolePrefix, $validPrefixes)) {
            // Fallback: determine from user's role column
            $rolePrefix = match($user->role) {
                'manager' => 'manager',
                'costing_officer' => 'costing-officer',
                'data_capturer' => 'data-capturer',
                'technician' => 'technician',
                default => 'manager'
            };
        }

        return route("{$rolePrefix}.{$route}", $parameters);
    }
}
