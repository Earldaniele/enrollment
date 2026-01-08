<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluator Dashboard - NCST Enrollment System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .dashboard-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            padding: 2rem;
            margin-bottom: 2rem;
            transition: all 0.3s ease;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
        }
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        .pending-badge { background: #ffc107; }
        .approved-badge { background: #28a745; }
        .rejected-badge { background: #dc3545; }
        .navbar-custom {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .registration-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        .registration-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        .action-btn {
            border-radius: 20px;
            padding: 8px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .action-btn:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="bi bi-mortarboard-fill me-2"></i>
                NCST Evaluator Portal
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-bell me-1"></i>Notifications</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="confirmLogout(); return false;"><i class="bi bi-box-arrow-right me-1"></i>Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="dashboard-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="fw-bold text-primary mb-1">Evaluator Dashboard</h2>
                            <p class="text-muted mb-0">Review and approve student registrations</p>
                        </div>
                        <div class="text-end">
                            <small class="text-muted">Last updated: <span id="lastUpdated">Loading...</span></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="stats-card">
                    <div class="display-6 fw-bold text-warning" id="pendingCount">0</div>
                    <h6 class="mt-2 mb-0">Pending Reviews</h6>
                    <small class="text-muted">Awaiting evaluation</small>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stats-card">
                    <div class="display-6 fw-bold text-success" id="approvedCount">0</div>
                    <h6 class="mt-2 mb-0">Approved Today</h6>
                    <small class="text-muted">Successfully processed</small>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stats-card">
                    <div class="display-6 fw-bold text-danger" id="rejectedCount">0</div>
                    <h6 class="mt-2 mb-0">Rejected Today</h6>
                    <small class="text-muted">Needs revision</small>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stats-card">
                    <div class="display-6 fw-bold text-info" id="totalCount">0</div>
                    <h6 class="mt-2 mb-0">Total Applications</h6>
                    <small class="text-muted">All time</small>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row">
            <div class="col-12">
                <div class="dashboard-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0"><i class="bi bi-funnel me-2"></i>Student Registrations</h5>
                        <div class="d-flex gap-2">
                            <select class="form-select" id="statusFilter" style="width: auto;">
                                <option value="all">All Status</option>
                                <option value="pending" selected>Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                            <button class="btn btn-outline-primary" onclick="refreshRegistrations()">
                                <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                            </button>
                        </div>
                    </div>
                    
                    <!-- Loading -->
                    <div id="loadingSection" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading registrations...</p>
                    </div>

                    <!-- Registrations List -->
                    <div id="registrationsContainer" style="display: none;">
                        <!-- Registrations will be loaded here -->
                    </div>

                    <!-- No Data -->
                    <div id="noDataSection" class="text-center py-5" style="display: none;">
                        <i class="bi bi-inbox display-4 text-muted mb-3"></i>
                        <h6 class="text-muted">No registrations found</h6>
                        <p class="text-muted">No student registrations match the current filter.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Registration Details Modal -->
    <div class="modal fade" id="registrationModal" tabindex="-1" aria-labelledby="registrationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registrationModalLabel">
                        <i class="bi bi-person-lines-fill me-2"></i>Student Registration Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="registrationDetails">
                    <!-- Registration details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger action-btn" onclick="updateRegistrationStatus('rejected')">
                        <i class="bi bi-x-circle me-1"></i>Reject
                    </button>
                    <button type="button" class="btn btn-success action-btn" onclick="updateRegistrationStatus('approved')">
                        <i class="bi bi-check-circle me-1"></i>Approve
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let currentRegistrationId = null;

        // Load registrations on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadStatistics();
            loadRegistrations();
            updateTimestamp();
        });

        // Status filter change
        document.getElementById('statusFilter').addEventListener('change', function() {
            loadRegistrations();
        });

        // Load statistics
        async function loadStatistics() {
            try {
                const response = await fetch('../../action/evaluator/get_statistics.php');
                const result = await response.json();
                
                if (result.success) {
                    document.getElementById('pendingCount').textContent = result.data.pending || 0;
                    document.getElementById('approvedCount').textContent = result.data.approved_today || 0;
                    document.getElementById('rejectedCount').textContent = result.data.rejected_today || 0;
                    document.getElementById('totalCount').textContent = result.data.total || 0;
                }
            } catch (error) {
                console.error('Error loading statistics:', error);
            }
        }

        // Load registrations
        async function loadRegistrations() {
            const status = document.getElementById('statusFilter').value;
            
            // Show loading
            document.getElementById('loadingSection').style.display = 'block';
            document.getElementById('registrationsContainer').style.display = 'none';
            document.getElementById('noDataSection').style.display = 'none';
            
            try {
                const response = await fetch(`../../action/evaluator/get_registrations.php?status=${status}`);
                const result = await response.json();
                
                if (result.success && result.data.length > 0) {
                    displayRegistrations(result.data);
                } else {
                    showNoData();
                }
            } catch (error) {
                console.error('Error loading registrations:', error);
                showNoData();
            } finally {
                document.getElementById('loadingSection').style.display = 'none';
            }
        }

        // Display registrations
        function displayRegistrations(registrations) {
            const container = document.getElementById('registrationsContainer');
            let html = '';
            
            registrations.forEach(registration => {
                const statusBadge = getStatusBadge(registration.status);
                const createdDate = new Date(registration.created_at).toLocaleDateString();
                
                html += `
                    <div class="registration-card mb-3 p-3">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <h6 class="mb-1 fw-bold">${registration.last_name}, ${registration.first_name}</h6>
                                <small class="text-muted">${registration.student_id || 'Pending ID'}</small>
                            </div>
                            <div class="col-md-2">
                                <small class="text-muted">Course</small>
                                <div class="fw-semibold">${registration.desired_course}</div>
                            </div>
                            <div class="col-md-2">
                                <small class="text-muted">Email</small>
                                <div class="small">${registration.email_address}</div>
                            </div>
                            <div class="col-md-2">
                                <small class="text-muted">Applied</small>
                                <div class="small">${createdDate}</div>
                            </div>
                            <div class="col-md-1 text-center">
                                ${statusBadge}
                            </div>
                            <div class="col-md-2 text-end">
                                <button class="btn btn-outline-primary btn-sm action-btn" onclick="viewRegistration(${registration.id})">
                                    <i class="bi bi-eye me-1"></i>Review
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html;
            document.getElementById('registrationsContainer').style.display = 'block';
        }

        // Show no data
        function showNoData() {
            document.getElementById('noDataSection').style.display = 'block';
        }

        // Get status badge
        function getStatusBadge(status) {
            switch(status) {
                case 'pending':
                    return '<span class="badge pending-badge">Pending</span>';
                case 'approved':
                    return '<span class="badge approved-badge">Approved</span>';
                case 'rejected':
                    return '<span class="badge rejected-badge">Rejected</span>';
                default:
                    return '<span class="badge bg-secondary">Unknown</span>';
            }
        }

        // View registration details
        async function viewRegistration(registrationId) {
            currentRegistrationId = registrationId;
            
            try {
                const response = await fetch(`../../action/evaluator/get_registration_details.php?id=${registrationId}`);
                const result = await response.json();
                
                if (result.success) {
                    displayRegistrationDetails(result.data);
                    const modal = new bootstrap.Modal(document.getElementById('registrationModal'));
                    modal.show();
                } else {
                    Swal.fire('Error', result.message || 'Failed to load registration details', 'error');
                }
            } catch (error) {
                console.error('Error loading registration details:', error);
                Swal.fire('Error', 'Failed to load registration details', 'error');
            }
        }

        // Display registration details
        function displayRegistrationDetails(registration) {
            const details = document.getElementById('registrationDetails');
            
            details.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3"><i class="bi bi-person me-2"></i>Personal Information</h6>
                        <div class="mb-2"><strong>Full Name:</strong> ${registration.first_name} ${registration.middle_name || ''} ${registration.last_name} ${registration.suffix || ''}</div>
                        <div class="mb-2"><strong>Email:</strong> ${registration.email_address}</div>
                        <div class="mb-2"><strong>Mobile:</strong> ${registration.mobile_no}</div>
                        <div class="mb-2"><strong>Gender:</strong> ${registration.gender}</div>
                        <div class="mb-2"><strong>Civil Status:</strong> ${registration.civil_status}</div>
                        <div class="mb-2"><strong>Date of Birth:</strong> ${registration.date_of_birth}</div>
                        <div class="mb-2"><strong>Place of Birth:</strong> ${registration.place_of_birth}</div>
                        <div class="mb-2"><strong>Nationality:</strong> ${registration.nationality}</div>
                        <div class="mb-2"><strong>Religion:</strong> ${registration.religion}</div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3"><i class="bi bi-geo-alt me-2"></i>Address Information</h6>
                        <div class="mb-2"><strong>Complete Address:</strong> ${registration.complete_address}</div>
                        <div class="mb-2"><strong>Barangay:</strong> ${registration.barangay}</div>
                        <div class="mb-2"><strong>Municipality:</strong> ${registration.town}</div>
                        <div class="mb-2"><strong>Province:</strong> ${registration.province}</div>
                        <div class="mb-2"><strong>Region:</strong> ${registration.region}</div>
                        <div class="mb-2"><strong>Zip Code:</strong> ${registration.zip_code}</div>
                        
                        <h6 class="text-primary mb-3 mt-4"><i class="bi bi-book me-2"></i>Academic Information</h6>
                        <div class="mb-2"><strong>Desired Course:</strong> ${registration.desired_course}</div>
                        ${registration.tertiary_school ? `<div class="mb-2"><strong>Previous School:</strong> ${registration.tertiary_school}</div>` : ''}
                        ${registration.tertiary_year ? `<div class="mb-2"><strong>Year Graduated:</strong> ${registration.tertiary_year}</div>` : ''}
                        ${registration.academic_achievement ? `<div class="mb-2"><strong>Academic Achievement:</strong> ${registration.academic_achievement}</div>` : ''}
                    </div>
                </div>
                
                <hr class="my-4">
                
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3"><i class="bi bi-people me-2"></i>Family Information</h6>
                        ${registration.father_name ? `<div class="mb-2"><strong>Father:</strong> ${registration.father_name} (${registration.father_occupation || 'N/A'})</div>` : ''}
                        ${registration.mother_name ? `<div class="mb-2"><strong>Mother:</strong> ${registration.mother_name} (${registration.mother_occupation || 'N/A'})</div>` : ''}
                        ${registration.guardian_name ? `<div class="mb-2"><strong>Guardian:</strong> ${registration.guardian_name} (${registration.guardian_relationship || 'N/A'})</div>` : ''}
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3"><i class="bi bi-info-circle me-2"></i>Application Status</h6>
                        <div class="mb-2"><strong>Status:</strong> ${getStatusBadge(registration.status)}</div>
                        <div class="mb-2"><strong>Student ID:</strong> ${registration.student_id || 'Will be assigned upon approval'}</div>
                        <div class="mb-2"><strong>Application Date:</strong> ${new Date(registration.created_at).toLocaleString()}</div>
                        ${registration.updated_at && registration.updated_at !== registration.created_at ? `<div class="mb-2"><strong>Last Updated:</strong> ${new Date(registration.updated_at).toLocaleString()}</div>` : ''}
                    </div>
                </div>
            `;
        }

        // Update registration status
        async function updateRegistrationStatus(status) {
            if (!currentRegistrationId) return;
            
            const action = status === 'approved' ? 'approve' : 'reject';
            const confirmation = await Swal.fire({
                title: `${action.charAt(0).toUpperCase() + action.slice(1)} Registration?`,
                text: `Are you sure you want to ${action} this student registration?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: status === 'approved' ? '#28a745' : '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: `Yes, ${action} it!`
            });
            
            if (!confirmation.isConfirmed) return;
            
            try {
                const response = await fetch('../../action/evaluator/update_registration_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        registration_id: currentRegistrationId,
                        status: status
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: `Registration has been ${status}`,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    // Close modal and refresh data
                    const modal = bootstrap.Modal.getInstance(document.getElementById('registrationModal'));
                    modal.hide();
                    loadStatistics();
                    loadRegistrations();
                } else {
                    Swal.fire('Error', result.message || 'Failed to update registration status', 'error');
                }
            } catch (error) {
                console.error('Error updating status:', error);
                Swal.fire('Error', 'Failed to update registration status', 'error');
            }
        }

        // Refresh registrations
        function refreshRegistrations() {
            loadStatistics();
            loadRegistrations();
            updateTimestamp();
        }

        // Update timestamp
        function updateTimestamp() {
            document.getElementById('lastUpdated').textContent = new Date().toLocaleString();
        }

        // Logout function
        function confirmLogout() {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will be logged out of your evaluator account.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, logout'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    Swal.fire({
                        title: 'Logging out...',
                        text: 'Please wait',
                        icon: 'info',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    fetch('../../action/logout.php?format=json', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        credentials: 'same-origin'
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Logged Out',
                                text: `${data.user_name}, you have been successfully logged out.`,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = data.redirect_url;
                            });
                        } else {
                            Swal.fire('Logout Error', data.message || 'Failed to logout. Redirecting to login page...', 'error').then(() => {
                                window.location.href = data.redirect_url || '../../login.php';
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Logout error:', error);
                        Swal.fire({
                            title: 'Logout Error', 
                            text: 'Failed to logout. Redirecting to login page...', 
                            icon: 'error',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = '../evaluator/login.php';
                        });
                    });
                }
            });
        }
    </script>
</body>
</html>
