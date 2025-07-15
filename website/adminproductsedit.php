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
   <?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
  <div class="dashboard-container">
    <?php include 'admindash.php'; ?>

    <main class="main-content">
      <h1>Edit Products</h1>

      <table class="admin-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Product Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td>RGB Gaming Mouse</td>
            <td>Mice</td>
            <td>$29.99</td>
            <td>25</td>
            <td>
              <a href="edit-products.html"><button>Edit</button></a>
              <button class="delete-btn">Delete</button>
            </td>
          </tr>
          <!-- Repeat rows dynamically with backend integration -->
        </tbody>
      </table>
    </main>
  </div>
</body>
</html>
