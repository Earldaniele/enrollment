<?php
session_start();
require_once '../../frontend/includes/db_config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    // Get current queue for today (student-assistant handles all departments)
    $stmt = $conn->prepare("
        SELECT 
            qt.id,
            qt.student_id,
            qt.queue_number,
            qt.status,
            qt.created_at,
            qt.expires_at,
            qt.department,
            CONCAT(sr.first_name, ' ', COALESCE(sr.middle_name, ''), ' ', sr.last_name) as student_name
        FROM queue_tickets qt
        LEFT JOIN student_registrations sr ON qt.student_id = sr.student_id
        WHERE qt.status IN ('waiting', 'ready', 'in_progress')
        AND DATE(qt.created_at) = CURDATE()
        ORDER BY qt.created_at ASC
    ");
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $queue_list = [];
    while ($row = $result->fetch_assoc()) {
        $queue_list[] = $row;
    }
    
    // Get statistics for today
    $stats_stmt = $conn->prepare("
        SELECT 
            COUNT(*) as total_tickets,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
            SUM(CASE WHEN status = 'waiting' THEN 1 ELSE 0 END) as waiting,
            SUM(CASE WHEN status = 'ready' THEN 1 ELSE 0 END) as ready,
            SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
            SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as no_show,
            SUM(CASE WHEN status = 'expired' THEN 1 ELSE 0 END) as expired
        FROM queue_tickets 
        WHERE DATE(created_at) = CURDATE()
    ");
    
    $stats_stmt->execute();
    $stats_result = $stats_stmt->get_result();
    $stats = $stats_result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'waiting' => (int)$stats['waiting'],
            'ready' => (int)$stats['ready'],
            'in_progress' => (int)$stats['in_progress'],
            'completed' => (int)$stats['completed'],
            'cancelled' => (int)$stats['no_show'],
            'expired' => (int)$stats['expired'],
            'total' => (int)$stats['total_tickets']
        ],
        'queue' => $queue_list
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>
