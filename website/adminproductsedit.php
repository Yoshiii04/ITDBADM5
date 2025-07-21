<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Products</title>
  <link rel="stylesheet" href="css/admindash.css">
  <link rel="stylesheet" href="css/adminproductsedit.css">
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
  
  // Handle delete action
  if (isset($_GET['delete_id'])) {
      $delete_id = intval($_GET['delete_id']);
      $sql = "DELETE FROM products WHERE product_id = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("i", $delete_id);
      
      if ($stmt->execute()) {
          $success_message = "Product deleted successfully!";
      } else {
          $error_message = "Error deleting product: " . $stmt->error;
      }
      $stmt->close();
  }
  ?>

  <div class="dashboard-container">
    <?php include 'admindash.php'; ?>

    <main class="main-content">
      <h1>Manage Products</h1>

      <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
      <?php endif; ?>
      
      <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
      <?php endif; ?>

      <div class="table-header">
        <a href="adminproductsadd.php" class="btn-add">Add New Product</a>
      </div>

      <table class="admin-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Product Name</th>
            <th>Description</th>
            <th>Price</th>
            <th>Category</th>
            <th>Stock</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $sql = "SELECT p.*, c.name as category_name 
                  FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.category_id
                  ORDER BY p.product_id";
          $result = $conn->query($sql);
          
          if ($result->num_rows > 0) {
              while($row = $result->fetch_assoc()) {
                  echo "<tr>
                          <td>".$row['product_id']."</td>
                          <td>".htmlspecialchars($row['name'])."</td>
                          <td>".htmlspecialchars(substr($row['description'], 0, 50))."...</td>
                          <td>$".number_format($row['price'], 2)."</td>
                          <td>".htmlspecialchars($row['category_name'])."</td>
                          <td>".$row['stock']."</td>
                          <td class='actions'>
                            <a href='edit-product.php?id=".$row['product_id']."' class='btn-edit'>Edit</a>
                            <a href='?delete_id=".$row['product_id']."' class='btn-delete' onclick='return confirm(\"Are you sure you want to delete this product?\")'>Delete</a>
                          </td>
                        </tr>";
              }
          } else {
              echo "<tr><td colspan='7'>No products found</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </main>
  </div>
</body>
</html>