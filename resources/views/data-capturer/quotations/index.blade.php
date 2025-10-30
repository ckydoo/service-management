@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Quotations</h1>
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
            @if(isset($quotations) && $quotations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Quote #</th>
                                <th>Customer</th>
                                <th>Service Request</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quotations as $quotation)
                                <tr>
                                    <td>
                                        <strong>#{{ $quotation->quote_number ?? $quotation->id }}</strong>
                                    </td>
                                    <td>
                                        {{ $quotation->customer_name ?? $quotation->customer->name ?? 'N/A' }}
                                    </td>
                                    <td>
                                        @if(isset($quotation->service_request_id))
                                            <a href="{{ url('/data-capturer/service-requests/' . $quotation->service_request_id) }}">
                                                SR #{{ $quotation->service_request_id }}
                                            </a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        <strong>${{ number_format($quotation->total_amount ?? $quotation->amount ?? 0, 2) }}</strong>
                                    </td>
                                    <td>
                                        @if(isset($quotation->status))
                                            @if($quotation->status === 'pending')
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @elseif($quotation->status === 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @elseif($quotation->status === 'rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                            @elseif($quotation->status === 'sent')
                                                <span class="badge bg-info">Sent</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($quotation->status) }}</span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $quotation->created_at ? $quotation->created_at->format('M d, Y') : 'N/A' }}
                                        @if($quotation->created_at)
                                            <br>
                                            <small class="text-muted">{{ $quotation->created_at->diffForHumans() }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ url('/data-capturer/quotations/' . $quotation->id) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="View">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if(method_exists($quotations, 'links'))
                    <div class="mt-4">
                        {{ $quotations->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="bi bi-file-earmark-text display-1 text-muted"></i>
                    <h4 class="mt-3 text-muted">No Quotations Found</h4>
                    <p class="text-muted">No quotations available at the moment.</p>
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
    }
</style>
@endpush
