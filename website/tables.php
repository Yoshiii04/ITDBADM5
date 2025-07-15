<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin - Manage Tables</title>

  <!-- Bootstrap -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
  <link rel="stylesheet" href="css/admindash.css"/>
</head>
<body>
  <?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
  <div class="dashboard-container">
    <?php include 'adminsidebar.php'; ?>

    <div class="main-content">
      <h1>Database Tables</h1>

      <!-- Add Table Button -->
      <div class="mb-3 text-right">
        <button class="btn btn-success" data-toggle="modal" data-target="#addTableModal">
          <i class="fas fa-plus-circle"></i> Add Table
        </button>
      </div>

      <!-- Simulated Tables -->
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
          <tr>
            <td>products</td>
            <td>8</td>
            <td>120</td>
            <td>
              <button class="btn btn-sm btn-primary"><i class="fas fa-pen"></i> Edit</button>
              <button class="btn btn-sm btn-info"><i class="fas fa-columns"></i> Add Column</button>
              <button class="btn btn-sm btn-warning"><i class="fas fa-plus"></i> Add Row</button>
              <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Delete</button>
            </td>
          </tr>
          <tr>
            <td>users</td>
            <td>5</td>
            <td>300</td>
            <td>
              <button class="btn btn-sm btn-primary"><i class="fas fa-pen"></i> Edit</button>
              <button class="btn btn-sm btn-info"><i class="fas fa-columns"></i> Add Column</button>
              <button class="btn btn-sm btn-warning"><i class="fas fa-plus"></i> Add Row</button>
              <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Add Table Modal -->
  <div class="modal fade" id="addTableModal" tabindex="-1" role="dialog" aria-labelledby="addTableModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <form class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addTableModalLabel">Create New Table</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <label>Table Name</label>
          <input type="text" class="form-control" placeholder="e.g. orders" required>

          <label class="mt-3">Number of Columns</label>
          <input type="number" class="form-control" placeholder="e.g. 5" min="1" required>

          <label class="mt-3">Number of Rows</label>
          <input type="number" class="form-control" placeholder="e.g. 5" min="1" required>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Create Table</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
