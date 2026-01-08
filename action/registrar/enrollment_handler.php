<?php
require_once 'config.php';

// Handle JSON enrollment request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    if (!empty($input)) {
        $data = json_decode($input, true);
        if ($data !== null && isset($data['student_id'])) {
            // This is a JSON enrollment request
            validateRegistrarAuth();
            
            $studentId = $data['student_id'] ?? '';
            $yearLevel = $data['year_level'] ?? '';
            $semester = $data['semester'] ?? '';
            $schoolYear = $data['school_year'] ?? '';
            $sectionId = $data['section_id'] ?? '';
            $subjects = $data['subjects'] ?? [];
            
            if (!$studentId || !$yearLevel || !$semester || !$schoolYear || !$sectionId || empty($subjects)) {
                sendResponse(false, 'All fields are required (student, year level, semester, school year, section, and subjects)');
            }
            
            // Extract subject IDs from subjects array
            $subjectIds = array_column($subjects, 'id');
            
            $conn->begin_transaction();
            
            try {
                // Check if student is already enrolled for this semester and school year
                $checkSql = "SELECT id FROM enrollments WHERE student_id = ? AND school_year = ? AND semester = ?";
                $checkStmt = $conn->prepare($checkSql);
                $checkStmt->bind_param("sss", $studentId, $schoolYear, $semester);
                $checkStmt->execute();
                
                if ($checkStmt->get_result()->num_rows > 0) {
                    throw new Exception('Student is already enrolled for this semester and school year');
                }
                
                // Calculate total units and tuition based on subject type
                $totalUnits = 0;
                $majorUnits = 0;
                $geUnits = 0;
                
                // Different tuition rates per unit based on subject type
                $MAJOR_TUITION_PER_UNIT = 1000.00; // IT, CS, and other major subjects
                $GE_TUITION_PER_UNIT = 800.00;     // GE, PE, NSTP subjects
                
                foreach ($subjects as $subject) {
                    $units = floatval($subject['units'] ?? 0);
                    $subjectCode = strtoupper($subject['subject_code'] ?? '');
                    $totalUnits += $units;
                    
                    // Check if it's a GE/PE/NSTP subject
                    if (strpos($subjectCode, 'GE') === 0 || strpos($subjectCode, 'PE') === 0 || strpos($subjectCode, 'NSTP') === 0) {
                        $geUnits += $units;
                    } else {
                        $majorUnits += $units;
                    }
                }
                
                // Calculate tuition fee based on subject types
                $majorTuition = $majorUnits * $MAJOR_TUITION_PER_UNIT;
                $geTuition = $geUnits * $GE_TUITION_PER_UNIT;
                $tuitionFee = $majorTuition + $geTuition;
                
                // Fixed fees structure
                $fixedFees = [
                    'Laboratory Fee' => 2000.00,
                    'Miscellaneous' => 3000.00,
                    'LMS' => 500.00,
                    'NSTP/ROTC' => 700.00,
                    'OMR' => 300.00
                ];
                
                // Delete existing assessments for this student
                $deleteAssessmentSql = "DELETE FROM student_assessments WHERE student_id = ?";
                $deleteAssessmentStmt = $conn->prepare($deleteAssessmentSql);
                $deleteAssessmentStmt->bind_param("s", $studentId);
                $deleteAssessmentStmt->execute();
                
                // Insert new assessment records
                $totalAssessment = $tuitionFee;
                
                // Insert tuition fee
                $insertTuitionSql = "INSERT INTO student_assessments (student_id, fee_type, amount, school_year, semester) VALUES (?, 'Tuition Fee', ?, ?, ?)";
                $insertTuitionStmt = $conn->prepare($insertTuitionSql);
                $insertTuitionStmt->bind_param("sdss", $studentId, $tuitionFee, $schoolYear, $semester);
                $insertTuitionStmt->execute();
                
                // Insert fixed fees
                foreach ($fixedFees as $feeType => $amount) {
                    $insertFeeSql = "INSERT INTO student_assessments (student_id, fee_type, amount, school_year, semester) VALUES (?, ?, ?, ?, ?)";
                    $insertFeeStmt = $conn->prepare($insertFeeSql);
                    $insertFeeStmt->bind_param("ssdss", $studentId, $feeType, $amount, $schoolYear, $semester);
                    $insertFeeStmt->execute();
                    $totalAssessment += $amount;
                }
                
                // Get section code from section_id
                $sectionSql = "SELECT section_code FROM sections WHERE id = ?";
                $sectionStmt = $conn->prepare($sectionSql);
                $sectionStmt->bind_param("i", $sectionId);
                $sectionStmt->execute();
                $sectionResult = $sectionStmt->get_result()->fetch_assoc();
                $sectionName = $sectionResult['section_code'] ?? '';
                
                // Create enrollment record
                $enrollSql = "INSERT INTO enrollments (student_id, section, section_id, year_level, semester, school_year, total_units, total_assessment, enrollment_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'enrolled')";
                $enrollStmt = $conn->prepare($enrollSql);
                $enrollStmt->bind_param("ssisssdd", $studentId, $sectionName, $sectionId, $yearLevel, $semester, $schoolYear, $totalUnits, $totalAssessment);
                $enrollStmt->execute();
                
                // Enroll subjects
                foreach ($subjectIds as $subjectId) {
                    $subjectEnrollSql = "INSERT INTO enrolled_subjects (student_id, subject_id) VALUES (?, ?)";
                    $subjectEnrollStmt = $conn->prepare($subjectEnrollSql);
                    $subjectEnrollStmt->bind_param("si", $studentId, $subjectId);
                    $subjectEnrollStmt->execute();
                }
                
                $conn->commit();
                sendResponse(true, 'Student enrolled successfully');
                
            } catch (Exception $e) {
                $conn->rollback();
                sendResponse(false, 'Enrollment failed: ' . $e->getMessage());
            }
            
            exit; // Stop further processing
        }
    }
}

