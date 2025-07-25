<?php include_once 'currency.php'; ?>



<!-- HEADER -->
		<header>
			<!-- TOP HEADER -->
			<div id="top-header">
				<div class="container">
					<ul class="header-links pull-left">
						<li><a href="#"><i class="fa fa-phone"></i> (632) 8634-1111</a></li>
						<li><a href="#"><i class="fa fa-envelope-o"></i> bytech@email.com</a></li>
						<li><a href="#"><i class="fa fa-map-marker"></i> De La Salle University, Manila</a></li>
					</ul>
					<ul class="header-links pull-right">

						 <!-- Currency Dropdown -->
						 <!-- whatever currency is selected, it will be the default currency and it shouldnt show up in the dropdown --> 
						<!-- Currency Dropdown -->
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
								<i class="fa fa-money"></i> 
								<?= htmlspecialchars($currency); ?> 
								<i class="fa fa-caret-down"></i>
							</a>
							<ul class="dropdown-menu">
								<?php foreach ($rates as $code => $rate): ?>
									<?php if ($code !== $currency): ?>
										<li><a href="?currency=<?= urlencode($code); ?>"><?= htmlspecialchars($code); ?></a></li>
									<?php endif; ?>
								<?php endforeach; ?>
							</ul>
						</li>

							<!-- Account Dropdown -->
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
									<i class="fa fa-user-o"></i> My Account <i class="fa fa-caret-down"></i>
								</a>
								<ul class="dropdown-menu">
									<?php if (!isset($_SESSION['username'])): ?>
										<li><a href="login.php">Login</a></li>
										<li><a href="register.php">Register</a></li>
									<?php else: ?>
										<li><a href="orderhistory.php">Order History</a></li>

										<?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
											<li><a href="admindash.php">Admin Panel</a></li>
										<?php endif; ?>

										<li><a href="logout.php">Logout</a></li>
									<?php endif; ?>
								</ul>
							</li>

					</ul>
				</div>
			</div>
			<!-- /TOP HEADER -->

			<!-- MAIN HEADER -->
			<div id="header">
				<!-- container -->
				<div class="container">
					<!-- row -->
					<div class="row">
						<!-- LOGO -->
						<div class="col-md-3">
							<div class="header-logo">
								<a href="index.php" class="logo">
									<img src="./img/bytechlogo.png" alt="">
								</a>
							</div>
						</div>
						<!-- /LOGO -->

						<!-- SEARCH BAR -->
						<div class="col-md-6">
							<div class="header-search">
								<form>
									<select class="input-select">
										<option value="0">All Categories</option>
										<option value="1">Keyboards</option>
										<option value="1">Headphones</option>
										<option value="1">Monitors</option>
										<option value="1">Mice</option>
									</select>
									<input class="input" placeholder="Search here">
									<button class="search-btn">Search</button>
								</form>
							</div>
						</div>
						<!-- /SEARCH BAR -->

						<!-- ACCOUNT -->
						<div class="col-md-3 clearfix">
							<div class="header-ctn">
								<!-- Wishlist -->
								<div>
									<a href="wishlist.php">
										<i class="fa fa-heart-o"></i>
										<span>Your Wishlist</span>
										<div class="qty">2</div>
									</a>
								</div>
								<!-- /Wishlist -->

								<!-- Cart -->
								<div class="dropdown">
									<a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
										<i class="fa fa-shopping-cart"></i>
										<span>Your Cart</span>
										<div class="qty">3</div>
									</a>
									<div class="cart-dropdown">
										<div class="cart-list">
											<div class="product-widget">
												<div class="product-img">
													<img src="./img/product01.png" alt="">
												</div>
												<div class="product-body">
													<h3 class="product-name"><a href="#">product name goes here</a></h3>
													<h4 class="product-price"><span class="qty">1x</span><?php echo displayPrice(980); ?></h4>
												</div>
												<button class="delete"><i class="fa fa-close"></i></button>
											</div>

											<div class="product-widget">
												<div class="product-img">
													<img src="./img/product02.png" alt="">
												</div>
												<div class="product-body">
													<h3 class="product-name"><a href="#">product name goes here</a></h3>
													<h4 class="product-price"><span class="qty">3x</span><?php echo displayPrice(980); ?></h4>
												</div>
												<button class="delete"><i class="fa fa-close"></i></button>
											</div>
										</div>
										<div class="cart-summary">
											<small>3 Item(s) selected</small>
											<h5>SUBTOTAL: <?php echo displayPrice(2940); ?></h5>
										</div>
										<div class="cart-btns">
											<a href="cart.php">View Cart</a>
											<a href="checkout.php">Checkout  <i class="fa fa-arrow-circle-right"></i></a>
										</div>
									</div>
								</div>
								<!-- /Cart -->

								<!-- Menu Toogle -->
								<div class="menu-toggle">
									<a href="#">
										<i class="fa fa-bars"></i>
										<span>Menu</span>
									</a>
								</div>
								<!-- /Menu Toogle -->
							</div>
						</div>
						<!-- /ACCOUNT -->
					</div>
					<!-- row -->
				</div>
				<!-- container -->
			</div>
			<!-- /MAIN HEADER -->
		</header>
		<!-- /HEADER -->