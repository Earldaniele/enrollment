<?php
session_start();
require_once '../../frontend/includes/db_config.php';

header('Content-Type: application/json');

// Allow both GET and POST for testing
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    // Test database connection first
    if (!$conn->ping()) {
        throw new Exception('Database connection failed');
    }
    
    // Test if we can query the database
    $test_query = $conn->query("SELECT 1");
    if (!$test_query) {
        throw new Exception('Database query test failed');
    }
    
    // Check if tables exist
    $table_check = $conn->query("SHOW TABLES LIKE 'student_registrations'");
    if ($table_check->num_rows === 0) {
        throw new Exception('student_registrations table not found');
    }
    
    $table_check2 = $conn->query("SHOW TABLES LIKE 'queue_tickets'");
    if ($table_check2->num_rows === 0) {
        throw new Exception('queue_tickets table not found');
    }
    
    // Get count of approved students
    $student_count = $conn->query("SELECT COUNT(*) as count FROM student_registrations WHERE status = 'approved'");
    $approved_count = $student_count->fetch_assoc()['count'];
    
    if ($approved_count == 0) {
        // Create a simple demo student for testing
        $insert_demo = $conn->prepare("
            INSERT INTO student_registrations (student_id, first_name, last_name, email_address, mobile_no, status, student_type)
            VALUES (?, ?, ?, ?, ?, 'approved', 'New')
        ");
        
        $demo_student = ['2025-DEMO01', 'Demo', 'Student', 'demo.student@test.edu.ph', '09123456789'];
        $insert_demo->bind_param("sssss", $demo_student[0], $demo_student[1], $demo_student[2], $demo_student[3], $demo_student[4]);
        
        if (!$insert_demo->execute()) {
            throw new Exception('Failed to create demo student: ' . $conn->error);
        }
        
        $approved_count = 1;
    }
    
    // Create a simple demo queue ticket
    $insert_ticket = $conn->prepare("
        INSERT INTO queue_tickets (student_id, department, queue_number, qr_data, status, created_at, expires_at)
        VALUES (?, ?, ?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 30 MINUTE))
    ");
    
    $demo_ticket = [
        'student_id' => '2025-DEMO01',
        'department' => 'Registrar',
        'queue_number' => 'DEMO-001',
        'qr_data' => json_encode(['demo' => true]),
        'status' => 'waiting'
    ];
    
    $insert_ticket->bind_param("sssss", 
        $demo_ticket['student_id'],
        $demo_ticket['department'],
        $demo_ticket['queue_number'],
        $demo_ticket['qr_data'],
        $demo_ticket['status']
    );
    
    if (!$insert_ticket->execute()) {
        throw new Exception('Failed to create demo ticket: ' . $conn->error);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Debug demo data created successfully',
        'debug_info' => [
            'database_connected' => true,
            'tables_exist' => true,
            'approved_students' => $approved_count,
            'demo_ticket_created' => true,
            'ticket_id' => $conn->insert_id
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Debug error: ' . $e->getMessage(),
        'debug_info' => [
            'error_type' => get_class($e),
            'error_message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ]);
}

$conn->close();
?>
