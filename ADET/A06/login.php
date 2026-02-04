<?php
session_start();
require_once 'connect.php';

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Handle cart data from URL (for checkout flow)
if (isset($_GET['cart']) && !empty($_GET['cart'])) {
    try {
        $cartData = json_decode(base64_decode($_GET['cart']), true);
        if (isset($cartData['cartItems']) && is_array($cartData['cartItems'])) {
            $_SESSION['cartItems'] = $cartData['cartItems'];
            $_SESSION['total'] = $cartData['total'] ?? 0;
        }
    } catch (Exception $e) {
        // Invalid cart data, ignore
    }
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    $errors = [];
    
    // Validation
    if (empty($username)) {
        $errors[] = "Username is required";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    }
    
    if (empty($errors)) {
        // Check user credentials
        $query = "SELECT * FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($user = mysqli_fetch_assoc($result)) {
            // Verify password (assuming plain text for demo, but should use password_hash)
            if ($password === $user['password']) {
                // Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'] ?? 'customer';
                
                // Check for redirect parameter
                $redirect = $_GET['redirect'] ?? '';
                
                // Redirect based on role and redirect parameter
                if ($_SESSION['role'] === 'admin') {
                    header('Location: admin/dashboard.php');
                } else {
                    if ($redirect === 'checkout') {
                        // Check if there's cart data in session from before login
                        if (isset($_SESSION['cartItems']) && !empty($_SESSION['cartItems'])) {
                            header('Location: checkout.php');
                        } else {
                            // Redirect to shop with message to add items
                            $_SESSION['info_message'] = 'Please add items to your cart before checkout.';
                            header('Location: index.php');
                        }
                    } else {
                        header('Location: index.php');
                    }
                }
                exit();
            } else {
                $errors[] = "Invalid password";
            }
        } else {
            $errors[] = "User not found";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Legend Brews</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --coffee-dark: #2c1810;
            --coffee-medium: #6f4e37;
            --coffee-light: #a67c52;
            --coffee-accent: #c17d4a;
            --coffee-cream: #f5e6d3;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--coffee-cream) 0%, #faf6f2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            overflow: hidden;
            width: 100%;
            max-width: 900px;
            min-height: 600px;
            display: flex;
            margin: 2rem;
        }
        
        .login-image {
            flex: 1;
            background: linear-gradient(135deg, var(--coffee-dark) 0%, var(--coffee-medium) 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .login-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><text y="50" font-size="100" fill="rgba(255,255,255,0.05)">☕</text></svg>');
            background-size: 100px 100px;
            animation: float 20s linear infinite;
        }
        
        @keyframes float {
            0% { transform: translateX(0) translateY(0); }
            100% { transform: translateX(-100px) translateY(-100px); }
        }
        
        .login-image h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            position: relative;
            z-index: 1;
        }
        
        .login-image p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 2rem;
            position: relative;
            z-index: 1;
        }
        
        .login-features {
            list-style: none;
            padding: 0;
            margin: 0;
            position: relative;
            z-index: 1;
        }
        
        .login-features li {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
            font-size: 0.95rem;
        }
        
        .login-features i {
            width: 30px;
            height: 30px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-form {
            flex: 1;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .login-header h3 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--coffee-dark);
            margin-bottom: 0.5rem;
        }
        
        .login-header p {
            color: #666;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--coffee-dark);
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.8rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--coffee-medium);
            box-shadow: 0 0 0 3px rgba(111, 78, 55, 0.1);
            outline: none;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-group i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            z-index: 1;
        }
        
        .input-group .form-control {
            padding-left: 2.5rem;
        }
        
        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #999;
            cursor: pointer;
            z-index: 1;
        }
        
        .btn-login {
            background: linear-gradient(135deg, var(--coffee-dark), var(--coffee-medium));
            color: white;
            border: none;
            border-radius: 10px;
            padding: 1rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(111, 78, 55, 0.3);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            padding: 1rem 1.5rem;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
        }
        
        .divider {
            text-align: center;
            margin: 2rem 0;
            position: relative;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e9ecef;
        }
        
        .divider span {
            background: white;
            padding: 0 1rem;
            color: #666;
        }
        
        .social-login {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .social-btn {
            flex: 1;
            padding: 0.8rem;
            border: 2px solid #e9ecef;
            background: white;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .social-btn:hover {
            border-color: var(--coffee-light);
            background: var(--coffee-cream);
        }
        
        .register-link {
            text-align: center;
            color: #666;
        }
        
        .register-link a {
            color: var(--coffee-medium);
            text-decoration: none;
            font-weight: 600;
        }
        
        .register-link a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                margin: 1rem;
            }
            
            .login-image {
                padding: 2rem;
                min-height: 200px;
            }
            
            .login-image h2 {
                font-size: 2rem;
            }
            
            .login-form {
                padding: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Side - Branding -->
        <div class="login-image">
            <h2>Legend Brews</h2>
            <p>Your Premium Coffee Shop Experience</p>
            
            <ul class="login-features">
                <li>
                    <i class="fas fa-shield-alt"></i>
                    <span>Secure Authentication</span>
                </li>
                <li>
                    <i class="fas fa-coffee"></i>
                    <span>Order Tracking</span>
                </li>
                <li>
                    <i class="fas fa-heart"></i>
                    <span>Loyalty Rewards</span>
                </li>
                <li>
                    <i class="fas fa-mobile-alt"></i>
                    <span>Mobile Friendly</span>
                </li>
            </ul>
        </div>
        
        <!-- Right Side - Login Form -->
        <div class="login-form">
            <div class="login-header">
                <h3>Welcome Back</h3>
                <p>Login to your account to continue</p>
            </div>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                        <div><?php echo htmlspecialchars($error); ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group">
                        <i class="fas fa-user"></i>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                               placeholder="Enter your username" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Enter your password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye" id="passwordIcon"></i>
                        </button>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember">
                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>
                        </div>
                        <a href="#" class="text-decoration-none">Forgot password?</a>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Login to Account
                </button>
            </form>
            
            <div class="divider">
                <span>OR</span>
            </div>
            
            <div class="social-login">
                <button class="social-btn" onclick="socialLogin('google')">
                    <i class="fab fa-google"></i>
                    Google
                </button>
                <button class="social-btn" onclick="socialLogin('facebook')">
                    <i class="fab fa-facebook-f"></i>
                    Facebook
                </button>
            </div>
            
            <div class="register-link">
                Don't have an account? <a href="register.php?redirect=<?php echo urlencode($_GET['redirect'] ?? ''); ?>">Create one here</a>
                <br><br>
                <a href="index.php" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-store me-2"></i>Continue Shopping as Guest
                </a>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }
        
        // Social login (simulation)
        function socialLogin(provider) {
            alert(`${provider} login would be implemented here with OAuth integration`);
        }
        
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;
            
            if (!username || !password) {
                e.preventDefault();
                alert('Please fill in all fields');
                return false;
            }
        });
        
        // Auto-focus on username field
        document.getElementById('username').focus();
    </script>
</body>
</html>
