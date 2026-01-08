<!DOCTYPE html>
<html lang="en">
<?php include '../includes/header.php'; ?>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <!-- Demo Banner -->
                <div class="alert alert-info mb-3" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <strong>Demo Mode:</strong> This is a demo of the login form. Try different inputs to test validations!
                </div>
                
                <!-- Alert Box at the top -->
                <div class="alert alert-danger d-none" id="errorAlert" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Error!</strong> <span id="errorMessage"></span>
                </div>
                
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold">Welcome Back <span class="badge bg-warning text-dark">DEMO</span></h2>
                            <p class="text-muted">Sign in to your NCST account</p>
                        </div>
                        
                        <!-- Login Form -->
                        <form id="loginForm" method="POST" action="">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" maxlength="250" required>
                                <small class="text-muted">Max 250 chars, valid email format</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" minlength="8" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Minimum 8 characters</small>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="rememberMe" name="rememberMe">
                                <label class="form-check-label" for="rememberMe">
                                    Remember me
                                </label>
                            </div>
                            
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">Sign In (Demo)</button>
                            </div>
                            
                            <div class="text-center mb-3">
                                <a href="#" class="text-primary text-decoration-none" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Forgot your password?</a>
                            </div>
                            
                            <div class="text-center">
                                <p class="text-muted">Don't have an account? <a href="register-demo.php" class="text-primary text-decoration-none">Create one here</a></p>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Test Cases Section -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-bug me-2"></i>Test Cases</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-3"><strong>Try these test cases to see validation and responses:</strong></p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-success">Valid Login:</h6>
                                <ul class="small">
                                    <li><strong>Email:</strong> any@valid.com</li>
                                    <li><strong>Password:</strong> password123</li>
                                    <li><em>Will show success SweetAlert</em></li>
                                </ul>
                                
                                <h6 class="text-warning mt-3">Error Test:</h6>
                                <ul class="small">
                                    <li><strong>Email:</strong> demo@error.com</li>
                                    <li><strong>Password:</strong> anypassword</li>
                                    <li><em>Will show error message</em></li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-danger">Invalid Examples:</h6>
                                <ul class="small">
                                    <li><strong>Email:</strong> invalid-email</li>
                                    <li><strong>Email:</strong> (over 250 chars)</li>
                                    <li><strong>Password:</strong> 123 (too short)</li>
                                    <li><strong>Password:</strong> (empty)</li>
                                </ul>
                                
                                <h6 class="text-info mt-3">Features:</h6>
                                <ul class="small">
                                    <li>Password visibility toggle</li>
                                    <li>Forgot password modal</li>
                                    <li>Form validation</li>
                                    <li>SweetAlert success</li>
                                </ul>
                            </div>
                        </div>
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
                    <h5 class="modal-title" id="forgotPasswordModalLabel">Reset Password <span class="badge bg-warning text-dark">DEMO</span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="forgotPasswordForm">
                        <div class="mb-3">
                            <label for="resetEmail" class="form-label">Enter your email address</label>
                            <input type="email" class="form-control" id="resetEmail" name="resetEmail" required>
                            <div class="form-text">We'll send you a link to reset your password. (Demo only)</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="sendResetBtn">Send Reset Link (Demo)</button>
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
            
            // Simulate login (DEMO VERSION)
            simulateLoginDemo(email, password);
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
                        title: 'Demo: Reset Link Sent!',
                        html: `
                            <p>Password reset link has been sent to your email address.</p>
                            <p><strong>This is just a demo.</strong> No actual email was sent.</p>
                        `,
                        confirmButtonColor: 'rgb(37, 52, 117)'
                    });
                } else {
                    alert('Demo: Password reset link sent to your email!');
                }
                
                bootstrap.Modal.getInstance(document.getElementById('forgotPasswordModal')).hide();
                document.getElementById('resetEmail').value = '';
                this.innerHTML = 'Send Reset Link (Demo)';
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
        
        function simulateLoginDemo(email, password) {
            // Disable form
            const form = document.getElementById('loginForm');
            const inputs = form.querySelectorAll('input, button');
            inputs.forEach(input => input.disabled = true);
            
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Signing In...';
            
            // Simulate API call delay
            setTimeout(() => {
                // Re-enable form for demo
                inputs.forEach(input => input.disabled = false);
                submitBtn.innerHTML = originalText;
                
                if (email === 'demo@error.com') {
                    // Simulate error case
                    showError('Invalid email or password. Please try again.');
                } else {
                    // Simulate successful login with SweetAlert (DEMO VERSION)
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Demo: Login Successful!',
                            html: `
                                <p>Welcome back! Login was successful.</p>
                                <p><strong>This is just a demo.</strong> No actual login occurred.</p>
                                <p>In the real version, you would be redirected to the dashboard.</p>
                            `,
                            confirmButtonText: 'Try Again',
                            confirmButtonColor: 'rgb(37, 52, 117)',
                            allowOutsideClick: true
                        }).then(() => {
                            // Reset form for demo
                            form.reset();
                        });
                    } else {
                        // Fallback if SweetAlert is not loaded
                        alert('Demo: Login successful! This is just a demo - no actual login occurred.');
                        form.reset();
                    }
                }
            }, 1500);
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
