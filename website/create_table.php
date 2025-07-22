<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $table_name = $_POST['table_name'];
    $num_rows = isset($_POST['num_rows']) ? (int)$_POST['num_rows'] : 0;

    // Basic validation
    if (empty($table_name)) {
        header("Location: tables.php?error=Table name is required");
        exit;
    }

    // Sanitize table name
    $table_name = $conn->real_escape_string(preg_replace('/[^a-zA-Z0-9_]/', '', $table_name));

    // Check if table already exists
    $result = $conn->query("SHOW TABLES LIKE '$table_name'");
    if ($result->num_rows > 0) {
        header("Location: tables.php?error=Table already exists");
        exit;
    }

    // Create table with only id column
    $sql = "CREATE TABLE `$table_name` (
        id INT AUTO_INCREMENT PRIMARY KEY
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

    if ($conn->query($sql) === TRUE) {
        // Optionally insert dummy rows
        if ($num_rows > 0) {
            $insert_sql = "INSERT INTO `$table_name` (id) VALUES (NULL)";
            for ($i = 0; $i < $num_rows; $i++) {
                if (!$conn->query($insert_sql)) {
                    header("Location: tables.php?error=Error inserting rows: " . $conn->error);
                    exit;
                }
            }
        }
        header("Location: tables.php?success=Table created successfully");
    } else {
        header("Location: tables.php?error=Error creating table: " . $conn->error);
    }
} else {
    header("Location: tables.php?error=Invalid request");
}
?>
