@extends('layouts.app')

@section('title', 'Invoices')

@section('content')
<div class="row mb-3">
    <div class="col">
        <h2><i class="fas fa-file-invoice"></i> Invoices</h2>
    </div>
    @if(auth()->user()->role === 'manager')
    <div class="col-auto">
        <a href="{{ route('service-requests.index') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> New Invoice
        </a>
    </div>
    @endif
</div>

@if ($message = Session::get('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ $message }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if ($message = Session::get('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ $message }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if ($message = Session::get('warning'))
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    {{ $message }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="table-responsive">
    <table class="table table-hover">
        <thead class="table-primary">
            <tr>
                <th>Invoice #</th>
                @if(auth()->user()->role === 'manager')
                <th>Customer</th>
                @endif
                <th>Service Ref</th>
                <th>Amount</th>
                <th>Due Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $invoice)
            <tr>
                <td><strong>{{ $invoice->invoice_number }}</strong></td>
                @if(auth()->user()->role === 'manager')
                <td>{{ $invoice->serviceRequest->customer->user->name ?? 'N/A' }}</td>
                @endif
                <td>{{ $invoice->serviceRequest->reference_number ?? 'N/A' }}</td>
                <td>${{ number_format($invoice->amount, 2) }}</td>
                <td>
                    {{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}
                    @if(\Carbon\Carbon::now()->isAfter($invoice->due_date) && $invoice->status !== 'paid')
                    <span class="badge bg-danger ms-2">Overdue</span>
                    @endif
                </td>
                <td>
                    @php
                        $statusColors = [
                            'pending' => 'warning',
                            'paid' => 'success',
                            'overdue' => 'danger',
                            'cancelled' => 'secondary'
                        ];
                    @endphp
                    <span class="badge bg-{{ $statusColors[$invoice->status] ?? 'info' }}">
                        {{ ucfirst($invoice->status) }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye"></i> View
                    </a>
                    @if(auth()->user()->role === 'manager' && $invoice->status !== 'paid')
                    <a href="{{ route('invoices.markAsPaid', $invoice->id) }}" class="btn btn-sm btn-success" onclick="return confirm('Mark as paid?')">
                        <i class="fas fa-check"></i> Paid
                    </a>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center text-muted py-4">
                    <i class="fas fa-inbox" style="font-size: 2rem;"></i>
                    <p class="mt-2">No invoices found.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($invoices->count() > 0)
    <div class="mt-3">
        {{ $invoices->links() }}
    </div>
@endif

@endsection