<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_table_name = $_POST['current_table_name'];
    $new_table_name = $_POST['new_table_name'];
    $column_names = $_POST['column_names'] ?? [];
    $column_types = $_POST['column_types'] ?? [];
    $row_ids = $_POST['row_ids'] ?? [];
    $row_values = $_POST['row_values'] ?? [];

    // Sanitize table names
    $current_table_name = $conn->real_escape_string(preg_replace('/[^a-zA-Z0-9_]/', '', $current_table_name));
    $new_table_name = $conn->real_escape_string(preg_replace('/[^a-zA-Z0-9_]/', '', $new_table_name));

    // Check if table exists
    $result = $conn->query("SHOW TABLES LIKE '$current_table_name'");
    if ($result->num_rows === 0) {
        header("Location: tables.php?error=Table does not exist");
        exit;
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Rename table if changed
        if ($current_table_name !== $new_table_name) {
            $result = $conn->query("SHOW TABLES LIKE '$new_table_name'");
            if ($result->num_rows > 0) {
                throw new Exception("Table name already exists");
            }
            $sql = "ALTER TABLE `$current_table_name` RENAME TO `$new_table_name`";
            if (!$conn->query($sql)) {
                throw new Exception("Error renaming table: " . $conn->error);
            }
            $current_table_name = $new_table_name;
        }

        // Get current columns
        $current_columns = [];
        $result = $conn->query("SHOW COLUMNS FROM `$current_table_name`");
        while ($row = $result->fetch_assoc()) {
            if ($row['Field'] !== 'id') {
                $current_columns[$row['Field']] = $row['Type'];
            }
        }

        // Validate and process columns
        $allowed_data_types = ['VARCHAR(255)', 'INT', 'DECIMAL(10,2)', 'TEXT', 'DATETIME'];
        $new_columns = [];
        for ($i = 0; $i < count($column_names); $i++) {
            $name = $conn->real_escape_string(preg_replace('/[^a-zA-Z0-9_]/', '', $column_names[$i]));
            $type = $column_types[$i];
            if (empty($name) || !in_array($type, $allowed_data_types)) {
                throw new Exception("Invalid column definition: $name:$type");
            }
            $new_columns[$name] = $type;
        }

        // Update or add columns
        foreach ($new_columns as $new_name => $new_type) {
            if (isset($current_columns[$new_name])) {
                if ($current_columns[$new_name] !== strtolower($new_type)) {
                    $sql = "ALTER TABLE `$current_table_name` CHANGE `$new_name` `$new_name` $new_type";
                        if (!$conn->query($sql)) {
                            throw new Exception("Error updating column $new_name: " . $conn->error);
                        }
                }
            } else {
                $sql = "ALTER TABLE `$current_table_name` ADD `$new_name` $new_type";
                if (!$conn->query($sql)) {
                    throw new Exception("Error adding column $new_name: " . $conn->error);
                }
            }
        }

        // Process rows
        $column_names = array_keys($new_columns ?: $current_columns);
        foreach ($row_ids as $index => $id) {
            $id = $conn->real_escape_string($id);
            $values = $row_values[$id] ?? [];
            if (count($values) !== count($column_names)) {
                throw new Exception("Row data does not match column count for ID $id");
            }
            $set_clause = [];
            foreach ($column_names as $col_index => $col) {
                $value = $values[$col_index] === '' ? 'NULL' : "'" . $conn->real_escape_string($values[$col_index]) . "'";
                $set_clause[] = "`$col` = $value";
            }
            $set_sql = implode(", ", $set_clause);
            if (is_numeric($id)) {
                // Update existing row
                $sql = "UPDATE `$current_table_name` SET $set_sql WHERE id = '$id'";
                    if (!$conn->query($sql) || $conn->affected_rows === 0) {
                        throw new Exception("Error updating row with ID $id: " . $conn->error);
                    }
            } else {
                // Insert new row
                $sql = "INSERT INTO `$current_table_name` (`" . implode("`, `", $column_names) . "`) VALUES (" . implode(", ", array_map(fn($v) => $v === 'NULL' ? 'NULL' : $v, $set_clause)) . ")";
                if (!$conn->query($sql)) {
                    throw new Exception("Error inserting new row: " . $conn->error);
                }
            }
        }

        $conn->commit();
        header("Location: tables.php?success=Table updated successfully");
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: tables.php?error=" . urlencode($e->getMessage()));
    }
} else {
    header("Location: tables.php?error=Invalid request");
}
?>
```