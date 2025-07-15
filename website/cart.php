<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/font-awesome.min.css" />
    <link rel="stylesheet" href="css/style.css" />
  </head>
  <body>
    <!-- currency, header, navigation, footer are important in each page if they require a header n a footer -->
	<!-- currency --> 
	<?php include 'currency.php'; ?>
	<!-- /currency --> 

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
						<h3 class="breadcrumb-header">Cart</h3>
						<ul class="breadcrumb-tree">
							<li>Cart</li>
							<li class="active">My Cart</li>
						</ul>
					</div>
				</div>
				<!-- /row -->
			</div>
			<!-- /container -->
		</div>
		<!-- /BREADCRUMB -->


    <div class="container mt-5">
      <h2 class="text-center mb-4">Your Shopping Cart</h2>
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Product</th>
              <th>Name</th>
              <th>Price</th>
              <th>Quantity</th>
              <th>Total</th>
              <th>Remove</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><img src="img/product01.png" alt="Product" width="50" /></td>
              <td>RGB Gaming Mouse</td>
              <td><?php echo displayPrice(580); ?></td>
              <td>
                <input type="number" value="1" class="form-control" style="width: 70px;" />
              </td>
              <td><?php echo displayPrice(580); ?></td>
              <td><button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button></td>
            </tr>
            <!-- Repeat rows as needed -->
          </tbody>
        </table>
      </div>

      <div class="row justify-content-end">
        <div class="col-md-4">
          <h4>Cart Summary</h4>
          <ul class="list-group">
            <li class="list-group-item">Subtotal: <strong><?php echo displayPrice(580); ?></strong></li>
            <li class="list-group-item">Shipping: <strong><?php echo displayPrice(20); ?></strong></li>
            <li class="list-group-item">Total: <strong><?php echo displayPrice(600); ?></strong></li>
          </ul>
          <a href="checkout.php" class="btn btn-success btn-block mt-3">Proceed to Checkout</a>
        </div>
      </div>
    </div>

	<!-- FOOTER --> 
	<?php include 'footer.php'; ?>
	<!-- /FOOTER -->

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
  </body>
</html>
