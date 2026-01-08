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
        <!-- Student Info Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h2 class="fw-bold mb-1">Student Payment Details</h2>
                                <p class="text-muted mb-3">Complete payment information and fee breakdown</p>
                                <div class="row" id="studentInfo">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Student ID:</strong> <span id="studentId">Loading...</span></p>
                                        <p class="mb-1"><strong>Name:</strong> <span id="studentName">Loading...</span></p>
                                        <p class="mb-1"><strong>Course:</strong> <span id="studentCourse">Loading...</span></p>
                                        <p class="mb-1"><strong>Year Level:</strong> <span id="studentYear">Loading...</span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Enrollment Status:</strong> <span id="enrollmentStatus" class="badge">Loading...</span></p>
                                        <p class="mb-1"><strong>Payment Status:</strong> <span id="paymentStatus" class="badge">Loading...</span></p>
                                        <p class="mb-1"><strong>Date Enrolled:</strong> <span id="dateEnrolled">Loading...</span></p>
                                        <p class="mb-1"><strong>Academic Year:</strong> <span id="academicYear">Loading...</span></p>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <a href="student_list.php" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>Back to List
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column: Fee Breakdown & Subjects -->
            <div class="col-md-8">
                <!-- Enrolled Subjects -->
                <div class="card dashboard-card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-book me-2"></i>Enrolled Subjects</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Subject Code</th>
                                        <th>Subject Title</th>
                                        <th>Units</th>
                                        <th>Lab Units</th>
                                        <th>Type</th>
                                    </tr>
                                </thead>
                                <tbody id="enrolledSubjectsTable">
                                    <!-- Dynamic content will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-light" id="subjectsFooter">
                        <div class="row text-center">
                            <div class="col-md-6">
                                <strong>Total Academic Units: <span id="totalAcademicUnits">0</span></strong>
                            </div>
                            <div class="col-md-6">
                                <strong>Total Lab Units: <span id="totalLabUnits">0</span></strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment History -->
                <div class="card dashboard-card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Payment History / Ledger</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>OR Number</th>
                                        <th>Amount</th>
                                        <th>Payment Method</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="paymentHistoryTable">
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox display-4"></i>
                                            <p class="mt-2 mb-0">No payment records found</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Assessment Summary & Payment Actions -->
            <div class="col-md-4">
                <!-- Assessment Summary -->
                <div class="card dashboard-card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-calculator me-2"></i>Fee Breakdown</h5>
                    </div>
                    <div class="card-body" id="feeBreakdown">
                        <!-- Dynamic assessment summary will be loaded here -->
                    </div>
                </div>

                <!-- Payment Method Selection -->
                <div class="card dashboard-card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>Payment Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="payment_verification.php?id=<?php echo $studentId; ?>" class="btn btn-warning" id="verificationBtn">
                                <i class="bi bi-shield-check me-2"></i>Online Payment Verification
                            </a>
                            <a href="installment_tracking.php?id=<?php echo $studentId; ?>" class="btn btn-info" id="installmentBtn">
                                <i class="bi bi-calendar-check me-2"></i>Installment Tracking
                            </a>
                        </div>
                        
                        <hr>
                        
                        <div class="d-grid gap-2">
                            <button class="btn btn-success" disabled>
                                <i class="bi bi-receipt me-2"></i>Generate Official Receipt
                            </button>
                            <small class="text-muted text-center">Available after payment is made</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
    
    <script>
        const studentId = '<?php echo htmlspecialchars($studentId); ?>';
        
        // Load student data on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadStudentDetails();
        });
        
        // Load student details from backend
        async function loadStudentDetails() {
            try {
                const response = await fetch(`/enrollmentsystem/action/cashier/student_list_handler.php?action=get_student_details&student_id=${studentId}&debug=1`);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const text = await response.text();
                console.log('Raw response:', text); // Debug raw response
                let result;
                
                try {
                    result = JSON.parse(text);
                    console.log('Parsed result:', result); // Debug parsed result
                } catch (e) {
                    console.error('Invalid JSON response:', text);
                    throw new Error('Invalid response from server');
                }
                
                if (result.success && result.data) {
                    const student = result.data;
                    
                    // Check if we got an error message from the server
                    if (student.error) {
                        showError('Error loading details: ' + student.error_message);
                        return;
                    }
                    
                    displayStudentInfo(student);
                    displaySubjects(student.subjects || []);
                    displayPaymentHistory(student.payment_history || []);
                    displayFeeBreakdown(student.fee_breakdown || []);
                } else {
                    showError('Failed to load student details: ' + (result.message || 'Student not found'));
                }
            } catch (error) {
                console.error('Error:', error);
                showError('Failed to load student details: ' + error.message);
            }
        }
        
        // Display student information
        function displayStudentInfo(student) {
            document.getElementById('studentId').textContent = student.student_id || 'N/A';
            document.getElementById('studentName').textContent = student.full_name || 'N/A';
            document.getElementById('studentCourse').textContent = student.desired_course || 'N/A';
            document.getElementById('studentYear').textContent = student.year_level || 'N/A';
            
            // Update status badges
            const enrollmentStatus = document.getElementById('enrollmentStatus');
            enrollmentStatus.textContent = student.enrollment_status || 'N/A';
            enrollmentStatus.className = `badge ${student.enrollment_status === 'approved' ? 'bg-success' : 'bg-warning'}`;
            
            const paymentStatus = document.getElementById('paymentStatus');
            const status = student.payment_status || 'unpaid';
            paymentStatus.textContent = formatPaymentStatus(status);
            paymentStatus.className = `badge ${getPaymentStatusBadge(status)}`;
            
            document.getElementById('dateEnrolled').textContent = formatDate(student.date_enrolled);
            document.getElementById('academicYear').textContent = student.school_year || 'N/A';
        }
        
        // Display enrolled subjects
        function displaySubjects(subjects) {
            const tbody = document.getElementById('enrolledSubjectsTable');
            
            if (!subjects || subjects.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="bi bi-book display-4"></i>
                            <p class="mt-2 mb-0">No enrolled subjects found</p>
                        </td>
                    </tr>
                `;
                document.getElementById('totalAcademicUnits').textContent = '0';
                document.getElementById('totalLabUnits').textContent = '0';
                return;
            }
            
            let totalAcademicUnits = 0;
            let totalLabUnits = 0;
            
            tbody.innerHTML = subjects.map(subject => {
                const academicUnits = parseInt(subject.academic_units || 0);
                const labUnits = parseInt(subject.lab_units || 0);
                totalAcademicUnits += academicUnits;
                totalLabUnits += labUnits;
                
                const subjectType = getSubjectType(subject.subject_code, academicUnits, labUnits);
                
                return `
                    <tr>
                        <td>${subject.subject_code || 'N/A'}</td>
                        <td>${subject.subject_title || 'N/A'}</td>
                        <td>${academicUnits}</td>
                        <td>${labUnits}</td>
                        <td><span class="badge ${subjectType.class}">${subjectType.label}</span></td>
                    </tr>
                `;
            }).join('');
            
            // Update totals
            document.getElementById('totalAcademicUnits').textContent = totalAcademicUnits;
            document.getElementById('totalLabUnits').textContent = totalLabUnits;
        }
        
        // Determine subject type based on code and units
        function getSubjectType(code, academicUnits, labUnits) {
            if (code && code.toLowerCase().includes('nstp')) {
                return { label: 'NSTP', class: 'bg-warning' };
            } else if (code && code.toLowerCase().includes('pe')) {
                return { label: 'Activity', class: 'bg-success' };
            } else if (labUnits > 0) {
                return { label: 'Lec/Lab', class: 'bg-primary' };
            } else {
                return { label: 'Lecture', class: 'bg-info' };
            }
        }
        
        // Display Assessment Summary with accurate payment information
        function displayFeeBreakdown(fees) {
            const container = document.getElementById('feeBreakdown');
            
            if (!fees || fees.length === 0) {
                container.innerHTML = '<p class="text-muted">No assessment information available</p>';
                return;
            }
            
            let html = '<div class="table-responsive"><table class="table table-hover mb-0"><tbody>';
            let totalAssessment = 0;
            let totalPaid = 0;
            let remainingBalance = 0;
            let hasTotal = false;
            
            fees.forEach(fee => {
                if (fee.is_total || fee.fee_name === 'TOTAL ASSESSMENT') {
                    // Get values from the total row
                    totalAssessment = parseFloat(fee.amount_due || 0);
                    totalPaid = parseFloat(fee.amount_paid || 0);
                    remainingBalance = parseFloat(fee.remaining_balance || 0);
                    hasTotal = true;
                    
                    // Display Total Assessment row
                    html += `
                        <tr class="table-light">
                            <td class="fw-bold">${fee.fee_name}:</td>
                            <td class="text-end fw-bold text-primary">₱${fee.amount_formatted || formatAmount(totalAssessment)}</td>
                        </tr>
                    `;
                } else {
                    // Display individual fee items
                    const amount = parseFloat(fee.amount_due || 0);
                    html += `
                        <tr>
                            <td>${fee.fee_name}</td>
                            <td class="text-end">₱${fee.amount_formatted || formatAmount(amount)}</td>
                        </tr>
                    `;
                }
            });
            
            html += '</tbody></table></div>';
            
            // Add payment information with accurate calculations
            if (hasTotal) {
                html += `
                    <hr class="my-3">
                    <div class="payment-summary">
                        <div class="d-flex justify-content-between mb-2 fs-5 fw-bold text-primary">
                            <span>TOTAL AMOUNT DUE:</span>
                            <span>₱${formatAmount(totalAssessment)}</span>
                        </div>
                `;
                
                if (totalPaid > 0) {
                    html += `
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>AMOUNT PAID:</span>
                            <span>₱${formatAmount(totalPaid)}</span>
                        </div>
                    `;
                }
                
                if (remainingBalance > 0) {
                    html += `
                        <div class="d-flex justify-content-between fw-bold text-danger">
                            <span>REMAINING BALANCE:</span>
                            <span>₱${formatAmount(remainingBalance)}</span>
                        </div>
                    `;
                } else if (remainingBalance === 0 && totalPaid > 0) {
                    html += `
                        <div class="d-flex justify-content-between fw-bold text-success">
                            <span>STATUS:</span>
                            <span>FULLY PAID</span>
                        </div>
                    `;
                }
                
                html += '</div>';
            } else {
                // Fallback if no total row found
                html += `
                    <hr class="my-3">
                    <div class="alert alert-warning">
                        <small><i class="bi bi-exclamation-triangle me-1"></i>Payment calculation unavailable</small>
                    </div>
                `;
            }
            
            container.innerHTML = html;
        }
        
        // Display payment history
        function displayPaymentHistory(paymentHistory) {
            const tbody = document.getElementById('paymentHistoryTable');
            if (!tbody) return;
            
            if (!paymentHistory || paymentHistory.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="bi bi-inbox display-4"></i>
                            <p class="mt-2 mb-0">No payment records found</p>
                        </td>
                    </tr>
                `;
                return;
            }
            
            tbody.innerHTML = paymentHistory.map(payment => `
                <tr>
                    <td>${formatDate(payment.payment_date)}</td>
                    <td>${payment.or_number}</td>
                    <td>₱${formatAmount(payment.amount)}</td>
                    <td>${payment.payment_method}</td>
                    <td><span class="badge ${getPaymentStatusBadge(payment.status)}">${payment.status}</span></td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="viewReceipt('${payment.or_number}')">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-secondary" onclick="printReceipt('${payment.or_number}')">
                            <i class="bi bi-printer"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }
        
        // Helper functions
        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric' 
            });
        }
        
        function formatAmount(amount) {
            return parseFloat(amount).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
        
        function formatPaymentStatus(status) {
            const statuses = {
                'paid': 'Fully Paid',
                'partial': 'Installment',
                'unpaid': 'Unpaid',
                'pending': 'Pending'
            };
            return statuses[status] || status;
        }
        
        function getPaymentStatusBadge(status) {
            switch (status.toLowerCase()) {
                case 'paid': return 'bg-success';
                case 'partial': return 'bg-warning';
                case 'unpaid': return 'bg-danger';
                case 'pending': return 'bg-info';
                default: return 'bg-secondary';
            }
        }
        
        function showError(message) {
            Swal.fire('Error', message, 'error');
        }
        
        function viewReceipt(orNumber) {
            window.open(`official_receipt.php?view=${orNumber}`, '_blank');
        }
        
        function printReceipt(orNumber) {
            window.open(`official_receipt.php?print=${orNumber}`, '_blank');
        }
    </script>
</body>
</html>
