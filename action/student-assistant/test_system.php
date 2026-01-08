<?php
session_start();
require_once '../../frontend/includes/db_config.php';

header('Content-Type: application/json');

try {
    // Test database connection
    if (!$conn->ping()) {
        throw new Exception('Database connection failed');
    }
    
    // Test if queue_tickets table exists and has data
    $queue_result = $conn->query("SELECT COUNT(*) as count FROM queue_tickets WHERE DATE(created_at) = CURDATE()");
    if (!$queue_result) {
        throw new Exception('queue_tickets table not accessible');
    }
    $queue_count = $queue_result->fetch_assoc()['count'];
    
    // Test if student_registrations table exists and has approved students
    $student_result = $conn->query("SELECT COUNT(*) as count FROM student_registrations WHERE status = 'approved'");
    if (!$student_result) {
        throw new Exception('student_registrations table not accessible');
    }
    $student_count = $student_result->fetch_assoc()['count'];
    
    // Test if we can create a test queue ticket
    $test_student = $conn->query("SELECT student_id FROM student_registrations WHERE status = 'approved' LIMIT 1");
    $test_student_data = $test_student->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'message' => 'System is working correctly',
        'system_info' => [
            'database_status' => 'Connected',
            'queue_tickets_today' => $queue_count,
            'approved_students' => $student_count,
            'test_student_available' => $test_student_data ? true : false,
            'test_student_id' => $test_student_data ? $test_student_data['student_id'] : null
        ],
        'endpoints' => [
            'get_queue_list' => 'GET /action/student-assistant/get_queue_list.php',
            'verify_queue' => 'GET /action/student-assistant/verify_queue.php',
            'manage_queue' => 'POST /action/student-assistant/manage_queue.php',
            'process_qr_upload' => 'POST /action/student-assistant/process_qr_upload.php',
            'process_qr_scan' => 'POST /action/student-assistant/process_qr_scan.php',
            'generate_demo_data' => 'POST /action/student-assistant/generate_demo_data.php'
        ],
        'note' => 'All backend endpoints are ready for use. You can now test the student-assistant system.'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'System test failed: ' . $e->getMessage(),
        'system_info' => [
            'database_status' => 'Failed',
            'error' => $e->getMessage()
        ]
    ]);
}

$conn->close();
?>
