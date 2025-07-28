<?php
session_start();
include 'config.php'; // Database connection

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "Access denied. You are not authorized to view this page.";
    exit;
}

// Check if product_id is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: adminproductsedit.php?error=" . urlencode("Invalid product ID."));
    exit;
}

$product_id = intval($_GET['id']);
$success_message = '';
$error_message = '';

// Fetch product details
$sql = "SELECT * FROM products WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) {
    header("Location: adminproductsedit.php?error=" . urlencode("Product not found."));
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = trim($_POST['product-name']);
    $description = trim($_POST['product-description']);
    $price = floatval($_POST['product-price']);
    $category_id = intval($_POST['product-category']);
    $stock = intval($_POST['stock']);
    $image_path = $product['image']; // Retain existing image by default

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
        // Check for duplicate name (excluding current product)
        $sql = "SELECT COUNT(*) FROM products WHERE name = ? AND product_id != ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $product_name, $product_id);
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
                    if (move_uploaded_file($image['tmp_name'], $image_path)) {
                        // Delete old image if it exists and is different
                        if ($product['image'] && $product['image'] !== $image_path && file_exists($product['image'])) {
                            unlink($product['image']);
                        }
                    } else {
                        $error_message = "Failed to upload image.";
                        $image_path = $product['image']; // Revert to old image
                    }
                }
            }

            // Update product if no error
            if (!$error_message) {
                $sql = "UPDATE products SET name = ?, description = ?, price = ?, category_id = ?, stock = ?, image = ? WHERE product_id = ?";
                $stmt = $conn->prepare($sql);
                $image_path = $image_path ?? null; // Ensure null if not set
                $stmt->bind_param("ssdiisi", $product_name, $description, $price, $category_id, $stock, $image_path, $product_id);
                if ($stmt->execute()) {
                    $success_message = "Product updated successfully!";
                    // Refresh product data
                    $sql = "SELECT * FROM products WHERE product_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $product_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $product = $result->fetch_assoc();
                    $stmt->close();
                } else {
                    $error_message = "Error updating product: " . $stmt->error;
                }
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
    <title>Admin - Edit Product</title>
    <link rel="stylesheet" href="css/admindash.css?v=1.7">
</head>
<body>
    <div class="dashboard-container">
        <?php include 'adminsidebar.php'; ?>
        <main class="main-content">
            <h1>Edit Product</h1>

            <?php if ($success_message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>
            <?php if ($error_message): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <form class="product-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . '?id=' . $product_id); ?>" enctype="multipart/form-data">
                <h2>Edit Product</h2>
                <label for="product-name">Product Name</label>
                <input type="text" id="product-name" name="product-name" value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>" required>

                <label for="product-description">Description</label>
                <textarea id="product-description" name="product-description" rows="4"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>

                <label for="product-price">Price</label>
                <input type="number" id="product-price" name="product-price" value="<?php echo htmlspecialchars($product['price'] ?? ''); ?>" step="0.01" min="0" required>

                <label for="product-category">Category</label>
                <select id="product-category" name="product-category" required>
                    <option value="">-- Select Category --</option>
                    <?php while ($category = $categories->fetch_assoc()): ?>
                        <option value="<?php echo $category['category_id']; ?>" <?php echo ($product['category_id'] == $category['category_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <label for="stock">Stock Quantity</label>
                <input type="number" id="stock" name="stock" value="<?php echo htmlspecialchars($product['stock'] ?? ''); ?>" min="0" required>

                <label for="product-image">Product Image (optional, saved as product_name.ext)</label>
                <input type="file" id="product-image" name="product-image" accept="image/jpeg,image/png,image/gif">
                <?php if ($product['image'] && file_exists($product['image'])): ?>
                    <p>Current Image: <img src="<?php echo htmlspecialchars($product['image'] . '?v=' . time()); ?>" alt="Current Image" width="50"></p>
                <?php else: ?>
                    <p>No current image</p>
                <?php endif; ?>

                <div class="btn-group">
                    <button type="submit" class="btn-primary">Update Product</button>
                    <a href="adminproductsedit.php" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>