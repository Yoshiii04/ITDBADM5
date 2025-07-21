<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Product Management</title>
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
  
  // Handle form submission
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $product_name = $_POST['product-name'];
      $price = $_POST['product-price'];
      $quantity = $_POST['product-quantity'];
      $supplier = $_POST['product-supplier'];
      
      $sql = "INSERT INTO products (product_name, price, quantity, supplier) 
              VALUES ('$product_name', $price, $quantity, '$supplier')";
      
      if ($conn->query($sql) === TRUE) {
          $success_message = "Product added successfully!";
      } else {
          $error_message = "Error: " . $sql . "<br>" . $conn->error;
      }
  }
  ?>

  <!-- Dashboard Layout -->
  <div class="dashboard-container">
    
    <!-- Sidebar -->
    <?php include 'adminsidebar.php'; ?> 

    <!-- Main Content -->
    <div class="main-content">
      <h1>Product Management</h1>
      <form class="product-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <h2>Add Product</h2>

        <?php if (isset($success_message)): ?>
          <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
          <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <label for="product-name">Product Name</label>
        <input type="text" id="product-name" name="product-name" placeholder="e.g. RGB Gaming Mouse" required>

        <label for="product-price">Price</label>
        <input type="number" id="product-price" name="product-price" placeholder="e.g. 999.99" step="0.01" required>

        <label for="product-quantity">Quantity</label>
        <input type="number" id="product-quantity" name="product-quantity" placeholder="e.g. 100" required>

        <label for="product-supplier">Supplier</label>
        <input type="text" id="product-supplier" name="product-supplier" placeholder="e.g. Tech Supplier Inc." required>

        <div class="btn-group">
          <button type="submit">Save Product</button>
          <button type="reset" class="reset-btn">Reset Form</button>
        </div>
      </form>
    </div>

  </div>
  
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>