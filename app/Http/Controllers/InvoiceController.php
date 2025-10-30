<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\PaymentProof;
use Illuminate\Http\Request;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class InvoiceController extends Controller
{
    /**
     * Display all invoices (manager view)
     */
    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if ($user->role === 'customer') {
            return $this->customerInvoices();
        } elseif ($user->role === 'manager') {
            $invoices = Invoice::with('serviceRequest.customer.user')
                ->paginate(15);
            return view('invoices.index', ['invoices' => $invoices]);
        }

        abort(403, 'Unauthorized');
    }

    // ============================================================
    // CUSTOMER METHODS - FIXED!
    // ============================================================

    /**
     * Display invoices for the logged-in customer (Customer Index)
     * This method is called by the route: route('invoices.index') for customers
     */
    public function customerIndex()
    {
        $user = auth()->user();

        // Get customer ID from user
        $customer = $user->customer;

        if (!$customer) {
            // Return empty paginator
            $invoices = Invoice::query()->paginate(15);
            $invoices->setCollection(collect());
            return view('invoices.index', ['invoices' => $invoices]);
        }

        $invoices = Invoice::whereHas('serviceRequest', function ($query) use ($customer) {
            $query->where('customer_id', $customer->id);
        })
        ->with('serviceRequest.customer.user', 'serviceRequest.machine')
        ->paginate(15);

        return view('invoices.index', ['invoices' => $invoices]);
    }

    /**
     * Display invoices for the logged-in customer (legacy method)
     */
    public function customerInvoices()
    {
        return $this->customerIndex();
    }

    /**
     * Show a single invoice (Customer view)
     * This method is called by the route: route('invoices.show') for customers
     */
    public function customerShow($id)
    {
        $invoice = Invoice::with('serviceRequest.customer.user', 'serviceRequest.machine')
            ->findOrFail($id);

        // Authorization check - customer can only see their own invoices
        $user = auth()->user();
        $customer = $user->customer;

        if ($invoice->serviceRequest->customer_id !== $customer->id) {
            abort(403, 'Unauthorized - this invoice does not belong to you');
        }

        return view('invoices.show', ['invoice' => $invoice]);
    }

    // ============================================================
    // SHARED METHODS (Manager, Costing Officer, Customer)
    // ============================================================

    /**
     * Show a single invoice (Manager/Costing Officer view)
     */
    public function show($id)
    {
        $invoice = Invoice::with('serviceRequest.customer.user', 'serviceRequest.machine')
            ->findOrFail($id);

        // Authorization check
        $user = auth()->user();
        if ($user->role === 'customer') {
            $customer = $user->customer;
            if ($invoice->serviceRequest->customer_id !== $customer->id) {
                abort(403, 'Unauthorized');
            }
        }

        return view('invoices.show', ['invoice' => $invoice]);
    }

    // ============================================================
    // MANAGER METHODS
    // ============================================================

    /**
     * Create a new invoice
     */
    public function create($serviceRequestId)
    {
        $serviceRequest = ServiceRequest::with('customer.user', 'machine', 'quotation')
            ->findOrFail($serviceRequestId);

        // Check if invoice already exists
        $existingInvoice = Invoice::where('service_request_id', $serviceRequestId)->first();
        if ($existingInvoice) {
            return redirect()->route('invoices.show', $existingInvoice->id)
                ->with('info', 'Invoice already exists for this service request');
        }

        return view('invoices.create', ['serviceRequest' => $serviceRequest]);
    }

    /**
     * Store a new invoice
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_request_id' => 'required|exists:service_requests,id',
            'amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date|after:today',
            'notes' => 'nullable|string|max:500',
        ]);

        $serviceRequest = ServiceRequest::findOrFail($validated['service_request_id']);

        // Check if invoice already exists
        $existingInvoice = Invoice::where('service_request_id', $validated['service_request_id'])->first();
        if ($existingInvoice) {
            return redirect()->route('invoices.show', $existingInvoice->id)
                            ->with('warning', 'Invoice already exists for this service request');
        }

        $invoice = Invoice::create([
            'service_request_id' => $validated['service_request_id'],
            'invoice_number' => $this->generateInvoiceNumber(),
            'amount' => $validated['amount'],
            'due_date' => $validated['due_date'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('invoices.show', $invoice->id)
                        ->with('success', 'Invoice created successfully');
    }

    /**
     * Update invoice status
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,paid,overdue,cancelled',
        ]);

        $invoice = Invoice::findOrFail($id);
        $invoice->update(['status' => $validated['status']]);

        return response()->json(['success' => true, 'message' => 'Invoice status updated']);
    }

    /**
     * Mark invoice as paid
     */
    public function markAsPaid($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->update([
            'status' => 'paid',
            'paid_date' => now(),
        ]);

        return redirect()->back()->with('success', 'Invoice marked as paid');
    }

    /**
     * Delete an invoice
     */
    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);

        if ($invoice->status === 'paid') {
            return redirect()->back()->with('error', 'Cannot delete a paid invoice');
        }

        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully');
    }

    // ============================================================
    // COSTING OFFICER METHODS
    // ============================================================

    /**
     * Display pending invoices (Costing Officer)
     */
    public function pending()
    {
        $invoices = Invoice::with('serviceRequest.customer.user')
            ->where('status', 'pending')
            ->orWhere('payment_status', 'proof_uploaded')
            ->paginate(15);

        return view('costing-officer.invoices.pending', ['invoices' => $invoices]);
    }

    /**
     * Update invoice cost (Costing Officer)
     */
    public function updateCost(Request $request, $id)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        $invoice = Invoice::findOrFail($id);
        $invoice->update(['amount' => $validated['amount']]);

        return redirect()->back()->with('success', 'Invoice cost updated successfully');
    }

    /**
     * Verify payment (Costing Officer)
     */
    public function verifyPayment(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        $validated = $request->validate([
            'payment_proof_id' => 'required|exists:payment_proofs,id',
            'verification_status' => 'required|in:verified,rejected',
            'verification_notes' => 'nullable|string|max:500',
        ]);

        try {
            $paymentProof = PaymentProof::findOrFail($validated['payment_proof_id']);

            // Update payment proof
            $paymentProof->update([
                'verification_status' => $validated['verification_status'],
                'verified_by' => auth()->id(),
                'verified_at' => now(),
                'verification_notes' => $validated['verification_notes'] ?? null,
            ]);

            // Update invoice status based on verification
            if ($validated['verification_status'] === 'verified') {
                $invoice->update([
                    'status' => 'paid',
                    'payment_status' => 'verified',
                    'paid_date' => now(),
                ]);
                $message = 'Payment verified and invoice marked as paid';
            } else {
                $invoice->update([
                    'payment_status' => 'rejected',
                ]);
                $message = 'Payment proof rejected. Customer will be notified';
            }

            // Send notification to customer
            // Notification::send($invoice->serviceRequest->customer, new PaymentVerified($invoice));

            return redirect()->route('costing-officer.invoices.pending')
                            ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'Verification failed: ' . $e->getMessage());
        }
    }

    /**
     * Costing Officer Reports
     */
    public function costingReports()
    {
        $stats = [
            'total_invoices' => Invoice::count(),
            'pending_invoices' => Invoice::where('status', 'pending')->count(),
            'paid_invoices' => Invoice::where('status', 'paid')->count(),
            'total_revenue' => Invoice::where('status', 'paid')->sum('total_amount'),
        ];

        return view('costing-officer.reports.index', ['stats' => $stats]);
    }

    /**
     * Costing Officer Analytics
     */
    public function costingAnalytics()
    {
        // Add analytics logic here
        return view('costing-officer.reports.analytics');
    }

    // ============================================================
    // CUSTOMER PAYMENT METHODS
    // ============================================================

    /**
     * Upload payment proof for an invoice
     */
    public function uploadProofForm($id)
    {
        $invoice = Invoice::findOrFail($id);

        // Authorization: only customer can upload proof for their own invoice
        $user = auth()->user();
        if ($user->role === 'customer') {
            $customer = $user->customer;
            if ($invoice->serviceRequest->customer_id !== $customer->id) {
                abort(403, 'Unauthorized');
            }
        }

        // Load the view that contains the form
        return view('invoices.upload-payment-proof', ['invoice' => $invoice]);
    }

    // Replace your existing uploadProofOfPayment method with this:
