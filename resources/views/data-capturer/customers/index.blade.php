@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Customers</h1>
        {{-- Remove or comment out the Add button if route doesn't exist --}}
        {{-- <a href="{{ route('data-capturer.customers.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add New Customer
        </a> --}}
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
            @if(isset($customers) && $customers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Company</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $customer)
                                <tr>
                                    <td><strong>#{{ $customer->id }}</strong></td>
                                    <td>{{ $customer->name ?? $customer->customer_name }}</td>
                                    <td>{{ $customer->email ?? 'N/A' }}</td>
                                    <td>{{ $customer->phone ?? $customer->phone_number ?? 'N/A' }}</td>
                                    <td>{{ $customer->company ?? $customer->company_name ?? 'N/A' }}</td>
                                    <td>
                                        @if(isset($customer->status))
                                            @if($customer->status === 'active')
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($customer->status) }}</span>
                                            @endif
                                        @else
                                            <span class="badge bg-success">Active</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ url('/data-capturer/customers/' . $customer->id) }}"
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

                @if(method_exists($customers, 'links'))
                    <div class="mt-4">
                        {{ $customers->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="bi bi-people display-1 text-muted"></i>
                    <h4 class="mt-3 text-muted">No Customers Found</h4>
                    <p class="text-muted">No customers available at the moment.</p>
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
