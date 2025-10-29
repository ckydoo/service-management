<!-- resources/views/customer/view-requests.blade.php -->
@extends('layouts.app')

@section('title', 'My Service Requests')

@section('content')
<div class="row mb-3">
    <div class="col">
        <h2><i class="fas fa-list-check"></i> My Service Requests</h2>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-hover">
        <thead class="table-primary">
            <tr>
                <th>Reference #</th>
                <th>Machine</th>
                <th>Type</th>
                <th>Status</th>
                <th>Quotation</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requests as $request)
            <tr>
                <td><strong>{{ $request->reference_number }}</strong></td>
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
                    <span class="badge bg-{{ $statusColors[$request->status] ?? 'secondary' }} badge-status">
                        {{ ucfirst($request->status) }}
                    </span>
                </td>
                <td>
                    @if($request->quotation)
                        <span class="badge bg-success">${{ number_format($request->quotation->total_cost, 2) }}</span>
                        @if($request->quotation->status === 'pending')
                            <button class="btn btn-xs btn-success" onclick="approveQuote({{ $request->quotation->id }})">Approve</button>
                        @endif
                    @else
                        <span class="badge bg-secondary">Pending</span>
                    @endif
                </td>
                <td>
                    <a href="/service-requests/{{ $request->id }}" class="btn btn-sm btn-primary btn-action">
                        <i class="fas fa-eye"></i> View
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{ $requests->links() }}
@endsection

@section('scripts')
<script>
function approveQuote(quoteId) {
    if (confirm('Approve this quotation?')) {
        fetch(`/quotations/${quoteId}/approve`, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        }).then(r => r.json()).then(d => {
            if (d.success) {
                location.reload();
            }
        });
    }
}
</script>
@endsection
