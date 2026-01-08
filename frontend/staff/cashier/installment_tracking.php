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
                                <h2 class="fw-bold mb-1">Payment Tracking</h2>
                                <p class="text-muted mb-0">Monitor student payments and remaining balances</p>
                            </div>
                            <div>
                                <button class="btn btn-primary" onclick="refreshInstallmentList()">
                                    <i class="bi bi-arrow-clockwise me-2"></i>Refresh List
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter and Search Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" id="statusFilter" onchange="filterInstallments()">
                                    <option value="all">All Status</option>
                                    <option value="current">Paid Partially</option>
                                    <option value="overdue">Has Balance</option>
                                    <option value="completed">Fully Paid</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Course</label>
                                <select class="form-select" id="courseFilter" onchange="filterInstallments()">
                                    <option value="all">All Courses</option>
                                    <!-- Options will be loaded dynamically -->
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Search</label>
                                <input type="text" class="form-control" id="searchInput" placeholder="Student ID or Name" onkeyup="filterInstallments()">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button class="btn btn-outline-secondary" onclick="clearFilters()">
                                        <i class="bi bi-x-circle me-2"></i>Clear
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Installment Tracking Table -->
        <div class="row">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Student Payment Records</h5>
                        <div>
                            <span class="badge bg-danger me-2" id="withBalanceCount">Loading...</span>
                            <span class="badge bg-success" id="fullyPaidCount">Loading...</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Student</th>
                                        <th>Course/Year</th>
                                        <th>Total Fees</th>
                                        <th>Amount Paid</th>
                                        <th>Remaining Balance</th>
                                        <th>Last Payment Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="installmentTrackingTable">
                                    <!-- Dynamic content will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center mb-0" id="pagination">
                                <!-- Pagination will be loaded dynamically -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Installment Details Modal -->
    <div class="modal fade" id="installmentDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Payment History Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Student Information</h6>
                            <p class="mb-1"><strong>ID:</strong> <span id="detailStudentId">Loading...</span></p>
                            <p class="mb-1"><strong>Name:</strong> <span id="detailStudentName">Loading...</span></p>
                            <p class="mb-0"><strong>Course/Year:</strong> <span id="detailCourseYear">Loading...</span></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Payment Information</h6>
                            <p class="mb-1"><strong>Total Fees:</strong> <span id="detailTotalFee">Loading...</span></p>
                            <p class="mb-1"><strong>Amount Paid:</strong> <span id="detailAmountPaid">Loading...</span></p>
                            <p class="mb-0"><strong>Remaining Balance:</strong> <span id="detailBalance">Loading...</span></p>
                        </div>
                    </div>
                    
                    <h6 class="border-bottom pb-2 mb-3">Payment History</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Payment Date</th>
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                    <th>OR Number</th>
                                    <th>Received By</th>
                                </tr>
                            </thead>
                            <tbody id="paymentHistoryBody">
                                <tr>
                                    <td colspan="5" class="text-center">Loading payment history...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mt-3 p-3 bg-light rounded">
                        <div>
                            <strong>Total Paid:</strong> <span id="totalPaidSummary" class="text-success">Loading...</span>
                        </div>
                        <div>
                            <strong>Remaining Balance:</strong> <span id="remainingBalanceSummary" class="text-danger">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Include JavaScript -->
    <script>
        let currentPage = 1;
        let totalPages = 1;
        
        // Load data when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadCourses();
            loadInstallmentTracking();
        });
        
        // Load courses for filter dropdown
        function loadCourses() {
            fetch('../../../action/cashier/student_list_handler.php?action=get_courses')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const courseFilter = document.getElementById('courseFilter');
                        const allOption = courseFilter.querySelector('option[value="all"]');
                        courseFilter.innerHTML = '';
                        courseFilter.appendChild(allOption);
                        
                        data.data.forEach(course => {
                            const option = document.createElement('option');
                            option.value = course.value;
                            option.textContent = course.label;
                            courseFilter.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error loading courses:', error);
                });
        }
        
        // Load installment tracking data
        function loadInstallmentTracking(page = 1) {
            const filters = {
                status: document.getElementById('statusFilter').value,
                course: document.getElementById('courseFilter').value,
                search: document.getElementById('searchInput').value,
                page: page,
                limit: 10
            };
            
            const queryString = new URLSearchParams(filters).toString();
            
            fetch(`../../../action/cashier/payment_handler.php?action=get_installment_tracking&${queryString}`)
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('API Response:', data);
                    if (data.success) {
                        displayInstallmentTracking(data.data);
                        updateCounts(data.data);
                    } else {
                        console.error('Error loading installment tracking:', data.message);
                        document.getElementById('installmentTrackingTable').innerHTML = 
                            '<tr><td colspan="8" class="text-center text-danger">Error loading data: ' + (data.message || 'Unknown error') + '</td></tr>';
                    }
                })
                .catch(error => {
                    console.error('Error loading installment tracking:', error);
                    document.getElementById('installmentTrackingTable').innerHTML = 
                        '<tr><td colspan="8" class="text-center text-danger">Network error: ' + error.message + '</td></tr>';
                });
        }
        
        // Display installment tracking in table
        function displayInstallmentTracking(tracking) {
            const tbody = document.getElementById('installmentTrackingTable');
            tbody.innerHTML = '';
            
            if (tracking.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center">No installment records found</td></tr>';
                return;
            }
            
                    tracking.forEach(record => {
                const row = document.createElement('tr');
                
                // Debug: Check what data we're getting
                console.log('Raw record data:', record);
                console.log('total_assessment:', record.total_assessment, typeof record.total_assessment);
                console.log('total_paid:', record.total_paid, typeof record.total_paid);
                
                const totalAssessment = parseFloat(record.total_assessment || 0);
                const totalPaid = parseFloat(record.total_paid || 0);
                
                console.log('Parsed values:', totalAssessment, totalPaid);
                
                // Calculate proper remaining balance
                const remainingBalance = Math.max(0, totalAssessment - totalPaid);
                
                // Determine status based on payment progress
                let status = 'No Payment';
                let statusClass = 'danger';
                
                if (totalAssessment === 0 || totalAssessment === null) {
                    // No enrollment/assessment data
                    status = 'No Assessment';
                    statusClass = 'secondary';
                } else if (totalPaid >= totalAssessment && totalAssessment > 0) {
                    status = 'Fully Paid';
                    statusClass = 'success';
                } else if (totalPaid > 0) {
                    status = 'Has Balance';
                    statusClass = 'warning text-dark';
                }
                
                // Format course/year info
                const courseYear = record.course && record.year_level && record.year_level !== 'Not Enrolled' ? 
                    `${record.course} - ${record.year_level}` : 
                    (record.course || 'N/A');
                
                // Format amounts with proper null handling
                const formatAmount = (amount) => {
                    const num = parseFloat(amount || 0);
                    return num.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                };
                
                console.log('Formatted amounts:', {
                    assessment: formatAmount(totalAssessment),
                    paid: formatAmount(totalPaid),
                    balance: formatAmount(remainingBalance)
                });
                
                row.innerHTML = `
                    <td>
                        <div class="d-flex align-items-center">
                            <div>
                                <div class="fw-bold">${record.student_name || 'N/A'}</div>
                                <small class="text-muted">${record.student_id || 'N/A'}</small>
                            </div>
                        </div>
                    </td>
                    <td>${courseYear}</td>
                    <td>₱${formatAmount(totalAssessment)}</td>
                    <td>₱${formatAmount(totalPaid)}</td>
                    <td>₱${formatAmount(remainingBalance)}</td>
                    <td>${record.last_payment_date || 'No payments yet'}</td>
                    <td><span class="badge bg-${statusClass}">${status}</span></td>
                    <td>
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-secondary" onclick="viewInstallmentDetails('${record.student_id}')" title="View Details">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }
        
        // Get status class for badge
        function getStatusClass(status) {
            const classes = {
                'Fully Paid': 'success',
                'Has Balance': 'warning text-dark',
                'No Payment': 'danger'
            };
            return classes[status] || 'secondary';
        }
        
        // Update counts
        function updateCounts(tracking) {
            let withBalance = 0;
            let fullyPaid = 0;
            
            tracking.forEach(record => {
                const totalAssessment = parseFloat(record.total_assessment || 0);
                const totalPaid = parseFloat(record.total_paid || 0);
                
                if (totalAssessment > 0) {
                    if (totalPaid >= totalAssessment) {
                        fullyPaid++;
                    } else if (totalPaid > 0) {
                        withBalance++;
                    } else {
                        withBalance++; // No payment yet, still has balance
                    }
                }
            });
            
            document.getElementById('withBalanceCount').textContent = `${withBalance} With Balance`;
            document.getElementById('fullyPaidCount').textContent = `${fullyPaid} Fully Paid`;
        }
        
        // Update pagination
        function updatePagination(current, total) {
            const pagination = document.getElementById('pagination');
            pagination.innerHTML = '';
            
            if (total <= 1) return;
            
            // Previous button
            const prevItem = document.createElement('li');
            prevItem.className = `page-item ${current === 1 ? 'disabled' : ''}`;
            prevItem.innerHTML = `
                <a class="page-link" href="#" onclick="changePage(${current - 1})" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            `;
            pagination.appendChild(prevItem);
            
            // Page numbers
            const startPage = Math.max(1, current - 2);
            const endPage = Math.min(total, current + 2);
            
            for (let i = startPage; i <= endPage; i++) {
                const pageItem = document.createElement('li');
                pageItem.className = `page-item ${i === current ? 'active' : ''}`;
                pageItem.innerHTML = `<a class="page-link" href="#" onclick="changePage(${i})">${i}</a>`;
                pagination.appendChild(pageItem);
            }
            
            // Next button
            const nextItem = document.createElement('li');
            nextItem.className = `page-item ${current === total ? 'disabled' : ''}`;
            nextItem.innerHTML = `
                <a class="page-link" href="#" onclick="changePage(${current + 1})" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            `;
            pagination.appendChild(nextItem);
        }
        
        // Change page
        function changePage(page) {
            if (page >= 1 && page <= totalPages && page !== currentPage) {
                loadInstallmentTracking(page);
            }
        }
        
        // Filter installments based on selected criteria
        function filterInstallments() {
            currentPage = 1; // Reset to first page when filtering
            loadInstallmentTracking();
        }
        
        // Clear all filters
        function clearFilters() {
            document.getElementById('statusFilter').value = 'all';
            document.getElementById('courseFilter').value = 'all';
            document.getElementById('searchInput').value = '';
            filterInstallments();
        }
        
        // Refresh installment list
        function refreshInstallmentList() {
            loadInstallmentTracking(currentPage);
        }
        
        // View installment details
        function viewInstallmentDetails(studentId) {
            showInstallmentDetailsModal(studentId);
        }
        
        // View payment details in modal (dynamic from API)
        function showInstallmentDetailsModal(studentId) {
            // Fetch real student payment details from API
            fetch(`../../../action/cashier/payment_handler.php?action=get_student_payment_details&student_id=${studentId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const student = data.data;
                        
                        // Update modal with real student info
                        document.getElementById('detailStudentId').textContent = student.student_id || studentId;
                        document.getElementById('detailStudentName').textContent = student.student_name || 'Unknown Student';
                        document.getElementById('detailCourseYear').textContent = `${student.course || 'Unknown'} / ${student.year_level || 'Unknown'}`;
                        document.getElementById('detailTotalFee').textContent = `₱${parseFloat(student.total_fee || 0).toLocaleString('en-US', {minimumFractionDigits: 2})}`;
                        document.getElementById('detailAmountPaid').textContent = `₱${parseFloat(student.amount_paid || 0).toLocaleString('en-US', {minimumFractionDigits: 2})}`;
                        
                        // Update balance with appropriate color
                        const balanceElement = document.getElementById('detailBalance');
                        const remainingBalance = parseFloat(student.remaining_balance || 0);
                        balanceElement.textContent = `₱${remainingBalance.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
                        balanceElement.className = remainingBalance > 0 ? 'text-danger' : 'text-success';
                        
                        // Update summary totals
                        document.getElementById('totalPaidSummary').textContent = `₱${parseFloat(student.amount_paid || 0).toLocaleString('en-US', {minimumFractionDigits: 2})}`;
                        document.getElementById('remainingBalanceSummary').textContent = `₱${remainingBalance.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
                        document.getElementById('remainingBalanceSummary').className = remainingBalance > 0 ? 'text-danger' : 'text-success';
                        
                        // Fetch and display payment history
                        fetchPaymentHistory(studentId);
                        
                        // Show modal
                        const modal = new bootstrap.Modal(document.getElementById('installmentDetailsModal'));
                        modal.show();
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: 'Failed to fetch student payment details.',
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error fetching student payment details:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'Failed to fetch student payment details.',
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                });
        }
        
        // Fetch and display payment history for a student
        function fetchPaymentHistory(studentId) {
            fetch(`../../../action/cashier/receipt_handler.php?action=get_receipts&search=${studentId}&limit=100`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const payments = data.data.receipts || [];
                        const tbody = document.getElementById('paymentHistoryBody');
                        
                        if (payments.length === 0) {
                            tbody.innerHTML = `
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No payment records found</td>
                                </tr>
                            `;
                        } else {
                            tbody.innerHTML = payments.map(payment => `
                                <tr>
                                    <td>${payment.date}</td>
                                    <td>₱${payment.amount}</td>
                                    <td><span class="badge ${getPaymentTypeBadge(payment.payment_method)}">${payment.payment_method}</span></td>
                                    <td>${payment.or_number}</td>
                                    <td>${payment.issued_by}</td>
                                </tr>
                            `).join('');
                        }
                    } else {
                        document.getElementById('paymentHistoryBody').innerHTML = `
                            <tr>
                                <td colspan="5" class="text-center text-danger">Failed to load payment history</td>
                            </tr>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error fetching payment history:', error);
                    document.getElementById('paymentHistoryBody').innerHTML = `
                        <tr>
                            <td colspan="5" class="text-center text-danger">Error loading payment history</td>
                        </tr>
                    `;
                });
        }
        
        // Get payment type badge class (same as official receipt page)
        function getPaymentTypeBadge(paymentType) {
            const type = paymentType.toLowerCase();
            switch (type) {
                case 'cash': return 'bg-success';
                case 'check': return 'bg-warning';
                case 'bank transfer': return 'bg-primary';
                case 'online': return 'bg-info';
                case 'gcash': return 'bg-info';
                case 'paymaya': return 'bg-secondary';
                default: return 'bg-secondary';
            }
        }
    </script>

    <?php include '../../includes/notifications-modal.php'; ?>

    <script src="../../../assets/js/notifications.js?v=<?php echo time(); ?>"></script>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>
