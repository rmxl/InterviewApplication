<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
      :root {
          --primary-blue: #3498db;
          --dark-blue: #2980b9;
          --light-blue: #e3f2fd;
          --accent-blue: #1565c0;
          --text-dark: #2c3e50;
      }
      
      body {
          background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
          min-height: 100vh;
          display: flex;
          justify-content: center;
          align-items: center;
          font-family: 'Segoe UI', system-ui, sans-serif;
      }
      
      .register-container {
          width: 380px;
          padding: 0;
          border-radius: 16px;
          overflow: hidden;
          box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
          background-color: white;
          transition: transform 0.3s ease, box-shadow 0.3s ease;
      }
      
      .register-container:hover {
          transform: translateY(-5px);
          box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
      }
      
      .register-header {
          background: linear-gradient(135deg, var(--primary-blue) 0%, var(--dark-blue) 100%);
          color: white;
          padding: 30px 0;
          text-align: center;
          border-radius: 10px 10px 0 0;
          position: relative;
      }
      
      .register-header h3 {
          margin: 0;
          font-weight: 600;
          letter-spacing: 0.5px;
          font-size: 24px;
      }
      
      .register-header .logo-icon {
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
      
      .register-header .logo-icon i {
          color: var(--primary-blue);
          font-size: 28px;
      }
      
      .register-body {
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
          border-color: var(--primary-blue);
          box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
      }
      
      .form-label {
          color: var(--text-dark);
          font-weight: 500;
          margin-bottom: 8px;
          font-size: 14px;
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
      
      .register-btn {
          background: linear-gradient(135deg, var(--primary-blue) 0%, var(--dark-blue) 100%);
          border: none;
          border-radius: 8px;
          padding: 12px;
          font-weight: 600;
          letter-spacing: 0.5px;
          box-shadow: 0 4px 12px rgba(52, 152, 219, 0.35);
          transition: all 0.3s ease;
          margin-top: 10px;
          width: 100%;
      }
      
      .register-btn:hover {
          background: linear-gradient(135deg, var(--dark-blue) 0%, var(--accent-blue) 100%);
          box-shadow: 0 6px 15px rgba(52, 152, 219, 0.45);
          transform: translateY(-2px);
      }
      
      .register-btn:active {
          transform: translateY(1px);
          box-shadow: 0 2px 8px rgba(52, 152, 219, 0.4);
      }
      
      .alert {
          border-radius: 8px;
          border-left: 4px solid #dc3545;
          background-color: #f8d7da;
          color: #721c24;
          padding: 12px 15px;
          font-size: 14px;
          margin-bottom: 20px;
      }
      
      .login-link {
          text-align: center;
          margin-top: 20px;
          color: var(--text-dark);
          font-size: 14px;
      }
      
      .login-link a {
          color: var(--primary-blue);
          text-decoration: none;
          font-weight: 600;
          transition: color 0.2s ease;
      }
      
      .login-link a:hover {
          color: var(--dark-blue);
          text-decoration: underline;
      }
      
      @media (max-width: 480px) {
          .register-container {
              width: 90%;
              max-width: 380px;
          }
          
          .register-body {
              padding: 20px;
          }
      }
  </style>
</head>
<body>
  <div class="register-container">
      <div class="register-header">
          <div class="logo-icon">
              <i class="fas fa-user-plus"></i>
          </div>
          <h3>Create Account</h3>
      </div>
      <div class="register-body">
          @if(session('error'))
              <div class="alert">
                  <i class="fas fa-exclamation-circle me-2"></i>
                  {{ session('error') }}
              </div>
          @endif

          <form action="{{ route('register') }}" method="POST">
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
                  </div>
              </div>
              <div class="form-group">
                  <label for="password_confirmation" class="form-label">Confirm Password</label>
                  <div class="input-group">
                      <i class="fas fa-key form-icon"></i>
                      <input type="password" class="form-control form-control-with-icon" id="password_confirmation" name="password_confirmation" placeholder="Re-enter your password" required>
                  </div>
              </div>
              <button type="submit" class="btn register-btn text-white">
                  <i class="fas fa-user-plus me-2"></i>Sign Up
              </button>
              <div class="login-link">
                  Already have an account? <a href="{{ route('login') }}">Log in</a>
              </div>
          </form>
      </div>
  </div>
</body>
</html>
