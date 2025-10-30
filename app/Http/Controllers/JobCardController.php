<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\JobCard;
use Illuminate\Http\Request;
use App\Models\ServiceReport;
use App\Models\JobStatusUpdate;

class JobCardController extends Controller
{
    /**
     * Display all job cards (Manager only)
     */
    public function index()
    {
        $jobCards = JobCard::with('technician.user', 'serviceRequest.customer')
            ->paginate(15);

        return view('job-cards.index', ['jobCards' => $jobCards]);
    }

    /**
     * Display a specific job card
     */
    public function show($id)
    {
        $jobCard = JobCard::with('technician.user', 'serviceRequest.customer', 'statusUpdates', 'serviceReport')
            ->findOrFail($id);

        return view('job-cards.show', ['jobCard' => $jobCard]);
    }

    /**
     * Update job card (Manager)
     */
    public function update(Request $request, $id)
    {
        $jobCard = JobCard::findOrFail($id);

        $validated = $request->validate([
            'status' => 'sometimes|in:pending,in_progress,completed,cancelled',
            'estimated_duration' => 'sometimes|numeric|min:1',
            'notes' => 'sometimes|string|max:1000',
        ]);

        $jobCard->update($validated);

        return redirect()->route('job-cards.show', $jobCard->id)
            ->with('success', 'Job card updated successfully');
    }

    /**
     * Get technician's dashboard
     */
    public function technicianDashboard()
    {
        $user = auth()->user();
        $technician = $user->technician;

        if (!$technician) {
            abort(403, 'Not a technician');
        }

        $activeJobs = JobCard::where('technician_id', $technician->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->with('serviceRequest.customer', 'statusUpdates')
            ->get();

        $completedToday = JobCard::where('technician_id', $technician->id)
            ->where('status', 'completed')
            ->whereDate('updated_at', today())
            ->count();

        return view('technicians.dashboard', [
            'jobs' => $activeJobs,
            'completedToday' => $completedToday
        ]);
    }

    /**
     * Update job status in real-time
     */
    public function updateStatus(Request $request, $id)
    {
        $jobCard = JobCard::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'notes' => 'nullable|string|max:1000',
        ]);

        JobStatusUpdate::create([
            'job_card_id' => $jobCard->id,
            'status' => $validated['status'],
            'location_lat' => $validated['latitude'] ?? null,
            'location_lng' => $validated['longitude'] ?? null,
            'notes' => $validated['notes'] ?? null
        ]);

        $jobCard->update(['status' => $validated['status']]);

        return response()->json(['success' => true, 'message' => 'Status updated']);
    }

   /**
 * Submit service report
 *
 * Replace the existing submitReport() method in JobCardController
 */
public function submitReport(Request $request, $id)
{
    // FIX: Eager load the serviceRequest relationship
    $jobCard = JobCard::with('serviceRequest')->findOrFail($id);

    $validated = $request->validate([
        'work_completed' => 'required|string|max:2000',
        'parts_used' => 'nullable|string|max:1000',
        'labor_hours' => 'required|numeric|min:0.5|max:24',
        'notes' => 'nullable|string|max:1000',
    ]);

    ServiceReport::create([
        'job_card_id' => $jobCard->id,
        'technician_id' => $jobCard->technician_id,
        'work_completed' => $validated['work_completed'],
        'parts_used' => $validated['parts_used'],
        'labor_hours' => $validated['labor_hours'],
        'additional_notes' => $validated['notes']
    ]);

    $jobCard->update(['status' => 'completed']);

    // Generate invoice
    $this->generateInvoice($jobCard);

    return response()->json(['success' => true, 'message' => 'Service report submitted']);
}

/**
 * Generate invoice after job completion
 *
 * Replace the existing generateInvoice() method in JobCardController
 */
private function generateInvoice($jobCard)
{
    // Ensure relationships are loaded
    if (!$jobCard->relationLoaded('serviceRequest')) {
        $jobCard->load('serviceRequest.quotation');
    } else if (!$jobCard->serviceRequest->relationLoaded('quotation')) {
        $jobCard->serviceRequest->load('quotation');
    }

    $serviceRequest = $jobCard->serviceRequest;
    $quotation = $serviceRequest->quotation;

    if (!$quotation) {
        return null;
    }

    // Check if invoice already exists for this job card
    $existingInvoice = Invoice::where('job_card_id', $jobCard->id)->first();
    if ($existingInvoice) {
        return $existingInvoice;
    }

    // Generate unique invoice number
    $invoiceNumber = $this->generateInvoiceNumber();

    $invoice = Invoice::create([
        'invoice_number' => $invoiceNumber,
        'job_card_id' => $jobCard->id,
        'service_request_id' => $serviceRequest->id,
        'customer_id' => $serviceRequest->customer_id,  // Now this will work!
        'subtotal' => $quotation->total_cost,
        'tax' => $quotation->total_cost * 0.15,
        'total_amount' => $quotation->total_cost * 1.15,
        'payment_status' => 'pending'
    ]);

    return $invoice;
}

/**
 * Generate unique invoice number
 *
 * Add this helper method if it doesn't exist in JobCardController
 */
private function generateInvoiceNumber()
{
    $lastInvoice = Invoice::orderByDesc('id')->first();
    $nextNumber = ($lastInvoice ? intval(substr($lastInvoice->invoice_number, 4)) : 0) + 1;
    return 'INV-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
}
}
