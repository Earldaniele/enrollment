<?php
require_once 'config.php';

class StudentListHandler {
    private $conn;
    
    public function __construct() {
        $this->conn = getDBConnection();
    }
    
    /**
     * Get enrolled students with payment information
     */
    public function getEnrolledStudents($filters = [], $page = 1, $limit = 10) {
        $students = [];
        $totalCount = 0;
        
        try {
            // Build WHERE clause
            $whereConditions = ["sr.status = 'approved'"];
            $params = [];
            $types = "";
            
            // Payment status filter
            if (!empty($filters['payment_status']) && $filters['payment_status'] !== 'all') {
                if ($filters['payment_status'] === 'pending') {
                    $whereConditions[] = "EXISTS (SELECT 1 FROM student_payments sp WHERE sp.student_id = sr.student_id AND sp.status = 'pending')";
                } else {
                    $whereConditions[] = "e.payment_status = ?";
                    $params[] = $filters['payment_status'];
                    $types .= "s";
                }
            }
            
            // Course filter
            if (!empty($filters['course']) && $filters['course'] !== 'all') {
                $whereConditions[] = "sr.desired_course LIKE ?";
                $params[] = "%{$filters['course']}%";
                $types .= "s";
            }
            
            // Year level filter
            if (!empty($filters['year_level']) && $filters['year_level'] !== 'all') {
                $yearLevel = $filters['year_level'] . 'st Year';
                if ($filters['year_level'] == '2') $yearLevel = '2nd Year';
                if ($filters['year_level'] == '3') $yearLevel = '3rd Year';
                if ($filters['year_level'] == '4') $yearLevel = '4th Year';
                
                $whereConditions[] = "e.year_level = ?";
                $params[] = $yearLevel;
                $types .= "s";
            }
            
            // Search filter
            if (!empty($filters['search'])) {
                $search = "%{$filters['search']}%";
                $whereConditions[] = "(sr.student_id LIKE ? OR CONCAT(sr.last_name, ', ', sr.first_name) LIKE ?)";
                $params[] = $search;
                $params[] = $search;
                $types .= "ss";
            }
            
            $whereClause = implode(" AND ", $whereConditions);
            
            // Get total count
            $countQuery = "SELECT COUNT(*) as total
                          FROM student_registrations sr
                          LEFT JOIN enrollments e ON sr.student_id = e.student_id
                          WHERE $whereClause";
            
            if (!empty($params)) {
                $stmt = $this->conn->prepare($countQuery);
                if (!empty($types)) {
                    $stmt->bind_param($types, ...$params);
                }
                $stmt->execute();
                $result = $stmt->get_result();
                $totalCount = $result->fetch_assoc()['total'];
            } else {
                $result = $this->conn->query($countQuery);
                $totalCount = $result->fetch_assoc()['total'];
            }
            
            // Get students with pagination
            $offset = ($page - 1) * $limit;
            $query = "SELECT 
                        sr.student_id,
                        CONCAT(sr.last_name, ', ', sr.first_name) as student_name,
                        sr.desired_course,
                        e.year_level,
                        e.total_assessment,
                        e.payment_status,
                        COALESCE(
                            (SELECT SUM(amount) FROM student_payments WHERE student_id = sr.student_id AND status = 'completed'),
                            0
                        ) as total_paid,
                        CASE 
                            WHEN EXISTS (SELECT 1 FROM student_payments WHERE student_id = sr.student_id AND status = 'pending') THEN 'pending_verification'
                            ELSE e.payment_status
                        END as display_status
                      FROM student_registrations sr
                      LEFT JOIN enrollments e ON sr.student_id = e.student_id
                      WHERE $whereClause
                      ORDER BY sr.student_id DESC
                      LIMIT ? OFFSET ?";
            
            $params[] = $limit;
            $params[] = $offset;
            $types .= "ii";
            
            $stmt = $this->conn->prepare($query);
            if (!empty($types)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $students[] = [
                    'student_id' => $row['student_id'],
                    'student_name' => $row['student_name'],
                    'course' => $this->formatCourse($row['desired_course']),
                    'year_level' => $this->formatYearLevel($row['year_level']),
                    'total_fee' => number_format($row['total_assessment'] ?? 0, 2),
                    'total_paid' => number_format($row['total_paid'], 2),
                    'remaining_balance' => number_format(($row['total_assessment'] ?? 0) - $row['total_paid'], 2),
                    'payment_status' => $row['display_status'] ?? 'unpaid',
                    'status_display' => $this->formatPaymentStatus($row['display_status'] ?? 'unpaid')
                ];
            }
            
        } catch (Exception $e) {
            error_log("Error getting enrolled students: " . $e->getMessage());
        }
        
        return [
            'students' => $students,
            'total_count' => $totalCount,
            'total_pages' => ceil($totalCount / $limit),
            'current_page' => $page
        ];
    }
    
