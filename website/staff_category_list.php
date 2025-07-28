<?php
session_start();
include 'config.php';

// Check if user is admin or staff
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'staff'])) {
    echo "Access denied.";
    exit;
}

// Set current page for sidebar
$currentPage = 'staff_product_list.php';

// Fetch categories
$categories = $conn->query("SELECT * FROM categories ORDER BY category_id");

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $response = ['status' => 'error', 'message' => 'Invalid request'];

    if ($_POST['action'] === 'add_category') {
        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            $response['message'] = 'Category name is required.';
        } else {
            // Check for duplicate name
            $stmt = $conn->prepare("SELECT COUNT(*) FROM categories WHERE name = ?");
            $stmt->bind_param("s", $name);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();
            if ($count > 0) {
                $response['message'] = 'Category name already exists.';
            } else {
                $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
                $stmt->bind_param("s", $name);
                if ($stmt->execute()) {
                    $response = ['status' => 'success', 'message' => 'Category added successfully.'];
                } else {
                    $response['message'] = 'Error adding category: ' . $stmt->error;
                }
                $stmt->close();
            }
        }
    } elseif ($_POST['action'] === 'edit_category') {
        $category_id = intval($_POST['category_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        if ($category_id <= 0) {
            $response['message'] = 'Invalid category ID.';
        } elseif (empty($name)) {
            $response['message'] = 'Category name is required.';
        } else {
            // Check for duplicate name (excluding current category)
            $stmt = $conn->prepare("SELECT COUNT(*) FROM categories WHERE name = ? AND category_id != ?");
            $stmt->bind_param("si", $name, $category_id);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();
            if ($count > 0) {
                $response['message'] = 'Category name already exists.';
            } else {
                $stmt = $conn->prepare("UPDATE categories SET name = ? WHERE category_id = ?");
                $stmt->bind_param("si", $name, $category_id);
                if ($stmt->execute()) {
                    $response = ['status' => 'success', 'message' => 'Category updated successfully.'];
                } else {
                    $response['message'] = 'Error updating category: ' . $stmt->error;
                }
                $stmt->close();
            }
        }
    } elseif ($_POST['action'] === 'delete_category') {
        $category_id = intval($_POST['category_id'] ?? 0);
        if ($category_id <= 0) {
            $response['message'] = 'Invalid category ID.';
        } else {
            // Check if category is used in products
            $stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
            $stmt->bind_param("i", $category_id);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();
            if ($count > 0) {
                $response['message'] = 'Cannot delete category: It is used by products.';
            } else {
                $stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
                $stmt->bind_param("i", $category_id);
                if ($stmt->execute()) {
                    $response = ['status' => 'success', 'message' => 'Category deleted successfully.'];
                } else {
                    $response['message'] = 'Error deleting category: ' . $stmt->error;
                }
                $stmt->close();
            }
        }
    }
    echo json_encode($response);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff - Category List</title>
    <link rel="stylesheet" href="css/admindash.css?v=1.7">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
    <style>
        .product-image {
            width: 50px;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include 'staffsidebar.php'; ?>
        <main class="main-content">
            <h1>Category List</h1>
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>
            <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addCategoryModal"><i class="fas fa-plus"></i> Add Category</button>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($category = $categories->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $category['category_id']; ?></td>
                            <td><?php echo htmlspecialchars($category['name']); ?></td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="editCategory(<?php echo $category['category_id']; ?>, '<?php echo htmlspecialchars($category['name']); ?>')"><i class="fas fa-pen"></i> Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteCategory(<?php echo $category['category_id']; ?>)"><i class="fas fa-trash"></i> Delete</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </main>
    </div>

    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">Add Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addCategoryForm">
                        <div class="form-group">
                            <label for="category-name">Category Name</label>
                            <input type="text" class="form-control" id="category-name" name="name" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="addCategory()">Add Category</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editCategoryForm">
                        <input type="hidden" id="edit-category-id" name="category_id">
                        <div class="form-group">
                            <label for="edit-category-name">Category Name</label>
                            <input type="text" class="form-control" id="edit-category-name" name="name" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveCategoryChanges()">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function addCategory() {
            const formData = $('#addCategoryForm').serialize() + '&action=add_category';
            $.ajax({
                url: 'staff_category_list.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#addCategoryModal').modal('hide');
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error adding category: ' + error);
                }
            });
        }

        function editCategory(id, name) {
            $('#edit-category-id').val(id);
            $('#edit-category-name').val(name);
            $('#editCategoryModal').modal('show');
        }

        function saveCategoryChanges() {
            const formData = $('#editCategoryForm').serialize() + '&action=edit_category';
            $.ajax({
                url: 'staff_category_list.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#editCategoryModal').modal('hide');
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error updating category: ' + error);
                }
            });
        }

        function deleteCategory(id) {
            if (confirm('Are you sure you want to delete this category?')) {
                $.ajax({
                    url: 'staff_category_list.php',
                    type: 'POST',
                    data: { action: 'delete_category', category_id: id },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            location.reload();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Error deleting category: ' + error);
                    }
                });
            }
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>