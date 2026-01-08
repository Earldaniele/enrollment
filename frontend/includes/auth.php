<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Simple authentication check - in a real system, this would be more secure
// Check if user is logged in via session
function getCurrentUserEmail() {
    return $_SESSION['email'] ?? null;
}

// Function to get current user by email
function getCurrentUserFromDB($conn) {
    $email = getCurrentUserEmail();
    if (!$email) return null;
    
    $stmt = $conn->prepare("SELECT student_id FROM student_registrations WHERE email_address = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        return $user['student_id'];
    }
    
    return null;
}

// Function to set current user (for login)
function setCurrentUser($email) {
    // Include database connection to get user details
    require_once 'db_config.php';
    global $conn;
    
    // Get student details
    $stmt = $conn->prepare("SELECT id, first_name, last_name, phone FROM student_accounts WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['email'] = $email;
        $_SESSION['student_id'] = $user['id'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['phone'] = $user['phone'];
        $_SESSION['user_type'] = 'student';
        $_SESSION['login_time'] = time();
    } else {
        // Fallback if user not found
        $_SESSION['email'] = $email;
        $_SESSION['user_type'] = 'student';
        $_SESSION['login_time'] = time();
    }
}

// Function to logout
function logoutUser() {
    session_destroy();
}
?>
