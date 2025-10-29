<!-- resources/views/technicians/show.blade.php -->
@extends('layouts.app')

@section('title', $technician->user->name)

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2>{{ $technician->user->name }}</h2>
        <p class="text-muted">Technician Profile</p>
    </div>
    <div class="col text-end">
        <a href="{{ route('technicians.edit', $technician->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit
        </a>
        <a href="{{ route('technicians.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>

<!-- Information Cards -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Personal Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Full Name:</strong>
                    <p>{{ $technician->user->name }}</p>
                </div>
                <div class="mb-3">
                    <strong>Email:</strong>
                    <p><a href="mailto:{{ $technician->user->email }}">{{ $technician->user->email }}</a></p>
                </div>
                <div class="mb-3">
                    <strong>Status:</strong>
                    <p>
                        @if($technician->availability_status === 'available')
                            <span class="badge bg-success">Available</span>
                        @else
                            <span class="badge bg-warning">On Job</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Professional Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Specialization:</strong>
                    <p>{{ $technician->specialization }}</p>
                </div>
                <div class="mb-3">
                    <strong>License Number:</strong>
                    <p>{{ $technician->license_number }}</p>
                </div>
                <div class="mb-3">
                    <strong>Current Workload:</strong>
                    <p>{{ $technician->current_workload }} active jobs</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Skills -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0">Skills</h5>
    </div>
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2">
            @php
                $skillsArray = array_map('trim', explode(',', $technician->skills));
            @endphp
            @foreach($skillsArray as $skill)
                <span class="badge bg-primary">{{ $skill }}</span>
            @endforeach
        </div>
    </div>
</div>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-box">
            <h6>Total Jobs</h6>
            <h2>{{ $technician->jobCards->count() }}</h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-box">
            <h6>Completed</h6>
            <h2>{{ $technician->jobCards->where('status', 'completed')->count() }}</h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-box">
            <h6>In Progress</h6>
            <h2>{{ $technician->jobCards->where('status', 'in_progress')->count() }}</h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-box">
            <h6>Pending</h6>
            <h2>{{ $technician->jobCards->where('status', 'pending')->count() }}</h2>
        </div>
    </div>
</div>

<!-- Recent Jobs -->
<div class="card card-dashboard">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Recent Job Cards</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Reference</th>
                    <th>Customer</th>
                    <th>Machine</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($technician->jobCards->sortByDesc('updated_at')->take(10) as $job)
                <tr>
                    <td>
                        <strong>{{ $job->serviceRequest->reference_number ?? 'N/A' }}</strong>
                    </td>
                    <td>{{ $job->serviceRequest->customer->company_name ?? 'N/A' }}</td>
                    <td>{{ $job->serviceRequest->machine->machine_name ?? 'N/A' }}</td>
                    <td>
                        @php
                            $statusColors = [
                                'pending' => 'warning',
                                'in_progress' => 'primary',
                                'completed' => 'success',
                                'cancelled' => 'danger'
                            ];
                            $color = $statusColors[$job->status] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $color }}">{{ ucfirst($job->status) }}</span>
                    </td>
                    <td>{{ $job->updated_at->format('Y-m-d H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-3">
                        No job cards found for this technician
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection