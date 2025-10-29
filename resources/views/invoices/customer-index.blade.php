@extends('layouts.app')

@section('title', 'My Invoices')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">
                    <i class="fas fa-receipt text-primary me-2"></i>My Invoices
                </h1>
                <p class="text-muted small mt-1">View and manage your invoices</p>
            </div>
        </div>

        <!-- Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-box">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-0">Total Invoices</p>
                            <h4 class="mb-0 mt-2">{{ $invoices->total() }}</h4>
                        </div>
                        <i class="fas fa-file-invoice text-primary" style="font-size: 1.5rem; opacity: 0.2;"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-0">Pending</p>
                            <h4 class="mb-0 mt-2 text-warning">{{ $invoices->where('payment_status', 'pending')->count() }}</h4>
                        </div>
                        <i class="fas fa-hourglass-end text-warning" style="font-size: 1.5rem; opacity: 0.2;"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-0">Paid</p>
                            <h4 class="mb-0 mt-2 text-success">{{ $invoices->where('payment_status', 'paid')->count() }}</h4>
                        </div>
                        <i class="fas fa-check-circle text-success" style="font-size: 1.5rem; opacity: 0.2;"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-0">Total Amount</p>
                            <h4 class="mb-0 mt-2">${{ number_format($invoices->sum('total_amount'), 2) }}</h4>
                        </div>
                        <i class="fas fa-dollar-sign text-info" style="font-size: 1.5rem; opacity: 0.2;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoices Table -->
        <div class="card card-dashboard">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Your Invoices</h5>
                <span class="badge bg-light text-primary">{{ $invoices->total() }} Total</span>
            </div>
            <div class="card-body p-0">
                @if($invoices->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Service Request</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Issued</th>
                                    <th>Due</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoices as $invoice)
                                    <tr>
                                        <td>
                                            <strong>#{{ $invoice->id }}</strong>
                                        </td>
                                        <td>
                                            {{ $invoice->serviceRequest->reference_number ?? 'N/A' }}
                                        </td>
                                        <td>
                                            <strong>${{ number_format($invoice->total_amount, 2) }}</strong>
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'pending' => 'warning',
                                                    'pending_verification' => 'info',
                                                    'verified' => 'secondary',
                                                    'paid' => 'success',
                                                    'cancelled' => 'danger'
                                                ];
                                                $statusLabel = str_replace('_', ' ', $invoice->payment_status);
                                            @endphp
                                            <span class="badge bg-{{ $statusColors[$invoice->payment_status] ?? 'secondary' }}">
                                                {{ ucfirst($statusLabel) }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $invoice->created_at->format('M d, Y') }}
                                            </small>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $invoice->created_at->addDays(30)->format('M d, Y') }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-outline-primary">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                @if($invoice->payment_status === 'pending')
                                                    <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-outline-success">
                                                        <i class="fas fa-upload"></i> Pay
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-center">
                            {{ $invoices->links() }}
                        </div>
                    </div>
                @else
                    <div class="p-4 text-center text-muted">
                        <i class="fas fa-inbox" style="font-size: 2rem; opacity: 0.3;"></i>
                        <p class="mt-2">No invoices found</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .stat-box {
        background: white;
        padding: 1.5rem;
        border-radius: 0.375rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
    }

    .card-dashboard {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
    }

    .btn-group-sm .btn {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }
</style>
@endsection