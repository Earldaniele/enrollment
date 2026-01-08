<?php
// Ensure no whitespace or output before the session start
session_start();
require_once '../../frontend/includes/db_config.php';

// Set content type header for JSON response
header('Content-Type: application/json');

// Support both GET (for verifying) and POST (for updating) requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Handle GET request - Original verification behavior
    $studentId = $_GET['student_id'] ?? '';
    $queueId = $_GET['queue_id'] ?? '';

    if (empty($studentId)) {
        echo json_encode(['success' => false, 'message' => 'Student ID is required']);
        exit;
    }

    try {
        // If queue ID is provided, verify specific ticket
        if (!empty($queueId)) {
            $stmt = $conn->prepare("
                SELECT 
                    qt.id,
                    qt.student_id,
                    qt.queue_number,
                    qt.department,
                    qt.status,
                    qt.created_at,
                    qt.expires_at,
                    CONCAT(sr.first_name, ' ', COALESCE(sr.middle_name, ''), ' ', sr.last_name) as student_name
                FROM queue_tickets qt
                LEFT JOIN student_registrations sr ON qt.student_id = sr.student_id
                WHERE qt.id = ? AND qt.student_id = ?
                AND DATE(qt.created_at) = CURDATE()
            ");
            
            $stmt->bind_param("is", $queueId, $studentId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $ticket = $result->fetch_assoc();
                echo json_encode([
                    'success' => true,
                    'message' => 'Queue ticket verified successfully',
                    'ticket' => $ticket
                ]);
                exit;
            } else {
                echo json_encode(['success' => false, 'message' => 'Queue ticket not found']);
                exit;
            }
        } else {
            // Just verify student exists and check if they have any active queue tickets
            $stmt = $conn->prepare("
                SELECT 
                    student_id,
                    CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name) as student_name,
                    status
                FROM student_registrations 
                WHERE student_id = ?
            ");
            
            $stmt->bind_param("s", $studentId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $student = $result->fetch_assoc();
                
                // Check if student has an active queue ticket
                $queue_stmt = $conn->prepare("
                    SELECT 
                        qt.id,
                        qt.student_id,
                        qt.queue_number,
                        qt.department,
                        qt.status,
                        qt.created_at,
                        qt.expires_at
                    FROM queue_tickets qt
                    WHERE qt.student_id = ?
                    AND qt.status IN ('waiting', 'ready', 'in_progress')
                    AND DATE(qt.created_at) = CURDATE()
                    ORDER BY qt.created_at DESC
                    LIMIT 1
                ");
                
                $queue_stmt->bind_param("s", $studentId);
                $queue_stmt->execute();
                $queue_result = $queue_stmt->get_result();
                
                if ($queue_result->num_rows > 0) {
                    $ticket = $queue_result->fetch_assoc();
                    echo json_encode([
                        'success' => true,
                        'message' => 'Student found with active queue ticket',
                        'ticket' => $ticket,
                        'student' => $student
                    ]);
                    exit;
                } else {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Student found but has no active queue tickets',
                        'student' => $student
                    ]);
                    exit;
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Student not found']);
                exit;
            }
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false, 
            'message' => 'Error verifying queue: ' . $e->getMessage()
        ]);
        exit;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle POST request - Update queue status

    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || !isset($input['student_id']) || !isset($input['ticket_id'])) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        exit;
    }

    $studentId = $input['student_id'];
    $ticketId = $input['ticket_id'];
    $action = $input['action'] ?? 'verify';

    try {
        // Verify the queue ticket exists and belongs to the student
        $stmt = $conn->prepare("
            SELECT 
                qt.id,
                qt.student_id,
                qt.queue_number,
                qt.department,
                qt.status,
                qt.created_at,
                qt.expires_at
            FROM queue_tickets qt
            WHERE qt.id = ?
            AND qt.student_id = ?
        ");
        
        $stmt->bind_param("is", $ticketId, $studentId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Queue ticket not found or does not belong to the student']);
            exit;
        }
        
        $ticket = $result->fetch_assoc();
        
        // Check if the ticket is in a valid state for verification
        if ($ticket['status'] !== 'waiting' && $ticket['status'] !== 'ready') {
            echo json_encode([
                'success' => false, 
                'message' => 'Queue ticket cannot be verified - current status: ' . $ticket['status']
            ]);
            exit;
        }
        
        // Perform the action
        if ($action === 'verify') {
            // Update the ticket status to 'in_progress'
            $update_stmt = $conn->prepare("
                UPDATE queue_tickets
                SET status = 'in_progress',
                    verified_at = NOW(),
                    verified_by = ?
                WHERE id = ?
            ");
            
            $assistantId = $_SESSION['student_assistant_id'] ?? 'SYSTEM';
            $update_stmt->bind_param("si", $assistantId, $ticketId);
            
            if (!$update_stmt->execute()) {
                throw new Exception('Failed to update ticket status: ' . $conn->error);
            }
            
            // Get the updated ticket
            $stmt->execute();
            $updated_result = $stmt->get_result();
            $updated_ticket = $updated_result->fetch_assoc();
            
            echo json_encode([
                'success' => true,
                'message' => 'Queue ticket successfully verified',
                'ticket' => $updated_ticket
            ]);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid action requested']);
            exit;
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false, 
            'message' => 'Error verifying queue ticket: ' . $e->getMessage()
        ]);
        exit;
    }
} else {
    // Method not allowed
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Ensure connection is closed
$conn->close();
?>
