<?php
require_once 'config.php';

// Get student document requirements
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_document_requirements') {
    validateRegistrarAuth();
    
    $studentId = $_GET['student_id'] ?? '';
    
    if (!$studentId) {
        sendResponse(false, 'Student ID is required');
    }
    
    // Get student info first
    $sql = "SELECT * FROM student_registrations WHERE student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        sendResponse(false, 'Student not found');
    }
    
    $student = $result->fetch_assoc();
    
    // Determine student type based on student ID and school
    $studentYear = substr($studentId, 0, 4);
    $studentType = '';
    
    if ($studentYear == '2025') {
        $studentType = 'new';
    } elseif ($student['tertiary_school'] && $student['tertiary_school'] !== 'NCST') {
        $studentType = 'transferee';
    } elseif ($studentYear == '2024') {
        $studentType = 'old';
    } else {
        $studentType = 'shifting';
    }
    
    // Define required documents based on student type
    $requiredDocuments = [];
    
    if ($studentType == 'new') {
        $requiredDocuments = [
            ['name' => 'Form 138 (Report Card)', 'required' => true, 'status' => 'submitted'],
            ['name' => 'PSA Birth Certificate', 'required' => true, 'status' => 'submitted'],
            ['name' => '2x2 ID Photo (2 copies)', 'required' => true, 'status' => 'submitted'],
            ['name' => 'Certificate of Good Moral Character', 'required' => true, 'status' => 'missing'],
            ['name' => 'Medical Certificate', 'required' => false, 'status' => 'pending']
        ];
    } elseif ($studentType == 'old') {
        $requiredDocuments = [
            ['name' => 'Registration Form', 'required' => true, 'status' => 'submitted'],
            ['name' => 'Clearance from Previous Semester', 'required' => true, 'status' => 'submitted'],
            ['name' => 'Copy of Grades from Previous Semester', 'required' => true, 'status' => 'submitted']
        ];
    } elseif ($studentType == 'shifting') {
        $requiredDocuments = [
            ['name' => 'Shifting Form', 'required' => true, 'status' => 'submitted'],
            ['name' => 'Department Clearance', 'required' => true, 'status' => 'pending'],
            ['name' => 'Grade Evaluation from Previous Course', 'required' => true, 'status' => 'submitted'],
            ['name' => 'Letter of Intent', 'required' => true, 'status' => 'submitted']
        ];
    } else { // transferee
        $requiredDocuments = [
            ['name' => 'Transcript of Records', 'required' => true, 'status' => 'submitted'],
            ['name' => 'Honorable Dismissal', 'required' => true, 'status' => 'submitted'],
            ['name' => 'Certificate of Good Moral Character', 'required' => true, 'status' => 'missing'],
            ['name' => 'PSA Birth Certificate', 'required' => true, 'status' => 'submitted'],
            ['name' => 'Transfer Credentials', 'required' => true, 'status' => 'submitted']
        ];
    }
    
    // Count completed documents
    $completedCount = 0;
    $totalRequired = 0;
    
    foreach ($requiredDocuments as &$doc) {
        if ($doc['required']) {
            $totalRequired++;
            if ($doc['status'] == 'submitted') {
                $completedCount++;
            }
        }
        
        // Add validation date
        if ($doc['status'] == 'submitted') {
            $doc['validated_at'] = date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days'));
            $doc['validated_by'] = 'Registrar Staff';
        }
    }
    
    $documentStatus = $completedCount == $totalRequired ? 'complete' : 'incomplete';
    $completionPercentage = $totalRequired > 0 ? round(($completedCount / $totalRequired) * 100) : 0;
    
    $data = [
        'student' => $student,
        'student_type' => $studentType,
        'documents' => $requiredDocuments,
        'summary' => [
            'total_required' => $totalRequired,
            'completed' => $completedCount,
            'status' => $documentStatus,
            'completion_percentage' => $completionPercentage
        ]
    ];
    
    sendResponse(true, 'Document requirements retrieved successfully', $data);
}

// Update document status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_document_status') {
    validateRegistrarAuth();
    
    $studentId = $_POST['student_id'] ?? '';
    $documentName = $_POST['document_name'] ?? '';
    $status = $_POST['status'] ?? '';
    $remarks = $_POST['remarks'] ?? '';
    
    if (!$studentId || !$documentName || !$status) {
        sendResponse(false, 'All fields are required');
    }
    
    // For now, we'll simulate updating the document status
    // In a real implementation, you would have a documents table
    
    // Create a log entry for document validation
    $logSql = "INSERT INTO registration_logs (registration_id, action, remarks, processed_by, created_at) 
               SELECT id, 'review', ?, ?, NOW() 
               FROM student_registrations 
               WHERE student_id = ?";
    
    $registrarInfo = validateRegistrarAuth();
    $logRemarks = "Document '$documentName' status updated to '$status'. Remarks: $remarks";
    
    $stmt = $conn->prepare($logSql);
    $stmt->bind_param("sss", $logRemarks, $registrarInfo['name'], $studentId);
    
    if ($stmt->execute()) {
        // Send notification to student about document status
        require_once '../includes/notification_helpers.php';
        
        // Get student email and name
        $studentSql = "SELECT email_address, CONCAT(first_name, ' ', last_name) as full_name FROM student_registrations WHERE student_id = ?";
        $studentStmt = $conn->prepare($studentSql);
        $studentStmt->bind_param("s", $studentId);
        $studentStmt->execute();
        $studentResult = $studentStmt->get_result();
        
        if ($studentData = $studentResult->fetch_assoc()) {
            $helper = getNotificationHelper();
            
            if (strtolower($status) === 'verified' || strtolower($status) === 'approved') {
                $helper->notifyStudentDocumentsVerified($studentData['email_address'], $studentData['full_name'], $documentName);
            } elseif (strtolower($status) === 'rejected' || strtolower($status) === 'missing') {
                $helper->notifyStudentDocumentIssue($studentData['email_address'], $studentData['full_name'], $documentName, $remarks ?: 'Document requires attention');
            }
        }
        
        sendResponse(true, 'Document status updated successfully');
    } else {
        sendResponse(false, 'Failed to update document status');
    }
}

// Approve all documents
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'approve_all_documents') {
    validateRegistrarAuth();
    
    $studentId = $_POST['student_id'] ?? '';
    
    if (!$studentId) {
        sendResponse(false, 'Student ID is required');
    }
    
    // Create a log entry for document approval
    $logSql = "INSERT INTO registration_logs (registration_id, action, remarks, processed_by, created_at) 
               SELECT id, 'approve', 'All documents approved by registrar', ?, NOW() 
               FROM student_registrations 
               WHERE student_id = ?";
    
    $registrarInfo = validateRegistrarAuth();
    
    $stmt = $conn->prepare($logSql);
    $stmt->bind_param("ss", $registrarInfo['name'], $studentId);
    
    if ($stmt->execute()) {
        sendResponse(true, 'All documents approved successfully');
    } else {
        sendResponse(false, 'Failed to approve documents');
    }
}

sendResponse(false, 'Invalid request');
?>
