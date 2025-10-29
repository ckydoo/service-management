@extends('layouts.app')

@section('title', 'Submit Service Request')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">
                    <i class="fas fa-plus-circle text-primary me-2"></i>Submit New Service Request
                </h1>
                <p class="text-muted small mt-1">Describe the issue and we'll help you resolve it</p>
            </div>
            <a href="{{ route('service-requests.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Requests
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card card-dashboard">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Request Details</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('service-requests.store') }}" method="POST">
                            @csrf

                            <!-- Machine Selection -->
                            <div class="mb-3">
                                <label for="machine_id" class="form-label">
                                    <i class="fas fa-cog text-primary"></i> Select Your Machine
                                </label>
                                <select name="machine_id" id="machine_id" class="form-select @error('machine_id') is-invalid @enderror">
                                    <option value="">-- Select a machine --</option>
                                    @forelse($machines ?? [] as $machine)
                                        <option value="{{ $machine->id }}" @selected(old('machine_id') == $machine->id)>
                                            {{ $machine->machine_name }} ({{ $machine->model ?? 'N/A' }})
                                        </option>
                                    @empty
                                        <option value="" disabled>No machines found</option>
                                    @endforelse
                                </select>
                                @error('machine_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">You can select a specific machine or leave blank</small>
                            </div>

                            <!-- Request Type -->
                            <div class="mb-3">
                                <label for="request_type" class="form-label">
                                    <i class="fas fa-list text-primary"></i> Request Type <span class="text-danger">*</span>
                                </label>
                                <select name="request_type" id="request_type" class="form-select @error('request_type') is-invalid @enderror" required>
                                    <option value="">-- Select type --</option>
                                    <option value="breakdown" @selected(old('request_type') == 'breakdown')>
                                        <i class="fas fa-wrench"></i> Breakdown/Repair
                                    </option>
                                    <option value="maintenance" @selected(old('request_type') == 'maintenance')>
                                        <i class="fas fa-hammer"></i> Maintenance
                                    </option>
                                    <option value="installation" @selected(old('request_type') == 'installation')>
                                        <i class="fas fa-box"></i> Installation
                                    </option>
                                </select>
                                @error('request_type')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Request Description -->
                            <div class="mb-3">
                                <label for="request_description" class="form-label">
                                    <i class="fas fa-pen-to-square text-primary"></i> Describe the Issue <span class="text-danger">*</span>
                                </label>
                                <textarea 
                                    name="request_description" 
                                    id="request_description" 
                                    class="form-control @error('request_description') is-invalid @enderror" 
                                    rows="6" 
                                    placeholder="Provide detailed information about the problem, symptoms, or what you need..."
                                    required
                                >{{ old('request_description') }}</textarea>
                                @error('request_description')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Minimum 10 characters, maximum 2000 characters</small>
                            </div>

                            <!-- Assessment Required -->
                            <div class="mb-4">
                                <div class="form-check">
                                    <input 
                                        type="checkbox" 
                                        name="requires_assessment" 
                                        id="requires_assessment" 
                                        class="form-check-input"
                                        value="1"
                                        @checked(old('requires_assessment'))
                                    >
                                    <label for="requires_assessment" class="form-check-label">
                                        <i class="fas fa-user-check text-primary"></i> 
                                        This requires an on-site assessment
                                    </label>
                                </div>
                                <small class="form-text text-muted d-block mt-2">
                                    Check this if you need a technician to visit and assess the problem on-site
                                </small>
                            </div>

                            <!-- Form Actions -->
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('service-requests.index') }}" class="btn btn-light">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Submit Request
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Info Sidebar -->
            <div class="col-lg-4">
                <div class="card card-dashboard mb-3">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle"></i> Need Help?</h5>
                    </div>
                    <div class="card-body">
                        <h6 class="mb-2">Request Types:</h6>
                        <ul class="list-unstyled small">
                            <li class="mb-2"><strong>Breakdown/Repair:</strong> Emergency repairs for malfunctioning equipment</li>
                            <li class="mb-2"><strong>Maintenance:</strong> Routine maintenance and preventive care</li>
                            <li class="mb-2"><strong>Installation:</strong> New equipment installation or setup</li>
                        </ul>
                    </div>
                </div>

                <div class="card card-dashboard">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-check-circle"></i> Quick Tips</h5>
                    </div>
                    <div class="card-body small">
                        <ul class="list-unstyled">
                            <li class="mb-2">✓ Provide as much detail as possible</li>
                            <li class="mb-2">✓ Include error messages if any</li>
                            <li class="mb-2">✓ Mention when the problem started</li>
                            <li class="mb-2">✓ Check the assessment box if needed</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection