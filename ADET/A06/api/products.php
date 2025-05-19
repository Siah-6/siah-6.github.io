<?php
header('Content-Type: application/json');
require_once '../connect.php';

// Allow some time for "loading" visual
sleep(1);

$category = isset($_GET['category']) ? $_GET['category'] : 'all';

$query = "SELECT * FROM products";
if ($category !== 'all') {
    $query .= " WHERE category = '$category'";
}

$result = executeQuery($query);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . mysqli_error($conn)]);
    exit;
}

$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}

echo json_encode($products);
?>