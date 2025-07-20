<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Order Details</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/font-awesome.min.css">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

	<?php include 'currency.php'; ?> 
    <!-- HEADER --> 
	<?php include 'header.php'; ?>
	<!-- /HEADER -->

	<!-- NAVIGATION -->
	<?php include 'navigation.php'; ?>
	<!-- /NAVIGATION -->

		<!-- BREADCRUMB -->	
		<div id="breadcrumb" class="section">
			<!-- container -->
			<div class="container">
				<!-- row -->
				<div class="row">
					<div class="col-md-12">
						<h3 class="breadcrumb-header">View Order</h3>
						<ul class="breadcrumb-tree">
							<li>Account</li>
							<li>Order History</li>
							<li class="active">View Order</li>
						</ul>
					</div>
				</div>
				<!-- /row -->
			</div>
			<!-- /container -->
		</div>
		<!-- /BREADCRUMB -->

<div class="container my-5">
  <h2 class="text-center mb-4">Order Details</h2>

  <!-- Order Info -->
  <div class="card mb-4">
    <div class="card-body">
      <h5>Order #: <strong>ORD-20250714-001</strong></h5>
      <p><strong>Date Placed:</strong> July 14, 2025</p>
      <p><strong>Status:</strong> <span class="text-success">Shipped</span></p>
      <p><strong>Shipping Address:</strong> 123 Green Street, Makati, PH</p>
    </div>
  </div>

  <!-- Order Items -->
  <div class="table-responsive">
    <table class="table table-bordered">
      <thead class="thead-dark">
        <tr>
          <th>Image</th>
          <th>Product</th>
          <th>Qty</th>
          <th>Price</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><img src="img/product01.png" width="80" alt="Product"></td>
          <td>RGB Mechanical Keyboard</td>
          <td>1</td>
          <td><?php echo displayPrice(59.99); ?></td>
          <td><?php echo displayPrice(59.99); ?></td>
        </tr>
        <tr>
          <td><img src="img/product02.png" width="80" alt="Product"></td>
          <td>Wireless Gaming Mouse</td>
          <td>2</td>
          <td><?php echo displayPrice(39.99); ?></td>
          <td><?php echo displayPrice(79.98); ?></td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- Summary -->
  <div class="text-right">
    <h5>Subtotal: <?php echo displayPrice(139.97); ?></h5>
    <h5>Shipping: <?php echo displayPrice(10); ?></h5>
    <h4><strong>Total: <?php echo displayPrice(149.97); ?></strong></h4>
  </div>

  <div class="text-center mt-4">
    <a href="orderhistory.php" class="btn btn-primary">Back to Order History</a>
  </div>
</div>

 <!-- FOOTER -->
	<?php include 'footer.php'; ?>
	<!-- /FOOTER -->

  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
</body>
</html>