    /**
     * Get unique courses for filter dropdown
     */
    public function getCourses() {
        $courses = [];
        
        try {
            $query = "SELECT DISTINCT desired_course 
                     FROM student_registrations 
                     WHERE status = 'approved' AND desired_course IS NOT NULL
                     ORDER BY desired_course";
            
            $result = $this->conn->query($query);
            while ($row = $result->fetch_assoc()) {
                $courses[] = [
                    'value' => $this->getCourseCode($row['desired_course']),
                    'label' => $this->formatCourse($row['desired_course'])
                ];
            }
        } catch (Exception $e) {
            error_log("Error getting courses: " . $e->getMessage());
        }
        
        return $courses;
    }
    
    /**
     * Format course name for display
     */
    private function formatCourse($course) {
        if (strpos($course, 'Information Technology') !== false) {
            return 'BSIT';
        } elseif (strpos($course, 'Computer Science') !== false) {
            return 'BSCS';
        } elseif (strpos($course, 'Nursing') !== false) {
            return 'BSN';
        } elseif (strpos($course, 'Education') !== false) {
            return 'BSED';
        }
        
        return strtoupper(substr($course, 0, 4));
    }
    
    /**
     * Get course code from full course name
     */
    private function getCourseCode($course) {
        if (strpos($course, 'Information Technology') !== false) {
            return 'bsit';
        } elseif (strpos($course, 'Computer Science') !== false) {
            return 'bscs';
        } elseif (strpos($course, 'Nursing') !== false) {
            return 'bsn';
        } elseif (strpos($course, 'Education') !== false) {
            return 'bsed';
        }
        
        return strtolower(substr($course, 0, 4));
    }
    
    /**
     * Format year level for display
     */
    private function formatYearLevel($yearLevel) {
        if (empty($yearLevel)) return 'N/A';
        
        $year = str_replace(' Year', '', $yearLevel);
        return $year;
    }
    
