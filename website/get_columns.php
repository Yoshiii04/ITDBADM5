<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['table_name'])) {
    $table_name = $conn->real_escape_string(preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['table_name']));
    $result = $conn->query("SHOW COLUMNS FROM `$table_name`");

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $column = $row['Field'];
            $type = $row['Type'];
            // Skip auto-increment and timestamp columns
            if ($column !== 'id' && !str_contains($column, '_id') && !str_contains($column, '_at')) {
                echo "<div class='form-group'>";
                echo "<label>" . htmlspecialchars($column) . " ($type)</label>";
                if (str_contains($type, 'int') || str_contains($type, 'decimal')) {
                    echo "<input type='number' name='$column' class='form-control' placeholder='Enter $column' step='" . (str_contains($type, 'decimal') ? '0.01' : '1') . "'>";
                } elseif (str_contains($type, 'datetime')) {
                    echo "<input type='datetime-local' name='$column' class='form-control' placeholder='Enter $column'>";
                } else {
                    echo "<input type='text' name='$column' class='form-control' placeholder='Enter $column'>";
                }
                echo "</div>";
            }
        }
        $result->free();
    } else {
        echo "<p class='text-danger'>Error fetching columns: " . $conn->error . "</p>";
    }
} else {
    echo "<p class='text-danger'>Invalid request</p>";
}
?>