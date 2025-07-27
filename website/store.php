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

// Handle add to cart
if (isset($_POST['add_to_cart'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    
    // Check if product exists
    $product = $conn->query("SELECT * FROM products WHERE product_id = $product_id")->fetch_assoc();
    
    if ($product) {
        // Check if product already in cart
        $check = $conn->query("SELECT * FROM cart WHERE product_id = $product_id");
        if ($check->num_rows > 0) {
            // Update quantity if exists
            $conn->query("UPDATE cart SET quantity = quantity + $quantity WHERE product_id = $product_id");
        } else {
            // Add new item to cart
            $name = $conn->real_escape_string($product['name']);
            $price = $product['price'];
            $conn->query("INSERT INTO cart (product_id, name, price, quantity) VALUES ($product_id, '$name', $price, $quantity)");
        }
    }
    header("Location: cart.php");
    exit;
}

// Get category filter
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;
$category_name = "All Products";

if ($category_id > 0) {
    $stmt = $conn->prepare("SELECT name FROM categories WHERE category_id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $category = $result->fetch_assoc();
        $category_name = $category['name'];
    }
    $stmt->close();
}

// Get products
if ($category_id > 0) {
    $sql = "SELECT p.*, c.name as category_name 
            FROM products p 
            JOIN categories c ON p.category_id = c.category_id
            WHERE p.category_id = ?
            ORDER BY p.name";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $products = $stmt->get_result();
} else {
    $products = $conn->query("
        SELECT p.*, c.name as category_name 
        FROM products p 
        JOIN categories c ON p.category_id = c.category_id
        ORDER BY p.name
    ");
}

// Get all categories for sidebar
$categories = $conn->query("
    SELECT c.*, COUNT(p.product_id) as product_count 
    FROM categories c
    LEFT JOIN products p ON c.category_id = p.category_id
    GROUP BY c.category_id
    ORDER BY c.name
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($category_name) ?> - TechShop</title>
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
    <?php $currentPage = "store"; ?>
    
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
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <ul class="breadcrumb-tree">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="store.php">All Categories</a></li>
                        <?php if ($category_id > 0): ?>
                            <li class="active"><?= htmlspecialchars($category_name) ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- /BREADCRUMB -->

    <!-- SECTION -->
    <div class="section">
        <div class="container">
            <div class="row">
                <!-- ASIDE - Categories -->
                <div id="aside" class="col-md-3">
                    <div class="aside">
                        <h3 class="aside-title">Categories</h3>
                        <div class="checkbox-filter">
                            <div class="input-checkbox">
                                <input type="checkbox" id="category-all" <?= $category_id == 0 ? 'checked' : '' ?>>
                                <label for="category-all">
                                    <span></span>
                                    <a href="store.php">All Products</a>
                                </label>
                            </div>
                            <?php while($cat = $categories->fetch_assoc()): ?>
                                <div class="input-checkbox">
                                    <input type="checkbox" id="category-<?= $cat['category_id'] ?>" <?= $category_id == $cat['category_id'] ? 'checked' : '' ?>>
                                    <label for="category-<?= $cat['category_id'] ?>">
                                        <span></span>
                                        <a href="store.php?category_id=<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['name']) ?></a>
                                        <small>(<?= $cat['product_count'] ?>)</small>
                                    </label>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>

                    <!-- Price Filter -->
                    <div class="aside">
                        <h3 class="aside-title">Price</h3>
                        <div class="price-filter">
                            <div id="price-slider"></div>
                            <div class="input-number price-min">
                                <input id="price-min" type="number">
                                <span class="qty-up">+</span>
                                <span class="qty-down">-</span>
                            </div>
                            <span>-</span>
                            <div class="input-number price-max">
                                <input id="price-max" type="number">
                                <span class="qty-up">+</span>
                                <span class="qty-down">-</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /ASIDE -->

                <!-- STORE -->
                <div id="store" class="col-md-9">
                    <!-- store top filter -->
                    <div class="store-filter clearfix">
                        <div class="store-sort">
                            <label>
                                Sort By:
                                <select class="input-select">
                                    <option value="0">Popular</option>
                                    <option value="1">Price: Low to High</option>
                                    <option value="2">Price: High to Low</option>
                                </select>
                            </label>
                            <label>
                                Show:
                                <select class="input-select">
                                    <option value="0">20</option>
                                    <option value="1">50</option>
                                </select>
                            </label>
                        </div>
                        <ul class="store-grid">
                            <li class="active"><i class="fa fa-th"></i></li>
                            <li><a href="#"><i class="fa fa-th-list"></i></a></li>
                        </ul>
                    </div>
                    <!-- /store top filter -->

                    <!-- store products -->
                    <div class="row">
                    <?php if ($products && $products->num_rows > 0): ?>
                        <?php while($product = $products->fetch_assoc()): ?>
                            <div class="col-md-4 col-xs-6">
                                <div class="product">
                                    <div class="product-img">
                                        <img src="./img/product<?php echo str_pad($product['product_id'], 2, '0', STR_PAD_LEFT); ?>.png" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                        <?php if(rand(0,1) == 1): ?>
                                            <div class="product-label">
                                                <span class="sale">-<?= rand(10,30) ?>%</span>
                                                <?php if(rand(0,1) == 1): ?> <!-- new & sale , i dont know why is it randomized? based on who did this lol --> 
                                                    <span class="new">NEW</span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="product-body">
                                        <p class="product-category"><?= htmlspecialchars($product['category_name']) ?></p>
                                        <h3 class="product-name"><a href="product.php?id=<?= $product['product_id'] ?>"><?= htmlspecialchars($product['name']) ?></a></h3>
                                        <h4 class="product-price">
                                            <?= displayPrice($product['price']) ?>
                                            <?php if(rand(0,1) == 1): ?>
                                                <del class="product-old-price"><?= displayPrice($product['price'] * (1 + rand(10,30)/100)) ?></del>
                                            <?php endif; ?>
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
                                        <form method="post" action="store.php">
                                            <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" name="add_to_cart" class="add-to-cart-btn">
                                                <i class="fa fa-shopping-cart"></i> add to cart
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <p class="text-center">No products found in this category</p>
                        </div>
                    <?php endif; ?>
                </div>
                    <!-- /store products -->

                    <!-- store bottom filter -->
                    <div class="store-filter clearfix">
                        <span class="store-qty">Showing <?= $products->num_rows ?> products</span>
                        <ul class="store-pagination">
                            <li class="active">1</li>
                            <li><a href="#">2</a></li>
                            <li><a href="#">3</a></li>
                            <li><a href="#">4</a></li>
                            <li><a href="#"><i class="fa fa-angle-right"></i></a></li>
                        </ul>
                    </div>
                    <!-- /store bottom filter -->
                </div>
                <!-- /STORE -->
            </div>
        </div>
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
<?php $conn->close(); ?>