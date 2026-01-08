<?php
error_reporting(0); // Suppress all errors for clean JSON output
require_once 'config.php';

// Get available subjects for enrollment
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_available_subjects') {
    validateRegistrarAuth();
    
    $yearLevel = $_GET['year_level'] ?? '';
    $course = $_GET['course'] ?? '';
    $semester = $_GET['semester'] ?? '';
    
    // Debug logging
    error_log("Get subjects - year_level: '$yearLevel', course: '$course', semester: '$semester'");
    
    // Debug logging
    error_log("Get subjects - year_level: '$yearLevel', course: '$course', semester: '$semester'");
    
    $sql = "SELECT * FROM subjects 
            WHERE is_active = 1";
    
    $params = [];
    $types = "";
    
    if ($yearLevel) {
        $sql .= " AND year_level = ?";
        $params[] = $yearLevel;
        $types .= "s";
    }
    
    if ($course) {
        $sql .= " AND (course_code = ? OR course_code = 'ALL')";
        $params[] = $course;
        $types .= "s";
    }
    
    $sql .= " ORDER BY subject_code";
    
    error_log("Subjects SQL: $sql");
    error_log("Subjects params: " . json_encode($params));
    
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    $subjects = [];
    while ($row = $result->fetch_assoc()) {
        $subjects[] = $row;
    }
    
    error_log("Found " . count($subjects) . " subjects");
    
    sendResponse(true, 'Subjects retrieved successfully', $subjects);
    exit;
}

// Get available sections for enrollment
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_sections') {
    validateRegistrarAuth();
    
    $yearLevel = $_GET['year_level'] ?? '';
    $course = $_GET['course'] ?? '';
    
    // Debug logging
    error_log("Get sections - year_level: '$yearLevel', course: '$course'");
    
    $sql = "SELECT id, section_code, course, year_level, max_students, created_at 
            FROM sections 
            WHERE 1=1";
    
    $params = [];
    $types = "";
    
    if ($yearLevel) {
        $sql .= " AND year_level = ?";
        $params[] = $yearLevel;
        $types .= "s";
    }
    
    if ($course) {
        $sql .= " AND course = ?";
        $params[] = $course;
        $types .= "s";
    }
    
    $sql .= " ORDER BY section_code";
    
    error_log("Final SQL: $sql");
    error_log("Params: " . json_encode($params));
    
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    $sections = [];
    while ($row = $result->fetch_assoc()) {
        $sections[] = $row;
    }
    
    error_log("Found " . count($sections) . " sections");
    
    sendResponse(true, 'Sections retrieved successfully', $sections);
    exit;
}

// Get registrar dashboard statistics
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_dashboard_stats') {
    validateRegistrarAuth();
    
    try {
        $academicPeriod = getCurrentAcademicPeriod();
    
    // Count students by type using the student_type column
    $newStudentsSql = "SELECT COUNT(*) as count FROM student_registrations WHERE status = 'approved' AND student_type = 'New'";
    $newStudentsResult = $conn->query($newStudentsSql);
    $newStudentsCount = $newStudentsResult->fetch_assoc()['count'];
    
    $oldStudentsSql = "SELECT COUNT(*) as count FROM student_registrations WHERE status = 'approved' AND student_type = 'Old'";
    $oldStudentsResult = $conn->query($oldStudentsSql);
    $oldStudentsCount = $oldStudentsResult->fetch_assoc()['count'];
    
    $shiftingStudentsSql = "SELECT COUNT(*) as count FROM student_registrations WHERE status = 'approved' AND student_type = 'Shifting'";
    $shiftingStudentsResult = $conn->query($shiftingStudentsSql);
    $shiftingStudentsCount = $shiftingStudentsResult->fetch_assoc()['count'];
    
    $transfereeStudentsSql = "SELECT COUNT(*) as count FROM student_registrations WHERE status = 'approved' AND student_type = 'Transferee'";
    $transfereeStudentsResult = $conn->query($transfereeStudentsSql);
    $transfereeStudentsCount = $transfereeStudentsResult->fetch_assoc()['count'];
    
    // Count enrolled students
    $enrolledSql = "SELECT COUNT(*) as count FROM enrollments WHERE school_year = ? AND semester = ?";
    $enrolledStmt = $conn->prepare($enrolledSql);
    $enrolledStmt->bind_param("ss", $academicPeriod['school_year'], $academicPeriod['semester']);
    $enrolledStmt->execute();
    $enrolledCount = $enrolledStmt->get_result()->fetch_assoc()['count'];
    
    // Count pending enrollments (approved registrations not yet enrolled)
    $pendingSql = "SELECT COUNT(*) as count 
                   FROM student_registrations sr
                   LEFT JOIN enrollments e ON sr.student_id = e.student_id AND e.school_year = ? AND e.semester = ?
                   WHERE sr.status = 'approved' AND e.id IS NULL";
    $pendingStmt = $conn->prepare($pendingSql);
    $pendingStmt->bind_param("ss", $academicPeriod['school_year'], $academicPeriod['semester']);
    $pendingStmt->execute();
    $pendingCount = $pendingStmt->get_result()->fetch_assoc()['count'];
    
    // Recent activities
    $recentSql = "SELECT 
                      sr.student_id,
                      sr.first_name,
                      sr.last_name,
                      'Registration Approved' as activity,
                      sr.updated_at as activity_date
                  FROM student_registrations sr
                  WHERE sr.status = 'approved'
                  ORDER BY sr.updated_at DESC
                  LIMIT 5";
    
    $recentResult = $conn->query($recentSql);
    $recentActivities = [];
    
    while ($row = $recentResult->fetch_assoc()) {
        $row['full_name'] = $row['first_name'] . ' ' . $row['last_name'];
        $recentActivities[] = $row;
    }
    
    // Document status counts - use actual document submissions
    $submittedDocsSql = "SELECT COUNT(DISTINCT student_id) as count FROM document_submissions WHERE submission_status = 'Submitted'";
    $submittedDocsResult = $conn->query($submittedDocsSql);
    $completeDocsCount = $submittedDocsResult->fetch_assoc()['count'];
    
    $missingDocsSql = "SELECT COUNT(DISTINCT student_id) as count FROM document_submissions WHERE submission_status = 'Missing'";
    $missingDocsResult = $conn->query($missingDocsSql);
    $incompleteDocsCount = $missingDocsResult->fetch_assoc()['count'];
    
    // For pending validation and rejected, check if there are any (currently 0 based on data)
    $pendingValidationCount = 0; // No 'Under Review' status in current data
    $rejectedDocsCount = 0; // No 'Rejected' status in current data

    $stats = [
        'student_types' => [
            'new' => $newStudentsCount,
            'old' => $oldStudentsCount,
            'shifting' => $shiftingStudentsCount,
            'transferee' => $transfereeStudentsCount
        ],
        'enrollment_status' => [
            'enrolled' => $enrolledCount,
            'pending' => $pendingCount
        ],
        'document_status' => [
            'complete' => $completeDocsCount,
            'incomplete' => $incompleteDocsCount,
            'pending' => $pendingValidationCount,
            'rejected' => $rejectedDocsCount
        ],
        'totals' => [
            'total_approved' => $newStudentsCount + $oldStudentsCount + $shiftingStudentsCount + $transfereeStudentsCount,
            'total_enrolled' => $enrolledCount
        ],
        'recent_activities' => $recentActivities,
        'academic_period' => $academicPeriod
    ];
    
    sendResponse(true, 'Dashboard statistics retrieved successfully', $stats);
    exit;
    
    } catch (Exception $e) {
        sendResponse(false, 'Error retrieving dashboard statistics: ' . $e->getMessage());
    }
}

