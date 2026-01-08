<?php
// Handle login POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../includes/auth.php';
    require_once '../includes/db_config.php';
    
    header('Content-Type: application/json');
    
    $input = json_decode(file_get_contents('php://input'), true);
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Email and password are required']);
        exit;
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit;
    }
    
    // Check if user exists in student_accounts table
    $stmt = $conn->prepare("SELECT id, email, password, first_name, last_name FROM student_accounts WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Account not found. Please register first.']);
        exit;
    }
    
    $user = $result->fetch_assoc();
    
    // Verify password
    if (!password_verify($password, $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        exit;
    }
    
    // Set user session with proper authentication
    setCurrentUser($email);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Login successful',
        'data' => [
            'email' => $email,
            'name' => $user['first_name'] . ' ' . $user['last_name']
        ]
    ]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include '../includes/header.php'; ?>
<body class="student-dashboard">
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <!-- Alert Box at the top -->
                <div class="alert alert-danger d-none" id="errorAlert" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Error!</strong> <span id="errorMessage"></span>
                </div>
                
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold">Welcome Back</h2>
                            <p class="text-muted">Sign in to your NCST account</p>
                        </div>
                        
                        <!-- Login Form -->
                        <form id="loginForm" method="POST" action="">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" maxlength="250" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" minlength="8" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="rememberMe" name="rememberMe">
                                <label class="form-check-label" for="rememberMe">
                                    Remember me
                                </label>
                            </div>
                            
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">Sign In</button>
                            </div>
                            
                            <div class="text-center mb-3">
                                <a href="#" class="text-primary text-decoration-none" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Forgot your password?</a>
                            </div>
                            
                            <div class="text-center">
                                <p class="text-muted">Don't have an account? <a href="register.php" class="text-primary text-decoration-none">Create one here</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Forgot Password Modal -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="forgotPasswordModalLabel">Reset Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="forgotPasswordForm">
                        <div class="mb-3">
                            <label for="resetEmail" class="form-label">Enter your email address</label>
                            <input type="email" class="form-control" id="resetEmail" name="resetEmail" required>
                            <div class="form-text">We'll send you a link to reset your password.</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="sendResetBtn">Send Reset Link</button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for form functionality -->
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });

        // Login form submission
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            // Reset alerts
            document.getElementById('errorAlert').classList.add('d-none');
            
            // Email validation
            if (email.trim().length === 0) {
                showError('Email address is required.');
                return;
            }
            
            if (email.length > 250) {
                showError('Email address cannot exceed 250 characters.');
                return;
            }
            
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showError('Please enter a valid email address.');
                return;
            }
            
            // Password validation
            if (password.trim().length === 0) {
                showError('Password is required.');
                return;
            }
            
            if (password.length < 8) {
                showError('Password must be at least 8 characters long.');
                return;
            }
            
            // Simulate login
            simulateLogin(email, password);
        });
        
        // Forgot password functionality
        document.getElementById('sendResetBtn').addEventListener('click', function() {
            const resetEmail = document.getElementById('resetEmail').value;
            
            if (!resetEmail) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Email Required',
                        text: 'Please enter your email address.',
                        confirmButtonColor: 'rgb(37, 52, 117)'
                    });
                } else {
                    alert('Please enter your email address.');
                }
                return;
            }
            
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(resetEmail)) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Email',
                        text: 'Please enter a valid email address.',
                        confirmButtonColor: 'rgb(37, 52, 117)'
                    });
                } else {
                    alert('Please enter a valid email address.');
                }
                return;
            }
            
            // Simulate sending reset email
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Sending...';
            this.disabled = true;
            
            setTimeout(() => {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Reset Link Sent!',
                        text: 'Password reset link has been sent to your email address.',
                        confirmButtonColor: 'rgb(37, 52, 117)'
                    });
                } else {
                    alert('Password reset link sent to your email!');
                }
                
                bootstrap.Modal.getInstance(document.getElementById('forgotPasswordModal')).hide();
                document.getElementById('resetEmail').value = '';
                this.innerHTML = 'Send Reset Link';
                this.disabled = false;
            }, 2000);
        });
        
        function showError(message) {
            document.getElementById('errorMessage').textContent = message;
            document.getElementById('errorAlert').classList.remove('d-none');
            // Scroll to error alert
            document.getElementById('errorAlert').scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center' 
            });
        }
        
        async function simulateLogin(email, password) {
            // Disable form
            const form = document.getElementById('loginForm');
            const inputs = form.querySelectorAll('input, button');
            inputs.forEach(input => input.disabled = true);
            
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Signing In...';
            
            try {
                // Real API call to login (same file)
                const response = await fetch('login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ email: email, password: password })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Successful login with SweetAlert
                    console.log('Login successful, redirecting...');
                    
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Login Successful!',
                            text: 'Welcome back! Redirecting to your dashboard...',
                            timer: 2000,
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            timerProgressBar: true
                        }).then(() => {
                            console.log('SweetAlert completed, redirecting now...');
                            window.location.href = '../student/dashboard.php';
                        });
                    } else {
                        // Fallback if SweetAlert is not loaded
                        alert('Login successful! Redirecting to dashboard...');
                        window.location.href = '../student/dashboard.php';
                    }
                } else {
                    // Show error message
                    showError(result.message || 'Login failed. Please try again.');
                    
                    // Re-enable form
                    inputs.forEach(input => input.disabled = false);
                    submitBtn.innerHTML = originalText;
                }
                
            } catch (error) {
                console.error('Login error:', error);
                showError('Connection error. Please try again.');
                
                // Re-enable form
                inputs.forEach(input => input.disabled = false);
                submitBtn.innerHTML = originalText;
            }
        }
        
        // Clear error messages when user starts typing
        document.getElementById('email').addEventListener('input', clearAlerts);
        document.getElementById('password').addEventListener('input', clearAlerts);
        
        function clearAlerts() {
            document.getElementById('errorAlert').classList.add('d-none');
        }
    </script>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
