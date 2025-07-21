<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="css/admindash.css">
</head>
<body>
  <?php $currentPage = basename($_SERVER['PHP_SELF']); ?>

  <?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "Access denied. You are not authorized to view this page.";
    exit;
}
?> <!--  to check if the user is an admin -->


  <div class="dashboard-container">
   

    <?php include 'adminsidebar.php'; ?>

    
  </div>
</body>
</html>