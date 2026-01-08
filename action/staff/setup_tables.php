<?php
// Include database connection
require_once '../../frontend/includes/db_config.php';

// Read the SQL file
$sqlFile = file_get_contents('create_staff_tables.sql');

// Execute the SQL
if ($conn->multi_query($sqlFile)) {
    echo "Staff tables created successfully!";
    
    // Process all result sets
    do {
        // Store result
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->more_results() && $conn->next_result());
    
} else {
    echo "Error creating staff tables: " . $conn->error;
}

$conn->close();
?>
