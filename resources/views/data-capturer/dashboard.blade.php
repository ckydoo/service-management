@extends('layouts.app')

@section('title', 'Data Capturer Dashboard')

@section('content')
<div class="page-container">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-database text-primary me-2"></i>Data Capturer Dashboard
            </h1>
            <p class="text-muted small mt-1">Manage service requests and customer data</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-box">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted small mb-0">Total Requests</p>
                        <h4 class="mb-0 mt-2">{{ $stats['total_requests'] ?? 0 }}</h4>
                    </div>
                    <i class="fas fa-list-check text-primary" style="font-size: 1.5rem; opacity: 0.2;"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-box">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted small mb-0">Today's Requests</p>
                        <h4 class="mb-0 mt-2 text-success">{{ $stats['today_requests'] ?? 0 }}</h4>
                    </div>
                    <i class="fas fa-calendar-check text-success" style="font-size: 1.5rem; opacity: 0.2;"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-box">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted small mb-0">Pending Review</p>
                        <h4 class="mb-0 mt-2 text-warning">{{ $stats['pending_review'] ?? 0 }}</h4>
                    </div>
                    <i class="fas fa-hourglass-half text-warning" style="font-size: 1.5rem; opacity: 0.2;"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-box">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted small mb-0">My Role</p>
                        <h4 class="mb-0 mt-2">Data Capturer</h4>
                    </div>
                    <i class="fas fa-user-tie text-info" style="font-size: 1.5rem; opacity: 0.2;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Quick Actions</h5>
                    <div class="row g-2">
                        <div class="col-md-3">
                            <a href="{{ route('data-capturer.service-requests.create') }}" class="btn btn-primary w-100">
                                <i class="fas fa-plus-circle me-2"></i>New Request
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('data-capturer.service-requests.index') }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-list me-2"></i>All Requests
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('data-capturer.quotations.index') }}" class="btn btn-outline-info w-100">
                                <i class="fas fa-file-alt me-2"></i>Quotations
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('data-capturer.customers.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-users me-2"></i>Customers
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Requests -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-history me-2"></i>Recent Service Requests
            </h5>
        </div>

        @if($recentRequests->isEmpty())
        <div class="card-body text-center py-5">
            <i class="fas fa-inbox text-muted" style="font-size: 2.5rem;"></i>
            <p class="text-muted mt-3">No service requests found.</p>
            <a href="{{ route('data-capturer.service-requests.create') }}" class="btn btn-primary btn-sm mt-2">
                <i class="fas fa-plus"></i> Create First Request
            </a>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Reference</th>
                        <th>Customer</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentRequests as $request)
                    <tr>
                        <td>
                            <strong>{{ $request->reference_number ?? 'N/A' }}</strong>
                        </td>
                        <td>
                            {{ $request->customer?->company_name ?? 'N/A' }}
                        </td>
                        <td>
                            <span class="badge bg-info">
                                {{ ucfirst($request->request_type ?? 'unknown') }}
                            </span>
                        </td>
                        <td>
                            @php
                                $statusColors = [
                                    'pending' => 'warning',
                                    'pending_review' => 'info',
                                    'reviewed' => 'success',
                                    'rejected' => 'danger'
                                ];
                                $statusColor = $statusColors[$request->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $statusColor }}">
                                {{ ucfirst(str_replace('_', ' ', $request->status ?? 'unknown')) }}
                            </span>
                        </td>
                        <td>
                            <small class="text-muted">
                                {{ $request->created_at ? $request->created_at->format('M d, Y') : 'N/A' }}
                            </small>
                        </td>
                        <td>
                            <a href="{{ route('data-capturer.service-requests.show', $request->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <!-- Help Section -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card border-left border-primary">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-lightbulb text-primary me-2"></i>Tips
                    </h5>
                    <ul class="small">
                        <li>Create service requests on behalf of customers</li>
                        <li>Review quotations for accuracy</li>
                        <li>Manage customer information</li>
                        <li>Ensure all data is complete before submission</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-left border-success">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-info-circle text-success me-2"></i>Information
                    </h5>
                    <ul class="small">
                        <li>Last updated: {{ now()->format('M d, Y H:i') }}</li>
                        <li>Your role: <strong>Data Capturer</strong></li>
                        <li>Contact manager for access issues</li>
                        <li>Report bugs to: admin@example.com</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .stat-box {
        background: white;
        border-radius: 0.375rem;
        border: 1px solid #e0e0e0;
        padding: 1.5rem;
        transition: all 0.3s ease;
    }

    .stat-box:hover {
        border-color: #0d6efd;
        box-shadow: 0 0.125rem 0.25rem rgba(13, 110, 253, 0.15);
    }

    .page-container {
        padding: 1.5rem;
    }

    .btn-outline-primary, .btn-outline-info, .btn-outline-secondary {
        color: #0d6efd;
        border-color: #0d6efd;
    }

    .btn-outline-primary:hover, .btn-outline-info:hover, .btn-outline-secondary:hover {
        background-color: #f8f9fa;
    }
</style>
@endsection
