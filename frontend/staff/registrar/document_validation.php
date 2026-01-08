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

// Initialize student and documents data
$student = null;
$documents = [];
$error = null;

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
            } else {
                $error = isset($student_data['error']) ? $student_data['error'] : 'Failed to load student data';
            }
        } else {
            $error = 'Failed to connect to backend service';
        }
        
        // Fetch documents data if student exists
        if ($student) {
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
                    $documents = $docs_data['documents'];
                }
            }
        }
    } catch (Exception $e) {
        $error = 'An error occurred while loading data';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include '../../includes/header.php'; ?>
<body class="registrar-dashboard">
    <?php include '../../includes/navbar.php'; ?>
    
    <div class="container py-3">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="fw-bold mb-1">Document Validation</h2>
                                <p class="text-muted mb-0">Verify and update the status of student documents</p>
                            </div>
                            <div>
                        
                                <button class="btn btn-primary" onclick="window.location.reload()">
                                    <i class="bi bi-arrow-clockwise me-1"></i> Refresh
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if (empty($studentId)): ?>
        <!-- Student Search Form (shown when no student ID is provided) -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Search Student</h5>
                    </div>
                    <div class="card-body">
                        <form action="document_validation.php" method="GET">
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <input type="text" class="form-control" name="id" placeholder="Enter Student ID" required>
                                </div>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" name="name" placeholder="Enter Student Name (optional)">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-search me-1"></i>Find
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php elseif ($error): ?>
        <!-- Error Display -->
        <div class="row">
            <div class="col-12">
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
                <div class="text-center">
                    <a href="document_validation.php" class="btn btn-primary">
                        <i class="bi bi-arrow-left me-1"></i> Back to Search
                    </a>
                </div>
            </div>
        </div>
        <?php elseif (!$student): ?>
        <!-- No Data Display -->
        <div class="row">
            <div class="col-12">
                <div class="alert alert-warning">
                    <i class="bi bi-info-circle me-2"></i>
                    Student not found or no data available.
                </div>
                <div class="text-center">
                    <a href="document_validation.php" class="btn btn-primary">
                        <i class="bi bi-arrow-left me-1"></i> Back to Search
                    </a>
                </div>
            </div>
        </div>
        <?php else: ?>
        <!-- Document Validation Form (shown when student data is loaded) -->
        <div class="row g-4">
            <div class="col-md-4">
                <!-- Student Information Card -->
                <div class="card dashboard-card h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Student Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="bi bi-person text-white" style="font-size: 1.5rem;"></i>
                            </div>
                            <h5 class="mt-2 mb-1"><?php echo htmlspecialchars($student['full_name']); ?></h5>
                            <p class="text-muted small"><?php echo htmlspecialchars($student['id']); ?></p>
                        </div>
                        
                        <div class="mb-2">
                            <strong>Type:</strong><br>
                            <span class="badge bg-info">
                                <?php echo htmlspecialchars($student['type']); ?>
                            </span>
                        </div>
                        
                        <div class="mb-2">
                            <strong>Course:</strong><br>
                            <span class="text-muted"><?php echo htmlspecialchars($student['course']); ?></span>
                        </div>
                        
                        <div class="mb-2">
                            <strong>Email:</strong><br>
                            <span class="text-muted"><?php echo htmlspecialchars($student['email']); ?></span>
                        </div>
                        
                        <div class="mb-2">
                            <strong>Status:</strong><br>
                            <span class="badge bg-<?php echo $student['status'] == 'approved' ? 'success' : ($student['status'] == 'pending' ? 'warning' : 'danger'); ?>">
                                <?php echo ucfirst($student['status']); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <!-- Document Checklist Card -->
                <div class="card dashboard-card h-100">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Document Checklist</h5>
                        <div>
                            <button class="btn btn-success btn-sm me-1" onclick="approveAllDocuments()">
                                <i class="bi bi-check-all me-1"></i> Approve All
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($documents)): ?>
                        <div class="text-center py-4">
                            <i class="bi bi-file-earmark text-muted" style="font-size: 2rem;"></i>
                            <p class="mt-2 text-muted">No document requirements found for this student type.</p>
                        </div>
                        <?php else: ?>
                        
                        <!-- Document Progress Bar -->
                        <?php 
                        $totalDocs = count($documents);
                        $completedDocs = count(array_filter($documents, function($doc) { 
                            return $doc['status'] === 'Submitted'; 
                        }));
                        $completionPercent = $totalDocs > 0 ? round(($completedDocs / $totalDocs) * 100) : 0;
                        ?>
                        <div class="mb-4">
                            <h6 class="mb-2">Document Checklist</h6>
                            <div class="progress">
                                <div class="progress-bar bg-<?php echo $completionPercent == 100 ? 'success' : 'warning'; ?>" 
                                     role="progressbar" 
                                     style="width: <?php echo $completionPercent; ?>%"
                                     aria-valuenow="<?php echo $completedDocs; ?>" 
                                     aria-valuemin="0" 
                                     aria-valuemax="<?php echo $totalDocs; ?>">
                                    <?php echo $completedDocs; ?>/<?php echo $totalDocs; ?>
                                </div>
                            </div>
                        </div>
                        
                        <?php foreach ($documents as $index => $doc): ?>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="doc<?php echo $index; ?>" 
                                   data-document-name="<?php echo htmlspecialchars($doc['name']); ?>"
                                   <?php echo $doc['status'] == 'Submitted' || $doc['status'] == 'Approved' ? 'checked' : ''; ?>>
                            <label class="form-check-label d-flex justify-content-between align-items-center w-100" for="doc<?php echo $index; ?>">
                                <span>
                                    <i class="bi bi-file-earmark-text me-2"></i>
                                    <?php echo htmlspecialchars($doc['name']); ?>
                                    <?php if ($doc['required']): ?>
                                    <span class="text-danger">*</span>
                                    <?php endif; ?>
                                </span>
                                <span class="badge bg-<?php echo $doc['status'] == 'Submitted' || $doc['status'] == 'Approved' ? 'success' : ($doc['status'] == 'Under Review' ? 'warning' : 'danger'); ?>" 
                                      id="status-badge-<?php echo $index; ?>">
                                    <?php echo $doc['status']; ?>
                                </span>
                            </label>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <!-- Notes/Remarks Section -->
                        <div class="mt-4">
                            <h6>Notes/Remarks</h6>
                            <textarea class="form-control" rows="3" placeholder="Add any notes or remarks about the documents..."></textarea>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="mt-4 d-flex gap-2">
                            <button class="btn btn-success flex-fill" onclick="markAsComplete()">
                                <i class="bi bi-check-circle me-1"></i> Mark Complete
                            </button>
                            <button class="btn btn-warning flex-fill" onclick="markAsIncomplete()">
                                <i class="bi bi-exclamation-circle me-1"></i> Mark Incomplete
                            </button>
                            <button class="btn btn-secondary flex-fill" onclick="goBackToStudentList()">
                                <i class="bi bi-arrow-left me-1"></i> Back to Details
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Include Bootstrap JS -->
    <script>
        // Function to update document status in database
        function updateDocumentStatus(documentName, status) {
            const studentId = '<?php echo $studentId; ?>';
            
            return fetch('/enrollmentsystem/action/registrar/document_validation_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    student_id: studentId,
                    document_name: documentName,
                    status: status
                })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    throw new Error(data.error || 'Failed to update document status');
                }
                return data;
            });
        }
        
        // Function to update progress bar
        function updateProgressBar() {
            const checkboxes = document.querySelectorAll('.form-check-input');
            const checkedBoxes = document.querySelectorAll('.form-check-input:checked');
            const total = checkboxes.length;
            const completed = checkedBoxes.length;
            const percentage = total > 0 ? Math.round((completed / total) * 100) : 0;
            
            const progressBar = document.querySelector('.progress-bar');
            if (progressBar) {
                progressBar.style.width = percentage + '%';
                progressBar.textContent = completed + '/' + total;
                progressBar.className = 'progress-bar bg-' + (percentage === 100 ? 'success' : 'warning');
            }
        }

        // Function to mark documents as complete
        function markAsComplete() {
            const checkboxes = document.querySelectorAll('.form-check-input');
            const selectedBoxes = document.querySelectorAll('.form-check-input:checked');
            
            if (selectedBoxes.length === 0) {
                Swal.fire('Warning', 'Please select at least one document to mark as complete!', 'warning');
                return;
            }
            
            Swal.fire({
                title: 'Confirm Action',
                text: `Mark ${selectedBoxes.length} selected documents as complete?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, mark as complete'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Updating documents...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Update all selected documents
                    const promises = Array.from(selectedBoxes).map(checkbox => {
                        const documentName = checkbox.getAttribute('data-document-name');
                        return updateDocumentStatus(documentName, 'Submitted');
                    });
                    
                    Promise.all(promises)
                        .then(() => {
                            // Update badges for selected documents
                            selectedBoxes.forEach((checkbox, index) => {
                                const badgeId = 'status-badge-' + checkbox.id.replace('doc', '');
                                const badge = document.getElementById(badgeId);
                                if (badge) {
                                    badge.textContent = 'Submitted';
                                    badge.className = 'badge bg-success';
                                }
                            });
                            
                            updateProgressBar();
                            
                            Swal.fire({
                                title: 'Success!',
                                text: `${selectedBoxes.length} documents marked as complete successfully!`,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        })
                        .catch(error => {
                            Swal.fire('Error', error.message || 'Failed to update documents', 'error');
                        });
                }
            });
        }

        // Function to mark documents as incomplete
        function markAsIncomplete() {
            const checkboxes = document.querySelectorAll('.form-check-input');
            const selectedBoxes = document.querySelectorAll('.form-check-input:checked');
            
            if (selectedBoxes.length === 0) {
                Swal.fire('Warning', 'Please select at least one document to mark as incomplete!', 'warning');
                return;
            }
            
            Swal.fire({
                title: 'Confirm Action',
                text: `Mark ${selectedBoxes.length} selected documents as incomplete?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, mark as incomplete'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Updating documents...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Update all selected documents
                    const promises = Array.from(selectedBoxes).map(checkbox => {
                        const documentName = checkbox.getAttribute('data-document-name');
                        return updateDocumentStatus(documentName, 'Missing');
                    });
                    
                    Promise.all(promises)
                        .then(() => {
                            // Update badges and uncheck selected documents
                            selectedBoxes.forEach((checkbox, index) => {
                                const badgeId = 'status-badge-' + checkbox.id.replace('doc', '');
                                const badge = document.getElementById(badgeId);
                                if (badge) {
                                    badge.textContent = 'Missing';
                                    badge.className = 'badge bg-danger';
                                }
                                checkbox.checked = false; // Uncheck the box
                            });
                            
                            updateProgressBar();
                            
                            Swal.fire({
                                title: 'Updated!',
                                text: `${selectedBoxes.length} documents marked as incomplete!`,
                                icon: 'info',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        })
                        .catch(error => {
                            Swal.fire('Error', error.message || 'Failed to update documents', 'error');
                        });
                }
            });
        }

        // Function to approve all documents
        function approveAllDocuments() {
            const allCheckboxes = document.querySelectorAll('.form-check-input');
            
            Swal.fire({
                title: 'Approve All Documents',
                text: `Mark all ${allCheckboxes.length} documents as complete?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, approve all'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Updating all documents...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Update all documents
                    const promises = Array.from(allCheckboxes).map(checkbox => {
                        const documentName = checkbox.getAttribute('data-document-name');
                        return updateDocumentStatus(documentName, 'Submitted');
                    });
                    
                    Promise.all(promises)
                        .then(() => {
                            // Update all badges and check all boxes
                            allCheckboxes.forEach((checkbox, index) => {
                                const badgeId = 'status-badge-' + checkbox.id.replace('doc', '');
                                const badge = document.getElementById(badgeId);
                                if (badge) {
                                    badge.textContent = 'Submitted';
                                    badge.className = 'badge bg-success';
                                }
                                checkbox.checked = true;
                            });
                            
                            updateProgressBar();
                            
                            Swal.fire({
                                title: 'Success!',
                                text: 'All documents approved successfully!',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        })
                        .catch(error => {
                            Swal.fire('Error', error.message || 'Failed to approve documents', 'error');
                        });
                }
            });
        }

        // Function to proceed to enrollment
        function proceedToEnrollment() {
            <?php if (!empty($studentId)): ?>
            window.location.href = 'enrollment.php?id=<?php echo $studentId; ?>';
            <?php else: ?>
            Swal.fire('Error', 'No student selected for enrollment.', 'error');
            <?php endif; ?>
        }

        // Function to go back to student list
        function goBackToStudentList() {
            window.location.href = 'student_list.php';
        }
    </script>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>
