@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Costing Officer Reports</h1>

    <div class="row">
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-muted">Total Invoices</h5>
                    <h2 class="display-4">{{ $totalInvoices ?? 0 }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-success">Paid Amount</h5>
                    <h2 class="display-4">${{ number_format($paidTotal ?? 0, 2) }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-warning">Pending Amount</h5>
                    <h2 class="display-4">${{ number_format($pendingTotal ?? 0, 2) }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-info">Verified</h5>
                    <h2 class="display-4">{{ $verifiedCount ?? 0 }}</h2>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
