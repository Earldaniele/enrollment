<?php
session_start();
require_once '../../frontend/includes/db_config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    // Test database connection first
    if (!$conn->ping()) {
        throw new Exception('Database connection failed');
    }
    
    // Clear existing demo data from today
    $clear_stmt = $conn->prepare("DELETE FROM queue_tickets WHERE DATE(created_at) = CURDATE()");
    if (!$clear_stmt->execute()) {
        throw new Exception('Failed to clear existing data: ' . $conn->error);
    }
    
    // Get some approved students for demo
    $student_stmt = $conn->prepare("
        SELECT student_id, first_name, last_name 
        FROM student_registrations 
        WHERE status = 'approved' 
        ORDER BY RAND() 
        LIMIT 8
    ");
    
    if (!$student_stmt->execute()) {
        throw new Exception('Failed to query students: ' . $conn->error);
    }
    
    $result = $student_stmt->get_result();
    
    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
    
    if (empty($students)) {
        // Create demo students if none exist
        $demo_students = [
            ['2025-00001', 'Juan', 'Dela Cruz'],
            ['2025-00002', 'Maria', 'Santos'],
            ['2025-00003', 'Pedro', 'Reyes'],
            ['2025-00004', 'Ana', 'Garcia'],
            ['2025-00005', 'Luis', 'Torres'],
            ['2025-00006', 'Carmen', 'Lopez'],
            ['2025-00007', 'Roberto', 'Mendoza'],
            ['2025-00008', 'Isabel', 'Fernandez']
        ];
        
        foreach ($demo_students as $demo) {
            $insert_stmt = $conn->prepare("
                INSERT INTO student_registrations (student_id, first_name, last_name, email_address, mobile_no, status, student_type)
                VALUES (?, ?, ?, ?, ?, 'approved', 'New')
            ");
            
            if (!$insert_stmt) {
                throw new Exception('Failed to prepare student insert: ' . $conn->error);
            }
            
            $email = strtolower($demo[1]) . '.' . strtolower($demo[2]) . '@student.edu.ph';
            $mobile = '09' . rand(100000000, 999999999);
            $insert_stmt->bind_param("sssss", $demo[0], $demo[1], $demo[2], $email, $mobile);
            
            if (!$insert_stmt->execute()) {
                throw new Exception('Failed to insert demo student: ' . $conn->error);
            }
        }
        
        // Re-fetch students
        $student_stmt->execute();
        $result = $student_stmt->get_result();
        $students = [];
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
    }
    
    // Generate demo queue tickets
    $departments = ['Registrar', 'Treasury', 'Enrollment'];
    $statuses = ['waiting', 'ready', 'in_progress'];
    
    $insert_queue_stmt = $conn->prepare("
        INSERT INTO queue_tickets (student_id, department, queue_number, qr_data, status, created_at, expires_at)
        VALUES (?, ?, ?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 30 MINUTE))
    ");
    
    if (!$insert_queue_stmt) {
        throw new Exception('Failed to prepare queue insert: ' . $conn->error);
    }
    
    $generated_tickets = [];
    
    for ($i = 0; $i < count($students); $i++) {
        $student = $students[$i];
        $department = $departments[$i % count($departments)];
        $status = $statuses[$i % count($statuses)];
        
        // Generate queue number
        $dept_code = substr($department, 0, 2);
        $queue_num = $dept_code . '-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT);
        
        // Generate QR data
        $qr_data = json_encode([
            'queue_number' => $queue_num,
            'student_id' => $student['student_id'],
            'department' => strtolower($department),
            'timestamp' => time()
        ]);
        
        $insert_queue_stmt->bind_param("sssss", 
            $student['student_id'], 
            $department, 
            $queue_num, 
            $qr_data, 
            $status
        );
        
        if (!$insert_queue_stmt->execute()) {
            throw new Exception('Failed to insert queue ticket: ' . $conn->error);
        }
        
        $generated_tickets[] = [
            'student_id' => $student['student_id'],
            'student_name' => $student['first_name'] . ' ' . $student['last_name'],
            'department' => $department,
            'queue_number' => $queue_num,
            'status' => $status,
            'qr_data' => $qr_data
        ];
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Demo data generated successfully',
        'tickets_generated' => count($generated_tickets),
        'tickets' => $generated_tickets
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Error generating demo data: ' . $e->getMessage(),
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
