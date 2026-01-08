<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Prevent any output
error_reporting(0);
ini_set('display_errors', 0);

session_start();
require_once '../../frontend/includes/db_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['registration_id']) || !isset($input['status'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$registration_id = intval($input['registration_id']);
$status = trim($input['status']);

// Validate status
if (!in_array($status, ['pending', 'approved', 'rejected'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

try {
    // Get student registration data
    $reg_stmt = $conn->prepare("SELECT * FROM student_registrations WHERE id = ?");
    $reg_stmt->bind_param("i", $registration_id);
    $reg_stmt->execute();
    $reg_result = $reg_stmt->get_result();
    $reg_data = $reg_result->fetch_assoc();
    
    if (!$reg_data) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Registration not found']);
        exit;
    }
    
    $student_id = null;
    
    if ($status === 'approved') {
        // Generate student ID
        $year = date('Y');
        $count_stmt = $conn->prepare("SELECT COUNT(*) as count FROM student_registrations WHERE status = 'approved' AND YEAR(updated_at) = ?");
        $count_stmt->bind_param("s", $year);
        $count_stmt->execute();
        $count_result = $count_stmt->get_result();
        $count_data = $count_result->fetch_assoc();
        
        $next_number = str_pad($count_data['count'] + 1, 5, '0', STR_PAD_LEFT);
        $student_id = $year . '-' . $next_number;
        
        // Create student account if needed
        $email = $reg_data['email_address'];
        if (!empty($email)) {
            $check_account = $conn->prepare("SELECT id FROM student_accounts WHERE email = ?");
            $check_account->bind_param("s", $email);
            $check_account->execute();
            $account_result = $check_account->get_result();
            
            if ($account_result->num_rows === 0) {
                $default_password = password_hash('password123', PASSWORD_DEFAULT);
                $create_account = $conn->prepare("INSERT INTO student_accounts (email, password, first_name, last_name, phone, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                $create_account->bind_param("sssss", $email, $default_password, $reg_data['first_name'], $reg_data['last_name'], $reg_data['mobile_no']);
                $create_account->execute();
            }
        }
        
        // Update with student ID
        $stmt = $conn->prepare("UPDATE student_registrations SET status = ?, student_id = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("ssi", $status, $student_id, $registration_id);
    } else {
        // Update without student ID
        $stmt = $conn->prepare("UPDATE student_registrations SET status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("si", $status, $registration_id);
    }
    
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo json_encode([
            'success' => true,
            'message' => "Registration {$status} successfully",
            'student_id' => $student_id
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update registration status']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}

if (isset($conn)) {
    $conn->close();
}
exit;
?>
