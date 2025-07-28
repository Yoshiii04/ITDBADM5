<aside class="sidebar">
  <h2>Staff Panel</h2>
  <ul>
    <li><a href="staffdash.php" class="<?php echo ($currentPage == 'staffdash.php') ? 'active' : ''; ?>">Dashboard</a></li>
    <li><a href="index.php" class="<?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>">Home</a></li>
    <li>
      <a href="#" class="<?php echo (in_array($currentPage, ['staffproductsadd.php', 'staff_product_list.php'])) ? 'active' : ''; ?>">Product Management</a>
      <ul class="dropdown">
        <li><a href="staffproductsadd.php" class="<?php echo ($currentPage == 'staffproductsadd.php') ? 'active' : ''; ?>">Add Product</a></li>
        <li><a href="staff_product_list.php" class="<?php echo ($currentPage == 'staff_product_list.php') ? 'active' : ''; ?>">Edit Product</a></li>
      </ul>
    </li>
    <li><a href="orders.php" class="<?php echo ($currentPage == 'orders.php') ? 'active' : ''; ?>">Orders</a></li>
    <li><a href="tables.php" class="<?php echo ($currentPage == 'tables.php') ? 'active' : ''; ?>">Tables</a></li>
    <li><a href="logout.php" class="<?php echo ($currentPage == 'logout.php') ? 'active' : ''; ?>">Logout</a></li>
  </ul>
</aside>