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
  $database = "user_database";
  
  $conn = new mysqli($servername, $username, $password, $database);
  
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }
  
  // Handle delete action
  if (isset($_GET['delete_id'])) {
      $delete_id = $_GET['delete_id'];
      $sql = "DELETE FROM products WHERE product_id = $delete_id";
      if ($conn->query($sql)) {
          $success_message = "Product deleted successfully!";
      } else {
          $error_message = "Error deleting product: " . $conn->error;
      }
  }
  ?>

  <div class="dashboard-container">
    <?php include 'admindash.php'; ?>

    <main class="main-content">
      <h1>Edit Products</h1>

      <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
      <?php endif; ?>
      
      <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
      <?php endif; ?>

      <table class="admin-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Product Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Supplier</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $sql = "SELECT * FROM products";
          $result = $conn->query($sql);
          
          if ($result->num_rows > 0) {
              while($row = $result->fetch_assoc()) {
                  echo "<tr>
                          <td>".$row['product_id']."</td>
                          <td>".$row['product_name']."</td>
                          <td>$".number_format($row['price'], 2)."</td>
                          <td>".$row['quantity']."</td>
                          <td>".$row['supplier']."</td>
                          <td>
                            <a href='edit-product.php?id=".$row['product_id']."'><button>Edit</button></a>
                            <a href='?delete_id=".$row['product_id']."' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this product?\")'>Delete</a>
                          </td>
                        </tr>";
              }
          } else {
              echo "<tr><td colspan='6'>No products found</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </main>
  </div>
</body>
</html>