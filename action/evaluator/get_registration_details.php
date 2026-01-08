<?php
require_once '../../frontend/includes/db_config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$registration_id = $_GET['id'] ?? null;

if (!$registration_id) {
    echo json_encode(['success' => false, 'message' => 'Registration ID is required']);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT * FROM student_registrations WHERE id = ?");
    $stmt->bind_param("i", $registration_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Registration not found']);
        exit;
    }
    
    $registration = $result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'registration' => $registration
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>
