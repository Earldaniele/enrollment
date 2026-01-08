<?php
session_start();
require_once '../../frontend/includes/db_config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_GET['student_id'])) {
    echo json_encode(['success' => false, 'message' => 'Student ID is required']);
    exit;
}

$student_id = $_GET['student_id'];

try {
    // Get student enrollment information
    $stmt = $conn->prepare("
        SELECT 
            sr.student_id,
            sr.first_name,
            sr.middle_name,
            sr.last_name,
            sr.suffix,
            sr.desired_course,
            e.year_level,
            e.semester,
            e.school_year,
            e.enrollment_status,
            e.total_units,
            e.total_assessment,
            e.payment_status
        FROM student_registrations sr
        LEFT JOIN enrollments e ON sr.student_id = e.student_id
        WHERE sr.student_id = ?
    ");
    
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Student not found']);
        exit;
    }
    
    $student_data = $result->fetch_assoc();
    
    // Get enrolled subjects
    $subjects_stmt = $conn->prepare("
        SELECT 
            s.subject_code,
            s.subject_name,
            s.units,
            s.schedule,
            es.grade
        FROM enrolled_subjects es
        JOIN subjects s ON es.subject_id = s.id
        WHERE es.student_id = ?
        ORDER BY s.subject_code
    ");
    
    $subjects_stmt->bind_param("s", $student_id);
    $subjects_stmt->execute();
    $subjects_result = $subjects_stmt->get_result();
    
    $subjects = [];
    while ($subject = $subjects_result->fetch_assoc()) {
        $subjects[] = $subject;
    }
    
    // Get assessment details
    $assessment_stmt = $conn->prepare("
        SELECT 
            fee_type,
            amount
        FROM student_assessments
        WHERE student_id = ?
        ORDER BY id
    ");
    
    $assessment_stmt->bind_param("s", $student_id);
    $assessment_stmt->execute();
    $assessment_result = $assessment_stmt->get_result();
    
    $assessments = [];
    $total_assessment = 0;
    while ($assessment = $assessment_result->fetch_assoc()) {
        $assessments[] = $assessment;
        $total_assessment += $assessment['amount'];
    }
    
    // Get payment information
    $payment_stmt = $conn->prepare("
        SELECT 
            SUM(amount) as total_paid
        FROM student_payments
        WHERE student_id = ? AND status = 'completed'
    ");
    
    $payment_stmt->bind_param("s", $student_id);
    $payment_stmt->execute();
    $payment_result = $payment_stmt->get_result();
    $payment_data = $payment_result->fetch_assoc();
    
    $total_paid = $payment_data['total_paid'] ?? 0;
    $remaining_balance = $total_assessment - $total_paid;
    
    // Format full name
    $full_name = trim($student_data['first_name'] . ' ' . 
                     ($student_data['middle_name'] ? $student_data['middle_name'] . ' ' : '') . 
                     $student_data['last_name'] . 
                     ($student_data['suffix'] ? ' ' . $student_data['suffix'] : ''));
    
    echo json_encode([
        'success' => true,
        'data' => [
            'student_info' => [
                'student_id' => $student_data['student_id'],
                'full_name' => $full_name,
                'course' => $student_data['desired_course'],
                'year_level' => $student_data['year_level'] ?? 'Not enrolled',
                'semester' => $student_data['semester'] ?? 'N/A',
                'school_year' => $student_data['school_year'] ?? date('Y') . '-' . (date('Y') + 1),
                'enrollment_status' => $student_data['enrollment_status'] ?? 'Not enrolled'
            ],
            'subjects' => $subjects,
            'assessment' => [
                'fees' => $assessments,
                'total_assessment' => $total_assessment,
                'total_paid' => $total_paid,
                'remaining_balance' => $remaining_balance,
                'payment_status' => $student_data['payment_status'] ?? 'Unpaid'
            ]
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>
