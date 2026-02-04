<?php
// Completely disable all error reporting for production
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 0);

session_start();
require_once 'connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=checkout');
    exit();
}

// Get cart items from session
$cartItems = $_SESSION['cartItems'] ?? [];

// If cart is empty, try to get it from URL parameter (backup method)
if (empty($cartItems) && isset($_GET['cart'])) {
    try {
        $cartData = json_decode(base64_decode(urldecode($_GET['cart'])), true);
        if (isset($cartData['cartItems']) && is_array($cartData['cartItems'])) {
            $cartItems = $cartData['cartItems'];
            // Save to session for future use
            $_SESSION['cartItems'] = $cartItems;
            $_SESSION['total'] = $cartData['total'] ?? 0;
        }
    } catch (Exception $e) {
        // Invalid cart data, ignore
        $cartItems = [];
    }
}

// If still empty, try to get it from the API
if (empty($cartItems)) {
    // Try to load cart from session API
    if (isset($_SESSION['cartItems']) && is_array($_SESSION['cartItems'])) {
        $cartItems = $_SESSION['cartItems'];
    }
}

// Debug: Show cart contents (commented out - working properly)
// echo '<div style="background: #f0f0f0; padding: 10px; margin: 10px; border-radius: 5px;">';
// echo '<strong>Debug Info:</strong><br>';
// echo 'Session cartItems count: ' . count($_SESSION['cartItems'] ?? []) . '<br>';
// echo 'URL cart parameter: ' . (isset($_GET['cart']) ? 'Yes' : 'No') . '<br>';
// echo 'Final cartItems count: ' . count($cartItems) . '<br>';
// echo '</div>';

$total = 0;
foreach ($cartItems as $item) {
    // Handle both data structures (frontend and backend)
    $price = isset($item['product']['price']) ? $item['product']['price'] : $item['price'];
    $name = isset($item['product']['name']) ? $item['product']['name'] : $item['name'];
    $quantity = isset($item['quantity']) ? $item['quantity'] : 1;
    
    if (isset($price) && isset($quantity)) {
        $itemTotal = $price * $quantity;
        $total += $itemTotal;
        
        // Debug: Show each item calculation
        error_log("Item: " . $name . " - Price: " . $price . " x Qty: " . $quantity . " = " . $itemTotal);
    }
}

// Debug: Show final total
error_log("Final calculated total: " . $total);

// Debug: Show calculated total (commented out - working properly)
// echo '<div style="background: #f0f0f0; padding: 10px; margin: 10px; border-radius: 5px;">';
// echo '<strong>Debug Info:</strong><br>';
// echo 'Cart items count: ' . count($cartItems) . '<br>';
// echo 'Calculated subtotal: ₱' . number_format($total, 2) . '<br>';
// echo 'Total with fees (₱70): ₱' . number_format($total + 70, 2) . '<br>';
// echo '</div>';

