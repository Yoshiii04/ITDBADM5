<aside class="sidebar">
  <h2>Admin Panel</h2>
  <ul>
    <li><a href="admindash.php" class="<?php echo ($currentPage == 'admindash.php') ? 'active' : ''; ?>">Dashboard</a></li>
    <li><a href="index.php" class="<?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>">Home</a></li>
    
    <li>
      <a href="#" class="<?php echo (in_array($currentPage, ['adminproductsadd.php', 'adminproductsedit.php'])) ? 'active' : ''; ?>">Product Management</a>
      <ul class="dropdown">
        <li><a href="adminproductsadd.php" class="<?php echo ($currentPage == 'adminproductsadd.php') ? 'active' : ''; ?>">Add Product</a></li>
        <li><a href="adminproductsedit.php" class="<?php echo ($currentPage == 'adminproductsedit.php') ? 'active' : ''; ?>">Edit Product</a></li>
      </ul>
    </li>

    <li><a href="orders.php" class="<?php echo ($currentPage == 'orders.php') ? 'active' : ''; ?>">Orders</a></li>
    <li><a href="tables.php" class="<?php echo ($currentPage == 'tables.php') ? 'active' : ''; ?>">Tables</a></li>
    <li><a href="logout.php" class="<?php echo ($currentPage == 'logout.php') ? 'active' : ''; ?>">Logout</a></li>
  </ul>
</aside>