<?php
// Ensure no whitespace or output before the session start
session_start();
require_once '../../frontend/includes/db_config.php';

// Set JSON content type header for all responses
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['qr_data'])) {
    echo json_encode(['success' => false, 'message' => 'QR data is required']);
    exit;
}

$qrData = $input['qr_data'];

try {
    // Parse QR data - it should contain student information
    $parsedData = null;
    
    // Try to parse as JSON first
    if (is_string($qrData)) {
        try {
            $parsedData = json_decode($qrData, true);
        } catch (Exception $e) {
            // If not JSON, treat as plain student ID
            $parsedData = ['student_id' => $qrData];
        }
    } else {
        $parsedData = $qrData;
    }
    
    if (!$parsedData || !isset($parsedData['student_id'])) {
        // Try URL format fallback (for QR codes that contain URLs)
        if (is_string($qrData) && (strpos($qrData, 'scan.php?') !== false || strpos($qrData, '?s=') !== false)) {
            // Extract parameters from URL-like string
            $params = [];
            parse_str(parse_url($qrData, PHP_URL_QUERY) ?? "", $params);
            
            if (isset($params['s'])) {
                $parsedData = [
                    'student_id' => $params['s'],
                    'queue_id' => $params['q'] ?? null,
                    'queue_number' => $params['n'] ?? null
                ];
            }
        }
        
        // If still not valid, exit with error
        if (!$parsedData || !isset($parsedData['student_id'])) {
            echo json_encode([
                'success' => false, 
                'message' => 'Invalid QR code format - missing student ID',
                'raw_data' => $qrData
            ]);
            exit;
        }
    }
    
    $studentId = $parsedData['student_id'];
    $queueId = $parsedData['queue_id'] ?? null;
    
    // Verify student exists
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
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Student not found in system']);
        exit;
    }
    
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
        
        // If queue ID is provided, verify it matches
        if ($queueId && $ticket['id'] != $queueId) {
            echo json_encode([
                'success' => false,
                'message' => 'Queue ticket mismatch',
                'student' => $student,
                'note' => 'QR code contains different queue information'
            ]);
            exit;
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'QR Code scanned successfully',
            'ticket' => $ticket,
            'student' => $student,
            'scan_info' => [
                'timestamp' => date('Y-m-d H:i:s'),
                'qr_data' => $parsedData
            ]
        ]);
        exit;
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Student found but not currently in queue',
            'student' => $student,
            'scan_info' => [
                'timestamp' => date('Y-m-d H:i:s'),
                'qr_data' => $parsedData
            ]
        ]);
        exit;
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Error processing QR code: ' . $e->getMessage()
    ]);
    exit;
}

// Ensure connection is closed
$conn->close();
?>
