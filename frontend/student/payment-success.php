<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header('Location: ../pages/login.php');
    exit;
}

// Get session ID from URL
$session_id = $_GET['session_id'] ?? null;

if (!$session_id) {
    $_SESSION['payment_error'] = true;
    $_SESSION['payment_error_message'] = "Invalid payment session.";
    header('Location: payment.php');
    exit;
}

// Retrieve payment session data
$payment_session = $_SESSION['payment_session'] ?? null;

// TODO: Verify payment with PayMongo API
// You can add verification logic here to confirm payment status

try {
    require_once('../../vendor/autoload.php');
    
    $client = new \GuzzleHttp\Client();
    $secret_key = 'sk_test_YZ73KEtTnM1dCdnq4whiNunW';
    
    $response = $client->request('GET', 'https://api.paymongo.com/v1/checkout_sessions/' . $session_id, [
        'headers' => [
            'accept' => 'application/json',
            'authorization' => 'Basic ' . base64_encode($secret_key . ':'),
        ]
    ]);
    
    $checkout_session = json_decode($response->getBody(), true);
    // Verify payment status here
} catch (Exception $e) {
    // Handle verification error
}

// Set success message
$_SESSION['payment_success'] = true;
$_SESSION['payment_message'] = "Your payment has been processed successfully! A confirmation email will be sent to your registered email address.";

if ($payment_session) {
    $_SESSION['payment_data'] = [
        'transaction_id' => $session_id,
        'amount' => $payment_session['total_amount']
    ];
}

// Clear payment session
unset($_SESSION['payment_session']);

// Redirect to payment page to show success message
header('Location: payment.php');
exit;
?>
