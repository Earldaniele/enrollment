<?php
session_start();

/**
 * Require student assistant authentication
 * Redirects to login page if not authenticated
 */
function requireStudentAssistantAuth() {
    if (!isset($_SESSION['student_assistant_id']) || empty($_SESSION['student_assistant_id'])) {
        header('Location: ../auth/login.php');
        exit;
    }
}

/**
 * Get current student assistant information
 * @return array Student assistant information
 */
function getCurrentStudentAssistant() {
    if (isset($_SESSION['student_assistant_id']) && !empty($_SESSION['student_assistant_id'])) {
        return [
            'id' => $_SESSION['student_assistant_id'],
            'name' => $_SESSION['student_assistant_name'] ?? 'Student Assistant',
            'department' => $_SESSION['student_assistant_department'] ?? 'Main Office',
            'email' => $_SESSION['student_assistant_email'] ?? 'student.assistant@ncst.edu.ph'
        ];
    }
    return null;
}
?>
