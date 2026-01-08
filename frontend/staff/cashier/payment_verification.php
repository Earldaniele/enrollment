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
                                <h2 class="fw-bold mb-1">Payment Verification</h2>
                                <p class="text-muted mb-0">Verify online and bank transfer payments submitted by students</p>
                            </div>
                            <div>
                                <button class="btn btn-primary" onclick="refreshPaymentList()">
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
                                <label class="form-label">Payment Type</label>
                                <select class="form-select" id="paymentTypeFilter" onchange="filterPayments()">
                                    <option value="all">All Types</option>
                                    <!-- Dynamic options will be loaded here -->
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date Range</label>
                                <select class="form-select" id="dateRangeFilter" onchange="filterPayments()">
                                    <option value="all">All Dates</option>
                                    <option value="today">Today</option>
                                    <option value="yesterday">Yesterday</option>
                                    <option value="thisweek">This Week</option>
                                    <option value="lastweek">Last Week</option>
                                    <option value="thismonth">This Month</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Search</label>
                                <input type="text" class="form-control" id="searchInput" placeholder="Reference # or Student Name" oninput="debounceSearch()">
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

        <!-- Payment Verification Table -->
        <div class="row">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Pending Payment Verifications</h5>
                        <span class="badge bg-danger" id="pendingCount">Loading...</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Reference #</th>
                                        <th>Student</th>
                                        <th>Payment Type</th>
                                        <th>Amount</th>
                                        <th>Date Submitted</th>
                                        <th>Valid Until</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="paymentsVerificationTable">
                                    <!-- Dynamic content will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center mb-0">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                                </li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item">
                                    <a class="page-link" href="#">Next</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let currentPage = 1;
        let totalPages = 1;
        let searchTimeout;
        
        // Debounce search to prevent too many API calls
        function debounceSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                filterPayments();
            }, 500);
        }
        
        // Load data when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadPaymentTypes();
            loadPendingVerifications();
        });
        
        // Load payment types dynamically (only actual existing ones)
        function loadPaymentTypes() {
            fetch('../../../action/cashier/payment_handler.php?action=get_payment_types')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const filter = document.getElementById('paymentTypeFilter');
                        // Keep only "All Types" option
                        filter.innerHTML = '<option value="all">All Types</option>';
                        
                        // Add only actual payment types from database
                        data.data.forEach(type => {
                            const option = document.createElement('option');
                            option.value = type.value;
                            option.textContent = type.label;
                            filter.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.log('Error loading payment types:', error);
                });
        }
        
        // Load pending verifications
        function loadPendingVerifications(page = 1) {
            const filters = {
                payment_type: document.getElementById('paymentTypeFilter').value,
                date_range: document.getElementById('dateRangeFilter').value,
                search: document.getElementById('searchInput').value,
                page: page,
                limit: 10
            };
            
            const queryString = new URLSearchParams(filters).toString();
            
            fetch(`../../../action/cashier/payment_handler.php?action=get_pending_verifications&${queryString}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayPendingVerifications(data.data.verifications);
                        updatePendingCount(data.data.total_count);
                        updatePagination(data.data.current_page, data.data.total_pages);
                        currentPage = data.data.current_page;
                        totalPages = data.data.total_pages;
                    } else {
                        console.error('Error loading pending verifications:', data.message);
                        document.getElementById('paymentsVerificationTable').innerHTML = 
                            '<tr><td colspan="7" class="text-center text-danger">Error loading data: ' + (data.message || 'Unknown error') + '</td></tr>';
                    }
                })
                .catch(error => {
                    console.error('Error loading pending verifications:', error);
                    document.getElementById('paymentsVerificationTable').innerHTML = 
                        '<tr><td colspan="7" class="text-center text-danger">Network error: ' + error.message + '</td></tr>';
                });
        }
        
        // Display pending verifications in table
        function displayPendingVerifications(verifications) {
            const tbody = document.getElementById('paymentsVerificationTable');
            tbody.innerHTML = '';
            
            if (verifications.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center">No pending verifications found</td></tr>';
                return;
            }
            
            verifications.forEach(verification => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${verification.reference_number}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div>
                                <div class="fw-bold">${verification.student_name}</div>
                                <small class="text-muted">${verification.student_id}</small>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge bg-info">${verification.payment_method}</span></td>
                    <td>₱${verification.amount}</td>
                    <td>${verification.date_submitted}</td>
                    <td>${verification.valid_until}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-success" onclick="approvePayment(${verification.id})" title="Approve">
                                <i class="bi bi-check-circle"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="rejectPayment(${verification.id})" title="Reject">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }
        
        // Update pending count
        function updatePendingCount(count) {
            document.getElementById('pendingCount').textContent = `${count} pending`;
        }
        
        // Update pagination
        function updatePagination(current, total) {
            const pagination = document.querySelector('.pagination');
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
                loadPendingVerifications(page);
            }
        }
        
        // Filter payments based on selected criteria
        function filterPayments() {
            currentPage = 1; // Reset to first page when filtering
            loadPendingVerifications();
        }
        
        // Clear all filters
        function clearFilters() {
            document.getElementById('paymentTypeFilter').value = 'all';
            document.getElementById('dateRangeFilter').value = 'all';
            document.getElementById('searchInput').value = '';
            filterPayments();
        }
        
        // Refresh payment list
        function refreshPaymentList() {
            loadPendingVerifications(); // Reload the list
        }
        
        // Approve payment
        function approvePayment(paymentId) {
            Swal.fire({
                title: 'Confirm Approval',
                text: 'Are you sure you want to approve this payment?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, approve it!'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const formData = new FormData();
                        formData.append('payment_id', paymentId);
                        
                        const response = await fetch('../../../action/cashier/payment_handler.php?action=approve_payment', {
                            method: 'POST',
                            body: formData
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            Swal.fire('Approved!', data.message, 'success').then(() => {
                                loadPendingVerifications(); // Reload the list
                            });
                        } else {
                            Swal.fire('Error!', data.message, 'error');
                        }
                    } catch (error) {
                        console.error('Error approving payment:', error);
                        Swal.fire('Error!', 'Failed to approve payment', 'error');
                    }
                }
            });
        }
        
        // Reject payment
        function rejectPayment(paymentId) {
            Swal.fire({
                title: 'Confirm Rejection',
                text: 'Please provide a reason for rejecting this payment:',
                input: 'text',
                inputPlaceholder: 'Reason for rejection',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Reject Payment',
                inputValidator: (value) => {
                    if (!value) {
                        return 'You need to provide a reason!';
                    }
                }
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const formData = new FormData();
                        formData.append('payment_id', paymentId);
                        formData.append('reason', result.value);
                        
                        const response = await fetch('../../../action/cashier/payment_handler.php?action=reject_payment', {
                            method: 'POST',
                            body: formData
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            Swal.fire('Rejected!', data.message, 'success').then(() => {
                                loadPendingVerifications(); // Reload the list
                            });
                        } else {
                            Swal.fire('Error!', data.message, 'error');
                        }
                    } catch (error) {
                        console.error('Error rejecting payment:', error);
                        Swal.fire('Error!', 'Failed to reject payment', 'error');
                    }
                }
            });
        }
    </script>

    <?php include '../../includes/notifications-modal.php'; ?>

    <script src="../../../assets/js/notifications.js?v=<?php echo time(); ?>"></script>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>
