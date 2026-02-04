<?php
session_start();
require_once 'connect.php';

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Handle registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    
    $errors = [];
    
    // Validation
    if (empty($username)) {
        $errors[] = "Username is required";
    } elseif (strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = "Username can only contain letters, numbers, and underscores";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }
    
    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match";
    }
    
    // Check if username already exists
    if (empty($errors)) {
        $query = "SELECT id FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_fetch_assoc($result)) {
            $errors[] = "Username already exists";
        }
    }
    
    // Register user if no errors
    if (empty($errors)) {
        $query = "INSERT INTO users (username, password, role, created_at) 
                  VALUES (?, ?, 'customer', NOW())";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ss", $username, $password);
        
        if (mysqli_stmt_execute($stmt)) {
            // Get the newly created user ID
            $userId = mysqli_insert_id($conn);
            
            // Auto-login the user
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = 'customer';
            
            // Set success message
            $_SESSION['success_message'] = "Account created successfully! Welcome to Legend Brews!";
            
            // Redirect to checkout if coming from there, otherwise go to index
            $redirect = $_GET['redirect'] ?? '';
            if ($redirect === 'checkout') {
                header('Location: checkout.php');
            } else {
                header('Location: index.php');
            }
            exit();
        } else {
            $errors[] = "Registration failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Legend Brews</title>
    
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
            padding: 2rem 0;
        }
        
        .register-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            padding: 3rem;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .register-header h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--coffee-dark);
            margin-bottom: 1rem;
        }
        
        .register-header p {
            color: #666;
            font-size: 1.1rem;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--coffee-dark);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .form-label .required {
            color: #dc3545;
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
        
        .form-control.is-invalid {
            border-color: #dc3545;
        }
        
        .invalid-feedback {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        
        .password-strength {
            margin-top: 0.5rem;
        }
        
        .password-strength-bar {
            height: 4px;
            border-radius: 2px;
            background: #e9ecef;
            overflow: hidden;
        }
        
        .password-strength-fill {
            height: 100%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }
        
        .password-strength-fill.weak {
            width: 33%;
            background: #dc3545;
        }
        
        .password-strength-fill.medium {
            width: 66%;
            background: #ffc107;
        }
        
        .password-strength-fill.strong {
            width: 100%;
            background: #28a745;
        }
        
        .password-strength-text {
            font-size: 0.8rem;
            margin-top: 0.25rem;
        }
        
        .btn-register {
            background: linear-gradient(135deg, var(--coffee-dark), var(--coffee-medium));
            color: white;
            border: none;
            border-radius: 10px;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(111, 78, 55, 0.3);
        }
        
        .btn-register:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
        }
        
        .login-link {
            text-align: center;
            margin-top: 2rem;
            color: #666;
        }
        
        .login-link a {
            color: var(--coffee-medium);
            text-decoration: none;
            font-weight: 600;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        .terms-checkbox {
            margin-bottom: 2rem;
        }
        
        .terms-checkbox .form-check {
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
        }
        
        .terms-checkbox .form-check-input {
            margin-top: 0.25rem;
        }
        
        .terms-checkbox .form-check-label {
            font-size: 0.9rem;
            color: #666;
            line-height: 1.4;
        }
        
        .terms-checkbox .form-check-label a {
            color: var(--coffee-medium);
            text-decoration: none;
        }
        
        .terms-checkbox .form-check-label a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .register-container {
                padding: 2rem 1.5rem;
                margin: 1rem;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .register-header h2 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h2>Create Account</h2>
            <p>Join Legend Brews for an amazing coffee experience</p>
        </div>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <strong>Registration Error:</strong>
                <ul class="mb-0 mt-2">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="register.php" id="registerForm">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" placeholder="Choose a username" required>
                </div>
                <small class="text-muted">Username must be at least 3 characters and contain only letters, numbers, and underscores</small>
                <div class="invalid-feedback">Please enter a valid username</div>
            </div>
            
            
            <div class="form-group">
                <label for="password" class="form-label">
                    <i class="fas fa-lock"></i>
                    Password <span class="required">*</span>
                </label>
                <input type="password" class="form-control" id="password" name="password" 
                       placeholder="Create a password" required>
                <div class="password-strength">
                    <div class="password-strength-bar">
                        <div class="password-strength-fill" id="passwordStrengthFill"></div>
                    </div>
                    <div class="password-strength-text" id="passwordStrengthText"></div>
                </div>
                <small class="text-muted">Password must be at least 6 characters</small>
                <div class="invalid-feedback">Please enter a password</div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password" class="form-label">
                    <i class="fas fa-lock"></i>
                    Confirm Password <span class="required">*</span>
                </label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                       placeholder="Confirm your password" required>
                <div class="invalid-feedback">Passwords do not match</div>
            </div>
            
            <div class="terms-checkbox">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                    <label class="form-check-label" for="terms">
                        I agree to the <a href="#" onclick="showTerms()">Terms of Service</a> and <a href="#" onclick="showPrivacy()">Privacy Policy</a>
                    </label>
                </div>
            </div>
            
            <button type="submit" class="btn btn-register" id="registerBtn">
                <i class="fas fa-user-plus me-2"></i>
                Create Account
            </button>
        </form>
        
        <div class="login-link">
            Already have an account? <a href="login.php">Login here</a>
            <br><br>
            <a href="index.php" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-store me-2"></i>Continue Shopping as Guest
            </a>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Password strength checker
        function checkPasswordStrength(password) {
            let strength = 0;
            
            if (password.length >= 6) strength++;
            if (password.length >= 10) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;
            
            const strengthFill = document.getElementById('passwordStrengthFill');
            const strengthText = document.getElementById('passwordStrengthText');
            
            if (strength <= 2) {
                strengthFill.className = 'password-strength-fill weak';
                strengthText.textContent = 'Weak password';
                strengthText.style.color = '#dc3545';
            } else if (strength <= 4) {
                strengthFill.className = 'password-strength-fill medium';
                strengthText.textContent = 'Medium strength';
                strengthText.style.color = '#ffc107';
            } else {
                strengthFill.className = 'password-strength-fill strong';
                strengthText.textContent = 'Strong password';
                strengthText.style.color = '#28a745';
            }
        }
        
        // Real-time validation
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            checkPasswordStrength(password);
            
            // Check if passwords match
            const confirmPassword = document.getElementById('confirm_password').value;
            if (confirmPassword) {
                validatePasswordMatch();
            }
        });
        
        document.getElementById('confirm_password').addEventListener('input', validatePasswordMatch);
        
        function validatePasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const confirmInput = document.getElementById('confirm_password');
            
            if (confirmPassword && password !== confirmPassword) {
                confirmInput.classList.add('is-invalid');
                confirmInput.nextElementSibling.textContent = 'Passwords do not match';
            } else {
                confirmInput.classList.remove('is-invalid');
            }
        }
        
        // Username validation
        document.getElementById('username').addEventListener('input', function() {
            const username = this.value;
            const usernameRegex = /^[a-zA-Z0-9_]+$/;
            
            if (username && !usernameRegex.test(username)) {
                this.classList.add('is-invalid');
                this.nextElementSibling.textContent = 'Username can only contain letters, numbers, and underscores';
            } else if (username && username.length < 3) {
                this.classList.add('is-invalid');
                this.nextElementSibling.textContent = 'Username must be at least 3 characters';
            } else {
                this.classList.remove('is-invalid');
            }
        });
        
        // Email validation
        document.getElementById('email').addEventListener('input', function() {
            const email = this.value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (email && !emailRegex.test(email)) {
                this.classList.add('is-invalid');
                this.nextElementSibling.textContent = 'Please enter a valid email address';
            } else {
                this.classList.remove('is-invalid');
            }
        });
        
        // Form submission
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const terms = document.getElementById('terms').checked;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match');
                return false;
            }
            
            if (!terms) {
                e.preventDefault();
                alert('Please accept the terms of service');
                return false;
            }
            
            // Disable submit button to prevent double submission
            const submitBtn = document.getElementById('registerBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating Account...';
        });
        
        // Show terms (simulation)
        function showTerms() {
            alert('Terms of Service would be displayed here in a modal or separate page');
        }
        
        // Show privacy policy (simulation)
        function showPrivacy() {
            alert('Privacy Policy would be displayed here in a modal or separate page');
        }
    </script>
</body>
</html>
