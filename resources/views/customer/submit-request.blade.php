
<!-- resources/views/customer/submit-request.blade.php -->
@extends('layouts.app')

@section('title', 'Submit Service Request')

@section('content')
<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <div class="card card-dashboard">
            <div class="card-header bg-primary text-white">
                <h4><i class="fas fa-plus-circle"></i> Submit New Service Request</h4>
            </div>
            <div class="card-body">
                <form action="/service-requests" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="machine_id" class="form-label">Select Your Machine</label>
                        <select name="machine_id" id="machine_id" class="form-select">
                            <option value="">-- Select a machine --</option>
                            @foreach($machines as $machine)
                                <option value="{{ $machine->id }}">{{ $machine->machine_name }} ({{ $machine->model }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="request_type" class="form-label">Request Type</label>
                        <select name="request_type" id="request_type" class="form-select" required>
                            <option value="">-- Select type --</option>
                            <option value="breakdown">Breakdown/Repair</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="installation">Installation</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="request_description" class="form-label">Describe the Issue</label>
                        <textarea name="request_description" id="request_description" class="form-control" rows="4" placeholder="Provide details about the problem..." required></textarea>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="requires_assessment" id="requires_assessment" class="form-check-input">
                        <label for="requires_assessment" class="form-check-label">
                            This requires an on-site assessment
                        </label>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-send"></i> Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection




