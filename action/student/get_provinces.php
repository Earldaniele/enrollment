<?php
session_start();
require_once '../../frontend/includes/db_config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$region_code = $_GET['region_code'] ?? '';

if (empty($region_code)) {
    echo json_encode(['success' => false, 'message' => 'Region code is required']);
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
    
    // Filter provinces by region
    $provinces = [];
    foreach ($provinces_data as $province) {
        if ($province['regCode'] == $region_code) {
            $provinces[] = $province;
        }
    }
    
    echo json_encode([
        'success' => true,
        'data' => $provinces
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
