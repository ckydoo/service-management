@extends('layouts.app')

@section('title', 'Customers - Service Manager')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">
                    <i class="fas fa-users text-primary me-2"></i>Customers
                </h1>
                <p class="text-muted small mt-1">Manage and view all registered customers</p>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-box">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-0">Total Customers</p>
                            <h4 class="mb-0 mt-2">{{ $customers->total() }}</h4>
                        </div>
                        <i class="fas fa-users text-primary" style="font-size: 1.5rem; opacity: 0.2;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customers Table -->
        <div class="card card-dashboard">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Customers List</h5>
                <span class="badge bg-light text-primary">{{ $customers->total() }} Total</span>
            </div>
            <div class="card-body p-0">
                @if($customers->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Company Name</th>
                                    <th>Contact Person</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>City</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customers as $customer)
                                    <tr>
                                        <td>
                                            <strong>{{ $customer->company_name }}</strong>
                                        </td>
                                        <td>
                                            {{ $customer->user->name ?? 'N/A' }}
                                        </td>
                                        <td>
                                            <a href="mailto:{{ $customer->user->email ?? '' }}" class="text-decoration-none">
                                                {{ $customer->user->email ?? 'N/A' }}
                                            </a>
                                        </td>
                                        <td>
                                            <a href="tel:{{ $customer->phone }}" class="text-decoration-none">
                                                {{ $customer->phone }}
                                            </a>
                                        </td>
                                        <td>{{ $customer->city }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('customers.show', $customer->id) }}" class="btn btn-outline-primary">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-outline-secondary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
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
                            {{ $customers->links() }}
                        </div>
                    </div>
                @else
                    <div class="p-4 text-center text-muted">
                        <i class="fas fa-inbox" style="font-size: 2rem; opacity: 0.3;"></i>
                        <p class="mt-2">No customers found</p>
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