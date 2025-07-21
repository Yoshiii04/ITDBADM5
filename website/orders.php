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
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <!-- Static Rows With Currency Conversion -->
          <tr>
            <td>#1001</td>
            <td>Juan Dela Cruz</td>
            <td>2025-07-12</td>
            <td><span class="badge badge-success">Completed</span></td>
            <td><?= $symbol . number_format(1499.00 * $rate, 2) ?></td>
            <td>
              <form action="ordersview.php" method="GET">
                <input type="hidden" name="currency" value="<?= htmlspecialchars($currency) ?>">
                <button type="submit" class="btn btn-sm btn-primary">View</button>
              </form>
            </td>
          </tr>
          <tr>
            <td>#1002</td>
            <td>Maria Santos</td>
            <td>2025-07-13</td>
            <td><span class="badge badge-warning">Pending</span></td>
            <td><?= $symbol . number_format(2350.00 * $rate, 2) ?></td>
            <td>
              <form action="ordersview.php" method="GET">
                <input type="hidden" name="currency" value="<?= htmlspecialchars($currency) ?>">
                <button type="submit" class="btn btn-sm btn-primary">View</button>
              </form>
            </td>
          </tr>
          <tr>
            <td>#1003</td>
            <td>Carlos Reyes</td>
            <td>2025-07-13</td>
            <td><span class="badge badge-danger">Cancelled</span></td>
            <td><?= $symbol . number_format(999.00 * $rate, 2) ?></td>
            <td>
              <form action="ordersview.php" method="GET">
                <input type="hidden" name="currency" value="<?= htmlspecialchars($currency) ?>">
                <button type="submit" class="btn btn-sm btn-primary">View</button>
              </form>
            </td>
          </tr>
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
