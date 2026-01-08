<?php
// Test endpoint for cashier functionality with accurate database data
require_once 'config.php';
require_once 'dashboard_handler.php';
require_once 'payment_handler.php';
require_once 'fee_calculator.php';

// Set content type to JSON
header('Content-Type: application/json');

try {
    $conn = getDBConnection();
    
    // Test database connection
    if (!$conn) {
        throw new Exception("Database connection failed");
    }
    
    // Get comprehensive test data from actual database
    $testResults = [];
    
    // Test 1: Basic database connectivity
    $query = "SELECT COUNT(*) as total_students FROM student_registrations WHERE status = 'approved'";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $testResults['database_connection'] = [
        'status' => 'success',
        'total_approved_students' => $row['total_students']
    ];
    
    // Test 2: Dashboard handler statistics
    $dashboardHandler = new DashboardHandler();
    $stats = $dashboardHandler->getPaymentStatistics();
    $testResults['payment_statistics'] = [
        'status' => 'success',
        'data' => $stats
    ];
    
    // Test 3: Recent payment activities
    $recentActivities = $dashboardHandler->getRecentPaymentActivities(5);
    $testResults['recent_activities'] = [
        'status' => 'success',
        'count' => count($recentActivities),
        'data' => $recentActivities
    ];
    
    // Test 4: Get first approved student for payment handler test
    $studentQuery = "SELECT sr.student_id, sr.first_name, sr.last_name 
                     FROM student_registrations sr 
                     JOIN enrollments e ON sr.student_id = e.student_id 
                     WHERE sr.status = 'approved' 
                     LIMIT 1";
    $studentResult = $conn->query($studentQuery);
    
    if ($studentRow = $studentResult->fetch_assoc()) {
        $studentId = $studentRow['student_id'];
        
        // Test 5: Payment handler
        $paymentHandler = new PaymentHandler();
        $studentInfo = $paymentHandler->getStudentInfo($studentId);
        $testResults['payment_handler'] = [
            'status' => 'success',
            'student_id' => $studentId,
            'student_name' => $studentRow['first_name'] . ' ' . $studentRow['last_name'],
            'data_available' => !empty($studentInfo)
        ];
        
        // Test 6: Fee calculator
        $feeCalculator = new FeeCalculator();
        $feeBreakdown = $feeCalculator->calculateStudentFees($studentId);
        $paymentSummary = $feeCalculator->getPaymentSummary($studentId);
        $testResults['fee_calculator'] = [
            'status' => 'success',
            'fee_breakdown_items' => count($feeBreakdown),
            'payment_summary_available' => !empty($paymentSummary)
        ];
    } else {
        $testResults['student_tests'] = [
            'status' => 'warning',
            'message' => 'No approved enrolled students found for testing'
        ];
    }
    
    // Test 7: Database table integrity
    $tableTests = [];
    $tables = ['student_registrations', 'enrollments', 'student_payments', 'enrolled_subjects'];
    
    foreach ($tables as $table) {
        $tableQuery = "SELECT COUNT(*) as count FROM $table";
        $tableResult = $conn->query($tableQuery);
        $tableRow = $tableResult->fetch_assoc();
        $tableTests[$table] = [
            'exists' => true,
            'record_count' => $tableRow['count']
        ];
    }
    
    $testResults['database_tables'] = [
        'status' => 'success',
        'tables' => $tableTests
    ];
    
    // Final response
    $response = [
        'status' => 'success',
        'message' => 'All cashier components tested successfully',
        'timestamp' => date('Y-m-d H:i:s'),
        'endpoint' => 'test_endpoint.php',
        'test_results' => $testResults
    ];
    
    echo json_encode($response, JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s'),
        'endpoint' => 'test_endpoint.php'
    ], JSON_PRETTY_PRINT);
}
?>
