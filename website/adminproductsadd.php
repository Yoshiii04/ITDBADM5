<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Add Product</title>
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
  $database = "online_store";
  
  $conn = new mysqli($servername, $username, $password, $database);
  
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }
  
  // Handle form submission
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $product_name = $_POST['product-name'];
      $description = $_POST['product-description'];
      $price = $_POST['product-price'];
      $category_id = $_POST['product-category'];
      $stock = $_POST['product-stock'];
      
      $sql = "INSERT INTO products (name, description, price, category_id, stock) 
              VALUES (?, ?, ?, ?, ?)";
      
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("ssdii", $product_name, $description, $price, $category_id, $stock);
      
      if ($stmt->execute()) {
          $success_message = "Product added successfully!";
      } else {
          $error_message = "Error: " . $stmt->error;
      }
      $stmt->close();
  }
  
  // Get categories for dropdown
  $categories = $conn->query("SELECT * FROM categories ORDER BY name");
  ?>

  <!-- Dashboard Layout -->
  <div class="dashboard-container">
    
    <!-- Sidebar -->
    <?php include 'adminsidebar.php'; ?> 

    <!-- Main Content -->
    <div class="main-content">
      <h1>Add Product</h1>

      <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
      <?php endif; ?>
      
      <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
      <?php endif; ?>

      <form class="product-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <h2>Add New Product</h2>

        <label for="product-name">Product Name</label>
        <input type="text" id="product-name" name="product-name" placeholder="e.g. RGB Gaming Mouse" required>

        <label for="product-description">Description</label>
        <textarea id="product-description" name="product-description" rows="3"></textarea>

        <label for="product-price">Price</label>
        <input type="number" id="product-price" name="product-price" placeholder="e.g. 999.99" step="0.01" min="0" required>

        <label for="product-category">Category</label>
        <select id="product-category" name="product-category" required>
          <option value="">-- Select Category --</option>
          <?php while($category = $categories->fetch_assoc()): ?>
            <option value="<?php echo $category['category_id']; ?>">
              <?php echo htmlspecialchars($category['name']); ?>
            </option>
          <?php endwhile; ?>
        </select>

        <label for="product-stock">Stock Quantity</label>
        <input type="number" id="product-stock" name="product-stock" placeholder="e.g. 100" min="0" required>

        <div class="btn-group">
          <button type="submit" class="btn-primary">Save Product</button>
          <button type="reset" class="btn-secondary">Reset Form</button>
        </div>
      </form>
    </div>
  </div>
  
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>