<?php
session_start();
include 'config.php';

// Check if user is admin or staff
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'staff'])) {
    echo "Access denied.";
    exit;
}

// Set current page for sidebar
$currentPage = $_SESSION['role'] === 'admin' ? 'admin_service_repair.php' : 'staff_service_repair.php';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $repair_id = $conn->real_escape_string($_POST['repair_id']);
    $new_status = $conn->real_escape_string($_POST['new_status']);
    
    $stmt = $conn->prepare("UPDATE repairs SET status = ? WHERE repair_id = ?");
    $stmt->bind_param("si", $new_status, $repair_id);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        $success_message = "Repair status updated successfully!";
    } else {
        $error_message = "Failed to update status or no changes made.";
    }
    
    $stmt->close();
}

// Fetch all repair requests with user and product info
$repairs = [];
$query = "SELECT r.repair_id, r.description, r.status, r.request_date, 
                 u.username, u.email, 
                 p.name as product_name, p.image as product_image
          FROM repairs r
          LEFT JOIN users u ON r.user_id = u.user_id
          LEFT JOIN products p ON r.product_id = p.product_id
          ORDER BY r.request_date DESC";
$result = $conn->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $repairs[] = $row;
    }
    $result->free();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Repairs</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
    <link rel="stylesheet" href="css/admindash.css?v=1.7"/>
    <style>
        .status-select {
            width: 150px;
            display: inline-block;
        }
        .status-form {
            display: inline-block;
        }
        .repair-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            margin-right: 10px;
        }
        .repair-description {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php 
        
        include 'staffsidebar.php';
       
        ?>
        <main class="main-content">
            <h1>Service Repair Management</h1>
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Repair ID</th>
                            <th>Product</th>
                            <th>Description</th>
                            <th>Customer</th>
                            <th>Request Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($repairs)): ?>
                            <tr>
                                <td colspan="7" class="text-center">No repair requests found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($repairs as $repair): ?>
                                <tr>
                                    <td>#<?php echo htmlspecialchars($repair['repair_id']); ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if ($repair['product_image']): ?>
                                                <img src="<?php echo htmlspecialchars($repair['product_image']); ?>" alt="<?php echo htmlspecialchars($repair['product_name']); ?>" class="repair-img">
                                            <?php endif; ?>
                                            <span><?php echo htmlspecialchars($repair['product_name'] ?? 'N/A'); ?></span>
                                        </div>
                                    </td>
                                    <td class="repair-description" title="<?php echo htmlspecialchars($repair['description']); ?>">
                                        <?php echo htmlspecialchars($repair['description']); ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($repair['username']); ?><br>
                                        <small><?php echo htmlspecialchars($repair['email']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($repair['request_date']); ?></td>
                                    <td>
                                        <span class="badge 
                                            <?php echo $repair['status'] == 'pending' ? 'badge-warning' : 
                                                ($repair['status'] == 'in_progress' ? 'badge-info' : 'badge-success'); ?>">
                                            <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $repair['status']))); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" class="status-form">
                                            <input type="hidden" name="repair_id" value="<?php echo $repair['repair_id']; ?>">
                                            <select name="new_status" class="form-control form-control-sm status-select">
                                                <option value="pending" <?php echo $repair['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="in_progress" <?php echo $repair['status'] == 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                                <option value="completed" <?php echo $repair['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                            </select>
                                            <button type="submit" name="update_status" class="btn btn-sm btn-primary mt-1">Update</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>