// Get students for enrollment
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_students_for_enrollment') {
    validateRegistrarAuth();
    
    $studentType = $_GET['type'] ?? 'all';
    $paymentStatus = $_GET['payment_status'] ?? 'all';
    $search = $_GET['search'] ?? '';
    
    $sql = "SELECT 
                sr.student_id,
                sr.first_name,
                sr.last_name,
                sr.desired_course,
                sr.email_address,
                sr.status as registration_status,
                COALESCE((SELECT SUM(amount) FROM student_assessments WHERE student_id = sr.student_id), 0) as total_assessment,
                COALESCE((SELECT SUM(amount) FROM student_payments WHERE student_id = sr.student_id AND status = 'completed'), 0) as total_paid,
                (COALESCE((SELECT SUM(amount) FROM student_assessments WHERE student_id = sr.student_id), 0) - COALESCE((SELECT SUM(amount) FROM student_payments WHERE student_id = sr.student_id AND status = 'completed'), 0)) as balance,
                CASE 
                    WHEN COALESCE((SELECT SUM(amount) FROM student_payments WHERE student_id = sr.student_id AND status = 'completed'), 0) >= COALESCE((SELECT SUM(amount) FROM student_assessments WHERE student_id = sr.student_id), 0) THEN 'fully_paid'
                    WHEN COALESCE((SELECT SUM(amount) FROM student_payments WHERE student_id = sr.student_id AND status = 'completed'), 0) > 0 THEN 'has_balance'
                    ELSE 'unpaid'
                END as payment_status,
                sr.created_at,
                e.enrollment_status
            FROM student_registrations sr
            LEFT JOIN enrollments e ON sr.student_id = e.student_id AND e.school_year = '2024-2025' AND e.semester = '1st Semester'
            WHERE sr.status = 'approved'";
    
    // Add filters
    if ($studentType !== 'all') {
        $year = substr($studentType === 'new' ? '2025' : '2024', 0, 4);
        if ($studentType === 'new') {
            $sql .= " AND sr.student_id LIKE '2025-%'";
        } elseif ($studentType === 'old') {
            $sql .= " AND sr.student_id LIKE '2024-%' AND sr.tertiary_school = 'NCST'";
        } elseif ($studentType === 'shifting') {
            $sql .= " AND sr.student_id LIKE '2024-%' AND sr.tertiary_school != 'NCST'";
        } elseif ($studentType === 'transferee') {
            $sql .= " AND sr.student_id LIKE '202%' AND sr.tertiary_school IS NOT NULL AND sr.tertiary_school != 'NCST'";
        }
    }
    
    if ($search) {
        $search = $conn->real_escape_string($search);
        $sql .= " AND (sr.student_id LIKE '%$search%' OR sr.first_name LIKE '%$search%' OR sr.last_name LIKE '%$search%')";
    }
    
    $sql .= " GROUP BY sr.student_id
              HAVING 1=1";
    
    if ($paymentStatus === 'fully_paid') {
        $sql .= " AND payment_status = 'fully_paid'";
    } elseif ($paymentStatus === 'has_balance') {
        $sql .= " AND payment_status = 'has_balance'";
    }
    
    $sql .= " ORDER BY sr.created_at DESC";
    
    $result = $conn->query($sql);
    $students = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Determine student type from ID
            $studentYear = substr($row['student_id'], 0, 4);
            if ($studentYear == '2025') {
                $row['type'] = 'New Student';
            } elseif ($row['tertiary_school'] && $row['tertiary_school'] !== 'NCST') {
                $row['type'] = 'Transferee';
            } else {
                $row['type'] = 'Old Student';
            }
            
            $row['full_name'] = $row['first_name'] . ' ' . $row['last_name'];
            $row['is_enrolled'] = !empty($row['enrollment_status']);
            $students[] = $row;
        }
    }
    
    sendResponse(true, 'Students retrieved successfully', $students);
}

