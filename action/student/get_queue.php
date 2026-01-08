<?php
session_start();
require_once '../../frontend/includes/db_config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Handle both single department (legacy) and multiple departments (new)
if (isset($input['departments']) && is_array($input['departments'])) {
    $departments = $input['departments'];
} elseif (isset($input['department'])) {
    $departments = [$input['department']];
} else {
    echo json_encode(['success' => false, 'message' => 'No departments specified']);
    exit;
}

// Get student ID from session
if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

try {
    // Get student info from session
    $email = $_SESSION['email'];
    $student_stmt = $conn->prepare("SELECT student_id FROM student_registrations WHERE email_address = ? LIMIT 1");
    $student_stmt->bind_param("s", $email);
    $student_stmt->execute();
    $student_result = $student_stmt->get_result();
    
    if ($student_result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Student registration not found. Please complete your registration first.']);
        exit;
    }
    
    $student_data = $student_result->fetch_assoc();
    $student_id = $student_data['student_id'];
    
    if (empty($student_id)) {
        echo json_encode(['success' => false, 'message' => 'Student ID not assigned yet. Please contact an administrator.']);
        exit;
    }
    
    // Check if student already has active queues for any of these departments
    $placeholders = str_repeat('?,', count($departments) - 1) . '?';
    $check_stmt = $conn->prepare("SELECT department FROM queue_tickets WHERE student_id = ? AND department IN ($placeholders) AND status IN ('waiting', 'serving')");
    $check_stmt->bind_param(str_repeat('s', count($departments) + 1), $student_id, ...$departments);
    $check_stmt->execute();
    $existing_result = $check_stmt->get_result();
    
    if ($existing_result->num_rows > 0) {
        $existing_dept = $existing_result->fetch_assoc()['department'];
        echo json_encode(['success' => false, 'message' => "You already have an active queue ticket for $existing_dept"]);
        exit;
    }
    
    $created_queues = [];
    
    foreach ($departments as $department) {
        // Convert department to lowercase for consistency in database
        $department = strtolower($department);
        
        // Get next queue number for the department - only get the count of ACTUAL inserted tickets
        $queue_stmt = $conn->prepare("SELECT MAX(CAST(SUBSTRING(queue_number, 4) AS UNSIGNED)) as max_num FROM queue_tickets WHERE department = ? AND DATE(created_at) = CURDATE()");
        $queue_stmt->bind_param("s", $department);
        $queue_stmt->execute();
        $queue_result = $queue_stmt->get_result();
        $queue_data = $queue_result->fetch_assoc();
        
        // Get the max number and add 1, or start at 1 if no tickets exist
        $next_queue_num = ($queue_data['max_num'] > 0) ? ($queue_data['max_num'] + 1) : 1;
        $queue_number = str_pad($next_queue_num, 3, '0', STR_PAD_LEFT);
        $department_code = '';
        
        switch ($department) {
            case 'registrar':
                $department_code = 'RG';
                break;
            case 'treasury':
                $department_code = 'TR';
                break;
            case 'enrollment':
                $department_code = 'EN';
                break;
            default:
                $department_code = 'GN';
        }
        
        $full_queue_number = $department_code . '-' . $queue_number;
        
        // Generate QR code data
        $qr_data = json_encode([
            'queue_number' => $full_queue_number,
            'student_id' => $student_id,
            'department' => $department,
            'timestamp' => time()
        ]);
        
        // Start transaction for reliable queue number assignment
        $conn->begin_transaction();
        
        try {
            // First check if there are ANY active tickets for this department
            $active_stmt = $conn->prepare("SELECT COUNT(*) as active FROM queue_tickets WHERE department = ? AND status IN ('waiting', 'ready', 'in_progress') AND DATE(created_at) = CURDATE()");
            $active_stmt->bind_param("s", $department);
            $active_stmt->execute();
            $active_result = $active_stmt->get_result();
            $active_data = $active_result->fetch_assoc();
            
            // Calculate estimated wait time based on people ahead in queue
            $wait_time = $active_data['active'] * 1; // 1 minute per person
            $estimated_wait = $wait_time > 0 ? "{$wait_time} minutes" : "No wait time";
            
            // Double-check for active queues (race condition protection)
            $double_check = $conn->prepare("SELECT id FROM queue_tickets WHERE student_id = ? AND department = ? AND status IN ('waiting', 'ready', 'in_progress') LIMIT 1");
            $double_check->bind_param("ss", $student_id, $department);
            $double_check->execute();
            $double_result = $double_check->get_result();
            
            if ($double_result->num_rows > 0) {
                $conn->rollback();
                echo json_encode(['success' => false, 'message' => "You already have an active queue ticket for $department"]);
                exit;
            }
            
            // If no other tickets are active for this department, set status to 'ready' with 2-minute expiration timer
            // Otherwise, set to 'waiting'
            $status = $active_data['active'] == 0 ? 'ready' : 'waiting';
            $expires_at = ($status === 'ready') ? date('Y-m-d H:i:s', strtotime('+2 minutes')) : NULL;
            
            // Insert queue ticket
            $insert_stmt = $conn->prepare("INSERT INTO queue_tickets (student_id, department, queue_number, qr_data, status, created_at, expires_at) VALUES (?, ?, ?, ?, ?, NOW(), ?)");
            $insert_stmt->bind_param("ssssss", $student_id, $department, $full_queue_number, $qr_data, $status, $expires_at);
            
            if (!$insert_stmt->execute()) {
                $conn->rollback();
                echo json_encode(['success' => false, 'message' => "Failed to generate queue ticket for $department: " . $conn->error]);
                exit;
            }
            
            $ticket_id = $conn->insert_id;
            
            // Verify the insertion actually worked by checking if the ticket exists in the database
            $verify_stmt = $conn->prepare("SELECT * FROM queue_tickets WHERE id = ?");
            $verify_stmt->bind_param("i", $ticket_id);
            $verify_stmt->execute();
            $verify_result = $verify_stmt->get_result();
            
            if ($verify_result->num_rows === 0) {
                $conn->rollback();
                echo json_encode(['success' => false, 'message' => "Failed to verify queue ticket for $department"]);
                exit;
            }
            
            // Commit the transaction
            $conn->commit();
            
            $ticket_data = $verify_result->fetch_assoc();
            
            $created_queues[] = [
                'id' => $ticket_id,
                'ticket_id' => $ticket_id,
                'queue_number' => $full_queue_number,
                'department' => ucfirst($department),
                'qr_data' => $qr_data,
                'estimated_wait' => $estimated_wait,
                'status' => $status,
                'created_at' => date('Y-m-d H:i:s'),
                'expires_at' => $expires_at
            ];
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => "Error processing queue: " . $e->getMessage()]);
            exit;
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Queue tickets generated successfully',
        'queues' => $created_queues
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>
