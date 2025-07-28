<?php
session_start();
include 'config.php'; // Database connection

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "Access denied. You are not authorized to view this page.";
    exit;
}

$currentPage = basename($_SERVER['PHP_SELF']);
$success_message = '';
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = trim($_POST['product-name']);
    $description = trim($_POST['product-description']);
    $price = floatval($_POST['product-price']);
    $category_id = intval($_POST['product-category']);
    $stock = intval($_POST['product-stock']);
    $image_path = null;

    // Validate inputs
    if (empty($product_name)) {
        $error_message = "Product name is required.";
    } elseif ($price < 0) {
        $error_message = "Price cannot be negative.";
    } elseif ($category_id <= 0) {
        $error_message = "Please select a valid category.";
    } elseif ($stock < 0) {
        $error_message = "Stock quantity cannot be negative.";
    } else {
        // Check for duplicate product name
        $sql = "SELECT COUNT(*) FROM products WHERE name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $product_name);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        if ($count > 0) {
            $error_message = "Product name already exists. Please choose a unique name.";
        } else {
            // Handle image upload
            if (isset($_FILES['product-image']) && $_FILES['product-image']['error'] === UPLOAD_ERR_OK) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                $max_size = 5 * 1024 * 1024; // 5MB
                $image = $_FILES['product-image'];

                if (!in_array($image['type'], $allowed_types)) {
                    $error_message = "Invalid image format. Only JPEG, PNG, or GIF allowed.";
                } elseif ($image['size'] > $max_size) {
                    $error_message = "Image size exceeds 5MB.";
                } elseif (!is_dir('img') || !is_writable('img')) {
                    $error_message = "Image directory is not writable.";
                } else {
                    // Generate safe filename from product name
                    $safe_name = preg_replace('/[^A-Za-z0-9_-]/', '_', strtolower($product_name));
                    $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
                    $image_path = 'img/' . $safe_name . '.' . $ext;
                    // Append timestamp if file exists
                    if (file_exists($image_path)) {
                        $image_path = 'img/' . $safe_name . '_' . time() . '.' . $ext;
                    }
                    if (!move_uploaded_file($image['tmp_name'], $image_path)) {
                        $error_message = "Failed to upload image.";
                    }
                }
            }

            // Proceed with insertion if no error
            if (!$error_message) {
                $sql = "INSERT INTO products (name, description, price, category_id, stock, image) 
                        VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssdiis", $product_name, $description, $price, $category_id, $stock, $image_path);

                if ($stmt->execute()) {
                    $success_message = "Product added successfully!";
                } else {
                    $error_message = "Error: " . $stmt->error;
                }
                $stmt->close();
            }
        }
    }
}

// Get categories
$categories = $conn->query("SELECT * FROM categories ORDER BY name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Add Product</title>
  <link rel="stylesheet" href="css/admindash.css?v=1.7">
</head>
<body>
  <div class="dashboard-container">
    <?php include 'adminsidebar.php'; ?>
    <main class="main-content">
      <h1>Add Product</h1>

      <?php if ($success_message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
      <?php endif; ?>
      <?php if ($error_message): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
      <?php endif; ?>

      <form class="product-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
        <h2>Add New Product</h2>
        <label for="product-name">Product Name</label>
        <input type="text" id="product-name" name="product-name" value="<?php echo isset($_POST['product-name']) ? htmlspecialchars($_POST['product-name']) : ''; ?>" placeholder="e.g. RGB Gaming Mouse" required>

        <label for="product-description">Description</label>
        <textarea id="product-description" name="product-description" rows="4" placeholder="Enter product description"><?php echo isset($_POST['product-description']) ? htmlspecialchars($_POST['product-description']) : ''; ?></textarea>

        <label for="product-price">Price</label>
        <input type="number" id="product-price" name="product-price" value="<?php echo isset($_POST['product-price']) ? htmlspecialchars($_POST['product-price']) : ''; ?>" placeholder="e.g. 999.99" step="0.01" min="0" required>

        <label for="product-category">Category</label>
        <select id="product-category" name="product-category" required>
          <option value="">-- Select Category --</option>
          <?php while ($category = $categories->fetch_assoc()): ?>
            <option value="<?php echo $category['category_id']; ?>" <?php echo isset($_POST['product-category']) && $_POST['product-category'] == $category['category_id'] ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($category['name']); ?>
            </option>
          <?php endwhile; ?>
        </select>

        <label for="product-stock">Stock Quantity</label>
        <input type="number" id="product-stock" name="product-stock" value="<?php echo isset($_POST['product-stock']) ? htmlspecialchars($_POST['product-stock']) : ''; ?>" placeholder="e.g. 100" min="0" required>

        <label for="product-image">Product Image (optional, saved as product_name.ext)</label>
        <input type="file" id="product-image" name="product-image" accept="image/jpeg,image/png,image/gif">

        <div class="btn-group">
          <button type="submit" class="btn-primary">Save Product</button>
          <button type="reset" class="btn-secondary">Reset Form</button>
        </div>
      </form>
    </main>
  </div>
</body>
</html>