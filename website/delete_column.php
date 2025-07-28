<?php
ob_start();
include 'config.php';

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

$table_name = isset($_POST['table_name']) ? $_POST['table_name'] : '';
$column_name = isset($_POST['column_name']) ? $_POST['column_name'] : '';

$response = [];

if (!preg_match('/^[a-zA-Z0-9_]+$/', $table_name) || !preg_match('/^[a-zA-Z0-9_]+$/', $column_name)) {
    $response['error'] = 'Invalid table or column name';
    error_log("Invalid input in delete_column.php: table=$table_name, column=$column_name");
    echo json_encode($response);
    ob_end_flush();
    exit;
}

try {
    $table_check = $conn->query("SHOW TABLES LIKE '$table_name'");
    if (!$table_check || $table_check->num_rows == 0) {
        $response['error'] = "Table $table_name does not exist";
        error_log("Table does not exist: $table_name");
        echo json_encode($response);
        ob_end_flush();
        exit;
    }
    $table_check->free();

    // Check for constraints (e.g., UNIQUE on categories.name)
    if ($table_name === 'categories' && $column_name === 'name') {
        $conn->query("ALTER TABLE `$table_name` DROP INDEX `name`");
    }
    $sql = "ALTER TABLE `$table_name` DROP COLUMN `$column_name`";
    if ($conn->query($sql)) {
        $response['success'] = true;
    } else {
        $response['error'] = 'Failed to delete column: ' . $conn->error;
        error_log("Delete column error: $sql, " . $conn->error);
    }
} catch (Exception $e) {
    $response['error'] = 'Server error: ' . $e->getMessage();
    error_log("Server error in delete_column.php: " . $e->getMessage());
}

ob_end_clean();
header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
$conn->close();
?>