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

// Get order ID and currency
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$currency = isset($_GET['currency']) ? $_GET['currency'] : 'PHP';

// Get exchange rate
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

// Get order details
$order = [];
$orderitems = [];

if ($order_id > 0) {
    // Get basic order info
    $stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    $stmt->close();

    if ($order) {
        // Get order items
        $stmt = $conn->prepare("
            SELECT oi.*, p.name, p.price, p.image 
            FROM orderitems oi
            JOIN products p ON oi.product_id = p.product_id
            WHERE oi.order_id = ?
        ");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $orderitems[] = $row;
        }
        $stmt->close();
    }
}

// If no order found, redirect back
if (empty($order)) {
    header("Location: orders.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Order Details - Admin</title>

  <!-- Bootstrap + FontAwesome -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
  <link rel="stylesheet" href="css/admindash.css" />
  <style>
    .order-details-card {
      margin-bottom: 20px;
    }
    .product-img {
      width: 50px;
      height: 50px;
      object-fit: cover;
      margin-right: 10px;
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
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>Order Details #<?= $order_id ?></h1>
      <a href="orders.php?currency=<?= urlencode($currency) ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Orders
      </a>
    </div>

    <!-- Currency Selector -->
    <form method="GET" class="form-inline mb-3">
      <input type="hidden" name="order_id" value="<?= $order_id ?>">
      <label class="mr-2" for="currency">Currency:</label>
      <select class="form-control mr-2" name="currency" id="currency">
        <option value="PHP" <?= $currency == 'PHP' ? 'selected' : '' ?>>PHP (₱)</option>
        <option value="USD" <?= $currency == 'USD' ? 'selected' : '' ?>>USD ($)</option>
        <option value="KRW" <?= $currency == 'KRW' ? 'selected' : '' ?>>KRW (₩)</option>
      </select>
      <button class="btn btn-secondary" type="submit">Convert</button>
    </form>

    <div class="row">
      <div class="col-md-6">
        <div class="card order-details-card">
          <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Customer Information</h5>
          </div>
          <div class="card-body">
            <p><strong>Customer Name:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
            <p><strong>Order Date:</strong> <?= htmlspecialchars($order['order_date']) ?></p>
            <p><strong>Status:</strong> 
              <span class="badge 
                <?= $order['status'] == 'Pending' ? 'badge-warning' : 
                   ($order['status'] == 'Processing' ? 'badge-info' : 
                   ($order['status'] == 'Completed' ? 'badge-success' : 'badge-danger')) ?>">
                <?= htmlspecialchars($order['status']) ?>
              </span>
            </p>
          </div>
        </div>
      </div>
      
      <div class="col-md-6">
        <div class="card order-details-card">
          <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Order Summary</h5>
          </div>
          <div class="card-body">
            <p><strong>Total Amount:</strong> <?= $symbol . number_format($order['total_amount'] * $rate, 2) ?></p>
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header bg-dark text-white">
        <h5 class="mb-0">Order Items</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead class="thead-light">
              <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($orderitems)): ?>
                <tr>
                  <td colspan="4" class="text-center">No items found</td>
                </tr>
              <?php else: ?>
                <?php foreach ($orderitems as $item): ?>
                  <tr>
                    <td>
                      <div class="d-flex align-items-center">
                        <?php if ($item['image']): ?>
                          <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="product-img">
                        <?php endif; ?>
                        <span><?= htmlspecialchars($item['name']) ?></span>
                      </div>
                    </td>
                    <td><?= $symbol . number_format($item['price'] * $rate, 2) ?></td>
                    <td><?= htmlspecialchars($item['quantity']) ?></td>
                    <td><?= $symbol . number_format($item['price'] * $item['quantity'] * $rate, 2) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Optional JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>