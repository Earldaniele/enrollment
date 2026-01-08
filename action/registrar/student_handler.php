<?php
error_reporting(0); // Suppress all errors for clean JSON output
require_once 'config.php';

// Get all students
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_all_students') {
    validateRegistrarAuth();
    
    try {
        $studentType = $_GET['type'] ?? 'all';
        $search = $_GET['search'] ?? '';
        
        $baseSql = "SELECT 
                    sr.student_id,
                    sr.first_name,
                    sr.last_name,
                    sr.desired_course,
                    sr.email_address,
                    sr.mobile_no,
                    sr.tertiary_school,
                    sr.student_type,
                    sr.status as registration_status,
                    sr.created_at,
                    e.enrollment_status,
                    e.school_year,
                    e.semester
                FROM student_registrations sr
                LEFT JOIN enrollments e ON sr.student_id = e.student_id
                WHERE sr.status = 'approved'
                ORDER BY sr.last_name, sr.first_name";
        
        $result = $conn->query($baseSql);
        $students = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Use the student_type from database if available
                if (!empty($row['student_type'])) {
                    $detectedType = $row['student_type'];
                    switch($detectedType) {
                        case 'New': $filterType = 'new'; break;
                        case 'Old': $filterType = 'old'; break;
                        case 'Shifting': $filterType = 'shifting'; break;
                        case 'Transferee': $filterType = 'transferee'; break;
                        default: $filterType = 'other'; break;
                    }
                } else {
                    // Fallback logic for legacy data
                    if (!empty($row['tertiary_school']) && $row['tertiary_school'] != '') {
                        $detectedType = 'Transferee';
                        $filterType = 'transferee';
                    } else {
                        $studentYear = substr($row['student_id'], 0, 4);
                        if ($studentYear == '2025') {
                            $detectedType = 'New';
                            $filterType = 'new';
                        } elseif ($studentYear == '2024') {
                            $detectedType = 'Old';
                            $filterType = 'old';
                        } else {
                            $detectedType = 'Other';
                            $filterType = 'other';
                        }
                    }
                }
                
                // Apply filter based on detected type
                if ($studentType !== 'all' && $studentType !== $filterType) {
                    continue; // Skip this student if it doesn't match the filter
                }
                
                $row['type'] = $detectedType;
                
                // Apply search filter if provided
                if ($search) {
                    $searchLower = strtolower($search);
                    $fullName = strtolower($row['first_name'] . ' ' . $row['last_name']);
                    $studentId = strtolower($row['student_id']);
                    
                    if (strpos($fullName, $searchLower) === false && 
                        strpos($studentId, $searchLower) === false) {
                        continue; // Skip if doesn't match search
                    }
                }
                
                // Document status (simplified for now)
                $row['document_status'] = 'Complete';
                $row['full_name'] = $row['first_name'] . ' ' . $row['last_name'];
                $row['is_enrolled'] = !empty($row['enrollment_status']);
                
                $students[] = $row;
            }
        }
        
        sendResponse(true, 'Students retrieved successfully', $students);
        
    } catch (Exception $e) {
        sendResponse(false, 'Error retrieving students: ' . $e->getMessage());
    }
}

// Get student search results
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'search_students') {
    validateRegistrarAuth();
    
    $query = $_GET['q'] ?? '';
    
    if (strlen($query) < 2) {
        sendResponse(true, 'Query too short', []);
    }
    
    $query = $conn->real_escape_string($query);
    
    $sql = "SELECT 
                sr.student_id,
                sr.first_name,
                sr.last_name,
                sr.desired_course,
                sr.email_address,
                sr.tertiary_school,
                sr.student_type,
                e.enrollment_status
            FROM student_registrations sr
            LEFT JOIN enrollments e ON sr.student_id = e.student_id
            WHERE sr.status = 'approved' 
            AND (sr.student_id LIKE '%$query%' 
                OR sr.first_name LIKE '%$query%' 
                OR sr.last_name LIKE '%$query%'
                OR CONCAT(sr.first_name, ' ', sr.last_name) LIKE '%$query%')
            ORDER BY sr.last_name, sr.first_name
            LIMIT 10";
    
    $result = $conn->query($sql);
    $students = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Use the student_type from database if available
            if (!empty($row['student_type'])) {
                $row['type'] = $row['student_type'];
            } else {
                // Fallback logic for legacy data
                if (!empty($row['tertiary_school']) && $row['tertiary_school'] != '') {
                    $row['type'] = 'Transferee';
                } else {
                    $studentYear = substr($row['student_id'], 0, 4);
                    if ($studentYear == '2025') {
                        $row['type'] = 'New';
                    } elseif ($studentYear == '2024') {
                        $row['type'] = 'Old';
                    } else {
                        $row['type'] = 'Other';
                    }
                }
            }
            
            $row['full_name'] = $row['first_name'] . ' ' . $row['last_name'];
            $row['is_enrolled'] = !empty($row['enrollment_status']);
            $students[] = $row;
        }
    }
    
    sendResponse(true, 'Search results retrieved successfully', $students);
}

sendResponse(false, 'Invalid request');
?>
