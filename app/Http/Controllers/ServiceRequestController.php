<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\Quotation;
use App\Models\PricingTemplate;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
                ->orderBy('created_at', 'desc')
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

        if (!$customer) {
            abort(403, 'You must have a customer profile to view requests');
        }

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
    /**
 * Show create service request form (Customer)
 */
public function create()
{
    $user = auth()->user();
    $customer = $user->customer;

    // Check if customer has completed profile
    if (!$customer) {
        return redirect()->route('service-requests.index')
            ->with('warning', 'Please complete your customer profile to create service requests.');
    }

    // Get customer's machines safely
    $machines = [];
    if (method_exists($customer, 'machines')) {
        $machines = $customer->machines()->get();
    }

    return view('service-requests.create', ['machines' => $machines]);
}

    /**
     * Create a new service request (Customer)
     *
     * Comprehensive validation for:
     * - Customer profile existence
     * - Machine ownership verification
     * - Required field validation
     * - Description quality
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $customer = $user->customer;

        // Verify customer profile exists
        if (!$customer) {
            return back()
                ->with('error', 'You must have a customer profile to submit requests. Please complete your profile first.')
                ->withInput();
        }

        // Validation rules
        $validated = $request->validate([
            'machine_id' => [
                'nullable',
                'exists:machines,id',
                // If machine_id is provided, verify it belongs to this customer
                function ($attribute, $value, $fail) use ($customer) {
                    if ($value) {
                        $machine = $customer->machines()->find($value);
                        if (!$machine) {
                            $fail('The selected machine does not belong to your account.');
                        }
                    }
                }
            ],
            'request_description' => [
                'required',
                'string',
                'min:10', // Minimum 10 characters for quality
                'max:2000'
            ],
            'request_type' => 'required|in:breakdown,maintenance,installation',
            'requires_assessment' => 'sometimes|boolean',
        ], [
            'request_description.min' => 'Please provide a more detailed description (at least 10 characters).',
            'request_description.required' => 'Description is required. Please tell us what you need.',
            'machine_id.exists' => 'The selected machine does not exist.',
            'request_type.required' => 'Please select a request type.',
            'request_type.in' => 'Invalid request type selected.',
        ]);

        try {
            // Generate unique reference number
            $referenceNumber = $this->generateReferenceNumber();

            // Create the service request
            $serviceRequest = ServiceRequest::create([
                'reference_number' => $referenceNumber,
                'customer_id' => $customer->id,
                'machine_id' => $validated['machine_id'] ?? null,
                'request_description' => $validated['request_description'],
                'request_type' => $validated['request_type'],
                'requires_assessment' => $validated['requires_assessment'] ?? false,
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            // Log the creation
            \Log::info("Service Request Created", [
                'reference_number' => $referenceNumber,
                'customer_id' => $customer->id,
                'request_type' => $validated['request_type'],
            ]);

            // Send notification email (if configured)
            $this->sendConfirmationEmail($serviceRequest);

            // Redirect with success message
            return redirect()->route('service-requests.show', $serviceRequest->id)
                ->with('success', "Service request {$referenceNumber} has been submitted successfully. We'll review it within 24 hours.");

        } catch (\Exception $e) {
            // Log the error
            \Log::error("Service Request Creation Failed", [
                'customer_id' => $customer->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->with('error', 'An error occurred while submitting your request. Please try again.')
                ->withInput();
        }
    }

    /**
     * Update service request status (Manager only)
     */
    public function updateStatus(Request $request, $id)
    {
        $serviceRequest = ServiceRequest::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:submitted,assessed,assigned,in_progress,completed,cancelled',
            'notes' => 'nullable|string|max:500'
        ]);

        $serviceRequest->update([
            'status' => $validated['status'],
        ]);

        return redirect()->route('service-requests.show', $serviceRequest->id)
            ->with('success', 'Service request status updated successfully.');
    }

    /**
     * Generate a unique reference number
     * Format: SR-YYYYMMDD-HHMMSS-XXXX
     */
    private function generateReferenceNumber(): string
    {
        $timestamp = now()->format('YmdHis');
        $random = strtoupper(Str::random(4));

        $referenceNumber = "SR-{$timestamp}-{$random}";

        // Ensure uniqueness (very unlikely but check anyway)
        while (ServiceRequest::where('reference_number', $referenceNumber)->exists()) {
            $random = strtoupper(Str::random(4));
            $referenceNumber = "SR-{$timestamp}-{$random}";
        }

        return $referenceNumber;
    }

    /**
     * Send confirmation email to customer
     */
    private function sendConfirmationEmail(ServiceRequest $serviceRequest): void
    {
        try {
            // You can implement actual email sending here
            // For now, this is a placeholder for email logic

            // Example using Laravel Mail (uncomment when ready):
            // \Mail::send('emails.service-request-confirmation', [
            //     'request' => $serviceRequest,
            // ], function ($message) use ($serviceRequest) {
            //     $message->to($serviceRequest->customer->user->email)
            //         ->subject("Service Request Confirmation - {$serviceRequest->reference_number}");
            // });

            \Log::info("Confirmation email queued for Service Request", [
                'reference_number' => $serviceRequest->reference_number,
                'email' => $serviceRequest->customer->user->email ?? 'N/A',
            ]);

        } catch (\Exception $e) {
            \Log::warning("Failed to send confirmation email", [
                'reference_number' => $serviceRequest->reference_number,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
