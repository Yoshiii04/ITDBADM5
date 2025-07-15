<!-- ADMIN DASHBOARD ORDER VIEW PAGE -->

<?php


// Simulate order details (you can later fetch using $_GET['id'])
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
                <th>Price (₱)</th>
                <th>Subtotal (₱)</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($order['items'] as $item): ?>
                <tr>
                  <td><?= $item['name'] ?></td>
                  <td><?= $item['qty'] ?></td>
                  <td><?= number_format($item['price'], 2) ?></td>
                  <td><?= number_format($item['qty'] * $item['price'], 2) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

          <div class="text-right">
            <h5><strong>Total:</strong> ₱<?= number_format($order['total'], 2) ?></h5>
          </div>
        </div>
      </div>

      <a href="order.php" class="btn btn-secondary mt-4"><i class="fas fa-arrow-left"></i> Back to Orders</a>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
