<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $table_name = $_POST['table_name'];
    $column_name = $_POST['column_name'];
    $data_type = $_POST['data_type'];

    // Basic validation
    if (empty($table_name) || empty($column_name) || empty($data_type)) {
        header("Location: tables.php?error=Invalid input");
        exit;
    }

    // Sanitize inputs
    $table_name = $conn->real_escape_string(preg_replace('/[^a-zA-Z0-9_]/', '', $table_name));
    $column_name = $conn->real_escape_string(preg_replace('/[^a-zA-Z0-9_]/', '', $column_name));
    $allowed_data_types = ['VARCHAR(255)', 'INT', 'DECIMAL(10,2)', 'TEXT', 'DATETIME'];
    if (!in_array($data_type, $allowed_data_types)) {
        header("Location: tables.php?error=Invalid data type");
        exit;
    }

    // Check if column already exists
    $result = $conn->query("SHOW COLUMNS FROM `$table_name` LIKE '$column_name'");
    if ($result->num_rows > 0) {
        header("Location: tables.php?error=Column already exists");
        exit;
    }

    // Add column
    $sql = "ALTER TABLE `$table_name` ADD `$column_name` $data_type";
    if ($conn->query($sql) === TRUE) {
        header("Location: tables.php?success=Column added successfully");
    } else {
        header("Location: tables.php?error=Error adding column: " . $conn->error);
    }
} else {
    header("Location: tables.php?error=Invalid request");
}
?>