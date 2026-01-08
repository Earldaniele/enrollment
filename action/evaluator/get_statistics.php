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

try {
    // Get statistics
    $stats = [];
    
    // Pending registrations
    $pending_stmt = $conn->prepare("SELECT COUNT(*) as count FROM student_registrations WHERE status = 'pending'");
    $pending_stmt->execute();
    $stats['pending'] = $pending_stmt->get_result()->fetch_assoc()['count'];
    
    // Approved today
    $approved_today_stmt = $conn->prepare("SELECT COUNT(*) as count FROM student_registrations WHERE status = 'approved' AND DATE(updated_at) = CURDATE()");
    $approved_today_stmt->execute();
    $stats['approved_today'] = $approved_today_stmt->get_result()->fetch_assoc()['count'];
    
    // Rejected today
    $rejected_today_stmt = $conn->prepare("SELECT COUNT(*) as count FROM student_registrations WHERE status = 'rejected' AND DATE(updated_at) = CURDATE()");
    $rejected_today_stmt->execute();
    $stats['rejected_today'] = $rejected_today_stmt->get_result()->fetch_assoc()['count'];
    
    // Total registrations
    $total_stmt = $conn->prepare("SELECT COUNT(*) as count FROM student_registrations");
    $total_stmt->execute();
    $stats['total'] = $total_stmt->get_result()->fetch_assoc()['count'];
    
    echo json_encode([
        'success' => true,
        'stats' => $stats
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>
