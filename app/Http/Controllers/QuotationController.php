<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;

class QuotationController extends Controller
{
    /**
     * List all quotations (Manager view)
     */
    public function index()
    {
        $quotations = Quotation::with('serviceRequest', 'serviceRequest.customer')
            ->paginate(15);

        return view('quotations.index', ['quotations' => $quotations]);
    }

    /**
     * Show quotation details (Manager view)
     */
    public function show($id)
    {
        $quotation = Quotation::with('serviceRequest', 'serviceRequest.customer', 'serviceRequest.machine')
            ->findOrFail($id);

        return view('quotations.show', ['quotation' => $quotation]);
    }

    /**
     * Approve quotation
     */
    public function approve($id)
    {
        $quotation = Quotation::findOrFail($id);
        $quotation->update([
            'status' => 'approved',
            'approved_at' => now()
        ]);

        // Update service request status
        $serviceRequest = $quotation->serviceRequest;
        $serviceRequest->update(['status' => 'assigned']);

        return response()->json(['success' => true, 'message' => 'Quotation approved successfully']);
    }

    /**
     * Reject quotation
     */
    public function reject($id)
    {
        $quotation = Quotation::findOrFail($id);
        $quotation->update([
            'status' => 'rejected',
            'rejected_at' => now()
        ]);

        $serviceRequest = $quotation->serviceRequest;
        $serviceRequest->update(['status' => 'rejected']);

        return response()->json(['success' => true, 'message' => 'Quotation rejected']);
    }

    // ============================================================
    // DATA CAPTURER METHODS - NEW!
    // ============================================================

    /**
     * List quotations (Data Capturer view)
     */
    public function capturerIndex()
    {
        $quotations = Quotation::with('serviceRequest', 'serviceRequest.customer')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('data-capturer.quotations.index', ['quotations' => $quotations]);
    }

    /**
     * Show quotation details (Data Capturer view)
     */
    public function capturerShow($id)
    {
        $quotation = Quotation::with('serviceRequest', 'serviceRequest.customer', 'serviceRequest.machine')
            ->findOrFail($id);

        return view('data-capturer.quotations.show', ['quotation' => $quotation]);
    }
}
