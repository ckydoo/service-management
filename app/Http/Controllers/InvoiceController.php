<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;

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
}