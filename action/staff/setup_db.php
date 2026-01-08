<?php
// Include database connection
require_once '../../frontend/includes/db_config.php';

// Disable error reporting to prevent JSON errors
error_reporting(0);
ini_set('display_errors', 0);

// Set content type to JSON if this file is accessed directly
if (basename($_SERVER['PHP_SELF']) === 'setup_db.php') {
    header('Content-Type: application/json');
}

// Check if tables already exist
function tableExists($conn, $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    return $result->num_rows > 0;
}

// Create evaluators table if it doesn't exist
if (!tableExists($conn, 'evaluators')) {
    $sql = "CREATE TABLE `evaluators` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `email` varchar(100) NOT NULL,
        `password` varchar(255) NOT NULL,
        `role` varchar(50) DEFAULT 'evaluator',
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `email` (`email`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    
    $conn->query($sql);
    
    // Insert demo data
    $conn->query("INSERT INTO `evaluators` (`name`, `email`, `password`, `role`) 
                VALUES ('Evaluator Demo', 'evaluator@ncst.edu.ph', 'password', 'evaluator')");
}

// Create registrars table if it doesn't exist
if (!tableExists($conn, 'registrars')) {
    $sql = "CREATE TABLE `registrars` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `email` varchar(100) NOT NULL,
        `password` varchar(255) NOT NULL,
        `role` varchar(50) DEFAULT 'registrar',
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `email` (`email`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    
    $conn->query($sql);
    
    // Insert demo data
    $conn->query("INSERT INTO `registrars` (`name`, `email`, `password`, `role`) 
                VALUES ('Registrar Demo', 'registrar@ncst.edu.ph', 'password', 'registrar')");
}

// Create cashiers table if it doesn't exist
if (!tableExists($conn, 'cashiers')) {
    $sql = "CREATE TABLE `cashiers` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `email` varchar(100) NOT NULL,
        `password` varchar(255) NOT NULL,
        `role` varchar(50) DEFAULT 'cashier',
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `email` (`email`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    
    $conn->query($sql);
    
    // Insert demo data
    $conn->query("INSERT INTO `cashiers` (`name`, `email`, `password`, `role`) 
                VALUES ('Cashier Demo', 'cashier@ncst.edu.ph', 'password', 'cashier')");
}

// Create student_assistants table if it doesn't exist
if (!tableExists($conn, 'student_assistants')) {
    $sql = "CREATE TABLE `student_assistants` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `email` varchar(100) NOT NULL,
        `password` varchar(255) NOT NULL,
        `role` varchar(50) DEFAULT 'student-assistant',
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `email` (`email`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    
    $conn->query($sql);
    
    // Insert demo data
    $conn->query("INSERT INTO `student_assistants` (`name`, `email`, `password`, `role`) 
                VALUES ('Student Assistant Demo', 'studentassistant@ncst.edu.ph', 'password', 'student-assistant')");
}

// Return success if called directly
if (basename($_SERVER['PHP_SELF']) === 'setup_db.php') {
    echo json_encode(['success' => true, 'message' => 'Staff tables setup complete']);
}
?>

// Create evaluators table if it doesn't exist
if (!tableExists($conn, 'evaluators')) {
    $sql = "CREATE TABLE `evaluators` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `email` varchar(100) NOT NULL,
        `password` varchar(255) NOT NULL,
        `role` varchar(50) DEFAULT 'evaluator',
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `email` (`email`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    
    $conn->query($sql);
    
    // Insert demo data
    $conn->query("INSERT INTO `evaluators` (`name`, `email`, `password`, `role`) 
                VALUES ('Evaluator Demo', 'evaluator@ncst.edu.ph', 'password', 'evaluator')");
}

// Create registrars table if it doesn't exist
if (!tableExists($conn, 'registrars')) {
    $sql = "CREATE TABLE `registrars` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `email` varchar(100) NOT NULL,
        `password` varchar(255) NOT NULL,
        `role` varchar(50) DEFAULT 'registrar',
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `email` (`email`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    
    $conn->query($sql);
    
    // Insert demo data
    $conn->query("INSERT INTO `registrars` (`name`, `email`, `password`, `role`) 
                VALUES ('Registrar Demo', 'registrar@ncst.edu.ph', 'password', 'registrar')");
}

// Create cashiers table if it doesn't exist
if (!tableExists($conn, 'cashiers')) {
    $sql = "CREATE TABLE `cashiers` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `email` varchar(100) NOT NULL,
        `password` varchar(255) NOT NULL,
        `role` varchar(50) DEFAULT 'cashier',
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `email` (`email`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    
    $conn->query($sql);
    
    // Insert demo data
    $conn->query("INSERT INTO `cashiers` (`name`, `email`, `password`, `role`) 
                VALUES ('Cashier Demo', 'cashier@ncst.edu.ph', 'password', 'cashier')");
}

// Create student_assistants table if it doesn't exist
if (!tableExists($conn, 'student_assistants')) {
    $sql = "CREATE TABLE `student_assistants` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `email` varchar(100) NOT NULL,
        `password` varchar(255) NOT NULL,
        `role` varchar(50) DEFAULT 'student-assistant',
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `email` (`email`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    
    $conn->query($sql);
    
    // Insert demo data
    $conn->query("INSERT INTO `student_assistants` (`name`, `email`, `password`, `role`) 
                VALUES ('Student Assistant Demo', 'student-assistant@ncst.edu.ph', 'password', 'student-assistant')");
}

// Return success if called directly
if (basename($_SERVER['PHP_SELF']) === 'setup_db.php') {
    echo json_encode(['success' => true, 'message' => 'Staff tables setup complete']);
}
?>
