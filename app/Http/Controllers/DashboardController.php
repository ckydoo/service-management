<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\JobCard;
use Illuminate\Http\Request;
// app/Http/Controllers/DashboardController.php
class DashboardController extends Controller
{
    // Manager's live dashboard
    public function managerDashboard()
    {
        $activeJobs = JobCard::whereIn('status', ['pending', 'in_progress'])->count();
        $completedToday = JobCard::where('status', 'completed')
            ->whereDate('updated_at', today())->count();
        $pendingPayments = Invoice::where('payment_status', 'pending')->count();
        $totalRevenue = Invoice::where('payment_status', 'verified')->sum('total_amount');

        $jobsData = [
            'active' => $activeJobs,
            'completed_today' => $completedToday,
            'pending_payments' => $pendingPayments,
            'revenue' => $totalRevenue
        ];

        $recentJobs = JobCard::with('technician', 'serviceRequest.customer')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        return view('manager.dashboard', ['data' => $jobsData, 'recentJobs' => $recentJobs]);
    }
}
