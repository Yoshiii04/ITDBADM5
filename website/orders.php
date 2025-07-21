<?php
// Get selected currency from dropdown
$currency = isset($_GET['currency']) ? $_GET['currency'] : 'PHP';

// Hardcoded exchange rates (1 PHP = ?)
$exchangeRates = [
    'PHP' => 1.00,
    'USD' => 0.0182,
    'KRW' => 23.57
];

// Currency symbols
$symbols = [
    'PHP' => '₱',
    'USD' => '$',
    'KRW' => '₩'
];

$rate = $exchangeRates[$currency] ?? 1.00;
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
          <!-- Sample Static Rows with Converted Values -->
          <tr>
            <td>#1001</td>
            <td>Juan Dela Cruz</td>
            <td>2025-07-12</td>
            <td><span class="badge badge-success">Completed</span></td>
            <td><?= $symbol . number_format(1499.00 * $rate, 2) ?></td>
            <td>
              <form action="ordersview.php" method="GET">
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
                <button type="submit" class="btn btn-sm btn-primary">View</button>
              </form>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
