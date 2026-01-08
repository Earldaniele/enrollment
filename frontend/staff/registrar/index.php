<?php
// Start session and require authentication
session_start();
require_once '../../includes/registrar_auth.php';

// Check if registrar is logged in
requireRegistrarAuth();

// Get current registrar info
$registrar = getCurrentRegistrar();
?>
<!DOCTYPE html>
<html lang="en">
<?php include '../../includes/header.php'; ?>
<body class="registrar-dashboard">
    <?php include '../../includes/navbar.php'; ?>
    
    <div class="container py-5">
        <!-- Welcome Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card dashboard-card welcome-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="fw-bold mb-1">Welcome, <?php echo htmlspecialchars($registrar['name']); ?></h2>
                                <p class="mb-0">NCST Enrollment System - Registrar Dashboard</p>
                            </div>
                            <div>
                                <i class="bi bi-person-badge text-white" style="font-size: 3rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Type Statistics -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Student Types Overview</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <i class="bi bi-person-plus text-primary" style="font-size: 2rem;"></i>
                                    <h3 class="mt-2 mb-1" id="newStudentsCount">0</h3>
                                    <p class="text-muted mb-0">New Students</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <i class="bi bi-person-check text-success" style="font-size: 2rem;"></i>
                                    <h3 class="mt-2 mb-1" id="oldStudentsCount">0</h3>
                                    <p class="text-muted mb-0">Old Students</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <i class="bi bi-arrow-left-right text-warning" style="font-size: 2rem;"></i>
                                    <h3 class="mt-2 mb-1" id="shiftingStudentsCount">0</h3>
                                    <p class="text-muted mb-0">Shifting Students</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <i class="bi bi-box-arrow-in-right text-info" style="font-size: 2rem;"></i>
                                    <h3 class="mt-2 mb-1" id="transfereeStudentsCount">0</h3>
                                    <p class="text-muted mb-0">Transferees</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Document Status Overview -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Document Status Overview</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <i class="bi bi-file-earmark-check text-success" style="font-size: 2rem;"></i>
                                    <h3 class="mt-2 mb-1" id="completeDocsCount">0</h3>
                                    <p class="text-muted mb-0">Complete Documents</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <i class="bi bi-file-earmark-x text-warning" style="font-size: 2rem;"></i>
                                    <h3 class="mt-2 mb-1" id="incompleteDocsCount">0</h3>
                                    <p class="text-muted mb-0">Incomplete Documents</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <i class="bi bi-hourglass-split text-info" style="font-size: 2rem;"></i>
                                    <h3 class="mt-2 mb-1" id="pendingValidationCount">0</h3>
                                    <p class="text-muted mb-0">Pending Validation</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <i class="bi bi-x-circle text-danger" style="font-size: 2rem;"></i>
                                    <h3 class="mt-2 mb-1" id="rejectedDocsCount">0</h3>
                                    <p class="text-muted mb-0">Rejected Applications</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <a href="student_list.php?type=new" class="btn btn-primary w-100">
                                    <i class="bi bi-list-ul me-2"></i>View New Students
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="student_list.php?type=old" class="btn btn-primary w-100">
                                    <i class="bi bi-list-ul me-2"></i>View Old Students
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="student_list.php?type=shifting" class="btn btn-primary w-100">
                                    <i class="bi bi-list-ul me-2"></i>View Shifting Students
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="student_list.php?type=transferee" class="btn btn-primary w-100">
                                    <i class="bi bi-list-ul me-2"></i>View Transferees
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="document_validation.php" class="btn btn-primary w-100" style="background-color: #28a745; border-color: #28a745;">
                                    <i class="bi bi-check-circle me-2"></i>Document Validation
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="enrollment.php" class="btn btn-primary w-100" style="background-color: #6f42c1; border-color: #6f42c1;">
                                    <i class="bi bi-check2-circle me-2"></i>Student Enrollment
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        

        <!-- Search Section -->
        <div class="row">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Search Student</h5>
                    </div>
                    <div class="card-body">
                        <form action="student_search.php" method="GET">
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <input type="text" class="form-control" name="student_id" placeholder="Student ID">
                                </div>
                                
                                <div class="col-md-5">
                                    <input type="text" class="form-control" name="student_name" placeholder="Student Name">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-search me-2"></i>Search
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="../../../assets/js/bootstrap.min.js"></script>
    
    <!-- Include Registrar JavaScript -->
    <script src="../../../action/registrar/registrar.js"></script>
    
    <!-- Include Bootstrap JS and SweetAlert2 -->
    <script>
        // Demo function to refresh recent students
        function refreshRecentStudents() {
            // In a real implementation, this would fetch data from the server
            Swal.fire({
                title: 'Refreshing...',
                text: 'Refreshing recent students list...',
                icon: 'info',
                timer: 1500,
                showConfirmButton: false
            });
        }
    </script>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>
