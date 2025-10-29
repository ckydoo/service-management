<!-- resources/views/service-requests/show.blade.php -->
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
                                    'assigned' => 'info',
                                    'in_progress' => 'primary',
                                    'completed' => 'success',
                                    'cancelled' => 'danger'
                                ];
                                $color = $statusColors[$request->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $color }}">{{ ucfirst(str_replace('_', ' ', $request->status)) }}</span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <strong>Request Type:</strong>
                        <p>
                            <span class="badge bg-info">{{ ucfirst($request->request_type) }}</span>
                        </p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Requires Assessment:</strong>
                        <p>
                            @if($request->requires_assessment)
                                <span class="badge bg-danger">Yes - On-site assessment needed</span>
                            @else
                                <span class="badge bg-success">No - Quick fix available</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <strong>Date Submitted:</strong>
                        <p>{{ $request->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>

                <hr>

                <h6 class="mb-3">Issue Description</h6>
                <p class="bg-light p-3 rounded">
                    {{ $request->request_description }}
                </p>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="card card-dashboard mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Customer Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Company Name:</strong>
                        <p>
                            <a href="{{ route('customers.show', $request->customer->id) }}">
                                {{ $request->customer->company_name }}
                            </a>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <strong>Contact Person:</strong>
                        <p>{{ $request->customer->user->name ?? 'N/A' }}</p>
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
        <div class="card card-dashboard">
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

                @if($request->machine->description)
                    <div class="mt-3">
                        <strong>Description:</strong>
                        <p>{{ $request->machine->description }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar: Quotation & Actions -->
    <div class="col-md-4">
        <!-- Quotation Status -->
        @if($request->quotation)
            <div class="card card-dashboard mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Quotation</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Status:</strong>
                        <p>
                            @if($request->quotation->status === 'pending')
                                <span class="badge bg-warning">Pending Approval</span>
                            @elseif($request->quotation->status === 'approved')
                                <span class="badge bg-success">Approved</span>
                            @elseif($request->quotation->status === 'rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                        </p>
                    </div>

                    <div class="mb-3">
                        <strong>Labor Cost:</strong>
                        <p class="h5">${{ number_format($request->quotation->labor_cost, 2) }}</p>
                    </div>

                    <div class="mb-3">
                        <strong>Parts Cost:</strong>
                        <p class="h5">${{ number_format($request->quotation->parts_cost, 2) }}</p>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <strong>Total Cost:</strong>
                        <p class="h4 text-success">${{ number_format($request->quotation->total_cost, 2) }}</p>
                    </div>

                    @if($request->quotation->status === 'pending' && auth()->user()->role === 'customer')
                        <form method="POST" class="d-grid gap-2">
                            @csrf
                            <button type="button" class="btn btn-success" onclick="approveQuotation({{ $request->quotation->id }})">
                                <i class="fas fa-check"></i> Approve
                            </button>
                            <button type="button" class="btn btn-danger" onclick="rejectQuotation({{ $request->quotation->id }})">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @else
            <div class="card card-dashboard mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Quotation</h5>
                </div>
                <div class="card-body text-center py-4">
                    <p class="text-muted mb-0">
                        <i class="fas fa-hourglass-half"></i>
                        <br>
                        Quotation is being prepared...
                    </p>
                </div>
            </div>
        @endif

        <!-- Job Card Status -->
        @if($request->jobCard)
            <div class="card card-dashboard mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Job Card</h5>
                </div>
                <div class="card-body">
                    <p>
                        <strong>Status:</strong>
                        <span class="badge bg-primary">{{ ucfirst($request->jobCard->status) }}</span>
                    </p>
                    <p>
                        <strong>Assigned To:</strong>
                        <br>
                        {{ $request->jobCard->technician->user->name ?? 'Unassigned' }}
                    </p>
                    <p>
                        <strong>Estimated Duration:</strong>
                        <br>
                        {{ $request->jobCard->estimated_duration ?? 'N/A' }} hours
                    </p>
                </div>
            </div>
        @endif

        <!-- Quick Actions -->
        <div class="card card-dashboard">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body d-grid gap-2">
                @if(auth()->user()->role === 'manager')
                    <button class="btn btn-primary" disabled>
                        <i class="fas fa-check"></i> Assign to Technician
                    </button>
                    <a href="{{ route('service-requests.index') }}" class="btn btn-secondary">
                        <i class="fas fa-list"></i> View All Requests
                    </a>
                @elseif(auth()->user()->role === 'customer')
                    @if($request->quotation && $request->quotation->status === 'approved')
                        <p class="text-muted small text-center">Quotation approved. Waiting for technician assignment.</p>
                    @endif
                    <a href="{{ route('service-requests.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Requests
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function approveQuotation(quotationId) {
    if (confirm('Are you sure you want to approve this quotation?')) {
        fetch(`/quotations/${quotationId}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Quotation approved successfully!');
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to approve quotation'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
}

function rejectQuotation(quotationId) {
    if (confirm('Are you sure you want to reject this quotation?')) {
        fetch(`/quotations/${quotationId}/reject`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Quotation rejected successfully!');
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to reject quotation'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
}
</script>
@endsection