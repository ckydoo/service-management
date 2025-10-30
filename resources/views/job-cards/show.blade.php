<!-- resources/views/job-cards/show.blade.php -->
@extends('layouts.app')

@section('title', 'Job Card Details')

@section('content')
<div class="row mb-3">
    <div class="col">
        <h2><i class="fas fa-clipboard"></i> Job Card Details</h2>
        <p class="text-muted">{{ $jobCard->job_reference }}</p>
    </div>
    <div class="col text-end">
        <a href="{{ auth()->user()->role === 'technician' ? route('technician.dashboard') : route('job-cards.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>

<!-- Job Status Badge -->
<div class="row mb-3">
    <div class="col">
        @php
            $statusBadges = [
                'pending' => ['class' => 'warning', 'icon' => 'clock', 'text' => 'Pending'],
                'in_progress' => ['class' => 'primary', 'icon' => 'spinner fa-spin', 'text' => 'In Progress'],
                'completed' => ['class' => 'success', 'icon' => 'check-circle', 'text' => 'Completed'],
                'cancelled' => ['class' => 'danger', 'icon' => 'times-circle', 'text' => 'Cancelled']
            ];
            $badge = $statusBadges[$jobCard->status] ?? ['class' => 'secondary', 'icon' => 'question', 'text' => ucfirst($jobCard->status)];
        @endphp
        <h3>
            <span class="badge bg-{{ $badge['class'] }}">
                <i class="fas fa-{{ $badge['icon'] }}"></i> {{ $badge['text'] }}
            </span>
        </h3>
    </div>
</div>

