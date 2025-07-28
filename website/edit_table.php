<?php
ob_start();
include 'config.php';

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

$current_table_name = isset($_POST['current_table_name']) ? $_POST['current_table_name'] : '';
$new_table_name = isset($_POST['new_table_name']) ? $_POST['new_table_name'] : '';
$column_names = isset($_POST['column_names']) ? $_POST['column_names'] : [];
$row_ids = isset($_POST['row_ids']) ? $_POST['row_ids'] : [];
$row_values = isset($_POST['row_values']) ? $_POST['row_values'] : [];

$response = [];

if (!preg_match('/^[a-zA-Z0-9_]+$/', $current_table_name) || !preg_match('/^[a-zA-Z0-9_]+$/', $new_table_name)) {
    $response['error'] = 'Invalid table name';
    error_log("Invalid table name in edit_table.php: current=$current_table_name, new=$new_table_name");
    echo json_encode($response);
    ob_end_flush();
    exit;
}

try {
    // Verify table exists
    $table_check = $conn->query("SHOW TABLES LIKE '$current_table_name'");
    if (!$table_check || $table_check->num_rows == 0) {
        $response['error'] = "Table $current_table_name does not exist";
        error_log("Table does not exist: $current_table_name");
        echo json_encode($response);
        ob_end_flush();
        exit;
    }
    $table_check->free();

    // Get primary key
    $pk_result = $conn->query("SHOW KEYS FROM `$current_table_name` WHERE Key_name = 'PRIMARY'");
    if ($pk_result && $pk_result->num_rows > 0) {
        $pk_row = $pk_result->fetch_assoc();
        $primary_key = $pk_row['Column_name'];
    } else {
        $response['error'] = "No primary key found for table $current_table_name";
        error_log("No primary key for table: $current_table_name");
        echo json_encode($response);
        ob_end_flush();
        exit;
    }
    $pk_result->free();

    // Get current columns
    $current_columns = [];
    $column_result = $conn->query("SHOW COLUMNS FROM `$current_table_name`");
    if ($column_result) {
        while ($col = $column_result->fetch_assoc()) {
            $current_columns[$col['Field']] = $col['Type'];
        }
        $column_result->free();
    } else {
        $response['error'] = 'Unable to fetch current columns: ' . $conn->error;
        error_log("Column fetch error in edit_table.php: " . $conn->error);
        echo json_encode($response);
        ob_end_flush();
        exit;
    }

    // Rename table if changed
    if ($current_table_name !== $new_table_name) {
        $sql = "RENAME TABLE `$current_table_name` TO `$new_table_name`";
        if (!$conn->query($sql)) {
            $response['error'] = "Failed to rename table: " . $conn->error;
            error_log("Rename table error: $sql, " . $conn->error);
            echo json_encode($response);
            ob_end_flush();
            exit;
        }
    }

    // Rename columns
    foreach ($column_names as $index => $new_name) {
        $old_name = array_keys($current_columns)[$index];
        $type = $current_columns[$old_name];
        if ($old_name !== $new_name && preg_match('/^[a-zA-Z0-9_]+$/', $new_name)) {
            if ($current_table_name === 'categories' && $old_name === 'name') {
                $conn->query("ALTER TABLE `$current_table_name` DROP INDEX `name`");
            }
            $sql = "ALTER TABLE `$new_table_name` CHANGE `$old_name` `$new_name` $type";
            if (!$conn->query($sql)) {
                $response['error'] = "Failed to rename column $old_name to $new_name: " . $conn->error;
                error_log("Rename column error: $sql, " . $conn->error);
                echo json_encode($response);
                ob_end_flush();
                exit;
            }
        }
    }

    // Update row data
    foreach ($row_ids as $index => $pk_value) {
        if (!isset($row_values[$pk_value])) continue;
        $values = $row_values[$pk_value];
        $columns = array_keys($current_columns);
        $set_clause = [];
        foreach ($values as $i => $value) {
            $col = $column_names[$i + 1] ?? $columns[$i + 1];
            if ($col === $primary_key) continue;
            $set_clause[] = "`$col` = ?";
        }
        if (empty($set_clause)) continue;
        $sql = "UPDATE `$new_table_name` SET " . implode(', ', $set_clause) . " WHERE `$primary_key` = ?";
        $stmt = $conn->prepare($sql);
        $types = str_repeat('s', count($values)) . (strpos($current_columns[$primary_key], 'int') !== false ? 'i' : 's');
        $bind_params = array_merge($values, [$pk_value]);
        $stmt->bind_param($types, ...$bind_params);
        if (!$stmt->execute()) {
            $response['error'] = "Failed to update row $pk_value: " . $stmt->error;
            error_log("Update row error: $sql, " . $stmt->error);
            echo json_encode($response);
            ob_end_flush();
            exit;
        }
        $stmt->close();
    }

    $response['success'] = true;
} catch (Exception $e) {
    $response['error'] = 'Server error: ' . $e->getMessage();
    error_log("Server error in edit_table.php: " . $e->getMessage());
}

ob_end_clean();
header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
$conn->close();
?>