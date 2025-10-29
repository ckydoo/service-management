<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\JobCard;
use Illuminate\Http\Request;
use App\Models\ServiceReport;
use App\Models\JobStatusUpdate;

// app/Http/Controllers/JobCardController.php
class JobCardController extends Controller
{
    // Get technician's dashboard
    public function technicianDashboard()
    {
        $user = auth()->user();
        $technician = $user->technician;

        $activeJobs = JobCard::where('technician_id', $technician->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->with('serviceRequest.customer', 'statusUpdates')
            ->get();

        return view('technician.dashboard', ['jobs' => $activeJobs]);
    }

    // Update job status in real-time
    public function updateStatus(Request $request, $id)
    {
        $jobCard = JobCard::findOrFail($id);

        JobStatusUpdate::create([
            'job_card_id' => $jobCard->id,
            'status' => $request->status,
            'location_lat' => $request->latitude ?? null,
            'location_lng' => $request->longitude ?? null,
            'notes' => $request->notes ?? null
        ]);

        $jobCard->update(['status' => $request->status]);

        return response()->json(['success' => true]);
    }

    // Submit service report
    public function submitReport(Request $request, $id)
    {
        $jobCard = JobCard::findOrFail($id);

        ServiceReport::create([
            'job_card_id' => $jobCard->id,
            'technician_id' => $jobCard->technician_id,
            'work_completed' => $request->work_completed,
            'parts_used' => $request->parts_used,
            'labor_hours' => $request->labor_hours,
            'additional_notes' => $request->notes
        ]);

        $jobCard->update(['status' => 'completed']);

        // Generate invoice
        $this->generateInvoice($jobCard);

        return response()->json(['success' => true]);
    }

    private function generateInvoice($jobCard)
    {
        $serviceRequest = $jobCard->serviceRequest;
        $quotation = $serviceRequest->quotation;

        $invoice = Invoice::create([
            'job_card_id' => $jobCard->id,
            'service_request_id' => $serviceRequest->id,
            'customer_id' => $serviceRequest->customer_id,
            'subtotal' => $quotation->total_cost,
            'tax' => $quotation->total_cost * 0.15,
            'total_amount' => $quotation->total_cost * 1.15,
            'payment_status' => 'pending'
        ]);

        return $invoice;
    }
}