// Handle checkout submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Calculate total with fees
    $totalWithFees = $total + 70; // 50 delivery + 20 service
    
    // Create receipt
    $query = "INSERT INTO receipts (user_id, total, completed, created_at) VALUES (?, ?, 1, NOW())";
    $conn = getConnection(); // Use the getConnection function
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "id", $_SESSION['user_id'], $totalWithFees);
    
    if (mysqli_stmt_execute($stmt)) {
        $receiptId = mysqli_insert_id($conn);
        
        // Add receipt items
        foreach ($cartItems as $item) {
            // Handle both data structures
            $productId = isset($item['product']['id']) ? $item['product']['id'] : $item['id'];
            $quantity = isset($item['quantity']) ? $item['quantity'] : 1;
            $price = isset($item['product']['price']) ? $item['product']['price'] : $item['price'];
            
            $query = "INSERT INTO receipt_items (receipt_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "iiid", $receiptId, $productId, $quantity, $price);
            mysqli_stmt_execute($stmt);
        }
        
        // Clear cart
        $_SESSION['cartItems'] = [];
        $_SESSION['total'] = 0;
        
        // Set success message
        $_SESSION['success_message'] = "Order placed successfully! Your order #" . str_pad($receiptId, 6, '0', STR_PAD_LEFT) . " has been received.";
        
        // Show success message on checkout page instead of redirecting
        $orderCompleted = true;
        $orderReceiptId = str_pad($receiptId, 6, '0', STR_PAD_LEFT);
    } else {
        $orderError = "Failed to place order. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Legend Brews</title>
    
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
        
        .checkout-header {
            background: linear-gradient(135deg, var(--coffee-dark) 0%, var(--coffee-medium) 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        
        .checkout-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        
        .checkout-grid {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .checkout-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
        }
        
        .card-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--coffee-dark);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .form-label {
            font-weight: 500;
            color: var(--coffee-dark);
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.75rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--coffee-medium);
            box-shadow: 0 0 0 0.2rem rgba(111, 78, 55, 0.25);
        }
        
        .cart-item {
            display: flex;
            justify-content: between;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .cart-item:last-child {
            border-bottom: none;
        }
        
        .cart-item-info {
            flex: 1;
        }
        
        .cart-item-name {
            font-weight: 600;
            color: var(--coffee-dark);
        }
        
        .cart-item-price {
            color: #666;
            font-size: 0.9rem;
        }
        
        .cart-item-quantity {
            background: var(--coffee-light);
            color: white;
            padding: 0.3rem 0.6rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .order-summary {
            position: sticky;
            top: 2rem;
        }
        
        .summary-row {
            display: flex;
            justify-content: between;
            align-items: center;
            padding: 0.5rem 0;
        }
        
        .summary-row.total {
            border-top: 2px solid var(--coffee-dark);
            padding-top: 1rem;
            margin-top: 1rem;
            font-weight: 700;
            font-size: 1.2rem;
        }
        
        .btn-checkout {
            background: var(--coffee-medium);
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 10px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
        }
        
        .btn-checkout:hover {
            background: var(--coffee-dark);
            transform: translateY(-2px);
        }
        
        .empty-cart {
            text-align: center;
            padding: 3rem;
            color: #666;
        }
        
        .empty-cart i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 1rem;
        }
        
        @media (max-width: 768px) {
            .checkout-grid {
                grid-template-columns: 1fr;
            }
            
            .order-summary {
                position: static;
            }
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
                        <a class="nav-link" href="profile.php">
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

    <!-- Checkout Header -->
    <div class="checkout-header">
        <div class="container text-center">
            <h1 class="mb-2">Checkout</h1>
            <p class="mb-0">Complete your order</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="checkout-container">
        <!-- Success Modal -->
        <?php if (isset($orderCompleted) && $orderCompleted): ?>
            <div class="checkout-card">
                <form method="POST" action="checkout.php">
                    <div class="checkout-grid">
                        <div>
                            <div class="checkout-card">
                                <h3 class="card-title">
                                    <i class="fas fa-shopping-cart"></i>
                                    Order Summary
                                </h3>
                                <div class="text-center py-4">
                                    <div class="success-icon mb-3">
                                        <i class="fas fa-check-circle" style="font-size: 3rem; color: #28a745;"></i>
                                    </div>
                                    <h4 class="mb-2">Order Completed Successfully!</h4>
                                    <p class="text-muted mb-3">Order #<?php echo $orderReceiptId; ?></p>
                                    <p class="mb-4">Thank you for your order. We'll prepare it right away!</p>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="index.php" class="btn btn-primary">
                                            <i class="fas fa-home me-2"></i>Continue Shopping
                                        </a>
                                        <a href="orders.php" class="btn btn-outline-primary">
                                            <i class="fas fa-receipt me-2"></i>View Orders
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        <?php elseif (isset($orderError)): ?>
            <div class="checkout-card">
                <div class="text-center py-5">
                    <div class="error-icon mb-4">
                        <i class="fas fa-exclamation-circle" style="font-size: 4rem; color: #dc3545;"></i>
                    </div>
                    <h2 class="mb-3">Order Failed</h2>
                    <p class="text-danger mb-4"><?php echo htmlspecialchars($orderError); ?></p>
                    <a href="checkout.php" class="btn btn-primary">
                        <i class="fas fa-redo me-2"></i>Try Again
                    </a>
                </div>
            </div>
        <?php elseif (empty($cartItems)): ?>
            <div class="checkout-card">
                <div class="empty-cart">
                    <i class="fas fa-shopping-cart"></i>
                    <h3>Your cart is empty</h3>
                    <p>Add some items to your cart before checkout</p>
                    <a href="index.php" class="btn btn-primary">
                        <i class="fas fa-shopping-cart me-2"></i>Continue Shopping
                    </a>
                </div>
            </div>
        <?php else: ?>
            <form method="POST" action="checkout.php">
                <div class="checkout-grid">
                    <!-- Left Column -->
                    <div>
                        <!-- Customer Information -->
                        <div class="checkout-card">
                            <h3 class="card-title">
                                <i class="fas fa-user"></i>
                                Customer Information
                            </h3>
                            
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($_SESSION['username']); ?>" readonly>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" placeholder="+63 912 345 6789">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Delivery Address</label>
                                <textarea class="form-control" rows="3" placeholder="Enter your delivery address" required></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Order Notes (Optional)</label>
                                <textarea class="form-control" rows="2" placeholder="Special instructions or notes"></textarea>
                            </div>
                        </div>
                        
                        <!-- Payment Method -->
                        <div class="checkout-card">
                            <h3 class="card-title">
                                <i class="fas fa-credit-card"></i>
                                Payment Method
                            </h3>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment" id="cod" value="cod" checked>
                                    <label class="form-check-label" for="cod">
                                        <i class="fas fa-money-bill-wave me-2"></i>
                                        Cash on Delivery
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment" id="gcash" value="gcash">
                                    <label class="form-check-label" for="gcash">
                                        <i class="fas fa-mobile-alt me-2"></i>
                                        GCash
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment" id="card" value="card">
                                    <label class="form-check-label" for="card">
                                        <i class="fas fa-credit-card me-2"></i>
                                        Credit/Debit Card
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column - Order Summary -->
                    <div class="order-summary">
                        <div class="checkout-card">
                            <h3 class="card-title">
                                <i class="fas fa-shopping-cart"></i>
                                Order Summary
                            </h3>
                            
                            <!-- Cart Items -->
                            <?php foreach ($cartItems as $item): ?>
                                <?php
                                    // Handle both data structures
                                    $name = isset($item['product']['name']) ? $item['product']['name'] : $item['name'];
                                    $price = isset($item['product']['price']) ? $item['product']['price'] : $item['price'];
                                    $quantity = isset($item['quantity']) ? $item['quantity'] : 1;
                                ?>
                                <div class="cart-item">
                                    <div class="cart-item-info">
                                        <div class="cart-item-name"><?php echo htmlspecialchars($name); ?></div>
                                        <div class="cart-item-price">₱<?php echo number_format($price, 2); ?></div>
                                    </div>
                                    <div class="cart-item-quantity">x<?php echo $quantity; ?></div>
                                </div>
                            <?php endforeach; ?>
                            
                            <!-- Summary -->
                            <div class="mt-4">
                                <div class="summary-row">
                                    <span>Subtotal</span>
                                    <span>₱<?php echo number_format($total, 2); ?></span>
                                </div>
                                <div class="summary-row">
                                    <span>Delivery Fee</span>
                                    <span>₱50.00</span>
                                </div>
                                <div class="summary-row">
                                    <span>Service Fee</span>
                                    <span>₱20.00</span>
                                </div>
                                <div class="summary-row total">
                                    <span>Total</span>
                                    <span>₱<?php echo number_format($total + 70, 2); ?></span>
                                </div>
                            </div>
                            
                            <!-- Place Order Button -->
                            <button type="submit" class="btn-checkout">
                                <i class="fas fa-check-circle me-2"></i>
                                Place Order
                            </button>
                            
                            <div class="text-center mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-lock me-1"></i>
                                    Secure checkout powered by Legend Brews
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
