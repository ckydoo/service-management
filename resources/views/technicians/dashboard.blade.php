<!-- resources/views/technicians/dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'My Jobs Dashboard')

@section('content')
<div class="row mb-3">
    <div class="col">
        <h2><i class="fas fa-tasks"></i> My Active Jobs</h2>
    </div>
    <div class="col text-end">
        <span class="badge bg-info">{{ $jobs->count() }} Active Jobs</span>
        <span class="badge bg-success ms-2">{{ $completedToday }} Completed Today</span>
    </div>
</div>

@if($jobs->count() > 0)
    @foreach($jobs as $job)
    <div class="card card-dashboard mb-3 job-card" data-status="{{ $job->status }}">
        <div class="card-header">
            <div class="row">
                <div class="col">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tools"></i> {{ $job->serviceRequest->reference_number }}
                    </h5>
                    <small class="text-muted">
                        {{ $job->serviceRequest->customer->company_name ?? $job->serviceRequest->customer->user->name }}
                    </small>
                </div>
                <div class="col text-end">
                    @php
                        $statusBadges = [
                            'pending' => ['class' => 'warning', 'icon' => 'clock', 'text' => 'Pending'],
                            'in_progress' => ['class' => 'primary', 'icon' => 'spinner', 'text' => 'In Progress']
                        ];
                        $badge = $statusBadges[$job->status] ?? ['class' => 'secondary', 'icon' => 'question', 'text' => ucfirst($job->status)];
                    @endphp
                    <span class="badge bg-{{ $badge['class'] }} fs-6">
                        <i class="fas fa-{{ $badge['icon'] }}"></i> {{ $badge['text'] }}
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-2">
                        <strong><i class="fas fa-cog"></i> Machine:</strong> 
                        {{ $job->serviceRequest->machine->machine_name ?? 'N/A' }}
                    </p>
                    <p class="mb-2">
                        <strong><i class="fas fa-exclamation-circle"></i> Issue:</strong> 
                        {{ Str::limit($job->serviceRequest->request_description, 100) }}
                    </p>
                    <p class="mb-2">
                        <strong><i class="fas fa-clock"></i> Est. Duration:</strong> 
                        {{ $job->estimated_duration }} hours
                    </p>
                    <p class="mb-2">
                        <strong><i class="fas fa-hashtag"></i> Job Reference:</strong> 
                        <code>{{ $job->job_reference }}</code>
                    </p>
                </div>
                <div class="col-md-6">
                    <p class="mb-2">
                        <strong><i class="fas fa-user"></i> Contact Person:</strong> 
                        {{ $job->serviceRequest->customer->contact_person ?? 'N/A' }}
                    </p>
                    <p class="mb-2">
                        <strong><i class="fas fa-phone"></i> Phone:</strong> 
                        {{ $job->serviceRequest->customer->phone ?? 'N/A' }}
                    </p>
                    <p class="mb-2">
                        <strong><i class="fas fa-map-marker-alt"></i> Address:</strong> 
                        {{ $job->serviceRequest->customer->address ?? 'N/A' }}
                    </p>
                </div>
            </div>

            <hr>

            <!-- Status Update Buttons -->
            <div class="mt-3 d-flex gap-2 flex-wrap">
                @if($job->status === 'pending')
                    <button class="btn btn-warning" onclick="updateJobStatus({{ $job->id }}, 'in_progress', 'Started Work')">
                        <i class="fas fa-play"></i> Start Job
                    </button>
                @endif

                @if($job->status === 'in_progress')
                    <button class="btn btn-success" onclick="startReport({{ $job->id }})">
                        <i class="fas fa-check-circle"></i> Complete & Submit Report
                    </button>
                    <button class="btn btn-warning" onclick="updateJobStatus({{ $job->id }}, 'pending', 'Paused')">
                        <i class="fas fa-pause"></i> Pause
                    </button>
                @endif

                <a href="{{ route('technician.job-cards.show', $job->id) }}" class="btn btn-outline-primary">
                    <i class="fas fa-eye"></i> View Details
                </a>
            </div>

            <!-- Latest Status Update - FIXED: Added null checks -->
            @if($job->statusUpdates && $job->statusUpdates->count() > 0)
                @php 
                    $latestUpdate = $job->statusUpdates->sortByDesc('id')->first(); 
                @endphp
                @if($latestUpdate && $latestUpdate->created_at)
                    <div class="alert alert-info mt-3 mb-0">
                        <small>
                            <i class="fas fa-info-circle"></i>
                            <strong>Last Update:</strong> {{ $latestUpdate->notes ?? 'Status changed to ' . $latestUpdate->status }}
                            <span class="text-muted">({{ $latestUpdate->created_at->diffForHumans() }})</span>
                        </small>
                    </div>
                @endif
            @endif
        </div>
    </div>
    @endforeach
@else
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> <strong>No active jobs</strong> - You're all caught up!
    </div>
@endif

<!-- Complete Job Modal -->
<div class="modal fade" id="reportModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-clipboard-check"></i> Submit Service Report
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="reportForm" method="POST">
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
                            placeholder="Describe all work performed, parts replaced, and tests conducted..."></textarea>
                        <small class="form-text text-muted">Be detailed - this goes on the customer invoice.</small>
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
                        <small class="form-text text-muted">Include travel time if applicable.</small>
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
@endsection

@section('scripts')
<script>
/**
 * Update job status
 */
function updateJobStatus(jobId, status, notes = '') {
    // Show loading indicator
    const button = event.target;
    const originalHtml = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
    button.disabled = true;

    fetch(`/technician/job-cards/${jobId}/status`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            status: status,
            notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Status updated successfully!', 'success');
            setTimeout(() => {
                location.reload();
            }, 800);
        } else {
            showNotification(data.message || 'Failed to update status', 'error');
            button.innerHTML = originalHtml;
            button.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Network error. Please try again.', 'error');
        button.innerHTML = originalHtml;
        button.disabled = false;
    });
}

/**
 * Open report modal
 */
function startReport(jobId) {
    document.getElementById('reportForm').action = `/technician/job-cards/${jobId}/report`;
    const modal = new bootstrap.Modal(document.getElementById('reportModal'));
    modal.show();
}

/**
 * Handle report form submission
 */
document.getElementById('reportForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitButton = this.querySelector('button[type="submit"]');
    const originalHtml = submitButton.innerHTML;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    submitButton.disabled = true;

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
            showNotification('Service report submitted successfully!', 'success');
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showNotification(data.message || 'Failed to submit report', 'error');
            submitButton.innerHTML = originalHtml;
            submitButton.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Network error. Please try again.', 'error');
        submitButton.innerHTML = originalHtml;
        submitButton.disabled = false;
    });
});

/**
 * Show notification
 */
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}
</script>
@endsection