// Get student details for enrollment
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_student_details') {
    validateRegistrarAuth();
    
    $studentId = $_GET['student_id'] ?? '';
    
    if (!$studentId) {
        sendResponse(false, 'Student ID is required');
    }
    
    $sql = "SELECT 
                sr.*,
                COALESCE((SELECT SUM(amount) FROM student_assessments WHERE student_id = sr.student_id), 0) as total_assessment,
                COALESCE((SELECT SUM(amount) FROM student_payments WHERE student_id = sr.student_id AND status = 'completed'), 0) as total_paid,
                (COALESCE((SELECT SUM(amount) FROM student_assessments WHERE student_id = sr.student_id), 0) - COALESCE((SELECT SUM(amount) FROM student_payments WHERE student_id = sr.student_id AND status = 'completed'), 0)) as balance,
                e.enrollment_status,
                e.total_units
            FROM student_registrations sr
            LEFT JOIN enrollments e ON sr.student_id = e.student_id
            WHERE sr.student_id = ?
            LIMIT 1";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        sendResponse(false, 'Student not found');
    }
    
    $student = $result->fetch_assoc();
    
    // Get assessment details
    $assessmentSql = "SELECT * FROM student_assessments WHERE student_id = ? ORDER BY created_at";
    $assessmentStmt = $conn->prepare($assessmentSql);
    $assessmentStmt->bind_param("s", $studentId);
    $assessmentStmt->execute();
    $assessmentResult = $assessmentStmt->get_result();
    
    $assessments = [];
    while ($row = $assessmentResult->fetch_assoc()) {
        $assessments[] = $row;
    }
    
    // Get payment history
    $paymentSql = "SELECT * FROM student_payments WHERE student_id = ? ORDER BY created_at DESC";
    $paymentStmt = $conn->prepare($paymentSql);
    $paymentStmt->bind_param("s", $studentId);
    $paymentStmt->execute();
    $paymentResult = $paymentStmt->get_result();
    
    $payments = [];
    while ($row = $paymentResult->fetch_assoc()) {
        $payments[] = $row;
    }
    
    $student['assessments'] = $assessments;
    $student['payments'] = $payments;
    
    sendResponse(true, 'Student details retrieved successfully', $student);
}

