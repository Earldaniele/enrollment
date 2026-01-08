<?php
// Start the session
session_start();

// Set content type to JSON
header('Content-Type: application/json');

// Store username before clearing session
$user_name = $_SESSION['staff_name'] ?? 'User';

// Clear all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Return success response
echo json_encode([
    'success' => true,
    'message' => 'You have been successfully logged out.',
    'user_name' => $user_name,
    'redirect_url' => '../../frontend/staff/index.php'
]);
?>
