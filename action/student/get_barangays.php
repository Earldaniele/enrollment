<?php
session_start();
require_once '../../frontend/includes/db_config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$citymun_code = $_GET['citymun_code'] ?? '';

if (empty($citymun_code)) {
    echo json_encode(['success' => false, 'message' => 'City/Municipality code is required']);
    exit;
}

try {
    // Extract province code from citymun code (first 4 digits)
    $province_code = substr($citymun_code, 0, 4);
    
    // Load barangays data for this province
    $barangays_file = __DIR__ . '/../../assets/barangay-by-province/barangays-' . $province_code . '.json';
    
    if (!file_exists($barangays_file)) {
        echo json_encode(['success' => false, 'message' => 'Barangays data not found for this area']);
        exit;
    }
    
    $barangays_data = json_decode(file_get_contents($barangays_file), true);
    if (!$barangays_data) {
        echo json_encode(['success' => false, 'message' => 'Invalid barangays data']);
        exit;
    }
    
    // Filter barangays for the specific city/municipality
    $barangays = [];
    foreach ($barangays_data as $barangay) {
        if ($barangay['citymunCode'] == $citymun_code) {
            $barangays[] = $barangay;
        }
    }
    
    echo json_encode([
        'success' => true,
        'data' => $barangays
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
