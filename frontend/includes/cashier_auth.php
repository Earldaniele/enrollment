<?php
// Cashier authentication functions

function requireCashierAuth() {
    // Check if user is logged in and has cashier role
    if (!isset($_SESSION['staff_id']) || !isset($_SESSION['staff_type']) || $_SESSION['staff_type'] !== 'cashier') {
        // Redirect to staff login page
        header('Location: /enrollmentsystem/frontend/staff/index.php');
        exit();
    }
}

function getCurrentCashier() {
    // Return current cashier information from session
    return [
        'id' => $_SESSION['staff_id'] ?? 'CASH001',
        'name' => $_SESSION['staff_name'] ?? 'Treasury Staff',
        'email' => $_SESSION['staff_email'] ?? 'cashier@ncst.edu.ph',
        'role' => 'cashier'
    ];
}

function isCashierLoggedIn() {
    return isset($_SESSION['staff_id']) && isset($_SESSION['staff_type']) && $_SESSION['staff_type'] === 'cashier';
}
?>
