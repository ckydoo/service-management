<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Request Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .landing-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
        }

        .landing-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 500px;
        }

        .landing-left {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .landing-left h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .landing-left p {
            font-size: 1.1rem;
            opacity: 0.95;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .features {
            list-style: none;
            padding: 0;
        }

        .features li {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            font-size: 0.95rem;
        }

        .features li i {
            color: #fbbf24;
            margin-right: 12px;
            font-size: 1.3rem;
        }

        .landing-right {
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .landing-right h2 {
            font-size: 1.8rem;
            margin-bottom: 10px;
            color: #333;
            font-weight: 600;
        }

        .landing-right p {
            color: #666;
            margin-bottom: 40px;
            font-size: 0.95rem;
        }

        .btn-group-landing {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn-landing {
            padding: 12px 30px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .btn-register {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }

        .btn-register:hover {
            background: #f0f0f0;
            transform: translateY(-2px);
        }

        .divider-text {
            text-align: center;
            color: #999;
            margin: 30px 0 20px 0;
            font-size: 0.9rem;
        }

        .demo-credentials {
            background: #f5f5f5;
            border-left: 4px solid #667eea;
            padding: 15px;
            border-radius: 5px;
            margin-top: 30px;
            font-size: 0.85rem;
        }

        .demo-credentials h5 {
            color: #333;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .demo-credentials p {
            margin: 5px 0;
            color: #666;
        }

        .role-badge {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 0.8rem;
            margin-left: 5px;
        }

        @media (max-width: 768px) {
            .landing-content {
                grid-template-columns: 1fr;
                min-height: auto;
            }

            .landing-left {
                padding: 40px 30px;
            }

            .landing-right {
                padding: 40px 30px;
            }

            .landing-left h1 {
                font-size: 2rem;
            }

            .landing-right h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="landing-container">
        <div class="landing-content">
            <!-- Left Section -->
            <div class="landing-left">
                <h1>Welcome to SRMS</h1>
                <p>Service Request Management System</p>
                <p style="font-size: 0.95rem; margin-top: 20px;">Streamline your service operations with our comprehensive management platform. Manage requests, track technicians, and optimize your workflow.</p>

                <ul class="features">
                    <li>
                        <i class="fas fa-check-circle"></i>
                        Real-time job tracking
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        Technician assignment
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        Invoice management
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        Performance analytics
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        Multi-role support
                    </li>
                </ul>
            </div>

            <!-- Right Section -->
            <div class="landing-right">
                <h2>Get Started</h2>
                <p>Sign in to your account or create a new one</p>

                <div class="btn-group-landing">
                    <a href="{{ route('login') }}" class="btn-landing btn-login">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                    <a href="{{ route('register') }}" class="btn-landing btn-register">
                        <i class="fas fa-user-plus"></i> Register
                    </a>
                </div>

                <div class="divider-text">Demo Credentials</div>

                <div class="demo-credentials">
                    <h5>Test Accounts:</h5>
                    <p><strong>Manager:</strong> manager@example.com <span class="role-badge">Manager</span></p>
                    <p><strong>Tech:</strong> technician@example.com <span class="role-badge">Technician</span></p>
                    <p><strong>Capturer:</strong> capturer@example.com <span class="role-badge">Data Capturer</span></p>
                    <p style="margin-top: 10px;"><strong>Password:</strong> (See seeder)</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</body>
</html>
