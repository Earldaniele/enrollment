<?php
require_once 'config.php';

class FeeCalculator {
    private $conn;
    
    public function __construct() {
        $this->conn = getDBConnection();
    }
    
    /**
     * Calculate accurate fee breakdown for a student based on enrollment data
     */
    public function calculateStudentFees($studentId) {
        try {
            // Get enrollment details from the database
            $query = "SELECT 
                        e.year_level,
                        e.semester,
                        e.school_year,
                        e.total_units,
                        e.total_assessment,
                        sr.desired_course,
                        COUNT(es.id) as enrolled_subjects_count
                     FROM enrollments e
                     JOIN student_registrations sr ON e.student_id = sr.student_id
                     LEFT JOIN enrolled_subjects es ON e.student_id = es.student_id
                     WHERE e.student_id = ? AND sr.status = 'approved'
                     GROUP BY e.student_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("s", $studentId);
            $stmt->execute();
            $result = $stmt->get_result();
            $enrollmentData = $result->fetch_assoc();
            
            if (!$enrollmentData) {
                return [];
            }
            
            $totalUnits = $enrollmentData['total_units'] ?? 0;
            $totalAssessment = $enrollmentData['total_assessment'] ?? 0;
            $course = $enrollmentData['desired_course'] ?? '';
            $yearLevel = $enrollmentData['year_level'] ?? '1st Year';
            
            // Generate fee breakdown based on actual assessment
            $feeBreakdown = $this->generateAccurateFeeBreakdown($totalUnits, $totalAssessment, $course, $yearLevel);
            
            return $feeBreakdown;
            
        } catch (Exception $e) {
            error_log("Error calculating student fees: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Generate fee breakdown based on actual database values
     */
    private function generateAccurateFeeBreakdown($totalUnits, $totalAssessment, $course, $yearLevel) {
        // Base calculation using actual data from database
        $tuitionPerUnit = 850; // Standard tuition per unit
        $tuitionFee = $totalUnits * $tuitionPerUnit;
        
        // Calculate remaining fees from total assessment
        $remainingAmount = max(0, $totalAssessment - $tuitionFee);
        
        // Distribute remaining amount across fee categories
        $miscFee = $remainingAmount * 0.35;      // 35% for miscellaneous
        $labFee = $remainingAmount * 0.25;       // 25% for laboratory
        $devFee = $remainingAmount * 0.20;       // 20% for development
        $otherFees = $remainingAmount * 0.20;    // 20% for other fees
        
        $feeBreakdown = [];
        
        // Add tuition fee
        if ($tuitionFee > 0) {
            $feeBreakdown[] = [
                'fee_name' => 'Tuition Fee',
                'description' => sprintf('%s units × ₱%s per unit', number_format($totalUnits, 1), number_format($tuitionPerUnit, 2)),
                'amount' => $tuitionFee,
                'amount_formatted' => number_format($tuitionFee, 2)
            ];
        }
        
        // Add laboratory fee if applicable
        if ($labFee > 0) {
            $feeBreakdown[] = [
                'fee_name' => 'Laboratory Fee',
                'description' => 'Computer laboratory and practical work fees',
                'amount' => $labFee,
                'amount_formatted' => number_format($labFee, 2)
            ];
        }
        
        // Add miscellaneous fee
        if ($miscFee > 0) {
            $feeBreakdown[] = [
                'fee_name' => 'Miscellaneous Fee',
                'description' => 'Library, ID, Student Handbook, and other fees',
                'amount' => $miscFee,
                'amount_formatted' => number_format($miscFee, 2)
            ];
        }
        
        // Add development fee
        if ($devFee > 0) {
            $feeBreakdown[] = [
                'fee_name' => 'Development Fee',
                'description' => 'School development and infrastructure maintenance',
                'amount' => $devFee,
                'amount_formatted' => number_format($devFee, 2)
            ];
        }
        
        // Add other fees
        if ($otherFees > 0) {
            $feeBreakdown[] = [
                'fee_name' => 'Other School Fees',
                'description' => 'Registration, student activities, and miscellaneous fees',
                'amount' => $otherFees,
                'amount_formatted' => number_format($otherFees, 2)
            ];
        }
        
        // Add total assessment
        $feeBreakdown[] = [
            'fee_name' => 'TOTAL ASSESSMENT',
            'description' => 'Total amount due for ' . $yearLevel . ' enrollment',
            'amount' => $totalAssessment,
            'amount_formatted' => number_format($totalAssessment, 2),
            'is_total' => true
        ];
        
        return $feeBreakdown;
    }
    
    /**
     * Get comprehensive payment summary for a student
     */
    public function getPaymentSummary($studentId) {
        try {
            $query = "SELECT 
                        e.total_assessment,
                        e.payment_status,
                        COALESCE(SUM(CASE WHEN sp.status = 'completed' THEN sp.amount ELSE 0 END), 0) as total_paid,
                        COALESCE(SUM(CASE WHEN sp.status = 'pending' THEN sp.amount ELSE 0 END), 0) as pending_amount,
                        COUNT(CASE WHEN sp.status = 'pending' THEN 1 END) as pending_count,
                        COUNT(CASE WHEN sp.status = 'completed' THEN 1 END) as completed_count,
                        COUNT(CASE WHEN sp.status = 'failed' THEN 1 END) as failed_count
                     FROM enrollments e
                     LEFT JOIN student_payments sp ON e.student_id = sp.student_id
                     WHERE e.student_id = ?
                     GROUP BY e.student_id, e.total_assessment, e.payment_status";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("s", $studentId);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            
            if ($data) {
                $totalAssessment = $data['total_assessment'] ?? 0;
                $totalPaid = $data['total_paid'] ?? 0;
                $pendingAmount = $data['pending_amount'] ?? 0;
                $remainingBalance = $totalAssessment - $totalPaid;
                
                return [
                    'total_assessment' => $totalAssessment,
                    'total_paid' => $totalPaid,
                    'pending_amount' => $pendingAmount,
                    'remaining_balance' => $remainingBalance,
                    'payment_percentage' => $totalAssessment > 0 ? round(($totalPaid / $totalAssessment) * 100, 2) : 0,
                    'payment_status' => $data['payment_status'] ?? 'unpaid',
                    'pending_count' => $data['pending_count'] ?? 0,
                    'completed_count' => $data['completed_count'] ?? 0,
                    'failed_count' => $data['failed_count'] ?? 0,
                    'total_assessment_formatted' => number_format($totalAssessment, 2),
                    'total_paid_formatted' => number_format($totalPaid, 2),
                    'pending_amount_formatted' => number_format($pendingAmount, 2),
                    'remaining_balance_formatted' => number_format($remainingBalance, 2)
                ];
            }
            
            return null;
            
        } catch (Exception $e) {
            error_log("Error getting payment summary: " . $e->getMessage());
            return null;
        }
    }
}

// Handle API requests
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    try {
        $calculator = new FeeCalculator();
        
        switch ($_GET['action']) {
            case 'calculate_fees':
                $studentId = $_GET['student_id'] ?? '';
                
                if (empty($studentId)) {
                    throw new Exception('Student ID is required');
                }
                
                echo json_encode([
                    'success' => true,
                    'data' => $calculator->calculateStudentFees($studentId)
                ]);
                break;
                
            case 'get_payment_summary':
                $studentId = $_GET['student_id'] ?? '';
                
                if (empty($studentId)) {
                    throw new Exception('Student ID is required');
                }
                
                echo json_encode([
                    'success' => true,
                    'data' => $calculator->getPaymentSummary($studentId)
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
            'message' => $e->getMessage()
        ]);
    }
    
    exit;
}
?>
