<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use Illuminate\Http\Request;

// app/Http/Controllers/QuotationController.php
class QuotationController extends Controller
{
    public function approve($id)
    {
        $quotation = Quotation::findOrFail($id);
        $quotation->update([
            'status' => 'approved',
            'approved_at' => now()
        ]);

        // Create job card
        $serviceRequest = $quotation->serviceRequest;
        $serviceRequest->update(['status' => 'assigned']);

        return response()->json(['success' => true]);
    }

    public function reject($id)
    {
        $quotation = Quotation::findOrFail($id);
        $quotation->update(['status' => 'rejected']);

        return response()->json(['success' => true]);
    }
}

