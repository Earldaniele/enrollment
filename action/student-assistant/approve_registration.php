<?php
session_start();
require_once '../../frontend/includes/db_config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['registration_id']) || !isset($input['action'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$registration_id = $input['registration_id'];
$action = $input['action']; // 'approve' or 'reject'
$remarks = $input['remarks'] ?? '';

if (!in_array($action, ['approve', 'reject'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}

try {
    $new_status = ($action === 'approve') ? 'approved' : 'rejected';
    
    // Update registration status
    $stmt = $conn->prepare("UPDATE student_registrations SET status = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("si", $new_status, $registration_id);
    
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        // Get student information for response
        $info_stmt = $conn->prepare("SELECT student_id, first_name, last_name, email_address FROM student_registrations WHERE id = ?");
        $info_stmt->bind_param("i", $registration_id);
        $info_stmt->execute();
        $info_result = $info_stmt->get_result();
        $student_info = $info_result->fetch_assoc();
        
        // Log the action (optional - you can create an audit log table)
        $log_stmt = $conn->prepare("INSERT INTO registration_logs (registration_id, action, remarks, created_at) VALUES (?, ?, ?, NOW())");
        $log_stmt->bind_param("iss", $registration_id, $action, $remarks);
        $log_stmt->execute();
        
        echo json_encode([
            'success' => true,
            'message' => ucfirst($action) . 'd registration successfully',
            'data' => [
                'student_id' => $student_info['student_id'],
                'student_name' => $student_info['first_name'] . ' ' . $student_info['last_name'],
                'new_status' => $new_status
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update registration or registration not found']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>
