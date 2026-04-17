<?php
include("../config/db.php");
session_start();

$error_message = '';

if(isset($_POST['login'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = md5($_POST['password']);

    $res = $conn->query("SELECT * FROM users WHERE email='$email' AND password='$pass' AND role='admin'");

    if($res->num_rows > 0){
        $_SESSION['admin'] = true;
        header("Location: dashboard.php");
        exit();
    } else {
        $error_message = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | SellCart</title>
    
    <!-- Bootstrap 5 + Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        /* Background Animation */
        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.1)" fill-opacity="1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,165.3C1248,149,1344,107,1392,85.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
            background-size: cover;
            opacity: 0.4;
            pointer-events: none;
        }

        /* Login Card */
        .login-card {
            background: white;
            border-radius: 1.5rem;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 450px;
            margin: 1.5rem;
            animation: fadeInUp 0.5s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Header */
        .login-header {
            background: linear-gradient(135deg, #1e293b, #0f172a);
            padding: 2rem;
            text-align: center;
            position: relative;
        }

        .login-header::before {
            content: "";
            position: absolute;
            top: -20px;
            right: -20px;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, rgba(249,115,22,0.2) 0%, transparent 70%);
            border-radius: 50%;
        }

        .login-header::after {
            content: "";
            position: absolute;
            bottom: -20px;
            left: -20px;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, rgba(239,68,68,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }

        .logo-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #f97316, #ef4444);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            box-shadow: 0 8px 20px rgba(249,115,22,0.3);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 8px 20px rgba(249,115,22,0.3);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 12px 28px rgba(249,115,22,0.5);
            }
        }

        .logo-icon i {
            font-size: 2.2rem;
            color: white;
        }

        .login-header h2 {
            color: white;
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: 0.3rem;
        }

        .login-header p {
            color: rgba(255,255,255,0.7);
            font-size: 0.85rem;
        }

        /* Body */
        .login-body {
            padding: 2rem;
        }

        /* Form Groups */
        .form-group {
            margin-bottom: 1.2rem;
        }

        .input-group-custom {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            z-index: 2;
        }

        .form-control-custom {
            width: 100%;
            padding: 0.8rem 1rem 0.8rem 2.8rem;
            border: 2px solid #e2e8f0;
            border-radius: 0.8rem;
            font-size: 0.95rem;
            transition: all 0.3s;
        }

        .form-control-custom:focus {
            border-color: #f97316;
            outline: none;
            box-shadow: 0 0 0 3px rgba(249,115,22,0.1);
        }

        /* Password Toggle */
        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #94a3b8;
            z-index: 2;
        }

        .password-toggle:hover {
            color: #f97316;
        }

        /* Button */
        .btn-login {
            background: linear-gradient(135deg, #f97316, #ea580c);
            border: none;
            padding: 0.8rem;
            border-radius: 0.8rem;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            color: white;
            transition: all 0.3s;
            margin-top: 0.5rem;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(249,115,22,0.4);
        }

        /* Alert */
        .alert-custom {
            border-radius: 0.8rem;
            padding: 0.8rem;
            margin-bottom: 1.2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: #fee2e2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
            animation: shake 0.3s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .alert-custom i {
            font-size: 1.1rem;
        }

        /* Footer Links */
        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid #e2e8f0;
        }

        .login-footer a {
            color: #f97316;
            text-decoration: none;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        /* Demo Credentials */
        .demo-credentials {
            background: #f8fafc;
            border-radius: 0.8rem;
            padding: 0.8rem;
            margin-top: 1rem;
            text-align: center;
            font-size: 0.75rem;
            color: #64748b;
        }

        .demo-credentials i {
            color: #f97316;
            margin-right: 0.3rem;
        }

        .demo-credentials span {
            font-family: monospace;
            background: #e2e8f0;
            padding: 0.2rem 0.4rem;
            border-radius: 0.3rem;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-card {
                margin: 1rem;
            }
            .login-header {
                padding: 1.5rem;
            }
            .logo-icon {
                width: 55px;
                height: 55px;
            }
            .logo-icon i {
                font-size: 1.8rem;
            }
            .login-header h2 {
                font-size: 1.5rem;
            }
            .login-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>

<div class="login-card">
    <!-- Header -->
    <div class="login-header">
        <div class="logo-icon">
            <i class="fas fa-chart-line"></i>
        </div>
        <h2>Admin Login</h2>
        <p>Access the SellCart Admin Panel</p>
    </div>

    <!-- Body -->
    <div class="login-body">
        <?php if($error_message): ?>
            <div class="alert-custom">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo $error_message; ?></span>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <div class="input-group-custom">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" name="email" class="form-control-custom" 
                           placeholder="Email Address" required>
                </div>
            </div>

            <div class="form-group">
                <div class="input-group-custom">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="password" id="password" class="form-control-custom" 
                           placeholder="Password" required>
                    <i class="fas fa-eye password-toggle" id="togglePassword" onclick="togglePassword()"></i>
                </div>
            </div>

            <button type="submit" name="login" class="btn-login">
                <i class="fas fa-sign-in-alt me-2"></i> Login
            </button>
        </form>

        <div class="demo-credentials">
            <i class="fas fa-info-circle"></i> Demo Credentials:<br>
            Email: <span>admin@example.com</span> | Password: <span>admin123</span>
        </div>

        <div class="login-footer">
            <a href="../user/index.php">
                <i class="fas fa-arrow-left"></i> Back to Store
            </a>
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const password = document.getElementById('password');
        const toggle = document.getElementById('togglePassword');
        
        if(password.type === 'password') {
            password.type = 'text';
            toggle.classList.remove('fa-eye');
            toggle.classList.add('fa-eye-slash');
        } else {
            password.type = 'password';
            toggle.classList.remove('fa-eye-slash');
            toggle.classList.add('fa-eye');
        }
    }

    // Auto hide error after 3 seconds
    setTimeout(function() {
        const alert = document.querySelector('.alert-custom');
        if(alert) {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }
    }, 3000);
</script>

</body>
</html>