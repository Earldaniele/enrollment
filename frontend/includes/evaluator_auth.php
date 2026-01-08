<?php
// Evaluator authentication helper functions

// Check if evaluator is logged in
function isEvaluatorLoggedIn() {
    return isset($_SESSION['staff_id']) && isset($_SESSION['staff_type']) && $_SESSION['staff_type'] === 'evaluator';
}

// Get current evaluator info
function getCurrentEvaluator() {
    if (!isEvaluatorLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['staff_id'],
        'name' => $_SESSION['staff_name'] ?? 'Evaluator User',
        'role' => 'evaluator'
    ];
}

// Require evaluator authentication
function requireEvaluatorAuth() {
    if (!isEvaluatorLoggedIn()) {
        header('Location: ../index.php');
        exit;
    }
}

// Logout evaluator
function logoutEvaluator() {
    // Clear all session variables
    $_SESSION = [];
    
    // Destroy the session
    session_destroy();
}
?>
