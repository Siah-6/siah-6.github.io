<?php
session_start();
header('Content-Type: application/json');
require_once '../connect.php';

// Create a new receipt
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_GET['id'])) {
    $date = date('Y-m-d H:i:s');
    $query = "INSERT INTO receipts (total, created_at, completed) VALUES (0, '$date', 0)";
    
    $result = executeQuery($query);
    
    if (!$result) {
        http_response_code(500);
        echo json_encode(['error' => 'Could not create receipt: ' . mysqli_error($conn)]);
        exit;
    }
    
    $receiptId = mysqli_insert_id($conn);
    $_SESSION['currentReceipt'] = [
        'id' => $receiptId,
        'total' => 0,
        'items' => []
    ];
    $_SESSION['cartItems'] = [];
    $_SESSION['total'] = 0;
    
    echo json_encode(['id' => $receiptId, 'message' => 'Receipt created']);
    exit;
}

// Receipt ID is required for all other operations
if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Receipt ID is required']);
    exit;
}

$receiptId = $_GET['id'];

// Add item to receipt
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'add_item') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['productId']) || !isset($input['quantity'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Product ID and quantity are required']);
        exit;
    }
    
    $productId = $input['productId'];
    $quantity = $input['quantity'];
    
    // Get product details
    $query = "SELECT * FROM products WHERE id = $productId";
    $result = executeQuery($query);
    
    if (!$result || mysqli_num_rows($result) === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Product not found']);
        exit;
    }
    
    $product = mysqli_fetch_assoc($result);
    $price = $product['price'];
    
    // Check if item already exists in cart
    $itemExists = false;
    foreach ($_SESSION['cartItems'] as $key => $item) {
        if ($item['product']['id'] == $productId) {
            $_SESSION['cartItems'][$key]['quantity'] += $quantity;
            $itemExists = true;
            break;
        }
    }
    
    if (!$itemExists) {
        $_SESSION['cartItems'][] = [
            'product' => $product,
            'quantity' => $quantity
        ];
    }
    
    // Calculate new total
    $total = 0;
    foreach ($_SESSION['cartItems'] as $item) {
        $total += $item['product']['price'] * $item['quantity'];
    }
    $_SESSION['total'] = $total;
    
    // Update receipt total in database
    $updateQuery = "UPDATE receipts SET total = $total WHERE id = $receiptId";
    executeQuery($updateQuery);
    
    // Add to receipt_items table
    $insertQuery = "INSERT INTO receipt_items (product_id, receipt_id, quantity, price) 
                   VALUES ($productId, $receiptId, $quantity, $price)
                   ON DUPLICATE KEY UPDATE quantity = quantity + $quantity";
    executeQuery($insertQuery);
    
    echo json_encode([
        'message' => 'Item added to cart',
        'cartItems' => $_SESSION['cartItems'],
        'total' => $total
    ]);
    exit;
}

// Complete receipt (checkout)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'complete') {
    $query = "UPDATE receipts SET completed = 1 WHERE id = $receiptId";
    $result = executeQuery($query);
    
    if (!$result) {
        http_response_code(500);
        echo json_encode(['error' => 'Could not complete checkout: ' . mysqli_error($conn)]);
        exit;
    }
    
    // Clear the cart
    $_SESSION['cartItems'] = [];
    $_SESSION['total'] = 0;
    $_SESSION['currentReceipt'] = null;
    
    echo json_encode(['message' => 'Checkout completed']);
    exit;
}

// Get receipt details
$query = "SELECT * FROM receipts WHERE id = $receiptId";
$result = executeQuery($query);

if (!$result || mysqli_num_rows($result) === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Receipt not found']);
    exit;
}

$receipt = mysqli_fetch_assoc($result);

// Get receipt items
$query = "SELECT ri.*, p.name, p.description, p.category, p.image_url 
         FROM receipt_items ri
         JOIN products p ON ri.product_id = p.id
         WHERE ri.receipt_id = $receiptId";
$result = executeQuery($query);

$items = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = $row;
    }
}

$receipt['items'] = $items;

echo json_encode($receipt);
?>