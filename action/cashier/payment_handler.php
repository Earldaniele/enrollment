<?php
// Ensure only JSON output
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors to prevent breaking JSON

require_once 'config.php';

class PaymentHandler {
    private $conn;
    
    public function __construct() {
        $this->conn = getDBConnection();
    }
    
    /**
     * Get student information for payment using accurate data from student_assessments
     */
    public function getStudentInfo($studentId) {
        try {
            // Get basic student information
            $query = "SELECT 
                        sr.student_id,
                        CONCAT(sr.last_name, ', ', sr.first_name, 
                               CASE WHEN sr.middle_name IS NOT NULL THEN CONCAT(' ', sr.middle_name) ELSE '' END) as full_name,
                        sr.desired_course,
                        sr.email_address,
                        sr.mobile_no,
                        sr.status as registration_status,
                        e.year_level,
                        e.semester,
                        e.school_year,
                        e.total_units,
                        e.payment_status,
                        e.enrollment_status,
                        e.created_at as enrollment_date,
                        (SELECT COUNT(*) FROM student_payments WHERE student_id = sr.student_id AND status = 'pending') as pending_payments_count,
                        (SELECT COUNT(*) FROM enrolled_subjects WHERE student_id = sr.student_id) as enrolled_subjects_count
                      FROM student_registrations sr
                      LEFT JOIN enrollments e ON sr.student_id = e.student_id
                      WHERE sr.student_id = ? AND sr.status = 'approved'";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("s", $studentId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                // Get accurate total assessment from student_assessments table (same as payment_details.php)
                $assessmentQuery = "SELECT SUM(amount) as total_assessment FROM student_assessments WHERE student_id = ?";
                $assessmentStmt = $this->conn->prepare($assessmentQuery);
                $assessmentStmt->bind_param("s", $studentId);
                $assessmentStmt->execute();
                $assessmentResult = $assessmentStmt->get_result();
                $assessmentData = $assessmentResult->fetch_assoc();
                
                // Get accurate total paid from student_payments table
                $paymentsQuery = "SELECT COALESCE(SUM(amount), 0) as total_paid FROM student_payments WHERE student_id = ? AND status = 'completed'";
                $paymentsStmt = $this->conn->prepare($paymentsQuery);
                $paymentsStmt->bind_param("s", $studentId);
                $paymentsStmt->execute();
                $paymentsResult = $paymentsStmt->get_result();
                $paymentsData = $paymentsResult->fetch_assoc();
                
                // Use accurate calculations
                $totalAssessment = floatval($assessmentData['total_assessment'] ?? 0);
                $totalPaid = floatval($paymentsData['total_paid'] ?? 0);
                $remainingBalance = $totalAssessment - $totalPaid;
                
                // Update row with accurate data
                $row['total_assessment'] = $totalAssessment;
                $row['total_paid'] = $totalPaid;
                $row['remaining_balance'] = $remainingBalance;
                $row['total_assessment_formatted'] = number_format($totalAssessment, 2);
                $row['total_paid_formatted'] = number_format($totalPaid, 2);
                $row['remaining_balance_formatted'] = number_format($remainingBalance, 2);
                $row['has_pending_payments'] = $row['pending_payments_count'] > 0;
                
                // Calculate payment completion percentage
                if ($totalAssessment > 0) {
                    $row['payment_percentage'] = round(($totalPaid / $totalAssessment) * 100, 2);
                } else {
                    $row['payment_percentage'] = 0;
                }
                
                return $row;
            }
            
            return null;
        } catch (Exception $e) {
            error_log("Error getting student info: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Record a new payment
     */
    public function recordPayment($data) {
        try {
            $this->conn->begin_transaction();
            
            // Validate student exists and get current info
            $studentInfo = $this->getStudentInfo($data['student_id']);
            if (!$studentInfo) {
                throw new Exception("Student not found or not approved");
            }
            
            // Validate payment amount
            $amount = floatval($data['amount']);
            if ($amount <= 0) {
                throw new Exception("Invalid payment amount");
            }
            
            // Dynamic validation: Check for suspiciously small payments
            $minReasonablePayment = $studentInfo['total_assessment'] * 0.05; // 5% minimum
            if ($amount < $minReasonablePayment && $amount < 1000) {
                // Suggest possible corrections
                $suggestions = [];
                if ($amount * 100 <= $studentInfo['remaining_balance']) {
                    $suggestions[] = "₱" . number_format($amount * 100, 2) . " (if decimal point error)";
                }
                if ($amount * 1000 <= $studentInfo['remaining_balance']) {
                    $suggestions[] = "₱" . number_format($amount * 1000, 2) . " (if typing error)";
                }
                $suggestions[] = "₱" . number_format($studentInfo['total_assessment'] * 0.20, 2) . " (20% down payment)";
                
                $suggestionText = !empty($suggestions) ? " Did you mean: " . implode(", ", $suggestions) . "?" : "";
                
                throw new Exception("Payment amount (₱" . number_format($amount, 2) . ") seems unusually small for an assessment of ₱" . number_format($studentInfo['total_assessment'], 2) . "." . $suggestionText);
            }
            
            // Check if payment amount is reasonable (allow small overpayment for rounding)
            if ($amount > ($studentInfo['remaining_balance'] + 0.01)) {
                throw new Exception("Payment amount (₱" . number_format($amount, 2) . ") exceeds remaining balance (₱" . number_format($studentInfo['remaining_balance'], 2) . ")");
            }
            
            // Generate reference number
            $referenceNumber = $this->generateReferenceNumber($data['payment_method']);
            
            // Insert payment record
            $insertQuery = "INSERT INTO student_payments 
                           (student_id, amount, payment_method, reference_number, status, paid_at, created_at) 
                           VALUES (?, ?, ?, ?, 'completed', NOW(), NOW())";
            
            $stmt = $this->conn->prepare($insertQuery);
            $stmt->bind_param("sdss", 
                $data['student_id'], 
                $amount, 
                $data['payment_method'], 
                $referenceNumber
            );
            $stmt->execute();
            
            $paymentId = $this->conn->insert_id;
            
            // Update enrollment payment status
            $newTotalPaid = $studentInfo['total_paid'] + $amount;
            $newPaymentStatus = 'partial';
            
            if ($newTotalPaid >= $studentInfo['total_assessment']) {
                $newPaymentStatus = 'paid';
            } elseif ($newTotalPaid > 0) {
                $newPaymentStatus = 'partial';
            } else {
                $newPaymentStatus = 'unpaid';
            }
            
            $updateQuery = "UPDATE enrollments 
                           SET payment_status = ?, updated_at = NOW() 
                           WHERE student_id = ?";
            
            $stmt = $this->conn->prepare($updateQuery);
            $stmt->bind_param("ss", $newPaymentStatus, $data['student_id']);
            $stmt->execute();
            
            $this->conn->commit();
            
            // TODO: Send notifications (temporarily disabled to fix JSON response)
            // require_once '../includes/notification_helpers.php';
            // notifyStudentPaymentVerified($studentInfo['email_address'], $studentInfo['full_name'], $amount);
            
            return [
                'success' => true,
                'payment_id' => $paymentId,
                'reference_number' => $referenceNumber,
                'message' => 'Payment recorded successfully'
            ];
            
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Error recording payment: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Get payment history for a student
     */
    public function getPaymentHistory($studentId) {
        try {
            $query = "SELECT 
                        id,
                        amount,
                        payment_method,
                        reference_number,
                        status,
                        paid_at,
                        created_at
                      FROM student_payments
                      WHERE student_id = ?
                      ORDER BY created_at DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("s", $studentId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $payments = [];
            while ($row = $result->fetch_assoc()) {
                $payments[] = [
                    'id' => $row['id'],
                    'amount' => number_format($row['amount'], 2),
                    'payment_method' => $this->formatPaymentMethod($row['payment_method']),
                    'reference_number' => $row['reference_number'],
                    'status' => $this->formatPaymentStatus($row['status']),
                    'date' => date('M j, Y g:i A', strtotime($row['paid_at'] ?? $row['created_at']))
                ];
            }
            
            return $payments;
        } catch (Exception $e) {
            error_log("Error getting payment history: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get pending payment verifications
     */
    public function getPendingVerifications($filters = [], $page = 1, $limit = 10) {
        try {
            $whereConditions = ["sp.status = 'pending'"];
            $params = [];
            $types = "";
            
            // Payment type filter - intelligent matching
            if (!empty($filters['payment_type']) && $filters['payment_type'] !== 'all') {
                $paymentType = $filters['payment_type'];
                
                switch ($paymentType) {
                    case 'gcash':
                        $whereConditions[] = "(sp.payment_method = 'gcash' OR sp.reference_number LIKE '%GCASH%')";
                        break;
                    case 'paymaya':
                        $whereConditions[] = "(sp.payment_method IN ('paymaya', 'maya') OR sp.reference_number LIKE '%PAYMAYA%' OR sp.reference_number LIKE '%MAYA%')";
                        break;
                    case 'bank_transfer':
                        $whereConditions[] = "(sp.payment_method = 'bank_transfer' AND sp.reference_number NOT LIKE '%GCASH%' AND sp.reference_number NOT LIKE '%PAYMAYA%' AND sp.reference_number NOT LIKE '%MAYA%')";
                        break;
                    default:
                        $whereConditions[] = "sp.payment_method = ?";
                        $params[] = $paymentType;
                        $types .= "s";
                        break;
                }
            }
            
            // Date range filter
            if (!empty($filters['date_range']) && $filters['date_range'] !== 'all') {
                switch ($filters['date_range']) {
                    case 'today':
                        $whereConditions[] = "DATE(sp.created_at) = CURDATE()";
                        break;
                    case 'yesterday':
                        $whereConditions[] = "DATE(sp.created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
                        break;
                    case 'thisweek':
                        $whereConditions[] = "YEARWEEK(sp.created_at, 1) = YEARWEEK(CURDATE(), 1)";
                        break;
                    case 'lastweek':
                        $whereConditions[] = "YEARWEEK(sp.created_at, 1) = YEARWEEK(DATE_SUB(CURDATE(), INTERVAL 1 WEEK), 1)";
                        break;
                    case 'thismonth':
                        $whereConditions[] = "YEAR(sp.created_at) = YEAR(CURDATE()) AND MONTH(sp.created_at) = MONTH(CURDATE())";
                        break;
                }
            }
            
            // Search filter
            if (!empty($filters['search'])) {
                $search = "%{$filters['search']}%";
                $whereConditions[] = "(sp.reference_number LIKE ? OR CONCAT(sr.last_name, ', ', sr.first_name) LIKE ?)";
                $params[] = $search;
                $params[] = $search;
                $types .= "ss";
            }
            
            $whereClause = implode(" AND ", $whereConditions);
            
            // Get total count
            $countQuery = "SELECT COUNT(*) as total
                          FROM student_payments sp
                          JOIN student_registrations sr ON sp.student_id = sr.student_id
                          WHERE $whereClause";
            
            $totalCount = 0;
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
            
            // Get pending verifications with pagination
            $offset = ($page - 1) * $limit;
            $query = "SELECT 
                        sp.id,
                        sp.reference_number,
                        sr.student_id,
                        CONCAT(sr.last_name, ', ', sr.first_name) as student_name,
                        sp.payment_method,
                        sp.amount,
                        sp.created_at,
                        DATE_ADD(sp.created_at, INTERVAL 7 DAY) as valid_until
                      FROM student_payments sp
                      JOIN student_registrations sr ON sp.student_id = sr.student_id
                      WHERE $whereClause
                      ORDER BY sp.created_at DESC
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
            
            $verifications = [];
            while ($row = $result->fetch_assoc()) {
                $verifications[] = [
                    'id' => $row['id'],
                    'reference_number' => $row['reference_number'],
                    'student_id' => $row['student_id'],
                    'student_name' => $row['student_name'],
                    'payment_method' => $this->formatPaymentMethod($row['payment_method'], $row['reference_number']),
                    'amount' => number_format($row['amount'], 2),
                    'date_submitted' => date('M j, Y g:i A', strtotime($row['created_at'])),
                    'valid_until' => date('M j, Y g:i A', strtotime($row['valid_until']))
                ];
            }
            
            return [
                'verifications' => $verifications,
                'total_count' => $totalCount,
                'total_pages' => ceil($totalCount / $limit),
                'current_page' => $page
            ];
            
        } catch (Exception $e) {
            error_log("Error getting pending verifications: " . $e->getMessage());
            return [
                'verifications' => [],
                'total_count' => 0,
                'total_pages' => 0,
                'current_page' => 1
            ];
        }
    }
    
    /**
     * Verify a payment
     */
    public function verifyPayment($paymentId, $action) {
        try {
            $this->conn->begin_transaction();
            
            // Get payment details
            $query = "SELECT sp.*, sr.student_id 
                     FROM student_payments sp
                     JOIN student_registrations sr ON sp.student_id = sr.student_id
                     WHERE sp.id = ? AND sp.status = 'pending'";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $paymentId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if (!$payment = $result->fetch_assoc()) {
                throw new Exception("Payment not found or already processed");
            }
            
            $newStatus = ($action === 'approve') ? 'completed' : 'failed';
            
            // Update payment status
            $updateQuery = "UPDATE student_payments 
                           SET status = ?, paid_at = IF(? = 'completed', NOW(), paid_at)
                           WHERE id = ?";
            
            $stmt = $this->conn->prepare($updateQuery);
            $stmt->bind_param("ssi", $newStatus, $newStatus, $paymentId);
            $stmt->execute();
            
            // If approved, update enrollment payment status
            if ($action === 'approve') {
                $studentInfo = $this->getStudentInfo($payment['student_id']);
                if ($studentInfo) {
                    $newTotalPaid = $studentInfo['total_paid'] + $payment['amount'];
                    $enrollmentStatus = 'partial';
                    
                    if ($newTotalPaid >= $studentInfo['total_assessment']) {
                        $enrollmentStatus = 'paid';
                    }
                    
                    $updateEnrollmentQuery = "UPDATE enrollments 
                                            SET payment_status = ?, updated_at = NOW() 
                                            WHERE student_id = ?";
                    
                    $stmt = $this->conn->prepare($updateEnrollmentQuery);
                    $stmt->bind_param("ss", $enrollmentStatus, $payment['student_id']);
                    $stmt->execute();
                }
            }
            
            $this->conn->commit();
            
            return [
                'success' => true,
                'message' => 'Payment ' . ($action === 'approve' ? 'approved' : 'rejected') . ' successfully'
            ];
            
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Error verifying payment: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Approve a payment
     */
    public function approvePayment($paymentId) {
        return $this->verifyPayment($paymentId, 'approve');
    }
    
    /**
     * Reject a payment
     */
    public function rejectPayment($paymentId, $reason = '') {
        $result = $this->verifyPayment($paymentId, 'reject');
        
        // If rejection was successful and a reason was provided, log it
        if ($result['success'] && !empty($reason)) {
            try {
                $logQuery = "INSERT INTO payment_logs (payment_id, action, reason, created_at) 
                            VALUES (?, 'rejected', ?, NOW())";
                $stmt = $this->conn->prepare($logQuery);
                $stmt->bind_param("is", $paymentId, $reason);
                $stmt->execute();
            } catch (Exception $e) {
                error_log("Error logging rejection reason: " . $e->getMessage());
            }
        }
        
        return $result;
    }
    
    /**
     * Generate reference number
     */
    private function generateReferenceNumber($paymentMethod) {
        $prefix = [
            'cash' => 'CASH',
            'check' => 'CHECK',
            'bank_transfer' => 'BT',
            'online' => 'ON'
        ][$paymentMethod] ?? 'PAY';
        
        $date = date('Ymd');
        $random = sprintf('%03d', rand(1, 999));
        
        return $prefix . '-' . $date . '-' . $random;
    }
    
    /**
     * Format payment method for display (dynamic based on reference number and stored method)
     */
    private function formatPaymentMethod($method, $referenceNumber = '') {
        // First, try to detect from reference number if provided
        if (!empty($referenceNumber)) {
            if (strpos($referenceNumber, 'GCASH') !== false) {
                return 'GCash';
            } elseif (strpos($referenceNumber, 'PAYMAYA') !== false || strpos($referenceNumber, 'MAYA') !== false) {
                return 'PayMaya';
            } elseif (strpos($referenceNumber, 'BANK') !== false || strpos($referenceNumber, 'BPI') !== false || strpos($referenceNumber, 'BDO') !== false) {
                return 'Bank Transfer';
            }
        }
        
        // Fallback to method mapping
        $methods = [
            'cash' => 'Cash',
            'check' => 'Check',
            'bank_transfer' => 'Bank Transfer',
            'bank' => 'Bank Transfer',
            'online' => 'Online Transfer',
            'gcash' => 'GCash',
            'paymaya' => 'PayMaya',
            'maya' => 'PayMaya',
            'grab_pay' => 'GrabPay',
            'coins_ph' => 'Coins.ph',
            'other' => 'Other E-Wallet'
        ];
        
        return $methods[strtolower($method)] ?? ucfirst($method);
    }
    
    /**
     * Format payment status for display
     */
    private function formatPaymentStatus($status) {
        $statuses = [
            'pending' => ['label' => 'Pending', 'class' => 'warning'],
            'completed' => ['label' => 'Completed', 'class' => 'success'],
            'failed' => ['label' => 'Failed', 'class' => 'danger'],
            'cancelled' => ['label' => 'Cancelled', 'class' => 'secondary']
        ];
        
        return $statuses[$status] ?? ['label' => 'Unknown', 'class' => 'secondary'];
    }
    
    public function getInstallmentTracking($filters = [], $page = 1, $limit = 10) {
        try {
            $sql = "SELECT 
                        sr.student_id,
                        CONCAT(sr.last_name, ', ', sr.first_name, 
                               CASE WHEN sr.middle_name IS NOT NULL THEN CONCAT(' ', sr.middle_name) ELSE '' END) as student_name,
                        sr.desired_course as course,
                        COALESCE(e.year_level, 'Not Enrolled') as year_level,
                        COALESCE(e.semester, 'Not Enrolled') as semester,
                        COALESCE(e.school_year, 'Not Enrolled') as academic_year,
                        COALESCE(
                            (SELECT SUM(sa.amount) 
                             FROM student_assessments sa 
                             WHERE sa.student_id = sr.student_id 
                             AND (
                                 (e.school_year IS NOT NULL AND sa.school_year = e.school_year) OR
                                 (e.school_year IS NULL AND sa.school_year = '2024-2025')
                             )
                             AND (
                                 (e.semester IS NOT NULL AND sa.semester = e.semester) OR
                                 (e.semester IS NULL AND sa.semester = '1st Semester')
                             )
                            ), 
                            COALESCE(e.total_assessment, 0)
                        ) as total_assessment,
                        COALESCE(e.payment_status, 'no_assessment') as payment_status,
                        COALESCE(
                            (SELECT SUM(amount) FROM student_payments WHERE student_id = sr.student_id AND status = 'completed'),
                            0
                        ) as total_paid,
                        COALESCE(
                            (SELECT MAX(created_at) FROM student_payments WHERE student_id = sr.student_id AND status = 'completed'),
                            NULL
                        ) as last_payment_date
                    FROM student_registrations sr
                    LEFT JOIN enrollments e ON sr.student_id = e.student_id
                    WHERE sr.status = 'approved'";
            
            $params = [];
            $types = "";
            
            // Course filter
            if (!empty($filters['course']) && $filters['course'] !== 'all') {
                $sql .= " AND sr.desired_course LIKE ?";
                $params[] = "%" . $filters['course'] . "%";
                $types .= "s";
            }
            
            // Search filter
            if (!empty($filters['search'])) {
                $search = "%" . $filters['search'] . "%";
                $sql .= " AND (sr.student_id LIKE ? OR CONCAT(sr.last_name, ', ', sr.first_name) LIKE ?)";
                $params[] = $search;
                $params[] = $search;
                $types .= "ss";
            }
            
            $sql .= " ORDER BY sr.last_name, sr.first_name";
            
            $stmt = $this->conn->prepare($sql);
            
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            $results = [];
            while ($row = $result->fetch_assoc()) {
                // Calculate remaining balance
                $totalAssessment = floatval($row['total_assessment']);
                $totalPaid = floatval($row['total_paid']);
                $remainingBalance = max(0, $totalAssessment - $totalPaid);
                
                $row['total_pending'] = number_format($remainingBalance, 2, '.', '');
                
                // Apply status filter if specified
                if (!empty($filters['status']) && $filters['status'] !== 'all') {
                    $currentStatus = '';
                    if ($totalAssessment == 0) {
                        $currentStatus = 'no_assessment';
                    } elseif ($totalPaid >= $totalAssessment && $totalAssessment > 0) {
                        $currentStatus = 'fully_paid';
                    } elseif ($totalPaid > 0) {
                        $currentStatus = 'has_balance';
                    } else {
                        $currentStatus = 'no_payment';
                    }
                    
                    if ($filters['status'] !== $currentStatus) {
                        continue; // Skip this record
                    }
                }
                
                $results[] = $row;
            }
            
            return [
                'success' => true,
                'data' => $results
            ];
            
        } catch (Exception $e) {
            error_log("Database error in getInstallmentTracking: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Database error occurred'
            ];
        }
    }
    
    /**
     * Get detailed student payment information for installment tracking modal
     */
    public function getStudentPaymentDetails($studentId) {
        try {
            $query = "SELECT 
                        sr.student_id,
                        CONCAT(sr.last_name, ', ', sr.first_name, 
                               CASE WHEN sr.middle_name IS NOT NULL THEN CONCAT(' ', sr.middle_name) ELSE '' END) as student_name,
                        sr.desired_course as course,
                        e.year_level,
                        e.semester,
                        e.school_year,
                        (SELECT COALESCE(SUM(amount), 0) FROM student_assessments WHERE student_id = sr.student_id) as total_fee,
                        (SELECT COALESCE(SUM(amount), 0) FROM student_payments WHERE student_id = sr.student_id AND status = 'completed') as amount_paid,
                        ((SELECT COALESCE(SUM(amount), 0) FROM student_assessments WHERE student_id = sr.student_id) - 
                         (SELECT COALESCE(SUM(amount), 0) FROM student_payments WHERE student_id = sr.student_id AND status = 'completed')) as remaining_balance
                      FROM student_registrations sr
                      LEFT JOIN enrollments e ON sr.student_id = e.student_id
                      WHERE sr.student_id = ? AND sr.status = 'approved'";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("s", $studentId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                return $row;
            }
            
            return null;
        } catch (Exception $e) {
            error_log("Error getting student payment details: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Record installment payment
     */
    public function recordInstallmentPayment($data) {
        try {
            $this->conn->begin_transaction();
            
            // Validate student exists and has balance
            $studentCheck = "SELECT 
                               sr.student_id,
                               ((SELECT COALESCE(SUM(amount), 0) FROM student_assessments WHERE student_id = sr.student_id) - 
                                (SELECT COALESCE(SUM(amount), 0) FROM student_payments WHERE student_id = sr.student_id AND status = 'completed')) as remaining_balance
                             FROM student_registrations sr
                             WHERE sr.student_id = ? AND sr.status = 'approved'";
            
            $stmt = $this->conn->prepare($studentCheck);
            $stmt->bind_param("s", $data['student_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if (!$result->num_rows) {
                throw new Exception("Student not found or not approved");
            }
            
            $studentData = $result->fetch_assoc();
            $remainingBalance = floatval($studentData['remaining_balance']);
            
            if ($data['amount'] > $remainingBalance) {
                throw new Exception("Payment amount exceeds remaining balance");
            }
            
            // Insert payment record
            $insertQuery = "INSERT INTO student_payments (
                              student_id, amount, payment_method, or_number, 
                              remarks, status, payment_date, created_at
                            ) VALUES (?, ?, ?, ?, ?, 'completed', NOW(), NOW())";
            
            $stmt = $this->conn->prepare($insertQuery);
            $stmt->bind_param("sdsss", 
                $data['student_id'],
                $data['amount'],
                $data['payment_method'],
                $data['or_number'],
                $data['remarks']
            );
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to record payment");
            }
            
            $this->conn->commit();
            
            return [
                'success' => true,
                'message' => 'Installment payment recorded successfully',
                'payment_id' => $this->conn->insert_id
            ];
            
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Error recording installment payment: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Fix payment method inconsistencies based on reference numbers
     */
    public function fixPaymentMethodInconsistencies() {
        try {
            $updated = 0;
            
            // Fix GCash payments (including empty/null payment methods)
            $gcashQuery = "UPDATE student_payments SET payment_method = 'gcash' WHERE reference_number LIKE '%GCASH%' AND (payment_method != 'gcash' OR payment_method IS NULL OR payment_method = '')";
            $this->conn->query($gcashQuery);
            $updated += $this->conn->affected_rows;
            
            // Fix PayMaya payments
            $paymayaQuery = "UPDATE student_payments SET payment_method = 'paymaya' WHERE (reference_number LIKE '%PAYMAYA%' OR reference_number LIKE '%MAYA%') AND (payment_method NOT IN ('paymaya', 'maya') OR payment_method IS NULL OR payment_method = '')";
            $this->conn->query($paymayaQuery);
            $updated += $this->conn->affected_rows;
            
            return [
                'success' => true,
                'message' => "Fixed $updated payment method inconsistencies",
                'updated_count' => $updated
            ];
            
        } catch (Exception $e) {
            error_log("Error fixing payment method inconsistencies: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error fixing inconsistencies: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get available payment types from database (only actual existing ones)
     */
    public function getPaymentTypes() {
        try {
            // Get payment methods from pending payments and detect from reference numbers
            $query = "SELECT DISTINCT payment_method, reference_number FROM student_payments WHERE status = 'pending'";
            $result = $this->conn->query($query);
            
            $detectedTypes = [];
            while ($row = $result->fetch_assoc()) {
                $method = $row['payment_method'];
                $refNumber = $row['reference_number'];
                
                // Detect payment type from reference number if method is empty/generic
                if (empty($method) || $method === 'bank_transfer') {
                    if (strpos($refNumber, 'GCASH') !== false) {
                        $detectedTypes['gcash'] = 'GCash';
                    } elseif (strpos($refNumber, 'PAYMAYA') !== false || strpos($refNumber, 'MAYA') !== false) {
                        $detectedTypes['paymaya'] = 'PayMaya';
                    } elseif (strpos($refNumber, 'BANK') !== false) {
                        $detectedTypes['bank_transfer'] = 'Bank Transfer';
                    } else {
                        $detectedTypes['bank_transfer'] = 'Bank Transfer';
                    }
                } else {
                    $detectedTypes[$method] = $this->formatPaymentMethod($method, '');
                }
            }
            
            // Convert to expected format
            $types = [];
            foreach ($detectedTypes as $value => $label) {
                $types[] = ['value' => $value, 'label' => $label];
            }
            
            // Sort by label
            usort($types, function($a, $b) {
                return strcmp($a['label'], $b['label']);
            });
            
            return $types;
            
        } catch (Exception $e) {
            error_log("Error getting payment types: " . $e->getMessage());
            return [
                ['value' => 'bank_transfer', 'label' => 'Bank Transfer'],
                ['value' => 'gcash', 'label' => 'GCash'],
                ['value' => 'paymaya', 'label' => 'PayMaya']
            ];
        }
    }
}

// Handle AJAX requests
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    try {
        $handler = new PaymentHandler();
        
        switch ($_GET['action']) {
            case 'get_student_info':
                $studentId = $_GET['student_id'] ?? '';
                $info = $handler->getStudentInfo($studentId);
                
                if ($info) {
                    echo json_encode([
                        'success' => true,
                        'data' => $info
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Student not found or not approved'
                    ]);
                }
                break;
                
            case 'get_payment_history':
                $studentId = $_GET['student_id'] ?? '';
                echo json_encode([
                    'success' => true,
                    'data' => $handler->getPaymentHistory($studentId)
                ]);
                break;
                
            case 'get_pending_verifications':
                $filters = [
                    'payment_type' => $_GET['payment_type'] ?? 'all',
                    'date_range' => $_GET['date_range'] ?? 'all',
                    'search' => $_GET['search'] ?? ''
                ];
                $page = intval($_GET['page'] ?? 1);
                $limit = intval($_GET['limit'] ?? 10);
                
                echo json_encode([
                    'success' => true,
                    'data' => $handler->getPendingVerifications($filters, $page, $limit)
                ]);
                break;
                
            case 'get_payment_types':
                $result = $handler->getPaymentTypes();
                echo json_encode([
                    'success' => true,
                    'data' => $result
                ]);
                break;
                
            case 'fix_payment_methods':
                $result = $handler->fixPaymentMethodInconsistencies();
                echo json_encode($result);
                break;
                
            case 'get_installment_tracking':
                $filters = [
                    'status' => $_GET['status'] ?? 'all',
                    'course' => $_GET['course'] ?? 'all', 
                    'search' => $_GET['search'] ?? ''
                ];
                $page = intval($_GET['page'] ?? 1);
                $limit = intval($_GET['limit'] ?? 10);
                
                echo json_encode($handler->getInstallmentTracking($filters, $page, $limit));
                break;
                
            case 'get_student_payment_details':
                $studentId = $_GET['student_id'] ?? '';
                $result = $handler->getStudentPaymentDetails($studentId);
                
                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'data' => $result
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Student payment details not found'
                    ]);
                }
                break;
                
            case 'approve_payment':
                $paymentId = intval($_POST['payment_id'] ?? 0);
                $result = $handler->approvePayment($paymentId);
                echo json_encode($result);
                break;
                
            case 'reject_payment':
                $paymentId = intval($_POST['payment_id'] ?? 0);
                $reason = $_POST['reason'] ?? '';
                $result = $handler->rejectPayment($paymentId, $reason);
                echo json_encode($result);
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

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $handler = new PaymentHandler();
        
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'record_payment':
                $data = [
                    'student_id' => $_POST['student_id'] ?? '',
                    'amount' => $_POST['amount'] ?? 0,
                    'payment_method' => $_POST['payment_method'] ?? 'cash'
                ];
                
                $result = $handler->recordPayment($data);
                echo json_encode($result);
                break;
                
            case 'verify_payment':
                $paymentId = intval($_POST['payment_id'] ?? 0);
                $verifyAction = $_POST['verify_action'] ?? 'approve';
                
                echo json_encode($handler->verifyPayment($paymentId, $verifyAction));
                break;
                
            case 'record_installment_payment':
                $data = [
                    'student_id' => $_POST['student_id'] ?? '',
                    'amount' => floatval($_POST['amount'] ?? 0),
                    'payment_method' => $_POST['payment_method'] ?? 'cash',
                    'or_number' => $_POST['or_number'] ?? '',
                    'remarks' => $_POST['remarks'] ?? ''
                ];
                
                $result = $handler->recordInstallmentPayment($data);
                echo json_encode($result);
                break;
                
            default:
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid action'
                ]);
                break;
        }
    } catch (Exception $e) {
        error_log("Payment Handler Error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Server error: ' . $e->getMessage()
        ]);
    }
    exit;
}

// Handle GET requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $handler = new PaymentHandler();
        
        $action = $_GET['action'] ?? '';
        
        switch ($action) {
            case 'get_student_info':
                $studentId = $_GET['student_id'] ?? '';
                $result = $handler->getStudentInfo($studentId);
                
                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'data' => $result
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Student not found'
                    ]);
                }
                break;
                
            default:
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid action'
                ]);
                break;
        }
    } catch (Exception $e) {
        error_log("Payment Handler Error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Server error: ' . $e->getMessage()
        ]);
    }
    exit;
}

// If no valid request method, return error
echo json_encode([
    'success' => false,
    'message' => 'Invalid request method'
]);
exit;
?>
