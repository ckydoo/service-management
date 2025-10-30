@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ url('/data-capturer/customers') }}" class="btn btn-outline-secondary btn-sm mb-2">
                <i class="bi bi-arrow-left"></i> Back to Customers
            </a>
            <h1 class="mb-0">Customer Details</h1>
        </div>
        <div>
            <span class="badge bg-{{ $customer->status === 'active' ? 'success' : 'secondary' }} fs-6">
                {{ ucfirst($customer->status ?? 'Active') }}
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Customer Information Card -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-person-circle"></i> Customer Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th style="width: 40%;">Customer ID:</th>
                                <td><strong>#{{ $customer->id }}</strong></td>
                            </tr>
                            <tr>
                                <th>Name:</th>
                                <td>{{ $customer->name ?? $customer->customer_name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td>
                                    @if($customer->email)
                                        <a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a>
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Phone:</th>
                                <td>
                                    @if($customer->phone ?? $customer->phone_number)
                                        <a href="tel:{{ $customer->phone ?? $customer->phone_number }}">
                                            {{ $customer->phone ?? $customer->phone_number }}
                                        </a>
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Company:</th>
                                <td>{{ $customer->company ?? $customer->company_name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Address:</th>
                                <td>{{ $customer->address ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>City:</th>
                                <td>{{ $customer->city ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Created:</th>
                                <td>
                                    {{ $customer->created_at ? $customer->created_at->format('M d, Y h:i A') : 'N/A' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Additional Information Card -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Additional Details</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th style="width: 40%;">Account Type:</th>
                                <td>{{ $customer->account_type ?? 'Standard' }}</td>
                            </tr>
                            <tr>
                                <th>Tax ID:</th>
                                <td>{{ $customer->tax_id ?? $customer->tin ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Payment Terms:</th>
                                <td>{{ $customer->payment_terms ?? 'Net 30' }}</td>
                            </tr>
                            <tr>
                                <th>Credit Limit:</th>
                                <td>${{ number_format($customer->credit_limit ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Outstanding Balance:</th>
                                <td class="text-danger">
                                    <strong>${{ number_format($customer->outstanding_balance ?? 0, 2) }}</strong>
                                </td>
                            </tr>
                            <tr>
                                <th>Last Updated:</th>
                                <td>
                                    {{ $customer->updated_at ? $customer->updated_at->format('M d, Y h:i A') : 'N/A' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    @if($customer->notes ?? $customer->description)
                        <hr>
                        <h6 class="text-muted">Notes:</h6>
                        <p>{{ $customer->notes ?? $customer->description }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Service Requests Section -->
    @if(isset($serviceRequests) && $serviceRequests->count() > 0)
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0"><i class="bi bi-list-task"></i> Service Requests</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>SR #</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($serviceRequests as $sr)
                            <tr>
                                <td><strong>#{{ $sr->id }}</strong></td>
                                <td>{{ Str::limit($sr->description ?? 'N/A', 50) }}</td>
                                <td>
                                    <span class="badge bg-info">{{ ucfirst($sr->status ?? 'Pending') }}</span>
                                </td>
                                <td>{{ $sr->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ url('/data-capturer/service-requests/' . $sr->id) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Quotations Section -->
    @if(isset($quotations) && $quotations->count() > 0)
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="bi bi-file-earmark-text"></i> Quotations</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Quote #</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quotations as $quote)
                            <tr>
                                <td><strong>#{{ $quote->id }}</strong></td>
                                <td>${{ number_format($quote->total_amount ?? $quote->amount ?? 0, 2) }}</td>
                                <td>
                                    <span class="badge bg-success">{{ ucfirst($quote->status ?? 'Pending') }}</span>
                                </td>
                                <td>{{ $quote->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ url('/data-capturer/quotations/' . $quote->id) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
