<?php 

include 'config.php';
include_once 'currency.php';

// Check connection
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    $cartData = ['items' => [], 'total' => 0, 'count' => 0]; // Fallback
} else {
    function getCartItems($conn) {
        $sql = "SELECT c.item_id, c.quantity, c.product_id, p.name, p.price, p.stock 
                FROM cart c 
                JOIN products p ON c.product_id = p.product_id 
                WHERE p.stock > 0";
        $result = $conn->query($sql);
        $items = [];
        $total = 0;

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
                $total += $row['price'] * $row['quantity'];
            }
        } else {
            error_log("Query failed or no results: " . $conn->error);
        }
        return ['items' => $items, 'total' => $total, 'count' => count($items)];
    }

    // Get cart data
    $cartData = getCartItems($conn);
}

// Ensure $cartData is always defined
$cartData = isset($cartData) ? $cartData : ['items' => [], 'total' => 0, 'count' => 0];

 ?>



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
								<!-- Cart -->
					<div class="dropdown">
						<a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
							<i class="fa fa-shopping-cart"></i>
							<span>Your Cart</span>
							<div class="qty"><?php echo $cartData['count']; ?></div>
						</a>
						<div class="cart-dropdown">
							<div class="cart-list">
								<?php if (!empty($cartData['items']) && is_array($cartData['items'])): ?>
									<?php foreach ($cartData['items'] as $item): ?>
										<div class="product-widget">
											<div class="product-img">
												<img src="./img/product<?php echo str_pad($item['product_id'], 2, '0', STR_PAD_LEFT); ?>.png" alt="<?php echo htmlspecialchars($item['name']); ?>">
											</div>
											<div class="product-body">
												<h3 class="product-name"><a href="product.php?id=<?php echo $item['product_id']; ?>"><?php echo htmlspecialchars($item['name']); ?></a></h3>
												<h4 class="product-price"><span class="qty"><?php echo $item['quantity']; ?>x</span><?php echo displayPrice($item['price']); ?></h4>
											</div>
											<button class="delete" onclick="removeFromCart(<?php echo $item['item_id']; ?>)"><i class="fa fa-close"></i></button>
										</div>
									<?php endforeach; ?>
								<?php else: ?>
									<div class="product-widget">
										<div class="product-body">
											<h3 class="product-name">Your cart is empty</h3>
										</div>
									</div>
								<?php endif; ?>
							</div>
							<div class="cart-summary">
								<small><?php echo $cartData['count']; ?> Item(s) selected</small>
								<h5>SUBTOTAL: <?php echo displayPrice($cartData['total']); ?></h5>
							</div>
							<div class="cart-btns">
								<a href="cart.php">View Cart</a>
								<a href="checkout.php">Checkout <i class="fa fa-arrow-circle-right"></i></a>
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
		 <script>

					
			function removeFromCart(itemId) {
				if (confirm('Remove item from cart?')) {
					fetch('remove_from_cart.php', {
						method: 'POST',
						headers: {
							'Content-Type': 'application/x-www-form-urlencoded',
						},
						body: 'item_id=' + itemId
					})
					.then(response => response.json())
					.then(data => {
						if (data.success) {
							location.reload();
						} else {
							alert('Error removing item');
						}
					});
				}
			}

		 </script>