public function uploadProofOfPayment(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        // Authorization: only customer can upload proof for their own invoice
        $user = auth()->user();
        if ($user->role === 'customer') {
            $customer = $user->customer;
            if ($invoice->serviceRequest->customer_id !== $customer->id) {
                abort(403, 'Unauthorized');
            }
        }

        try {
            // STEP 1: Validate all required fields
            $validated = $request->validate([
                'payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // max 5MB
                'payment_reference' => 'required|string|max:255',
                'payment_method' => 'required|string|max:50',
                'payment_date' => 'required|date',
            ]);

            // Store the uploaded file
            $filePath = $request->file('payment_proof')->store('payment_proofs');

            // STEP 2: Create payment proof record with all required fields
            \App\Models\PaymentProof::create([
                'invoice_id' => $invoice->id,
                'file_path' => $filePath,
                'verification_status' => 'pending',
                'payment_reference' => $validated['payment_reference'],
                'payment_method' => $validated['payment_method'],
                'payment_date' => $validated['payment_date'],
            ]);

            // Update invoice payment status
            $invoice->update(['status' => 'proof_uploaded']);

            return redirect()->route('invoices.show', $invoice->id)
                            ->with('success', 'Payment proof uploaded successfully. Awaiting verification.');

        } catch (\Exception $e) {
            // STEP 3: Catch any exception and redirect with an error message
            Log::error("Payment proof upload failed for Invoice ID {$invoice->id}: " . $e->getMessage());

            return redirect()->back()
                            ->with('error', 'Upload failed. Please check form data and file permissions. Error: ' . $e->getMessage())
                            ->withInput();
        }
    }

    /**
     * Display a payment proof file (with authorization)
     */
    public function downloadProof($proofId)
    {
        $paymentProof = PaymentProof::findOrFail($proofId);
        $invoice = $paymentProof->invoice;

        // Authorization: only customer, costing officer, or manager can access
        $user = auth()->user();
        if ($user->role === 'customer') {
            $customer = $user->customer;
            if ($invoice->serviceRequest->customer_id !== $customer->id) {
                abort(403, 'Unauthorized');
            }
        } elseif (!in_array($user->role, ['costing_officer', 'manager'])) {
            abort(403, 'Unauthorized');
        }

        return Storage::download($paymentProof->file_path);
    }

    // ============================================================
    // HELPER METHODS
    // ============================================================

    /**
     * Generate unique invoice number
     */
    private function generateInvoiceNumber()
    {
        $lastInvoice = Invoice::orderByDesc('id')->first();
        $nextNumber = ($lastInvoice ? intval(substr($lastInvoice->invoice_number, 4)) : 0) + 1;
        return 'INV-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}
