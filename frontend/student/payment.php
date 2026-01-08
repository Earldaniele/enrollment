<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../includes/auth.php';

// Check if user is logged in using the correct session variable
if (!isset($_SESSION['email'])) {
    header('Location: ../pages/login.php');
    exit;
}




?>
<!DOCTYPE html>
<html lang="en">
<?php include '../includes/header.php'; ?>
<body class="student-dashboard">
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container py-5">
        <!-- Payment Success/Error Messages -->
        <?php if(isset($_SESSION['payment_success']) && $_SESSION['payment_success']): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-success" role="alert" id="paymentSuccessAlert">
                    <h4 class="alert-heading"><i class="bi bi-check-circle-fill me-2"></i>Payment Successful!</h4>
                    <p><?= $_SESSION['payment_message'] ?></p>
                    <?php if(isset($_SESSION['payment_data'])): ?>
                    <hr>
                    <p class="mb-0">Transaction ID: <strong><?= $_SESSION['payment_data']['transaction_id'] ?></strong></p>
                    <p class="mb-0">Amount: <strong>₱<?= number_format($_SESSION['payment_data']['amount'], 2) ?></strong></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <script>
            // Auto hide the success alert after 7 seconds
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
                    var alertElement = document.getElementById('paymentSuccessAlert');
                    if (alertElement) {
                        alertElement.style.transition = 'opacity 1s ease-out';
                        alertElement.style.opacity = '0';
                        
                        setTimeout(function() {
                            alertElement.style.display = 'none';
                        }, 1000);
                    }
                }, 7000);
            });
        </script>
        
        <?php 
            // Clear the session variables after displaying
            unset($_SESSION['payment_success']);
            unset($_SESSION['payment_message']);
            unset($_SESSION['payment_data']);
        ?>
        <?php endif; ?>

        <!-- Payment Error Alert -->
        <?php if(isset($_SESSION['payment_error']) && $_SESSION['payment_error']): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-danger" role="alert" id="paymentErrorAlert">
                    <h4 class="alert-heading"><i class="bi bi-exclamation-triangle-fill me-2"></i>Payment Failed</h4>
                    <p><?= $_SESSION['payment_error_message'] ?></p>
                </div>
            </div>
        </div>
        
        <?php 
            unset($_SESSION['payment_error']);
            unset($_SESSION['payment_error_message']);
        ?>
        <?php endif; ?>

        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-credit-card display-4 text-primary me-3"></i>
                            <div>
                                <h2 class="fw-bold text-primary mb-1">Online Payment</h2>
                                <p class="text-muted mb-0">Secure payment processing for tuition fees and other school charges</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Form -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-shield-check me-2"></i>Payment Information
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form id="paymentForm" method="POST" action="../../action/student/process_payment.php" novalidate>
                            <!-- Student Information Section -->
                            <div class="mb-4">
                                <h6 class="fw-bold text-secondary mb-3">
                                    <i class="bi bi-person-circle me-2"></i>Student Information
                                </h6>
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="firstName" class="form-label fw-semibold">
                                            First Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control form-control-lg" 
                                               id="firstName" 
                                               name="first_name" 
                                               placeholder="Enter your first name"
                                               required>
                                        <div class="invalid-feedback">
                                            Please provide your first name.
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="lastName" class="form-label fw-semibold">
                                            Last Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control form-control-lg" 
                                               id="lastName" 
                                               name="last_name" 
                                               placeholder="Enter your last name"
                                               required>
                                        <div class="invalid-feedback">
                                            Please provide your last name.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row g-3 mt-2">
                                    <div class="col-12">
                                        <label for="email" class="form-label fw-semibold">
                                            Email Address <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" 
                                               class="form-control form-control-lg" 
                                               id="email" 
                                               name="email" 
                                               placeholder="Enter your email address"
                                               value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>"
                                               required>
                                        <div class="invalid-feedback">
                                            Please provide a valid email address.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Payment Details Section -->
                            <div class="mb-4">
                                <h6 class="fw-bold text-secondary mb-3">
                                    <i class="bi bi-cash-coin me-2"></i>Payment Details
                                </h6>
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="paymentType" class="form-label fw-semibold">
                                            Payment Type <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select form-select-lg" id="paymentType" name="payment_type" required>
                                            <option value="">Select payment type</option>
                                            <option value="tuition">Tuition Fee</option>
                                            <option value="miscellaneous">Miscellaneous Fee</option>
                                            <option value="laboratory">Laboratory Fee</option>
                                            <option value="library">Library Fee</option>
                                            <option value="registration">Registration Fee</option>
                                            <option value="other">Other</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            Please select a payment type.
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="amount" class="form-label fw-semibold">
                                            Amount (PHP) <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text">₱</span>
                                            <input type="number" 
                                                   class="form-control" 
                                                   id="amount" 
                                                   name="amount" 
                                                   placeholder="0.00"
                                                   min="1"
                                                   step="0.01"
                                                   required>
                                            <div class="invalid-feedback">
                                                Please enter a valid amount (minimum ₱1.00).
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row g-3 mt-2">
                                    <div class="col-12">
                                        <label for="description" class="form-label fw-semibold">
                                            Payment Description (Optional)
                                        </label>
                                        <textarea class="form-control" 
                                                  id="description" 
                                                  name="description" 
                                                  rows="3" 
                                                  placeholder="Add any additional notes about this payment..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Payment Summary -->
                            <div class="mb-4">
                                <h6 class="fw-bold text-secondary mb-3">
                                    <i class="bi bi-receipt me-2"></i>Payment Summary
                                </h6>
                                <div class="bg-light rounded p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>Subtotal:</span>
                                        <span id="subtotalAmount" class="fw-semibold">₱0.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>Processing Fee:</span>
                                        <span id="processingFee" class="fw-semibold">₱0.00</span>
                                    </div>
                                    <hr class="my-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="h6 mb-0">Total Amount:</span>
                                        <span id="totalAmount" class="h5 mb-0 text-primary fw-bold">₱0.00</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Method Info -->
                            <div class="mb-4">
                                <h6 class="fw-bold text-secondary mb-3">
                                    <i class="bi bi-wallet2 me-2"></i>Payment Method
                                </h6>
                                <div class="alert alert-info">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-info-circle me-2"></i>
                                        <div>
                                            <strong>Secure Payment Gateway</strong><br>
                                            <small>You will be redirected to PayMongo's secure checkout page where you can choose from various payment methods including GCash, PayMaya, Credit/Debit Cards, and more.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Terms and Conditions -->
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="agreeTerms" name="agree_terms" required>
                                    <label class="form-check-label" for="agreeTerms">
                                        I agree to the <a href="#" class="text-primary">Terms and Conditions</a> and <a href="#" class="text-primary">Privacy Policy</a>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="invalid-feedback">
                                        You must agree to the terms and conditions.
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg py-3" id="submitPayment">
                                    <i class="bi bi-arrow-right-circle me-2"></i>
                                    <span id="submitButtonText">Proceed to PayMongo Checkout</span>
                                    <div class="spinner-border spinner-border-sm ms-2 d-none" id="submitSpinner"></div>
                                </button>
                                <a href="dashboard.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications Modal -->
    <?php include '../includes/notifications-modal.php'; ?>
    
    <?php include '../includes/footer.php'; ?>
    
    <script src="/enrollmentsystem/assets/js/notifications.js?v=<?php echo time(); ?>"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('paymentForm');
            const amountInput = document.getElementById('amount');
            const subtotalAmount = document.getElementById('subtotalAmount');
            const processingFee = document.getElementById('processingFee');
            const totalAmount = document.getElementById('totalAmount');
            const submitButton = document.getElementById('submitPayment');
            const submitButtonText = document.getElementById('submitButtonText');
            const submitSpinner = document.getElementById('submitSpinner');

            // Processing fee calculation (3.5% + ₱15)
            function calculateFees() {
                const amount = parseFloat(amountInput.value) || 0;
                const fee = (amount * 0.035) + 15;
                const total = amount + fee;

                subtotalAmount.textContent = '₱' + amount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                processingFee.textContent = '₱' + fee.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                totalAmount.textContent = '₱' + total.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            // Update fees when amount changes
            amountInput.addEventListener('input', calculateFees);

            // Form validation and submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                let isValid = true;
                
                // Validate amount
                const amount = parseFloat(amountInput.value);
                if (!amount || amount < 1) {
                    amountInput.classList.add('is-invalid');
                    isValid = false;
                } else {
                    amountInput.classList.remove('is-invalid');
                    amountInput.classList.add('is-valid');
                }

                // Check HTML5 form validation
                if (!form.checkValidity()) {
                    form.classList.add('was-validated');
                    isValid = false;
                }

                if (isValid) {
                    // Show loading state
                    submitButton.disabled = true;
                    submitButtonText.textContent = 'Redirecting to PayMongo...';
                    submitSpinner.classList.remove('d-none');
                    
                    // Submit form to process_payment.php
                    form.submit();
                }
            });

            // Initialize fees calculation
            calculateFees();
        });
    </script>

    <style>        
        .form-control:focus, .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
        
        .form-control.is-valid, .form-select.is-valid {
            border-color: #198754;
        }
        
        .form-control.is-invalid, .form-select.is-invalid {
            border-color: #dc3545;
        }
    </style>
</body>
</html>
