<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    
    <!-- Font Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS eksternal -->
    <link rel="stylesheet" href="<?= base_url('assets/css/login_style.css'); ?>">
</head>
<body class="light-mode">
    <div class="container">
        <div class="login-card">
            <div class="theme-toggle">
                <button id="theme-button" aria-label="Toggle dark mode">
                    <span class="moon">🌙</span>
                    <span class="sun">☀️</span>
                </button>
            </div>
            
            <div class="login-header">
                <h1>Welcome Back</h1>
                <p>Sign in to your account</p>
            </div>
            
            <div class="login-form">
                <form action="<?= base_url('/auth') ?>" method="post">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                        <div class="password-toggle">
                            <button type="button" id="toggle-password" aria-label="Show password">
                                <span class="show-password">👁️</span>
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-options">
                        <div class="remember-me">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Remember me</label>
                        </div>
                        <a href="#" class="forgot-password">Forgot Password?</a>
                    </div>
                    
                    <button type="submit" class="login-button">Sign In</button>
                </form>
            </div>
            
            <div class="login-footer">
                <p>Don't have an account? <a href="#">Sign Up</a></p>
            </div>
        </div>
    </div>
    
    <script src="<?= base_url('assets/js/login_script.js'); ?>"></script>
</body>
</html>