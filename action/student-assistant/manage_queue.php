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

if (!$input || !isset($input['ticket_id']) || !isset($input['action'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$ticketId = $input['ticket_id'];
$action = $input['action'];

try {
    // First, get the current ticket information
    $stmt = $conn->prepare("
        SELECT id, student_id, status, department
        FROM queue_tickets 
        WHERE id = ?
    ");
    
    $stmt->bind_param("i", $ticketId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Queue ticket not found']);
        exit;
    }
    
    $ticket = $result->fetch_assoc();
    $currentStatus = $ticket['status'];
    
    // Determine new status based on action
    $newStatus = '';
    $message = '';
    
    switch ($action) {
        case 'call_next':
            if ($currentStatus === 'waiting') {
                $newStatus = 'ready';
                $message = 'Student has been called successfully. They have 2 minutes to get their QR code scanned.';
            } else {
                echo json_encode(['success' => false, 'message' => 'Student is not in waiting status']);
                exit;
            }
            break;
            
        case 'start_processing':
            if ($currentStatus === 'ready') {
                $newStatus = 'in_progress';
                $message = 'Student is now being processed';
            } else {
                echo json_encode(['success' => false, 'message' => 'Student is not currently ready']);
                exit;
            }
            break;
            
        case 'complete':
            if ($currentStatus === 'in_progress') {
                $newStatus = 'completed';
                $message = 'Queue ticket completed successfully';
            } else {
                echo json_encode(['success' => false, 'message' => 'Student is not currently being processed']);
                exit;
            }
            break;
            
        case 'no_show':
            if (in_array($currentStatus, ['waiting', 'ready'])) {
                $newStatus = 'expired';
                $message = 'Student marked as no-show';
            } else {
                echo json_encode(['success' => false, 'message' => 'Cannot mark student as no-show in current status']);
                exit;
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            exit;
    }
    
    // Update the ticket status
    $update_stmt = $conn->prepare("
        UPDATE queue_tickets 
        SET status = ?, updated_at = NOW()
        WHERE id = ?
    ");
    
    $update_stmt->bind_param("si", $newStatus, $ticketId);
    
    if ($update_stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => $message,
            'ticket_id' => $ticketId,
            'old_status' => $currentStatus,
            'new_status' => $newStatus
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update ticket status']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>
