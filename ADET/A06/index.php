<?php
session_start();
require_once 'connect.php';

// Initialize cart if not exists
if (!isset($_SESSION['cartItems'])) {
    $_SESSION['cartItems'] = [];
    $_SESSION['currentReceipt'] = null;
    $_SESSION['total'] = 0;
}

// Handle API requests
if (isset($_GET['action'])) {
    if ($_GET['action'] === 'get_cart') {
        header('Content-Type: application/json');
        $receiptId = isset($_SESSION['currentReceipt']['id']) ? $_SESSION['currentReceipt']['id'] : null;
        
        $total = 0;
        foreach ($_SESSION['cartItems'] as $item) {
            $total += $item['product']['price'] * $item['quantity'];
        }
        
        echo json_encode([
            'cartItems' => $_SESSION['cartItems'],
            'receiptTotal' => $total,
            'receiptId' => $receiptId
        ]);
        exit;
    }
    
    if ($_GET['action'] === 'save_cart') {
        // Handle both JSON and form POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $json = file_get_contents('php://input');
            if (empty($json) && isset($_POST['cartData'])) {
                $json = $_POST['cartData'];
            }
            
            $data = json_decode($json, true);
            
            if (isset($data['cartItems'])) {
                $_SESSION['cartItems'] = $data['cartItems'];
                $_SESSION['total'] = $data['total'] ?? 0;
            }
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'cart_count' => count($_SESSION['cartItems'])]);
            exit;
        }
    }
}

// Handle user authentication
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['username'] : '';

