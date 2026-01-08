<?php
// Start session
session_start();

// Redirect if already logged in
if (isset($_SESSION['staff_id'])) {
    $staffType = $_SESSION['staff_type'];
    $redirectUrl = '';
    
    // Redirect based on staff type
    switch ($staffType) {
        case 'evaluator':
            $redirectUrl = 'evaluator/index.php';
            break;
        case 'registrar':
            $redirectUrl = 'registrar/index.php';
            break;
        case 'cashier':
            $redirectUrl = 'cashier/index.php';
            break;
        case 'student-assistant':
            $redirectUrl = 'student-assistant/index.php';
            break;
        default:
            // Default redirect if staff type is unknown
            $redirectUrl = 'index.php';
    }
    
    header('Location: ' . $redirectUrl);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include '../includes/header.php'; ?>
<body style="background-color: white; min-height: 100vh;">
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center">
        <div class="row w-100">
            <div class="col-md-4 col-lg-3 mx-auto">
                <div class="card shadow-lg border-0" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <!-- Logo/Header -->
                        <div class="text-center mb-3">
                            <div class="mb-2">
                                <i class="bi bi-building-check text-primary" style="font-size: 2.5rem;"></i>
                            </div>
                            <h4 class="fw-bold text-primary">Staff Login</h4>
                            <p class="text-muted small">NCST Enrollment System</p>
                        </div>

                        <!-- Alert Messages -->
                        <div id="alertContainer"></div>

                        <!-- Login Form -->
                        <form id="staffLoginForm">
                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-envelope text-muted"></i>
                                    </span>
                                    <input type="email" class="form-control border-start-0 ps-0" id="email" name="email" required 
                                           placeholder="email@ncst.edu.ph" style="border-radius: 0 8px 8px 0;">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-bold">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-lock text-muted"></i>
                                    </span>
                                    <input type="password" class="form-control border-start-0 ps-0" id="password" name="password" required 
                                           placeholder="••••••••" style="border-radius: 0 8px 8px 0;">
                                    <button class="btn btn-outline-secondary border-start-0" type="button" id="togglePassword" style="border-radius: 0 8px 8px 0;">
                                        <i class="bi bi-eye" id="toggleIcon"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary fw-bold" style="border-radius: 12px; background: rgb(37, 52, 117); border-color: rgb(37, 52, 117);">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                                </button>
                            </div>
                        </form>

                        <!-- Additional Info -->
                        <div class="text-center mt-3">
                            <p class="text-muted small mb-1">Need help accessing your account?</p>
                            <a href="mailto:admin@ncst.edu.ph" class="text-decoration-none small">
                                <i class="bi bi-envelope me-1"></i>Contact Administrator
                            </a>
                        </div>

                        <!-- Staff Login Information -->
                        <div class="alert alert-info mt-3" role="alert" style="font-size: 0.85rem;">
                            <h6 class="alert-heading small"><i class="bi bi-info-circle me-1"></i>Staff Login Information</h6>
                            <p class="mb-1 small"><strong>Staff Types:</strong> evaluator, registrar, cashier, student assistant</p>
                            <p class="mb-1 small"><strong>Email:</strong> 
                                evaluator@ncst.edu.ph, registrar@ncst.edu.ph, cashier@ncst.edu.ph, studentassistant@ncst.edu.ph
                            </p>
                            <p class="mb-0 small"><strong>Password:</strong> password</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.className = 'bi bi-eye-slash';
            } else {
                passwordInput.type = 'password';
                toggleIcon.className = 'bi bi-eye';
            }
        });

        // Login form submission
        document.getElementById('staffLoginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            
            // Show loading state
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Signing In...';
            
            try {
                const response = await fetch('../../action/staff/login.php', {
                    method: 'POST',
                    body: formData
                });
                
                let result;
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    result = await response.json();
                } else {
                    // If not JSON, get text and show an error
                    const text = await response.text();
                    console.error('Non-JSON response:', text);
                    throw new Error('Server returned an invalid response format. Please check the server logs.');
                }
                
                if (result.success) {
                    showAlert(`Login successful! Welcome, ${result.staff_name}. Redirecting to dashboard...`, 'success');
                    setTimeout(() => {
                        window.location.href = result.redirect_url;
                    }, 1500);
                } else {
                    showAlert(result.message || 'Login failed. Please check your credentials.', 'danger');
                }
            } catch (error) {
                console.error('Login error:', error);
                showAlert(`Error: ${error.message}. Please check your browser console for details.`, 'danger');
            } finally {
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            }
        });

        function showAlert(message, type) {
            const alertContainer = document.getElementById('alertContainer');
            alertContainer.innerHTML = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
        }
    </script>
</body>
</html>
