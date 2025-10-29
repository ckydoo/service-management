<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Service Request System')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body { font-family: 'Figtree', sans-serif; }
            .sidebar { background: #f8f9fa; }
            .nav-link { color: #495057; padding: 0.75rem 1.5rem; display: block; }
            .nav-link:hover { background: #e9ecef; color: #0d6efd; }
            .nav-link.active { background: #0d6efd; color: white; }
            .card-dashboard { border: none; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075); margin-bottom: 1.5rem; }
            .stat-box { background: white; padding: 1.5rem; border-radius: 0.375rem; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075); }
        </style>
    </head>
    <body class="bg-light">
        <!-- Navigation Bar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
            <div class="container-fluid">
                <a class="navbar-brand fw-bold" href="/">
                    <i class="fas fa-tools text-primary"></i> Service Manager
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <div class="ms-auto">
                        @auth
                            <div class="dropdown">
                                <button class="btn btn-link text-decoration-none dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    {{ auth()->user()->name }}
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item">Logout</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm me-2">Login</a>
                            <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Register</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <div class="d-flex">
            <!-- Sidebar -->
            @auth
            <div class="sidebar p-3" style="width: 250px; min-height: 100vh;">
                <div class="text-center mb-4">
                    <i class="fas fa-user-circle" style="font-size: 40px;"></i>
                    <p class="mt-2 small"><strong>{{ auth()->user()->role }}</strong></p>
                </div>

                @if(auth()->user()->role === 'customer')
                    <a href="{{ route('service-requests.index') }}" class="nav-link">
                        <i class="fas fa-plus-circle"></i> New Request
                    </a>
                    <a href="{{ route('service-requests.index') }}" class="nav-link">
                        <i class="fas fa-list"></i> My Requests
                    </a>
                    <a href="{{ route('invoices.index') }}" class="nav-link">
                        <i class="fas fa-receipt"></i> Invoices
                    </a>
                @elseif(auth()->user()->role === 'technician')
                    <a href="{{ route('technician.dashboard') }}" class="nav-link">
                        <i class="fas fa-tasks"></i> My Jobs
                    </a>
                    <a href="{{ route('technician.profile') }}" class="nav-link">
                        <i class="fas fa-user"></i> Profile
                    </a>
                
@elseif(auth()->user()->role === 'manager')
<a href="{{ route('manager.dashboard') }}" class="nav-link {{ request()->routeIs('manager.dashboard') ? 'active' : '' }}">
    <i class="fas fa-chart-line"></i> Dashboard
</a>

<hr class="my-2">

<p class="nav-label small text-muted px-3 mt-3 mb-2">SERVICE MANAGEMENT</p>

<a href="{{ route('service-requests.index') }}" class="nav-link {{ request()->routeIs('service-requests.*') ? 'active' : '' }}">
    <i class="fas fa-list-check"></i> Service Requests
</a>

<a href="{{ route('invoices.index') }}" class="nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
    <i class="fas fa-file-invoice"></i> Invoices
</a>

<hr class="my-2">

<p class="nav-label small text-muted px-3 mt-3 mb-2">MANAGEMENT</p>

<a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
    <i class="fas fa-users"></i> Customers
</a>

<a href="{{ route('technicians.index') }}" class="nav-link {{ request()->routeIs('technicians.*') ? 'active' : '' }}">
    <i class="fas fa-people-carry"></i> Technicians
    <span class="badge bg-primary float-end">{{ \App\Models\Technician::count() }}</span>
</a>

<a href="{{ route('technicians.create') }}" class="nav-link">
    <i class="fas fa-plus-circle"></i> Add Technician
</a>

                @elseif(auth()->user()->role === 'costing_officer')
                    <a href="{{ route('invoices.pending') }}" class="nav-link">
                        <i class="fas fa-hourglass"></i> Pending Verifications
                    </a>
                    <a href="{{ route('invoices.index') }}" class="nav-link">
                        <i class="fas fa-check-circle"></i> Verified
                    </a>
                @endif
            </div>
            @endauth

            <!-- Main Content -->
            <div class="flex-grow-1">
                <div class="container-fluid p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        @yield('scripts')
    </body>
</html>
