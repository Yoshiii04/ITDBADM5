<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin - Manage Tables</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
  <link rel="stylesheet" href="css/admindash.css"/>
  <style>
    .table-editable th, .table-editable td {
      padding: 8px;
      vertical-align: middle;
    }
    .table-editable th {
      background-color: #343a40; /* Match thead-dark */
      color: white;
      text-align: left;
    }
    /* Columns table specific widths */
    #edit-columns th:nth-child(1), #edit-columns td:nth-child(1) {
      width: 50%; /* Wider for column name input */
    }
    #edit-columns th:nth-child(2), #edit-columns td:nth-child(2) {
      width: 35%; /* Medium for data type dropdown */
    }
    #edit-columns th:nth-child(3), #edit-columns td:nth-child(3) {
      width: 15%; /* Narrow for action button */
      text-align: center;
    }
    /* Rows table: Dynamic widths based on columns */
    #edit-rows-table th, #edit-rows-table td {
      width: auto;
      min-width: 100px; /* Minimum width for readability */
    }
    #edit-rows-table th:first-child, #edit-rows-table td:first-child {
      width: 10%; /* Narrower for ID */
    }
    #edit-rows-table th:last-child, #edit-rows-table td:last-child {
      width: 10%; /* Narrow for action button */
      text-align: center;
    }
    .table-editable input, .table-editable select {
      width: 100%;
      box-sizing: border-box;
      margin: 0;
    }
    .remove-btn {
      cursor: pointer;
      color: red;
      font-size: 16px;
    }
    .btn-sm {
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <?php 
  $currentPage = basename($_SERVER['PHP_SELF']);

    include 'config.php'; 

  ?>
<!--  db connection ^^^ -->

  <div class="dashboard-container">
    <?php include 'adminsidebar.php'; ?>

    <div class="main-content">
      <h1>Database Tables</h1>

      <!-- Success/Error Messages -->
      <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
      <?php endif; ?>
      <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
      <?php endif; ?>

      <!-- Add Table Button -->
      <div class="mb-3 text-right">
        <button class="btn btn-success" data-toggle="modal" data-target="#addTableModal">
          <i class="fas fa-plus-circle"></i> Add Table
        </button>
      </div>

      <!-- Dynamic Table List -->
      <table class="table table-bordered table-striped">
        <thead class="thead-dark">
          <tr>
            <th>Table Name</th>
            <th>Columns</th>
            <th>Rows</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // Fetch all tables in the database using MySQLi
          $result = $conn->query("SHOW TABLES");
          if ($result) {
              while ($row = $result->fetch_array()) {
                  $table = $row[0]; // MySQLi fetch_array returns the table name in the first column

                  // Get column count
                  $column_result = $conn->query("SHOW COLUMNS FROM `$table`");
                  $column_count = $column_result->num_rows;

                  // Get row count
                  $row_result = $conn->query("SELECT COUNT(*) as row_count FROM `$table`");
                  $row_count = $row_result->fetch_assoc()['row_count'];

                  echo "<tr>";
                  echo "<td>$table</td>";
                  echo "<td>$column_count</td>";
                  echo "<td>$row_count</td>";
                  echo "<td>
                          <button class='btn btn-sm btn-primary' onclick='editTable(\"$table\")'><i class='fas fa-pen'></i> Edit</button>
                          <button class='btn btn-sm btn-info' onclick='addColumn(\"$table\")'><i class='fas fa-columns'></i> Add Column</button>
                          <button class='btn btn-sm btn-warning' onclick='addRow(\"$table\")'><i class='fas fa-plus'></i> Add Row</button>
                          <button class='btn btn-sm btn-danger' onclick='deleteTable(\"$table\")'><i class='fas fa-trash'></i> Delete</button>
                        </td>";
                  echo "</tr>";
              }
              $result->free(); // Free the result set
          } else {
              echo "<tr><td colspan='4'>Error fetching tables: " . $conn->error . "</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>

  
  <!-- Add Table Modal -->
  <div class="modal fade" id="addTableModal" tabindex="-1" role="dialog" aria-labelledby="addTableModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <form class="modal-content" method="POST" action="create_table.php">
        <div class="modal-header">
          <h5 class="modal-title" id="addTableModalLabel">Create New Table</h5>
          <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Table Name</label>
            <input type="text" name="table_name" class="form-control" placeholder="e.g. orders" required>
          </div>
          <div class="form-group">
            <label>Number of Initial Rows (optional)</label>
            <input type="number" name="num_rows" class="form-control" min="0" value="0" placeholder="e.g. 0">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Create Table</button>
        </div>
      </form>
    </div>
  </div>

        <!-- error in modal action column and row data delete button, edit table modal works but u have to edit the row first before shitfing to INT or VARCHAR-->
    <!-- Edit Table Modal -->
  <div class="modal fade" id="editTableModal" tabindex="-1" role="dialog" aria-labelledby="editTableModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <form class="modal-content" id="editTableForm" method="POST" action="edit_table.php">
        <div class="modal-header">
          <h5 class="modal-title" id="editTableModalLabel">Edit Table</h5>
          <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Current Table Name</label>
            <input type="text" name="current_table_name" class="form-control" readonly>
          </div>
          <div class="form-group">
            <label>New Table Name</label>
            <input type="text" name="new_table_name" class="form-control" placeholder="Enter new table name" required>
          </div>
          <div class="form-group">
            <label>Columns</label>
            <table class="table table-bordered table-editable">
              <thead>
                <tr>
                  <th>Column Name</th>
                  <th>Data Type</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="edit-columns"></tbody>
            </table>
           
          </div>
          <div class="form-group">
            <label>Row Data</label>
            <table class="table table-bordered table-editable" id="edit-rows-table">
              <thead id="edit-rows-headers"></thead>
              <tbody id="edit-rows"></tbody>
            </table>
           
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger" onclick="deleteTableFromModal()">Delete Table</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Add Column Modal -->
  <div class="modal fade" id="addColumnModal" tabindex="-1" role="dialog" aria-labelledby="addColumnModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <form class="modal-content" method="POST" action="add_column.php">
        <div class="modal-header">
          <h5 class="modal-title" id="addColumnModalLabel">Add Column</h5>
          <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Table Name</label>
            <input type="text" name="table_name" class="form-control" readonly>
          </div>
          <div class="form-group">
            <label>Column Name</label>
            <input type="text" name="column_name" class="form-control" placeholder="e.g. new_column" required>
          </div>
          <div class="form-group">
            <label>Data Type</label>
            <select name="data_type" class="form-control" required>
              <option value="VARCHAR(255)">VARCHAR(255)</option>
              <option value="INT">INT</option>
              <option value="DECIMAL(10,2)">DECIMAL(10,2)</option>
              <option value="TEXT">TEXT</option>
              <option value="DATETIME">DATETIME</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-info">Add Column</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Add Row Modal -->
  <div class="modal fade" id="addRowModal" tabindex="-1" role="dialog" aria-labelledby="addRowModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <form class="modal-content" method="POST" action="add_row.php">
        <div class="modal-header">
          <h5 class="modal-title" id="addRowModalLabel">Add Row</h5>
          <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Table Name</label>
            <input type="text" name="table_name" class="form-control" readonly>
          </div>
          <div id="row-inputs"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-warning">Add Row</button>
        </div>
      </form>
    </div>
  </div>

   <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Display and clear success/error messages
    <?php if (isset($_GET['success']) || isset($_GET['error'])): ?>
      $(document).ready(function() {
        const message = <?php echo isset($_GET['success']) ? json_encode(htmlspecialchars($_GET['success'])) : json_encode(htmlspecialchars($_GET['error'])); ?>;
        const alertClass = <?php echo isset($_GET['success']) ? '"alert-success"' : '"alert-danger"'; ?>;
        const alertHtml = `<div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                            ${message}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>`;
        $('body').append(alertHtml);
        // Clear URL query parameters
        history.replaceState(null, '', 'tables.php');
        // Auto-dismiss after 5 seconds
        setTimeout(() => $('.alert').alert('close'), 5000);
      });
    <?php endif; ?>

    function editTable(tableName) {
      $('#editTableModal').find('input[name="current_table_name"]').val(tableName);
      $('#editTableModal').find('input[name="new_table_name"]').val(tableName);
      $.ajax({
        url: 'get_table_data.php',
        method: 'POST',
        data: { table_name: tableName },
        success: function(response) {
          try {
            const data = JSON.parse(response);
            if (data.error) {
              console.error('Error from get_table_data.php:', data.error);
              $('#edit-columns').html('<tr><td colspan="3">Error: ' + data.error + '</td></tr>');
              $('#edit-rows').html('<tr><td>Error: ' + data.error + '</td></tr>');
              $('#editTableModal').modal('show');
              return;
            }
            // Populate columns
            $('#edit-columns').html('');
            data.columns.forEach(col => {
              const [name, type] = col.split(':');
              const row = `
                <tr>
                  <td><input type="text" name="column_names[]" class="form-control" value="${name}" required></td>
                  <td>
                    <select name="column_types[]" class="form-control" required>
                      <option value="VARCHAR(255)" ${type === 'VARCHAR(255)' ? 'selected' : ''}>VARCHAR(255)</option>
                      <option value="INT" ${type === 'INT' ? 'selected' : ''}>INT</option>
                      <option value="DECIMAL(10,2)" ${type === 'DECIMAL(10,2)' ? 'selected' : ''}>DECIMAL(10,2)</option>
                      <option value="TEXT" ${type === 'TEXT' ? 'selected' : ''}>TEXT</option>
                      <option value="DATETIME" ${type === 'DATETIME' ? 'selected' : ''}>DATETIME</option>
                    </select>
                  </td>
                  <td><i class="fas fa-trash remove-btn" onclick="deleteColumn('${tableName}', '${name}', this)"></i></td>
                </tr>`;
              $('#edit-columns').append(row);
            });
            // Populate row headers
            $('#edit-rows-headers').html(`<tr><th>ID</th>${data.columns.map(col => `<th>${col.split(':')[0]}</th>`).join('')}<th>Action</th></tr>`);
            // Populate rows
            $('#edit-rows').html('');
            data.rows.forEach(row => {
              const values = row.split(',');
              const id = values.shift();
              const rowHtml = `
                <tr>
                  <td><input type="text" name="row_ids[]" class="form-control" value="${id}" readonly></td>
                  ${values.map(val => `<td><input type="text" name="row_values[${id}][]" class="form-control" value="${val}"></td>`).join('')}
                  <td><i class="fas fa-trash remove-btn" onclick="deleteRow('${tableName}', '${id}', this)"></i></td>
                </tr>`;
              $('#edit-rows').append(rowHtml);
            });
            $('#editTableModal').modal('show');
          } catch (e) {
            console.error('Error parsing response:', e, response);
            $('#edit-columns').html('<tr><td colspan="3">Error loading columns</td></tr>');
            $('#edit-rows').html('<tr><td>Error loading rows</td></tr>');
            $('#editTableModal').modal('show');
          }
        },
        error: function(xhr, status, error) {
          console.error('AJAX error:', status, error, xhr.responseText);
          $('#edit-columns').html('<tr><td colspan="3">Error loading columns</td></tr>');
          $('#edit-rows').html('<tr><td>Error loading rows</td></tr>');
          $('#editTableModal').modal('show');
        }
      });
    }

  function deleteColumn(tableName, columnName, element) {
  if (confirm(`Are you sure you want to delete the column '${columnName}'? This action cannot be undone.`)) {
    $.ajax({
      url: 'delete_column.php',
      method: 'POST',
      data: { table_name: tableName, column_name: columnName },
      success: function(response) {
        try {
          const result = JSON.parse(response);
          if (result.success) {
            $(element).closest('tr').remove();
            alert('Column deleted successfully');
            editTable(tableName); // Refresh modal
          } else {
            console.error('Error from delete_column.php:', result.error);
            alert('Error deleting column: ' + result.error);
          }
        } catch (e) {
          console.error('Error parsing delete_column response:', e, response);
          alert('Error processing response: ' + response);
        }
      },
      error: function(xhr, status, error) {
        console.error('AJAX error in deleteColumn:', status, error, xhr.responseText);
        alert('Error deleting column: AJAX request failed');
      }
    });
  }
}

  function deleteRow(tableName, id, element) {
  if (confirm(`Are you sure you want to delete the row with ID ${id}? This action cannot be undone.`)) {
        $.ajax({
          url: 'delete_row.php',
          method: 'POST',
          data: { table_name: tableName, id: id },
          success: function(response) {
            try {
              const result = JSON.parse(response);
              if (result.success) {
                $(element).closest('tr').remove();
                alert('Row deleted successfully');
                editTable(tableName); // Refresh modal
              } else {
                console.error('Error from delete_row.php:', result.error);
                alert('Error deleting row: ' + result.error);
              }
            } catch (e) {
              console.error('Error parsing delete_row response:', e, response);
              alert('Error processing response: ' + response);
            }
          },
          error: function(xhr, status, error) {
            console.error('AJAX error in deleteRow:', status, error, xhr.responseText);
            alert('Error deleting row: AJAX request failed');
          }
        });
      }
    }

    function addColumnInput(name = '', type = 'VARCHAR(255)') {
      const entry = document.createElement('tr');
      entry.className = 'column-entry';
      entry.innerHTML = `
        <td><input type="text" name="column_names[]" class="form-control" value="${name}" placeholder="e.g. column_name" required></td>
        <td>
          <select name="column_types[]" class="form-control" required>
            <option value="VARCHAR(255)" ${type === 'VARCHAR(255)' ? 'selected' : ''}>VARCHAR(255)</option>
            <option value="INT" ${type === 'INT' ? 'selected' : ''}>INT</option>
            <option value="DECIMAL(10,2)" ${type === 'DECIMAL(10,2)' ? 'selected' : ''}>DECIMAL(10,2)</option>
            <option value="TEXT" ${type === 'TEXT' ? 'selected' : ''}>TEXT</option>
            <option value="DATETIME" ${type === 'DATETIME' ? 'selected' : ''}>DATETIME</option>
          </select>
        </td>
        <td><i class="fas fa-trash remove-btn" onclick="this.parentElement.parentElement.remove()"></i></td>
      `;
      document.getElementById('edit-columns').appendChild(entry);
    }

    function addRowInput(id = '', values = [], columnNames = []) {
      const entry = document.createElement('tr');
      entry.className = 'row-entry';
      let html = `<td><input type="text" name="row_ids[]" class="form-control" value="${id}" ${id ? 'readonly' : ''} placeholder="New ID" required></td>`;
      values.forEach((val, index) => {
        html += `<td><input type="text" name="row_values[${id || 'new_' + Math.random().toString(36).substr(2, 9)}][]" class="form-control" value="${val}" placeholder="Value for ${columnNames[index] || 'column'}" ${id ? '' : 'required'}></td>`;
      });
      html += '<td><i class="fas fa-trash remove-btn" onclick="this.parentElement.parentElement.remove()"></i></td>';
      entry.innerHTML = html;
      document.getElementById('edit-rows').appendChild(entry);
    }

    function validateColumnTypeChange(columnName, newType, callback) {
    $.ajax({
        url: 'check_column_data.php',
        method: 'POST',
        data: { table_name: $('#editTableModal input[name="current_table_name"]').val(), column_name: columnName },
        success: function(response) {
            const data = JSON.parse(response);
            let valid = true;
            if (newType === 'INT') {
                data.values.forEach(val => {
                    if (val !== null && (!isNumeric(val) || Math.floor(parseFloat(val)) != parseFloat(val))) {
                        valid = false;
                    }
                });
            } else if (newType === 'DECIMAL(10,2)') {
                data.values.forEach(val => {
                    if (val !== null && !isNumeric(val)) {
                        valid = false;
                    }
                });
            } else if (newType === 'DATETIME') {
                data.values.forEach(val => {
                    if (val !== null && !isValidDate(val)) {
                        valid = false;
                    }
                });
            }
            if (!valid) {
                alert(`Warning: Column ${columnName} contains data incompatible with ${newType}. Please update row data first.`);
            }
            callback(valid);
        }
    });
}

    function isNumeric(str) {
        return !isNaN(parseFloat(str)) && isFinite(str);
    }

    function isValidDate(str) {
        return !isNaN(Date.parse(str));
    }

    // Attach to select change
    $('#edit-columns').on('change', 'select[name="column_types[]"]', function() {
        const columnName = $(this).closest('tr').find('input[name="column_names[]"]').val();
        const newType = $(this).val();
        validateColumnTypeChange(columnName, newType, function(valid) {
            if (!valid) {
                $(this).val('VARCHAR(255)'); // Revert to safe type
            }
        });
    });

    function addColumn(tableName) {
      $('#addColumnModal').find('input[name="table_name"]').val(tableName);
      $('#addColumnModal').modal('show');
    }

    function addRow(tableName) {
      $('#addRowModal').find('input[name="table_name"]').val(tableName);
      $.ajax({
        url: 'get_columns.php',
        method: 'POST',
        data: { table_name: tableName },
        success: function(response) {
          $('#row-inputs').html(response);
          $('#addRowModal').modal('show');
        },
        error: function() {
          $('#row-inputs').html('<p class="text-danger">Error loading columns.</p>');
          $('#addRowModal').modal('show');
        }
      });
    }


    function deleteTable(tableName) {
      if (confirm('Are you sure you want to delete the table ' + tableName + '?')) {
        window.location.href = 'delete_table.php?table=' + encodeURIComponent(tableName);
      }
    }

    function deleteTableFromModal() {
      const tableName = $('#editTableModal').find('input[name="current_table_name"]').val();
      if (confirm('Are you sure you want to delete the table ' + tableName + '? This action cannot be undone.')) {
        window.location.href = 'delete_table.php?table=' + encodeURIComponent(tableName);
        $('#editTableModal').modal('hide');
      }
    }

     
  </script>
</body>
</html>