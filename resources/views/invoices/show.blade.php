@extends('layouts.app')

@section('title', 'Invoices')

@section('content')
@if(auth()->user()->role === 'customer')
    <!-- Payment Status Section for Customers -->
    <div class="row mb-4">
        <div class="col">
            <div class="alert alert-warning" role="alert">
                <h5><i class="fas fa-exclamation-triangle"></i> Payment Required</h5>
                <p class="mb-2">
                    This invoice requires payment. Please upload proof of payment below to complete the transaction.
                </p>
                <small class="text-muted">
                    Current Status:
                    @if($invoice->payment_status === 'pending')
                        <strong>Awaiting Payment</strong>
                    @elseif($invoice->payment_status === 'proof_uploaded')
                        <strong>Proof Uploaded - Pending Verification</strong>
                    @elseif($invoice->payment_status === 'verified')
                        <span class="badge bg-success">Payment Verified</span>
                    @elseif($invoice->payment_status === 'rejected')
                        <span class="badge bg-danger">Payment Proof Rejected</span>
                    @endif
                </small>
            </div>
        </div>
    </div>

    <!-- Payment Proof Section -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-receipt"></i> Payment Proof</h5>
                </div>
                <div class="card-body">
                    @if($invoice->paymentProofs && $invoice->paymentProofs->count() > 0)
                        @php
                            $latestProof = $invoice->paymentProofs->sortByDesc('created_at')->first();
                        @endphp
                        <p class="mb-2">
                            <strong>Latest Submission:</strong>
                            <span class="badge bg-{{ $latestProof->verification_status === 'verified' ? 'success' : ($latestProof->verification_status === 'pending' ? 'warning' : 'danger') }}">
                                {{ ucfirst($latestProof->verification_status) }}
                            </span>
                        </p>
                        <small class="text-muted">
                            Submitted: {{ $latestProof->created_at->format('M d, Y h:i A') }}
                        </small>
                        @if($latestProof->verified_at)
                        <br>
                        <small class="text-muted">
                            Verified by: {{ $latestProof->verifiedBy->name ?? 'System' }} at {{ $latestProof->verified_at->format('M d, Y h:i A') }}
                        </small>
                        @endif
                        @if($latestProof->verification_notes)
                        <div class="alert alert-info mt-2 mb-0">
                            <small><strong>Officer Notes:</strong> {{ $latestProof->verification_notes }}</small>
                        </div>
                        @endif

                        @if($latestProof->verification_status !== 'verified')
                        <div class="mt-3">
                            <a href="{{ route('invoices.upload-proof-form', $invoice->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-upload"></i> Upload New Proof
                            </a>
                        </div>
                        @endif
                    @else
                        <p class="text-muted mb-3">
                            <i class="fas fa-info-circle"></i> No payment proof uploaded yet.
                        </p>
                        <a href="{{ route('invoices.upload-proof-form', $invoice->id) }}" class="btn btn-primary btn-block">
                            <i class="fas fa-upload"></i> Upload Payment Proof
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card bg-light">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> How to Submit Payment</h5>
                </div>
                <div class="card-body small">
                    <ol class="mb-0">
                        <li>Click "Upload Payment Proof" button</li>
                        <li>Select your payment receipt or confirmation document</li>
                        <li>Enter payment details (method, date, reference)</li>
                        <li>Submit for verification</li>
                        <li>Our team will verify within 24-48 hours</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endif

@endsection
