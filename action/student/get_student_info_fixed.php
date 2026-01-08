<?php
require_once '../../frontend/includes/db_config.php';
require_once '../../frontend/includes/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check if user is logged in using the correct session variable
    if (!isset($_SESSION['email'])) {
        echo json_encode([
            'success' => false,
            'message' => 'No user session found - please login',
            'debug' => [
                'session_exists' => false,
                'session_data' => $_SESSION ?? []
            ]
        ]);
        exit;
    }
    
    $current_email = $_SESSION['email'];
    
    // First, try to get student information from student_accounts table (basic account info)
    $stmt = $conn->prepare("
        SELECT 
            id as account_id,
            first_name,
            last_name,
            email,
            phone,
            created_at
        FROM student_accounts 
        WHERE email = ?
        LIMIT 1
    ");
    $stmt->bind_param("s", $current_email);
    $stmt->execute();
    $account_result = $stmt->get_result();
    
    $student_data = null;
    $data_source = '';
    
    if ($account_result->num_rows > 0) {
        // Found student account, get basic info
        $account = $account_result->fetch_assoc();
        
        // Now check if they also have enrollment registration info
        $reg_stmt = $conn->prepare("
            SELECT 
                student_id,
                first_name,
                middle_name,
                last_name,
                suffix,
                email_address,
                mobile_no,
                desired_course,
                status,
                created_at
            FROM student_registrations 
            WHERE email_address = ?
            ORDER BY created_at DESC 
            LIMIT 1
        ");
        $reg_stmt->bind_param("s", $current_email);
        $reg_stmt->execute();
        $reg_result = $reg_stmt->get_result();
        
        if ($reg_result->num_rows > 0) {
            // Has both account and registration - use registration data (more complete)
            $student_data = $reg_result->fetch_assoc();
            $data_source = 'registration';
        } else {
            // Only has account, use account data
            $student_data = [
                'student_id' => null,
                'first_name' => $account['first_name'],
                'middle_name' => '',
                'last_name' => $account['last_name'],
                'suffix' => '',
                'email_address' => $account['email'],
                'mobile_no' => $account['phone'],
                'desired_course' => '',
                'status' => 'account_only',
                'created_at' => $account['created_at']
            ];
            $data_source = 'account';
        }
    } else {
        // Fallback: check only student_registrations table (legacy)
        $stmt = $conn->prepare("
            SELECT 
                student_id,
                first_name,
                middle_name,
                last_name,
                suffix,
                email_address,
                mobile_no,
                desired_course,
                status,
                created_at
            FROM student_registrations 
            WHERE email_address = ?
            ORDER BY created_at DESC 
            LIMIT 1
        ");
        $stmt->bind_param("s", $current_email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $student_data = $result->fetch_assoc();
            $data_source = 'registration_only';
        } else {
            // No data found in either table
            echo json_encode([
                'success' => false,
                'message' => 'Student record not found',
                'debug' => [
                    'email' => $current_email,
                    'checked_tables' => ['student_accounts', 'student_registrations']
                ]
            ]);
            exit;
        }
    }
    
    // Format name
    $firstName = ucwords(strtolower($student_data['first_name']));
    $middleName = ucwords(strtolower($student_data['middle_name'] ?? ''));
    $lastName = ucwords(strtolower($student_data['last_name']));
    $suffix = $student_data['suffix'] ?? '';
    
    $fullName = $firstName . ' ';
    if ($middleName) {
        $fullName .= $middleName . ' ';
    }
    $fullName .= $lastName;
    if ($suffix) {
        $fullName .= ' ' . $suffix;
    }
    
    // Determine if approved
    $isApproved = ($student_data['status'] === 'approved');
    
    echo json_encode([
        'success' => true,
        'data' => [
            'student_id' => $student_data['student_id'],
            'full_name' => trim($fullName),
            'first_name' => $firstName,
            'middle_name' => $middleName,
            'last_name' => $lastName,
            'suffix' => $suffix,
            'email' => $student_data['email_address'],
            'mobile' => $student_data['mobile_no'],
            'course' => $student_data['desired_course'],
            'status' => $student_data['status'],
            'registration_date' => $student_data['created_at'],
            'is_approved' => $isApproved,
            'data_source' => $data_source
        ]
    ]);
        
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage(),
        'debug' => [
            'email' => $current_email ?? 'unknown',
            'error' => $e->getMessage()
        ]
    ]);
}
?>
