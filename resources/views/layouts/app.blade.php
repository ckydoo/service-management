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
        * {
            font-family: 'Figtree', sans-serif;
        }

        body {
            background-color: #f5f5f5;
        }

        .main-wrapper {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
        }

        .navbar-custom {
            background: white;
            border-bottom: 1px solid #e9ecef;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .sidebar {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border-right: 1px solid #e9ecef;
            min-height: calc(100vh - 60px);
            overflow-y: auto;
            padding: 1.5rem 0;
            width: 260px;
        }

        .sidebar-header {
            padding: 0 1.5rem 1.5rem;
            text-align: center;
            border-bottom: 1px solid #e9ecef;
            margin-bottom: 1.5rem;
        }

        .sidebar-avatar {
            font-size: 48px;
            margin-bottom: 0.5rem;
            color: #0d6efd;
        }

        .sidebar-role {
            font-size: 0.875rem;
            color: #6c757d;
            font-weight: 500;
            text-transform: capitalize;
        }

        .sidebar-username {
            font-size: 0.9rem;
            font-weight: 600;
            color: #212529;
            margin-top: 0.25rem;
        }

        .nav-section {
            padding: 0 1.5rem;
        }

        .nav-section-title {
            font-size: 0.75rem;
            font-weight: 700;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 1rem 0 0.5rem 0;
            margin-top: 1rem;
            margin-bottom: 0.5rem;
            border-top: 1px solid #e9ecef;
            padding-top: 1rem;
        }

        .nav-link {
            color: #495057;
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            margin-bottom: 0.25rem;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .nav-link:hover {
            background-color: #e9ecef;
            color: #0d6efd;
            border-left-color: #0d6efd;
        }

        .nav-link.active {
            background-color: #0d6efd;
            color: white;
            border-left-color: #0d6efd;
        }

        .nav-link i {
            width: 20px;
            text-align: center;
        }

        .content-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            background: white;
            border-bottom: 1px solid #e9ecef;
            padding: 0.75rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 60px;
        }

        .topbar-brand {
            font-size: 1.25rem;
            font-weight: 700;
            color: #0d6efd;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .topbar-brand i {
            font-size: 1.5rem;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-dropdown {
            position: relative;
        }

        .user-dropdown-btn {
            background: white;
            border: 1px solid #dee2e6;
            color: #495057;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.9rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .user-dropdown-btn:hover {
            background-color: #f8f9fa;
            border-color: #0d6efd;
            color: #0d6efd;
        }

        .dropdown-menu-custom {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 0.375rem;
            min-width: 200px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            z-index: 1000;
            display: none;
            margin-top: 0.25rem;
        }

        .dropdown-menu-custom.show {
            display: block;
        }

        .dropdown-menu-item {
            color: #495057;
            padding: 0.75rem 1rem;
            text-decoration: none;
            display: block;
            transition: all 0.3s ease;
        }

        .dropdown-menu-item:hover {
            background-color: #f8f9fa;
            color: #0d6efd;
        }

        .dropdown-divider {
            border-top: 1px solid #e9ecef;
            margin: 0.5rem 0;
        }

        .page-content {
            flex: 1;
            padding: 2rem 1.5rem;
        }

        @media (max-width: 768px) {
            .sidebar {
                display: none;
                position: absolute;
                width: 100%;
                height: calc(100vh - 60px);
                z-index: 100;
            }

            .sidebar.show {
                display: block;
            }

            .nav-section {
                padding: 0 1rem;
            }
        }
    </style>
</head>
<body>
<div class="main-wrapper">
    <!-- Top Navigation Bar -->
    <nav class="topbar">
        <a href="/" class="topbar-brand">
            <i class="fas fa-tools"></i>
            Service Manager
        </a>

        <div class="topbar-actions">
            @auth
                <div class="user-dropdown">
                    <button class="user-dropdown-btn" onclick="toggleDropdown()">
                        <i class="fas fa-user-circle"></i>
                        {{ auth()->user()->name }}
                        <i class="fas fa-chevron-down" style="font-size: 0.7rem;"></i>
                    </button>
                    <div class="dropdown-menu-custom" id="dropdownMenu">
                        <a href="{{ route('profile.edit') }}" class="dropdown-menu-item">
                            <i class="fas fa-user"></i> Profile
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                            @csrf
                            <button type="submit" class="dropdown-menu-item w-100 text-start" style="border: none; background: none; padding: 0.75rem 1rem;">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm me-2">Login</a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Register</a>
            @endauth
        </div>
    </nav>

    <div style="display: flex; flex: 1;">
        <!-- Sidebar -->
        @auth
        <div class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="sidebar-username">{{ auth()->user()->name }}</div>
                <div class="sidebar-role">{{ auth()->user()->role }}</div>
            </div>

            <!-- Manager Navigation -->
            @if(auth()->user()->role === 'manager')
                <div class="nav-section">
                    <a href="{{ route('manager.dashboard') }}" class="nav-link {{ request()->routeIs('manager.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-chart-line"></i> Dashboard
                    </a>
                </div>

                <div class="nav-section">
                    <h6 class="nav-section-title">Service Management</h6>
                    <a href="{{ route('service-requests.index') }}" class="nav-link {{ request()->routeIs('service-requests.*') ? 'active' : '' }}">
                        <i class="fas fa-list-check"></i> Service Requests
                    </a>
                    <a href="{{ route('invoices.index') }}" class="nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                        <i class="fas fa-file-invoice"></i> Invoices
                    </a>
                </div>

                <div class="nav-section">
                    <h6 class="nav-section-title">Management</h6>
                    <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i> Customers
                    </a>
                    <a href="{{ route('technicians.index') }}" class="nav-link {{ request()->routeIs('technicians.*') ? 'active' : '' }}">
                        <i class="fas fa-wrench"></i> Technicians
                    </a>
                    <a href="{{ route('job-cards.index') }}" class="nav-link {{ request()->routeIs('job-cards.*') ? 'active' : '' }}">
                        <i class="fas fa-briefcase"></i> Job Cards
                    </a>
                </div>
            @endif

            <!-- Technician Navigation -->
            @if(auth()->user()->role === 'technician')
                <div class="nav-section">
                    <a href="{{ route('technician.dashboard') }}" class="nav-link {{ request()->routeIs('technician.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tasks"></i> My Jobs
                    </a>
                    <a href="{{ route('technician.profile') }}" class="nav-link {{ request()->routeIs('technician.profile') ? 'active' : '' }}">
                        <i class="fas fa-user"></i> Profile
                    </a>
                </div>
            @endif

            <!-- Customer Navigation -->
            @if(auth()->user()->role === 'customer')
                <div class="nav-section">
                    <a href="{{ route('service-requests.create') }}" class="nav-link {{ request()->routeIs('service-requests.create') ? 'active' : '' }}">
                        <i class="fas fa-plus-circle"></i> New Request
                    </a>
                    <a href="{{ route('service-requests.index') }}" class="nav-link {{ request()->routeIs('service-requests.index') ? 'active' : '' }}">
                        <i class="fas fa-list"></i> My Requests
                    </a>
                    <a href="{{ route('invoices.index') }}" class="nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                        <i class="fas fa-receipt"></i> Invoices
                    </a>
                </div>
            @endif

            <!-- Costing Officer Navigation -->
            @if(auth()->user()->role === 'costing_officer')
                <div class="nav-section">
                    <a href="{{ route('invoices.pending') }}" class="nav-link {{ request()->routeIs('invoices.pending') ? 'active' : '' }}">
                        <i class="fas fa-hourglass"></i> Pending Verification
                    </a>
                    <a href="{{ route('invoices.index') }}" class="nav-link {{ request()->routeIs('invoices.index') ? 'active' : '' }}">
                        <i class="fas fa-file-invoice-dollar"></i> All Invoices
                    </a>
                </div>
            @endif
        </div>
        @endauth

        <!-- Main Content -->
        <div class="content-wrapper">
            @yield('content')
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function toggleDropdown() {
        const dropdown = document.getElementById('dropdownMenu');
        dropdown.classList.toggle('show');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('dropdownMenu');
        const btn = document.querySelector('.user-dropdown-btn');
        if (!event.target.closest('.user-dropdown')) {
            dropdown.classList.remove('show');
        }
    });
</script>
</body>
</html>