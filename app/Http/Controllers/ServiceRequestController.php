<?php

// app/Http/Controllers/ServiceRequestController.php
namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\Quotation;
use App\Models\PricingTemplate;
use Illuminate\Http\Request;

class ServiceRequestController extends Controller
{
    // Create a new service request
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'machine_id' => 'nullable|exists:machines,id',
            'request_description' => 'required|string',
            'request_type' => 'required|in:breakdown,maintenance,installation',
        ]);

        $referenceNumber = 'SR-' . date('YmdHis') . '-' . rand(1000, 9999);

        $serviceRequest = ServiceRequest::create([
            'reference_number' => $referenceNumber,
            'customer_id' => $validated['customer_id'],
            'machine_id' => $validated['machine_id'],
            'request_description' => $validated['request_description'],
            'request_type' => $validated['request_type'],
            'requires_assessment' => $request->requires_assessment ?? false,
            'status' => 'submitted',
        ]);

        // Check if quick quotation is possible
        if (!$serviceRequest->requires_assessment) {
            $this->generateQuotation($serviceRequest);
        }

        return response()->json([
            'success' => true,
            'reference_number' => $referenceNumber,
            'message' => 'Service request submitted successfully'
        ]);
    }

    // Generate quotation based on pricing templates
    private function generateQuotation($serviceRequest)
    {
        $template = PricingTemplate::where('service_type', $serviceRequest->request_type)
            ->where('is_active', true)->first();

        if (!$template) {
            return null;
        }

        $laborCost = $template->labor_cost_per_hour * 2; // Assume 2 hours base
        $partsCost = 0; // Would be calculated based on parts needed

        $totalCost = $laborCost + $partsCost;

        $quotation = Quotation::create([
            'service_request_id' => $serviceRequest->id,
            'labor_cost' => $laborCost,
            'parts_cost' => $partsCost,
            'total_cost' => $totalCost,
            'status' => 'pending'
        ]);

        return $quotation;
    }

    // List all service requests
    public function index()
    {
        $requests = ServiceRequest::with('customer', 'machine', 'quotation')
            ->paginate(15);

        return view('service-requests.index', ['requests' => $requests]);
    }

    // Show details of a specific request
    public function show($id)
    {
        $request = ServiceRequest::with('customer', 'machine', 'quotation', 'jobCard')
            ->findOrFail($id);

        return view('service-requests.show', ['request' => $request]);
    }

    // Update service request status
    public function updateStatus(Request $request, $id)
    {
        $serviceRequest = ServiceRequest::findOrFail($id);
        $serviceRequest->update(['status' => $request->status]);

        return response()->json(['success' => true]);
    }
}






