<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $table_name = $_POST['table_name'];

    // Sanitize table name
    $table_name = $conn->real_escape_string(preg_replace('/[^a-zA-Z0-9_]/', '', $table_name));

    // Get columns
    $result = $conn->query("SHOW COLUMNS FROM `$table_name`");
    if (!$result) {
        header("Location: tables.php?error=Error fetching columns: " . $conn->error);
        exit;
    }

    $columns = [];
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row['Field'];
    }

    // Prepare insert query
    $column_names = [];
    $values = [];
    foreach ($columns as $column) {
        if ($column !== 'id' && !str_contains($column, '_id') && !str_contains($column, '_at')) { // Skip auto-increment and timestamp columns
            if (isset($_POST[$column])) {
                $column_names[] = "`$column`";
                $values[] = "'" . $conn->real_escape_string($_POST[$column]) . "'";
            }
        }
    }

    if (empty($column_names)) {
        header("Location: tables.php?error=No valid columns to insert");
        exit;
    }

    $columns_sql = implode(", ", $column_names);
    $values_sql = implode(", ", $values);
    $sql = "INSERT INTO `$table_name` ($columns_sql) VALUES ($values_sql)";

    if ($conn->query($sql) === TRUE) {
        header("Location: tables.php?success=Row added successfully");
    } else {
        header("Location: tables.php?error=Error adding row: " . $conn->error);
    }
} else {
    header("Location: tables.php?error=Invalid request");
}
?>