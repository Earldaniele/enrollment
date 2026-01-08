<?php
// Start session and require authentication
session_start();
require_once '../../includes/cashier_auth.php';
requireCashierAuth();

// Get current cashier info
$cashier = getCurrentCashier();

// Get student ID from URL parameter
$studentId = $_GET['id'] ?? null;

if (!$studentId) {
    header('Location: student_list.php');
    exit;
}
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
                                <h2 class="fw-bold mb-1">Walk-in Payment Entry</h2>
                                <p class="text-muted mb-0">Process cash or check payments received at the cashier window</p>
                            </div>
                            <div>
                                <a href="student_list.php" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>Back to Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column: Student Info -->
            <div class="col-md-4">
                <div class="card dashboard-card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-person me-2"></i>Student Information</h5>
                    </div>
                    <div class="card-body" id="studentInfo">
                        <p class="mb-2"><strong>Student ID:</strong> <span id="studentId">Loading...</span></p>
                        <p class="mb-2"><strong>Name:</strong> <span id="studentName">Loading...</span></p>
                        <p class="mb-2"><strong>Course:</strong> <span id="studentCourse">Loading...</span></p>
                        <p class="mb-2"><strong>Year:</strong> <span id="studentYear">Loading...</span></p>
                        <p class="mb-2"><strong>Status:</strong> <span id="enrollmentStatus" class="badge">Loading...</span></p>
                    </div>
                </div>

                <!-- Payment Summary -->
                <div class="card dashboard-card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-calculator me-2"></i>Payment Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <p class="mb-1"><strong>Total Assessment:</strong> <span id="totalAssessment">₱0.00</span></p>
                                <p class="mb-1"><strong>Previous Payments:</strong> <span id="previousPayments">₱0.00</span></p>
                                <p class="mb-1"><strong>Outstanding Balance:</strong> <span id="outstandingBalance" class="text-danger">₱0.00</span></p>
                                <hr>
                                <p class="mb-1"><strong>Amount Being Paid:</strong> <span id="amountBeingPaid">₱0.00</span></p>
                                <p class="mb-0"><strong>Remaining Balance:</strong> <span id="remainingBalance">₱0.00</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Payment Form -->
            <div class="col-md-8">
                <div class="card dashboard-card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-cash-coin me-2"></i>Payment Entry Form</h5>
                    </div>
                    <div class="card-body">
                        <form id="paymentForm">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="paymentDate" class="form-label">Payment Date *</label>
                                    <input type="date" class="form-control" id="paymentDate" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="orNumber" class="form-label">OR Number *</label>
                                    <input type="text" class="form-control" id="orNumber" placeholder="Auto-generated" readonly style="background-color: #f8f9fa;">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="paymentMethod" class="form-label">Payment Method *</label>
                                    <select class="form-select" id="paymentMethod" required onchange="togglePaymentDetails()">
                                        <option value="">Select payment method</option>
                                        <option value="cash">Cash</option>
                                        <option value="check">Check</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="amount" class="form-label">Amount *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" class="form-control" id="amount" step="0.01" min="0" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Check Details (hidden by default) -->
                            <div id="checkDetails" class="d-none">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="checkNumber" class="form-label">Check Number *</label>
                                        <input type="text" class="form-control" id="checkNumber">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="checkDate" class="form-label">Check Date *</label>
                                        <input type="date" class="form-control" id="checkDate">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="bankName" class="form-label">Bank Name *</label>
                                        <input type="text" class="form-control" id="bankName" placeholder="e.g. BPI, BDO">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description *</label>
                                <input type="text" class="form-control" id="description" value="Tuition and Fees Payment" required>
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes (Optional)</label>
                                <textarea class="form-control" id="notes" rows="3" placeholder="Enter any additional notes"></textarea>
                            </div>
                                <div class="col-md-6">
                                    <label for="amount" class="form-label">Amount *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" class="form-control" id="amount" step="0.01" min="0" placeholder="0.00" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Check Details (hidden by default) -->
                            <div id="checkDetails" class="d-none">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="checkNumber" class="form-label">Check Number</label>
                                        <input type="text" class="form-control" id="checkNumber" placeholder="Enter check number">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="bankName" class="form-label">Bank Name</label>
                                        <input type="text" class="form-control" id="bankName" placeholder="Enter bank name">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="checkDate" class="form-label">Check Date</label>
                                        <input type="date" class="form-control" id="checkDate">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description *</label>
                                <input type="text" class="form-control" id="description" value="Tuition and Fees Payment" required>
                            </div>

                            <div class="mb-4">
                                <label for="notes" class="form-label">Notes (Optional)</label>
                                <textarea class="form-control" id="notes" rows="3" placeholder="Enter any additional notes"></textarea>
                            </div>

                            <!-- Payment Summary -->
                            <div class="card bg-light mb-4">
                                <div class="card-body">
                                    <h6 class="mb-3">Payment Summary</h6>
                                    <div id="paymentSummary">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Total Assessment:</span>
                                            <span class="fw-bold" id="totalAssessment">₱0.00</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Previous Payments:</span>
                                            <span class="text-success" id="previousPayments">₱0.00</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Outstanding Balance:</span>
                                            <span class="text-danger fw-bold" id="outstandingBalance">₱0.00</span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between">
                                            <span class="fw-bold">Amount Being Paid:</span>
                                            <span class="fw-bold text-primary" id="amountBeingPaid">₱0.00</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="fw-bold">Remaining Balance:</span>
                                            <span class="fw-bold" id="remainingBalance">₱0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="button" class="btn btn-secondary me-md-2" onclick="window.history.back()">Cancel</button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg me-1"></i>Process Payment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const studentId = '<?php echo $studentId; ?>';

        // Load student information on page load
        window.addEventListener('load', async function() {
            await loadStudentInfo();
            generateORNumber();
        });

        // Load student information
        async function loadStudentInfo() {
            try {
                const response = await fetch(`../../../action/cashier/student_list_handler.php?action=get_student_details&student_id=${studentId}`);
                const result = await response.json();

                if (result.success && result.data) {
                    const student = result.data;
                    
                    // Update student information
                    document.getElementById('studentId').textContent = student.student_id;
                    document.getElementById('studentName').textContent = student.full_name;
                    document.getElementById('studentCourse').textContent = student.desired_course;
                    document.getElementById('studentYear').textContent = student.year_level || 'N/A';
                    
                    // Update enrollment status
                    const statusElement = document.getElementById('enrollmentStatus');
                    statusElement.textContent = student.enrollment_status || 'Not Set';
                    statusElement.className = `badge ${getStatusBadgeClass(student.enrollment_status)}`;
                    
                    // Load payment summary
                    await loadPaymentSummary(student.student_id);
                } else {
                    Swal.fire('Error', 'Student not found', 'error');
                }
            } catch (error) {
                console.error('Error loading student info:', error);
                Swal.fire('Error', 'Failed to load student information', 'error');
            }
        }

        // Load payment summary
        async function loadPaymentSummary(studentId) {
            try {
                const response = await fetch(`../../../action/cashier/fee_calculator.php?action=get_payment_summary&student_id=${studentId}`);
                const result = await response.json();

                if (result.success) {
                    const fees = result.data;
                    document.getElementById('totalAssessment').textContent = `₱${parseFloat(fees.total_assessment || 0).toLocaleString('en-PH', {minimumFractionDigits: 2})}`;
                    document.getElementById('previousPayments').textContent = `₱${parseFloat(fees.total_paid || 0).toLocaleString('en-PH', {minimumFractionDigits: 2})}`;
                    document.getElementById('outstandingBalance').textContent = `₱${parseFloat(fees.remaining_balance || 0).toLocaleString('en-PH', {minimumFractionDigits: 2})}`;
                }
            } catch (error) {
                console.error('Error loading payment summary:', error);
            }
        }

        // Get status badge class
        function getStatusBadgeClass(status) {
            switch(status?.toLowerCase()) {
                case 'approved': return 'bg-success';
                case 'pending': return 'bg-warning';
                case 'rejected': return 'bg-danger';
                default: return 'bg-secondary';
            }
        }

        // Toggle payment method details
        function togglePaymentDetails() {
            const paymentMethod = document.getElementById('paymentMethod').value;
            const checkDetails = document.getElementById('checkDetails');
            
            if (paymentMethod === 'check') {
                checkDetails.classList.remove('d-none');
                // Make check fields required
                document.getElementById('checkNumber').required = true;
                document.getElementById('checkDate').required = true;
                document.getElementById('bankName').required = true;
            } else {
                checkDetails.classList.add('d-none');
                // Remove required from check fields
                document.getElementById('checkNumber').required = false;
                document.getElementById('checkDate').required = false;
                document.getElementById('bankName').required = false;
            }
        }

        // Update payment summary when amount changes
        document.getElementById('amount').addEventListener('input', function() {
            const amount = parseFloat(this.value) || 0;
            const outstanding = parseFloat(document.getElementById('outstandingBalance').textContent.replace(/[₱,]/g, '')) || 0;
            
            document.getElementById('amountBeingPaid').textContent = `₱${amount.toLocaleString('en-PH', {minimumFractionDigits: 2})}`;
            document.getElementById('remainingBalance').textContent = `₱${Math.max(0, outstanding - amount).toLocaleString('en-PH', {minimumFractionDigits: 2})}`;
        });

        // Generate OR Number
        function generateORNumber() {
            const orNumber = 'OR-' + new Date().getFullYear() + '-' + String(Math.floor(Math.random() * 100000)).padStart(5, '0');
            document.getElementById('orNumber').value = orNumber;
        }

        // Form submission
        document.getElementById('paymentForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('action', 'process_walk_in_payment');
            formData.append('student_id', studentId);
            formData.append('or_number', document.getElementById('orNumber').value);
            formData.append('payment_date', document.getElementById('paymentDate').value);
            formData.append('amount', document.getElementById('amount').value);
            formData.append('payment_method', document.getElementById('paymentMethod').value);
            formData.append('description', document.getElementById('description').value);
            formData.append('notes', document.getElementById('notes').value);
            
            // Add check details if payment method is check
            if (document.getElementById('paymentMethod').value === 'check') {
                formData.append('check_number', document.getElementById('checkNumber').value);
                formData.append('check_date', document.getElementById('checkDate').value);
                formData.append('bank_name', document.getElementById('bankName').value);
            }
            
            try {
                const response = await fetch('../../../action/cashier/payment_handler.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    Swal.fire({
                        title: 'Payment Processed',
                        text: `Payment has been processed successfully. OR Number: ${result.or_number}`,
                        icon: 'success',
                        confirmButtonColor: '#28a745'
                    }).then(() => {
                        // Ask if user wants to print receipt
                        Swal.fire({
                            title: 'Print Receipt?',
                            text: 'Would you like to print the official receipt?',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, Print',
                            cancelButtonText: 'No, thanks'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.print();
                            }
                            // Redirect back to student list
                            window.location.href = `student_list.php`;
                        });
                    });
                } else {
                    Swal.fire('Error', result.message, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire('Error', 'Failed to process payment', 'error');
            }
        });
    </script>
    
    <?php include '../../includes/notifications-modal.php'; ?>
    <script src="../../../assets/js/notifications.js?v=<?php echo time(); ?>"></script>
    <?php include '../../includes/footer.php'; ?>
</body>
</html>