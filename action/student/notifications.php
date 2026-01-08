<?php
session_start();
require_once '../../frontend/includes/db_config.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$user_email = $_SESSION['email'];
$user_type = $_SESSION['user_type'] ?? 'student';

try {
    // Database connection is already established in db_config.php as $conn
    // Use the existing connection
    
    // Get action parameter
    $action = $_GET['action'] ?? $_POST['action'] ?? '';

    switch ($action) {
        case 'fetch':
            // Fetch notifications for the user
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
            $unread_count = $unread_result->fetch_assoc()['unread_count'];
            
            echo json_encode([
                'success' => true,
                'notifications' => $notifications,
                'unread_count' => $unread_count
            ]);
            break;

        case 'mark_read':
            $notification_id = $_POST['id'] ?? 0;
            
            if ($notification_id) {
                $stmt = $conn->prepare("
                    UPDATE notifications 
                    SET is_read = TRUE 
                    WHERE id = ? AND user_email = ? AND user_type = ?
                ");
                $stmt->bind_param("iss", $notification_id, $user_email, $user_type);
                $success = $stmt->execute();
                
                echo json_encode(['success' => $success]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid notification ID']);
            }
            break;

        case 'mark_all_read':
            $stmt = $conn->prepare("
                UPDATE notifications 
                SET is_read = TRUE 
                WHERE user_email = ? AND user_type = ? AND is_read = FALSE
            ");
            $stmt->bind_param("ss", $user_email, $user_type);
            $success = $stmt->execute();
            $affected_rows = $conn->affected_rows;
            
            echo json_encode([
                'success' => $success,
                'marked_count' => $affected_rows
            ]);
            break;

        case 'delete':
            $notification_id = $_POST['id'] ?? 0;
            
            if ($notification_id) {
                $stmt = $conn->prepare("
                    DELETE FROM notifications 
                    WHERE id = ? AND user_email = ? AND user_type = ?
                ");
                $stmt->bind_param("iss", $notification_id, $user_email, $user_type);
                $success = $stmt->execute();
                
                echo json_encode(['success' => $success]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid notification ID']);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function time_ago($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'Just now';
    if ($time < 3600) return floor($time/60) . ' minutes ago';
    if ($time < 86400) return floor($time/3600) . ' hours ago';
    if ($time < 2592000) return floor($time/86400) . ' days ago';
    if ($time < 31536000) return floor($time/2592000) . ' months ago';
    return floor($time/31536000) . ' years ago';
}
?>
