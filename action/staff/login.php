<?php
// Include database connection
require_once '../../frontend/includes/db_config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set content type to JSON
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'redirect_url' => '',
    'staff_type' => '',
    'staff_id' => null,
    'staff_name' => ''
];

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get submitted data
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    // Validate inputs
    if (empty($email) || empty($password)) {
        $response['message'] = 'Email and password are required.';
        echo json_encode($response);
        exit;
    }
    
    try {
        // Use unified staff_accounts table for all staff login
        $query = "SELECT * FROM staff_accounts WHERE email = ? AND status = 'active' LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $staff = $result->fetch_assoc();
            
            // Verify password (using proper password_verify for hashed passwords)
            if (password_verify($password, $staff['password'])) {
                // Set session variables based on staff role
                $_SESSION['staff_id'] = $staff['id'];
                $_SESSION['staff_type'] = $staff['role'];
                $_SESSION['staff_name'] = $staff['name'];
                $_SESSION['staff_email'] = $staff['email'];
                
                // For backward compatibility with existing code
                if ($staff['role'] === 'evaluator') {
                    $_SESSION['evaluator_id'] = $staff['id'];
                }
                
                // Determine redirect URL based on role
                $redirect_map = [
                    'evaluator' => 'evaluator/index.php',
                    'registrar' => 'registrar/index.php',
                    'cashier' => 'cashier/index.php',
                    'student-assistant' => 'student-assistant/index.php',
                    'admin' => 'admin/index.php'
                ];
                
                $redirect_url = isset($redirect_map[$staff['role']]) ? $redirect_map[$staff['role']] : 'index.php';
                
                // Set response
                $response['success'] = true;
                $response['message'] = 'Login successful.';
                $response['redirect_url'] = $redirect_url;
                $response['staff_type'] = $staff['role'];
                $response['staff_id'] = $staff['id'];
                $response['staff_name'] = $staff['name'];
                
                echo json_encode($response);
                exit;
            }
        }
        
        // If we get here, no valid user was found
        $response['message'] = 'Invalid email or password.';
        echo json_encode($response);
        exit;
        
    } catch (Exception $e) {
        $response['message'] = 'Server error: ' . $e->getMessage();
        echo json_encode($response);
        exit;
    }
} else {
    $response['message'] = 'Invalid request method.';
    echo json_encode($response);
    exit;
}
