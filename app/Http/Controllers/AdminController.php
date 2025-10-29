<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ServiceRequest;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Admin Dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_requests' => ServiceRequest::count(),
            'users_by_role' => User::selectRaw('role, COUNT(*) as count')->groupBy('role')->get(),
            'recent_activity' => ActivityLog::with('user')
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get(),
        ];

        return view('admin.dashboard', $stats);
    }

    /**
     * List all users
     */
    public function listUsers()
    {
        $users = User::paginate(20);
        return view('admin.users.index', ['users' => $users]);
    }

    /**
     * Show specific user
     */
    public function showUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.show', ['user' => $user]);
    }

    /**
     * Edit user form
     */
    public function editUser($id)
    {
        $user = User::findOrFail($id);
        $roles = ['admin', 'manager', 'data_capturer', 'technician', 'customer', 'costing_officer'];
        return view('admin.users.edit', ['user' => $user, 'roles' => $roles]);
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:admin,manager,data_capturer,technician,customer,costing_officer',
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.show', $user->id)
            ->with('success', 'User updated successfully');
    }

    /**
     * View all reports
     */
    public function reports()
    {
        $requestsByStatus = ServiceRequest::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        $requestsByRole = ServiceRequest::selectRaw('role, COUNT(*) as count')
            ->groupBy('role')
            ->get();

        return view('admin.reports.index', [
            'requestsByStatus' => $requestsByStatus,
            'requestsByRole' => $requestsByRole,
        ]);
    }

    /**
     * View activity log
     */
    public function activityLog()
    {
        $logs = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('admin.reports.activity', ['logs' => $logs]);
    }

    /**
     * System settings
     */
    public function settings()
    {
        return view('admin.settings');
    }
}
