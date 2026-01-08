<?php
session_start();

// Clear all session data
session_destroy();

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'message' => 'Logout successful'
]);
?>
