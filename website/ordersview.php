<?php
// Simulated order details
$order = [
  'order_id' => '#1001',
  'customer' => 'Juan Dela Cruz',
  'email' => 'juan@example.com',
  'date' => '2025-07-12',
  'status' => 'Completed',
  'total' => 1499.00,
  'items' => [
    ['name' => 'RGB Gaming Mouse', 'qty' => 1, 'price' => 999.00],
    ['name' => 'Mouse Pad XL', 'qty' => 1, 'price' => 500.00],
  ]
];

// Currency settings
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

// Recalculate total for display
$convertedTotal = $order['total'] * $rate;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>View Order - Admin</title>

  <!-- Bootstrap + FontAwesome -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />

  <!-- Custom Admin Dashboard CSS -->
  <link rel="stylesheet" href="css/admindash.css" />
</head>
<body>
<?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
<div class="dashboard-container">
  <!-- Sidebar -->
  <?php include 'adminsidebar.php'; ?>

  <!-- Main Content -->
  <div class="main-content">
    <h1>Order Details</h1>

    <!-- Currency Selector -->
    <form method="GET" class="form-inline mb-3">
      <label class="mr-2" for="currency">Currency:</label>
      <select class="form-control mr-2" name="currency" id="currency">
        <option value="PHP" <?= $currency == 'PHP' ? 'selected' : '' ?>>PHP (₱)</option>
        <option value="USD" <?= $currency == 'USD' ? 'selected' : '' ?>>USD ($)</option>
        <option value="KRW" <?= $currency == 'KRW' ? 'selected' : '' ?>>KRW (₩)</option>
      </select>
      <button class="btn btn-secondary" type="submit">Convert</button>
    </form>

    <div class="card">
      <div class="card-body">
        <h4 class="card-title mb-3"><?= $order['order_id'] ?> - <?= $order['customer'] ?></h4>
        <p><strong>Email:</strong> <?= $order['email'] ?></p>
        <p><strong>Date:</strong> <?= $order['date'] ?></p>
        <p><strong>Status:</strong> 
          <span class="badge badge-success"><?= $order['status'] ?></span>
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
            <?php foreach ($order['items'] as $item): ?>
              <tr>
                <td><?= $item['name'] ?></td>
                <td><?= $item['qty'] ?></td>
                <td><?= $symbol . number_format($item['price'] * $rate, 2) ?></td>
                <td><?= $symbol . number_format($item['qty'] * $item['price'] * $rate, 2) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <div class="text-right">
          <h5><strong>Total:</strong> <?= $symbol . number_format($convertedTotal, 2) ?></h5>
        </div>
      </div>
    </div>

    <a href="order.php?currency=<?= urlencode($currency) ?>" class="btn btn-secondary mt-4">
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
