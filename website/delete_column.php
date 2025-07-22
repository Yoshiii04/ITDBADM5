<?php
include 'config.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['table_name'], $_POST['column_name'])) {
    $table_name = $conn->real_escape_string(preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['table_name']));
    $column_name = $conn->real_escape_string(preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['column_name']));
    $result = $conn->query("SHOW TABLES LIKE '$table_name'");
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'error' => 'Table does not exist']);
        exit;
    }
    $result = $conn->query("SHOW COLUMNS FROM `$table_name` LIKE '$column_name'");
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'error' => 'Column does not exist']);
        exit;
    }
    if ($column_name === 'id') {
        echo json_encode(['success' => false, 'error' => 'Cannot delete the id column']);
        exit;
    }
    $sql = "ALTER TABLE `$table_name` DROP COLUMN `$column_name`";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error deleting column: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>