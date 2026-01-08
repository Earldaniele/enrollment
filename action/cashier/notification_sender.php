<?php
session_start();
require_once __DIR__ . '/../../frontend/includes/db_config.php';
require_once __DIR__ . '/../../frontend/includes/cashier_auth.php';

header('Content-Type: application/json');

// Check if user is logged in as cashier
if (!isCashierLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Handle different request methods
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    handleGetRequest();
} elseif ($method === 'POST') {
    handlePostRequest();
}

function handleGetRequest() {
    if (isset($_GET['action']) && $_GET['action'] === 'recent') {
        getRecentNotifications();
    }
}

function handlePostRequest() {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        echo json_encode(['success' => false, 'message' => 'Invalid input data']);
        return;
    }
    
    sendBulkNotification($input);
}

function getRecentNotifications() {
    global $conn;
    
    try {
        $stmt = $conn->prepare("
            SELECT 
                title,
                user_email as recipient,
                type,
                created_at
            FROM notifications 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            ORDER BY created_at DESC 
            LIMIT 20
        ");
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $notifications = [];
        while ($row = $result->fetch_assoc()) {
            $notifications[] = [
                'title' => $row['title'],
                'recipient' => $row['recipient'],
                'type' => $row['type'],
                'created_at' => date('M j, Y g:i A', strtotime($row['created_at']))
            ];
        }
        
        echo json_encode([
            'success' => true,
            'notifications' => $notifications
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error fetching notifications: ' . $e->getMessage()
        ]);
    }
}

function sendBulkNotification($data) {
    global $conn;
    
    $recipientType = $data['recipient_type'] ?? '';
    $title = $data['title'] ?? '';
    $message = $data['message'] ?? '';
    $notificationType = $data['notification_type'] ?? 'info';
    $specificStudentId = $data['specific_student_id'] ?? null;
    
    // Validate required fields
    if (empty($recipientType) || empty($title) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        return;
    }
    
    try {
        $sentCount = 0;
        
        switch ($recipientType) {
            case 'all_students':
                $stmt = $conn->prepare("
                    SELECT DISTINCT email_address 
                    FROM student_registrations 
                    WHERE status = 'approved'
                ");
                $stmt->execute();
                $result = $stmt->get_result();
                
                while ($row = $result->fetch_assoc()) {
                    $stmt2 = $conn->prepare("
                        INSERT INTO notifications (user_email, user_type, title, message, type, icon, created_at) 
                        VALUES (?, 'student', ?, ?, ?, 'bi-bell-fill', NOW())
                    ");
                    $stmt2->bind_param("ssss", $row['email_address'], $title, $message, $notificationType);
                    if ($stmt2->execute()) {
                        $sentCount++;
                    }
                }
                break;
                
            case 'unpaid_students':
                $stmt = $conn->prepare("
                    SELECT DISTINCT sr.email_address 
                    FROM student_registrations sr
                    LEFT JOIN student_payments sp ON sr.student_id = sp.student_id AND sp.status = 'verified'
                    WHERE sr.status = 'approved' 
                    AND (sp.student_id IS NULL OR sp.amount < 1000)
                ");
                $stmt->execute();
                $result = $stmt->get_result();
                
                while ($row = $result->fetch_assoc()) {
                    $stmt2 = $conn->prepare("
                        INSERT INTO notifications (user_email, user_type, title, message, type, icon, created_at) 
                        VALUES (?, 'student', ?, ?, ?, 'bi-bell-fill', NOW())
                    ");
                    $stmt2->bind_param("ssss", $row['email_address'], $title, $message, $notificationType);
                    if ($stmt2->execute()) {
                        $sentCount++;
                    }
                }
                break;
                
            case 'partial_students':
                $stmt = $conn->prepare("
                    SELECT DISTINCT sr.email_address 
                    FROM student_registrations sr
                    JOIN student_payments sp ON sr.student_id = sp.student_id 
                    WHERE sr.status = 'approved' 
                    AND sp.status = 'verified'
                    AND sp.amount > 0 AND sp.amount < 1000
                ");
                $stmt->execute();
                $result = $stmt->get_result();
                
                while ($row = $result->fetch_assoc()) {
                    $stmt2 = $conn->prepare("
                        INSERT INTO notifications (user_email, user_type, title, message, type, icon, created_at) 
                        VALUES (?, 'student', ?, ?, ?, 'bi-bell-fill', NOW())
                    ");
                    $stmt2->bind_param("ssss", $row['email_address'], $title, $message, $notificationType);
                    if ($stmt2->execute()) {
                        $sentCount++;
                    }
                }
                break;
                
            case 'paid_students':
                $stmt = $conn->prepare("
                    SELECT DISTINCT sr.email_address 
                    FROM student_registrations sr
                    JOIN student_payments sp ON sr.student_id = sp.student_id 
                    WHERE sr.status = 'approved' 
                    AND sp.status = 'verified'
                    AND sp.amount >= 1000
                ");
                $stmt->execute();
                $result = $stmt->get_result();
                
                while ($row = $result->fetch_assoc()) {
                    $stmt2 = $conn->prepare("
                        INSERT INTO notifications (user_email, user_type, title, message, type, icon, created_at) 
                        VALUES (?, 'student', ?, ?, ?, 'bi-bell-fill', NOW())
                    ");
                    $stmt2->bind_param("ssss", $row['email_address'], $title, $message, $notificationType);
                    if ($stmt2->execute()) {
                        $sentCount++;
                    }
                }
                break;
                
            case 'specific_student':
                if (empty($specificStudentId)) {
                    echo json_encode(['success' => false, 'message' => 'Student ID is required']);
                    return;
                }
                
                $stmt = $conn->prepare("
                    SELECT email_address 
                    FROM student_registrations 
                    WHERE student_id = ? AND status = 'approved'
                ");
                $stmt->bind_param("s", $specificStudentId);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $stmt2 = $conn->prepare("
                        INSERT INTO notifications (user_email, user_type, title, message, type, icon, created_at) 
                        VALUES (?, 'student', ?, ?, ?, 'bi-bell-fill', NOW())
                    ");
                    $stmt2->bind_param("ssss", $row['email_address'], $title, $message, $notificationType);
                    if ($stmt2->execute()) {
                        $sentCount = 1;
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Student not found or not approved']);
                    return;
                }
                break;
                
            case 'recent_payments':
                $stmt = $conn->prepare("
                    SELECT DISTINCT sr.email_address 
                    FROM student_registrations sr
                    JOIN student_payments sp ON sr.student_id = sp.student_id 
                    WHERE sr.status = 'approved' 
                    AND sp.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                ");
                $stmt->execute();
                $result = $stmt->get_result();
                
                while ($row = $result->fetch_assoc()) {
                    $stmt2 = $conn->prepare("
                        INSERT INTO notifications (user_email, user_type, title, message, type, icon, created_at) 
                        VALUES (?, 'student', ?, ?, ?, 'bi-bell-fill', NOW())
                    ");
                    $stmt2->bind_param("ssss", $row['email_address'], $title, $message, $notificationType);
                    if ($stmt2->execute()) {
                        $sentCount++;
                    }
                }
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Invalid recipient type']);
                return;
        }
        
        if ($sentCount > 0) {
            echo json_encode([
                'success' => true, 
                'message' => "Notification sent successfully to {$sentCount} recipient(s)"
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'No recipients found or failed to send notifications'
            ]);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error sending notifications: ' . $e->getMessage()
        ]);
    }
}
?>
