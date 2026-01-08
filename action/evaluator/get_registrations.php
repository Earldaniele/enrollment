<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in output
ini_set('log_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../frontend/includes/db_config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$status = $_GET['status'] ?? 'all';

try {
    // Build query based on status filter
    if ($status === 'all') {
        $stmt = $conn->prepare("SELECT * FROM student_registrations ORDER BY created_at DESC");
    } else {
        $stmt = $conn->prepare("SELECT * FROM student_registrations WHERE status = ? ORDER BY created_at DESC");
        $stmt->bind_param("s", $status);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $registrations = [];
    while ($row = $result->fetch_assoc()) {
        $registrations[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'registrations' => $registrations
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>
