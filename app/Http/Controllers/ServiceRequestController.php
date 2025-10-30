<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\Quotation;
use App\Models\PricingTemplate;
use App\Models\Customer;
use App\Models\Machine;
use Illuminate\Http\Request;

class ServiceRequestController extends Controller
{
    // ============================================================
    // MANAGER METHODS
    // ============================================================

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
     * Show service request details (Manager view)
     */
    public function show($id)
    {
        $request = ServiceRequest::with('customer', 'machine', 'quotation', 'jobCard')
            ->findOrFail($id);

        return view('service-requests.show', ['request' => $request]);
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

    // ============================================================
    // DATA CAPTURER METHODS - FIXED ROUTES!
    // ============================================================

    /**
     * List service requests (Data Capturer view)
     */
    public function capturerIndex()
    {
        $requests = ServiceRequest::with('customer', 'machine', 'quotation')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('service-requests.index', ['requests' => $requests]);
    }

    /**
     * Show create service request form (Data Capturer)
     */
    public function capturerCreate()
    {
        $customers = Customer::with('user')->get();
        $machines = Machine::all();

        return view('service-requests.create', [
            'customers' => $customers,
            'machines' => $machines,
        ]);
    }

    /**
     * Create a new service request (Data Capturer on behalf of customer)
     */
    public function capturerStore(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'machine_id' => 'nullable|exists:machines,id',
            'request_description' => 'required|string|max:2000',
            'request_type' => 'required|in:breakdown,maintenance,installation',
            'requires_assessment' => 'sometimes|boolean',
        ]);

        $referenceNumber = 'SR-' . date('YmdHis') . '-' . rand(1000, 9999);

        $serviceRequest = ServiceRequest::create([
            'reference_number' => $referenceNumber,
            'customer_id' => $validated['customer_id'],
            'machine_id' => $validated['machine_id'],
            'request_description' => $validated['request_description'],
            'request_type' => $validated['request_type'],
            'requires_assessment' => $validated['requires_assessment'] ?? false,
            'status' => 'pending_review', // Data capturer creates as pending review
        ]);

        // FIXED: Use data-capturer specific route instead of generic route
        return redirect()->route('data-capturer.service-requests.show', $serviceRequest->id)
            ->with('success', 'Service request created successfully. Reference: ' . $referenceNumber);
    }

    /**
     * Show service request details (Data Capturer view)
     */
    public function capturerShow($id)
    {
        $request = ServiceRequest::with('customer', 'machine', 'quotation', 'jobCard')
            ->findOrFail($id);

        return view('service-requests.show', ['request' => $request]);
    }

    /**
     * Show edit service request form (Data Capturer)
     */
    public function capturerEdit($id)
    {
        $request = ServiceRequest::findOrFail($id);
        $customers = Customer::with('user')->get();
        $machines = Machine::all();

        return view('service-requests.edit', [
            'request' => $request,
            'customers' => $customers,
            'machines' => $machines,
        ]);
    }

    /**
     * Update service request (Data Capturer)
     */
    public function capturerUpdate(Request $request, $id)
    {
        $serviceRequest = ServiceRequest::findOrFail($id);

        // Only allow editing if status is pending_review
        if ($serviceRequest->status !== 'pending_review') {
            return back()->with('error', 'Cannot edit service requests that have been reviewed');
        }

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'machine_id' => 'nullable|exists:machines,id',
            'request_description' => 'required|string|max:2000',
            'request_type' => 'required|in:breakdown,maintenance,installation',
            'requires_assessment' => 'sometimes|boolean',
        ]);

        $serviceRequest->update($validated);

        // FIXED: Use data-capturer specific route
        return redirect()->route('data-capturer.service-requests.show', $serviceRequest->id)
            ->with('success', 'Service request updated successfully');
    }

    // ============================================================
    // CUSTOMER METHODS
    // ============================================================

    /**
     * List service requests (Customer view - their own only)
     */
    public function customerIndex()
    {
        $user = auth()->user();
        $customer = $user->customer;

        $requests = ServiceRequest::with('customer', 'machine', 'quotation')
            ->where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('service-requests.customer-index', ['requests' => $requests]);
    }

    /**
     * Show create service request form (Customer)
     */
    public function create()
    {
        $user = auth()->user();
        $customer = $user->customer;

        $machines = Machine::where('customer_id', $customer->id)->get();

        return view('service-requests.create', ['machines' => $machines]);
    }

    /**
     * Create a new service request (Customer submits their own)
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $customer = $user->customer;

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


    // ============================================================
    // HELPER METHODS
    // ============================================================

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
 * Show service request details (Customer view)
 */
public function customerShow($id)
{
    $user = auth()->user();
    $customer = $user->customer;

    if (!$customer) {
        abort(403, 'You must have a customer profile to view requests');
    }

    // Ensure customer can only view their own requests
    $request = ServiceRequest::with('customer', 'machine', 'quotation', 'jobCard')
        ->where('customer_id', $customer->id)
        ->findOrFail($id);

    return view('service-requests.customer-show', ['request' => $request]);
}
}