<!-- Main Information -->
<div class="row">
    <!-- Left Column -->
    <div class="col-md-8">
        <!-- Service Request Details -->
        <div class="card card-dashboard mb-3">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-wrench"></i> Service Request</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-2">
                            <strong><i class="fas fa-hashtag"></i> Reference:</strong><br>
                            <code>{{ $jobCard->serviceRequest->reference_number }}</code>
                        </p>
                        <p class="mb-2">
                            <strong><i class="fas fa-tag"></i> Type:</strong><br>
                            <span class="badge bg-info">{{ ucfirst($jobCard->serviceRequest->request_type) }}</span>
                        </p>
                        <p class="mb-2">
                            <strong><i class="fas fa-calendar"></i> Created:</strong><br>
                            {{ $jobCard->serviceRequest->created_at->format('M d, Y H:i') }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2">
                            <strong><i class="fas fa-cog"></i> Machine:</strong><br>
                            {{ $jobCard->serviceRequest->machine->machine_name ?? 'N/A' }}
                        </p>
                        <p class="mb-2">
                            <strong><i class="fas fa-barcode"></i> Serial Number:</strong><br>
                            {{ $jobCard->serviceRequest->machine->serial_number ?? 'N/A' }}
                        </p>
                        <p class="mb-2">
                            <strong><i class="fas fa-clock"></i> Est. Duration:</strong><br>
                            {{ $jobCard->estimated_duration }} hours
                        </p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <p class="mb-0">
                            <strong><i class="fas fa-exclamation-circle"></i> Issue Description:</strong><br>
                            {{ $jobCard->serviceRequest->request_description }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="card card-dashboard mb-3">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-building"></i> Customer Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-2">
                            <strong><i class="fas fa-building"></i> Company:</strong><br>
                            {{ $jobCard->serviceRequest->customer->company_name ?? 'N/A' }}
                        </p>
                        <p class="mb-2">
                            <strong><i class="fas fa-user"></i> Contact Person:</strong><br>
                            {{ $jobCard->serviceRequest->customer->contact_person ?? 'N/A' }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2">
                            <strong><i class="fas fa-phone"></i> Phone:</strong><br>
                            <a href="tel:{{ $jobCard->serviceRequest->customer->phone }}">
                                {{ $jobCard->serviceRequest->customer->phone ?? 'N/A' }}
                            </a>
                        </p>
                        <p class="mb-2">
                            <strong><i class="fas fa-envelope"></i> Email:</strong><br>
                            <a href="mailto:{{ $jobCard->serviceRequest->customer->user->email ?? '' }}">
                                {{ $jobCard->serviceRequest->customer->user->email ?? 'N/A' }}
                            </a>
                        </p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <p class="mb-0">
                            <strong><i class="fas fa-map-marker-alt"></i> Address:</strong><br>
                            {{ $jobCard->serviceRequest->customer->address ?? 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Updates Timeline -->
        @if($jobCard->statusUpdates && $jobCard->statusUpdates->count() > 0)
        <div class="card card-dashboard mb-3">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="fas fa-history"></i> Status Updates</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @foreach($jobCard->statusUpdates->sortByDesc('created_at') as $update)
                    <div class="d-flex mb-3">
                        <div class="me-3">
                            @php
                                $updateIcons = [
                                    'pending' => ['icon' => 'clock', 'color' => 'warning'],
                                    'in_progress' => ['icon' => 'spinner', 'color' => 'primary'],
                                    'completed' => ['icon' => 'check-circle', 'color' => 'success'],
                                    'cancelled' => ['icon' => 'times-circle', 'color' => 'danger']
                                ];
                                $updateIcon = $updateIcons[$update->status] ?? ['icon' => 'circle', 'color' => 'secondary'];
                            @endphp
                            <i class="fas fa-{{ $updateIcon['icon'] }} text-{{ $updateIcon['color'] }} fa-2x"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">
                                <span class="badge bg-{{ $updateIcon['color'] }}">{{ ucfirst(str_replace('_', ' ', $update->status)) }}</span>
                            </h6>
                            @if($update->notes)
                                <p class="mb-1">{{ $update->notes }}</p>
                            @endif
                            @if($update->created_at)
                                <small class="text-muted">
                                    <i class="fas fa-clock"></i> {{ $update->created_at->format('M d, Y H:i') }}
                                    ({{ $update->created_at->diffForHumans() }})
                                </small>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Service Report -->
        @if($jobCard->serviceReport)
        <div class="card card-dashboard mb-3">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-clipboard-check"></i> Service Report</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <strong><i class="fas fa-tasks"></i> Work Completed:</strong>
                        <p>{{ $jobCard->serviceReport->work_completed }}</p>
                    </div>
                </div>
                @if($jobCard->serviceReport->parts_used)
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <strong><i class="fas fa-wrench"></i> Parts Used:</strong>
                        <p>{{ $jobCard->serviceReport->parts_used }}</p>
                    </div>
                </div>
                @endif
                <div class="row">
                    <div class="col-md-6">
                        <strong><i class="fas fa-clock"></i> Labor Hours:</strong>
                        <p>{{ $jobCard->serviceReport->labor_hours }} hours</p>
                    </div>
                    <div class="col-md-6">
                        <strong><i class="fas fa-calendar"></i> Completed:</strong>
                        <p>{{ $jobCard->serviceReport->created_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
                @if($jobCard->serviceReport->additional_notes)
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <strong><i class="fas fa-sticky-note"></i> Additional Notes:</strong>
                        <p>{{ $jobCard->serviceReport->additional_notes }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Right Column -->
    <div class="col-md-4">
        <!-- Technician Information -->
        @if($jobCard->technician)
        <div class="card card-dashboard mb-3">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="fas fa-user-cog"></i> Assigned Technician</h5>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-user-circle fa-4x text-primary"></i>
                </div>
                <h5>{{ $jobCard->technician->user->name }}</h5>
                <p class="text-muted mb-2">{{ $jobCard->technician->specialization }}</p>
                <p class="mb-2">
                    <small><i class="fas fa-id-badge"></i> {{ $jobCard->technician->license_number }}</small>
                </p>
                <p class="mb-2">
                    @php
                        $availabilityBadges = [
                            'available' => ['class' => 'success', 'text' => 'Available'],
                            'busy' => ['class' => 'warning', 'text' => 'Busy'],
                            'offline' => ['class' => 'secondary', 'text' => 'Offline']
                        ];
                        $availBadge = $availabilityBadges[$jobCard->technician->availability_status] ?? ['class' => 'secondary', 'text' => 'Unknown'];
                    @endphp
                    <span class="badge bg-{{ $availBadge['class'] }}">{{ $availBadge['text'] }}</span>
                </p>
                <hr>
                <p class="mb-0">
                    <small class="text-muted">
                        <i class="fas fa-briefcase"></i> Current Workload: {{ $jobCard->technician->current_workload }} jobs
                    </small>
                </p>
            </div>
        </div>
        @endif

        <!-- Action Buttons (For Technicians) -->
        @if(auth()->user()->role === 'technician' && auth()->user()->technician->id === $jobCard->technician_id)
        <div class="card card-dashboard mb-3">
            <div class="card-header bg-warning">
                <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($jobCard->status === 'pending')
                        <button class="btn btn-primary" onclick="updateJobStatus({{ $jobCard->id }}, 'in_progress', 'Started work on job')">
                            <i class="fas fa-play"></i> Start Job
                        </button>
                    @endif

                    @if($jobCard->status === 'in_progress')
                        <button class="btn btn-success" onclick="openReportModal()">
                            <i class="fas fa-check-circle"></i> Complete & Submit Report
                        </button>
                        <button class="btn btn-warning" onclick="updateJobStatus({{ $jobCard->id }}, 'pending', 'Job paused')">
                            <i class="fas fa-pause"></i> Pause Job
                        </button>
                    @endif

                    @if($jobCard->status !== 'completed' && $jobCard->status !== 'cancelled')
                        <button class="btn btn-danger" onclick="cancelJob()">
                            <i class="fas fa-times"></i> Cancel Job
                        </button>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Job Details Summary -->
        <div class="card card-dashboard">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Job Summary</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <strong>Job Reference:</strong><br>
                        <code>{{ $jobCard->job_reference }}</code>
                    </li>
                    <li class="mb-2">
                        <strong>Created:</strong><br>
                        {{ $jobCard->created_at->format('M d, Y H:i') }}
                    </li>
                    @if($jobCard->started_at)
                    <li class="mb-2">
                        <strong>Started:</strong><br>
                        {{ $jobCard->started_at->format('M d, Y H:i') }}
                    </li>
                    @endif
                    @if($jobCard->completed_at)
                    <li class="mb-2">
                        <strong>Completed:</strong><br>
                        {{ $jobCard->completed_at->format('M d, Y H:i') }}
                    </li>
                    @endif
                    <li class="mb-0">
                        <strong>Last Updated:</strong><br>
                        {{ $jobCard->updated_at->diffForHumans() }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Service Report Modal (For Technicians) -->
@if(auth()->user()->role === 'technician' && auth()->user()->technician->id === $jobCard->technician_id)
<div class="modal fade" id="reportModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-clipboard-check"></i> Submit Service Report
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="reportForm" action="{{ route('technician.job-cards.submit-report', $jobCard->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-tasks"></i> Work Completed <span class="text-danger">*</span>
                        </label>
                        <textarea 
                            name="work_completed" 
                            class="form-control" 
                            rows="4" 
                            required
                            placeholder="Describe all work performed..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-wrench"></i> Parts Used
                        </label>
                        <textarea 
                            name="parts_used" 
                            class="form-control" 
                            rows="3"
                            placeholder="List any parts used (optional)..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-clock"></i> Labor Hours <span class="text-danger">*</span>
                        </label>
                        <input 
                            type="number" 
                            name="labor_hours" 
                            class="form-control" 
                            step="0.5" 
                            min="0.5"
                            max="24"
                            required
                            placeholder="e.g., 2.5">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-sticky-note"></i> Additional Notes
                        </label>
                        <textarea 
                            name="notes" 
                            class="form-control" 
                            rows="3"
                            placeholder="Any recommendations or observations? (optional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-paper-plane"></i> Submit Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
function updateJobStatus(jobId, status, notes = '') {
    if (!confirm('Update job status to ' + status.replace('_', ' ') + '?')) {
        return;
    }

    fetch(`/technician/job-cards/${jobId}/status`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: status, notes: notes })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Failed to update status: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error. Please try again.');
    });
}

function openReportModal() {
    const modal = new bootstrap.Modal(document.getElementById('reportModal'));
    modal.show();
}

function cancelJob() {
    if (confirm('Are you sure you want to cancel this job?')) {
        updateJobStatus({{ $jobCard->id }}, 'cancelled', 'Job cancelled by technician');
    }
}

@if(auth()->user()->role === 'technician')
document.getElementById('reportForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Service report submitted successfully!');
            location.reload();
        } else {
            alert('Failed to submit report: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error. Please try again.');
    });
});
@endif
</script>
@endsection