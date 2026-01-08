<?php
// Handle registration POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../includes/db_config.php';
    require_once '../includes/auth.php';
    
    header('Content-Type: application/json');
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    $firstName = trim($input['firstName'] ?? '');
    $lastName = trim($input['lastName'] ?? '');
    $email = trim($input['email'] ?? '');
    $phone = trim($input['phone'] ?? '');
    $password = $input['password'] ?? '';
    
    // Validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit;
    }
    
    // Check if email already exists
    $stmt = $conn->prepare("SELECT email FROM student_accounts WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already registered']);
        exit;
    }
    
    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user account with password
    $stmt = $conn->prepare("INSERT INTO student_accounts (first_name, last_name, email, phone, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $firstName, $lastName, $email, $phone, $hashedPassword);
    
    if ($stmt->execute()) {
        // Don't auto-login, let user login manually after registration
        echo json_encode([
            'success' => true, 
            'message' => 'Account created successfully',
            'data' => ['email' => $email]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Registration failed. Please try again.']);
    }
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
            <div class="col-md-6 col-lg-5">
                <!-- Alert Box at the top -->
                <div class="alert alert-danger d-none" id="errorAlert" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Error!</strong> <span id="errorMessage"></span>
                </div>
                
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold">Create Account</h2>
                            <p class="text-muted">Join NCST College today</p>
                        </div>
                        
                        <!-- Registration Form -->
                        <form id="registerForm" method="POST" action="">
                            <div class="mb-3">
                                <label for="firstName" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="firstName" name="firstName" maxlength="100" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="lastName" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lastName" name="lastName" maxlength="100" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" maxlength="250" required>
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
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="agreeTerms" name="agreeTerms" required>
                                <label class="form-check-label" for="agreeTerms">
                                    I agree to the <a href="#" class="text-primary">Terms and Conditions</a> and <a href="#" class="text-primary">Privacy Policy</a>
                                </label>
                            </div>
                            
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">Create Account</button>
                            </div>
                            
                            <div class="text-center">
                                <p class="text-muted">Already have an account? <a href="login.php" class="text-primary text-decoration-none">Sign in here</a></p>
                            </div>
                        </form>
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
            
            // If validation passes, simulate registration
            simulateRegistration();
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
        
        async function simulateRegistration() {
            // Disable form
            const form = document.getElementById('registerForm');
            const inputs = form.querySelectorAll('input, button');
            inputs.forEach(input => input.disabled = true);
            
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Creating Account...';
            
            try {
                // Get form data
                const formData = {
                    firstName: document.getElementById('firstName').value,
                    lastName: document.getElementById('lastName').value,
                    email: document.getElementById('email').value,
                    phone: document.getElementById('phone').value,
                    password: document.getElementById('password').value
                };
                
                // Real API call to register
                const response = await fetch('register.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Show SweetAlert success message
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Account Created Successfully!',
                            text: `Your account has been created with email: ${result.data.email}. Redirecting to login page...`,
                            timer: 2000,
                            showConfirmButton: false,
                            allowOutsideClick: false
                        }).then(() => {
                            window.location.href = 'login.php';
                        });
                    } else {
                        // Fallback if SweetAlert is not loaded
                        alert('Account created successfully! Redirecting to login page...');
                        setTimeout(() => {
                            window.location.href = 'login.php';
                        }, 1000);
                    }
                } else {
                    // Show error message
                    showError(result.message || 'Registration failed. Please try again.');
                    
                    // Re-enable form
                    inputs.forEach(input => input.disabled = false);
                    submitBtn.innerHTML = originalText;
                }
                
            } catch (error) {
                console.error('Registration error:', error);
                showError('Connection error. Please try again.');
                
                // Re-enable form
                inputs.forEach(input => input.disabled = false);
                submitBtn.innerHTML = originalText;
            }
        }
    </script>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
