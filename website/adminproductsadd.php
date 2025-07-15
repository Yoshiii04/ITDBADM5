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
  <?php $currentPage = basename($_SERVER['PHP_SELF']); ?>

  <!-- Dashboard Layout -->
  <div class="dashboard-container">
    
    <!-- Sidebar -->
    <?php include 'adminsidebar.php'; ?> 

    <!-- Main Content -->
    <div class="main-content">
      <h1>Product Management</h1>
      <form class="product-form">
        <h2>Add Product</h2>

        <label for="product-name">Product Name</label>
        <input type="text" id="product-name" name="product-name" placeholder="e.g. RGB Gaming Mouse" required>

        <label for="product-price">Price</label>
        <input type="number" id="product-price" name="product-price" placeholder="e.g. 999.99" step="0.01" required>

        <label for="product-discount">Discount (%)</label>
        <input type="number" id="product-discount" name="product-discount" placeholder="e.g. 10" min="0" max="100" step="1">

        <label for="product-category">Category</label>
        <select id="product-category" name="product-category" required>
          <option value="">-- Select Category --</option>
          <option value="mice">Mice</option>
          <option value="keyboards">Keyboards</option>
          <option value="monitors">Monitors</option>
          <option value="accessories">Accessories</option>
        </select>

        <label for="product-stock">Stock Status</label>
        <select id="product-stock" name="product-stock" required>
          <option value="in-stock">In Stock</option>
          <option value="out-of-stock">Out of Stock</option>
        </select>

        <label for="product-images">Upload Images</label>
        <input type="file" id="product-images" name="product-images" accept="image/*" multiple>

        <label for="product-variations">Variations</label>
        <input type="text" id="product-variations" name="product-variations" placeholder="e.g. Color: Black, White">

        <div class="btn-group">
          <button type="submit">Save Product</button>
          <button type="button" class="delete-btn">Delete Product</button>
        </div>
      </form>
    </div>

  </div> <!-- CLOSE dashboard-container here -->
   <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>