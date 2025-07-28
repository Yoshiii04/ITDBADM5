<?php
ob_start();
include 'config.php';

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

$table_name = isset($_POST['table_name']) ? $_POST['table_name'] : '';
$pk_value = isset($_POST['id']) ? $_POST['id'] : '';
$primary_key = isset($_POST['primary_key']) ? $_POST['primary_key'] : '';

$response = [];

if (!preg_match('/^[a-zA-Z0-9_]+$/', $table_name) || !preg_match('/^[a-zA-Z0-9_]+$/', $primary_key) || empty($pk_value)) {
    $response['error'] = 'Invalid table name, primary key, or ID';
    error_log("Invalid input in delete_row.php: table=$table_name, pk=$primary_key, id=$pk_value");
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

    $result = $conn->query("SHOW COLUMNS FROM `$table_name` WHERE Field = '$primary_key'");
    if ($result && $row = $result->fetch_assoc()) {
        $type = strtolower($row['Type']);
        $bind_type = strpos($type, 'int') !== false ? 'i' : 's';
    } else {
        $response['error'] = 'Unable to determine primary key type';
        error_log("Primary key type error for $table_name.$primary_key");
        echo json_encode($response);
        ob_end_flush();
        exit;
    }
    $result->free();

    $sql = "DELETE FROM `$table_name` WHERE `$primary_key` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($bind_type, $pk_value);
    if ($stmt->execute()) {
        $response['success'] = true;
    } else {
        $response['error'] = 'Failed to delete row: ' . $conn->error;
        error_log("Delete row error for $table_name: " . $conn->error);
    }
    $stmt->close();
} catch (Exception $e) {
    $response['error'] = 'Server error: ' . $e->getMessage();
    error_log("Server error in delete_row.php: " . $e->getMessage());
}

ob_end_clean();
header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
$conn->close();
?>