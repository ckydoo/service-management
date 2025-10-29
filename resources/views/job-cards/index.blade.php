@extends('layouts.app')

@section('title', 'Job Cards - Service Manager')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">
                    <i class="fas fa-briefcase text-primary me-2"></i>Job Cards
                </h1>
                <p class="text-muted small mt-1">Track all job assignments and statuses</p>
            </div>
        </div>

        <!-- Status Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-box">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-0">Total Jobs</p>
                            <h4 class="mb-0 mt-2">{{ $jobCards->total() }}</h4>
                        </div>
                        <i class="fas fa-briefcase text-primary" style="font-size: 1.5rem; opacity: 0.2;"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-0">In Progress</p>
                            <h4 class="mb-0 mt-2 text-warning">{{ $jobCards->where('status', 'in_progress')->count() }}</h4>
                        </div>
                        <i class="fas fa-spinner text-warning" style="font-size: 1.5rem; opacity: 0.2;"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-0">Completed</p>
                            <h4 class="mb-0 mt-2 text-success">{{ $jobCards->where('status', 'completed')->count() }}</h4>
                        </div>
                        <i class="fas fa-check-circle text-success" style="font-size: 1.5rem; opacity: 0.2;"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-0">Pending</p>
                            <h4 class="mb-0 mt-2 text-info">{{ $jobCards->where('status', 'pending')->count() }}</h4>
                        </div>
                        <i class="fas fa-hourglass-end text-info" style="font-size: 1.5rem; opacity: 0.2;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Job Cards Table -->
        <div class="card card-dashboard">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">All Job Cards</h5>
                <span class="badge bg-light text-primary">{{ $jobCards->total() }} Total</span>
            </div>
            <div class="card-body p-0">
                @if($jobCards->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Job ID</th>
                                    <th>Service Request</th>
                                    <th>Technician</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th>Est. Duration</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jobCards as $jobCard)
                                    <tr>
                                        <td>
                                            <strong>#{{ $jobCard->id }}</strong>
                                        </td>
                                        <td>
                                            {{ $jobCard->serviceRequest->reference_number ?? 'N/A' }}
                                        </td>
                                        <td>
                                            @if($jobCard->technician)
                                                <span class="badge bg-secondary">
                                                    {{ $jobCard->technician->user->name ?? 'N/A' }}
                                                </span>
                                            @else
                                                <span class="badge bg-warning text-dark">Unassigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $jobCard->serviceRequest->customer->company_name ?? 'N/A' }}
                                        </td>
                                        <td>
                                            @switch($jobCard->status)
                                                @case('pending')
                                                    <span class="badge bg-info">Pending</span>
                                                    @break
                                                @case('in_progress')
                                                    <span class="badge bg-warning text-dark">In Progress</span>
                                                    @break
                                                @case('completed')
                                                    <span class="badge bg-success">Completed</span>
                                                    @break
                                                @case('cancelled')
                                                    <span class="badge bg-danger">Cancelled</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">Unknown</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            {{ $jobCard->estimated_duration ?? 'N/A' }} hrs
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $jobCard->created_at->format('M d, Y') }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('job-cards.show', $jobCard->id) }}" class="btn btn-outline-primary">
                                                    <i class="fas fa-eye"></i> View
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
                            {{ $jobCards->links() }}
                        </div>
                    </div>
                @else
                    <div class="p-4 text-center text-muted">
                        <i class="fas fa-inbox" style="font-size: 2rem; opacity: 0.3;"></i>
                        <p class="mt-2">No job cards found</p>
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