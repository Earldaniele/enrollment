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
            $student = $reg_result->fetch_assoc();
            $useRegistrationData = true;
        } else {
            // Only has account, use account data
            $student = [
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
            $useRegistrationData = false;
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
            $student = $result->fetch_assoc();
            $useRegistrationData = true;
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
        $firstName = ucwords(strtolower($student['first_name']));
        $middleName = ucwords(strtolower($student['middle_name']));
        $lastName = ucwords(strtolower($student['last_name']));
        $suffix = $student['suffix'];
        
        $fullName = $firstName . ' ';
        if ($middleName) {
            $fullName .= $middleName . ' ';
        }
        $fullName .= $lastName;
        if ($suffix) {
            $fullName .= ' ' . $suffix;
        }
        
        // Determine if approved
        $isApproved = ($student['status'] === 'approved');
        
        echo json_encode([
            'success' => true,
            'data' => [
                'student_id' => $student['student_id'],
                'full_name' => trim($fullName),
                'first_name' => $firstName,
                'middle_name' => $middleName,
                'last_name' => $lastName,
                'suffix' => $suffix,
                'email' => $student['email_address'],
                'mobile' => $student['mobile_no'],
                'course' => $student['desired_course'],
                'status' => $student['status'],
                'registration_date' => $student['created_at'],
                'is_approved' => $isApproved
            ]
        ]);
    } else {
        // Student not found - user needs to register
        echo json_encode([
            'success' => false,
            'message' => 'Student not found. Please complete your registration first.',
            'data' => [
                'student_id' => null,
                'full_name' => 'Not Registered',
                'email' => $current_email,
                'mobile' => 'N/A',
                'course' => 'Not registered',
                'status' => 'not_registered',
                'registration_date' => null,
                'is_approved' => false
            ]
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>
