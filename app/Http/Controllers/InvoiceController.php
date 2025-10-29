<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\PaymentProof;
use Illuminate\Http\Request;


// app/Http/Controllers/InvoiceController.php
class InvoiceController extends Controller
{
    // Show customer invoices
    public function customerInvoices()
    {
        $user = auth()->user();
        $invoices = Invoice::where('customer_id', $user->customer->id)
            ->with('jobCard.serviceRequest', 'paymentProofs')
            ->paginate(10);

        return view('customer.invoices', ['invoices' => $invoices]);
    }

    // Upload payment proof
    public function uploadPaymentProof(Request $request, $invoiceId)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5000'
        ]);

        $invoice = Invoice::findOrFail($invoiceId);
        $file = $request->file('file');
        $path = $file->store('payment-proofs', 'public');

        PaymentProof::create([
            'invoice_id' => $invoiceId,
            'file_path' => $path,
            'verification_status' => 'pending'
        ]);

        return response()->json(['success' => true]);
    }

    // Costing officer verify payment
    public function verifyPayment(Request $request, $proofId)
    {
        $proof = PaymentProof::findOrFail($proofId);
        $proof->update([
            'verification_status' => $request->verified ? 'verified' : 'rejected',
            'verified_by' => auth()->id(),
            'verification_notes' => $request->notes,
            'verified_at' => now()
        ]);

        if ($request->verified) {
            $proof->invoice->update(['payment_status' => 'verified']);
        }

        return response()->json(['success' => true]);
    }
}
