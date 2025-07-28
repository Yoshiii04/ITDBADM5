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
$category_names_param = isset($_GET['category_id']) ? explode(',', $_GET['category_id']) : [];
$category_names = [];
$category_ids = [];

// Get price filter
$price_min = isset($_GET['price_min']) ? floatval(str_replace(',', '', $_GET['price_min'])) : 1;
$price_max = isset($_GET['price_max']) ? floatval(str_replace(',', '', $_GET['price_max'])) : 100000;

// Fetch category IDs based on names
if (!empty($category_names_param)) {
    $lowered_names = array_map('strtolower', $category_names_param);

    $placeholders = implode(',', array_fill(0, count($lowered_names), '?'));
    $sql = "SELECT * FROM categories WHERE LOWER(name) IN ($placeholders)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param(str_repeat('s', count($lowered_names)), ...$lowered_names);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($category = $result->fetch_assoc()) {
            $category_ids[] = $category['category_id'];
            $category_names[] = $category['name'];
        }
        $stmt->close();
    }
}

$category_name = empty($category_names) ? "All Products" : implode(', ', $category_names);

// Get sort option (default: 0 = Popular - by name asc)
$sort = isset($_GET['sort']) ? intval($_GET['sort']) : 0;

// Get show option (default: 20)
$show_options = [20, 50];
$show_index = isset($_GET['show']) ? intval($_GET['show']) : 0;
$page = isset($_GET['page']) && intval($_GET['page']) > 0 ? intval($_GET['page']) : 1;
$show = $show_options[$show_index] ?? 20;

// Set order by clause based on sort option
$order_by = 'p.rating DESC'; // default (Popular)

switch ($sort) {
    case 0:
        $order_by = 'p.rating DESC'; // Popular
        break;
    case 1:
        $order_by = 'p.price ASC';
        break;
    case 2:
        $order_by = 'p.price DESC';
        break;
}

// Get products based on category filter and sort option
if (!empty($category_ids)) {
    $placeholders = implode(',', array_fill(0, count($category_ids), '?'));
    $sqlCount = "SELECT COUNT(*) as total 
                 FROM products 
                 WHERE category_id IN ($placeholders) 
                 AND price BETWEEN ? AND ?";
    $stmtCount = $conn->prepare($sqlCount);

    $typesCount = str_repeat('i', count($category_ids)) . 'dd';
    $paramsCount = array_merge($category_ids, [$price_min, $price_max]);

    $stmtCount->bind_param($typesCount, ...$paramsCount);
    $stmtCount->execute();
    $resultCount = $stmtCount->get_result();
    $totalProducts = $resultCount->fetch_assoc()['total'];
    $stmtCount->close();
} else {
    $sqlCount = "SELECT COUNT(*) as total FROM products WHERE price BETWEEN ? AND ?";
    $stmtCount = $conn->prepare($sqlCount);
    $stmtCount->bind_param('dd', $price_min, $price_max);
    $stmtCount->execute();
    $resultCount = $stmtCount->get_result();
    $totalProducts = $resultCount->fetch_assoc()['total'];
    $stmtCount->close();
}

//Calculate pagination
$totalPages = ceil($totalProducts / $show);
$offset = ($page - 1) * $show;

