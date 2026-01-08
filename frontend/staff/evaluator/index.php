<?php
// Start session and require authentication
session_start();
require_once '../../includes/evaluator_auth.php';

// Check if evaluator is logged in
requireEvaluatorAuth();

// Get current evaluator info
$evaluator = getCurrentEvaluator();
?>
<!DOCTYPE html>
<html lang="en">
<?php include '../../includes/header.php'; ?>
<body class="evaluator-dashboard">
    <?php include '../../includes/navbar.php'; ?>

    <!-- Custom Evaluator Styles -->
    <style>
        .evaluator-dashboard {
            background-color: #f8f9fc;
        }
        .dashboard-card {
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        .pending { background: #fff3cd; color: #856404; }
        .approved { background: #d1edff; color: #0c5460; }
        .rejected { background: #f8d7da; color: #721c24; }
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .welcome-card {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
    </style>
    
    <div class="container py-5">
        <!-- Welcome Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card welcome-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="fw-bold mb-1">Welcome, <?php echo htmlspecialchars($evaluator['name']); ?></h2>
                                <p class="mb-0">NCST Enrollment System - Evaluator Dashboard</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <i class="bi bi-clock-history text-warning" style="font-size: 2rem;"></i>
                    <h3 class="mt-2 mb-1" id="pendingCount">0</h3>
                    <p class="text-muted mb-0">Pending</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                    <h3 class="mt-2 mb-1" id="approvedCount">0</h3>
                    <p class="text-muted mb-0">Approved Today</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <i class="bi bi-x-circle text-danger" style="font-size: 2rem;"></i>
                    <h3 class="mt-2 mb-1" id="rejectedCount">0</h3>
                    <p class="text-muted mb-0">Rejected Today</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <i class="bi bi-people text-info" style="font-size: 2rem;"></i>
                    <h3 class="mt-2 mb-1" id="totalCount">0</h3>
                    <p class="text-muted mb-0">Total</p>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Filter by Status:</label>
                                <select class="form-select" id="statusFilter" onchange="filterRegistrations()">
                                    <option value="all">All Applications</option>
                                    <option value="pending" selected>Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Search:</label>
                                <input type="text" class="form-control" id="searchInput" placeholder="Name, Email, or ID" onkeyup="searchRegistrations()">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button class="btn btn-primary w-50 me-2" onclick="refreshData()">
                                    <i class="bi bi-arrow-clockwise me-1"></i> Refresh
                                </button>
                                <button class="btn btn-secondary w-50" onclick="clearFilters()">
                                    <i class="bi bi-x-circle me-1"></i> Clear
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Applications List -->
        <div class="row">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Registration Applications</h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="registrationsContainer">
                            <!-- Loading state -->
                            <div class="text-center py-5" id="loadingState">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-3 text-muted">Loading registration applications...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Registration Details Modal -->
    <div class="modal fade" id="registrationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registration Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="registrationDetails">
                    <!-- Details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" onclick="updateStatus('approved')">Approve</button>
                    <button type="button" class="btn btn-danger" onclick="updateStatus('rejected')">Reject</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS and SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Global variables
        let allRegistrations = [];
        let currentRegistration = null;
        const registrationModal = new bootstrap.Modal(document.getElementById('registrationModal'));

        // Load data on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Check if required elements exist
            if (!document.getElementById('statusFilter') || !document.getElementById('searchInput')) {
                console.error('Required form elements not found');
                return;
            }
            
            loadStatistics();
            loadRegistrations();
        });

        // Load statistics
        async function loadStatistics() {
            try {
                const response = await fetch('../../../action/evaluator/get_statistics.php');
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('pendingCount').textContent = data.stats.pending;
                    document.getElementById('approvedCount').textContent = data.stats.approved_today;
                    document.getElementById('rejectedCount').textContent = data.stats.rejected_today;
                    document.getElementById('totalCount').textContent = data.stats.total;
                } else {
                    showError(data.message || 'Failed to load statistics');
                }
            } catch (error) {
                console.error('Error loading statistics:', error);
                showError('Failed to load statistics. Please try again.');
            }
        }

        // Load registrations
        async function loadRegistrations() {
            try {
                const status = document.getElementById('statusFilter').value;
                const response = await fetch(`../../../action/evaluator/get_registrations.php?status=${status}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    allRegistrations = data.registrations;
                    displayRegistrations(allRegistrations);
                } else {
                    showError(data.message || 'Failed to load registrations');
                }
            } catch (error) {
                console.error('Error loading registrations:', error);
                showError('Failed to load registrations. Please try again.');
            }
        }

        // Display registrations in the table
        function displayRegistrations(registrations) {
            const container = document.getElementById('registrationsContainer');
            
            // Remove loading state
            const loadingState = document.getElementById('loadingState');
            if (loadingState) {
                loadingState.remove();
            }
            
            // Show message if no registrations
            if (registrations.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-5">
                        <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                        <p class="mt-3 text-muted">No registration applications found.</p>
                    </div>
                `;
                return;
            }
            
            // Create table with registrations
            let html = `
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Course</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            registrations.forEach(reg => {
                const statusClass = 
                    reg.status === 'pending' ? 'pending' : 
                    reg.status === 'approved' ? 'approved' : 'rejected';
                
                html += `
                    <tr>
                        <td>${reg.id}</td>
                        <td>${reg.last_name}, ${reg.first_name}</td>
                        <td>${reg.email_address}</td>
                        <td>${reg.desired_course || 'N/A'}</td>
                        <td>${formatDate(reg.created_at)}</td>
                        <td>
                            <span class="status-badge ${statusClass}">
                                ${reg.status.charAt(0).toUpperCase() + reg.status.slice(1)}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="viewRegistration(${reg.id})">
                                <i class="bi bi-eye"></i>
                            </button>
                            ${reg.status === 'pending' ? `
                                <button class="btn btn-sm btn-success" onclick="quickApprove(${reg.id})">
                                    <i class="bi bi-check"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="quickReject(${reg.id})">
                                    <i class="bi bi-x"></i>
                                </button>
                            ` : ''}
                        </td>
                    </tr>
                `;
            });
            
            html += `
                        </tbody>
                    </table>
                </div>
            `;
            
            container.innerHTML = html;
        }

        // View registration details
        async function viewRegistration(id) {
            try {
                const response = await fetch(`../../../action/evaluator/get_registration_details.php?id=${id}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    currentRegistration = data.registration;
                    showRegistrationDetails(currentRegistration);
                    registrationModal.show();
                } else {
                    showError(data.message || 'Failed to load registration details');
                }
            } catch (error) {
                console.error('Error loading registration details:', error);
                showError('Failed to load registration details. Please try again.');
            }
        }

        // Show registration details in modal
        function showRegistrationDetails(reg) {
            const detailsContainer = document.getElementById('registrationDetails');
            
            // Format details HTML
            const html = `
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-bold">Personal Information</h6>
                        <p><strong>Name:</strong> ${reg.last_name}, ${reg.first_name} ${reg.middle_name || ''}</p>
                        <p><strong>Email:</strong> ${reg.email_address}</p>
                        <p><strong>Phone:</strong> ${reg.mobile_no || 'N/A'}</p>
                        <p><strong>Address:</strong> ${reg.complete_address || 'N/A'}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold">Educational Information</h6>
                        <p><strong>Course:</strong> ${reg.desired_course || 'N/A'}</p>
                        <p><strong>Student Type:</strong> ${reg.student_type || 'N/A'}</p>
                        <p><strong>Previous School:</strong> ${reg.tertiary_school || 'N/A'}</p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-12">
                        <h6 class="fw-bold">Status Information</h6>
                        <p><strong>Status:</strong> 
                            <span class="status-badge ${reg.status}">
                                ${reg.status.charAt(0).toUpperCase() + reg.status.slice(1)}
                            </span>
                        </p>
                        <p><strong>Registration Date:</strong> ${formatDateTime(reg.created_at)}</p>
                        <p><strong>Last Updated:</strong> ${reg.updated_at ? formatDateTime(reg.updated_at) : 'N/A'}</p>
                    </div>
                </div>
            `;
            
            detailsContainer.innerHTML = html;
        }

        // Quick approve function
        async function quickApprove(id) {
            const result = await Swal.fire({
                title: 'Confirm Approval',
                text: 'Are you sure you want to approve this registration?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Approve',
                cancelButtonText: 'Cancel'
            });
            
            if (result.isConfirmed) {
                await updateRegistrationStatus(id, 'approved');
            }
        }

        // Quick reject function
        async function quickReject(id) {
            const result = await Swal.fire({
                title: 'Confirm Rejection',
                text: 'Are you sure you want to reject this registration?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Reject',
                cancelButtonText: 'Cancel'
            });
            
            if (result.isConfirmed) {
                await updateRegistrationStatus(id, 'rejected');
            }
        }

        // Update status from modal
        async function updateStatus(status) {
            if (!currentRegistration) return;
            
            await updateRegistrationStatus(currentRegistration.id, status);
            registrationModal.hide();
        }

        // Update registration status
        async function updateRegistrationStatus(id, status) {
            try {
                const response = await fetch('../../../action/evaluator/update_registration_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        registration_id: id,
                        status: status
                    })
                });
                
                // Check if response is ok
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                // Get response text first to debug
                const responseText = await response.text();
                
                // Try to parse as JSON
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (jsonError) {
                    console.error('Raw response:', responseText);
                    console.error('JSON parsing error:', jsonError);
                    throw new Error('Invalid JSON response from server. Check console for details.');
                }
                
                if (data.success) {
                    Swal.fire({
                        title: 'Success',
                        text: `Registration has been ${status}.`,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                    // Refresh data
                    loadStatistics();
                    loadRegistrations();
                } else {
                    showError(data.message || `Failed to ${status} registration`);
                }
            } catch (error) {
                console.error(`Error ${status} registration:`, error);
                showError(`Failed to ${status} registration. Please try again.`);
            }
        }

        // Filter and search functions
        function filterRegistrations() {
            loadRegistrations(); // Always reload with current filter
        }

        function searchRegistrations() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const statusFilter = document.getElementById('statusFilter').value;
            
            let filtered = [...allRegistrations];
            
            // Filter by status
            if (statusFilter !== 'all') {
                filtered = filtered.filter(reg => reg.status === statusFilter);
            }
            
            // Filter by search term
            if (searchTerm) {
                filtered = filtered.filter(reg => 
                    reg.first_name.toLowerCase().includes(searchTerm) ||
                    reg.last_name.toLowerCase().includes(searchTerm) ||
                    reg.email_address.toLowerCase().includes(searchTerm) ||
                    (reg.student_id && reg.student_id.toLowerCase().includes(searchTerm))
                );
            }
            
            displayRegistrations(filtered);
        }

        function clearFilters() {
            document.getElementById('statusFilter').value = 'all';
            document.getElementById('searchInput').value = '';
            loadRegistrations();
        }

        function refreshData() {
            loadStatistics();
            loadRegistrations();
        }

        // Utility functions
        function formatDate(dateString) {
            return new Date(dateString).toLocaleDateString();
        }

        function formatDateTime(dateString) {
            return new Date(dateString).toLocaleString();
        }

        function showError(message) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message
            });
        }

        // Logout function
        async function logoutEvaluator() {
            const result = await Swal.fire({
                title: 'Logout Confirmation',
                text: 'Are you sure you want to logout?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Logout',
                cancelButtonText: 'Cancel'
            });

            if (result.isConfirmed) {
                try {
                    const response = await fetch('../../../action/evaluator/logout.php', {
                        method: 'POST'
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        Swal.fire({
                            title: 'Logged Out',
                            text: 'You have been successfully logged out.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = data.redirect_url || '../index.php';
                        });
                    } else {
                        throw new Error(data.message || 'Logout failed');
                    }
                } catch (error) {
                    console.error('Logout error:', error);
                    window.location.href = '../index.php';
                }
            }
        }
    </script>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>