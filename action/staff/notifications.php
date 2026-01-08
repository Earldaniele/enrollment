<?php
session_start();
require_once dirname(__DIR__, 2) . '/frontend/includes/db_config.php';

header('Content-Type: application/json');

// Check if staff is logged in using the unified staff system
if (!isset($_SESSION['staff_email']) && !isset($_SESSION['email']) && !isset($_SESSION['staff_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

// Get staff info from session - be flexible with different session formats
$user_email = $_SESSION['staff_email'] ?? $_SESSION['email'] ?? '';

// For cashiers who might not have email in session, use default based on role
if (empty($user_email) && isset($_SESSION['staff_type'])) {
    switch($_SESSION['staff_type']) {
        case 'cashier':
            $user_email = 'cashier@ncst.edu.ph';
            break;
        case 'registrar':
            $user_email = 'registrar@ncst.edu.ph';
            break;
        case 'evaluator':
            $user_email = 'evaluator@ncst.edu.ph';
            break;
    }
}

$user_type = 'evaluator';  // Use 'evaluator' to match existing notification data
$staff_role = $_SESSION['staff_type'] ?? $_SESSION['role'] ?? '';

try {
    // Get action parameter
    $action = $_GET['action'] ?? $_POST['action'] ?? '';

    switch ($action) {
        case 'fetch':
            // Fetch notifications for the staff user
            $stmt = $conn->prepare("
                SELECT id, title, message, type, icon, is_read, created_at 
                FROM notifications 
                WHERE user_email = ? AND user_type = ? 
                ORDER BY created_at DESC 
                LIMIT 20
            ");
            $stmt->bind_param("ss", $user_email, $user_type);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $notifications = [];
            while ($row = $result->fetch_assoc()) {
                $row['time_ago'] = time_ago($row['created_at']);
                $notifications[] = $row;
            }
            
            // Get unread count
            $stmt = $conn->prepare("
                SELECT COUNT(*) as unread_count 
                FROM notifications 
                WHERE user_email = ? AND user_type = ? AND is_read = FALSE
            ");
            $stmt->bind_param("ss", $user_email, $user_type);
            $stmt->execute();
            $unread_result = $stmt->get_result();
            $unread_row = $unread_result->fetch_assoc();
            $unread_count = $unread_row['unread_count'];
            
            echo json_encode([
                'success' => true,
                'notifications' => $notifications,
                'unread_count' => $unread_count
            ]);
            break;

        case 'mark_read':
            $notification_id = $_POST['notification_id'] ?? 0;
            
            if ($notification_id > 0) {
                $stmt = $conn->prepare("
                    UPDATE notifications 
                    SET is_read = TRUE, updated_at = NOW() 
                    WHERE id = ? AND user_email = ? AND user_type = ?
                ");
                $stmt->bind_param("iss", $notification_id, $user_email, $user_type);
                $stmt->execute();
                
                if ($stmt->affected_rows > 0) {
                    echo json_encode(['success' => true, 'message' => 'Notification marked as read']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Notification not found or already read']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid notification ID']);
            }
            break;

        case 'mark_all_read':
            $stmt = $conn->prepare("
                UPDATE notifications 
                SET is_read = TRUE, updated_at = NOW() 
                WHERE user_email = ? AND user_type = ? AND is_read = FALSE
            ");
            $stmt->bind_param("ss", $user_email, $user_type);
            $stmt->execute();
            
            $affected_rows = $stmt->affected_rows;
            echo json_encode([
                'success' => true, 
                'message' => "Marked {$affected_rows} notifications as read"
            ]);
            break;

        case 'delete':
            $notification_id = $_POST['notification_id'] ?? 0;
            if ($notification_id > 0) {
                $stmt = $conn->prepare("
                    DELETE FROM notifications 
                    WHERE id = ? AND user_email = ? AND user_type = ?
                ");
                $stmt->bind_param("iss", $notification_id, $user_email, $user_type);
                $stmt->execute();
                if ($stmt->affected_rows > 0) {
                    echo json_encode(['success' => true, 'message' => 'Notification deleted successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Notification not found or already deleted']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid notification ID']);
            }
            break;

        case 'create':
            // Create a new notification (for staff to create notifications for students)
            $target_email = $_POST['target_email'] ?? '';
            $target_type = $_POST['target_type'] ?? 'student';
            $title = $_POST['title'] ?? '';
            $message = $_POST['message'] ?? '';
            $notification_type = $_POST['notification_type'] ?? 'info';
            $icon = $_POST['icon'] ?? 'bi-info-circle-fill';
            
            if (empty($target_email) || empty($title) || empty($message)) {
                echo json_encode(['success' => false, 'message' => 'Missing required fields']);
                break;
            }
            
            $stmt = $conn->prepare("
                INSERT INTO notifications (user_email, user_type, title, message, type, icon, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->bind_param("ssssss", $target_email, $target_type, $title, $message, $notification_type, $icon);
            $stmt->execute();
            
            if ($stmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'Notification created successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to create notification']);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

// Helper function to format time ago
function time_ago($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
?>
