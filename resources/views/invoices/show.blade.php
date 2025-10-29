@extends('layouts.app')

@section('title', 'Invoice ' . $invoice->invoice_number)

@section('content')
<div class="row mb-3">
    <div class="col">
        <h2><i class="fas fa-file-invoice"></i> {{ $invoice->invoice_number }}</h2>
    </div>
    <div class="col-auto">
        <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <button class="btn btn-primary" onclick="window.print()">
            <i class="fas fa-print"></i> Print
        </button>
    </div>
</div>

@if ($message = Session::get('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ $message }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card">
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <h5>Invoice Details</h5>
                <p><strong>Invoice #:</strong> {{ $invoice->invoice_number }}</p>
                <p><strong>Date:</strong> {{ $invoice->created_at->format('M d, Y') }}</p>
                <p><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</p>
            </div>
            <div class="col-md-6">
                <h5>Status</h5>
                @php
                    $statusColors = [
                        'pending' => 'warning',
                        'paid' => 'success',
                        'overdue' => 'danger',
                        'cancelled' => 'secondary'
                    ];
                @endphp
                <p>
                    <span class="badge bg-{{ $statusColors[$invoice->status] ?? 'info' }} p-2" style="font-size: 1rem;">
                        {{ ucfirst($invoice->status) }}
                    </span>
                </p>
                @if($invoice->status === 'paid' && $invoice->paid_date)
                <p><strong>Paid Date:</strong> {{ $invoice->paid_date->format('M d, Y') }}</p>
                @endif
            </div>
        </div>

        <hr>

        <div class="row mb-4">
            <div class="col-md-6">
                <h5>Bill To</h5>
                <p>
                    <strong>{{ $invoice->serviceRequest->customer->user->name ?? 'N/A' }}</strong><br>
                    {{ $invoice->serviceRequest->customer->user->email ?? 'N/A' }}<br>
                    {{ $invoice->serviceRequest->customer->user->phone ?? 'N/A' }}
                </p>
            </div>
            <div class="col-md-6">
                <h5>Service Details</h5>
                <p>
                    <strong>Reference #:</strong> {{ $invoice->serviceRequest->reference_number }}<br>
                    <strong>Machine:</strong> {{ $invoice->serviceRequest->machine->machine_name ?? 'N/A' }}<br>
                    <strong>Type:</strong> {{ ucfirst($invoice->serviceRequest->request_type) }}
                </p>
            </div>
        </div>

        <hr>

        <div class="row mb-4">
            <div class="col">
                <h5>Invoice Summary</h5>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Service - {{ ucfirst($invoice->serviceRequest->request_type) }}</td>
                            <td class="text-end">${{ number_format($invoice->amount, 2) }}</td>
                        </tr>
                        @if($invoice->serviceRequest->quotation)
                        <tr>
                            <td colspan="2" class="text-muted small">
                                Labor: ${{ number_format($invoice->serviceRequest->quotation->labor_cost, 2) }} | 
                                Parts: ${{ number_format($invoice->serviceRequest->quotation->parts_cost, 2) }}
                            </td>
                        </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr class="table-active">
                            <th>Total Due</th>
                            <th class="text-end"><h5>${{ number_format($invoice->amount, 2) }}</h5></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        @if($invoice->notes)
        <div class="row mb-4">
            <div class="col">
                <h5>Notes</h5>
                <p>{{ $invoice->notes }}</p>
            </div>
        </div>
        @endif

        <hr>

        <div class="row">
            <div class="col">
                @if(auth()->user()->role === 'manager')
                    @if($invoice->status !== 'paid')
                    <a href="{{ route('invoices.markAsPaid', $invoice->id) }}" class="btn btn-success" onclick="return confirm('Mark this invoice as paid?')">
                        <i class="fas fa-check"></i> Mark as Paid
                    </a>
                    @endif
                    <a href="{{ route('invoices.destroy', $invoice->id) }}" class="btn btn-danger" onclick="return confirm('Delete this invoice? This action cannot be undone.')">
                        <i class="fas fa-trash"></i> Delete
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('styles')
<style>
    @media print {
        .btn, .row .col-auto {
            display: none !important;
        }
        body {
            background: white;
        }
    }
</style>
@endsection