$initialCategory = 'hot'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Legend Brews - Premium Coffee Shop Management System</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --coffee-dark: #2c1810;
            --coffee-medium: #6f4e37;
            --coffee-light: #a67c52;
            --coffee-cream: #f5e6d3;
            --coffee-accent: #c17d4a;
            --white: #ffffff;
            --gray-light: #f8f9fa;
            --shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--coffee-cream) 0%, #faf6f2 100%);
            color: var(--coffee-dark);
            min-height: 100vh;
        }
        
        /* Navigation */
        .navbar {
            background: linear-gradient(135deg, var(--coffee-dark) 0%, var(--coffee-medium) 100%);
            box-shadow: var(--shadow);
            padding: 1rem 0;
        }
        
        .navbar-brand {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--white) !important;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .nav-link:hover {
            color: var(--white) !important;
            transform: translateY(-2px);
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background: var(--coffee-accent);
            transition: width 0.3s ease;
        }
        
        .nav-link:hover::after {
            width: 100%;
        }
        
        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, var(--coffee-dark) 0%, var(--coffee-medium) 100%);
            color: var(--white);
            padding: 4rem 0;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
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
        
        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            position: relative;
            z-index: 1;
        }
        
        .hero-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 2rem;
            position: relative;
            z-index: 1;
        }
        
        /* Category Tabs */
        .category-tabs {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin: 2rem 0;
            flex-wrap: wrap;
        }
        
        .category-tab {
            padding: 0.8rem 1.5rem;
            background: var(--white);
            border: 2px solid var(--coffee-light);
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            color: var(--coffee-dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .category-tab:hover {
            background: var(--coffee-light);
            color: var(--white);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(111, 78, 55, 0.3);
        }
        
        .category-tab.active {
            background: var(--coffee-medium);
            color: var(--white);
            border-color: var(--coffee-medium);
        }
        
        /* Search Bar */
        .search-container {
            max-width: 600px;
            margin: 2rem auto;
            position: relative;
        }
        
        .search-input {
            width: 100%;
            padding: 1rem 3rem 1rem 1.5rem;
            border: 2px solid var(--coffee-light);
            border-radius: 25px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .search-input:focus {
            outline: none;
            border-color: var(--coffee-medium);
            box-shadow: 0 0 0 3px rgba(111, 78, 55, 0.1);
        }
        
        .search-btn {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--coffee-medium);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .search-btn:hover {
            background: var(--coffee-dark);
            transform: translateY(-50%) scale(1.1);
        }
        
        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }
        
        .product-card {
            background: var(--white);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            position: relative;
        }
        
        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        .product-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: var(--coffee-accent);
            color: var(--white);
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            z-index: 1;
        }
        
        .product-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .product-card:hover .product-image {
            transform: scale(1.05);
        }
        
        .product-content {
            padding: 1.5rem;
        }
        
        .product-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--coffee-dark);
            margin-bottom: 0.5rem;
        }
        
        .product-description {
            color: #666;
            margin-bottom: 1rem;
            line-height: 1.5;
        }
        
        .product-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--coffee-medium);
            margin-bottom: 1rem;
        }
        
        .add-to-cart {
            width: 100%;
            padding: 0.8rem;
            background: linear-gradient(135deg, var(--coffee-dark), var(--coffee-medium));
            color: var(--white);
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .add-to-cart:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(111, 78, 55, 0.3);
        }
        
        /* Shopping Cart Sidebar */
        .cart-sidebar {
            position: fixed;
            right: -400px;
            top: 0;
            width: 400px;
            height: 100vh;
            background: var(--white);
            box-shadow: -5px 0 15px rgba(0,0,0,0.1);
            transition: right 0.3s ease;
            z-index: 1050;
            display: flex;
            flex-direction: column;
        }
        
        .cart-sidebar.open {
            right: 0;
        }
        
        .cart-header {
            background: var(--coffee-dark);
            color: var(--white);
            padding: 1.5rem;
            display: flex;
            justify-content: between;
            align-items: center;
        }
        
        .cart-items {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
        }
        
        .cart-footer {
            padding: 1.5rem;
            border-top: 1px solid #eee;
        }
        
        .cart-toggle {
            position: fixed;
            right: 2rem;
            bottom: 2rem;
            background: var(--coffee-medium);
            color: var(--white);
            border: none;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            z-index: 1040;
        }
        
        .cart-toggle:hover {
            transform: scale(1.1);
            background: var(--coffee-dark);
        }
        
        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ff4757;
            color: var(--white);
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 700;
        }
        
        /* Loading State */
        .loading-container {
            text-align: center;
            padding: 4rem 2rem;
        }
        
        .spinner {
            border: 3px solid var(--coffee-light);
            border-top: 3px solid var(--coffee-medium);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #666;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: var(--coffee-light);
            margin-bottom: 1rem;
        }
        
        /* Toast Notifications */
        .toast-container {
            position: fixed;
            top: 2rem;
            right: 2rem;
            z-index: 1060;
        }
        
        .toast {
            background: var(--white);
            border-radius: 8px;
            box-shadow: var(--shadow);
            padding: 1rem 1.5rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            min-width: 300px;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .toast.success {
            border-left: 4px solid #28a745;
        }
        
        .toast.error {
            border-left: 4px solid #dc3545;
        }
        
        .toast.info {
            border-left: 4px solid #17a2b8;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .products-grid {
                grid-template-columns: 1fr;
            }
            
            .cart-sidebar {
                width: 100%;
                right: -100%;
            }
        }
    </style>
