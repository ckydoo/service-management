<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Service Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        .navbar-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .sidebar {
            background-color: #2c3e50;
            min-height: 100vh;
            padding: 20px 0;
            position: fixed;
            width: 250px;
            left: 0;
            top: 70px;
        }
        .sidebar a {
            color: #ecf0f1;
            text-decoration: none;
            display: block;
            padding: 12px 20px;
            transition: all 0.3s;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: #667eea;
            padding-left: 30px;
        }
        .main-content {
            margin-left: 250px;
            margin-top: 70px;
            padding: 20px;
        }
        .card-dashboard {
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .card-dashboard:hover {
            transform: translateY(-5px);
        }
        .stat-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .badge-status {
            font-size: 12px;
            padding: 5px 10px;
        }
        .btn-action {
            border-radius: 20px;
            padding: 8px 16px;
            font-size: 13px;
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand text-white" href="/">
                <i class="fas fa-tools"></i> Service Manager
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="navbar-text text-white">{{ auth()->user()->name }}</span>
                    </li>
                    <li class="nav-item">
                        <form action="/logout" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-light btn-sm ms-2">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <div class="text-center mb-4 text-white">
            <i class="fas fa-user-circle" style="font-size: 40px;"></i>
            <p class="mt-2 small">{{ auth()->user()->role }}</p>
        </div>

        @if(auth()->user()->role === 'customer')
            <a href="/service-requests" class="nav-link"><i class="fas fa-plus-circle"></i> New Request</a>
            <a href="/service-requests" class="nav-link"><i class="fas fa-list"></i> My Requests</a>
            <a href="/invoices" class="nav-link"><i class="fas fa-receipt"></i> Invoices</a>
        @elseif(auth()->user()->role === 'technician')
            <a href="/technician/dashboard" class="nav-link"><i class="fas fa-tasks"></i> My Jobs</a>
            <a href="/technician/profile" class="nav-link"><i class="fas fa-user"></i> Profile</a>
        @elseif(auth()->user()->role === 'manager')
            <a href="/manager/dashboard" class="nav-link"><i class="fas fa-chart-line"></i> Dashboard</a>
            <a href="/service-requests" class="nav-link"><i class="fas fa-list-check"></i> All Requests</a>
            <a href="/technicians" class="nav-link"><i class="fas fa-people"></i> Technicians</a>
            <a href="/invoices" class="nav-link"><i class="fas fa-file-invoice"></i> Invoices</a>
        @elseif(auth()->user()->role === 'costing_officer')
            <a href="/invoices/pending" class="nav-link"><i class="fas fa-hourglass"></i> Pending Verifications</a>
            <a href="/invoices" class="nav-link"><i class="fas fa-check-circle"></i> Verified</a>
        @endif
    </div>

    <!-- Main Content -->
    <div class="main-content">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
