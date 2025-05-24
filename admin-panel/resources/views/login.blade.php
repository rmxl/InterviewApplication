<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-red: #e74c3c;
            --dark-red: #c0392b;
            --light-red: #fadbd8;
            --accent-red: #922b21;
            --text-dark: #2c3e50;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #f1c1bb 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }
        
        .login-container {
            width: 380px;
            padding: 0;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            background-color: white;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .login-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }
        
        .login-header {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
            color: white;
            padding: 30px 0;
            text-align: center;
            border-radius: 10px 10px 0 0;
            position: relative;
        }
        
        .login-header h3 {
            margin: 0;
            font-weight: 600;
            letter-spacing: 0.5px;
            font-size: 24px;
        }
        
        .login-header .logo-icon {
            background-color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .login-header .logo-icon i {
            color: var(--primary-red);
            font-size: 28px;
        }
        
        .login-body {
            padding: 30px;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary-red);
            box-shadow: 0 0 0 0.2rem rgba(231, 76, 60, 0.25);
        }
        
        .form-label {
            color: var(--text-dark);
            font-weight: 500;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-group-text {
            background-color: transparent;
            border: none;
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            color: #6c757d;
            cursor: pointer;
        }
        
        .login-btn {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-red) 100%);
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px rgba(231, 76, 60, 0.35);
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .login-btn:hover {
            background: linear-gradient(135deg, var(--dark-red) 0%, var(--accent-red) 100%);
            box-shadow: 0 6px 15px rgba(231, 76, 60, 0.45);
            transform: translateY(-2px);
        }
        
        .login-btn:active {
            transform: translateY(1px);
            box-shadow: 0 2px 8px rgba(231, 76, 60, 0.4);
        }
        
        .alert {
            border-radius: 8px;
            border-left: 4px solid #e74c3c;
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px 15px;
            font-size: 14px;
            margin-bottom: 20px;
        }
        
        .form-group {
            position: relative;
            margin-bottom: 25px;
        }
        
        .form-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 10;
        }
        
        .form-control-with-icon {
            padding-left: 40px;
        }
        
        .forgot-password {
            text-align: right;
            margin-bottom: 20px;
        }
        
        .forgot-password a {
            color: var(--primary-red);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.2s ease;
        }
        
        .forgot-password a:hover {
            color: var(--dark-red);
            text-decoration: underline;
        }
        
        .register-link {
            text-align: center;
            margin-top: 20px;
            color: var(--text-dark);
            font-size: 14px;
        }
        
        .register-link a {
            color: var(--primary-red);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }
        
        .register-link a:hover {
            color: var(--dark-red);
            text-decoration: underline;
        }
        
        @media (max-width: 480px) {
            .login-container {
                width: 90%;
                max-width: 380px;
            }
            
            .login-body {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="logo-icon">
                <i class="fas fa-lock"></i>
            </div>
            <h3>Welcome Back</h3>
        </div>
        <div class="login-body">
            @if(session('error'))
                <div class="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group">
                        <i class="fas fa-user form-icon"></i>
                        <input type="text" class="form-control form-control-with-icon" id="username" name="username" placeholder="Enter your username" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <i class="fas fa-key form-icon"></i>
                        <input type="password" class="form-control form-control-with-icon" id="password" name="password" placeholder="Enter your password" required>
                        <span class="input-group-text" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </span>
                    </div>
                </div>
                <button type="submit" class="btn login-btn text-white w-100">
                    <i class="fas fa-sign-in-alt me-2"></i>Log In
                </button>
                <div class="register-link">
                    Don't have an account? <a href="{{ route('register') }}">Sign up</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>