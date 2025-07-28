<?php
// MySQL connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "online_store";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = $conn->real_escape_string($_POST['order_id']);
    $new_status = $conn->real_escape_string($_POST['new_status']);
    
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
    $stmt->bind_param("si", $new_status, $order_id);
    $stmt->execute();
    $stmt->close();
    
    // Refresh to show updated status
    header("Location: orders.php?currency=" . urlencode($_GET['currency'] ?? 'PHP'));
    exit;
}

// Get selected currency from dropdown
$currency = isset($_GET['currency']) ? $_GET['currency'] : 'PHP';

// Get exchange rate from database
$rate = 1.00;
if ($currency !== 'PHP') {
    $stmt = $conn->prepare("SELECT exchange_rate FROM currencies WHERE currency_code = ?");
    $stmt->bind_param("s", $currency);
    $stmt->execute();
    $stmt->bind_result($rate);
    $stmt->fetch();
    $stmt->close();
}

// Currency symbols
$symbols = [
    'PHP' => '₱',
    'USD' => '$',
    'KRW' => '₩'
];
$symbol = $symbols[$currency] ?? '₱';

// Fetch orders from database
$orders = [];
$query = "SELECT * FROM orders ORDER BY order_date DESC";
$result = $conn->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    $result->free();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin - Orders</title>

  <!-- Bootstrap + FontAwesome -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
  <link rel="stylesheet" href="css/admindash.css" />
  <style>
    .status-select {
      width: 120px;
      display: inline-block;
    }
    .status-form {
      display: inline-block;
    }
  </style>
</head>
<body>
<?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
<div class="dashboard-container">

  <!-- Sidebar -->
  <?php include 'adminsidebar.php'; ?>

  <!-- Main Content -->
  <div class="main-content">
    <h1>Order Management</h1>

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

    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead class="thead-dark">
          <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Date</th>
            <th>Status</th>
            <th>Total (<?= htmlspecialchars($currency) ?>)</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($orders)): ?>
            <tr>
              <td colspan="6" class="text-center">No orders found</td>
            </tr>
          <?php else: ?>
            <?php foreach ($orders as $order): ?>
              <tr>
                <td>#<?= htmlspecialchars($order['order_id']) ?></td>
                <td><?= htmlspecialchars($order['customer_name']) ?></td>
                <td><?= htmlspecialchars($order['order_date']) ?></td>
                <td>
                  <form method="POST" class="status-form">
                    <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                    <select name="new_status" class="form-control form-control-sm status-select" onchange="this.form.submit()">
                      <option value="Pending" <?= $order['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                      <option value="Processing" <?= $order['status'] == 'Processing' ? 'selected' : '' ?>>Processing</option>
                      <option value="Completed" <?= $order['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                      <option value="Cancelled" <?= $order['status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                    <input type="hidden" name="update_status" value="1">
                  </form>
                </td>
                <td><?= $symbol . number_format($order['total_amount'] * $rate, 2) ?></td>
                <td>
                  <a href="ordersview.php?order_id=<?= $order['order_id'] ?>&currency=<?= urlencode($currency) ?>" class="btn btn-sm btn-primary">View</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Optional JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>