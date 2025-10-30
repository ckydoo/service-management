<!-- resources/views/service-requests/customer-show.blade.php -->
@extends('layouts.app')

@section('title', 'Service Request - ' . $request->reference_number)

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2>
            <i class="fas fa-list-check"></i> Service Request
            <span class="badge bg-secondary">{{ $request->reference_number }}</span>
        </h2>
        <p class="text-muted">Request submitted on {{ $request->created_at->format('Y-m-d H:i') }}</p>
    </div>
    <div class="col text-end">
        <a href="{{ route('service-requests.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row mb-4">
    <!-- Status & Details -->
    <div class="col-md-8">
        <div class="card card-dashboard mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Request Details</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Status:</strong>
                        <p>
                            @php
                                $statusColors = [
                                    'submitted' => 'warning',
                                    'pending_review' => 'info',
                                    'assessed' => 'primary',
                                    'assigned' => 'info',
                                    'in_progress' => 'primary',
                                    'completed' => 'success',
                                    'cancelled' => 'danger'
                                ];
                                $color = $statusColors[$request->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $color }}">
                                {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <strong>Request Type:</strong>
                        <p>
                            <span class="badge bg-info">
                                {{ ucfirst($request->request_type) }}
                            </span>
                        </p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <strong>Description:</strong>
                        <p>{{ $request->request_description }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <strong>Requires Assessment:</strong>
                        <p>
                            @if($request->requires_assessment)
                                <span class="badge bg-warning">Yes</span>
                            @else
                                <span class="badge bg-secondary">No</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <strong>Created:</strong>
                        <p>{{ $request->created_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="card card-dashboard mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Customer Information</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Company Name:</strong>
                        <p>{{ $request->customer->company_name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Contact Person:</strong>
                        <p>{{ $request->customer->contact_person ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <strong>Email:</strong>
                        <p>
                            <a href="mailto:{{ $request->customer->email }}">
                                {{ $request->customer->email }}
                            </a>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <strong>Phone:</strong>
                        <p>
                            <a href="tel:{{ $request->customer->phone }}">
                                {{ $request->customer->phone }}
                            </a>
                        </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <strong>Address:</strong>
                        <p>{{ $request->customer->address }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>City:</strong>
                        <p>{{ $request->customer->city }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Machine Information -->
        @if($request->machine)
        <div class="card card-dashboard mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Machine Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Machine Name:</strong>
                        <p>{{ $request->machine->machine_name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Type:</strong>
                        <p>{{ $request->machine->machine_type ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <strong>Model:</strong>
                        <p>{{ $request->machine->model ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Serial Number:</strong>
                        <p>{{ $request->machine->serial_number ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Quotation Information -->
        @if($request->quotation)
        <div class="card card-dashboard mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Quotation</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Labor Cost:</strong>
                        <p>${{ number_format($request->quotation->labor_cost, 2) }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Parts Cost:</strong>
                        <p>${{ number_format($request->quotation->parts_cost, 2) }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <strong>Total Cost:</strong>
                        <p>
                            <strong class="text-success fs-5">
                                ${{ number_format($request->quotation->total_cost, 2) }}
                            </strong>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <strong>Status:</strong>
                        <p>
                            @php
                                $quotationColors = [
                                    'pending' => 'warning',
                                    'approved' => 'success',
                                    'rejected' => 'danger'
                                ];
                                $quotationColor = $quotationColors[$request->quotation->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $quotationColor }}">
                                {{ ucfirst($request->quotation->status) }}
                            </span>
                        </p>
                    </div>
                </div>

                @if($request->quotation->status === 'pending')
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="d-flex gap-2">
                            <form action="{{ route('quotations.approve', $request->quotation->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success" onclick="return confirm('Approve this quotation?')">
                                    <i class="fas fa-check"></i> Approve Quotation
                                </button>
                            </form>
                            
                            <form action="{{ route('quotations.reject', $request->quotation->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Reject this quotation?')">
                                    <i class="fas fa-times"></i> Reject Quotation
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Job Card Information -->
        @if($request->jobCard)
        <div class="card card-dashboard mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Job Card</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Assigned Technician:</strong>
                        <p>{{ $request->jobCard->technician->user->name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Status:</strong>
                        <p>
                            <span class="badge bg-primary">
                                {{ ucfirst(str_replace('_', ' ', $request->jobCard->status)) }}
                            </span>
                        </p>
                    </div>
                </div>

                @if($request->jobCard->completion_notes)
                <div class="row mt-2">
                    <div class="col-md-12">
                        <strong>Completion Notes:</strong>
                        <p>{{ $request->jobCard->completion_notes }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="col-md-4">
        <!-- Quick Actions -->
        <div class="card card-dashboard mb-3">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('service-requests.index') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-list"></i> View All Requests
                    </a>
                    
                    @if(auth()->user()->role === 'customer')
                    <a href="{{ route('service-requests.create') }}" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-plus"></i> New Request
                    </a>
                    @endif

                    @if($request->quotation && $request->quotation->status === 'approved')
                    <a href="{{ route('invoices.index') }}" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-file-invoice"></i> View Invoices
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Request Timeline -->
        <div class="card card-dashboard">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Timeline</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <i class="fas fa-check-circle text-success"></i>
                        <div>
                            <strong>Request Created</strong>
                            <p class="small text-muted">{{ $request->created_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>

                    @if($request->quotation)
                    <div class="timeline-item">
                        <i class="fas fa-file-invoice text-info"></i>
                        <div>
                            <strong>Quotation Generated</strong>
                            <p class="small text-muted">{{ $request->quotation->created_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                    @endif

                    @if($request->quotation && $request->quotation->status === 'approved')
                    <div class="timeline-item">
                        <i class="fas fa-thumbs-up text-success"></i>
                        <div>
                            <strong>Quotation Approved</strong>
                            <p class="small text-muted">{{ $request->quotation->updated_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                    @endif

                    @if($request->jobCard)
                    <div class="timeline-item">
                        <i class="fas fa-wrench text-warning"></i>
                        <div>
                            <strong>Job Assigned</strong>
                            <p class="small text-muted">{{ $request->jobCard->created_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                    @endif

                    @if($request->status === 'completed')
                    <div class="timeline-item">
                        <i class="fas fa-flag-checkered text-success"></i>
                        <div>
                            <strong>Completed</strong>
                            <p class="small text-muted">{{ $request->updated_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card-dashboard {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
        margin-bottom: 1rem;
    }

    .timeline {
        position: relative;
        padding-left: 30px;
    }

    .timeline-item {
        position: relative;
        padding-bottom: 20px;
    }

    .timeline-item:last-child {
        padding-bottom: 0;
    }

    .timeline-item i {
        position: absolute;
        left: -30px;
        width: 20px;
        height: 20px;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: -21px;
        top: 20px;
        bottom: -20px;
        width: 2px;
        background: #e9ecef;
    }

    .timeline-item:last-child::before {
        display: none;
    }

    .timeline-item strong {
        display: block;
        margin-bottom: 3px;
    }
</style>
@endsection