<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../includes/auth.php';

// Check if user is logged in using the correct session variable
if (!isset($_SESSION['email'])) {
    header('Location: ../pages/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include '../includes/header.php'; ?>
<body class="student-dashboard">
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container py-5">
        <?php if(isset($_SESSION['registration_success']) && $_SESSION['registration_success']): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-success" role="alert" id="registrationSuccessAlert">
                    <h4 class="alert-heading">Registration Successful!</h4>
                    <p><?= $_SESSION['registration_message'] ?></p>
                    <?php if(isset($_SESSION['registration_data'])): ?>
                    <hr>
                    <p class="mb-0">Student ID: <strong><?= $_SESSION['registration_data']['student_id'] ?></strong></p>
                    <p class="mb-0">Course: <strong><?= $_SESSION['registration_data']['course'] ?></strong></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <script>
            // Auto hide the success alert after 5 seconds
            document.addEventListener('DOMContentLoaded', function() {
                // First delay - wait 5 seconds before starting to fade
                setTimeout(function() {
                    var alertElement = document.getElementById('registrationSuccessAlert');
                    if (alertElement) {
                        // Create a fade out effect with CSS transition
                        alertElement.style.transition = 'opacity 1s ease-out';
                        alertElement.style.opacity = '0';
                        
                        // Remove the element after the transition completes
                        setTimeout(function() {
                            alertElement.style.display = 'none';
                        }, 1000); // 1 second for the transition to complete
                    }
                }, 5000); // 5000 milliseconds = 5 seconds
            });
        </script>
        
        <?php 
            // Clear the session variables after displaying
            unset($_SESSION['registration_success']);
            unset($_SESSION['registration_message']);
            unset($_SESSION['registration_data']);
        ?>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2 class="fw-bold text-primary mb-0" id="welcomeMessage">Welcome, Loading...</h2>
                        </div>
                        <div id="studentIdSection">
                            <!-- Student ID will be shown here only if approved -->
                        </div>
                        <div id="registrationStatus" class="mt-2">
                            <!-- Registration status will be shown here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Queue Status Section -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-primary mb-0">
                                <i class="bi bi-list-ul me-2"></i>Your Current Queue Status
                            </h5>
                        </div>
                        <div id="currentQueuesContainer" class="queue-scroll-container" style="max-height: 500px; overflow-y: auto; padding: 1rem;">
                            <!-- Current queues will be loaded here -->
                            <div class="text-center text-muted py-3">
                                <i class="bi bi-clock-history display-4 mb-3"></i>
                                <h6>No Active Queue Tickets</h6>
                                <p class="mb-3">You don't have any pending queue tickets.</p>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#queueModal">
                                    <i class="bi bi-plus-circle me-1"></i>Get Queue Number
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row" id="queue-management">
            <div class="col-md-3 mb-4">
                <div class="card shadow-sm h-100 dashboard-card">
                    <div class="card-body text-center">
                        <i class="bi bi-clock-history display-1 text-primary mb-3"></i>
                        <h5 class="card-title">Queue Management</h5>
                        <p class="card-text">Get your queue number for faster service at any department</p>
                        
                        <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#queueModal">
                            Get Queue
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-4">
                <div class="card shadow-sm h-100 dashboard-card">
                    <div class="card-body text-center">
                        <i class="bi bi-book display-1 text-primary mb-3"></i>
                        <h5 class="card-title">Enrollment</h5>
                        <p class="card-text">View your enrollment status and course registration</p>
                        <a href="view-enrollment.php" class="btn btn-primary">View Enrollment</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-4">
                <div class="card shadow-sm h-100 dashboard-card">
                    <div class="card-body text-center">
                        <i class="bi bi-file-earmark-plus display-1 text-primary mb-3"></i>
                        <h5 class="card-title">College Registration</h5>
                        <p class="card-text">Complete your college admission application form</p>
                        <a href="./college-registration.php" class="btn btn-primary">Start Registration</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-4">
                <div class="card shadow-sm h-100 dashboard-card">
                    <div class="card-body text-center">
                        <i class="bi bi-credit-card display-1 text-primary mb-3"></i>
                        <h5 class="card-title">Online Payment</h5>
                        <p class="card-text">Pay tuition fees and other charges securely online</p>
                        <a href="payment.php" class="btn btn-primary">Make Payment</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
        
    <!-- Queue Department Selection Modal -->
    <div class="modal fade" id="queueModal" tabindex="-1" aria-labelledby="queueModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="queueModalLabel">
                        <i class="bi bi-clock-history me-2"></i>Select Department
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-4">Choose the department where you need service. You will receive a queue number and estimated waiting time.</p>
                    <div class="d-grid gap-3">
                        <button class="btn btn-lg" style="background-color: transparent; color: rgb(37, 52, 117); border: 2px solid rgb(37, 52, 117);" onclick="selectDepartmentAndClose('Registrar')">
                            <i class="bi bi-file-earmark-text me-2"></i>
                            <div>
                                <strong>Registrar</strong>
                                <br><small>Transcripts, Certifications, Records</small>
                            </div>
                        </button>
                        <button class="btn btn-lg" style="background-color: transparent; color: rgb(37, 52, 117); border: 2px solid rgb(37, 52, 117);" onclick="selectDepartmentAndClose('Treasury')">
                            <i class="bi bi-cash-coin me-2"></i>
                            <div>
                                <strong>Treasury</strong>
                                <br><small>Payments, Billing, Financial Services</small>
                            </div>
                        </button>
                        <button class="btn btn-lg" style="background-color: transparent; color: rgb(37, 52, 117); border: 2px solid rgb(37, 52, 117);" onclick="selectDepartmentAndClose('Enrollment')">
                            <i class="bi bi-people me-2"></i>
                            <div>
                                <strong>Enrollment</strong>
                                <br><small>Course Registration, Class Schedules</small>
                            </div>
                        </button>
                    </div>
                    
                    <!-- Alert Box for Queue Messages -->
                    <div id="queueModalAlert" class="mt-3" style="display: none;">
                        <!-- Dynamic alert messages will appear here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications Modal -->
    <?php include '../includes/notifications-modal.php'; ?>
    
<!-- QR Code Modal -->
    <div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrCodeModalLabel">
                        <i class="bi bi-qr-code me-2"></i>Queue QR Code
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="alert alert-info mb-3">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Status: Ready</strong> - You have been called to proceed to the counter
                    </div>
 
                    <div id="qrCodeContainer" class="d-flex justify-content-center align-items-center mb-3" style="width: 100%; min-height: 350px;"></div>
 
                    <div class="alert alert-warning mb-3">
                        <i class="bi bi-clock me-2"></i>
                        <strong>Important:</strong> You have 2 minutes to present this QR code at the counter
                    </div>
 
                    <div class="alert alert-success mb-2">
                        <i class="bi bi-lightbulb me-2"></i>
                        <small>For best scanning results, keep your screen brightness high when showing this QR code.</small>
                    </div>
 
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <small class="text-muted d-block">Department</small>
                                <strong id="qrCodeDepartment">-</strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <small class="text-muted d-block">Student ID</small>
                                <strong id="qrCodeStudentId">-</strong>
                            </div>
                        </div>
                    </div>
 
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="bi bi-lightbulb me-1"></i>
                            Present this QR code to the staff when you reach the counter
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" onclick="downloadQRCode()">
                        <i class="bi bi-download me-1"></i>Download QR
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
    
<!-- QR Code Generator Library -->
    <script src="/enrollmentsystem/assets/js/qrcode.min.js"></script>
    <script src="/enrollmentsystem/assets/html5-qrcode/html5-qrcode.min.js"></script>
    <script src="/enrollmentsystem/assets/js/notifications.js?v=<?php echo time(); ?>"></script>
    <script src="/enrollmentsystem/assets/js/queue-management.js?v=<?php echo time(); ?>"></script>
    <script src="/enrollmentsystem/assets/js/dashboard.js?v=<?php echo time(); ?>"></script>
</body>
</html>
