<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" >
  <meta name="viewport" content="width=device-width, initial-scale=1.0" >
  <title>Wishlist</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/font-awesome.min.css">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
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
						<h3 class="breadcrumb-header">Wishlist</h3>
						<ul class="breadcrumb-tree">
							<li>Wishlist</li>
							<li class="active">My Wishlist</li>
						</ul>
					</div>
				</div>
				<!-- /row -->
			</div>
			<!-- /container -->
		</div>
		<!-- /BREADCRUMB -->

  <div class="container my-5">
    <h2 class="text-center mb-4">My Wishlist</h2>
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead class="thead-dark">
          <tr>
            <th scope="col">Image</th>
            <th scope="col">Product Name</th>
            <th scope="col">Price</th>
            <th scope="col">Availability</th>
            <th scope="col">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><img src="img/product01.png" width="80" alt="Product"></td>
            <td>RGB Mechanical Keyboard</td>
            <td><?php echo displayPrice(60); ?></td>
            <td><span class="text-success">In Stock</span></td>
            <td>
              <button class="btn btn-sm btn-primary">Add to Cart</button>
              <button class="btn btn-sm btn-danger">Remove</button>
            </td>
          </tr>
          <tr>
            <td><img src="img/product02.png" width="80" alt="Product"></td>
            <td>Wireless Gaming Mouse</td>
            <td><?php echo displayPrice(70); ?></td>
            <td><span class="text-danger">Out of Stock</span></td>
            <td>
              <button class="btn btn-sm btn-secondary" disabled>Add to Cart</button>
              <button class="btn btn-sm btn-danger">Remove</button>
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
