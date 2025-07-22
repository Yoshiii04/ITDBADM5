<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['table_name'], $_POST['column_name'])) {
    $table_name = $conn->real_escape_string(preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['table_name']));
    $column_name = $conn->real_escape_string(preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['column_name']));
    $values = [];
    $result = $conn->query("SELECT `$column_name` FROM `$table_name`");
    while ($row = $result->fetch_assoc()) {
        $values[] = $row[$column_name];
    }
    echo json_encode(['values' => $values]);
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>