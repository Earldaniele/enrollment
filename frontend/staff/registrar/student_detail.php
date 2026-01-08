<?php
// Start session and require authentication
session_start();
require_once '../../includes/registrar_auth.php';

// Check if registrar is logged in
requireRegistrarAuth();

// Get current registrar info
$registrar = getCurrentRegistrar();

// Get student ID from URL parameter
$studentId = isset($_GET['id']) ? $_GET['id'] : '';

// Initialize student data
$student = null;
$error = null;
$assessments = [];
$totalAssessment = 0;
$enrolledSubjects = [];
$totalUnits = 0;
$enrollmentInfo = null;

// Fetch student data if ID is provided
if (!empty($studentId)) {
    try {
        // Use cURL to make proper HTTP requests
        $base_url = 'http://localhost/enrollmentsystem/action/registrar/';
        
        // Fetch student data
        $student_url = $base_url . "student_detail_handler.php?student_id=" . urlencode($studentId);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $student_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $student_response = curl_exec($ch);
        $student_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($student_response !== false && $student_http_code == 200) {
            $student_data = json_decode($student_response, true);
            if ($student_data && isset($student_data['success']) && $student_data['success']) {
                $student = $student_data['student'];
                $assessments = isset($student_data['assessments']) ? $student_data['assessments'] : [];
                $totalAssessment = isset($student_data['total_assessment']) ? $student_data['total_assessment'] : 0;
                $enrolledSubjects = isset($student_data['enrolled_subjects']) ? $student_data['enrolled_subjects'] : [];
                $totalUnits = isset($student_data['total_units']) ? $student_data['total_units'] : 0;
                $enrollmentInfo = isset($student_data['enrollment']) ? $student_data['enrollment'] : null;
            } else {
                $error = isset($student_data['error']) ? $student_data['error'] : 'Failed to load student data';
            }
        } else {
            $error = 'Failed to connect to backend service';
        }
    } catch (Exception $e) {
        $error = 'An error occurred while loading student data';
    }
}

// Initialize documents data
$requiredDocuments = [];
$completedCount = 0;
$totalDocuments = 0;
$completionPercentage = 0;

