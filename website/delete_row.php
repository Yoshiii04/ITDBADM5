
<?php
include 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['table_name'], $_POST['id'])) {
    $table_name = $conn->real_escape_string(preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['table_name']));
    $id = $conn->real_escape_string($_POST['id']);

    // Check if table exists
    $result = $conn->query("SHOW TABLES LIKE '$table_name'");
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'error' => 'Table does not exist']);
        exit;
    }

    // Check if row exists
    $result = $conn->query("SELECT id FROM `$table_name` WHERE id = '$id'");
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'error' => 'Row does not exist']);
        exit;
    }

    // Delete row
    $sql = "DELETE FROM `$table_name` WHERE id = '$id'";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error deleting row: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>
