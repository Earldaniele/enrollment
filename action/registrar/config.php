<?php
// Database configuration for registrar module
require_once '../../frontend/includes/db_config.php';

// Response helper function
function sendResponse($success, $message, $data = null) {
    header('Content-Type: application/json');
    $response = [
        'success' => $success,
        'message' => $message
    ];
    
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    echo json_encode($response);
    exit;
}

// Validate registrar authentication
function validateRegistrarAuth() {
    session_start();
    
    // Debug logging
    error_log("Auth Debug - staff_id: " . ($_SESSION['staff_id'] ?? 'null') . ", staff_type: " . ($_SESSION['staff_type'] ?? 'null'));
    
    if (!isset($_SESSION['staff_id']) || !isset($_SESSION['staff_type']) || $_SESSION['staff_type'] !== 'registrar') {
        sendResponse(false, 'Unauthorized access. Debug: staff_id=' . ($_SESSION['staff_id'] ?? 'null') . ', staff_type=' . ($_SESSION['staff_type'] ?? 'null'));
    }
    return [
        'id' => $_SESSION['staff_id'],
        'name' => $_SESSION['staff_name'] ?? 'Registrar User',
        'role' => 'registrar'
    ];
}

// Get current academic year and semester
function getCurrentAcademicPeriod() {
    return [
        'school_year' => '2024-2025',
        'semester' => '1st Semester'
    ];
}
?>
