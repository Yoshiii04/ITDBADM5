<?php
ob_start();
include 'config.php';

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

$table_name = isset($_POST['table_name']) ? $_POST['table_name'] : '';
$response = [];

if (!preg_match('/^[a-zA-Z0-9_]+$/', $table_name)) {
    $response['error'] = 'Invalid or missing table name';
    error_log("Invalid table name: $table_name");
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

    $pk_result = $conn->query("SHOW KEYS FROM `$table_name` WHERE Key_name = 'PRIMARY'");
    if ($pk_result && $pk_result->num_rows > 0) {
        $pk_row = $pk_result->fetch_assoc();
        $primary_key = $pk_row['Column_name'];
    } else {
        $response['error'] = "No primary key found for table $table_name";
        error_log("No primary key for table: $table_name");
        echo json_encode($response);
        ob_end_flush();
        exit;
    }
    $pk_result->free();

    $columns = [];
    $column_result = $conn->query("SHOW COLUMNS FROM `$table_name`");
    if ($column_result) {
        while ($col = $column_result->fetch_assoc()) {
            $columns[] = $col['Field'] . ':' . $col['Type'];
        }
        $column_result->free();
    } else {
        $response['error'] = 'Unable to fetch columns: ' . $conn->error;
        error_log("Column fetch error for $table_name: " . $conn->error);
        echo json_encode($response);
        ob_end_flush();
        exit;
    }

    $rows = [];
    $row_result = $conn->query("SELECT * FROM `$table_name`");
    if ($row_result) {
        while ($row = $row_result->fetch_assoc()) {
            $row_data = [$row[$primary_key]];
            unset($row[$primary_key]);
            $row_data = array_merge($row_data, array_values($row));
            $rows[] = implode(',', array_map(function($value) {
                return $value === null ? '' : addslashes($value);
            }, $row_data));
        }
        $row_result->free();
    } else {
        $response['error'] = 'Unable to fetch rows: ' . $conn->error;
        error_log("Row fetch error for $table_name: " . $conn->error);
        echo json_encode($response);
        ob_end_flush();
        exit;
    }

    $response = [
        'primary_key' => $primary_key,
        'columns' => $columns,
        'rows' => $rows
    ];

} catch (Exception $e) {
    $response['error'] = 'Server error: ' . $e->getMessage();
    error_log("Server error in get_table_data.php: " . $e->getMessage());
}

ob_end_clean();
header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
$conn->close();
?>