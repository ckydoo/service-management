<!-- resources/views/technician/dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'My Jobs Dashboard')

@section('content')
<div class="row mb-3">
    <div class="col">
        <h2><i class="fas fa-tasks"></i> My Active Jobs</h2>
    </div>
    <div class="col text-end">
        <select id="status-filter" class="form-select d-inline-block w-auto" onchange="filterJobs()">
            <option value="">All Jobs</option>
            <option value="pending">Pending</option>
            <option value="in_progress">In Progress</option>
        </select>
    </div>
</div>

@foreach($jobs as $job)
<div class="card card-dashboard mb-3">
    <div class="card-header">
        <div class="row">
            <div class="col">
                <h5 class="card-title mb-0">
                    {{ $job->serviceRequest->reference_number }} - {{ $job->serviceRequest->customer->company_name }}
                </h5>
            </div>
            <div class="col text-end">
                <span class="badge bg-primary">{{ ucfirst($job->status) }}</span>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Machine:</strong> {{ $job->serviceRequest->machine->machine_name }}</p>
                <p><strong>Issue:</strong> {{ $job->serviceRequest->request_description }}</p>
                <p><strong>Estimated Duration:</strong> {{ $job->estimated_duration }} hours</p>
            </div>
            <div class="col-md-6">
                <p><strong>Customer:</strong> {{ $job->serviceRequest->customer->contact_person }}</p>
                <p><strong>Phone:</strong> {{ $job->serviceRequest->customer->phone }}</p>
                <p><strong>Address:</strong> {{ $job->serviceRequest->customer->address }}</p>
            </div>
        </div>

        <hr>

        <div class="mt-3">
            <button class="btn btn-warning btn-action" onclick="updateStatus({{ $job->id }}, 'En Route')">
                <i class="fas fa-road"></i> En Route
            </button>
            <button class="btn btn-primary btn-action" onclick="updateStatus({{ $job->id }}, 'In Progress')">
                <i class="fas fa-spinner"></i> In Progress
            </button>
            <button class="btn btn-success btn-action" onclick="startReport({{ $job->id }})">
                <i class="fas fa-check-circle"></i> Complete & Report
            </button>
        </div>
    </div>
</div>
@endforeach

<!-- Complete Job Modal -->
<div class="modal fade" id="reportModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Submit Service Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="reportForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Work Completed</label>
                        <textarea name="work_completed" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Labor Hours</label>
                        <input type="number" name="labor_hours" class="form-control" step="0.5" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Additional Notes</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Report</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function updateStatus(jobId, status) {
    fetch(`/job-cards/${jobId}/status`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({status: status})
    }).then(r => r.json()).then(d => {
        if (d.success) {
            location.reload();
        }
    });
}

function startReport(jobId) {
    document.getElementById('reportForm').action = `/job-cards/${jobId}/report`;
    new bootstrap.Modal(document.getElementById('reportModal')).show();
}

document.getElementById('reportForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch(this.action, {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: formData
    }).then(r => r.json()).then(d => {
        if (d.success) {
            alert('Report submitted successfully!');
            location.reload();
        }
    });
});
</script>
@endsection
