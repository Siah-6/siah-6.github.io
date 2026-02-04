<?php
session_start();
require_once 'connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get user information
function getUserInfo($conn, $userId) {
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

// Get user order statistics
function getUserStats($conn, $userId) {
    $stats = [];
    
    // Total orders
    $result = executeQuery("SELECT COUNT(*) as count FROM receipts WHERE user_id = $userId AND completed = 1");
    $stats['total_orders'] = mysqli_fetch_assoc($result)['count'];
    
    // Total spent
    $result = executeQuery("SELECT COALESCE(SUM(total), 0) as total FROM receipts WHERE user_id = $userId AND completed = 1");
    $stats['total_spent'] = mysqli_fetch_assoc($result)['total'];
    
    // Favorite category
    $result = executeQuery("
        SELECT p.category, COUNT(*) as order_count 
        FROM receipt_items ri 
        JOIN products p ON ri.product_id = p.id 
        JOIN receipts r ON ri.receipt_id = r.id 
        WHERE r.user_id = $userId AND r.completed = 1 
        GROUP BY p.category 
        ORDER BY order_count DESC 
        LIMIT 1
    ");
    $favorite = mysqli_fetch_assoc($result);
    $stats['favorite_category'] = $favorite ? $favorite['category'] : 'None';
    
    return $stats;
}

$user = getUserInfo($conn, $_SESSION['user_id']);
$stats = getUserStats($conn, $_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Legend Brews</title>
    
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
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fa;
            color: var(--coffee-dark);
        }
        
        .profile-header {
            background: linear-gradient(135deg, var(--coffee-dark) 0%, var(--coffee-medium) 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            margin: 0 auto 1rem;
            border: 4px solid rgba(255,255,255,0.3);
        }
        
        .profile-name {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .profile-role {
            opacity: 0.8;
            font-size: 1.1rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: rgba(111, 78, 55, 0.1);
            color: var(--coffee-medium);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin: 0 auto 1rem;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--coffee-dark);
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }
        
        .info-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
        }
        
        .info-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--coffee-dark);
            margin-bottom: 1.5rem;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 500;
            color: #666;
            flex: 1;
        }
        
        .info-value {
            font-weight: 600;
            color: var(--coffee-dark);
            margin-left: 2rem;
        }
        
        .btn-edit {
            background: var(--coffee-medium);
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-edit:hover {
            background: var(--coffee-dark);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: var(--coffee-dark);">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-mug-hot"></i>
                Legend Brews
            </a>
            
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-home me-1"></i> Home
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="orders.php">
                            <i class="fas fa-receipt me-1"></i> My Orders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="profile.php">
                            <i class="fas fa-user me-1"></i> Profile
                        </a>
                    </li>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="admin/dashboard.php">
                                <i class="fas fa-tachometer-alt me-1"></i> Admin
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt me-1"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Profile Header -->
    <div class="profile-header">
        <div class="container text-center">
            <div class="profile-avatar">
                <i class="fas fa-user"></i>
            </div>
            <h1 class="profile-name"><?php echo htmlspecialchars($user['username']); ?></h1>
            <p class="profile-role">
                <?php echo ucfirst(htmlspecialchars($user['role'] ?? 'customer')); ?>
                <?php if ($user['role'] === 'admin'): ?>
                    <i class="fas fa-shield-alt ms-2"></i>
                <?php endif; ?>
            </p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div class="stat-value"><?php echo number_format($stats['total_orders']); ?></div>
                <div class="stat-label">Total Orders</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-peso-sign"></i>
                </div>
                <div class="stat-value">₱<?php echo number_format($stats['total_spent'], 2); ?></div>
                <div class="stat-label">Total Spent</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-heart"></i>
                </div>
                <div class="stat-value"><?php echo ucfirst(htmlspecialchars($stats['favorite_category'])); ?></div>
                <div class="stat-label">Favorite Category</div>
            </div>
        </div>
        
        <!-- Account Information -->
        <div class="info-card">
            <h3 class="info-title">
                <i class="fas fa-user-cog me-2"></i>Account Information
            </h3>
            
            <div class="info-item">
                <span class="info-label">Username</span>
                <span class="info-value"><?php echo htmlspecialchars($user['username']); ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Account Type</span>
                <span class="info-value"><?php echo ucfirst(htmlspecialchars($user['role'] ?? 'customer')); ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Member Since</span>
                <span class="info-value"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Account Status</span>
                <span class="info-value">
                    <span class="badge bg-success">Active</span>
                </span>
            </div>
            
            <div class="mt-4">
                <button class="btn-edit" onclick="editProfile()">
                    <i class="fas fa-edit me-2"></i>Edit Profile
                </button>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="info-card">
            <h3 class="info-title">
                <i class="fas fa-bolt me-2"></i>Quick Actions
            </h3>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <a href="orders.php" class="btn btn-outline-primary w-100">
                        <i class="fas fa-receipt me-2"></i>View Order History
                    </a>
                </div>
                <div class="col-md-6 mb-3">
                    <a href="index.php" class="btn btn-outline-success w-100">
                        <i class="fas fa-shopping-cart me-2"></i>Start Shopping
                    </a>
                </div>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <div class="col-md-6 mb-3">
                        <a href="admin/dashboard.php" class="btn btn-outline-warning w-100">
                            <i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard
                        </a>
                    </div>
                <?php endif; ?>
                <div class="col-md-6 mb-3">
                    <button class="btn btn-outline-danger w-100" onclick="confirmLogout()">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Edit profile (placeholder function)
        function editProfile() {
            alert('Profile editing would be implemented here. For now, this is a demo.');
        }
        
        // Confirm logout
        function confirmLogout() {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = 'logout.php';
            }
        }
    </script>
</body>
</html>
