<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Product Categories</title>
  <link rel="stylesheet" href="css/bootstrap.min.css" />
  <link rel="stylesheet" href="css/font-awesome.min.css" />
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>
<?php 
$currentPage = "categories";

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "online_store";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get all categories from database
$categories = $conn->query("SELECT * FROM categories ORDER BY name");
?>

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
                <h3 class="breadcrumb-header">Categories</h3>
                <ul class="breadcrumb-tree">
                    <li class="active">Categories</li>
                </ul>
            </div>
        </div>
        <!-- /row -->
    </div>
    <!-- /container -->
</div>
<!-- /BREADCRUMB -->

<!-- CATEGORY SECTION --> 
<h2 class="text-center mb-4">Categories</h2>
<section class="section">
    <div class="container">
        <div class="row">
            <!-- "All Products" category -->
            <div class="col-md-3 col-xs-6">
            <div class="product"> 
                <div class="category">
                <a href="store.php">
                    <div class="category-img">
                    <img src="./img/product01.png" alt="All Products">
                    </div>
                </a>
                <div class="product-body">
                    <h3 class="product-category">
                    <a href="store.php">All Products</a>
                    </h3>
                </div>
                </div>
            </div>
            </div>

            <!-- Dynamic categories from database -->
            <?php 
                $category_images = [
                    'Keyboards' => 'product15.png',
                    'Headphones' => 'product09.png',
                    'Monitors' => 'product02.png',
                    'Mice' => 'product05.png'
                ];

                if ($categories->num_rows > 0) {
                    while($category = $categories->fetch_assoc()) {
                        $category_name = $category['name'];
                        $category_slug = strtolower($category_name); // convert to lowercase
                        $image = isset($category_images[$category_name]) ? $category_images[$category_name] : 'product05.png';
                        ?>
                        <div class="col-md-3 col-xs-6">
                            <div class="product"> 
                                <div class="category">
                                    <a href="store.php?category_id=<?php echo urlencode($category_slug); ?>">
                                        <div class="category-img">
                                            <img src="./img/<?php echo $image; ?>" alt="<?php echo htmlspecialchars($category_name); ?>">
                                        </div>
                                    </a>
                                    <div class="product-body">
                                        <h3 class="product-category">
                                            <a href="store.php?category_id=<?php echo urlencode($category_slug); ?>">
                                                <?php echo htmlspecialchars($category_name); ?>
                                            </a>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<div class="col-12"><p class="text-center">No categories found</p></div>';
                }
                ?>
        </div>
    </div>
</section>

<!-- FOOTER -->
<?php include 'footer.php'; ?>
<!-- /FOOTER -->

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>