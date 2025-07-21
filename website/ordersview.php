<?php
//  DB Connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "online_store";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Currency setup
$currency = isset($_GET['currency']) ? $_GET['currency'] : 'PHP';

$exchangeRates = [
  'PHP' => 1.00,
  'USD' => 0.0182,
  'KRW' => 23.57
];

$symbols = [
  'PHP' => '₱',
  'USD' => '$',
  'KRW' => '₩'
];

$rate = $exchangeRates[$currency] ?? 1.00;
$symbol = $symbols[$currency] ?? '₱';

// Get order_id (can be passed from orders.php via GET)
$order_id = isset($_GET['order_id']) ? (int) $_GET['order_id'] : 1001; // default fallback

// Fetch order items from ordersview table
$sql = "SELECT * FROM ordersview WHERE order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

$order_items = [];
$order_info = null;

while ($row = $result->fetch_assoc()) {
    $order_items[] = $row;
    if (!$order_info) {
        $order_info = $row; // grab common fields only once
    }
}

$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>View Order - Admin</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
  <link rel="stylesheet" href="css/admindash.css" />
</head>
<body>
<?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
<div class="dashboard-container">
  <?php include 'adminsidebar.php'; ?>

  <div class="main-content">
    <h1>Order Details</h1>

    <!-- Currency Dropdown -->
    <form method="GET" class="form-inline mb-3">
      <input type="hidden" name="order_id" value="<?= htmlspecialchars($order_id) ?>">
      <label class="mr-2" for="currency">Currency:</label>
      <select class="form-control mr-2" name="currency" id="currency">
        <option value="PHP" <?= $currency == 'PHP' ? 'selected' : '' ?>>PHP (₱)</option>
        <option value="USD" <?= $currency == 'USD' ? 'selected' : '' ?>>USD ($)</option>
        <option value="KRW" <?= $currency == 'KRW' ? 'selected' : '' ?>>KRW (₩)</option>
      </select>
      <button class="btn btn-secondary" type="submit">Convert</button>
    </form>

    <?php if ($order_info): ?>
    <div class="card">
      <div class="card-body">
        <h4 class="card-title mb-3">#<?= $order_info['order_id'] ?> - <?= htmlspecialchars($order_info['customer_name']) ?></h4>
        <p><strong>Email:</strong> <?= htmlspecialchars($order_info['customer_email']) ?></p>
        <p><strong>Date:</strong> <?= htmlspecialchars($order_info['order_date']) ?></p>
        <p><strong>Status:</strong> 
          <span class="badge badge-success"><?= htmlspecialchars($order_info['status']) ?></span>
        </p>

        <hr>
        <h5>Items:</h5>
        <table class="table table-bordered mt-3">
          <thead class="thead-light">
            <tr>
              <th>Product</th>
              <th>Quantity</th>
              <th>Price (<?= $symbol ?>)</th>
              <th>Subtotal (<?= $symbol ?>)</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $total = 0;
            foreach ($order_items as $item): 
              $price = $item['price'] * $rate;
              $subtotal = $item['subtotal'] * $rate;
              $total += $subtotal;
            ?>
              <tr>
                <td><?= htmlspecialchars($item['product_name']) ?></td>
                <td><?= $item['quantity'] ?></td>
                <td><?= $symbol . number_format($price, 2) ?></td>
                <td><?= $symbol . number_format($subtotal, 2) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <div class="text-right">
          <h5><strong>Total:</strong> <?= $symbol . number_format($total, 2) ?></h5>
        </div>
      </div>
    </div>
    <?php else: ?>
      <div class="alert alert-warning">Order not found.</div>
    <?php endif; ?>

    <a href="orders.php?currency=<?= urlencode($currency) ?>" class="btn btn-secondary mt-4">
      <i class="fas fa-arrow-left"></i> Back to Orders
    </a>
  </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

