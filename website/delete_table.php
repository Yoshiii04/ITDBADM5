
<?php
include 'config.php';

if (isset($_GET['table'])) {
    $table_name = $_GET['table'];

    // Sanitize table name
    $table_name = $conn->real_escape_string(preg_replace('/[^a-zA-Z0-9_]/', '', $table_name));

    $sql = "DROP TABLE `$table_name`";
    if ($conn->query($sql) === TRUE) {
        header("Location: tables.php?success=Table deleted successfully");
    } else {
        header("Location: tables.php?error=Error deleting table: " . $conn->error);
    }
} else {
    header("Location: tables.php?error=No table specified");
}
?>
```