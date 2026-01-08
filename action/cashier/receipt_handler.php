<?php
require_once 'config.php';

class ReceiptHandler {
    private $conn;
    
    public function __construct() {
        $this->conn = getDBConnection();
    }
    
    /**
     * Get receipt records with filters
     */
    public function getReceipts($filters = [], $page = 1, $limit = 10) {
        try {
            $whereConditions = ["sp.status = 'completed'", "sr.status = 'approved'"];
            $params = [];
            $types = "";
            
            // Date range filter
            if (!empty($filters['date_range']) && $filters['date_range'] !== 'all') {
                switch ($filters['date_range']) {
                    case 'today':
                        $whereConditions[] = "DATE(sp.paid_at) = CURDATE()";
                        break;
                    case 'this_week':
                        $whereConditions[] = "WEEK(sp.paid_at) = WEEK(CURDATE()) AND YEAR(sp.paid_at) = YEAR(CURDATE())";
                        break;
                    case 'this_month':
                        $whereConditions[] = "MONTH(sp.paid_at) = MONTH(CURDATE()) AND YEAR(sp.paid_at) = YEAR(CURDATE())";
                        break;
                }
            }
            
            // Payment type filter
            if (!empty($filters['payment_type']) && $filters['payment_type'] !== 'all') {
                $whereConditions[] = "sp.payment_method = ?";
                $params[] = $filters['payment_type'];
                $types .= "s";
            }
            
            // Search filter
            if (!empty($filters['search'])) {
                $search = "%{$filters['search']}%";
                $whereConditions[] = "(sp.reference_number LIKE ? OR sr.student_id LIKE ? OR CONCAT(sr.last_name, ', ', sr.first_name) LIKE ?)";
                $params[] = $search;
                $params[] = $search;
                $params[] = $search;
                $types .= "sss";
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
            
            // Get receipts with pagination
            $offset = ($page - 1) * $limit;
            $query = "SELECT 
                        sp.id,
                        sp.reference_number as or_number,
                        sp.paid_at as date,
                        sr.student_id,
                        CONCAT(sr.last_name, ', ', sr.first_name) as student_name,
                        sp.description,
                        sp.amount,
                        sp.payment_method,
                        sp.cashier_name as issued_by
                      FROM student_payments sp
                      JOIN student_registrations sr ON sp.student_id = sr.student_id
                      WHERE $whereClause
                      ORDER BY sp.paid_at DESC
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
            
            $receipts = [];
            while ($row = $result->fetch_assoc()) {
                $receipts[] = [
                    'id' => $row['id'],
                    'or_number' => $this->generateORNumber($row['or_number'], $row['id']),
                    'date' => date('M j, Y', strtotime($row['date'])),
                    'student_id' => $row['student_id'],
                    'student_name' => $row['student_name'],
                    'description' => $row['description'],
                    'amount' => number_format($row['amount'], 2),
                    'payment_method' => $this->formatPaymentMethod($row['payment_method']),
                    'issued_by' => $row['issued_by']
                ];
            }
            
            return [
                'receipts' => $receipts,
                'total_count' => $totalCount,
                'total_pages' => ceil($totalCount / $limit),
                'current_page' => $page
            ];
            
        } catch (Exception $e) {
            error_log("Error getting receipts: " . $e->getMessage());
            return [
                'receipts' => [],
                'total_count' => 0,
                'total_pages' => 0,
                'current_page' => 1
            ];
        }
    }
    
    /**
     * Get installment tracking data
     */
    public function getInstallmentTracking($filters = [], $page = 1, $limit = 10) {
        try {
            $whereConditions = ["sr.status = 'approved'"];
            $params = [];
            $types = "";
            
            // Status filter
            if (!empty($filters['status']) && $filters['status'] !== 'all') {
                if ($filters['status'] === 'has_balance') {
                    $whereConditions[] = "((e.total_assessment - COALESCE(paid_amounts.total_paid, 0)) > 0)";
                } elseif ($filters['status'] === 'fully_paid') {
                    $whereConditions[] = "e.payment_status = 'paid'";
                }
            }
            
            // Course filter
            if (!empty($filters['course']) && $filters['course'] !== 'all') {
                $whereConditions[] = "sr.desired_course LIKE ?";
                $params[] = "%{$filters['course']}%";
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
                          LEFT JOIN (
                              SELECT student_id, SUM(amount) as total_paid 
                              FROM student_payments 
                              WHERE status = 'completed' 
                              GROUP BY student_id
                          ) paid_amounts ON sr.student_id = paid_amounts.student_id
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
            
            // Get installment tracking with pagination
            $offset = ($page - 1) * $limit;
            $query = "SELECT 
                        sr.student_id,
                        CONCAT(sr.last_name, ', ', sr.first_name) as student_name,
                        CONCAT(e.year_level, ' / ', e.semester) as course_year,
                        e.total_assessment,
                        COALESCE(paid_amounts.total_paid, 0) as amount_paid,
                        (e.total_assessment - COALESCE(paid_amounts.total_paid, 0)) as remaining_balance,
                        COALESCE(last_payment.paid_at, 'N/A') as last_payment_date,
                        CASE 
                            WHEN e.payment_status = 'paid' THEN 'Fully Paid'
                            WHEN COALESCE(paid_amounts.total_paid, 0) > 0 THEN 'Has Balance'
                            ELSE 'No Payment'
                        END as status
                      FROM student_registrations sr
                      LEFT JOIN enrollments e ON sr.student_id = e.student_id
                      LEFT JOIN (
                          SELECT student_id, SUM(amount) as total_paid 
                          FROM student_payments 
                          WHERE status = 'completed' 
                          GROUP BY student_id
                      ) paid_amounts ON sr.student_id = paid_amounts.student_id
                      LEFT JOIN (
                          SELECT student_id, MAX(paid_at) as paid_at
                          FROM student_payments 
                          WHERE status = 'completed'
                          GROUP BY student_id
                      ) last_payment ON sr.student_id = last_payment.student_id
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
            
            $tracking = [];
            while ($row = $result->fetch_assoc()) {
                $tracking[] = [
                    'student_id' => $row['student_id'],
                    'student_name' => $row['student_name'],
                    'course_year' => $row['course_year'],
                    'total_fees' => number_format($row['total_assessment'] ?? 0, 2),
                    'amount_paid' => number_format($row['amount_paid'], 2),
                    'remaining_balance' => number_format($row['remaining_balance'], 2),
                    'last_payment_date' => $row['last_payment_date'] !== 'N/A' ? 
                                          date('M j, Y', strtotime($row['last_payment_date'])) : 'N/A',
                    'status' => $row['status']
                ];
            }
            
            return [
                'tracking' => $tracking,
                'total_count' => $totalCount,
                'total_pages' => ceil($totalCount / $limit),
                'current_page' => $page
            ];
            
        } catch (Exception $e) {
            error_log("Error getting installment tracking: " . $e->getMessage());
            return [
                'tracking' => [],
                'total_count' => 0,
                'total_pages' => 0,
                'current_page' => 1
            ];
        }
    }
    
    /**
     * Generate OR number based on payment ID
     */
    private function generateORNumber($referenceNumber, $paymentId) {
        return "OR-" . date('Y') . "-" . sprintf('%04d', $paymentId);
    }
    
    /**
     * Format payment method for display
     */
    private function formatPaymentMethod($method) {
        $methods = [
            'cash' => 'Cash',
            'check' => 'Check',
            'bank_transfer' => 'Bank Transfer',
            'online' => 'Online'
        ];
        
        return $methods[$method] ?? ucfirst($method);
    }
    
    /**
     * Get detailed receipt information
     */
    public function getReceiptDetails($orNumber) {
        try {
            $sql = "SELECT 
                        sp.id,
                        sp.student_id,
                        sp.amount,
                        sp.payment_method,
                        sp.reference_number,
                        sp.paid_at as payment_date,
                        CONCAT(sr.last_name, ', ', sr.first_name, 
                               CASE WHEN sr.middle_name IS NOT NULL THEN CONCAT(' ', sr.middle_name) ELSE '' END) as student_name,
                        sr.desired_course as course,
                        COALESCE(e.semester, 'Not Enrolled') as semester,
                        sp.cashier_name,
                        sp.description,
                        sp.created_at,
                        sp.notes
                    FROM student_payments sp
                    LEFT JOIN student_registrations sr ON sp.student_id = sr.student_id
                    LEFT JOIN enrollments e ON sp.student_id = e.student_id
                    WHERE sp.id = ? AND sp.status = 'completed'";
                    
            // Extract payment ID from OR number (OR-2025-0001 -> 1)
            $paymentId = intval(substr($orNumber, strrpos($orNumber, '-') + 1));
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('i', $paymentId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                return [
                    'success' => true,
                    'data' => [
                        'or_number' => $orNumber,
                        'student_id' => $row['student_id'],
                        'student_name' => $row['student_name'],
                        'course' => $row['course'],
                        'semester' => $row['semester'],
                        'description' => $row['description'],
                        'amount' => number_format($row['amount'], 2),
                        'payment_method' => $this->formatPaymentMethod($row['payment_method']),
                        'payment_date' => date('Y-m-d', strtotime($row['payment_date'])),
                        'cashier_name' => $row['cashier_name'],
                        'notes' => $row['notes']
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Receipt not found'
                ];
            }
            
        } catch (Exception $e) {
            error_log("Error getting receipt details: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Update receipt information
     */
    public function updateReceipt($data) {
        try {
            // Debug logging
            error_log("UpdateReceipt called with data: " . json_encode($data));
            
            // Extract payment ID from OR number
            $paymentId = intval(substr($data['or_number'], strrpos($data['or_number'], '-') + 1));
            error_log("Extracted payment ID: " . $paymentId . " from OR: " . $data['or_number']);
            
            $sql = "UPDATE student_payments 
                    SET amount = ?, 
                        payment_method = ?,
                        paid_at = ?,
                        description = ?,
                        cashier_name = ?,
                        notes = ?
                    WHERE id = ? AND status = 'completed'";
                    
            error_log("Update SQL: " . $sql);
            error_log("Parameters: amount=" . $data['amount'] . ", method=" . $data['payment_method'] . ", date=" . $data['date'] . ", desc=" . $data['description'] . ", cashier=" . $data['cashier'] . ", notes=" . $data['notes'] . ", id=" . $paymentId);
                    
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('dsssssi', 
                $data['amount'], 
                $data['payment_method'], 
                $data['date'],
                $data['description'],
                $data['cashier'],
                $data['notes'],
                $paymentId
            );
            
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    return [
                        'success' => true,
                        'message' => 'Receipt updated successfully'
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Receipt not found or no changes made'
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to update receipt'
                ];
            }
            
        } catch (Exception $e) {
            error_log("Error updating receipt: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }
}

// Handle AJAX requests
$action = $_GET['action'] ?? $_POST['action'] ?? '';
if (!empty($action)) {
    header('Content-Type: application/json');
    $handler = new ReceiptHandler();
    
    switch ($action) {
        case 'get_receipts':
            $filters = [
                'date_range' => $_GET['date_range'] ?? 'all',
                'payment_type' => $_GET['payment_type'] ?? 'all',
                'search' => $_GET['search'] ?? ''
            ];
            $page = intval($_GET['page'] ?? 1);
            $limit = intval($_GET['limit'] ?? 10);
            
            echo json_encode([
                'success' => true,
                'data' => $handler->getReceipts($filters, $page, $limit)
            ]);
            break;
            
        case 'get_installment_tracking':
            $filters = [
                'status' => $_GET['status'] ?? 'all',
                'course' => $_GET['course'] ?? 'all',
                'search' => $_GET['search'] ?? ''
            ];
            $page = intval($_GET['page'] ?? 1);
            $limit = intval($_GET['limit'] ?? 10);
            
            echo json_encode([
                'success' => true,
                'data' => $handler->getInstallmentTracking($filters, $page, $limit)
            ]);
            break;
            
        case 'get_receipt_details':
            $orNumber = $_GET['or_number'] ?? '';
            echo json_encode($handler->getReceiptDetails($orNumber));
            break;
            
        case 'update_receipt':
            $data = [
                'or_number' => $_POST['or_number'] ?? '',
                'amount' => floatval($_POST['amount'] ?? 0),
                'payment_method' => $_POST['payment_method'] ?? '',
                'date' => $_POST['date'] ?? '',
                'description' => $_POST['description'] ?? 'Tuition Fee',
                'cashier' => $_POST['cashier'] ?? 'Maria Reyes',
                'notes' => $_POST['notes'] ?? ''
            ];
            echo json_encode($handler->updateReceipt($data));
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action'
            ]);
            break;
    }
    exit;
}
?>
