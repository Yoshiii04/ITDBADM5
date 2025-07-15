<!-- ADMIN DASHBOARD ORDER--> 

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin - Orders</title>

  <!-- Bootstrap + FontAwesome (optional for icons) -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

  <!-- Your Custom Dashboard CSS -->
  <link rel="stylesheet" href="css/admindash.css">
</head>
<body>
 <?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
<div class="dashboard-container">
  
  <!-- Sidebar -->
  <?php include 'adminsidebar.php'; ?>

  <!-- Main Content -->
  <div class="main-content">
    <h1>Order Management</h1>

    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead class="thead-dark">
          <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Date</th>
            <th>Status</th>
            <th>Total (₱)</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <!-- Sample static rows (replace with dynamic data from database) -->
          <tr>
            <td>#1001</td>
            <td>Juan Dela Cruz</td>
            <td>2025-07-12</td>
            <td><span class="badge badge-success">Completed</span></td>
            <td>₱1,499.00</td>
            <td><form action="ordersview.php" method="GET">
			  <!--<input type="hidden" name="order_id" value="12345">--> <!-- Replace with actual order ID -->
				<button type="submit" class="btn btn-sm btn-primary">View</button>
				</form></td>
          </tr>
          <tr>
            <td>#1002</td>
            <td>Maria Santos</td>
            <td>2025-07-13</td>
            <td><span class="badge badge-warning">Pending</span></td>
            <td>₱2,350.00</td>
            <td><form action="ordersview.php" method="GET">
			  <!--<input type="hidden" name="order_id" value="12345">--> <!-- Replace with actual order ID -->
				<button type="submit" class="btn btn-sm btn-primary">View</button>
				</form></td>
          </tr>
          <tr>
            <td>#1003</td>
            <td>Carlos Reyes</td>
            <td>2025-07-13</td>
            <td><span class="badge badge-danger">Cancelled</span></td>