// Enroll student
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'enroll_student') {
    validateRegistrarAuth();
    
    $studentId = $_POST['student_id'] ?? '';
    $yearLevel = $_POST['year_level'] ?? '';
    $sectionId = $_POST['section_id'] ?? '';
    $subjectIds = json_decode($_POST['subject_ids'] ?? '[]', true);
    
    if (!$studentId || !$yearLevel || !$sectionId || empty($subjectIds)) {
        sendResponse(false, 'All fields are required (student, year level, section, and subjects)');
    }
    
    $conn->begin_transaction();
    
    try {
        $academicPeriod = getCurrentAcademicPeriod();
        
        // Check if student is already enrolled for this semester
        $checkSql = "SELECT id FROM enrollments WHERE student_id = ? AND school_year = ? AND semester = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("sss", $studentId, $academicPeriod['school_year'], $academicPeriod['semester']);
        $checkStmt->execute();
        
        if ($checkStmt->get_result()->num_rows > 0) {
            throw new Exception('Student is already enrolled for this semester');
        }
        
        // Calculate total units and assessment
        $subjectIdList = implode(',', array_map('intval', $subjectIds));
        $subjectSql = "SELECT SUM(units) as total_units FROM subjects WHERE id IN ($subjectIdList)";
        $subjectResult = $conn->query($subjectSql);
        $totalUnits = $subjectResult->fetch_assoc()['total_units'];
        
        // Get total assessment
        $assessmentSql = "SELECT SUM(amount) as total_assessment FROM student_assessments WHERE student_id = ?";
        $assessmentStmt = $conn->prepare($assessmentSql);
        $assessmentStmt->bind_param("s", $studentId);
        $assessmentStmt->execute();
        $totalAssessment = $assessmentStmt->get_result()->fetch_assoc()['total_assessment'];
        
        // Get section code from section_id
        $sectionSql = "SELECT section_code FROM sections WHERE id = ?";
        $sectionStmt = $conn->prepare($sectionSql);
        $sectionStmt->bind_param("i", $sectionId);
        $sectionStmt->execute();
        $sectionResult = $sectionStmt->get_result()->fetch_assoc();
        $sectionName = $sectionResult['section_code'] ?? '';
        
        // Create enrollment record
        $enrollSql = "INSERT INTO enrollments (student_id, section, section_id, year_level, semester, school_year, total_units, total_assessment, enrollment_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'enrolled')";
        $enrollStmt = $conn->prepare($enrollSql);
        $enrollStmt->bind_param("ssisssdd", $studentId, $sectionName, $sectionId, $yearLevel, $academicPeriod['semester'], $academicPeriod['school_year'], $totalUnits, $totalAssessment);
        $enrollStmt->execute();
        
        // Enroll subjects
        foreach ($subjectIds as $subjectId) {
            $subjectEnrollSql = "INSERT INTO enrolled_subjects (student_id, subject_id) VALUES (?, ?)";
            $subjectEnrollStmt = $conn->prepare($subjectEnrollSql);
            $subjectEnrollStmt->bind_param("si", $studentId, $subjectId);
            $subjectEnrollStmt->execute();
        }
        
        $conn->commit();
        sendResponse(true, 'Student enrolled successfully');
        
    } catch (Exception $e) {
        $conn->rollback();
        sendResponse(false, 'Enrollment failed: ' . $e->getMessage());
    }
}

// Simple enrollment endpoint
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'simple_enroll') {
    try {
        validateRegistrarAuth();
        
        $studentId = $_POST['student_id'] ?? '';
        
        if (empty($studentId)) {
            sendResponse(false, 'Student ID is required');
        }
        
        // Check if student exists and is approved
        $checkSql = "SELECT student_id, status FROM student_registrations WHERE student_id = ? AND status = 'approved'";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("s", $studentId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        if ($checkResult->num_rows === 0) {
            sendResponse(false, 'Student not found or not approved');
        }
        
        // Check if already enrolled
        $enrolledSql = "SELECT id FROM enrollments WHERE student_id = ? AND school_year = '2024-2025' AND semester = '1st Semester'";
        $enrolledStmt = $conn->prepare($enrolledSql);
        $enrolledStmt->bind_param("s", $studentId);
        $enrolledStmt->execute();
        $enrolledResult = $enrolledStmt->get_result();
        
        if ($enrolledResult->num_rows > 0) {
            sendResponse(false, 'Student is already enrolled');
        }
        
        // Create enrollment record
        $enrollSql = "INSERT INTO enrollments (student_id, year_level, semester, school_year, enrollment_status) VALUES (?, '1st Year', '1st Semester', '2024-2025', 'enrolled')";
        $enrollStmt = $conn->prepare($enrollSql);
        $enrollStmt->bind_param("s", $studentId);
        
        if ($enrollStmt->execute()) {
            sendResponse(true, 'Student enrolled successfully');
        } else {
            sendResponse(false, 'Failed to enroll student');
        }
        
    } catch (Exception $e) {
        sendResponse(false, 'Enrollment failed: ' . $e->getMessage());
    }
}

sendResponse(false, 'Invalid request');
?>
