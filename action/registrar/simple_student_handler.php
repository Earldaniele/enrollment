<?php
header('Content-Type: application/json');
error_reporting(0);

// Simple response function
function sendResponse($success, $message, $data = null) {
    $response = [
        'success' => $success,
        'message' => $message
    ];
    
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    echo json_encode($response);
    exit;
}

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'enrollment_db';

try {
    $conn = new mysqli($host, $username, $password, $database);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Get all students
    if ($_GET['action'] === 'get_all_students') {
        $type = $_GET['type'] ?? 'all';
        
        $sql = "SELECT 
                    student_id,
                    first_name,
                    last_name,
                    desired_course,
                    email_address,
                    mobile_no,
                    status as registration_status,
                    created_at
                FROM student_registrations 
                WHERE status = 'approved'";
        
        // Add type filtering
        if ($type === 'old') {
            $sql .= " AND student_id LIKE '2024-%'";
        } else if ($type === 'new') {
            $sql .= " AND student_id LIKE '2025-%'";
        } else if ($type === 'shifting') {
            $sql .= " AND student_type = 'Shifting'";
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        $result = $conn->query($sql);
        $students = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Determine student type based on ID
                if (strpos($row['student_id'], '2025-') !== false) {
                    $row['type'] = 'New Student';
                } else if (strpos($row['student_id'], '2024-') !== false) {
                    $row['type'] = 'Old Student';
                } else {
                    $row['type'] = 'Shifting Student';
                }
                
                $row['document_status'] = 'Complete';
                $row['full_name'] = $row['first_name'] . ' ' . $row['last_name'];
                $row['is_enrolled'] = true;
                
                $students[] = $row;
            }
        }
        
        sendResponse(true, 'Students retrieved successfully', $students);
    }
    
} catch (Exception $e) {
    sendResponse(false, 'Database error: ' . $e->getMessage());
}
?>
