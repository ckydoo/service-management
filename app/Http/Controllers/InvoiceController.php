<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use App\Models\PaymentProof;


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

    /**
     * Display invoices for the logged-in customer
     */
    public function customerInvoices()
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
     * Show a single invoice
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

    /**
     * Generate unique invoice number
     */
    private function generateInvoiceNumber()
    {
        $lastInvoice = Invoice::orderByDesc('id')->first();
        $nextNumber = ($lastInvoice ? intval(substr($lastInvoice->invoice_number, 4)) : 0) + 1;
        return 'INV-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

      /**
     * Upload payment proof for an invoice
     */
    public function uploadPaymentProof(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        
        // Authorization: only customer can upload proof for their own invoice
        $user = auth()->user();
        if ($user->role === 'customer') {
            $customer = $user->customer;
            if ($invoice->serviceRequest->customer_id !== $customer->id) {
                return redirect()->back()->with('error', 'Unauthorized');
            }
        }

        $validated = $request->validate([
            'proof_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
            'payment_reference' => 'required|string|max:100',
            'payment_method' => 'required|in:bank_transfer,cash,check,mobile_money',
            'payment_date' => 'required|date|before_or_equal:today',
        ]);

        try {
            // Store the file
            $filePath = $request->file('proof_file')->store('payment-proofs', 'private');

            // Create payment proof record
            $paymentProof = PaymentProof::create([
                'invoice_id' => $invoice->id,
                'file_path' => $filePath,
                'verification_status' => 'pending',
            ]);

            // Update invoice status to reflect proof uploaded
            $invoice->update([
                'payment_status' => 'proof_uploaded',
            ]);

            // Notify Costing Officer (implement notification)
            // Notification::send($costingOfficers, new PaymentProofUploaded($paymentProof));

            return redirect()->route('invoices.show', $invoice->id)
                            ->with('success', 'Payment proof uploaded successfully. Awaiting verification.');

        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'Failed to upload proof: ' . $e->getMessage())
                            ->withInput();
        }
    }

    /**
     * Get pending payment proofs for Costing Officer
     */
    public function pending(Request $request)
    {
        // Only Costing Officer can access this
        if (auth()->user()->role !== 'costing_officer') {
            abort(403, 'Unauthorized');
        }

        $pendingProofs = PaymentProof::where('verification_status', 'pending')
            ->with('invoice.serviceRequest.customer.user', 'invoice.serviceRequest.machine')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('payment-proofs.pending', ['paymentProofs' => $pendingProofs]);
    }

    /**
     * Verify payment proof
     */
    public function verifyPayment(Request $request, $proofId)
    {
        // Only Costing Officer can verify
        if (auth()->user()->role !== 'costing_officer') {
            abort(403, 'Unauthorized');
        }

        $paymentProof = PaymentProof::findOrFail($proofId);

        $validated = $request->validate([
            'verification_status' => 'required|in:verified,rejected',
            'verification_notes' => 'nullable|string|max:500',
        ]);

        try {
            // Update payment proof
            $paymentProof->update([
                'verification_status' => $validated['verification_status'],
                'verification_notes' => $validated['verification_notes'] ?? null,
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ]);

            // Update invoice based on verification result
            $invoice = $paymentProof->invoice;

            if ($validated['verification_status'] === 'verified') {
                $invoice->update([
                    'payment_status' => 'verified',
                    'payment_verified_at' => now(),
                ]);

                $message = 'Payment verified successfully.';
            } else {
                $invoice->update([
                    'payment_status' => 'rejected',
                ]);

                $message = 'Payment verification rejected.';
            }

            // Send notification to customer
            // Notification::send($invoice->serviceRequest->customer, new PaymentVerified($invoice));

            return redirect()->route('invoices.pending')
                            ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'Verification failed: ' . $e->getMessage());
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
}