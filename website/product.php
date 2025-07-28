<?php

// MySQL connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "online_store";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch product
$sql = "SELECT p.*, c.name AS category_name FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id
        WHERE p.product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    echo "Product not found.";
    exit;
}

if (isset($_POST['add_to_cart'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = max(1, intval($_POST['quantity']));
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $session_id = session_id();

    // Check product exists and is in stock
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ? AND stock > 0");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($product) {
        // Check if product already in cart
        if ($user_id) {
            $stmt = $conn->prepare("SELECT * FROM cart WHERE product_id = ? AND user_id = ?");
            $stmt->bind_param("ii", $product_id, $user_id);
        } else {
            $stmt = $conn->prepare("SELECT * FROM cart WHERE product_id = ? AND session_id = ?");
            $stmt->bind_param("is", $product_id, $session_id);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows > 0) {
            // Update existing
            if ($user_id) {
                $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + ? WHERE product_id = ? AND user_id = ?");
                $stmt->bind_param("iii", $quantity, $product_id, $user_id);
            } else {
                $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + ? WHERE product_id = ? AND session_id = ?");
                $stmt->bind_param("iis", $quantity, $product_id, $session_id);
            }
        } else {
            // Insert new
            $name = $product['name'];
            $price = $product['price'];
            if ($user_id) {
                $stmt = $conn->prepare("INSERT INTO cart (product_id, name, price, quantity, user_id) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("isdii", $product_id, $name, $price, $quantity, $user_id);
            } else {
                $stmt = $conn->prepare("INSERT INTO cart (product_id, name, price, quantity, session_id) VALUES (?, ?, ?, ?, ?)");
				$stmt->bind_param("isdis", $product_id, $name, $price, $quantity, $session_id);
            }
        }

        if ($stmt->execute()) {
            echo "<script>alert('Added to cart successfully!'); window.location.href = 'product.php?id={$product_id}';</script>";
            exit;
        } else {
            echo "<script>alert('Failed to add to cart.');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Product not found or out of stock.');</script>";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		 <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

		<title>Products Page</title>

 		<!-- Google font -->
 		<link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700" rel="stylesheet">

 		<!-- Bootstrap -->
 		<link type="text/css" rel="stylesheet" href="css/bootstrap.min.css"/>

 		<!-- Slick -->
 		<link type="text/css" rel="stylesheet" href="css/slick.css"/>
 		<link type="text/css" rel="stylesheet" href="css/slick-theme.css"/>

 		<!-- nouislider -->
 		<link type="text/css" rel="stylesheet" href="css/nouislider.min.css"/>

 		<!-- Font Awesome Icon -->
 		<link rel="stylesheet" href="css/font-awesome.min.css">

 		<!-- Custom stlylesheet -->
 		<link type="text/css" rel="stylesheet" href="css/style.css"/>

		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->

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
						<ul class="breadcrumb-tree">
							<li><a href="#">Home</a></li>
							<li><a href="#">All Categories</a></li>
							<li><a href="#"><?php echo htmlspecialchars($product['category_name']); ?></a></li>
							<li class="active"><?php echo htmlspecialchars($product['name']); ?></li>
						</ul>
					</div>
				</div>
				<!-- /row -->
			</div>
			<!-- /container -->
		</div>
		<!-- /BREADCRUMB -->

		<!-- SECTION -->
		<div class="section">
			<!-- container -->
			<div class="container">
				<!-- row -->
				<div class="row">
					<!-- Product main img -->
					<div class="col-md-5">
						<div id="product-main-img">
							<div class="product-preview">
								<img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
							</div>
						</div>
					</div>
					<!-- /Product main img -->

					<!-- Product details -->
					<div class="col-md-5">
						<div class="product-details">
							<h2 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h2>
							<div>
								<div class="product-rating" >
								<?php
									// Ensure rating is a float and within 0-5 range
									if ($product['rating'] !== null) {
										if (is_numeric($product['rating'])) {
											$product['rating'] = floatval($product['rating']);
										} else {
											$product['rating'] = 0; // Default to 0 if not numeric
										}
										if ($product['rating'] < 0) {
											$product['rating'] = 0;
										$product['rating'] = 0; // Default to 0 if rating is null
											$product['rating'] = 5;
										}
									} else {
										$product['rating'] = 0; // Default to 0 if no rating
									}
									// Calculate full and half stars based on rating
									$fullStars = floor($product['rating']);
									// Determine if a half-star should be displayed
									$halfStar = ($product['rating'] - $fullStars) >= 0.5;
								?>
								<div class="rating-stars">
									<?php for ($i = 0; $i < $fullStars; $i++): ?>
										<i class="fa fa-star"></i>
									<?php endfor; ?>
									<?php if ($halfStar): ?>
										<i class="fa fa-star-half-o"></i>
									<?php endif; ?>
									<?php for ($i = $fullStars + $halfStar; $i < 5; $i++): ?>
										<i class="fa fa-star-o"></i>
									<?php endfor; ?>
								</div>
								</div>
								<a>As Rated by Our Staff</a>
							</div>
							<div>
								<h3 class="product-price"><?php echo displayPrice($product['price']); ?></h3>
								<span class="product-available">
									<?php echo ($product['stock'] > 0) ? "In Stock" : "Out of Stock"; ?>
								</span>
							</div>

							<br>
							<div class="add-to-cart">
							<form method="POST" id="addToCartForm">
								<input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
								<div class="add-to-cart">
									<div class="qty-label">
										Qty
										<div class="input-number">
											<input type="number" name="quantity" value="1" min="1" max="<?php echo htmlspecialchars($product['stock']); ?>" required>
											<span class="qty-up">+</span>
											<span class="qty-down">-</span>
										</div>
									</div>
									<button type="submit" name="add_to_cart" class="add-to-cart-btn"><i class="fa fa-shopping-cart"></i> Add to cart</button>
								</div>
							</form>

							<ul class="product-links">
								<li>Category:</li>
								<li><a><?php echo htmlspecialchars($product['category_name']); ?></a></li>
							</ul>

							<ul class="product-links">
								<li>Share:</li>
								<li><a href="#"><i class="fa fa-facebook"></i></a></li>
								<li><a href="#"><i class="fa fa-twitter"></i></a></li>
								<li><a href="#"><i class="fa fa-google-plus"></i></a></li>
								<li><a href="#"><i class="fa fa-envelope"></i></a></li>
							</ul>

						</div>
						</div>
					</div>
					<!-- /Product details -->

					<!-- Product tab -->
					<div class="col-md-12">
						<div id="product-tab">
							<!-- product tab nav -->
							<ul class="tab-nav">
								<li class="active"><a data-toggle="tab" href="#tab1">Description</a></li>
								<li><a data-toggle="tab" href="#tab2">Details</a></li>
							</ul>
							<!-- /product tab nav -->

							<!-- product tab content -->
							<div class="tab-content">
								<!-- tab1  -->
								<div id="tab1" class="tab-pane fade in active">
									<div class="row">
										<div class="col-md-12">
											<p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
									</div>
									</div>
								</div>
								<!-- /tab1  -->

								<!-- tab2  -->
								<div id="tab2" class="tab-pane fade in">
									<div class="row">
										<div class="col-md-12">
											<p>All our products are covered by our store’s standard terms and conditions. We aim to provide a smooth shopping experience—from secure checkout to timely delivery. Orders are typically processed within 1–2 business days and shipped via trusted couriers. If you're not completely satisfied with your purchase, you may request a return or exchange within 14 days of delivery, subject to our return policy.</p>Personal data is handled with care in accordance with our privacy policy; we do not share your information with third parties without consent. For more details, please review our full Terms & Conditions and Privacy Policy available on our website.</p>
										</div>
									</div>
								</div>
								<!-- /tab2  -->
							</div>
							<!-- /product tab content  -->
						</div>
					</div>
					<!-- /product tab -->
				</div>
				<!-- /row -->
			</div>
			<!-- /container -->
		</div>
		<!-- /SECTION -->

		<!-- FOOTER -->
		<?php include 'footer.php'; ?>
		<!-- /FOOTER -->

		<!-- jQuery Plugins -->
		<script src="js/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/slick.min.js"></script>
		<script src="js/nouislider.min.js"></script>
		<script src="js/jquery.zoom.min.js"></script>
		<script src="js/main.js"></script>

	</body>
</html>