// Get enrollment form data
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_enrollment_form_data') {
    validateRegistrarAuth();
    
    $studentId = $_GET['student_id'] ?? '';
    
    if (!$studentId) {
        sendResponse(false, 'Student ID is required');
    }
    
    // Get student info
    $studentSql = "SELECT * FROM student_registrations WHERE student_id = ?";
    $studentStmt = $conn->prepare($studentSql);
    $studentStmt->bind_param("s", $studentId);
    $studentStmt->execute();
    $studentResult = $studentStmt->get_result();
    
    if ($studentResult->num_rows === 0) {
        sendResponse(false, 'Student not found');
    }
    
    $student = $studentResult->fetch_assoc();
    
    // Determine suggested year level based on student type
    $studentYear = substr($studentId, 0, 4);
    $suggestedYearLevel = '';
    
    if ($studentYear == '2025') {
        $suggestedYearLevel = '1st Year';
    } elseif ($studentYear == '2024') {
        $suggestedYearLevel = '2nd Year';
    } else {
        $suggestedYearLevel = '3rd Year';
    }
    
    // Get course code from desired course
    $courseCode = 'BSIT'; // Default
    if (strpos($student['desired_course'], 'BSIT') !== false) {
        $courseCode = 'BSIT';
    } elseif (strpos($student['desired_course'], 'BSCS') !== false) {
        $courseCode = 'BSCS';
    } elseif (strpos($student['desired_course'], 'BSIS') !== false) {
        $courseCode = 'BSIS';
    }
    
    // Get available subjects
    $academicPeriod = getCurrentAcademicPeriod();
    $subjectsSql = "SELECT * FROM subjects 
                    WHERE is_active = 1 
                    AND semester = ? 
                    AND year_level = ?
                    AND (course_code = ? OR course_code = 'ALL')
                    ORDER BY subject_code";
    
    $subjectsStmt = $conn->prepare($subjectsSql);
    $subjectsStmt->bind_param("sss", $academicPeriod['semester'], $suggestedYearLevel, $courseCode);
    $subjectsStmt->execute();
    $subjectsResult = $subjectsStmt->get_result();
    
    $subjects = [];
    while ($row = $subjectsResult->fetch_assoc()) {
        $subjects[] = $row;
    }
    
    $data = [
        'student' => $student,
        'suggested_year_level' => $suggestedYearLevel,
        'course_code' => $courseCode,
        'subjects' => $subjects,
        'academic_period' => $academicPeriod
    ];
    
    sendResponse(true, 'Enrollment form data retrieved successfully', $data);
    exit; // Exit after successful response
}

// Get student assessment
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_assessment') {
    validateRegistrarAuth();
    
    $studentId = $_GET['student_id'] ?? '';
    
    if (!$studentId) {
        sendResponse(false, 'Student ID is required');
    }
    
    $sql = "SELECT SUM(amount) as total_assessment FROM student_assessments WHERE student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $assessment = $result->fetch_assoc();
        sendResponse(true, 'Assessment retrieved successfully', $assessment);
    } else {
        sendResponse(true, 'No assessment found', ['total_assessment' => 0]);
    }
    exit;
}

// If no valid action is found, return error
sendResponse(false, 'Invalid request');
?>