</head>
<body>
    <!-- Success Message Alert -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 9999;" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?php 
                echo htmlspecialchars($_SESSION['success_message']); 
                unset($_SESSION['success_message']); 
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <!-- Info Message Alert -->
    <?php if (isset($_SESSION['info_message'])): ?>
        <div class="alert alert-info alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 9999;" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            <?php 
                echo htmlspecialchars($_SESSION['info_message']); 
                unset($_SESSION['info_message']); 
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-mug-hot"></i>
                Legend Brews
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#menu">
                            <i class="fas fa-coffee me-1"></i> Menu
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if ($isLoggedIn): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="orders.php">
                                <i class="fas fa-receipt me-1"></i> My Orders
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($userName); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="profile.php">
                                    <i class="fas fa-user-cog me-2"></i> Profile
                                </a></li>
                                <li><a class="dropdown-item" href="logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </a></li>
                            </ul>
                        </li>
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="admin/dashboard.php">
                                    <i class="fas fa-tachometer-alt me-1"></i> Admin
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">
                                <i class="fas fa-sign-in-alt me-1"></i> Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">
                                <i class="fas fa-user-plus me-1"></i> Register
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">
                                <i class="fas fa-user-shield me-1"></i> Admin
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container text-center">
            <h1 class="hero-title">Welcome to Legend Brews</h1>
            <p class="hero-subtitle">Premium Coffee Shop Management System</p>
            <div class="mt-4">
                <span class="badge bg-success me-2"><i class="fas fa-check me-1"></i> PHP Backend</span>
                <span class="badge bg-info me-2"><i class="fas fa-database me-1"></i> MySQL Database</span>
                <span class="badge bg-warning me-2"><i class="fas fa-chart-line me-1"></i> Analytics</span>
                <span class="badge bg-primary"><i class="fas fa-mobile-alt me-1"></i> Mobile Responsive</span>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main class="container my-5" id="menu">
        <!-- Search Bar -->
        <div class="search-container">
            <input type="text" class="search-input" id="searchInput" placeholder="Search for coffee, pastries, or merchandise...">
            <button class="search-btn" onclick="searchProducts()">
                <i class="fas fa-search"></i>
            </button>
        </div>

        <!-- Category Tabs -->
        <div class="category-tabs">
            <button class="category-tab active" data-category="hot">
                <i class="fas fa-mug-hot"></i> Hot Drinks
            </button>
            <button class="category-tab" data-category="cold">
                <i class="fas fa-glass-water"></i> Cold Drinks
            </button>
            <button class="category-tab" data-category="pastries">
                <i class="fas fa-cookie-bite"></i> Pastries
            </button>
            <button class="category-tab" data-category="merch">
                <i class="fas fa-gift"></i> Merchandise
            </button>
        </div>

        <!-- Products Container -->
        <div id="productsContainer">
            <div class="loading-container">
                <div class="spinner"></div>
                <p>Loading delicious products...</p>
            </div>
        </div>
    </main>

    <!-- Shopping Cart Sidebar -->
    <div class="cart-sidebar" id="cartSidebar">
        <div class="cart-header">
            <h5 class="mb-0">
                <i class="fas fa-shopping-cart me-2"></i> Your Cart
            </h5>
            <button class="btn btn-sm btn-light" onclick="toggleCart()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="cart-items" id="cartItems">
            <!-- Cart items will be loaded here -->
        </div>
        <div class="cart-footer">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>Total:</h5>
                <h5 id="cartTotal">₱0</h5>
            </div>
            <button class="btn btn-success w-100" id="checkoutBtn" onclick="proceedToCheckout()">
                <i class="fas fa-credit-card me-2"></i> Proceed to Checkout
            </button>
        </div>
    </div>

    <!-- Cart Toggle Button -->
    <button class="cart-toggle" onclick="toggleCart()">
        <i class="fas fa-shopping-cart"></i>
        <span class="cart-count" id="cartCount">0</span>
    </button>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Global variables
        let currentCategory = 'hot';
        let cartItems = [];
        let total = 0;

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            setupCategoryTabs();
            loadProducts('hot');
            loadCartFromSession();
            setupSearch();
        });

        // Setup category tabs
        function setupCategoryTabs() {
            const tabs = document.querySelectorAll('.category-tab');
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    tabs.forEach(t => t.classList.remove('active'));
                    tab.classList.add('active');
                    currentCategory = tab.dataset.category;
                    loadProducts(currentCategory);
                });
            });
        }

        // Setup search
        function setupSearch() {
            const searchInput = document.getElementById('searchInput');
            searchInput.addEventListener('input', debounce(searchProducts, 300));
            searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    searchProducts();
                }
            });
        }

        // Debounce function
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Load products
        async function loadProducts(category) {
            const container = document.getElementById('productsContainer');
            
            // Show loading
            container.innerHTML = `
                <div class="loading-container">
                    <div class="spinner"></div>
                    <p>Loading ${category} products...</p>
                </div>
            `;
            
            try {
                const response = await fetch(`api/products.php?category=${category}`);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const products = await response.json();
                displayProducts(products);
                
            } catch (error) {
                console.error('Error loading products:', error);
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h4>Unable to load products</h4>
                        <p>Please try again later.</p>
                        <button class="btn btn-primary" onclick="loadProducts('${category}')">
                            <i class="fas fa-redo me-2"></i> Retry
                        </button>
                    </div>
                `;
            }
        }

        // Display products
        function displayProducts(products) {
            const container = document.getElementById('productsContainer');
            
            if (!products || products.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-box-open"></i>
                        <h4>No products found</h4>
                        <p>No products available in this category.</p>
                    </div>
                `;
                return;
            }
            
            const productsHTML = products.map(product => `
                <div class="product-card">
                    <span class="product-badge">${getCategoryBadge(product.category)}</span>
                    <img src="${product.image_url}" alt="${product.name}" class="product-image"
                         onerror="this.src='https://via.placeholder.com/320x250/6f4e37/ffffff?text=${encodeURIComponent(product.name)}'">
                    <div class="product-content">
                        <h3 class="product-title">${product.name}</h3>
                        <p class="product-description">${product.description}</p>
                        <div class="product-price">₱${parseFloat(product.price).toFixed(2)}</div>
                        <button class="add-to-cart" onclick="addToCart(${product.id}, '${product.name}', ${product.price}, '${product.image_url}')">
                            <i class="fas fa-shopping-cart me-2"></i> Add to Cart
                        </button>
                    </div>
                </div>
            `).join('');
            
            container.innerHTML = `<div class="products-grid">${productsHTML}</div>`;
        }

        // Get category badge
        function getCategoryBadge(category) {
            const badges = {
                'hot': '☕ Hot',
                'cold': '🧊 Cold',
                'pastries': '🥐 Bakery',
                'merch': '🎁 Merch'
            };
            return badges[category] || category;
        }

        // Search products
        async function searchProducts() {
            const searchTerm = document.getElementById('searchInput').value.trim();
            const container = document.getElementById('productsContainer');
            
            if (!searchTerm) {
                loadProducts(currentCategory);
                return;
            }
            
            container.innerHTML = `
                <div class="loading-container">
                    <div class="spinner"></div>
                    <p>Searching for "${searchTerm}"...</p>
                </div>
            `;
            
            try {
                const response = await fetch(`api/products.php?search=${encodeURIComponent(searchTerm)}`);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const products = await response.json();
                displayProducts(products);
                
            } catch (error) {
                console.error('Error searching products:', error);
                showToast('Error searching products', 'error');
            }
        }

        // Add to cart
        function addToCart(productId, productName, price, imageUrl) {
            const existingItem = cartItems.find(item => item.id === productId);
            
            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cartItems.push({
                    id: productId,
                    name: productName,
                    price: price,
                    image: imageUrl,
                    quantity: 1
                });
            }
            
            updateCart();
            showToast(`${productName} added to cart!`, 'success');
        }

        // Update cart
        function updateCart() {
            const cartItemsContainer = document.getElementById('cartItems');
            const cartTotal = document.getElementById('cartTotal');
            const cartCount = document.getElementById('cartCount');
            const checkoutBtn = document.getElementById('checkoutBtn');
            
            // Update cart items
            if (cartItems.length === 0) {
                cartItemsContainer.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Your cart is empty</p>
                    </div>
                `;
                checkoutBtn.disabled = true;
            } else {
                const itemsHTML = cartItems.map(item => `
                    <div class="d-flex align-items-center mb-3">
                        <img src="${item.image}" alt="${item.name}" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                        <div class="flex-grow-1">
                            <h6 class="mb-0">${item.name}</h6>
                            <small class="text-muted">₱${item.price.toFixed(2)} x ${item.quantity}</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <button class="btn btn-sm btn-outline-secondary me-2" onclick="updateQuantity(${item.id}, -1)">
                                <i class="fas fa-minus"></i>
                            </button>
                            <span class="mx-2">${item.quantity}</span>
                            <button class="btn btn-sm btn-outline-secondary me-2" onclick="updateQuantity(${item.id}, 1)">
                                <i class="fas fa-plus"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="removeFromCart(${item.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `).join('');
                
                cartItemsContainer.innerHTML = itemsHTML;
                checkoutBtn.disabled = false;
            }
            
            // Update total
            total = cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            cartTotal.textContent = `₱${total.toFixed(2)}`;
            
            // Update count
            const itemCount = cartItems.reduce((sum, item) => sum + item.quantity, 0);
            cartCount.textContent = itemCount;
            
            // Save to session
            saveCartToSession();
        }

        // Update quantity
        function updateQuantity(productId, change) {
            const item = cartItems.find(item => item.id === productId);
            if (item) {
                item.quantity += change;
                if (item.quantity <= 0) {
                    removeFromCart(productId);
                } else {
                    updateCart();
                }
            }
        }

        // Remove from cart
        function removeFromCart(productId) {
            cartItems = cartItems.filter(item => item.id !== productId);
            updateCart();
            showToast('Item removed from cart', 'info');
        }

        // Toggle cart
        function toggleCart() {
            const cartSidebar = document.getElementById('cartSidebar');
            cartSidebar.classList.toggle('open');
        }

        // Save cart to session
        async function saveCartToSession() {
            try {
                console.log('Saving cart:', { cartItems, total });
                const response = await fetch('index.php?action=save_cart', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ cartItems, total })
                });
                const result = await response.json();
                console.log('Cart saved result:', result);
                return result;
            } catch (error) {
                console.error('Error saving cart:', error);
                return { success: false };
            }
        }

        // Load cart from session
        async function loadCartFromSession() {
            try {
                const response = await fetch('index.php?action=get_cart');
                const data = await response.json();
                cartItems = data.cartItems || [];
                total = data.receiptTotal || 0;
                updateCart();
            } catch (error) {
                console.error('Error loading cart:', error);
            }
        }

        // Proceed to checkout
        function proceedToCheckout() {
            if (cartItems.length === 0) {
                showToast('Your cart is empty', 'error');
                return;
            }
            
            // Save cart first, then redirect
            saveCartToSession().then((result) => {
                console.log('Cart save result:', result);
                
                if (result && result.success) {
                    // Cart saved successfully, now redirect
                    <?php if (!$isLoggedIn): ?>
                        // For guest users, transfer cart via URL parameter
                        const cartData = btoa(JSON.stringify({ cartItems, total }));
                        window.location.href = 'login.php?redirect=checkout&cart=' + encodeURIComponent(cartData);
                    <?php else: ?>
                        // For logged users, transfer cart via URL parameter
                        const cartData = btoa(JSON.stringify({ cartItems, total }));
                        window.location.href = 'checkout.php?cart=' + encodeURIComponent(cartData);
                    <?php endif; ?>
                } else {
                    showToast('Failed to save cart. Please try again.', 'error');
                }
            }).catch(error => {
                console.error('Cart save error:', error);
                showToast('Error saving cart. Please try again.', 'error');
            });
        }

        // Show toast notification
        function showToast(message, type = 'info') {
            const toastContainer = document.getElementById('toastContainer');
            const toastId = 'toast-' + Date.now();
            
            const toastHTML = `
                <div class="toast ${type}" id="${toastId}">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                    <div class="flex-grow-1">${message}</div>
                    <button class="btn-close" onclick="removeToast('${toastId}')"></button>
                </div>
            `;
            
            toastContainer.insertAdjacentHTML('beforeend', toastHTML);
            
            // Auto remove after 3 seconds
            setTimeout(() => removeToast(toastId), 3000);
        }

        // Remove toast
        function removeToast(toastId) {
            const toast = document.getElementById(toastId);
            if (toast) {
                toast.remove();
            }
        }
    </script>
</body>
</html>
