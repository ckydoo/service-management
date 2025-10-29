<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\Quotation;
use App\Models\Customer;
use Illuminate\Http\Request;

class DataCapturerController extends Controller
{
    /**
     * Data Capturer Dashboard
     */
    public function dashboard()
    {
        $totalRequests = ServiceRequest::count();
        $todayRequests = ServiceRequest::whereDate('created_at', today())->count();
        $pendingReview = ServiceRequest::where('status', 'pending_review')->count();
        $recentRequests = ServiceRequest::with('customer')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $stats = [
            'total_requests' => $totalRequests,
            'today_requests' => $todayRequests,
            'pending_review' => $pendingReview,
        ];

        return view('data-capturer.dashboard', [
            'stats' => $stats,
            'recentRequests' => $recentRequests,
        ]);
    }
}
