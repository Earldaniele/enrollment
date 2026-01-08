<?php
session_start();
require_once '../../frontend/includes/db_config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$province_code = $_GET['province_code'] ?? '';

if (empty($province_code)) {
    echo json_encode(['success' => false, 'message' => 'Province code is required']);
    exit;
}

try {
    // Load provinces data
    $provinces_file = __DIR__ . '/../../assets/refprovince.json';
    if (!file_exists($provinces_file)) {
        echo json_encode(['success' => false, 'message' => 'Provinces data not found']);
        exit;
    }
    
    $provinces_data = json_decode(file_get_contents($provinces_file), true);
    if (!$provinces_data) {
        echo json_encode(['success' => false, 'message' => 'Invalid provinces data']);
        exit;
    }
    
    // Find municipalities/cities for the province
    $municipalities = [];
    foreach ($provinces_data as $province) {
        if ($province['provCode'] == $province_code) {
            // Load citymun data for this province
            $citymun_file = __DIR__ . '/../../assets/citymun-by-province/citymun-' . $province_code . '.json';
            if (file_exists($citymun_file)) {
                $citymun_data = json_decode(file_get_contents($citymun_file), true);
                if ($citymun_data) {
                    $municipalities = $citymun_data;
                }
            }
            break;
        }
    }
    
    echo json_encode([
        'success' => true,
        'data' => $municipalities
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
