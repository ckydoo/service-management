@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Admin Dashboard</h3>
                </div>

                <div class="card-body">
                    <h4>Welcome, {{ auth()->user()->name ?? 'Admin' }}</h4>
                    
                    <div class="row mt-4">
                        <div class="col-md-3">
                            <div class="card text-white bg-primary">
                                <div class="card-body">
                                    <h5>Users</h5>
                                    <p class="h2">{{ \App\Models\User::count() }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card text-white bg-success">
                                <div class="card-body">
                                    <h5>Service Requests</h5>
                                    <p class="h2">{{ \App\Models\ServiceRequest::count() ?? 0 }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Add more stats cards as needed -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection