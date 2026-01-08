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
                                <h2 class="fw-bold mb-1">Official Receipts</h2>
                                <p class="text-muted mb-0">Manage and print official payment receipts</p>
                            </div>
                            <div>
                                <button class="btn btn-primary" onclick="refreshReceiptList()">
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
                                <label class="form-label">Date Range</label>
                                <select class="form-select" id="dateRangeFilter" onchange="filterReceipts()">
                                    <option value="today">Today</option>
                                    <option value="yesterday">Yesterday</option>
                                    <option value="thisweek" selected>This Week</option>
                                    <option value="lastweek">Last Week</option>
                                    <option value="thismonth">This Month</option>
                                    <option value="lastmonth">Last Month</option>
                                    <option value="custom">Custom Range</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Payment Type</label>
                                <select class="form-select" id="paymentTypeFilter" onchange="filterReceipts()">
                                    <option value="all">All Types</option>
                                    <option value="cash">Cash</option>
                                    <option value="check">Check</option>
                                    <option value="bank">Bank Transfer</option>
                                    <option value="gcash">GCash</option>
                                    <option value="paymaya">PayMaya</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Search</label>
                                <input type="text" class="form-control" id="searchInput" placeholder="OR Number, Student ID or Name" onkeyup="filterReceipts()">
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
                        
                        <!-- Custom date range (initially hidden) -->
                        <div class="row mt-3 d-none" id="customDateRange">
                            <div class="col-md-3">
                                <label class="form-label">From Date</label>
                                <input type="date" class="form-control" id="fromDate" onchange="filterReceipts()">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">To Date</label>
                                <input type="date" class="form-control" id="toDate" onchange="filterReceipts()">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Official Receipts Table -->
        <div class="row">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Official Receipt Records</h5>
                        <span class="badge bg-primary" id="receiptCount">Loading...</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>OR Number</th>
                                        <th>Date</th>
                                        <th>Student</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                        <th>Payment Type</th>
                                        <th>Issued By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="receiptsTableBody">
                                    <!-- Dynamic data will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <nav aria-label="Receipt pagination">
                            <ul class="pagination justify-content-center" id="receiptPagination">
                                <!-- Pagination will be dynamically generated -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Receipt Details Modal -->
    <div class="modal fade" id="receiptDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Official Receipt Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Receipt Preview -->
                    <div class="border p-3 mb-3" id="receiptPreview">
                        <div class="text-center mb-3">
                            <h5 class="mb-0">NATIONAL COLLEGE OF SCIENCE AND TECHNOLOGY</h5>
                            <p class="small mb-0">Amafel Building, Aguinaldo Highway</p>
                            <p class="small mb-0">Dasmariñas City, Cavite</p>
                            <h6 class="mt-3 border-top border-bottom py-1">OFFICIAL RECEIPT</h6>
                        </div>
                        
                        <div class="row mb-2">
                            <div class="col-6">
                                <p class="mb-0"><strong>Receipt No:</strong> <span id="receiptNo">-</span></p>
                            </div>
                            <div class="col-6 text-end">
                                <p class="mb-0"><strong>Date:</strong> <span id="receiptDate">-</span></p>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <p class="mb-1"><strong>Student:</strong> <span id="receiptStudent">-</span></p>
                            <p class="mb-1"><strong>Course/Year:</strong> <span id="receiptCourse">-</span></p>
                            <p class="mb-0"><strong>Semester:</strong> <span id="receiptSemester">-</span></p>
                        </div>
                        
                        <div class="table-responsive mb-3">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th width="70%">Particular</th>
                                        <th width="30%" class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>-</td>
                                        <td class="text-end">-</td>
                                    </tr>
                                    <tr>
                                        <th>TOTAL</th>
                                        <th class="text-end">-</th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-6">
                                <p class="mb-0"><strong>Payment Method:</strong> <span id="receiptPaymentMethod">-</span></p>
                            </div>
                            <div class="col-6">
                                <p class="mb-0"><strong>Received By:</strong> <span id="receiptCashier">-</span></p>
                            </div>
                        </div>
                        
                        <div class="text-center small mt-4">
                            <p class="mb-0">This is your official receipt. Please keep this for your records.</p>
                            <p class="mb-0">Thank you!</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Receipt Modal -->
    <div class="modal fade" id="editReceiptModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Receipt Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editReceiptForm">
                        <div class="mb-3">
                            <label class="form-label">OR Number</label>
                            <input type="text" class="form-control" id="editOrNumber" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control" id="editDate" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Student ID</label>
                            <input type="text" class="form-control" id="editStudentId" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Student Name</label>
                            <input type="text" class="form-control" id="editStudentName" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <input type="text" class="form-control" id="editDescription" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount</label>
                            <input type="text" class="form-control" id="editAmount" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <select class="form-select" id="editPaymentMethod" required>
                                <option value="cash">Cash</option>
                                <option value="check">Check</option>
                                <option value="bank">Bank Transfer</option>
                                <option value="gcash">GCash</option>
                                <option value="paymaya">PayMaya</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cashier/Staff</label>
                            <input type="text" class="form-control" id="editCashier" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes (Optional)</label>
                            <textarea class="form-control" id="editNotes" rows="2"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="updateReceipt()">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
    
    <!-- Include JavaScript -->
    <script>
        let currentPage = 1;
        let receiptsData = [];
        
        // Load receipts on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadReceipts();
            
            // Set up modal event listeners once
            const receiptDetailsModal = document.getElementById('receiptDetailsModal');
            const editReceiptModal = document.getElementById('editReceiptModal');
            
            // Fix aria-hidden for receipt details modal
            receiptDetailsModal.addEventListener('shown.bs.modal', function () {
                this.removeAttribute('aria-hidden');
            });
            receiptDetailsModal.addEventListener('hidden.bs.modal', function () {
                this.setAttribute('aria-hidden', 'true');
            });
            
            // Fix aria-hidden for edit receipt modal
            editReceiptModal.addEventListener('shown.bs.modal', function () {
                this.removeAttribute('aria-hidden');
            });
            editReceiptModal.addEventListener('hidden.bs.modal', function () {
                this.setAttribute('aria-hidden', 'true');
            });
            
            // Attach filter event listeners
            document.getElementById('dateRangeFilter').addEventListener('change', function() {
                const customDateRange = document.getElementById('customDateRange');
                if (this.value === 'custom') {
                    customDateRange.classList.remove('d-none');
                    
                    // Set default dates (today and 7 days ago)
                    const today = new Date();
                    const lastWeek = new Date(today);
                    lastWeek.setDate(lastWeek.getDate() - 7);
                    
                    document.getElementById('toDate').value = today.toISOString().split('T')[0];
                    document.getElementById('fromDate').value = lastWeek.toISOString().split('T')[0];
                } else {
                    customDateRange.classList.add('d-none');
                }
                filterReceipts();
            });
            
            document.getElementById('paymentTypeFilter').addEventListener('change', filterReceipts);
            document.getElementById('searchInput').addEventListener('input', filterReceipts);
        });
        
        // Load receipts from backend
        async function loadReceipts(page = 1, filters = {}) {
            try {
                const params = new URLSearchParams({
                    action: 'get_receipts',
                    page: page,
                    limit: 10,
                    ...filters
                });
                
                const response = await fetch('/enrollmentsystem/action/cashier/receipt_handler.php?' + params);
                const result = await response.json();
                
                if (result.success) {
                    // The receipts are nested in result.data.receipts
                    const receipts = result.data.receipts || [];
                    receiptsData = receipts;
                    displayReceipts(receipts);
                    updateReceiptCount(result.data.total_count || 0);
                    updatePagination({
                        current_page: result.data.current_page || 1,
                        total_pages: result.data.total_pages || 1,
                        total_count: result.data.total_count || 0
                    });
                } else {
                    console.error('Error loading receipts:', result.message);
                    showNoDataMessage();
                }
            } catch (error) {
                console.error('Error:', error);
                showNoDataMessage();
            }
        }
        
        // Display receipts in table
        function displayReceipts(receipts) {
            const tbody = document.getElementById('receiptsTableBody');
            
            // Debug: log what we received
            console.log('displayReceipts received:', receipts);
            
            // Ensure receipts is an array
            if (!receipts) {
                console.warn('Receipts is null or undefined');
                receipts = [];
            } else if (!Array.isArray(receipts)) {
                console.warn('Receipts is not an array:', typeof receipts);
                receipts = [];
            }
            
            if (receipts.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                No receipts found
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }
            
            tbody.innerHTML = receipts.map(receipt => `
                <tr>
                    <td>${receipt.or_number}</td>
                    <td>${formatDate(receipt.date)}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div>
                                <div class="fw-bold">${receipt.student_name}</div>
                                <small class="text-muted">${receipt.student_id}</small>
                            </div>
                        </div>
                    </td>
                    <td>${receipt.description}</td>
                    <td>₱${receipt.amount}</td>
                    <td><span class="badge ${getPaymentTypeBadge(receipt.payment_method)}">${receipt.payment_method}</span></td>
                    <td>${receipt.issued_by}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-primary" onclick="printReceipt('${receipt.or_number}')" title="Print Receipt">
                                <i class="bi bi-printer"></i>
                            </button>
                            <button class="btn btn-sm btn-secondary" onclick="viewReceiptDetails('${receipt.or_number}')" title="View Details">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-warning" onclick="editReceipt('${receipt.or_number}')" title="Edit Receipt">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }
        
        // Update receipt count
        function updateReceiptCount(total) {
            document.getElementById('receiptCount').textContent = `${total} receipts`;
        }
        
        // Update pagination
        function updatePagination(pagination) {
            const paginationContainer = document.getElementById('receiptPagination');
            
            if (!pagination || pagination.total_pages <= 1) {
                paginationContainer.innerHTML = '';
                return;
            }
            
            let paginationHTML = '';
            
            // Previous button
            paginationHTML += '<li class="page-item ' + (pagination.current_page <= 1 ? 'disabled' : '') + '">' +
                '<a class="page-link" href="#" onclick="changePage(' + (pagination.current_page - 1) + ')"' + 
                (pagination.current_page <= 1 ? ' tabindex="-1" aria-disabled="true"' : '') + '>Previous</a>' +
                '</li>';
            
            // Page numbers
            for (let i = 1; i <= pagination.total_pages; i++) {
                if (i === pagination.current_page) {
                    paginationHTML += '<li class="page-item active"><a class="page-link" href="#">' + i + '</a></li>';
                } else if (i === 1 || i === pagination.total_pages || (i >= pagination.current_page - 2 && i <= pagination.current_page + 2)) {
                    paginationHTML += '<li class="page-item"><a class="page-link" href="#" onclick="changePage(' + i + ')">' + i + '</a></li>';
                } else if (i === pagination.current_page - 3 || i === pagination.current_page + 3) {
                    paginationHTML += '<li class="page-item disabled"><span class="page-link">...</span></li>';
                }
            }
            
            // Next button
            paginationHTML += '<li class="page-item ' + (pagination.current_page >= pagination.total_pages ? 'disabled' : '') + '">' +
                '<a class="page-link" href="#" onclick="changePage(' + (pagination.current_page + 1) + ')"' + 
                (pagination.current_page >= pagination.total_pages ? ' tabindex="-1" aria-disabled="true"' : '') + '>Next</a>' +
                '</li>';
            
            paginationContainer.innerHTML = paginationHTML;
        }
        
        // Change page
        function changePage(page) {
            if (page < 1) return;
            currentPage = page;
            filterReceipts();
        }
        
        // Filter receipts based on selected criteria
        function filterReceipts() {
            const filters = {
                date_range: document.getElementById('dateRangeFilter').value,
                payment_type: document.getElementById('paymentTypeFilter').value,
                search: document.getElementById('searchInput').value
            };
            
            if (filters.date_range === 'custom') {
                filters.from_date = document.getElementById('fromDate').value;
                filters.to_date = document.getElementById('toDate').value;
            }
            
            loadReceipts(currentPage, filters);
        }
        
        // View receipt details
        async function viewReceiptDetails(orNumber) {
            try {
                console.log('Fetching receipt details for:', orNumber);
                const response = await fetch(`/enrollmentsystem/action/cashier/receipt_handler.php?action=get_receipt_details&or_number=${orNumber}`);
                const result = await response.json();
                
                console.log('API Response:', result);
                
                if (result.success) {
                    const receipt = result.data;
                    
                    // Update modal content with correct IDs
                    document.getElementById('receiptNo').textContent = receipt.or_number;
                    document.getElementById('receiptDate').textContent = formatDate(receipt.payment_date);
                    document.getElementById('receiptStudent').textContent = `${receipt.student_name} (${receipt.student_id})`;
                    document.getElementById('receiptCourse').textContent = receipt.course || 'N/A';
                    document.getElementById('receiptSemester').textContent = receipt.semester || 'N/A';
                    document.getElementById('receiptPaymentMethod').textContent = receipt.payment_method;
                    document.getElementById('receiptCashier').textContent = receipt.cashier_name;
                    
                    // Update the amount in the table
                    const tableBody = document.querySelector('#receiptDetailsModal tbody');
                    if (tableBody) {
                        tableBody.innerHTML = `
                            <tr>
                                <td>${receipt.description}</td>
                                <td class="text-end">₱${receipt.amount}</td>
                            </tr>
                            <tr>
                                <th>TOTAL</th>
                                <th class="text-end">₱${receipt.amount}</th>
                            </tr>
                        `;
                    }
                    
                    // Show modal with correct ID
                    const modalElement = document.getElementById('receiptDetailsModal');
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                } else {
                    Swal.fire('Error', result.message, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire('Error', 'Failed to load receipt details: ' + error.message, 'error');
            }
        }
        
        // Print receipt from modal
        function printReceiptFromModal() {
            const orNumber = document.getElementById('receiptNo').textContent;
            printReceipt(orNumber);
        }
        
        // Print receipt
        function printReceipt(orNumber) {
            // Find receipt data
            const receipt = receiptsData.find(r => r.or_number === orNumber);
            if (!receipt) {
                Swal.fire('Error', 'Receipt data not found', 'error');
                return;
            }
            
            const receiptContent = `
                <div class="text-center mb-3">
                    <h3 class="mb-1">University Name</h3>
                    <p class="mb-0">Address Line 1<br>Address Line 2</p>
                    <h4 class="mt-3 mb-3">OFFICIAL RECEIPT</h4>
                </div>
                
                <div class="row mb-3">
                    <div class="col-6">
                        <p class="mb-0"><strong>OR Number:</strong> ${receipt.or_number}</p>
                    </div>
                    <div class="col-6 text-end">
                        <p class="mb-0"><strong>Date:</strong> ${formatDate(receipt.date)}</p>
                    </div>
                </div>
                
                <div class="mb-3">
                    <p class="mb-1"><strong>Student:</strong> ${receipt.student_name} (${receipt.student_id})</p>
                    <p class="mb-1"><strong>Course/Year:</strong> ${receipt.course || 'N/A'}</p>
                    <p class="mb-0"><strong>Semester:</strong> ${receipt.semester || 'N/A'}</p>
                </div>
                
                <table class="table table-bordered mb-3">
                    <thead>
                        <tr>
                            <th width="70%">Particular</th>
                            <th width="30%" class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>${receipt.description}</td>
                            <td class="text-end">₱${receipt.amount}</td>
                        </tr>
                        <tr>
                            <th>TOTAL</th>
                            <th class="text-end">₱${receipt.amount}</th>
                        </tr>
                    </tbody>
                </table>
                
                <div class="row mb-3">
                    <div class="col-6">
                        <p class="mb-0"><strong>Payment Method:</strong> ${receipt.payment_method}</p>
                    </div>
                    <div class="col-6">
                        <p class="mb-0"><strong>Received By:</strong> ${receipt.issued_by}</p>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <p class="mb-0">This is your official receipt. Please keep this for your records.</p>
                    <p class="mb-0">Thank you!</p>
                </div>
            `;
            
            const printWindow = window.open('', '_blank');
            printWindow.document.write("<!DOCTYPE html><html><head><title>Official Receipt - " + orNumber + "</title><style>body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; } .container { max-width: 800px; margin: 0 auto; } .text-center { text-align: center; } .text-end { text-align: right; } .mb-3 { margin-bottom: 15px; } .mb-0 { margin-bottom: 0; } .mb-1 { margin-bottom: 5px; } .border-top { border-top: 1px solid #000; padding-top: 5px; } .border-bottom { border-bottom: 1px solid #000; padding-bottom: 5px; } table { width: 100%; border-collapse: collapse; } th, td { border: 1px solid #000; padding: 5px; } th { background-color: #f0f0f0; }</style></head><body><div class='container'>" + receiptContent + "</div><scr" + "ipt>window.onload = function() { window.print(); setTimeout(function() { window.close(); }, 500); };</scr" + "ipt></body></html>");
            
            printWindow.document.close();
        }
        
        // Edit receipt
        async function editReceipt(orNumber) {
            try {
                const response = await fetch(`/enrollmentsystem/action/cashier/receipt_handler.php?action=get_receipt_details&or_number=${orNumber}`);
                const result = await response.json();
                
                if (result.success) {
                    const receipt = result.data;
                    
                    // Update modal with receipt info
                    document.getElementById('editOrNumber').value = receipt.or_number;
                    document.getElementById('editStudentId').value = receipt.student_id;
                    document.getElementById('editStudentName').value = receipt.student_name;
                    document.getElementById('editDescription').value = receipt.description;
                    document.getElementById('editAmount').value = receipt.amount;
                    document.getElementById('editPaymentMethod').value = receipt.payment_method.toLowerCase();
                    document.getElementById('editCashier').value = receipt.cashier_name;
                    document.getElementById('editDate').value = receipt.payment_date;
                    document.getElementById('editNotes').value = receipt.notes || '';
                    
                    // Show modal
                    const modalElement = document.getElementById('editReceiptModal');
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                } else {
                    Swal.fire('Error', result.message, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire('Error', 'Failed to load receipt details', 'error');
            }
        }
        
        // Update receipt
        async function updateReceipt() {
            const form = document.getElementById('editReceiptForm');
            
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            const formData = new FormData();
            formData.append('action', 'update_receipt');
            formData.append('or_number', document.getElementById('editOrNumber').value);
            formData.append('date', document.getElementById('editDate').value);
            formData.append('description', document.getElementById('editDescription').value);
            formData.append('amount', document.getElementById('editAmount').value);
            formData.append('payment_method', document.getElementById('editPaymentMethod').value);
            formData.append('cashier', document.getElementById('editCashier').value);
            formData.append('notes', document.getElementById('editNotes').value);
            
            try {
                const response = await fetch('/enrollmentsystem/action/cashier/receipt_handler.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    Swal.fire({
                        title: 'Receipt Updated',
                        text: 'Receipt has been updated successfully.',
                        icon: 'success',
                        confirmButtonColor: '#28a745'
                    }).then(() => {
                        // Close modal and refresh
                        document.getElementById('editReceiptModal').querySelector('.btn-close').click();
                        filterReceipts();
                    });
                } else {
                    Swal.fire('Error', result.message, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire('Error', 'Failed to update receipt', 'error');
            }
        }
        
        // Helper functions
        function formatDate(dateString) {
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
        
        function getPaymentTypeBadge(paymentType) {
            const type = paymentType.toLowerCase();
            switch (type) {
                case 'cash': return 'bg-success';
                case 'check': return 'bg-warning';
                case 'bank transfer': return 'bg-primary';
                case 'gcash': return 'bg-info';
                case 'paymaya': return 'bg-secondary';
                default: return 'bg-secondary';
            }
        }
        
        function showNoDataMessage() {
            const tbody = document.getElementById('receiptsTableBody');
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <div class="text-muted">
                            <i class="bi bi-exclamation-circle fs-3 d-block mb-2"></i>
                            Failed to load receipts
                        </div>
                    </td>
                </tr>
            `;
            document.getElementById('receiptCount').textContent = 'Error loading data';
        }
    </script>

    <!-- Notifications Modal -->
    <?php include '../../includes/notifications-modal.php'; ?>

    <!-- Include notification JavaScript -->
    <script src="../../../assets/js/notifications.js?v=<?php echo time(); ?>"></script>
</body>
</html>