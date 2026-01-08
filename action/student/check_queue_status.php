<?php
session_start();
require_once '../../frontend/includes/db_config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get student ID from session
if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

try {
    // Get student info from session
    $email = $_SESSION['email'];
    $student_stmt = $conn->prepare("SELECT student_id FROM student_registrations WHERE email_address = ? LIMIT 1");
    $student_stmt->bind_param("s", $email);
    $student_stmt->execute();
    $student_result = $student_stmt->get_result();
    
    if ($student_result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Student registration not found']);
        exit;
    }
    
    $student_data = $student_result->fetch_assoc();
    $student_id = $student_data['student_id'];
    
    if (empty($student_id)) {
        echo json_encode(['success' => false, 'message' => 'Student ID not found']);
        exit;
    }
    
    // Get all active queue tickets for this student
    $stmt = $conn->prepare("SELECT * FROM queue_tickets WHERE student_id = ? AND status IN ('waiting', 'ready', 'in_progress') ORDER BY created_at DESC");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $active_queues = [];
    
    if ($result->num_rows === 0) {
        // No active queues found
        echo json_encode([
            'success' => true,
            'queues' => []
        ]);
        exit;
    }
    
    while ($ticket = $result->fetch_assoc()) {
        // Check if timer-based status (ready or in_progress) has expired
        if (($ticket['status'] === 'ready' || $ticket['status'] === 'in_progress') && $ticket['expires_at'] !== null) {
            // Check if ticket has expired
            if (strtotime($ticket['expires_at']) < time()) {
                // Update ticket to expired
                $update_stmt = $conn->prepare("UPDATE queue_tickets SET status = 'expired' WHERE id = ?");
                $update_stmt->bind_param("i", $ticket['id']);
                $update_stmt->execute();
                continue; // Skip this ticket as it's now expired
            } else {
                // Calculate time remaining using TIMESTAMPDIFF for accuracy
                $time_stmt = $conn->prepare("SELECT TIMESTAMPDIFF(SECOND, NOW(), ?) as seconds_remaining");
                $time_stmt->bind_param("s", $ticket['expires_at']);
                $time_stmt->execute();
                $time_result = $time_stmt->get_result();
                $time_data = $time_result->fetch_assoc();
                
                $time_remaining_seconds = max(0, (int)$time_data['seconds_remaining']);
                
                // Store original expiration timestamp to prevent timer reset on page refresh
                $expires_timestamp = strtotime($ticket['expires_at']);
                
                $minutes = floor($time_remaining_seconds / 60);
                $seconds = $time_remaining_seconds % 60;
                
                $time_remaining = sprintf('%02d:%02d', $minutes, $seconds);
            }
        } else if ($ticket['status'] === 'waiting') {
            // Check if this waiting ticket should be promoted to 'ready'
            // (if no other tickets are active for this department)
            $check_stmt = $conn->prepare(
                "SELECT COUNT(*) as ahead FROM queue_tickets 
                WHERE department = ? AND status IN ('ready', 'in_progress') 
                AND DATE(created_at) = CURDATE()
                AND created_at < ?
                AND id != ?"
            );
            $check_stmt->bind_param("ssi", $ticket['department'], $ticket['created_at'], $ticket['id']);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            $check_data = $check_result->fetch_assoc();
            
            if ($check_data['ahead'] == 0) {
                // No tickets ahead, promote this one to 'ready'
                $expires_at = date('Y-m-d H:i:s', strtotime('+2 minutes'));
                $update_stmt = $conn->prepare("UPDATE queue_tickets SET status = 'ready', expires_at = ? WHERE id = ?");
                $update_stmt->bind_param("si", $expires_at, $ticket['id']);
                $update_stmt->execute();
                
                // Update ticket data in memory
                $ticket['status'] = 'ready';
                $ticket['expires_at'] = $expires_at;
                
                // Calculate time remaining
                $time_remaining_seconds = 120; // 2 minutes
                $expires_timestamp = strtotime($expires_at);
                $time_remaining = "02:00";
            } else {
                // Still waiting
                $time_remaining = 'Waiting...';
                $time_remaining_seconds = null;
            }
        } else {
            $time_remaining = 'Waiting...';
            $time_remaining_seconds = null;
        }
        
        $active_queues[] = [
            'id' => $ticket['id'],
            'queue_number' => $ticket['queue_number'],
            'department' => $ticket['department'],
            'status' => $ticket['status'],
            'qr_data' => $ticket['qr_data'],
            'created_at' => $ticket['created_at'],
            'expires_at' => $ticket['expires_at'],
            'expires_timestamp' => isset($expires_timestamp) ? $expires_timestamp : null,
            'time_remaining' => $time_remaining,
            'time_remaining_seconds' => $time_remaining_seconds
        ];
    }
    
    echo json_encode([
        'success' => true,
        'queues' => $active_queues
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>
