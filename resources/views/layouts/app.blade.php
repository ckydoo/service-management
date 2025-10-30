<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Service Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }

        .app-container {
            display: flex;
            height: 100vh;
        }

        .sidebar {
            width: 280px;
            background: #2c3e50;
            color: white;
            overflow-y: auto;
            padding: 20px 0;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }

        .sidebar-brand {
            padding: 0 20px 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }

        .sidebar-brand h5 {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0;
        }

        .nav-section {
            margin-bottom: 30px;
            padding: 0 10px;
        }

        .nav-section-title {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #95a5a6;
            padding: 10px 10px;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .nav-link {
            display: block;
            padding: 12px 15px;
            color: #ecf0f1;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 5px;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: #3498db;
        }

        .nav-link.active {
            background: #3498db;
            color: white;
            border-left-color: #2980b9;
        }

        .nav-link i {
            width: 20px;
            margin-right: 10px;
        }

        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .navbar-top {
            background: white;
            border-bottom: 1px solid #dee2e6;
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-role-badge {
            font-size: 0.75rem;
            padding: 5px 10px;
        }

        .user-dropdown {
            position: relative;
        }

        .user-dropdown-btn {
            padding: 8px 15px;
            font-size: 0.95rem;
            border-radius: 5px;
        }

        .dropdown-menu-custom {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            min-width: 220px;
            display: none;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-top: 5px;
        }

        .dropdown-menu-custom.show {
            display: block;
        }

        .dropdown-menu-custom a,
        .dropdown-menu-custom button {
            display: block;
            padding: 12px 15px;
            color: #495057;
            text-decoration: none;
            background: none;
            border: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
            font-size: 0.95rem;
        }

        .dropdown-menu-custom a:hover,
        .dropdown-menu-custom button:hover {
            background: #f8f9fa;
            color: #0d6efd;
        }

        .dropdown-divider-custom {
            height: 1px;
            background: #dee2e6;
            margin: 5px 0;
        }

        .content-wrapper {
            flex: 1;
            overflow-y: auto;
            padding: 30px;
        }

        .page-content {
            background: white;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .stat-box {
            background: white;
            padding: 1.5rem;
            border-radius: 0.375rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
        }

        .card-dashboard {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: 0;
                top: 0;
                height: 100vh;
                z-index: 1000;
                transform: translateX(-100%);
                transition: transform 0.3s;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .app-container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                height: auto;
                max-height: 70vh;
            }

            .main-wrapper {
                width: 100%;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
<div class="app-container">
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <h5><i class="fas fa-tools"></i> Service System</h5>
        </div>

        @auth
            <!-- Admin Navigation -->
            @if(auth()->user()->role === 'admin')
                <div class="nav-section">
                    <h6 class="nav-section-title">Dashboard</h6>
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </div>

                <div class="nav-section">
                    <h6 class="nav-section-title">User Management</h6>
                    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="fas fa-users-cog"></i> Manage Users
                    </a>
                </div>

                <div class="nav-section">
                    <h6 class="nav-section-title">Reports & Analytics</h6>
                    <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.index') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar"></i> Reports
                    </a>
                    <a href="{{ route('admin.reports.activity') }}" class="nav-link {{ request()->routeIs('admin.reports.activity') ? 'active' : '' }}">
                        <i class="fas fa-history"></i> Activity Log
                    </a>
                </div>

                <div class="nav-section">
                    <h6 class="nav-section-title">System</h6>
                    <a href="{{ route('admin.settings') }}" class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                </div>
            @endif

            <!-- Manager Navigation -->
            @if(auth()->user()->role === 'manager')
                <div class="nav-section">
                    <h6 class="nav-section-title">Dashboard</h6>
                    <a href="{{ route('manager.dashboard') }}" class="nav-link {{ request()->routeIs('manager.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-chart-line"></i> Dashboard
                    </a>
                </div>

                <div class="nav-section">
                    <h6 class="nav-section-title">Management</h6>
                    <a href="{{ route('manager.service-requests.index') }}" class="nav-link {{ request()->routeIs('manager.service-requests.*') ? 'active' : '' }}">
                        <i class="fas fa-list-check"></i> Service Requests
                    </a>
                    <a href="{{ route('manager.customers.index') }}" class="nav-link {{ request()->routeIs('manager.customers.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i> Customers
                    </a>
                    <a href="{{ route('manager.technicians.index') }}" class="nav-link {{ request()->routeIs('manager.technicians.*') ? 'active' : '' }}">
                        <i class="fas fa-wrench"></i> Technicians
                    </a>
                    <a href="{{ route('manager.job-cards.index') }}" class="nav-link {{ request()->routeIs('manager.job-cards.*') ? 'active' : '' }}">
                        <i class="fas fa-briefcase"></i> Job Cards
                    </a>
                    <a href="{{ route('manager.invoices.index') }}" class="nav-link {{ request()->routeIs('manager.invoices.*') ? 'active' : '' }}">
                        <i class="fas fa-file-invoice"></i> Invoices
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
                    <h6 class="nav-section-title">Service Requests</h6>
                    <a href="{{ route('service-requests.create') }}" class="nav-link {{ request()->routeIs('service-requests.create') ? 'active' : '' }}">
                        <i class="fas fa-plus-circle"></i> New Request
                    </a>
                    <a href="{{ route('service-requests.index') }}" class="nav-link {{ request()->routeIs('service-requests.index') ? 'active' : '' }}">
                        <i class="fas fa-list"></i> My Requests
                    </a>
                </div>

                <div class="nav-section">
                    <h6 class="nav-section-title">Billing</h6>
                    <a href="{{ route('invoices.index') }}" class="nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                        <i class="fas fa-receipt"></i> Invoices
                    </a>
                </div>
            @endif

            <!-- Data Capturer Navigation -->
            @if(auth()->user()->role === 'data_capturer')
                <div class="nav-section">
                    <h6 class="nav-section-title">Dashboard</h6>
                    <a href="{{ route('data-capturer.dashboard') }}" class="nav-link {{ request()->routeIs('data-capturer.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-inbox"></i> Dashboard
                    </a>
                </div>

                <div class="nav-section">
                    <h6 class="nav-section-title">Data Management</h6>
                    <a href="{{ route('data-capturer.service-requests.index') }}" class="nav-link {{ request()->routeIs('data-capturer.service-requests.*') ? 'active' : '' }}">
                        <i class="fas fa-list-check"></i> Service Requests
                    </a>
                    <a href="{{ route('data-capturer.customers.index') }}" class="nav-link {{ request()->routeIs('data-capturer.customers.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i> Customers
                    </a>
                    <a href="{{ route('data-capturer.quotations.index') }}" class="nav-link {{ request()->routeIs('data-capturer.quotations.*') ? 'active' : '' }}">
                        <i class="fas fa-file-alt"></i> Quotations
                    </a>
                </div>
            @endif

            <!-- Costing Officer Navigation -->
            @if(auth()->user()->role === 'costing_officer')
                <div class="nav-section">
                    <h6 class="nav-section-title">Invoices</h6>
                    <a href="{{ route('costing-officer.invoices.pending') }}" class="nav-link {{ request()->routeIs('costing-officer.invoices.*') ? 'active' : '' }}">
                        <i class="fas fa-hourglass"></i> Pending Payments
                    </a>
                </div>

                <div class="nav-section">
                    <h6 class="nav-section-title">Reports</h6>
                    <a href="{{ route('costing-officer.reports.index') }}" class="nav-link {{ request()->routeIs('costing-officer.reports.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-pie"></i> Reports
                    </a>
                </div>
            @endif
        @endauth
    </div>

    <!-- Main Content Area -->
    <div class="main-wrapper">
        <!-- Top Navigation -->
        <div class="navbar-top">
            <button class="btn btn-sm btn-outline-secondary d-md-none" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>

            <div></div>

            @auth
                <div class="user-info">
                    <div class="user-dropdown">
                        <button class="btn btn-sm btn-outline-primary user-dropdown-btn" onclick="toggleDropdown()">
                            <i class="fas fa-user"></i> {{ auth()->user()->name }}
                        </button>
                        <div id="dropdownMenu" class="dropdown-menu-custom">
                            <a href="{{ route('profile.edit') }}">
                                <i class="fas fa-user-edit"></i> Edit Profile
                            </a>
                            <div class="dropdown-divider-custom"></div>
                            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                                @csrf
                                <button type="submit" style="color: #e74c3c;">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <div class="user-info">
                    <a href="{{ route('login') }}" class="btn btn-sm btn-primary">Login</a>
                </div>
            @endauth
        </div>

        <!-- Page Content -->
        <div class="content-wrapper">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

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

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('show');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('dropdownMenu');
        const btn = document.querySelector('.user-dropdown-btn');
        if (!event.target.closest('.user-dropdown') && !event.target.closest('.user-dropdown-btn')) {
            dropdown.classList.remove('show');
        }
    });

    // Close sidebar on mobile when link clicked
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth < 768) {
                document.getElementById('sidebar').classList.remove('show');
            }
        });
    });
</script>

@yield('scripts')
</body>
</html>
