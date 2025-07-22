<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['table_name'])) {
    $table_name = $conn->real_escape_string(preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['table_name']));

    // Get columns
    $columns = [];
    $result = $conn->query("SHOW COLUMNS FROM `$table_name`");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            if ($row['Field'] !== 'id') {
                $columns[] = $row['Field'] . ':' . ($row['Type'] === 'int(11)' ? 'INT' : $row['Type']);
            }
        }
        $result->free();
    } else {
        echo json_encode(['error' => 'Error fetching columns: ' . $conn->error]);
        exit;
    }

    // Get rows (limit to first 10)
    $rows = [];
    $result = $conn->query("SELECT * FROM `$table_name` LIMIT 10");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $row_values = [$row['id']];
            foreach ($columns as $col) {
                $col_name = explode(':', $col)[0];
                $row_values[] = $row[$col_name] ?? '';
            }
            $rows[] = implode(',', $row_values);
        }
        $result->free();
    } else {
        echo json_encode(['error' => 'Error fetching rows: ' . $conn->error]);
        exit;
    }

    echo json_encode(['columns' => $columns, 'rows' => $rows]);
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>