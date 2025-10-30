@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Service Request Details</h1>
    
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Request #{{ $serviceRequest->id }}</h5>
            <p><strong>Status:</strong> {{ $serviceRequest->status }}</p>
            <p><strong>Created:</strong> {{ $serviceRequest->created_at->format('Y-m-d H:i') }}</p>
            
            {{-- Add more fields as needed --}}
        </div>
    </div>
    
    <a href="{{ route('service-requests.index') }}" class="btn btn-secondary mt-3">Back to List</a>
</div>
@endsection