    /**
     * Get detailed student information for payment details page
     */
    public function getStudentDetails($studentId) {
        try {
            $query = "SELECT 
                        sr.student_id,
                        CONCAT(sr.last_name, ', ', sr.first_name, 
                               CASE WHEN sr.middle_name IS NOT NULL THEN CONCAT(' ', sr.middle_name) ELSE '' END) as full_name,
                        sr.desired_course,
                        sr.email_address,
                        sr.mobile_no,
                        sr.status as enrollment_status,
                        e.year_level,
                        e.semester,
                        e.school_year,
                        e.total_assessment,
                        e.payment_status,
                        e.created_at as date_enrolled,
                        COALESCE(
                            (SELECT SUM(amount) FROM student_payments WHERE student_id = sr.student_id AND status = 'completed'),
                            0
                        ) as total_paid
                      FROM student_registrations sr
                      LEFT JOIN enrollments e ON sr.student_id = e.student_id
                      WHERE sr.student_id = ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("s", $studentId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                $row['remaining_balance'] = ($row['total_assessment'] ?? 0) - $row['total_paid'];
                $row['total_assessment_formatted'] = number_format($row['total_assessment'] ?? 0, 2);
                $row['total_paid_formatted'] = number_format($row['total_paid'], 2);
                $row['remaining_balance_formatted'] = number_format($row['remaining_balance'], 2);
                
                // Get enrolled subjects
                $subjectsQuery = "SELECT 
                                    s.subject_code,
                                    s.subject_name as subject_title,
                                    s.units as academic_units,
                                    0 as lab_units,
                                    'Lecture' as type
                                 FROM enrolled_subjects es
                                 JOIN subjects s ON es.subject_id = s.id
                                 WHERE es.student_id = ?";
                
                $subjectsStmt = $this->conn->prepare($subjectsQuery);
                $subjectsStmt->bind_param("s", $studentId);
                $subjectsStmt->execute();
                $subjectsResult = $subjectsStmt->get_result();
                
                $subjects = [];
                $totalUnits = 0;
                $totalLabUnits = 0;
                
                while ($subject = $subjectsResult->fetch_assoc()) {
                    $subjects[] = $subject;
                    $totalUnits += $subject['academic_units'];
                    $totalLabUnits += $subject['lab_units'];
                }
                
                $row['subjects'] = $subjects;
                $row['total_units'] = $totalUnits;
                $row['total_lab_units'] = $totalLabUnits;
                
                // Get Assessment Summary from student_assessments table (same as view-enrollment.php)
                $assessmentQuery = "SELECT 
                                      fee_type,
                                      amount
                                    FROM student_assessments
                                    WHERE student_id = ?
                                    ORDER BY 
                                      CASE fee_type
                                        WHEN 'Tuition Fee' THEN 1
                                        WHEN 'Laboratory Fee' THEN 2
                                        WHEN 'Library Fee' THEN 3
                                        WHEN 'Registration Fee' THEN 4
                                        WHEN 'Medical/Dental Fee' THEN 5
                                        WHEN 'Student Activities Fee' THEN 6
                                        WHEN 'Insurance Fee' THEN 7
                                        WHEN 'ID Fee' THEN 8
                                        ELSE 9
                                      END";
                
                $assessmentStmt = $this->conn->prepare($assessmentQuery);
                $assessmentStmt->bind_param("s", $studentId);
                $assessmentStmt->execute();
                $assessmentResult = $assessmentStmt->get_result();
                
                $feeBreakdown = [];
                $totalAssessment = 0;
                
                while ($fee = $assessmentResult->fetch_assoc()) {
                    $amount = floatval($fee['amount']);
                    $feeBreakdown[] = [
                        'fee_name' => $fee['fee_type'],
                        'amount_due' => $amount,
                        'amount_paid' => 0, // Will be calculated separately
                        'amount_formatted' => number_format($amount, 2)
                    ];
                    $totalAssessment += $amount;
                }
                
                // Calculate total amount paid from student_payments table
                $totalPaidQuery = "SELECT COALESCE(SUM(amount), 0) as total_paid 
                                   FROM student_payments 
                                   WHERE student_id = ? AND status = 'completed'";
                $paidStmt = $this->conn->prepare($totalPaidQuery);
                $paidStmt->bind_param("s", $studentId);
                $paidStmt->execute();
                $paidResult = $paidStmt->get_result();
                $paidRow = $paidResult->fetch_assoc();
                $totalPaid = floatval($paidRow['total_paid']);
                
                // Calculate remaining balance
                $remainingBalance = $totalAssessment - $totalPaid;
                
                // Add Total Assessment row
                if (!empty($feeBreakdown)) {
                    $feeBreakdown[] = [
                        'fee_name' => 'TOTAL ASSESSMENT',
                        'amount_due' => $totalAssessment,
                        'amount_paid' => $totalPaid,
                        'amount_formatted' => number_format($totalAssessment, 2),
                        'amount_paid_formatted' => number_format($totalPaid, 2),
                        'remaining_balance' => $remainingBalance,
                        'remaining_balance_formatted' => number_format($remainingBalance, 2),
                        'is_total' => true
                    ];
                }
                
                $row['fee_breakdown'] = $feeBreakdown;
                $row['total_assessment'] = $totalAssessment;
                $row['total_paid'] = $totalPaid;
                $row['remaining_balance'] = $remainingBalance;
                $row['total_assessment_formatted'] = number_format($totalAssessment, 2);
                $row['total_paid_formatted'] = number_format($totalPaid, 2);
                $row['remaining_balance_formatted'] = number_format($remainingBalance, 2);
                
                // Get payment history
                $paymentQuery = "SELECT 
                                   reference_number as or_number,
                                   amount,
                                   payment_method,
                                   status,
                                   paid_at as payment_date
                                FROM student_payments 
                                WHERE student_id = ? 
                                ORDER BY paid_at DESC";
                
                $paymentStmt = $this->conn->prepare($paymentQuery);
                $paymentStmt->bind_param("s", $studentId);
                $paymentStmt->execute();
                $paymentResult = $paymentStmt->get_result();
                
                $payments = [];
                while ($payment = $paymentResult->fetch_assoc()) {
                    $payments[] = $payment;
                }
                
                $row['payment_history'] = $payments;
                
                return $row;
            }
            
            return null;
        } catch (Exception $e) {
            error_log("Error getting student details: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Format payment status for display
     */
    private function formatPaymentStatus($status) {
        $statuses = [
            'unpaid' => ['label' => 'Unpaid', 'class' => 'danger'],
            'partial' => ['label' => 'Installment', 'class' => 'warning'],
            'paid' => ['label' => 'Fully Paid', 'class' => 'success'],
            'pending_verification' => ['label' => 'Pending Verification', 'class' => 'info']
        ];
        
        return $statuses[$status] ?? ['label' => 'Unknown', 'class' => 'secondary'];
    }
}

// Handle AJAX requests
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    try {
        $handler = new StudentListHandler();
        
        switch ($_GET['action']) {
            case 'get_students':
                $filters = [
                    'payment_status' => $_GET['payment_status'] ?? 'all',
                    'course' => $_GET['course'] ?? 'all',
                    'year_level' => $_GET['year_level'] ?? 'all',
                    'search' => $_GET['search'] ?? ''
                ];
                $page = intval($_GET['page'] ?? 1);
                $limit = intval($_GET['limit'] ?? 10);
                
                echo json_encode([
                    'success' => true,
                    'data' => $handler->getEnrolledStudents($filters, $page, $limit)
                ]);
                break;
                
            case 'get_courses':
                echo json_encode([
                    'success' => true,
                    'data' => $handler->getCourses()
                ]);
                break;
                
            case 'get_student_details':
                $studentId = $_GET['student_id'] ?? '';
                echo json_encode([
                    'success' => true,
                    'data' => $handler->getStudentDetails($studentId)
                ]);
                break;
                
            default:
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid action'
                ]);
                break;
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Server error: ' . $e->getMessage()
        ]);
    }
    exit;
}
?>
