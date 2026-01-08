<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../includes/auth.php';
require_once '../includes/db_config.php';

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header('Location: ../pages/login.php');
    exit;
}

$user_email = $_SESSION['email'];

// Get student account information
$stmt = $conn->prepare("SELECT id, email, first_name, last_name FROM student_accounts WHERE email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$student_result = $stmt->get_result();

if ($student_result->num_rows === 0) {
    header('Location: ../pages/login.php');
    exit;
}

$student = $student_result->fetch_assoc();

// Get student registration information
$stmt = $conn->prepare("
    SELECT sr.*, sa.first_name, sa.last_name 
    FROM student_registrations sr 
    JOIN student_accounts sa ON sa.email = sr.email_address 
    WHERE sr.email_address = ? 
    ORDER BY sr.created_at DESC 
    LIMIT 1
");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$registration_result = $stmt->get_result();

$has_registration = $registration_result->num_rows > 0;
$registration = $has_registration ? $registration_result->fetch_assoc() : null;

// Generate student number if registration exists
$student_number = null;
if ($has_registration) {
    $year = date('Y', strtotime($registration['created_at']));
    $student_number = str_pad($registration['student_id'], 5, '0', STR_PAD_LEFT);
}

// Get real enrolled subjects data if student is approved
$subjects = [];
$total_units = 0;
if ($has_registration && $registration['status'] === 'approved') {
    $subjects_stmt = $conn->prepare("
        SELECT 
            s.subject_code as code,
            s.subject_name as description,
            s.units,
            s.schedule
        FROM enrolled_subjects es
        JOIN subjects s ON es.subject_id = s.id
        WHERE es.student_id = ? AND es.status = 'enrolled'
        ORDER BY s.subject_code
    ");
    $subjects_stmt->bind_param("s", $registration['student_id']);
    $subjects_stmt->execute();
    $subjects_result = $subjects_stmt->get_result();
    
    while ($subject = $subjects_result->fetch_assoc()) {
        $subjects[] = $subject;
    }
    
    $total_units = array_sum(array_column($subjects, 'units'));
}

// Get real assessment fees data if student is approved
$fees = [];
$total_fees = 0;
if ($has_registration && $registration['status'] === 'approved') {
    $fees_stmt = $conn->prepare("
        SELECT 
            fee_type as type,
            amount
        FROM student_assessments
        WHERE student_id = ?
        ORDER BY fee_type
    ");
    $fees_stmt->bind_param("s", $registration['student_id']);
    $fees_stmt->execute();
    $fees_result = $fees_stmt->get_result();
    
    while ($fee = $fees_result->fetch_assoc()) {
        $fees[] = $fee;
    }
    
    $total_fees = array_sum(array_column($fees, 'amount'));
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include '../includes/header.php'; ?>
<body class="student-dashboard">
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                <!-- Page Header -->
                <div class="d-flex align-items-center mb-4">
                    <i class="bi bi-file-earmark-text text-primary me-3" style="font-size: 2rem;"></i>
                    <h2 class="fw-bold mb-0" style="color: rgb(37, 52, 117);">View Enrollment</h2>
                </div>

                <?php if (!$has_registration): ?>
                <!-- No Registration Found -->
                <div class="alert alert-info text-center py-5">
                    <i class="bi bi-info-circle display-1 text-info mb-3"></i>
                    <h4>No Registration Found</h4>
                    <p class="mb-3">You haven't submitted a registration application yet.</p>
                    <a href="college-registration.php" class="btn btn-primary">
                        <i class="bi bi-file-earmark-plus me-2"></i>Start Registration
                    </a>
                </div>
                <?php else: ?>
                
                <!-- Registration Status Alert -->
                <div class="alert alert-<?php echo $registration['status'] === 'approved' ? 'success' : ($registration['status'] === 'rejected' ? 'danger' : 'warning'); ?> mb-4">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-<?php echo $registration['status'] === 'approved' ? 'check-circle' : ($registration['status'] === 'rejected' ? 'x-circle' : 'clock-history'); ?> me-3" style="font-size: 1.5rem;"></i>
                        <div>
                            <h5 class="mb-1">Registration Status: <?php echo ucfirst($registration['status']); ?></h5>
                            <p class="mb-0">
                                <?php if ($registration['status'] === 'approved'): ?>
                                    Your registration has been approved.
                                <?php elseif ($registration['status'] === 'rejected'): ?>
                                    Your registration has been rejected. Please contact the registrar for more information.
                                <?php else: ?>
                                    Your registration is currently being reviewed by the evaluation team.
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Student Information Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header" style="background-color: rgb(37, 52, 117); color: white;">
                        <h5 class="mb-0"><i class="bi bi-person me-2"></i>Student Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <strong>Full Name:</strong>
                                    <span class="ms-2"><?php echo htmlspecialchars($registration['first_name'] . ' ' . ($registration['middle_name'] ? $registration['middle_name'] . ' ' : '') . $registration['last_name'] . ($registration['suffix'] ? ' ' . $registration['suffix'] : '')); ?></span>
                                </div>
                                <div class="mb-3">
                                    <strong>Student Number:</strong>
                                    <span class="ms-2 text-primary fw-bold"><?php echo htmlspecialchars($student_number); ?></span>
                                </div>
                                <div class="mb-3">
                                    <strong>Course/Program:</strong>
                                    <span class="ms-2"><?php echo htmlspecialchars($registration['desired_course']); ?></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <strong>Email:</strong>
                                    <span class="ms-2"><?php echo htmlspecialchars($registration['email_address']); ?></span>
                                </div>
                                <div class="mb-3">
                                    <strong>Mobile Number:</strong>
                                    <span class="ms-2"><?php echo htmlspecialchars($registration['mobile_no']); ?></span>
                                </div>
                                <div class="mb-3">
                                    <strong>Registration Date:</strong>
                                    <span class="ms-2"><?php echo date('F j, Y', strtotime($registration['created_at'])); ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Personal Details -->
                        <hr>
                        <h6 class="fw-bold text-primary mb-3">Personal Details</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <strong>Date of Birth:</strong>
                                    <span class="ms-2"><?php echo date('F j, Y', strtotime($registration['date_of_birth'])); ?></span>
                                </div>
                                <div class="mb-3">
                                    <strong>Gender:</strong>
                                    <span class="ms-2"><?php echo htmlspecialchars($registration['gender']); ?></span>
                                </div>
                                <div class="mb-3">
                                    <strong>Civil Status:</strong>
                                    <span class="ms-2"><?php echo htmlspecialchars($registration['civil_status']); ?></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <strong>Nationality:</strong>
                                    <span class="ms-2"><?php echo htmlspecialchars($registration['nationality']); ?></span>
                                </div>
                                <div class="mb-3">
                                    <strong>Place of Birth:</strong>
                                    <span class="ms-2"><?php echo htmlspecialchars($registration['place_of_birth']); ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Address Information -->
                        <hr>
                        <h6 class="fw-bold text-primary mb-3">Address Information</h6>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <strong>Complete Address:</strong>
                                    <span class="ms-2"><?php echo htmlspecialchars($registration['complete_address']); ?></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <strong>Region:</strong>
                                    <span class="ms-2"><?php echo htmlspecialchars($registration['region']); ?></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <strong>Province:</strong>
                                    <span class="ms-2"><?php echo htmlspecialchars($registration['province']); ?></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <strong>Town/City:</strong>
                                    <span class="ms-2"><?php echo htmlspecialchars($registration['town']); ?></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <strong>Barangay:</strong>
                                    <span class="ms-2"><?php echo htmlspecialchars($registration['barangay']); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($registration['status'] === 'approved'): ?>
                <!-- Enrolled Subjects Card - Only show if approved -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header" style="background-color: rgb(37, 52, 117); color: white;">
                        <h5 class="mb-0"><i class="bi bi-book me-2"></i>Enrolled Subjects</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Subject Code</th>
                                        <th>Description</th>
                                        <th class="text-center">Units</th>
                                        <th>Schedule</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($subjects as $subject): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($subject['code']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($subject['description']); ?></td>
                                        <td class="text-center"><?php echo number_format($subject['units'], 1); ?></td>
                                        <td><?php echo htmlspecialchars($subject['schedule']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="2" class="text-end">Total Units:</th>
                                        <th class="text-center"><?php echo number_format($total_units, 1); ?></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Assessment Summary Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header" style="background-color: rgb(37, 52, 117); color: white;">
                        <h5 class="mb-0"><i class="bi bi-calculator me-2"></i>Assessment Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Fee Type</th>
                                                <th class="text-end">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($fees as $fee): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($fee['type']); ?></td>
                                                <td class="text-end">₱<?php echo number_format($fee['amount'], 2); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <th>Total Assessment:</th>
                                                <th class="text-end">₱<?php echo number_format($total_fees, 2); ?></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="alert alert-info">
                                    <h6 class="fw-bold mb-2"><i class="bi bi-info-circle me-2"></i>Payment Information</h6>
                                    <p class="mb-2"><strong>Due Date:</strong> <?php echo date('F j, Y', strtotime('+30 days')); ?></p>
                                    <p class="mb-0"><small>Please settle your account before the due date to avoid penalties.</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Action Buttons -->
                <div class="text-center mb-4">
                    <a href="dashboard.php" class="btn btn-secondary me-2">
                        <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                    </a>
                    <?php if ($registration['status'] === 'approved'): ?>
                    <button class="btn btn-primary" onclick="window.print()">
                        <i class="bi bi-printer me-2"></i>Print Enrollment
                    </button>
                    <?php elseif ($registration['status'] === 'pending'): ?>
                    <span class="text-muted">
                        <i class="bi bi-clock-history me-2"></i>Waiting for approval
                    </span>
                    <?php elseif ($registration['status'] === 'rejected'): ?>
                    <a href="college-registration.php" class="btn btn-warning">
                        <i class="bi bi-arrow-repeat me-2"></i>Re-apply
                    </a>
                    <?php endif; ?>
                </div>
                
                <?php endif; ?>
            </div> <!-- col-lg-11 -->
        </div> <!-- row -->
    </div> <!-- container -->

    <!-- Print Styles -->
    <style>
        @media print {
            body { print-color-adjust: exact; }
            .btn, .navbar, .alert { display: none !important; }
            .container { margin: 0; padding: 0; max-width: 100%; }
            .card { border: 1px solid #000; page-break-inside: avoid; }
            .card-header { background-color: #253475 !important; color: white !important; }
        }
    </style>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
