<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\Customer;
use App\Models\Quotation;
use Illuminate\Http\Request;

class DataCapturerController extends Controller
{
    /**
     * Data Capturer Dashboard
     */
    public function dashboard()
    {
        // Get statistics
        $stats = [
            'total_requests' => ServiceRequest::count(),
            'today_requests' => ServiceRequest::whereDate('created_at', today())->count(),
            'pending_review' => ServiceRequest::where('status', 'submitted')->count(),
            'total_customers' => Customer::count(),
        ];

        // Get recent service requests
        $recentRequests = ServiceRequest::with('customer', 'machine')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('data-capturer.dashboard', [
            'stats' => $stats,
            'recentRequests' => $recentRequests
        ]);
    }
}
