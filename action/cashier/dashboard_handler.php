<?php
require_once 'config.php';

class CashierDashboardHandler {
    private $conn;
    
    public function __construct() {
        $this->conn = getDBConnection();
    }
    
    /**
     * Get payment statistics for dashboard
     */
    public function getPaymentStatistics() {
        $stats = [
            'fully_paid' => 0,
            'installment' => 0,
            'pending_verification' => 0
        ];
        
        try {
            // Get fully paid students - only count enrolled students with approved registrations
            $query = "SELECT COUNT(DISTINCT e.student_id) as count 
                     FROM enrollments e
                     JOIN student_registrations sr ON e.student_id = sr.student_id
                     WHERE e.payment_status = 'paid' 
                     AND sr.status = 'approved'
                     AND e.enrollment_status = 'enrolled'";
            $result = $this->conn->query($query);
            if ($result && $row = $result->fetch_assoc()) {
                $stats['fully_paid'] = intval($row['count']);
            }
            
            // Get installment students (partial payment)
            $query = "SELECT COUNT(DISTINCT e.student_id) as count 
                     FROM enrollments e
                     JOIN student_registrations sr ON e.student_id = sr.student_id
                     WHERE e.payment_status = 'partial' 
                     AND sr.status = 'approved'
                     AND e.enrollment_status = 'enrolled'";
            $result = $this->conn->query($query);
            if ($result && $row = $result->fetch_assoc()) {
                $stats['installment'] = intval($row['count']);
            }
            
            // Get pending verification payments - count unique students with pending payments
            $query = "SELECT COUNT(DISTINCT sp.student_id) as count 
                     FROM student_payments sp
                     JOIN student_registrations sr ON sp.student_id = sr.student_id
                     WHERE sp.status = 'pending' 
                     AND sr.status = 'approved'";
            $result = $this->conn->query($query);
            if ($result && $row = $result->fetch_assoc()) {
                $stats['pending_verification'] = intval($row['count']);
            }
            
            return $stats;
        } catch (Exception $e) {
            error_log("Error getting payment statistics: " . $e->getMessage());
            return $stats;
        }
    }
    
    /**
     * Get recent payment activities
     */
    public function getRecentPaymentActivities($limit = 10) {
        $activities = [];
        
        try {
            $query = "SELECT 
                        sp.id,
                        sr.student_id,
                        CONCAT(sr.last_name, ', ', sr.first_name) as student_name,
                        sp.amount,
                        sp.payment_method,
                        sp.reference_number,
                        sp.status,
                        sp.paid_at,
                        sp.created_at,
                        e.total_assessment,
                        e.payment_status as enrollment_payment_status,
                        COALESCE(
                            (SELECT SUM(amount) FROM student_payments 
                             WHERE student_id = sp.student_id AND status = 'completed'), 
                            0
                        ) as total_paid
                      FROM student_payments sp
                      JOIN student_registrations sr ON sp.student_id = sr.student_id
                      LEFT JOIN enrollments e ON sp.student_id = e.student_id
                      WHERE sr.status = 'approved'
                      ORDER BY sp.created_at DESC
                      LIMIT ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $remaining_balance = ($row['total_assessment'] ?? 0) - $row['total_paid'];
                
                $activities[] = [
                    'student_id' => $row['student_id'],
                    'student_name' => $row['student_name'],
                    'amount' => number_format($row['amount'], 2),
                    'payment_method' => $this->formatPaymentMethod($row['payment_method']),
                    'reference_number' => $row['reference_number'] ?? 'N/A',
                    'status' => $this->formatPaymentStatus($row['status']),
                    'date' => date('M j, Y', strtotime($row['paid_at'] ?? $row['created_at'])),
                    'time' => date('h:i A', strtotime($row['paid_at'] ?? $row['created_at'])),
                    'total_assessment' => number_format($row['total_assessment'] ?? 0, 2),
                    'total_paid' => number_format($row['total_paid'], 2),
                    'remaining_balance' => number_format($remaining_balance, 2),
                    'enrollment_payment_status' => $row['enrollment_payment_status'] ?? 'unpaid'
                ];
            }
            
        } catch (Exception $e) {
            error_log("Error getting recent payment activities: " . $e->getMessage());
        }
        
        return $activities;
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
     * Format payment status for display
     */
    private function formatPaymentStatus($status) {
        $statuses = [
            'pending' => 'Pending',
            'completed' => 'Paid',
            'failed' => 'Failed',
            'cancelled' => 'Cancelled'
        ];
        
        return $statuses[$status] ?? ucfirst($status);
    }
}

// Handle AJAX requests
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    try {
        $handler = new CashierDashboardHandler();
        
        switch ($_GET['action']) {
            case 'get_stats':
                echo json_encode([
                    'success' => true,
                    'data' => $handler->getPaymentStatistics()
                ]);
                break;
                
            case 'get_recent_activities':
                $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
                echo json_encode([
                    'success' => true,
                    'data' => $handler->getRecentPaymentActivities($limit)
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
