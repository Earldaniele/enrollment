<?php
// Start session and require authentication
session_start();
require_once '../../includes/cashier_auth.php';
requireCashierAuth();

// Get current cashier info
$cashier = getCurrentCashier();
?>
<!DOCTYPE html>
<html lang="en">
<?php include '../../includes/header.php'; ?>
<body class="registrar-dashboard">
    <?php include '../../includes/navbar.php'; ?>
    
    <div class="container py-5">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="fw-bold mb-1">Record Payment</h2>
                                <p class="text-muted mb-0">Record student payments for tuition and other fees</p>
                            </div>
                            <div>
                                <a href="student_list.php" class="btn btn-outline-primary">
                                    <i class="bi bi-arrow-left me-2"></i>Back to Student List
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Search Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-search me-2"></i>Find Student</h5>
                    </div>
                    <div class="card-body">
                        <form id="studentSearchForm" onsubmit="findStudent(event)">
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label class="form-label">Student ID</label>
                                    <input type="text" class="form-control" id="studentId" placeholder="Enter Student ID" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">Student Name</label>
                                    <input type="text" class="form-control" id="studentName" placeholder="Enter Student Name">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-search me-2"></i>Find
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Form -->
        <div class="row" id="paymentFormSection" style="display: none;">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-cash-coin me-2"></i>Payment Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="border p-3 h-100">
                                    <h6 class="border-bottom pb-2 mb-3">Student Information</h6>
                                    <div class="mb-2">
                                        <strong>ID:</strong> <span id="displayStudentId">-</span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>Name:</strong> <span id="displayStudentName">-</span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>Course/Year:</strong> <span id="displayCourseYear">-</span>
                                    </div>
                                    <div class="mb-0">
                                        <strong>Semester:</strong> <span id="displaySemester">-</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border p-3 h-100">
                                    <h6 class="border-bottom pb-2 mb-3">Fee Summary</h6>
                                    <div class="mb-2">
                                        <strong>Total Fee:</strong> <span id="displayTotalFee" class="fw-bold">₱0.00</span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>Amount Paid:</strong> <span id="displayAmountPaid" class="text-success fw-bold">₱0.00</span>
                                    </div>
                                    <div class="mb-0">
                                        <strong>Remaining Balance:</strong> <span id="displayRemainingBalance" class="text-danger fw-bold">₱0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form id="paymentForm" onsubmit="recordPayment(event)">
                            <h6 class="border-bottom pb-2 mb-3">Payment Information</h6>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Payment Type</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="paymentType" id="fullPayment" value="full" checked onchange="calculatePayment()">
                                            <label class="form-check-label" for="fullPayment">Full Payment</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="paymentType" id="installmentPayment" value="installment" onchange="calculatePayment()">
                                            <label class="form-check-label" for="installmentPayment">Installment</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Payment Location</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="paymentLocation" id="onsite" value="onsite" checked onchange="togglePaymentMethod()">
                                            <label class="form-check-label" for="onsite">On-site (Treasury)</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="paymentLocation" id="online" value="online" onchange="togglePaymentMethod()">
                                            <label class="form-check-label" for="online">Online Payment</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Payment Method</label>
                                    <select class="form-select" id="paymentMethod" onchange="togglePaymentDetails()">
                                        <option value="cash">Cash</option>
                                        <option value="check">Check</option>
                                        <option value="bank">Bank Transfer</option>
                                        <option value="gcash">GCash</option>
                                        <option value="paymaya">PayMaya</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Check/Reference Details (initially hidden) -->
                            <div class="row mb-3 d-none" id="referenceDetails">
                                <div class="col-md-6">
                                    <label class="form-label">Reference/Check Number</label>
                                    <input type="text" class="form-control" id="referenceNumber" placeholder="Enter reference or check number">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Bank/Provider</label>
                                    <input type="text" class="form-control" id="bankProvider" placeholder="Enter bank name or provider">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Date Paid</label>
                                    <input type="date" class="form-control" id="datePaid" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Amount</label>
                                    <input type="number" class="form-control" id="amountPaid" placeholder="Enter amount" required step="0.01" min="1" oninput="validatePaymentAmount()">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">OR Number</label>
                                    <input type="text" class="form-control" id="orNumber" value="OR-<?php echo date('Ymd'); ?>-<?php echo rand(1000, 9999); ?>" required>
                                </div>
                            </div>

                            <!-- Online Payment Proof Upload (initially hidden) -->
                            <div class="row mb-3 d-none" id="paymentProofSection">
                                <div class="col-md-8">
                                    <label class="form-label">Payment Proof</label>
                                    <input type="file" class="form-control" id="paymentProof" accept="image/*">
                                    <div class="form-text">Upload screenshot or photo of payment receipt/confirmation</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Payment Status</label>
                                    <select class="form-select" id="paymentStatus">
                                        <option value="verified">Verified</option>
                                        <option value="pending">Pending Verification</option>
                                    </select>
                                </div>
                            </div>

                            <!-- For Installment Plans -->
                            <div class="row mb-3 d-none" id="installmentSection">
                                <div class="col-md-12">
                                    <label class="form-label">Next Due Date</label>
                                    <input type="date" class="form-control" id="nextDueDate">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label">Notes</label>
                                    <textarea class="form-control" id="paymentNotes" rows="2" placeholder="Enter additional notes or remarks about this payment"></textarea>
                                </div>
                            </div>

                            <div class="d-flex gap-2 justify-content-end">
                                <button type="button" class="btn btn-secondary" onclick="resetForm()">Cancel</button>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-save me-2"></i>Save and Print Receipt
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Payment Recorded Successfully</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                        <h4 class="mt-3">Payment has been recorded!</h4>
                        <p class="mb-0">Official Receipt No: <strong id="successOrNumber">OR-20250812-1234</strong></p>
                        <p>Amount: <strong id="successAmount">₱18,500.00</strong></p>
                    </div>
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        The official receipt will be printed automatically after closing this dialog.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="official_receipt.php" class="btn btn-primary">
                        <i class="bi bi-receipt me-2"></i>View All Receipts
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Include JavaScript -->
    <script>
        // Find student
        function findStudent(event) {
            event.preventDefault();
            
            const studentId = document.getElementById('studentId').value.trim();
            const studentName = document.getElementById('studentName').value.trim();
            
            if (!studentId && !studentName) {
                Swal.fire({
                    title: 'Input Required',
                    text: 'Please enter Student ID to search',
                    icon: 'warning'
                });
                return;
            }
            
            if (!studentId) {
                Swal.fire({
                    title: 'Student ID Required',
                    text: 'Please enter Student ID',
                    icon: 'warning'
                });
                return;
            }
            
            // Show loading
            Swal.fire({
                title: 'Searching...',
                text: 'Please wait while we fetch student information',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Make AJAX request to get student info
            fetch(`/enrollmentsystem/action/cashier/payment_handler.php?action=get_student_info&student_id=${encodeURIComponent(studentId)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.text();
                })
                .then(text => {
                    try {
                        const data = JSON.parse(text);
                        Swal.close();
                        
                        if (data.success) {
                            // Display the student data
                            displayStudentInfo(data.data);
                            
                            // Show the payment form
                            document.getElementById('paymentFormSection').style.display = 'block';
                            
                            // Set OR number
                            const orNumber = 'OR-' + new Date().toISOString().slice(0, 10).replace(/-/g, '') + '-' + Math.floor(Math.random() * 9000 + 1000);
                            document.getElementById('orNumber').value = orNumber;
                            
                            // Set max amount based on remaining balance
                            const amountInput = document.getElementById('amountPaid');
                            amountInput.max = data.data.remaining_balance;
                            
                            // Initialize the form values
                            calculatePayment();
                            
                            // Scroll to the payment form
                            document.getElementById('paymentFormSection').scrollIntoView({ behavior: 'smooth' });
                        } else {
                            Swal.fire({
                                title: 'Student Not Found',
                                text: data.message || 'No student found with the provided information',
                                icon: 'error'
                            });
                        }
                    } catch (e) {
                        Swal.close();
                        console.error('Invalid JSON response:', text);
                        Swal.fire({
                            title: 'Error',
                            text: 'Invalid response from server',
                            icon: 'error'
                        });
                        throw new Error('Invalid JSON response');
                    }
                })
                .catch(error => {
                    Swal.close();
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'An error occurred while searching for the student',
                        icon: 'error'
                    });
                });
        }
        
        // Display student information with accurate data from cashier module
        function displayStudentInfo(student) {
            document.getElementById('displayStudentId').textContent = student.student_id;
            document.getElementById('displayStudentName').textContent = student.full_name;
            document.getElementById('displayCourseYear').textContent = student.desired_course + ' / ' + (student.year_level || 'N/A');
            document.getElementById('displaySemester').textContent = (student.semester || 'N/A') + ', ' + (student.school_year || 'N/A');
            
            // Use accurate totals from cashier module (same as payment_details.php)
            const totalAssessment = parseFloat(student.total_assessment || 0);
            const totalPaid = parseFloat(student.total_paid || 0);
            const remainingBalance = totalAssessment - totalPaid;
            
            // Format amounts consistently
            const totalAssessmentFormatted = formatAmount(totalAssessment);
            const totalPaidFormatted = formatAmount(totalPaid);
            const remainingBalanceFormatted = formatAmount(remainingBalance);
            
            // Update display fields with accurate data
            document.getElementById('displayTotalFee').textContent = '₱' + totalAssessmentFormatted;
            document.getElementById('displayAmountPaid').textContent = '₱' + totalPaidFormatted;
            document.getElementById('displayRemainingBalance').textContent = '₱' + remainingBalanceFormatted;
            
            // Store student data for payment submission
            window.currentStudent = student;
            
            // Store accurate amounts for calculations
            window.currentStudent.accurate_total_assessment = totalAssessment;
            window.currentStudent.accurate_total_paid = totalPaid;
            window.currentStudent.accurate_remaining_balance = remainingBalance;
        }
        
        // Toggle payment method based on location
        function togglePaymentMethod() {
            const paymentLocation = document.querySelector('input[name="paymentLocation"]:checked').value;
            const paymentMethod = document.getElementById('paymentMethod');
            const paymentProofSection = document.getElementById('paymentProofSection');
            
            // Reset payment method options
            paymentMethod.innerHTML = '';
            
            if (paymentLocation === 'onsite') {
                // On-site payment methods
                paymentMethod.innerHTML += '<option value="cash">Cash</option>';
                paymentMethod.innerHTML += '<option value="check">Check</option>';
                paymentProofSection.classList.add('d-none');
            } else {
                // Online payment methods
                paymentMethod.innerHTML += '<option value="bank">Bank Transfer</option>';
                paymentMethod.innerHTML += '<option value="gcash">GCash</option>';
                paymentMethod.innerHTML += '<option value="paymaya">PayMaya</option>';
                paymentProofSection.classList.remove('d-none');
            }
            
            // Trigger the change event to update the UI
            togglePaymentDetails();
        }
        
        // Toggle payment details based on method
        function togglePaymentDetails() {
            const paymentMethod = document.getElementById('paymentMethod').value;
            const referenceDetails = document.getElementById('referenceDetails');
            
            if (paymentMethod === 'cash') {
                referenceDetails.classList.add('d-none');
            } else {
                referenceDetails.classList.remove('d-none');
            }
        }
        
        // Calculate payment based on type
        function calculatePayment() {
            const paymentType = document.querySelector('input[name="paymentType"]:checked').value;
            const installmentSection = document.getElementById('installmentSection');
            const amountPaid = document.getElementById('amountPaid');
            
            if (paymentType === 'full') {
                // Full payment - show remaining balance as placeholder
                amountPaid.value = '';
                if (window.currentStudent && window.currentStudent.accurate_remaining_balance) {
                    const remainingBalance = window.currentStudent.accurate_remaining_balance;
                    amountPaid.placeholder = `Enter full payment amount (₱${formatAmount(remainingBalance)})`;
                } else {
                    amountPaid.placeholder = 'Enter payment amount';
                }
                installmentSection.classList.add('d-none');
            } else if (paymentType === 'partial') {
            } else if (paymentType === 'installment') {
                // Installment payment
                installmentSection.classList.remove('d-none');
                updateInstallmentInfo();
            }
        }
        
        // Update installment information
        function updateInstallmentInfo() {
            // Set next due date (30 days from now)
            const nextDueDate = new Date();
            nextDueDate.setDate(nextDueDate.getDate() + 30);
            document.getElementById('nextDueDate').value = nextDueDate.toISOString().slice(0, 10);
        }
        
        // Record payment
        function recordPayment(event) {
            event.preventDefault();
            
            if (!window.currentStudent) {
                Swal.fire({
                    title: 'Error',
                    text: 'Please search for a student first',
                    icon: 'error'
                });
                return;
            }
            
            // Validate form
            const amountPaid = parseFloat(document.getElementById('amountPaid').value);
            if (!amountPaid || amountPaid <= 0) {
                Swal.fire({
                    title: 'Invalid Amount',
                    text: 'Please enter a valid payment amount',
                    icon: 'error'
                });
                return;
            }
            
            if (amountPaid > window.currentStudent.remaining_balance) {
                Swal.fire({
                    title: 'Amount Too High',
                    text: 'Payment amount cannot exceed remaining balance',
                    icon: 'error'
                });
                return;
            }
            
            const paymentMethod = document.getElementById('paymentMethod').value;
            
            // Show loading
            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we record your payment',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Prepare form data
            const formData = new FormData();
            formData.append('action', 'record_payment');
            formData.append('student_id', window.currentStudent.student_id);
            formData.append('amount', amountPaid);
            formData.append('payment_method', paymentMethod);
            
            // Submit payment
            fetch('/enrollmentsystem/action/cashier/payment_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text();
            })
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    Swal.close();
                
                if (data.success) {
                    // Show success modal
                    const orNumber = data.reference_number;
                    const amount = '₱' + amountPaid.toLocaleString('en-PH', {minimumFractionDigits: 2});
                    
                    document.getElementById('successOrNumber').textContent = orNumber;
                    document.getElementById('successAmount').textContent = amount;
                    
                    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();
                    
                    // Add event listener to redirect when modal is closed
                    document.getElementById('successModal').addEventListener('hidden.bs.modal', function () {
                        window.location.href = `student_list.php`;
                    }, { once: true });
                } else {
                    Swal.fire({
                        title: 'Payment Failed',
                        text: data.message || 'An error occurred while recording the payment',
                        icon: 'error'
                    });
                }
                } catch (e) {
                    Swal.close();
                    console.error('Invalid JSON response:', text);
                    Swal.fire({
                        title: 'Error',
                        text: 'Invalid response from server',
                        icon: 'error'
                    });
                    throw new Error('Invalid JSON response');
                }
            })
            .catch(error => {
                Swal.close();
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'An error occurred while processing the payment',
                    icon: 'error'
                });
            });
        }
        
        // Reset form
        function resetForm() {
            document.getElementById('paymentFormSection').style.display = 'none';
            document.getElementById('studentSearchForm').reset();
            document.getElementById('paymentForm').reset();
            
            // Reset radio buttons
            document.getElementById('fullPayment').checked = true;
            document.getElementById('onsite').checked = true;
            
            // Trigger changes to reset UI
            calculatePayment();
            togglePaymentMethod();
        }
        
        // Validate payment amount for suspicious values
        function validatePaymentAmount() {
            const amountInput = document.getElementById('amountPaid');
            const amount = parseFloat(amountInput.value) || 0;
            
            if (!window.currentStudent || amount === 0) return;
            
            const totalAssessment = window.currentStudent.accurate_total_assessment || 0;
            const remainingBalance = window.currentStudent.accurate_remaining_balance || 0;
            
            // Check for suspiciously small amounts (less than 5% of total assessment and less than ₱1000)
            const minReasonablePayment = totalAssessment * 0.05;
            
            if (amount > 0 && amount < minReasonablePayment && amount < 1000) {
                // Show warning and suggestions
                let suggestions = [];
                
                if (amount * 100 <= remainingBalance) {
                    suggestions.push(`₱${formatAmount(amount * 100)} (decimal point error?)`);
                }
                if (amount * 1000 <= remainingBalance) {
                    suggestions.push(`₱${formatAmount(amount * 1000)} (typing error?)`);
                }
                suggestions.push(`₱${formatAmount(totalAssessment * 0.20)} (20% down payment)`);
                suggestions.push(`₱${formatAmount(totalAssessment * 0.50)} (50% payment)`);
                
                const suggestionText = suggestions.slice(0, 3).join(', ');
                
                // Add visual warning
                amountInput.classList.add('border-warning');
                amountInput.title = `Amount seems small for ₱${formatAmount(totalAssessment)} assessment. Did you mean: ${suggestionText}?`;
                
                // Show subtle warning message
                let warningDiv = document.getElementById('amountWarning');
                if (!warningDiv) {
                    warningDiv = document.createElement('div');
                    warningDiv.id = 'amountWarning';
                    warningDiv.className = 'alert alert-warning alert-sm mt-2';
                    amountInput.parentNode.appendChild(warningDiv);
                }
                
                warningDiv.innerHTML = `
                    <small>
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        <strong>Amount seems small.</strong> Did you mean: ${suggestionText}?
                    </small>
                `;
                warningDiv.style.display = 'block';
            } else {
                // Remove warnings
                amountInput.classList.remove('border-warning');
                amountInput.title = '';
                
                const warningDiv = document.getElementById('amountWarning');
                if (warningDiv) {
                    warningDiv.style.display = 'none';
                }
            }
        }
        
        // Helper function to format amounts consistently
        function formatAmount(amount) {
            return parseFloat(amount).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
        
        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            // Set initial state
            togglePaymentMethod();
            calculatePayment();
        });
    </script>

    <?php include '../../includes/notifications-modal.php'; ?>

    <script src="../../../assets/js/notifications.js?v=<?php echo time(); ?>"></script>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>