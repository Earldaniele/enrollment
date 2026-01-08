<?php
error_reporting(0);
header('Content-Type: application/json');

try {
    // Database connection
    require_once 'config.php';
    
    // Handle POST requests (updating document status)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if (!$data) {
            echo json_encode(['success' => false, 'error' => 'Invalid JSON data']);
            exit;
        }
        
        $student_id = isset($data['student_id']) ? trim($data['student_id']) : '';
        $document_name = isset($data['document_name']) ? trim($data['document_name']) : '';
        $status = isset($data['status']) ? trim($data['status']) : '';
        
        if (empty($student_id) || empty($document_name) || empty($status)) {
            echo json_encode(['success' => false, 'error' => 'Missing required fields']);
            exit;
        }
        
        // Valid statuses
        $validStatuses = ['Missing', 'Submitted', 'Under Review', 'Approved', 'Rejected'];
        if (!in_array($status, $validStatuses)) {
            echo json_encode(['success' => false, 'error' => 'Invalid status']);
            exit;
        }
        
        // Update document status
        $updateSql = "UPDATE document_submissions 
                      SET submission_status = ?, 
                          submitted_date = CASE WHEN ? = 'Submitted' THEN NOW() ELSE submitted_date END,
                          updated_at = NOW()
                      WHERE student_id = ? AND document_name = ?";
        
        $updateStmt = $conn->prepare($updateSql);
        if (!$updateStmt) {
            echo json_encode(['success' => false, 'error' => 'Database prepare error']);
            exit;
        }
        
        $updateStmt->bind_param("ssss", $status, $status, $student_id, $document_name);
        
        if ($updateStmt->execute()) {
            if ($updateStmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'Document status updated successfully']);
            } else {
                echo json_encode(['success' => false, 'error' => 'No document found to update']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to update document status']);
        }
        
        $updateStmt->close();
        exit;
    }
    
    // Handle GET requests (fetching documents)
    
    // Get student ID from request
    $student_id = isset($_GET['student_id']) ? trim($_GET['student_id']) : '';
    
    if (empty($student_id)) {
        echo json_encode(['error' => 'Student ID is required']);
        exit;
    }
    
    // Get student info first
    $sql = "SELECT desired_course, student_type FROM student_registrations WHERE student_id = ?";
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
    $student_type = !empty($student['student_type']) ? $student['student_type'] : 'new';
    
    // Try to get actual document submission records (get latest status for each document)
    $documentSql = "SELECT DISTINCT document_name, submission_status, is_required
                    FROM document_submissions 
                    WHERE student_id = ?
                    ORDER BY document_name";
    
    $docStmt = $conn->prepare($documentSql);
    $requiredDocuments = [];
    
    if ($docStmt) {
        $docStmt->bind_param("s", $student_id);
        $docStmt->execute();
        $docResult = $docStmt->get_result();
        
        while ($row = $docResult->fetch_assoc()) {
            $requiredDocuments[] = [
                'name' => $row['document_name'],
                'required' => (bool)$row['is_required'],
                'status' => $row['submission_status'] ?: 'Missing'
            ];
        }
        $docStmt->close();
    }
    
    // If no document records found, use fallback based on student type
    if (empty($requiredDocuments)) {
        switch ($student_type) {
            case 'new':
                $requiredDocuments = [
                    ['name' => 'Form 138', 'required' => true, 'status' => 'Missing'],
                    ['name' => 'PSA Birth Certificate', 'required' => true, 'status' => 'Missing'],
                    ['name' => '2x2 ID Photo', 'required' => true, 'status' => 'Missing'],
                    ['name' => 'Certificate of Good Moral Character', 'required' => true, 'status' => 'Missing'],
                    ['name' => 'Medical Certificate', 'required' => false, 'status' => 'Missing']
                ];
                break;
                
            case 'transferee':
                $requiredDocuments = [
                    ['name' => 'Certificate of Transfer Credential', 'required' => true, 'status' => 'Missing'],
                    ['name' => 'Official Transcript of Records', 'required' => true, 'status' => 'Missing'],
                    ['name' => 'PSA Birth Certificate', 'required' => true, 'status' => 'Missing'],
                    ['name' => '2x2 ID Photo', 'required' => true, 'status' => 'Missing'],
                    ['name' => 'Certificate of Good Moral Character', 'required' => true, 'status' => 'Missing'],
                    ['name' => 'Medical Certificate', 'required' => false, 'status' => 'Missing']
                ];
                break;
                
            case 'shifting':
                $requiredDocuments = [
                    ['name' => 'Certificate of Good Moral Character', 'required' => true, 'status' => 'Missing'],
                    ['name' => '2x2 ID Photo', 'required' => true, 'status' => 'Missing'],
                    ['name' => 'Medical Certificate', 'required' => false, 'status' => 'Missing']
                ];
                break;
                
            case 'old':
                $requiredDocuments = [
                    ['name' => 'Certificate of Good Moral Character', 'required' => true, 'status' => 'Missing'],
                    ['name' => '2x2 ID Photo', 'required' => true, 'status' => 'Missing']
                ];
                break;
                
            default:
                $requiredDocuments = [
                    ['name' => '2x2 ID Photo', 'required' => true, 'status' => 'Missing']
                ];
        }
    }
    
    // Calculate summary
    $total_required = count(array_filter($requiredDocuments, function($doc) { return $doc['required']; }));
    $completed = count(array_filter($requiredDocuments, function($doc) { return $doc['status'] === 'Submitted'; }));
    
    $response = [
        'success' => true,
        'student_type' => $student_type,
        'documents' => $requiredDocuments,
        'summary' => [
            'total_required' => $total_required,
            'completed' => $completed,
            'pending' => $total_required - $completed
        ]
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode(['error' => 'An error occurred while fetching document requirements']);
}
?>
