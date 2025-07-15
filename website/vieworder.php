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

 <!-- NEWSLETTER -->
		<div id="newsletter" class="section">
			<!-- container -->
			<div class="container">
				<!-- row -->
				<div class="row">
					<div class="col-md-12">
						<div class="newsletter">
							<p>Sign Up for the <strong>NEWSLETTER</strong></p>
							<form>
								<input class="input" type="email" placeholder="Enter Your Email">
								<button class="newsletter-btn"><i class="fa fa-envelope"></i> Subscribe</button>
							</form>
							<ul class="newsletter-follow">
								<li>
									<a href="#"><i class="fa fa-facebook"></i></a>
								</li>
								<li>
									<a href="#"><i class="fa fa-twitter"></i></a>
								</li>
								<li>
									<a href="#"><i class="fa fa-instagram"></i></a>
								</li>
								<li>
									<a href="#"><i class="fa fa-pinterest"></i></a>
								</li>
							</ul>
						</div>
					</div>
				</div>
				<!-- /row -->
			</div>
			<!-- /container -->
		</div>
		<!-- /NEWSLETTER -->

		<!-- FOOTER -->
		<footer id="footer">
			<!-- top footer -->
			<div class="section">
				<!-- container -->
				<div class="container">
					<!-- row -->
					<div class="row">
						<div class="col-md-3 col-xs-6">
							<div class="footer">
								<h3 class="footer-title">About Us</h3>
								<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut.</p>
								<ul class="footer-links">
									<li><a href="#"><i class="fa fa-map-marker"></i>De La Salle University, Manila</a></li>
									<li><a href="#"><i class="fa fa-phone"></i>(632) 8634-1111</a></li>
									<li><a href="#"><i class="fa fa-envelope-o"></i>bytech@email.com</a></li>
								</ul>
							</div>
						</div>

						<div class="col-md-3 col-xs-6">
							<div class="footer">
								<h3 class="footer-title">Categories</h3>
								<ul class="footer-links">
									<li><a href="#">Hot deals</a></li>
									<li><a href="#">Laptops</a></li>
									<li><a href="#">Smartphones</a></li>
									<li><a href="#">Cameras</a></li>
									<li><a href="#">Accessories</a></li>
								</ul>
							</div>
						</div>

						<div class="clearfix visible-xs"></div>

						<div class="col-md-3 col-xs-6">
							<div class="footer">
								<h3 class="footer-title">Information</h3>
								<ul class="footer-links">
									<li><a href="#">About Us</a></li>
									<li><a href="#">Contact Us</a></li>
									<li><a href="#">Privacy Policy</a></li>
									<li><a href="#">Orders and Returns</a></li>
									<li><a href="#">Terms & Conditions</a></li>
								</ul>
							</div>
						</div>

						<div class="col-md-3 col-xs-6">
							<div class="footer">
								<h3 class="footer-title">Service</h3>
								<ul class="footer-links">
									<li><a href="#">My Account</a></li>
									<li><a href="#">View Cart</a></li>
									<li><a href="#">Wishlist</a></li>
									<li><a href="#">Track My Order</a></li>
									<li><a href="#">Help</a></li>
								</ul>
							</div>
						</div>
					</div>
					<!-- /row -->
				</div>
				<!-- /container -->
			</div>
			<!-- /top footer -->

			<!-- bottom footer -->
			<div id="bottom-footer" class="section">
				<div class="container">
					<!-- row -->
					<div class="row">
						<div class="col-md-12 text-center">
							<ul class="footer-payments">
								<li><a href="#"><i class="fa fa-cc-visa"></i></a></li>
								<li><a href="#"><i class="fa fa-credit-card"></i></a></li>
								<li><a href="#"><i class="fa fa-cc-paypal"></i></a></li>
								<li><a href="#"><i class="fa fa-cc-mastercard"></i></a></li>
								<li><a href="#"><i class="fa fa-cc-discover"></i></a></li>
								<li><a href="#"><i class="fa fa-cc-amex"></i></a></li>
							</ul>
							<span class="copyright">
								<!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
								Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | This template is made with <i class="fa fa-heart-o" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib</a>
							<!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
							</span>


						</div>
					</div>
						<!-- /row -->
				</div>
				<!-- /container -->
			</div>
			<!-- /bottom footer -->
		</footer>
		<!-- /FOOTER -->

  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
</body>
</html>
