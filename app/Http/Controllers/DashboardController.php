<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\JobCard;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Manager's live dashboard
     */
    public function managerDashboard()
    {
        $activeJobs = JobCard::whereIn('status', ['pending', 'in_progress'])->count();

        $completedToday = JobCard::where('status', 'completed')
            ->whereDate('updated_at', today())
            ->count();

        // FIX: Use payment_status instead of status
        // The invoices table has payment_status, not status
        $pendingPayments = Invoice::whereIn('payment_status', ['pending', 'proof_uploaded'])
            ->count();

        // FIX: Use total_amount instead of amount
        $totalRevenue = Invoice::where('payment_status', 'verified')
            ->sum('total_amount');

        $jobsData = [
            'active' => $activeJobs,
            'completed_today' => $completedToday,
            'pending_payments' => $pendingPayments,
            'revenue' => $totalRevenue
        ];

        $recentJobs = JobCard::with('technician.user', 'serviceRequest.customer')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        return view('manager.dashboard', [
            'data' => $jobsData,
            'recentJobs' => $recentJobs
        ]);
    }
}
