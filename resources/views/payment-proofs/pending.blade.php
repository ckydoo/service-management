@extends('layouts.app')

@section('title', 'Payment Verification - Pending Proofs')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2><i class="fas fa-check-circle"></i> Payment Verification</h2>
        <p class="text-muted">Review and verify customer payment proofs</p>
    </div>
    <div class="col-auto">
        <span class="badge bg-danger rounded-pill">{{ $paymentProofs->total() }} Pending</span>
    </div>
</div>

@if($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($message = Session::get('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($paymentProofs->count() > 0)
    <div class="row">
        @foreach($paymentProofs as $proof)
            @php
                $invoice = $proof->invoice;
                $serviceRequest = $invoice->serviceRequest;
                $customer = $serviceRequest->customer;
            @endphp
            <div class="col-lg-6 mb-4">
                <div class="card border-left-warning h-100">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">
                                <strong>Invoice:</strong> {{ $invoice->invoice_number }}
                            </h6>
                            <small class="text-muted">
                                <i class="fas fa-user"></i> {{ $customer->user->name }} 
                                ({{ $customer->user->email }})
                            </small>
                        </div>
                        <span class="badge bg-warning">Pending Review</span>
                    </div>

                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-1"><small class="text-muted">Service Request #</small></p>
                                <p class="mb-3">
                                    <strong>{{ $serviceRequest->reference_number }}</strong>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><small class="text-muted">Invoice Amount</small></p>
                                <p class="mb-3">
                                    <strong class="h5 text-danger">${{ number_format($invoice->amount, 2) }}</strong>
                                </p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-1"><small class="text-muted">Machine</small></p>
                                <p class="mb-0">
                                    {{ $serviceRequest->machine->machine_name ?? 'N/A' }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><small class="text-muted">Service Type</small></p>
                                <p class="mb-0">
                                    <span class="badge bg-info">{{ ucfirst($serviceRequest->request_type) }}</span>
                                </p>
                            </div>
                        </div>

                        <hr class="my-2">

                        <p class="mb-2"><small class="text-muted">Proof Submitted:</small></p>
                        <p class="mb-3">
                            {{ $proof->created_at->format('M d, Y h:i A') }}
                        </p>

                        <div class="alert alert-info mb-3" role="alert">
                            <i class="fas fa-file"></i> Payment proof document ready for review
                            <a href="{{ route('invoices.download-proof', $proof->id) }}" 
                               class="btn btn-sm btn-outline-info float-end"
                               target="_blank">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </div>

                        <!-- Verification Form -->
                        <form action="{{ route('invoices.verify-payment', $proof->id) }}" method="POST" class="verify-form">
                            @csrf

                            <div class="mb-3">
                                <label for="verification_status_{{ $proof->id }}" class="form-label">
                                    <strong>Verification Decision</strong>
                                </label>
                                <select name="verification_status" 
                                        id="verification_status_{{ $proof->id }}"
                                        class="form-select"
                                        required>
                                    <option value="">-- Select Decision --</option>
                                    <option value="verified">✓ Verify Payment</option>
                                    <option value="rejected">✕ Reject Payment</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="verification_notes_{{ $proof->id }}" class="form-label">
                                    Verification Notes
                                </label>
                                <textarea name="verification_notes" 
                                          id="verification_notes_{{ $proof->id }}"
                                          class="form-control"
                                          rows="3"
                                          placeholder="Enter any notes (e.g., amount mismatch, duplicate payment, etc.)"
                                          maxlength="500"></textarea>
                                <small class="form-text text-muted">Maximum 500 characters</small>
                            </div>

                            <div class="d-grid gap-2 d-sm-flex justify-content-sm-end">
                                <button type="submit" class="btn btn-primary" onclick="return confirm('Confirm verification decision?')">
                                    <i class="fas fa-check"></i> Submit Verification
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="row mt-4">
        <div class="col d-flex justify-content-center">
            {{ $paymentProofs->links() }}
        </div>
    </div>
@else
    <div class="alert alert-success" role="alert">
        <i class="fas fa-check-circle"></i>
        <strong>Great!</strong> All payment proofs have been verified. No pending proofs at this time.
    </div>
@endif

@section('scripts')
<script>
document.querySelectorAll('.verify-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        const decision = this.querySelector('[name="verification_status"]').value;
        if (!decision) {
            e.preventDefault();
            alert('Please select a verification decision');
        }
    });
});
</script>
@endsection
@endsection