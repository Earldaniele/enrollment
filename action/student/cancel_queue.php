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

// Accept either ticket_id or queue_id for backward compatibility
if (isset($input['ticket_id'])) {
    $queue_id = $input['ticket_id'];
} elseif (isset($input['queue_id'])) {
    $queue_id = $input['queue_id'];
} else {
    echo json_encode(['success' => false, 'message' => 'Queue ID is required']);
    exit;
}

try {
    // Find and cancel the specific queue ticket
    $stmt = $conn->prepare("SELECT id, student_id FROM queue_tickets WHERE id = ? AND status IN ('waiting', 'ready', 'in_progress')");
    $stmt->bind_param("i", $queue_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $ticket = $result->fetch_assoc();
        
        // Get the department of this ticket
        $dept_stmt = $conn->prepare("SELECT department, status FROM queue_tickets WHERE id = ?");
        $dept_stmt->bind_param("i", $queue_id);
        $dept_stmt->execute();
        $dept_result = $dept_stmt->get_result();
        $ticket_data = $dept_result->fetch_assoc();
        $department = $ticket_data['department'];
        $current_status = $ticket_data['status'];
        
        // Start transaction to ensure consistency
        $conn->begin_transaction();
        
        try {
            // Update ticket status to cancelled
            $update_stmt = $conn->prepare("UPDATE queue_tickets SET status = 'cancelled', updated_at = NOW() WHERE id = ?");
            $update_stmt->bind_param("i", $queue_id);
            
            if (!$update_stmt->execute()) {
                throw new Exception("Failed to update ticket status");
            }
            
            // If this was a 'ready' ticket, promote the next waiting ticket to 'ready'
            if ($current_status === 'ready') {
                $next_stmt = $conn->prepare(
                    "SELECT id FROM queue_tickets 
                    WHERE department = ? AND status = 'waiting' 
                    AND DATE(created_at) = CURDATE() 
                    ORDER BY created_at ASC LIMIT 1"
                );
                $next_stmt->bind_param("s", $department);
                $next_stmt->execute();
                $next_result = $next_stmt->get_result();
                
                if ($next_result->num_rows > 0) {
                    $next_ticket = $next_result->fetch_assoc();
                    $expires_at = date('Y-m-d H:i:s', strtotime('+2 minutes'));
                    
                    $promote_stmt = $conn->prepare("UPDATE queue_tickets SET status = 'ready', expires_at = ? WHERE id = ?");
                    $promote_stmt->bind_param("si", $expires_at, $next_ticket['id']);
                    $promote_stmt->execute();
                }
            }
            
            $conn->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Your queue ticket has been cancelled successfully. You can join the queue again anytime.'
            ]);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => 'Unable to cancel your queue ticket. Please try again or contact support if the problem persists.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No active queue ticket found. The ticket may have already been processed or cancelled.']);
    }
    
} catch (Exception $e) {
    error_log("Queue cancellation error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'A system error occurred while cancelling your queue ticket. Please try again later.']);
}

$conn->close();
?>
