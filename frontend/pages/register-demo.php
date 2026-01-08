<!DOCTYPE html>
<html lang="en">
<?php include '../includes/header.php'; ?>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <!-- Demo Banner -->
                <div class="alert alert-info mb-3" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <strong>Demo Mode:</strong> This is a demo of the registration form. Try different inputs to test validations!
                </div>
                
                <!-- Alert Box at the top -->
                <div class="alert alert-danger d-none" id="errorAlert" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Error!</strong> <span id="errorMessage"></span>
                </div>
                
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold">Create Account <span class="badge bg-warning text-dark">DEMO</span></h2>
                            <p class="text-muted">Join NCST College today</p>
                        </div>
                        
                        <!-- Registration Form -->
                        <form id="registerForm" method="POST" action="">
                            <div class="mb-3">
                                <label for="firstName" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="firstName" name="firstName" maxlength="100" required>
                                <small class="text-muted">Max 100 chars, letters only</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="lastName" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lastName" name="lastName" maxlength="100" required>
                                <small class="text-muted">Max 100 chars, letters only</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" maxlength="250" required>
                                <small class="text-muted">Max 250 chars, valid email format</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" pattern="^(09\d{9}|639\d{9})$" placeholder="09XXXXXXXXX or 639XXXXXXXXX" maxlength="12" required>
                                <div class="form-text">Enter 11 digits starting with 09 or 12 digits starting with 639</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" minlength="8" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text">Password must be at least 8 characters long</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="confirmPassword" class="form-label">Confirm Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                                    <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text">Must match the password above</div>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="agreeTerms" name="agreeTerms" required>
                                <label class="form-check-label" for="agreeTerms">
                                    I agree to the <a href="#" class="text-primary">Terms and Conditions</a> and <a href="#" class="text-primary">Privacy Policy</a>
                                </label>
                            </div>
                            
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">Create Account (Demo)</button>
                            </div>
                            
                            <div class="text-center">
                                <p class="text-muted">Already have an account? <a href="login.php" class="text-primary text-decoration-none">Sign in here</a></p>
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
                        <p class="mb-3"><strong>Try these test cases to see validation in action:</strong></p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-success">Valid Examples:</h6>
                                <ul class="small">
                                    <li><strong>Name:</strong> John Doe</li>
                                    <li><strong>Email:</strong> user@institution.edu.ph</li>
                                    <li><strong>Phone:</strong> 09123456789</li>
                                    <li><strong>Phone:</strong> 639123456789</li>
                                    <li><strong>Password:</strong> password123</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-danger">Invalid Examples:</h6>
                                <ul class="small">
                                    <li><strong>Name:</strong> John123 (numbers)</li>
                                    <li><strong>Email:</strong> invalid-email</li>
                                    <li><strong>Phone:</strong> 081234567 (wrong prefix)</li>
                                    <li><strong>Phone:</strong> 09123 (too short)</li>
                                    <li><strong>Password:</strong> 123 (too short)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for form validation and functionality -->
    <script>
        // Phone number input restriction - only allow numbers
        document.getElementById('phone').addEventListener('input', function(e) {
            // Remove any non-digit characters
            this.value = this.value.replace(/\D/g, '');
        });

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

        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            const confirmPassword = document.getElementById('confirmPassword');
            const icon = this.querySelector('i');
            
            if (confirmPassword.type === 'password') {
                confirmPassword.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                confirmPassword.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });

        // Form validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const email = document.getElementById('email').value;
            const firstName = document.getElementById('firstName').value;
            const lastName = document.getElementById('lastName').value;
            const phone = document.getElementById('phone').value;
            const agreeTerms = document.getElementById('agreeTerms').checked;
            
            // Reset alerts
            document.getElementById('errorAlert').classList.add('d-none');
            
            // First Name validation
            if (firstName.trim().length === 0) {
                showError('First name is required.');
                return;
            }
            if (firstName.length > 100) {
                showError('First name cannot exceed 100 characters.');
                return;
            }
            if (!/^[A-Za-z\s.'-]+$/.test(firstName)) {
                showError('First name can only contain letters, spaces, periods, apostrophes, and hyphens.');
                return;
            }
            
            // Last Name validation
            if (lastName.trim().length === 0) {
                showError('Last name is required.');
                return;
            }
            if (lastName.length > 100) {
                showError('Last name cannot exceed 100 characters.');
                return;
            }
            if (!/^[A-Za-z\s.'-]+$/.test(lastName)) {
                showError('Last name can only contain letters, spaces, periods, apostrophes, and hyphens.');
                return;
            }
            
            // Email validation
            if (email.length > 250) {
                showError('Email address cannot exceed 250 characters.');
                return;
            }
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showError('Please enter a valid email address.');
                return;
            }
            
            // Phone validation - strict requirements
            if (!/^\d+$/.test(phone)) {
                showError('Phone number can only contain numbers.');
                return;
            }
            
            if (phone.length === 11) {
                if (!phone.startsWith('09')) {
                    showError('11-digit phone numbers must start with 09.');
                    return;
                }
            } else if (phone.length === 12) {
                if (!phone.startsWith('639')) {
                    showError('12-digit phone numbers must start with 639.');
                    return;
                }
            } else {
                showError('Phone number must be 11 digits (starting with 09) or 12 digits (starting with 639).');
                return;
            }
            
            // Password validation
            if (password.length < 8) {
                showError('Password must be at least 8 characters long.');
                return;
            }
            
            // Confirm password validation
            if (password !== confirmPassword) {
                showError('Passwords do not match.');
                return;
            }
            
            // Terms agreement validation
            if (!agreeTerms) {
                showError('You must agree to the terms and conditions.');
                return;
            }
            
            // If validation passes, simulate registration (DEMO VERSION)
            simulateRegistrationDemo();
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
        
        function simulateRegistrationDemo() {
            // Disable form
            const form = document.getElementById('registerForm');
            const inputs = form.querySelectorAll('input, button');
            inputs.forEach(input => input.disabled = true);
            
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Creating Account...';
            
            // Simulate API call delay
            setTimeout(() => {
                // Re-enable form for demo purposes
                inputs.forEach(input => input.disabled = false);
                submitBtn.innerHTML = originalText;
                
                // Show SweetAlert success message (DEMO VERSION)
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Demo: Account Created Successfully!',
                        html: `
                            <p>Your account has been created successfully!</p>
                            <p><strong>This is just a demo.</strong> No actual account was created.</p>
                            <p>In the real version, you would be redirected to the login page.</p>
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
                    alert('Demo: Account created successfully! This is just a demo - no actual account was created.');
                    form.reset();
                }
            }, 1500);
        }
    </script>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
