<?php
// Start session and require authentication
session_start();
require_once '../../includes/registrar_auth.php';
requireRegistrarAuth();

// Get current registrar info
$registrar = getCurrentRegistrar();

// Get student ID from URL parameter
$studentId = isset($_GET['student_id']) ? $_GET['student_id'] : '';

// Redirect if no student ID is provided
if (empty($studentId)) {
    header('Location: enrollment.php');
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
                                <h2 class="fw-bold mb-1">Enrollment Successful</h2>
                                <p class="text-muted mb-0">Student has been officially enrolled for the current semester</p>
                            </div>
                            <div>
                                <a href="enrollment.php" class="btn btn-outline-primary">
                                    <i class="bi bi-arrow-left me-1"></i> Back to List
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        <div class="row mb-4">
            <div class="col-md-8 mx-auto">
                <div class="card dashboard-card">
                    <div class="card-body text-center py-5">
                        <div class="d-flex justify-content-center">
                            <div class="success-icon bg-success rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 100px; height: 100px;">
                                <i class="bi bi-check-lg text-white" style="font-size: 3rem;"></i>
                            </div>
                        </div>
                        <h3 class="mb-3">Student Successfully Enrolled!</h3>
                        <p class="mb-2"><strong><?php echo $student['first_name'] . ' ' . $student['last_name']; ?> (<?php echo $student['id']; ?>)</strong> has been officially enrolled for the current semester.</p>
                        <div class="mt-4 mb-3">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card dashboard-card">
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="bi bi-check-circle text-white" style="font-size: 3rem;"></i>
                            </div>
                        </div>
                        
                        <h2 class="text-success fw-bold mb-3">Enrollment Successful!</h2>
                        <p class="lead mb-4">The student has been successfully enrolled.</p>
                        
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <p class="mb-1"><strong>Student ID:</strong> <span id="enrolledStudentId"><?php echo htmlspecialchars($studentId); ?></span></p>
                                        <p class="mb-1"><strong>Enrollment Date:</strong> <?php echo date('F j, Y'); ?></p>
                                        <p class="mb-0"><strong>Status:</strong> <span class="badge bg-success">Enrolled</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <div class="d-flex justify-content-center gap-3">
                                <a href="student_detail.php?id=<?php echo $studentId; ?>" class="btn btn-primary">
                                    <i class="bi bi-eye me-1"></i> View Student Details
                                </a>
                                <a href="enrollment.php" class="btn btn-success">
                                    <i class="bi bi-list-check me-1"></i> Return to Enrollment List
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>
