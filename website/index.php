<?php

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "online_store";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get featured products
$featured_products = $conn->query("
    SELECT p.*, c.name as category_name 
    FROM products p 
    JOIN categories c ON p.category_id = c.category_id
    ORDER BY RAND() LIMIT 12
");

// Get new arrival products
$new_products = $conn->query("
    SELECT p.*, c.name as category_name 
    FROM products p 
    JOIN categories c ON p.category_id = c.category_id
    ORDER BY p.product_id DESC LIMIT 8
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TechShop - Home</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="css/bootstrap.min.css"/>
    <link type="text/css" rel="stylesheet" href="css/slick.css"/>
    <link type="text/css" rel="stylesheet" href="css/slick-theme.css"/>
    <link type="text/css" rel="stylesheet" href="css/nouislider.min.css"/>
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link type="text/css" rel="stylesheet" href="css/style.css"/>
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
    <?php $currentPage = "home"; ?>
    
    <!-- currency --> 
    <?php include 'currency.php'; ?>
    <!-- /currency --> 

    <!-- HEADER --> 
    <?php include 'header.php'; ?>
    <!-- /HEADER -->

    <!-- NAVIGATION -->
    <?php include 'navigation.php'; ?>
    <!-- /NAVIGATION -->

    <!-- SECTION - Category Collections -->
    <div class="section">
        <div class="container">
            <div class="row">
                <!-- Keyboard Collection -->
                <div class="col-md-3 col-xs-6">
                    <div class="shop">
                        <div class="shop-img">
                            <img src="./img/product15.png" alt="Keyboards">
                        </div>
                        <div class="shop-body">
                            <h3>Keyboard<br>Collection</h3>
                            <a href="store.php?category_id=2" class="cta-btn">Shop now <i class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>
                
                <!-- Headphone Collection -->
                <div class="col-md-3 col-xs-6">
                    <div class="shop">
                        <div class="shop-img">
                            <img src="./img/product05.png" alt="Headphones">
                        </div>
                        <div class="shop-body">
                            <h3>Headphone<br>Collection</h3>
                            <a href="store.php?category_id=4" class="cta-btn">Shop now <i class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>
                
                <!-- Monitor Collection -->
                <div class="col-md-3 col-xs-6">
                    <div class="shop">
                        <div class="shop-img">
                            <img src="./img/product02.png" alt="Monitors">
                        </div>
                        <div class="shop-body">
                            <h3>Monitor<br>Collection</h3>
                            <a href="store.php?category_id=3" class="cta-btn">Shop now <i class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>
                
                <!-- Mouse Collection -->
                <div class="col-md-3 col-xs-6">
                    <div class="shop">
                        <div class="shop-img">
                            <img src="./img/product09.png" alt="Mice">
                        </div>
                        <div class="shop-body">
                            <h3>Mouse<br>Collection</h3>
                            <a href="store.php?category_id=1" class="cta-btn">Shop now <i class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION - Featured Products -->
    <div class="section">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="section-title">
                        <h3 class="title">Featured Products</h3>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="row">
                        <div class="products-tabs">
                            <div id="tab1" class="tab-pane active">
                                <div class="products-slick" data-nav="#slick-nav-1">
                                    <?php while($product = $featured_products->fetch_assoc()): ?>
                                        <div class="product">
                                            <div class="product-img">
                                                <img src="./img/product<?= $product['product_id'] ?>.png" alt="<?= htmlspecialchars($product['name']) ?>">
                                                <div class="product-label">
                                                    <span class="sale">-10%</span>
                                                    <span class="new">NEW</span>
                                                </div>
                                            </div>
                                            <div class="product-body">
                                                <p class="product-category"><?= htmlspecialchars($product['category_name']) ?></p>
                                                <h3 class="product-name"><a href="product.php?id=<?= $product['product_id'] ?>"><?= htmlspecialchars($product['name']) ?></a></h3>
                                                <h4 class="product-price">
                                                    <?= displayPrice($product['price']) ?>
                                                    <del class="product-old-price"><?= displayPrice($product['price'] * 1.1) ?></del>
                                                </h4>
                                                <div class="product-rating">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <?= $i <= round($product['rating'] ?? 0) ? '<i class="fa fa-star"></i>' : '<i class="fa fa-star-o"></i>' ?>
                                                    <?php endfor; ?>
                                                </div>
                                                <div class="product-btns">
                                                    <button class="add-to-wishlist"><i class="fa fa-heart-o"></i><span class="tooltipp">add to wishlist</span></button>
                                                    <a href="product.php?id=<?= $product['product_id'] ?>" class="quick-view"><i class="fa fa-eye"></i><span class="tooltipp">quick view</span></a>
                                                </div>
                                            </div>
                                            <div class="add-to-cart">
                                                <form action="cart.php" method="post" style="display:inline;">
                                                    <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                                                    <button type="submit" class="add-to-cart-btn">
                                                        <i class="fa fa-shopping-cart"></i> add to cart
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                                <div id="slick-nav-1" class="products-slick-nav"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- HOT DEAL SECTION -->
    <div id="hot-deal" class="section">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="hot-deal">
                        <h2 class="text-uppercase">hot deal this week</h2>
                        <p>New Collection Up to 50% OFF</p>
                        <a class="primary-btn cta-btn" href="store.php">Shop now</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION - New Arrivals -->
    <div class="section">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="section-title">
                        <h3 class="title">New Arrivals</h3>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="row">
                        <div class="products-tabs">
                            <div id="tab2" class="tab-pane fade in active">
                                <div class="products-slick" data-nav="#slick-nav-2">
                                    <?php while($product = $new_products->fetch_assoc()): ?>
                                        <div class="product">
                                            <div class="product-img">
                                                <img src="./img/product<?= $product['product_id'] ?>.png" alt="<?= htmlspecialchars($product['name']) ?>">
                                                <div class="product-label">
                                                    <span class="new">NEW</span>
                                                </div>
                                            </div>
                                            <div class="product-body">
                                                <p class="product-category"><?= htmlspecialchars($product['category_name']) ?></p>
                                                <h3 class="product-name"><a href="product.php?id=<?= $product['product_id'] ?>"><?= htmlspecialchars($product['name']) ?></a></h3>
                                                <h4 class="product-price"><?= displayPrice($product['price']) ?></h4>
                                                <div class="product-rating">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <?= $i <= round($product['rating'] ?? 0) ? '<i class="fa fa-star"></i>' : '<i class="fa fa-star-o"></i>' ?>
                                                    <?php endfor; ?>
                                                </div>
                                                <div class="product-btns">
                                                    <button class="add-to-wishlist"><i class="fa fa-heart-o"></i><span class="tooltipp">add to wishlist</span></button>
                                                    <a href="product.php?id=<?= $product['product_id'] ?>" class="quick-view"><i class="fa fa-eye"></i><span class="tooltipp">quick view</span></a>
                                                </div>
                                            </div>
                                            <div class="add-to-cart">
                                                <form action="cart.php" method="post" style="display:inline;">
                                                    <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                                                    <button type="submit" class="add-to-cart-btn">
                                                        <i class="fa fa-shopping-cart"></i> add to cart
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                                <div id="slick-nav-2" class="products-slick-nav"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
<?php $conn->close(); ?>