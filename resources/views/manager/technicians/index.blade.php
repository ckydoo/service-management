@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Technicians</h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            @if(isset($technicians) && $technicians->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Specialization</th>
                                <th>Status</th>
                                <th>Assigned Tasks</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($technicians as $technician)
                                <tr>
                                    <td><strong>#{{ $technician->id }}</strong></td>
                                    <td>{{ $technician->name ?? $technician->user->name ?? 'N/A' }}</td>
                                    <td>
                                        @if($technician->email ?? $technician->user->email ?? null)
                                            {{ $technician->email ?? $technician->user->email }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $technician->phone ?? $technician->phone_number ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $technician->specialization ?? $technician->specialty ?? 'General' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if(isset($technician->status))
                                            @if($technician->status === 'active')
                                                <span class="badge bg-success">Active</span>
                                            @elseif($technician->status === 'busy')
                                                <span class="badge bg-warning text-dark">Busy</span>
                                            @elseif($technician->status === 'inactive')
                                                <span class="badge bg-secondary">Inactive</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($technician->status) }}</span>
                                            @endif
                                        @else
                                            <span class="badge bg-success">Active</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">
                                            {{ $technician->assigned_tasks_count ?? $technician->tasks_count ?? 0 }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('manager.technicians.show', $technician->id) }}" class="btn btn-sm btn-info">
                                            View
                                        </a>
                                        <a href="{{ route('manager.technicians.edit', $technician->id) }}" class="btn btn-sm btn-warning">
                                            Edit
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if(method_exists($technicians, 'links'))
                    <div class="mt-4">
                        {{ $technicians->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <h4 class="text-muted">No Technicians Found</h4>
                    <p class="text-muted">No technicians available at the moment.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
