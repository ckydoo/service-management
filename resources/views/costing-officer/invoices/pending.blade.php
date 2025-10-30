@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Pending Invoices</h1>
        <div>
            <span class="badge bg-warning text-dark fs-6">{{ $invoices->total() }} Pending</span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            @if($invoices->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Invoice #</th>
                                <th>Client</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Payment Status</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoices as $invoice)
                                <tr>
                                    <td>
                                        <strong>#{{ $invoice->invoice_number ?? $invoice->id }}</strong>
                                    </td>
                                    <td>
                                        {{ $invoice->client_name ?? $invoice->client->name ?? 'N/A' }}
                                    </td>
                                    <td>
                                        <strong>${{ number_format($invoice->total_amount ?? $invoice->amount, 2) }}</strong>
                                    </td>
                                    <td>
                                        {{ $invoice->created_at->format('M d, Y') }}
                                        <br>
                                        <small class="text-muted">{{ $invoice->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        @if($invoice->payment_status === 'proof_uploaded')
                                            <span class="badge bg-info">Proof Uploaded</span>
                                        @elseif($invoice->payment_status === 'paid')
                                            <span class="badge bg-success">Paid</span>
                                        @else
                                            <span class="badge bg-warning text-dark">{{ ucfirst($invoice->payment_status ?? 'Unpaid') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($invoice->status))
                                            @if($invoice->status === 'pending')
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @elseif($invoice->status === 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @elseif($invoice->status === 'rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($invoice->status) }}</span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('costing-officer.invoices.show', $invoice->id) }}"
                                               class="btn btn-outline-primary"
                                               title="View">
                                                <i class="bi bi-eye"></i> View
                                            </a>

                                            @if($invoice->payment_status === 'proof_uploaded')
                                                <a href="{{ route('costing-officer.invoices.verify', $invoice->id) }}"
                                                   class="btn btn-outline-success"
                                                   title="Verify Payment">
                                                    <i class="bi bi-check-circle"></i> Verify
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
                <div class="mt-4">
                    {{ $invoices->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <h4 class="mt-3 text-muted">No Pending Invoices</h4>
                    <p class="text-muted">All invoices have been processed.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }
    .badge {
        padding: 0.5em 0.75em;
        font-weight: 500;
    }
</style>
@endpush