// Load documents data if student exists
if ($student) {
    try {
        $base_url = 'http://localhost/enrollmentsystem/action/registrar/';
        $docs_url = $base_url . "document_validation_handler.php?student_id=" . urlencode($studentId);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $docs_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $docs_response = curl_exec($ch);
        $docs_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($docs_response !== false && $docs_http_code == 200) {
            $docs_data = json_decode($docs_response, true);
            if ($docs_data && isset($docs_data['success']) && $docs_data['success']) {
                $requiredDocuments = $docs_data['documents'];
                $completedCount = $docs_data['summary']['completed'];
                $totalDocuments = count($requiredDocuments);
                $completionPercentage = $totalDocuments > 0 ? round(($completedCount / $totalDocuments) * 100) : 0;
            }
        }
    } catch (Exception $e) {
        // Continue with empty documents if there's an error
    }
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
                                <h2 class="fw-bold mb-1">Student Details</h2>
                                <p class="text-muted mb-0">View and manage student information and documents</p>
                            </div>
                            <div>
                                <a href="student_list.php" class="btn btn-outline-primary">
                                    <i class="bi bi-arrow-left me-1"></i> Back to List
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Information -->
        <?php if ($error): ?>
        <div class="row">
            <div class="col-12">
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
                <div class="text-center">
                    <a href="student_list.php" class="btn btn-primary">
                        <i class="bi bi-arrow-left me-1"></i> Back to Student List
                    </a>
                </div>
            </div>
        </div>
        <?php elseif (!$student): ?>
        <div class="row">
            <div class="col-12">
                <div class="alert alert-warning">
                    <i class="bi bi-info-circle me-2"></i>
                    Please provide a student ID to view details.
                </div>
                <div class="text-center">
                    <a href="student_list.php" class="btn btn-primary">
                        <i class="bi bi-arrow-left me-1"></i> Back to Student List
                    </a>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card dashboard-card h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Personal Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="bi bi-person text-white" style="font-size: 2rem;"></i>
                            </div>
                            <h4 class="mt-2 mb-1"><?php echo htmlspecialchars($student['full_name']); ?></h4>
                            <p class="text-muted"><?php echo htmlspecialchars($student['id']); ?></p>
                        </div>
                        
                        <hr>
                        
                        <div class="mb-2">
                            <strong>Email:</strong><br>
                            <span class="text-muted"><?php echo htmlspecialchars($student['email'] ?? 'Not provided'); ?></span>
                        </div>
                        
                        <div class="mb-2">
                            <strong>Contact:</strong><br>
                            <span class="text-muted"><?php echo htmlspecialchars($student['phone_number'] ?? 'Not provided'); ?></span>
                        </div>
                        
                        <div class="mb-2">
                            <strong>Address:</strong><br>
                            <span class="text-muted"><?php echo htmlspecialchars($student['address'] ?? 'Not provided'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card dashboard-card h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Academic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Student Type:</strong><br>
                            <span class="text-muted"><?php echo htmlspecialchars($student['type'] ?? 'Not specified'); ?></span>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Course:</strong><br>
                            <span class="text-muted"><?php echo htmlspecialchars($student['course']); ?></span>
                        </div>
                        
                        <?php if ($enrollmentInfo): ?>
                        <div class="mb-3">
                            <strong>Section:</strong><br>
                            <span class="text-muted"><?php echo htmlspecialchars($enrollmentInfo['section'] ?? $enrollmentInfo['section_code'] ?? 'Not assigned'); ?></span>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Year Level:</strong><br>
                            <span class="text-muted"><?php echo htmlspecialchars($enrollmentInfo['year_level'] ?? 'Not specified'); ?></span>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Semester:</strong><br>
                            <span class="text-muted"><?php echo htmlspecialchars($enrollmentInfo['semester'] ?? 'Not specified'); ?></span>
                        </div>
                        
                        <div class="mb-3">
                            <strong>School Year:</strong><br>
                            <span class="text-muted"><?php echo htmlspecialchars($enrollmentInfo['school_year'] ?? 'Not specified'); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($student['type'] == 'transferee' && !empty($student['previous_school'])): ?>
                        <div class="mb-3">
                            <strong>Previous School:</strong><br>
                            <span class="text-muted"><?php echo htmlspecialchars($student['previous_school']); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <strong>Registration Status:</strong><br>
                            <span class="text-muted"><?php echo htmlspecialchars(ucfirst($student['status'])); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card dashboard-card h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Document Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="progress mb-3" style="height: 20px;">
                                <div class="progress-bar bg-<?php echo $completionPercentage == 100 ? 'success' : 'warning'; ?>" 
                                     role="progressbar" style="width: <?php echo $completionPercentage; ?>%">
                                    <?php echo $completionPercentage; ?>%
                                </div>
                            </div>
                            <h6>Document Completion: <?php echo $completedCount; ?>/<?php echo $totalDocuments; ?></h6>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Status:</strong><br>
                            <span class="text-muted"><?php echo $completionPercentage == 100 ? 'Complete' : 'Incomplete'; ?></span>
                        </div>
                        
                        <?php if (!empty($student['date_approved'])): ?>
                        <div class="mb-3">
                            <strong>Date Approved:</strong><br>
                            <span class="text-muted"><?php echo htmlspecialchars($student['date_approved']); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Required Documents List -->
                        <hr>
                        <strong class="d-block mb-2">Required Documents:</strong>
                        <div class="small">
                            <ul class="list-group list-group-flush">
                                <?php foreach ($requiredDocuments as $doc): ?>
                                <li class="list-group-item px-0 py-1 d-flex justify-content-between align-items-center">
                                    <span>
                                        <i class="bi bi-file-earmark-text me-1 text-primary"></i>
                                        <?php echo htmlspecialchars($doc['name']); ?>
                                    </span>
                                    <?php 
                                    $badgeClass = $doc['status'] == 'Submitted' ? 'success' : ($doc['status'] == 'Pending' ? 'warning' : 'danger');
                                    ?>
                                    <span class="badge bg-<?php echo $badgeClass; ?>">
                                        <?php echo $doc['status']; ?>
                                    </span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enrollment Information -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Enrollment Information</h5>
                    </div>
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Registration Form</h6>
                        <div class="table-responsive mb-4">
                            <!-- Combined Registration Form Table -->
                            <table class="table table-bordered table-sm mb-4">
                                <!-- Student and Enrollment Information - Two Rows Only -->
                                <tr>
                                    <td class="text-center"><strong>Student No.</strong><br><?php echo htmlspecialchars($student['id']); ?></td>
                                    <td class="text-center"><strong>Last Name</strong><br><?php echo htmlspecialchars($student['last_name']); ?></td>
                                    <td class="text-center"><strong>First Name</strong><br><?php echo htmlspecialchars($student['first_name']); ?></td>
                                    <td class="text-center"><strong>Middle Name</strong><br><?php echo htmlspecialchars($student['middle_name'] ?? 'N/A'); ?></td>
                                    <td class="text-center"><strong>Course Code</strong><br><?php echo htmlspecialchars($student['course']); ?></td>
                                    <td class="text-center"><strong>Type</strong><br><?php echo ucfirst($student['type']); ?></td>
                                    <td class="text-center"><strong>Status</strong><br><?php echo ucfirst($student['status']); ?></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-center"><strong>Address</strong><br><?php echo htmlspecialchars($student['address'] ?? 'Not provided'); ?></td>
                                    <td class="text-center"><strong>Contact No.</strong><br><?php echo htmlspecialchars($student['phone_number'] ?? 'Not provided'); ?></td>
                                    <td class="text-center"><strong>Gender</strong><br>Male</td>
                                    <td class="text-center"><strong>Semester</strong><br>1st Semester</td>
                                    <td colspan="2" class="text-center"><strong>SY</strong><br>2025-2026</td>
                                </tr>
                                
                                <!-- Class Schedule Section -->
                                <tr class="bg-light">
                                    <th>Code</th>
                                    <th>Description</th>
                                    <th>Units</th>
                                    <th>Type</th>
                                    <th>Days</th>
                                    <th>Time</th>
                                    <th>Room</th>
                                </tr>
                                <?php foreach ($enrolledSubjects as $subject): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($subject['subject_code']); ?></td>
                                    <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                                    <td><?php echo $subject['units']; ?></td>
                                    <td><?php echo htmlspecialchars($subject['type'] ?? 'Lecture'); ?></td>
                                    <td><?php echo htmlspecialchars($subject['days'] ?? 'TBA'); ?></td>
                                    <td><?php echo htmlspecialchars($subject['time_display'] ?? 'TBA'); ?></td>
                                    <td><?php echo htmlspecialchars($subject['room'] ?? 'TBA'); ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <tr class="fw-bold">
                                    <td colspan="2" class="text-end">Total Units:</td>
                                    <td><?php echo $totalUnits; ?></td>
                                    <td colspan="4"></td>
                                </tr>
                            </table>

                            <!-- Assessment of Fees Table -->
                            <h6 class="fw-bold mb-2">Assessment of Fees</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <table class="table table-bordered table-sm">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Fee</th>
                                                <th class="text-end">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Cash Payment Items -->
                                            <tr class="table-secondary">
                                                <th colspan="2">Cash Payment Items</th>
                                            </tr>
                                            <?php 
                                            $cashTotal = 0;
                                            foreach ($assessments as $assessment): 
                                                $cashTotal += $assessment['amount'];
                                            ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($assessment['fee_type']); ?></td>
                                                <td class="text-end">₱<?php echo number_format($assessment['amount'], 2); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <tr class="fw-bold">
                                                <td>Cash Total:</td>
                                                <td class="text-end">₱<?php echo number_format($cashTotal, 2); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card dashboard-card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <a href="document_validation.php?id=<?php echo $student['id']; ?>" class="btn btn-success w-100">
                                    <i class="bi bi-check-circle me-2"></i>Validate Documents
                                </a>
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                    <i class="bi bi-x-circle me-2"></i>Reject Application
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS -->
    <script>
        // Function to handle student rejection
        function rejectStudent() {
            // Get the rejection reason from the form
            const rejectionReason = document.getElementById('rejectionReason').value;
            
            if (!rejectionReason.trim()) {
                // Show error if reason is empty
                Swal.fire({
                    title: 'Error',
                    text: 'Please provide a reason for rejection',
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                });
                return;
            }
            
            Swal.fire({
                title: 'Confirm Rejection',
                text: 'Are you sure you want to reject this student?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, reject student'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Here you would normally save the rejection reason to the database
                    // For now, we'll just show a success message
                    Swal.fire({
                        title: 'Rejected!',
                        text: 'Student rejected successfully!',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = 'student_list.php';
                    });
                }
            });
        }
    </script>
    
    <!-- Rejection Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="rejectModalLabel">Reject Student Application</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="rejectionForm">
                        <div class="mb-3">
                            <label for="rejectionReason" class="form-label">Reason for Rejection:</label>
                            <textarea class="form-control" id="rejectionReason" rows="4" placeholder="Please provide a detailed reason for rejecting this application" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="rejectionCategory" class="form-label">Rejection Category:</label>
                            <select class="form-select" id="rejectionCategory">
                                <option value="" selected disabled>Select a category</option>
                                <option value="quota">Class quota reached</option>
                                <option value="requirements">Incomplete requirements</option>
                                <option value="eligibility">Not eligible for the program</option>
                                <option value="grades">Grades do not meet minimum requirements</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="rejectStudent()">Reject Application</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>
