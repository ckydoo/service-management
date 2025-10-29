<!-- resources/views/invoices/upload-payment-proof.blade.php -->
@extends('layouts.app')

@section('title', 'Upload Payment Proof')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2><i class="fas fa-upload"></i> Upload Payment Proof</h2>
        <p class="text-muted">Invoice #{{ $invoice->invoice_number }} - Amount Due: ${{ number_format($invoice->amount, 2) }}</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-file-upload"></i> Submit Payment Proof</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('invoices.upload-proof', $invoice->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle"></i>
                        <strong>Instructions:</strong> Please upload proof of payment (receipt, bank transfer confirmation, or payment screenshot).
                        Accepted formats: PDF, JPG, JPEG, PNG (Max 5MB)
                    </div>

                    <div class="mb-3">
                        <label for="proof_file" class="form-label">
                            <i class="fas fa-file"></i> Payment Proof Document <span class="text-danger">*</span>
                        </label>
                        <input type="file" 
                               name="proof_file" 
                               id="proof_file" 
                               class="form-control @error('proof_file') is-invalid @enderror"
                               accept=".pdf,.jpg,.jpeg,.png"
                               required>
                        <small class="form-text text-muted">
                            Accepted: PDF, JPG, JPEG, PNG (Maximum 5MB)
                        </small>
                        @error('proof_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="payment_method" class="form-label">
                            <i class="fas fa-credit-card"></i> Payment Method <span class="text-danger">*</span>
                        </label>
                        <select name="payment_method" 
                                id="payment_method" 
                                class="form-select @error('payment_method') is-invalid @enderror"
                                required>
                            <option value="">-- Select Payment Method --</option>
                            <option value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>
                                Bank Transfer
                            </option>
                            <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>
                                Cash Payment
                            </option>
                            <option value="check" {{ old('payment_method') === 'check' ? 'selected' : '' }}>
                                Check
                            </option>
                            <option value="mobile_money" {{ old('payment_method') === 'mobile_money' ? 'selected' : '' }}>
                                Mobile Money
                            </option>
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="payment_date" class="form-label">
                            <i class="fas fa-calendar"></i> Payment Date <span class="text-danger">*</span>
                        </label>
                        <input type="date" 
                               name="payment_date" 
                               id="payment_date" 
                               class="form-control @error('payment_date') is-invalid @enderror"
                               value="{{ old('payment_date', date('Y-m-d')) }}"
                               required>
                        @error('payment_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="payment_reference" class="form-label">
                            <i class="fas fa-hashtag"></i> Payment Reference/Transaction ID <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="payment_reference" 
                               id="payment_reference" 
                               class="form-control @error('payment_reference') is-invalid @enderror"
                               placeholder="e.g., TXN123456, Check #789"
                               value="{{ old('payment_reference') }}"
                               maxlength="100"
                               required>
                        <small class="form-text text-muted">
                            Enter bank transfer reference, check number, or transaction ID
                        </small>
                        @error('payment_reference')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2 d-sm-flex justify-content-sm-end">
                        <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check"></i> Submit Proof
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card bg-light">
            <div class="card-header">
                <h5 class="mb-0">Invoice Summary</h5>
            </div>
            <div class="card-body">
                <p>
                    <strong>Invoice #:</strong> {{ $invoice->invoice_number }}<br>
                    <strong>Amount Due:</strong> 
                    <span class="badge bg-danger">${{ number_format($invoice->amount, 2) }}</span><br>
                    <strong>Status:</strong>
                    @if($invoice->payment_status === 'pending')
                        <span class="badge bg-warning">Pending Payment</span>
                    @elseif($invoice->payment_status === 'proof_uploaded')
                        <span class="badge bg-info">Proof Uploaded</span>
                    @elseif($invoice->payment_status === 'verified')
                        <span class="badge bg-success">Verified</span>
                    @elseif($invoice->payment_status === 'rejected')
                        <span class="badge bg-danger">Rejected</span>
                    @endif
                </p>
            </div>
        </div>

        @if($invoice->paymentProofs && $invoice->paymentProofs->count() > 0)
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Submission History</h5>
            </div>
            <div class="card-body">
                @foreach($invoice->paymentProofs as $proof)
                <div class="mb-3 pb-3 border-bottom">
                    <p class="mb-1">
                        <strong>
                            @if($proof->verification_status === 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif($proof->verification_status === 'verified')
                                <span class="badge bg-success">Verified</span>
                            @else
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                        </strong>
                    </p>
                    <small class="text-muted">
                        Uploaded: {{ $proof->created_at->format('M d, Y h:i A') }}
                    </small>
                    @if($proof->verified_at)
                    <br>
                    <small class="text-muted">
                        Verified: {{ $proof->verified_at->format('M d, Y h:i A') }}
                    </small>
                    @endif
                    @if($proof->verification_notes)
                    <p class="mt-2 mb-0"><small><strong>Notes:</strong> {{ $proof->verification_notes }}</small></p>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection