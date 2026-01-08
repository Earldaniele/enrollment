<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$database = "enrollment_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");

// Helper function to get database connection
function getDBConnection() {
    global $conn;
    return $conn;
}

// Function to escape string for database queries
function escape_string($str) {
    global $conn;
    return $conn->real_escape_string($str);
}

// Function to prepare statements safely
function prepare_statement($query) {
    global $conn;
    return $conn->prepare($query);
}
?>
