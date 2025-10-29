@extends('layouts.app')

@section('title', 'Technicians')

@section('content')
<div class="row mb-3">
    <div class="col">
        <h2><i class="fas fa-people"></i> Technicians</h2>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-hover">
        <thead class="table-primary">
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
                <th>Workload</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($technicians as $technician)
            <tr>
                <td><strong>{{ $technician->user->name ?? 'N/A' }}</strong></td>
                <td>{{ $technician->user->email ?? 'N/A' }}</td>
                <td>
                    @php
                        $statusColor = $technician->availability_status === 'available' ? 'success' : 'warning';
                    @endphp
                    <span class="badge bg-{{ $statusColor }}">
                        {{ ucfirst($technician->availability_status) }}
                    </span>
                </td>
                <td>{{ $technician->current_workload ?? 0 }} jobs</td>
                <td>
                    <a href="{{ route('technicians.show', $technician->id) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye"></i> View
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center text-muted">No technicians found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $technicians->links() }}
@endsection