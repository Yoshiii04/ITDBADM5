<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" >
  <meta name="viewport" content="width=device-width, initial-scale=1.0" >
  <title>Order History</title>
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
						<h3 class="breadcrumb-header">Order History</h3>
						<ul class="breadcrumb-tree">
							<li>Account</li>
							<li class="active">Order History</li>
						</ul>
					</div>
				</div>
				<!-- /row -->
			</div>
			<!-- /container -->
		</div>
		<!-- /BREADCRUMB -->

  <div class="container my-5">
    <h2 class="text-center mb-4">Order History</h2>
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead class="thead-dark">
          <tr>
            <th scope="col">Order ID</th>
            <th scope="col">Date</th>
            <th scope="col">Items</th>
            <th scope="col">Total</th>
            <th scope="col">Status</th>
            <th scope="col">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>#1001</td>
            <td>2025-07-10</td>
            <td>3 items</td>
            <td><?php echo displayPrice(2150); ?></td>
            <td><span class="badge bg-success">Delivered</span></td>
            <td>
              <form action="vieworder.php" method="GET">
			  <!--<input type="hidden" name="order_id" value="12345">--> <!-- Replace with actual order ID -->
				<button type="submit" class="btn btn-sm btn-primary">View</button>
				</form>

            </td>
          </tr>
          <tr>
            <td>#1002</td>
            <td>2025-07-03</td>
            <td>1 item</td>
            <td><?php echo displayPrice(799); ?></td>
            <td> <span class="badge bg-warning text-dark">Processing</span> </td>
            <td> 
              <form action="vieworder.php" method="GET">
			  <!--<input type="hidden" name="order_id" value="12345">--> <!-- Replace with actual order ID -->
				<button type="submit" class="btn btn-sm btn-primary">View</button>
				</form>

            </td>
          </tr>
          <!-- More items can be added here -->
        </tbody>
      </table>
    </div>
  </div>

  <!-- FOOTER -->
  <?php include 'footer.php'; ?>
  <!-- /FOOTER -->
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
</body>
</html>
