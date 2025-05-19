<?php
session_start();
require_once 'connect.php';

if (!isset($_SESSION['cartItems'])) {
    $_SESSION['cartItems'] = [];
    $_SESSION['currentReceipt'] = null;
    $_SESSION['total'] = 0;
}

if (isset($_GET['action']) && $_GET['action'] === 'get_cart') {
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

$initialCategory = 'hot'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Legend Brews</title>
  <link rel="stylesheet" href="style.css" />
  <meta name="description" content="Legend Brews Coffee Shop - Order delicious hot and cold coffee drinks, pastries, and merchandise online." />
</head>
<body>
  <header>
    <h1>Legend Brews</h1>
    <nav class="tabs">
      <button class="tab" data-category="hot">Hot Drinks</button>
      <button class="tab" data-category="cold">Cold Drinks</button>
      <button class="tab" data-category="pastries">Pastries</button>
      <button class="tab" data-category="merch">Merchandise</button>
    </nav>
  </header>

  <main>
    <section class="content">
      <section class="products">
        <div class="loading">Loading products...</div>
      </section>

      <aside class="receipt" data-receipt-id="<?php echo isset($_SESSION['currentReceipt']['id']) ? $_SESSION['currentReceipt']['id'] : ''; ?>">
        <h2>Receipt</h2>
        <ul id="receipt-items">
          <?php
          foreach ($_SESSION['cartItems'] as $item) {
              $itemTotal = $item['product']['price'] * $item['quantity'];
              echo "<li>{$item['product']['name']} " . ($item['quantity'] > 1 ? "(x{$item['quantity']})" : "") . " - ₱{$itemTotal}</li>";
          }
          ?>
        </ul>
        <p><strong>Total: ₱<span id="total"><?php echo $_SESSION['total']; ?></span></strong></p>
        <?php if (count($_SESSION['cartItems']) > 0): ?>
        <button id="checkout-btn" class="checkout-btn" onclick="handleCheckout()">Checkout</button>
        <?php else: ?>
        <button id="checkout-btn" class="checkout-btn" disabled>Checkout</button>
        <?php endif; ?>
      </aside>
    </section>
  </main>

  <script>
    const initialCategory = '<?php echo $initialCategory; ?>';
  </script>
  <script src="scripts.js"></script>
</body>
</html>