@extends('layouts.app')

@section('title', 'My Service Requests')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">
                    <i class="fas fa-list text-primary me-2"></i>My Service Requests
                </h1>
                <p class="text-muted small mt-1">View and track your service requests</p>
            </div>
            <a href="{{ route('service-requests.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> New Request
            </a>
        </div>

        <!-- Stats -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stat-box">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-0">Total Requests</p>
                            <h4 class="mb-0 mt-2">{{ $requests->total() }}</h4>
                        </div>
                        <i class="fas fa-list-check text-primary" style="font-size: 1.5rem; opacity: 0.2;"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-box">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-0">In Progress</p>
                            <h4 class="mb-0 mt-2 text-info">{{ $requests->where('status', 'in_progress')->count() }}</h4>
                        </div>
                        <i class="fas fa-spinner text-info" style="font-size: 1.5rem; opacity: 0.2;"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-box">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-0">Completed</p>
                            <h4 class="mb-0 mt-2 text-success">{{ $requests->where('status', 'completed')->count() }}</h4>
                        </div>
                        <i class="fas fa-check-circle text-success" style="font-size: 1.5rem; opacity: 0.2;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Service Requests Table -->
        <div class="card card-dashboard">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Your Service Requests</h5>
                <span class="badge bg-light text-primary">{{ $requests->total() }} Total</span>
            </div>
            <div class="card-body p-0">
                @if($requests->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Reference #</th>
                                    <th>Machine</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Quotation</th>
                                    <th>Submitted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requests as $request)
                                    <tr>
                                        <td>
                                            <strong>{{ $request->reference_number }}</strong>
                                        </td>
                                        <td>
                                            {{ $request->machine->machine_name ?? 'N/A' }}
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ ucfirst($request->request_type) }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'submitted' => 'warning',
                                                    'assessed' => 'info',
                                                    'assigned' => 'primary',
                                                    'in_progress' => 'secondary',
                                                    'completed' => 'success',
                                                    'cancelled' => 'danger'
                                                ];
                                                $statusLabel = str_replace('_', ' ', $request->status);
                                            @endphp
                                            <span class="badge bg-{{ $statusColors[$request->status] ?? 'secondary' }}">
                                                {{ ucfirst($statusLabel) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($request->quotation)
                                                @if($request->quotation->status === 'approved')
                                                    <span class="badge bg-success">✓ ${{ number_format($request->quotation->total_cost, 2) }}</span>
                                                @elseif($request->quotation->status === 'rejected')
                                                    <span class="badge bg-danger">✗ Rejected</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">${{ number_format($request->quotation->total_cost, 2) }}</span>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $request->created_at->format('M d, Y') }}
                                            </small>
                                        </td>
                                        <td>
                                            <a href="{{ route('service-requests.show', $request->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-center">
                            {{ $requests->links() }}
                        </div>
                    </div>
                @else
                    <div class="p-4 text-center">
                        <i class="fas fa-inbox" style="font-size: 2rem; opacity: 0.3;"></i>
                        <p class="mt-2 text-muted">No service requests found</p>
                        <a href="{{ route('service-requests.create') }}" class="btn btn-primary btn-sm mt-2">
                            <i class="fas fa-plus-circle"></i> Submit Your First Request
                        </a>
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
</style>
@endsection