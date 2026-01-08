<?php
/**
 * Global Logout Handler
 * Handles logout for all user types: students, evaluators, admins
 * Supports both AJAX (JSON) and direct browser requests
 */

session_start();

// Set headers for JSON response if requested via AJAX
$is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

$content_type = $_GET['format'] ?? ($is_ajax ? 'json' : 'html');

if ($content_type === 'json' || $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
}

/**
 * Determine user type and perform appropriate logout
 */
function performLogout() {
    $logout_info = [
        'previous_user_type' => 'unknown',
        'previous_user_id' => null,
        'previous_user_name' => 'Unknown User'
    ];

    // Detect user type and gather info before clearing session
    if (isset($_SESSION['evaluator_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'evaluator') {
        // Evaluator logout
        $logout_info['previous_user_type'] = 'evaluator';
        $logout_info['previous_user_id'] = $_SESSION['evaluator_id'];
        $logout_info['previous_user_name'] = $_SESSION['evaluator_name'] ?? 'NCST Evaluator';
        
        // Clear evaluator session variables
        unset($_SESSION['evaluator_id']);
        unset($_SESSION['evaluator_email']);
        unset($_SESSION['evaluator_name']);
        unset($_SESSION['evaluator_role']);
        
    } elseif (isset($_SESSION['email']) && (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'student' || !isset($_SESSION['evaluator_id']))) {
        // Student logout (students have email and user_type=student, or email without evaluator_id)
        $logout_info['previous_user_type'] = 'student';
        $logout_info['previous_user_id'] = $_SESSION['student_id'] ?? $_SESSION['email'];
        $logout_info['previous_user_name'] = ($_SESSION['first_name'] ?? '') . ' ' . ($_SESSION['last_name'] ?? '');
        $logout_info['previous_user_name'] = trim($logout_info['previous_user_name']) ?: 'Student';
        
        // Clear student session variables
        unset($_SESSION['email']);
        unset($_SESSION['student_id']);
        unset($_SESSION['first_name']);
        unset($_SESSION['last_name']);
        unset($_SESSION['phone']);
        
    } elseif (isset($_SESSION['admin_id'])) {
        // Admin logout (if you have admin system)
        $logout_info['previous_user_type'] = 'admin';
        $logout_info['previous_user_id'] = $_SESSION['admin_id'];
        $logout_info['previous_user_name'] = $_SESSION['admin_name'] ?? 'Administrator';
        
        // Clear admin session variables
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_email']);
        unset($_SESSION['admin_name']);
        
    } else {
        // Generic logout - clear everything
        $logout_info['previous_user_type'] = 'guest';
    }

    // Clear common session variables
    unset($_SESSION['user_type']);
    unset($_SESSION['last_activity']);
    unset($_SESSION['login_time']);

    // Destroy session completely if no other data exists
    if (empty($_SESSION)) {
        session_destroy();
    }

    return $logout_info;
}

/**
 * Get appropriate redirect URL based on user type
 */
function getRedirectUrl($user_type) {
    switch ($user_type) {
        case 'evaluator':
            return '/enrollmentsystem/frontend/evaluator/login.php';
        case 'student':
            return '/enrollmentsystem/frontend/pages/login.php';
        case 'admin':
            return '/enrollmentsystem/admin/login.php';
        default:
            return '/enrollmentsystem/index.php';
    }
}

/**
 * Handle the logout process
 */
try {
    // Perform logout and get info
    $logout_info = performLogout();
    
    $redirect_url = getRedirectUrl($logout_info['previous_user_type']);
    
    // Prepare response data
    $response_data = [
        'success' => true,
        'message' => 'Logout successful',
        'user_type' => $logout_info['previous_user_type'],
        'user_name' => $logout_info['previous_user_name'],
        'redirect_url' => $redirect_url,
        'timestamp' => date('Y-m-d H:i:s')
    ];

    // Handle response based on request type
    if ($content_type === 'json') {
        // AJAX/JSON response
        echo json_encode($response_data);
        
    } else {
        // Direct browser request - show HTML page with redirect
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Logout - NCST</title>
            <link href="/enrollmentsystem/assets/css/bootstrap.min.css" rel="stylesheet">
            <link href="/enrollmentsystem/assets/css/bootstrap-icons.css" rel="stylesheet">
            <style>
                body {
                    background: linear-gradient(135deg, rgb(37, 52, 117) 0%, rgb(45, 62, 140) 100%);
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                }
                .logout-card {
                    background: white;
                    border-radius: 15px;
                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
                    padding: 2rem;
                    text-align: center;
                    max-width: 400px;
                    width: 100%;
                }
                .logout-icon {
                    font-size: 4rem;
                    color: #28a745;
                    margin-bottom: 1rem;
                }
                .countdown {
                    font-size: 1.2rem;
                    font-weight: bold;
                    color: rgb(37, 52, 117);
                }
            </style>
        </head>
        <body>
            <div class="logout-card">
                <div class="logout-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <h2 class="mb-3">Logout Successful</h2>
                <p class="text-muted mb-3">
                    <?php echo htmlspecialchars($logout_info['previous_user_name']); ?>, you have been successfully logged out.
                </p>
                <p class="countdown mb-4">
                    Redirecting in <span id="countdown">3</span> seconds...
                </p>
                <div class="d-grid gap-2">
                    <a href="<?php echo htmlspecialchars($redirect_url); ?>" class="btn btn-primary">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Go to Login
                    </a>
                    <a href="/enrollmentsystem/index.php" class="btn btn-outline-secondary">
                        <i class="bi bi-house me-2"></i>Back to Home
                    </a>
                </div>
            </div>

            <script>
                // Countdown and auto-redirect
                let timeLeft = 3;
                const countdownElement = document.getElementById('countdown');
                
                const countdown = setInterval(() => {
                    timeLeft--;
                    countdownElement.textContent = timeLeft;
                    
                    if (timeLeft <= 0) {
                        clearInterval(countdown);
                        window.location.href = '<?php echo addslashes($redirect_url); ?>';
                    }
                }, 1000);
            </script>
        </body>
        </html>
        <?php
    }

} catch (Exception $e) {
    // Error handling
    error_log("Global logout error: " . $e->getMessage());
    
    if ($content_type === 'json') {
        echo json_encode([
            'success' => false,
            'message' => 'Logout failed: ' . $e->getMessage(),
            'redirect_url' => '/enrollmentsystem/index.php'
        ]);
    } else {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Logout Error - NCST</title>
            <link href="/enrollmentsystem/assets/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body class="bg-light">
            <div class="container py-5">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="alert alert-danger">
                            <h4>Logout Error</h4>
                            <p>An error occurred during logout. Please close your browser for security.</p>
                            <a href="/enrollmentsystem/index.php" class="btn btn-primary">Back to Home</a>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
    }
}
?>
