<?php
session_start();
require_once '../../frontend/includes/db_config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$status = $_GET['status'] ?? 'pending';
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

try {
    // Get student registrations with pagination
    $stmt = $conn->prepare("
        SELECT 
            id,
            student_id,
            CONCAT(first_name, ' ', middle_name, ' ', last_name) as full_name,
            desired_course,
            email_address,
            mobile_no,
            status,
            created_at
        FROM student_registrations 
        WHERE status = ?
        ORDER BY created_at DESC
        LIMIT ? OFFSET ?
    ");
    
    $stmt->bind_param("sii", $status, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $registrations = [];
    while ($row = $result->fetch_assoc()) {
        $registrations[] = $row;
    }
    
    // Get total count
    $count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM student_registrations WHERE status = ?");
    $count_stmt->bind_param("s", $status);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $total_count = $count_result->fetch_assoc()['total'];
    
    echo json_encode([
        'success' => true,
        'data' => [
            'registrations' => $registrations,
            'total_count' => $total_count,
            'current_page' => floor($offset / $limit) + 1,
            'total_pages' => ceil($total_count / $limit),
            'status' => $status
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>
