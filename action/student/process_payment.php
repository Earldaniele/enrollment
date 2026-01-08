<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header('Location: ../../frontend/pages/login.php');
    exit;
}

// Validate form submission
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../frontend/student/payment.php');
    exit;
}

// Validate required fields
$required_fields = ['first_name', 'last_name', 'email', 'payment_type', 'amount'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
        $_SESSION['payment_error'] = true;
        $_SESSION['payment_error_message'] = "Please fill in all required fields.";
        header('Location: ../../frontend/student/payment.php');
        exit;
    }
}

// Sanitize and validate input data
$first_name = trim($_POST['first_name']);
$last_name = trim($_POST['last_name']);
$email = trim($_POST['email']);
$payment_type = trim($_POST['payment_type']);
$amount = floatval($_POST['amount']);
$description = isset($_POST['description']) ? trim($_POST['description']) : '';

// Validate amount
if ($amount < 1) {
    $_SESSION['payment_error'] = true;
    $_SESSION['payment_error_message'] = "Invalid payment amount.";
    header('Location: ../../frontend/student/payment.php');
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['payment_error'] = true;
    $_SESSION['payment_error_message'] = "Invalid email address.";
    header('Location: ../../frontend/student/payment.php');
    exit;
}

// Calculate total amount with processing fee (3.5% + ₱15)
$processing_fee = ($amount * 0.035) + 15;
$total_amount = $amount + $processing_fee;

// Convert to centavos for PayMongo (PHP amount * 100)
$amount_centavos = intval($total_amount * 100);

try {
    // PayMongo API Integration
    require_once __DIR__ . '/../../vendor/autoload.php';
    
    $client = new \GuzzleHttp\Client();
    
    // PayMongo API credentials (use your test keys)
    $public_key = 'pk_test_pMzBbNW5ALFfjUnNZjB7mFU6'; // Replace with your actual test public key
    $secret_key = 'sk_test_YZ73KEtTnM1dCdnq4whiNunW'; // Replace with your actual test secret key
    
    // Debug: Log the request data (remove in production)
    error_log("PayMongo Request Data: " . json_encode([
        'amount' => $amount_centavos,
        'currency' => 'PHP',
        'description' => $description ?: ucfirst($payment_type) . ' Fee Payment'
    ]));
    
    // Create PayMongo Checkout Session
    $checkout_data = [
        'data' => [
            'type' => 'checkout_session',
            'attributes' => [
                'amount' => $amount_centavos,
                'currency' => 'PHP',
                'description' => $description ?: ucfirst($payment_type) . ' Fee Payment',
                'line_items' => [
                    [
                        'name' => ucfirst($payment_type) . ' Fee',
                        'amount' => intval($amount * 100), // Original amount
                        'currency' => 'PHP',
                        'quantity' => 1
                    ],
                    [
                        'name' => 'Processing Fee',
                        'amount' => intval($processing_fee * 100), // Processing fee
                        'currency' => 'PHP',
                        'quantity' => 1
                    ]
                ],
                'payment_method_types' => [
                    'gcash',
                    'grab_pay',
                    'paymaya',
                    'card'
                ],
                'success_url' => 'http://localhost/enrollmentsystem/frontend/student/payment-success.php?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => 'http://localhost/enrollmentsystem/frontend/student/payment.php?cancelled=1',
                'billing' => [
                    'name' => $first_name . ' ' . $last_name,
                    'email' => $email
                ]
            ]
        ]
    ];
    
    // Make API request to create checkout session
    $response = $client->request('POST', 'https://api.paymongo.com/v1/checkout_sessions', [
        'headers' => [
            'Content-Type' => 'application/json',
            'accept' => 'application/json',
            'authorization' => 'Basic ' . base64_encode($secret_key . ':'),
        ],
        'json' => $checkout_data
    ]);
    
    $response_body = json_decode($response->getBody(), true);
    
    if ($response->getStatusCode() === 200 || $response->getStatusCode() === 201) {
        // Success - get checkout URL
        $checkout_url = $response_body['data']['attributes']['checkout_url'];
        
        // Store payment session data for tracking
        $_SESSION['payment_session'] = [
            'checkout_session_id' => $response_body['data']['id'],
            'amount' => $amount,
            'total_amount' => $total_amount,
            'payment_type' => $payment_type,
            'student_email' => $email,
            'student_name' => $first_name . ' ' . $last_name,
            'description' => $description,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        // Redirect to PayMongo checkout
        header('Location: ' . $checkout_url);
        exit;
        
    } else {
        // API Error
        $_SESSION['payment_error'] = true;
        $_SESSION['payment_error_message'] = "Failed to create payment session. Please try again.";
        header('Location: ../../frontend/student/payment.php');
        exit;
    }

} catch (\GuzzleHttp\Exception\RequestException $e) {
    // Handle Guzzle HTTP exceptions
    $error_message = "Payment gateway error. Please try again later.";
    
    // Log the actual error for debugging
    error_log("PayMongo API Error: " . $e->getMessage());
    
    if ($e->hasResponse()) {
        $error_response = json_decode($e->getResponse()->getBody(), true);
        error_log("PayMongo API Response: " . json_encode($error_response));
        
        if (isset($error_response['errors']) && is_array($error_response['errors'])) {
            $error_message = $error_response['errors'][0]['detail'] ?? $error_message;
        }
    }
    
    $_SESSION['payment_error'] = true;
    $_SESSION['payment_error_message'] = $error_message . " (Debug: Check error logs)";
    header('Location: ../../frontend/student/payment.php');
    exit;
    
} catch (Exception $e) {
    // Handle other exceptions
    error_log("General Error: " . $e->getMessage());
    $_SESSION['payment_error'] = true;
    $_SESSION['payment_error_message'] = "An unexpected error occurred: " . $e->getMessage();
    header('Location: ../../frontend/student/payment.php');
    exit;
}
?>