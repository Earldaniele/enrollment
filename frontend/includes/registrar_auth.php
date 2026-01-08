<?php
// Registrar authentication helper functions

// Check if registrar is logged in
function isRegistrarLoggedIn() {
    return isset($_SESSION['staff_id']) && isset($_SESSION['staff_type']) && $_SESSION['staff_type'] === 'registrar';
}

// Get current registrar info
function getCurrentRegistrar() {
    if (!isRegistrarLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['staff_id'],
        'name' => $_SESSION['staff_name'] ?? 'Registrar User',
        'role' => 'registrar'
    ];
}

// Require registrar authentication
function requireRegistrarAuth() {
    if (!isRegistrarLoggedIn()) {
        header('Location: ../index.php');
        exit;
    }
}

// Logout registrar
function logoutRegistrar() {
    // Clear all session variables
    $_SESSION = [];
    
    // Destroy the session
    session_destroy();
}
?>
