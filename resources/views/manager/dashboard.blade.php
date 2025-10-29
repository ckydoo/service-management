
<!-- resources/views/manager/dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Manager Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-box">
            <h6>Active Jobs</h6>
            <h2>{{ $data['active'] }}</h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-box">
            <h6>Completed Today</h6>
            <h2>{{ $data['completed_today'] }}</h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-box">
            <h6>Pending Payments</h6>
            <h2>{{ $data['pending_payments'] }}</h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-box">
            <h6>Total Revenue</h6>
            <h2>${{ number_format($data['revenue'], 0) }}</h2>
        </div>
    </div>
</div>

<div class="card card-dashboard">
    <div class="card-header bg-primary text-white">
        <h5>Recent Jobs</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Reference</th>
                    <th>Customer</th>
                    <th>Technician</th>
                    <th>Status</th>
                    <th>Last Update</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentJobs as $job)
                <tr>
                    <td>{{ $job->serviceRequest->reference_number }}</td>
                    <td>{{ $job->serviceRequest->customer->company_name }}</td>
                    <td>{{ $job->technician->user->name }}</td>
                    <td><span class="badge bg-primary">{{ ucfirst($job->status) }}</span></td>
                    <td>{{ $job->updated_at->format('Y-m-d H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