if (!empty($category_ids)) {
    $placeholders = implode(',', array_fill(0, count($category_ids), '?'));
    $sql = "SELECT p.*, c.name as category_name 
            FROM products p 
            JOIN categories c ON p.category_id = c.category_id
            WHERE p.category_id IN ($placeholders) 
            AND p.price BETWEEN ? AND ?
            ORDER BY $order_by
            LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);

    $types = str_repeat('i', count($category_ids)) . 'ddii';
    $params = array_merge($category_ids, [$price_min, $price_max, $show, $offset]);

    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $products = $stmt->get_result();
} else {
    $sql = "SELECT p.*, c.name as category_name 
            FROM products p 
            JOIN categories c ON p.category_id = c.category_id
            WHERE p.price BETWEEN ? AND ?
            ORDER BY $order_by
            LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ddii', $price_min, $price_max, $show, $offset);
    $stmt->execute();
    $products = $stmt->get_result();
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
                        <?php if (!empty($category_ids)): ?>
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
                        <div class="checkbox-filter" id="category-checkboxes">
                        <div class="input-checkbox">
                            <input type="checkbox" id="category-all" data-name="" <?= empty($category_ids) ? 'checked' : '' ?>>
                            <label for="category-all">
                                <span></span>
                                All Products
                            </label>
                        </div>
                        <?php while ($cat = $categories->fetch_assoc()): ?>
                            <?php $cat_name = strtolower($cat['name']); ?>
                            <div class="input-checkbox">
                                <input type="checkbox"
                                    id="category-<?= $cat['category_id'] ?>"
                                    class="category-filter"
                                    data-name="<?= $cat_name ?>"
                                    <?= in_array($cat['category_id'], $category_ids) ? 'checked' : '' ?>>
                                <label for="category-<?= $cat['category_id'] ?>">
                                    <span></span>
                                    <?= htmlspecialchars($cat['name']) ?>
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
                                <select class="input-select" id="sort-select" name="sort">
                                    <option value="0" <?= $sort === 0 ? 'selected' : '' ?>>Popular</option>
                                    <option value="1" <?= $sort === 1 ? 'selected' : '' ?>>Price: Low to High</option>
                                    <option value="2" <?= $sort === 2 ? 'selected' : '' ?>>Price: High to Low</option>
                                </select>
                            </label>
                            <label>
                                Show:
                                <select class="input-select" id="show-select" name="show">
                                    <option value="0" <?= $show === 20 ? 'selected' : '' ?>>20</option>
                                    <option value="1" <?= $show === 50 ? 'selected' : '' ?>>50</option>
                                </select>
                            </label>
                        </div>
                        <!-- remove store grid view for now -->
                    </div>
                    <!-- /store top filter -->

                    <!-- store products -->
                    <div class="row">
                    <?php if ($products && $products->num_rows > 0): ?>
                        <?php while($product = $products->fetch_assoc()): ?>
                            <div class="col-md-4 col-xs-6">
                                <div class="product">
                                    <div class="product-img">
                                        <?php if (isset($product['image']) && $product['image'] && file_exists($product['image'])): ?>
                                            <img src="<?php echo htmlspecialchars($product['image'] . '?v=' . time()); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                        <?php else: ?>
                                            No Image
                                        <?php endif; ?>
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
                                            <a href="product.php?id=<?= $product['product_id'] ?>" class="quick-view"><i class="fa fa-eye"></i><span class="tooltipp"> Quick View</span></a>
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
                            <?php
                            $queryParams = $_GET;
                            $queryParams['price_min'] = $price_min;
                            $queryParams['price_max'] = $price_max;
                            for ($i = 1; $i <= $totalPages; $i++) {
                                $queryParams['page'] = $i;
                                $url = htmlspecialchars($_SERVER['PHP_SELF'] . '?' . http_build_query($queryParams));
                                $activeClass = ($i === $page) ? 'active' : '';
                                echo "<li class=\"$activeClass\"><a href=\"$url\">$i</a></li>";
                            }
                            ?>
                            <?php if ($page < $totalPages): ?>
                                <?php
                                $queryParams['price_min'] = $price_min;
                                $queryParams['price_max'] = $price_max;
                                $queryParams['page'] = $page + 1;
                                $nextUrl = htmlspecialchars($_SERVER['PHP_SELF'] . '?' . http_build_query($queryParams));
                                ?>
                                <li><a href="<?= $nextUrl ?>"><i class="fa fa-angle-right"></i></a></li>
                            <?php endif; ?>
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
    
    <!-- Category Filter Script -->
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const checkboxes = document.querySelectorAll('.category-filter, #category-all');

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                // If "All Products" is checked, reload with no category_id filter
                if (checkbox.id === 'category-all') {
                    window.location.href = 'store.php';
                    return;
                }

                const selected = [];
                document.querySelectorAll('.category-filter:checked').forEach(cb => {
                    selected.push(cb.getAttribute('data-name'));
                });

                if (selected.length > 0) {
                    const params = new URLSearchParams(window.location.search);
                    params.set('category_id', selected.join(','));
                    params.set('price_min', document.getElementById('price-min').value || 1);
                    params.set('price_max', document.getElementById('price-max').value || 100000);
                    params.set('sort', document.getElementById('sort-select').value);
                    params.set('show', document.getElementById('show-select').value);

                    window.location.href = window.location.pathname + '?' + params.toString();
                } else {
                    window.location.href = 'store.php';
                }
            });
        });
    });
    </script>

    <!-- Sort Refresh Script -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
        const sortSelect = document.getElementById('sort-select');
        const showSelect = document.getElementById('show-select');

        function updateUrl() {
            const params = new URLSearchParams(window.location.search);

            params.set('sort', sortSelect.value);
            params.set('show', showSelect.value);

            // Preserve price
            params.set('price_min', document.getElementById('price-min').value || 1);
            params.set('price_max', document.getElementById('price-max').value || 100000);

            // Preserve category filter if any
            // (already in params, so no change needed)
            window.location.href = window.location.pathname + '?' + params.toString();
        }

        sortSelect.addEventListener('change', updateUrl);
        showSelect.addEventListener('change', updateUrl);
    });
    </script>

    <!-- Price Slider Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var priceSlider = document.getElementById('price-slider');
        var priceInputMin = document.getElementById('price-min');
        var priceInputMax = document.getElementById('price-max');

        function getURLParam(name) {
            const url = new URL(window.location.href);
            return url.searchParams.get(name);
        }

        if (priceSlider) {
            noUiSlider.create(priceSlider, {
                start: [
                    parseFloat(getURLParam('price_min')) || 1,
                    parseFloat(getURLParam('price_max')) || 100000
                ],
                connect: true,
                step: 1,
                range: {
                    'min': 1,
                    'max': 100000
                },
                format: {
                    to: value => Math.round(value),
                    from: value => parseFloat(value)
                }
            });

            // Sync slider -> inputs
            priceSlider.noUiSlider.on('update', function (values) {
                priceInputMin.value = Math.round(values[0]);
                priceInputMax.value = Math.round(values[1]);
            });

            // On slider change (user finishes sliding), reload URL with updated price
            priceSlider.noUiSlider.on('change', function (values) {
                updateUrlWithPrice(Math.round(values[0]), Math.round(values[1]));
            });
        }

        // When inputs change, update slider and reload URL
        priceInputMin.addEventListener('change', () => {
            let minVal = parseInt(priceInputMin.value) || 1;
            let maxVal = parseInt(priceInputMax.value) || 100000;

            if (minVal < 1) minVal = 1;
            if (maxVal > 100000) maxVal = 100000;
            if (minVal > maxVal) minVal = maxVal;

            priceInputMin.value = minVal;
            priceInputMax.value = maxVal;

            if (priceSlider) priceSlider.noUiSlider.set([minVal, maxVal]);

            updateUrlWithPrice(minVal, maxVal);
        });

        priceInputMax.addEventListener('change', () => {
            let minVal = parseInt(priceInputMin.value) || 1;
            let maxVal = parseInt(priceInputMax.value) || 100000;

            if (minVal < 1) minVal = 1;
            if (maxVal > 100000) maxVal = 100000;
            if (minVal > maxVal) maxVal = minVal;

            priceInputMin.value = minVal;
            priceInputMax.value = maxVal;

            if (priceSlider) priceSlider.noUiSlider.set([minVal, maxVal]);

            updateUrlWithPrice(minVal, maxVal);
        });

        function updateUrlWithPrice(min, max) {
            const params = new URLSearchParams(window.location.search);

            params.set('price_min', min);
            params.set('price_max', max);

            // Preserve sort and show selects
            params.set('sort', document.getElementById('sort-select').value);
            params.set('show', document.getElementById('show-select').value);

            // Preserve category filter
            const selected = [];
            document.querySelectorAll('.category-filter:checked').forEach(cb => {
                selected.push(cb.getAttribute('data-name'));
            });
            if (selected.length > 0) {
                params.set('category_id', selected.join(','));
            } else {
                params.delete('category_id');
            }

            window.location.href = window.location.pathname + '?' + params.toString();
        }
        });    
    </script>
</body>
</html>
<?php $conn->close(); ?>

