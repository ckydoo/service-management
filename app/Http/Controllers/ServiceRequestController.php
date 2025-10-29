<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\Quotation;
use App\Models\PricingTemplate;
use App\Models\Customer;
use Illuminate\Http\Request;

class ServiceRequestController extends Controller
{
    /**
     * List all service requests (Manager view)
     */
    public function index()
    {
        $requests = ServiceRequest::with('customer', 'machine', 'quotation')
            ->paginate(15);

        return view('service-requests.index', ['requests' => $requests]);
    }

    /**
     * List customer's own service requests
     */
    public function customerIndex()
    {
        $user = auth()->user();
        $customer = $user->customer;

        if (!$customer) {
            // User has no associated customer record
            $requests = collect();
            $requests = new \Illuminate\Pagination\Paginator($requests, 15);
        } else {
            $requests = ServiceRequest::where('customer_id', $customer->id)
                ->with('machine', 'quotation', 'jobCard')
                ->paginate(15);
        }

        return view('service-requests.customer-index', ['requests' => $requests]);
    }

    /**
     * Show service request details (Manager view)
     */
    public function show($id)
    {
        $request = ServiceRequest::with('customer', 'machine', 'quotation', 'jobCard')
            ->findOrFail($id);

        return view('service-requests.show', ['request' => $request]);
    }

    /**
     * Show service request details (Customer view)
     */
    public function customerShow($id)
    {
        $user = auth()->user();
        $customer = $user->customer;

        $request = ServiceRequest::with('customer', 'machine', 'quotation', 'jobCard')
            ->findOrFail($id);

        // Authorize: customer can only see their own requests
        if ($request->customer_id !== $customer->id) {
            abort(403, 'Unauthorized - this request does not belong to you');
        }

        return view('service-requests.customer-show', ['request' => $request]);
    }

    /**
     * Show create service request form (Customer)
     */
    public function create()
    {
        return view('service-requests.create');
    }

    /**
     * Create a new service request (Customer)
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $customer = $user->customer;

        if (!$customer) {
            abort(403, 'You must have a customer profile to submit requests');
        }

        $validated = $request->validate([
            'machine_id' => 'nullable|exists:machines,id',
            'request_description' => 'required|string|max:2000',
            'request_type' => 'required|in:breakdown,maintenance,installation',
            'requires_assessment' => 'sometimes|boolean',
        ]);

        $referenceNumber = 'SR-' . date('YmdHis') . '-' . rand(1000, 9999);

        $serviceRequest = ServiceRequest::create([
            'reference_number' => $referenceNumber,
            'customer_id' => $customer->id,
            'machine_id' => $validated['machine_id'],
            'request_description' => $validated['request_description'],
            'request_type' => $validated['request_type'],
            'requires_assessment' => $validated['requires_assessment'] ?? false,
            'status' => 'submitted',
        ]);

        // Check if quick quotation is possible
        if (!$serviceRequest->requires_assessment) {
            $this->generateQuotation($serviceRequest);
        }

        return redirect()->route('service-requests.show', $serviceRequest->id)
            ->with('success', 'Service request submitted successfully. Reference: ' . $referenceNumber);
    }

    /**
     * Generate quotation based on pricing templates
     */
    private function generateQuotation($serviceRequest)
    {
        $template = PricingTemplate::where('service_type', $serviceRequest->request_type)
            ->where('is_active', true)
            ->first();

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

    /**
     * Update service request status (Manager)
     */
    public function updateStatus(Request $request, $id)
    {
        $serviceRequest = ServiceRequest::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:submitted,assessed,assigned,in_progress,completed,cancelled'
        ]);

        $serviceRequest->update(['status' => $validated['status']]);

        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }
}