<?php
session_start();
include 'config.php';

// Check if user is admin or staff
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'staff'])) {
    echo "Access denied.";
    exit;
}

// Set current page for sidebar
$currentPage = 'tables.php';

// Get all tables
$tables = $conn->query("SHOW TABLES");
$allowed_tables = ['categories', 'products', 'users'];
$display_tables = [];
while ($row = $tables->fetch_array()) {
    $table_name = $row[0];
    if (in_array($table_name, $allowed_tables)) {
        $display_tables[] = $table_name;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff - Tables</title>
    <link rel="stylesheet" href="css/admindash.css?v=1.7">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
    <style>
        .modal-body table {
            width: 100%;
        }
        .modal-body th, .modal-body td {
            padding: 8px;
            text-align: left;
        }
        .modal-body th input, .modal-body td input {
            width: 100%;
        }
        .modal-body .row-actions {
            display: flex;
            gap: 5px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include 'staffsidebar.php'; ?>
        <main class="main-content">
            <h1>Tables</h1>
            <?php foreach ($display_tables as $table): ?>
                <h2><?php echo htmlspecialchars(ucfirst($table)); ?></h2>
                <?php
                // Get columns
                $columns = $conn->query("SHOW COLUMNS FROM `$table`");
                $column_names = [];
                while ($col = $columns->fetch_assoc()) {
                    if ($table === 'users' && $col['Field'] === 'password') {
                        continue; // Skip password for users table
                    }
                    $column_names[] = $col['Field'];
                }

                // Get data
                $query = $table === 'users' ? 
                    "SELECT user_id, username, email FROM `$table`" : 
                    "SELECT * FROM `$table`";
                $result = $conn->query($query);
                if (!$result) {
                    echo "<div class='alert alert-danger'>Error fetching $table data: " . htmlspecialchars($conn->error) . "</div>";
                    continue;
                }
                ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <?php foreach ($column_names as $col): ?>
                                <th><?php echo htmlspecialchars($col); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <?php foreach ($column_names as $col): ?>
                                    <td><?php echo htmlspecialchars($row[$col] ?? 'admin view only'); ?></td>
                                <?php endforeach; ?>
                        
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php if ($table !== 'users'): ?>
                    <div class="table-actions">
                        <?php if ($table === 'categories'): ?>
                            <a href="staff_category_list.php" class="btn btn-sm btn-success"><i class="fas fa-edit"></i> Edit Categories</a>
                        <?php endif; ?>
                         <?php if ($table === 'products'): ?>
                            <a href="staff_product_list.php" class="btn btn-sm btn-success"><i class="fas fa-edit"></i> Edit Products</a>
                        <?php endif; ?>
                       
                        <hr>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </main>
    </div>

    <!-- Modals -->
    <div class="modal fade" id="editTableModal" tabindex="-1" aria-labelledby="editTableModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTableModalLabel">Edit Table</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table id="editTableContent"></table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveTableChanges()">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addColumnModal" tabindex="-1" aria-labelledby="addColumnModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addColumnModalLabel">Add Column</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="newColumnName">Column Name</label>
                        <input type="text" class="form-control" id="newColumnName">
                    </div>
                    <div class="form-group">
                        <label for="newColumnType">Data Type</label>
                        <select class="form-control" id="newColumnType">
                            <option value="VARCHAR(255)">VARCHAR(255)</option>
                            <option value="INT">INT</option>
                            <option value="TEXT">TEXT</option>
                            <option value="DECIMAL(10,2)">DECIMAL(10,2)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveNewColumn()">Add Column</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addRowModal" tabindex="-1" aria-labelledby="addRowModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRowModalLabel">Add Row</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addRowForm"></form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveNewRow()">Add Row</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        let currentTable = '';
        let currentRowId = '';

        function editTable(table) {
            currentTable = table;
            $.ajax({
                url: 'edit_table.php',
                type: 'POST',
                data: { table: table, action: 'get_columns' },
                success: function(response) {
                    $('#editTableContent').html(response);
                    $('#editTableModal').modal('show');
                },
                error: function(xhr, status, error) {
                    alert('Error fetching columns: ' + error);
                }
            });
        }

        function addColumn(table) {
            currentTable = table;
            $('#addColumnModal').modal('show');
        }

        function saveNewColumn() {
            const columnName = $('#newColumnName').val();
            const columnType = $('#newColumnType').val();
            $.ajax({
                url: 'edit_table.php',
                type: 'POST',
                data: { table: currentTable, action: 'add_column', column_name: columnName, column_type: columnType },
                success: function(response) {
                    if (response === 'success') {
                        location.reload();
                    } else {
                        alert('Error adding column: ' + response);
                    }
                }
            });
        }

        function addRow(table) {
            currentTable = table;
            $.ajax({
                url: 'edit_table.php',
                type: 'POST',
                data: { table: table, action: 'get_columns_for_row' },
                success: function(response) {
                    $('#addRowForm').html(response);
                    $('#addRowModal').modal('show');
                }
            });
        }

        function saveNewRow() {
            const formData = $('#addRowForm').serialize() + '&table=' + currentTable + '&action=add_row';
            $.ajax({
                url: 'edit_table.php',
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response === 'success') {
                        location.reload();
                    } else {
                        alert('Error adding row: ' + response);
                    }
                }
            });
        }

        function editRow(table, id) {
            currentTable = table;
            currentRowId = id;
            $.ajax({
                url: 'edit_table.php',
                type: 'POST',
                data: { table: table, id: id, action: 'get_row' },
                success: function(response) {
                    $('#editTableContent').html(response);
                    $('#editTableModal').modal('show');
                }
            });
        }

        function saveTableChanges() {
            const formData = $('#editTableContent form').serialize() + '&table=' + currentTable + '&id=' + currentRowId + '&action=update_row';
            $.ajax({
                url: 'edit_table.php',
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response === 'success') {
                        location.reload();
                    } else {
                        alert('Error updating table: ' + response);
                    }
                }
            });
        }

        function deleteRow(table, id) {
            if (confirm('Are you sure you want to delete this row?')) {
                $.ajax({
                    url: 'delete_row.php',
                    type: 'POST',
                    data: { table: table, id: id },
                    success: function(response) {
                        if (response === 'success') {
                            location.reload();
                        } else {
                            alert('Error deleting row: ' + response);
                        }
                    }
                });
            }
        }

        function deleteTable(table) {
            if (confirm('Are you sure you want to delete this table?')) {
                $.ajax({
                    url: 'edit_table.php',
                    type: 'POST',
                    data: { table: table, action: 'delete_table' },
                    success: function(response) {
                        if (response === 'success') {
                            location.reload();
                        } else {
                            alert('Error deleting table: ' + response);
                        }
                    }
                });
            }
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>