<?php
error_reporting(0);
header('Content-Type: application/json');

try {
    // Database connection
    require_once 'config.php';
    
    // Check if database connection is available
    if (!isset($conn)) {
        echo json_encode(['error' => 'Database connection not available']);
        exit;
    }
    
    // Get student ID from request
    $student_id = isset($_GET['student_id']) ? trim($_GET['student_id']) : '';
    
    if (empty($student_id)) {
        echo json_encode(['error' => 'Student ID is required']);
        exit;
    }
    
    // Prepare SQL query to get student details
    $sql = "SELECT 
                student_id,
                first_name,
                last_name,
                middle_name,
                email_address,
                mobile_no,
                complete_address,
                desired_course,
                tertiary_school,
                status,
                created_at,
                updated_at
            FROM student_registrations 
            WHERE student_id = ?";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['error' => 'Database prepare error']);
        exit;
    }
    
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['error' => 'Student not found']);
        exit;
    }
    
    $student = $result->fetch_assoc();
    
    // Get enrollment information including section
    $enrollment_sql = "SELECT e.section, e.semester, e.school_year, e.year_level, s.section_code
                       FROM enrollments e 
                       LEFT JOIN sections s ON e.section_id = s.id
                       WHERE e.student_id = ? 
                       ORDER BY e.created_at DESC 
                       LIMIT 1";
    $enrollment_stmt = $conn->prepare($enrollment_sql);
    $enrollment_stmt->bind_param("s", $student_id);
    $enrollment_stmt->execute();
    $enrollment_result = $enrollment_stmt->get_result();
    
    $enrollment_info = null;
    if ($enrollment_result->num_rows > 0) {
        $enrollment_info = $enrollment_result->fetch_assoc();
    }
    
    // Get assessment data for the student
    $assessment_sql = "SELECT fee_type, amount FROM student_assessments WHERE student_id = ? ORDER BY fee_type";
    $assessment_stmt = $conn->prepare($assessment_sql);
    $assessment_stmt->bind_param("s", $student_id);
    $assessment_stmt->execute();
    $assessment_result = $assessment_stmt->get_result();
    
    $assessments = [];
    $total_assessment = 0;
    while ($assessment_row = $assessment_result->fetch_assoc()) {
        $assessments[] = $assessment_row;
        $total_assessment += $assessment_row['amount'];
    }
    
    // Get enrolled subjects for the student with their schedules
    $subjects_sql = "SELECT s.subject_code, s.subject_name, s.units, s.schedule
                     FROM enrolled_subjects es 
                     JOIN subjects s ON es.subject_id = s.id 
                     WHERE es.student_id = ? 
                     ORDER BY s.subject_code";
    $subjects_stmt = $conn->prepare($subjects_sql);
    $subjects_stmt->bind_param("s", $student_id);
    $subjects_stmt->execute();
    $subjects_result = $subjects_stmt->get_result();
    
    $enrolled_subjects = [];
    $total_units = 0;
    while ($subject_row = $subjects_result->fetch_assoc()) {
        // Parse the schedule format - expecting formats like "M,W 8:00AM-11:00AM Room 301"
        $schedule = $subject_row['schedule'] ?? 'TBA';
        
        // Try to extract days, time, and room from schedule
        if ($schedule && $schedule != 'TBA') {
            // Handle formats like "M,W 8:00AM-11:00AM Room 301"
            if (preg_match('/^([A-Z,\s]+)\s+([0-9:APM\s\-]+)(?:\s+Room\s+(\w+))?$/i', $schedule, $matches)) {
                $subject_row['days'] = trim($matches[1]);
                $subject_row['time_display'] = trim($matches[2]);
                $subject_row['room'] = isset($matches[3]) ? trim($matches[3]) : 'TBA';
            } else {
                $subject_row['days'] = $schedule;
                $subject_row['time_display'] = 'TBA';
                $subject_row['room'] = 'TBA';
            }
        } else {
            $subject_row['days'] = 'TBA';
            $subject_row['time_display'] = 'TBA';
            $subject_row['room'] = 'TBA';
        }
        
        // Set default type
        $subject_row['type'] = 'Lecture';
        
        $enrolled_subjects[] = $subject_row;
        $total_units += $subject_row['units'];
    }
    
    // Determine student type from ID
    $year = substr($student['student_id'], 0, 4);
    if ($year == '2025') {
        $studentType = 'New Student';
    } elseif ($year == '2024') {
        $studentType = 'Old Student';
    } else {
        $studentType = 'Transferee';
    }
    
    // Format the response
    $response = [
        'success' => true,
        'student' => [
            'id' => $student['student_id'],
            'first_name' => $student['first_name'],
            'last_name' => $student['last_name'],
            'middle_name' => $student['middle_name'] ?? '',
            'full_name' => trim($student['first_name'] . ' ' . ($student['middle_name'] ?? '') . ' ' . $student['last_name']),
            'email' => $student['email_address'],
            'phone_number' => $student['mobile_no'] ?? '',
            'address' => $student['complete_address'] ?? '',
            'type' => $studentType,
            'course' => $student['desired_course'],
            'previous_school' => $student['tertiary_school'] ?? '',
            'status' => $student['status'],
            'date_created' => $student['created_at'],
            'date_approved' => $student['updated_at'] ?? ''
        ],
        'enrollment' => $enrollment_info,
        'assessments' => $assessments,
        'total_assessment' => $total_assessment,
        'enrolled_subjects' => $enrolled_subjects,
        'total_units' => $total_units
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode(['error' => 'An error occurred while fetching student details']);
}
?>
