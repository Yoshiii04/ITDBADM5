<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Product</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="css/admindash.css">
  <link rel="stylesheet" href="css/adminproductsadd.css">
</head>
<body>
  <?php 
  $currentPage = basename($_SERVER['PHP_SELF']);
  
  // Database connection
  $servername = "localhost";
  $username = "root";
  $password = "";
  $database = "user_database";
  
  $conn = new mysqli($servername, $username, $password, $database);
  
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }

  // Get product ID from URL
  $product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

  // Handle form submission
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $product_name = $_POST['product-name'];
      $price = $_POST['product-price'];
      $quantity = $_POST['product-quantity'];
      $supplier = $_POST['product-supplier'];
      
      $sql = "UPDATE products SET 
              product_name = '$product_name',
              price = $price,
              quantity = $quantity,
              supplier = '$supplier'
              WHERE product_id = $product_id";
      
      if ($conn->query($sql) === TRUE) {
          $success_message = "Product updated successfully!";
      } else {
          $error_message = "Error updating product: " . $conn->error;
      }
  }

  // Get current product data
  $sql = "SELECT * FROM products WHERE product_id = $product_id";
  $result = $conn->query($sql);
  $product = $result->fetch_assoc();

  if (!$product) {
      die("Product not found");
  }
  ?>

  <!-- Dashboard Layout -->
  <div class="dashboard-container">
    
    <!-- Sidebar -->
    <?php include 'adminsidebar.php'; ?> 

    <!-- Main Content -->
    <div class="main-content">
      <h1>Edit Product</h1>

      <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
      <?php endif; ?>
      
      <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
      <?php endif; ?>

      <form class="product-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]).'?id='.$product_id; ?>">
        <h2>Edit Product #<?php echo $product_id; ?></h2>

        <label for="product-name">Product Name</label>
        <input type="text" id="product-name" name="product-name" 
               value="<?php echo htmlspecialchars($product['product_name']); ?>" required>

        <label for="product-price">Price</label>
        <input type="number" id="product-price" name="product-price" 
               value="<?php echo htmlspecialchars($product['price']); ?>" step="0.01" required>

        <label for="product-quantity">Quantity</label>
        <input type="number" id="product-quantity" name="product-quantity" 
               value="<?php echo htmlspecialchars($product['quantity']); ?>" required>

        <label for="product-supplier">Supplier</label>
        <input type="text" id="product-supplier" name="product-supplier" 
               value="<?php echo htmlspecialchars($product['supplier']); ?>" required>

        <div class="btn-group">
          <button type="submit">Update Product</button>
          <a href="adminproductsedit.php" class="cancel-btn">Cancel</a>
        </div>
      </form>
    </div>

  </div>
  
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>