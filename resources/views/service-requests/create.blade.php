@extends('layouts.app')

@section('title', 'Create Service Request')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="mb-4">
            <h1 class="h3 mb-0">
                <i class="fas fa-plus-circle text-primary me-2"></i>New Service Request
            </h1>
            <p class="text-muted small mt-1">Submit a new service request for your machine</p>
        </div>

        <!-- Form Card -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-dashboard">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Service Request Details</h5>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Please fix the following errors:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('service-requests.store') }}" method="POST">
                            @csrf

                            <!-- Machine Selection -->
                            <div class="mb-3">
                                <label for="machine_id" class="form-label">
                                    <i class="fas fa-cogs text-primary me-1"></i>Machine (Optional)
                                </label>
                                <select class="form-control @error('machine_id') is-invalid @enderror" 
                                        id="machine_id" name="machine_id">
                                    <option value="">-- Select a machine --</option>
                                    @if(auth()->user()->customer && auth()->user()->customer->machines)
                                        @foreach(auth()->user()->customer->machines as $machine)
                                            <option value="{{ $machine->id }}" 
                                                    {{ old('machine_id') == $machine->id ? 'selected' : '' }}>
                                                {{ $machine->machine_name }} ({{ $machine->machine_model ?? 'N/A' }})
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('machine_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Select a machine or leave blank if not listed</small>
                            </div>

                            <!-- Request Type -->
                            <div class="mb-3">
                                <label for="request_type" class="form-label">
                                    <i class="fas fa-list text-primary me-1"></i>Service Type <span class="text-danger">*</span>
                                </label>
                                <select class="form-control @error('request_type') is-invalid @enderror" 
                                        id="request_type" name="request_type" required>
                                    <option value="">-- Select a type --</option>
                                    <option value="breakdown" {{ old('request_type') == 'breakdown' ? 'selected' : '' }}>
                                        <i class="fas fa-exclamation-triangle"></i> Breakdown / Emergency
                                    </option>
                                    <option value="maintenance" {{ old('request_type') == 'maintenance' ? 'selected' : '' }}>
                                        <i class="fas fa-wrench"></i> Preventive Maintenance
                                    </option>
                                    <option value="installation" {{ old('request_type') == 'installation' ? 'selected' : '' }}>
                                        <i class="fas fa-tools"></i> Installation / Setup
                                    </option>
                                </select>
                                @error('request_type')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="mb-3">
                                <label for="request_description" class="form-label">
                                    <i class="fas fa-pen-to-square text-primary me-1"></i>Description <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control @error('request_description') is-invalid @enderror" 
                                          id="request_description" name="request_description" 
                                          rows="5" placeholder="Describe your service request in detail..." required>{{ old('request_description') }}</textarea>
                                @error('request_description')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Provide as much detail as possible to help us understand your needs</small>
                            </div>

                            <!-- Assessment Checkbox -->
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="requires_assessment" 
                                           name="requires_assessment" value="1"
                                           {{ old('requires_assessment') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="requires_assessment">
                                        <i class="fas fa-clipboard-list text-primary me-1"></i>I need a free assessment/quotation before proceeding
                                    </label>
                                </div>
                                <small class="form-text text-muted d-block mt-2">
                                    Check this if you'd like a technician to assess the issue and provide a quotation first
                                </small>
                            </div>

                            <!-- Form Actions -->
                            <div class="d-grid gap-2 d-sm-flex justify-content-sm-end">
                                <a href="{{ route('service-requests.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-1"></i>Submit Request
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Help Section -->
                <div class="card mt-4" style="background-color: #f8f9fa;">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="fas fa-circle-info text-info me-2"></i>Need Help?
                        </h6>
                        <p class="small text-muted mb-2">
                            <strong>Service Types:</strong>
                        </p>
                        <ul class="small text-muted">
                            <li><strong>Breakdown/Emergency:</strong> Machine has stopped working or is in critical condition</li>
                            <li><strong>Preventive Maintenance:</strong> Regular maintenance to prevent future issues</li>
                            <li><strong>Installation/Setup:</strong> Installation of new equipment or configuration assistance</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card-dashboard {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
    }

    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
</style>
@endsection