<?php
// Test file to check if Guzzle and PayMongo API are accessible
require_once __DIR__ . '/../../vendor/autoload.php';

try {
    $client = new \GuzzleHttp\Client();
    
    // Simple test to check if we can reach PayMongo
    $response = $client->request('GET', 'https://api.paymongo.com/v1/payment_methods', [
        'headers' => [
            'accept' => 'application/json',
        ]
    ]);
    
    echo "✅ Guzzle is working!\n";
    echo "✅ Can reach PayMongo API!\n";
    echo "Status Code: " . $response->getStatusCode() . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
