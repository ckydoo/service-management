@extends('layouts.app')

@section('title', 'Service Requests')

@section('content')
<div class="row mb-3">
    <div class="col">
        <h2><i class="fas fa-list-check"></i> Service Requests</h2>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-hover">
        <thead class="table-primary">
            <tr>
                <th>Reference #</th>
                <th>Customer</th>
                <th>Machine</th>
                <th>Type</th>
                <th>Status</th>
                <th>Quotation</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $request)
            <tr>
                <td><strong>{{ $request->reference_number }}</strong></td>
                <td>{{ $request->customer->user->name ?? 'N/A' }}</td>
                <td>{{ $request->machine->machine_name ?? 'N/A' }}</td>
                <td><span class="badge bg-info">{{ ucfirst($request->request_type) }}</span></td>
                <td>
                    @php
                        $statusColors = [
                            'submitted' => 'warning',
                            'assigned' => 'info',
                            'in_progress' => 'primary',
                            'completed' => 'success',
                            'cancelled' => 'danger'
                        ];
                    @endphp
                    <span class="badge bg-{{ $statusColors[$request->status] ?? 'secondary' }}">
                        {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                    </span>
                </td>
                <td>
                    @if($request->quotation)
                        <span class="badge bg-success">${{ number_format($request->quotation->total_cost, 2) }}</span>
                    @else
                        <span class="badge bg-secondary">Pending</span>
                    @endif
                </td>
                <td>
                    @php
                        $showRoute = match(auth()->user()->role) {
                            'manager' => route('manager.service-requests.show', $request->id),
                            'data_capturer' => route('data-capturer.service-requests.show', $request->id),
                            default => route('service-requests.show', $request->id)
                        };
                    @endphp
                    <a href="{{ $showRoute }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye"></i> View
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center text-muted">No service requests found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $requests->links() }}
@endsection