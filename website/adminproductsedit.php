<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Manage Products</title>
  <link rel="stylesheet" href="css/admindash.css?v=1.6">
</head>
<body>
  <?php 
  session_start();
  $currentPage = basename($_SERVER['PHP_SELF']);

  // Check if user is admin
  if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
      echo "Access denied. You are not authorized to view this page.";
      exit;
  }

  include 'config.php'; // db config

  // Handle delete action
  if (isset($_GET['delete_id'])) {
      $delete_id = intval($_GET['delete_id']);
      // Fetch image path to delete file
      $sql = "SELECT image FROM products WHERE product_id = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("i", $delete_id);
      $stmt->execute();
      $result = $stmt->get_result();
      $product = $result->fetch_assoc();
      $stmt->close();

      // Delete product
      $sql = "DELETE FROM products WHERE product_id = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("i", $delete_id);
      
      if ($stmt->execute()) {
          // Delete image file if exists
          if (isset($product['image']) && $product['image'] && file_exists($product['image'])) {
              unlink($product['image']);
          }
          $success_message = "Product deleted successfully!";
      } else {
          $error_message = "Error deleting product: " . $stmt->error;
      }
      $stmt->close();
  }

  // Fetch products
  $sql = "SELECT p.*, c.name as category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.category_id
          ORDER BY p.product_id";
  $result = $conn->query($sql);
  ?>

  <div class="dashboard-container">
    <?php include 'adminsidebar.php'; ?>

    <main class="main-content">
      <h1>Manage Products</h1>

      <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
      <?php endif; ?>
      
      <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
      <?php endif; ?>

      <div class="table-header">
        <a href="adminproductsadd.php" class="btn-add">Add New Product</a>
      </div>

      <table class="admin-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Product Name</th>
            <th>Description</th>
            <th>Price</th>
            <th>Category</th>
            <th>Stock</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?php echo htmlspecialchars($row['product_id']); ?></td>
                <td>
                  <?php if (isset($row['image']) && $row['image'] && file_exists($row['image'])): ?>
                    <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Product Image">
                  <?php else: ?>
                    No Image
                  <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars(substr($row['description'] ?: '', 0, 50)) . (strlen($row['description'] ?: '') > 50 ? '...' : ''); ?></td>
                <td>$<?php echo number_format($row['price'], 2); ?></td>
                <td><?php echo htmlspecialchars($row['category_name'] ?: 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($row['stock']); ?></td>
                <td class="actions">
                  <a href="edit-product.php?id=<?php echo $row['product_id']; ?>" class="btn-edit">Edit</a>
                  <a href="?delete_id=<?php echo $row['product_id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="8">No products found</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </main>
  </div>
</body>
</html>