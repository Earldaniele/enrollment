<?php
session_start();
require_once '../../frontend/includes/db_config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get form data
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validate input
if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Email and password are required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

try {
    // Check if evaluator exists and is active
    $stmt = $conn->prepare("SELECT id, email, password, name, role, status FROM evaluator_accounts WHERE email = ? AND status = 'active'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        exit;
    }
    
    $evaluator = $result->fetch_assoc();
    
    // Verify password
    if (!password_verify($password, $evaluator['password'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        exit;
    }
    
    // Create session
    $_SESSION['evaluator_id'] = $evaluator['id'];
    $_SESSION['evaluator_email'] = $evaluator['email'];
    $_SESSION['evaluator_name'] = $evaluator['name'];
    $_SESSION['evaluator_role'] = $evaluator['role'];
    $_SESSION['user_type'] = 'evaluator';
    
    // Update last login (optional)
    $update_stmt = $conn->prepare("UPDATE evaluator_accounts SET updated_at = NOW() WHERE id = ?");
    $update_stmt->bind_param("i", $evaluator['id']);
    $update_stmt->execute();
    
    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'evaluator' => [
            'id' => $evaluator['id'],
            'name' => $evaluator['name'],
            'email' => $evaluator['email'],
            'role' => $evaluator['role']
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Evaluator login error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'System error. Please try again later.']);
}

$conn->close();
?>
