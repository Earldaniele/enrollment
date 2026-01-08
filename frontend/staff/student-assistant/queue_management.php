<?php
// Start session and require authentication
session_start();

// Temporary bypass authentication for development
if (!isset($_SESSION['student_assistant_id'])) {
    // Set session variables for development
    $_SESSION['student_assistant_id'] = 'SA001';
    $_SESSION['student_assistant_name'] = 'Student Assistant';
    $_SESSION['student_assistant_department'] = 'Main Office';
    $_SESSION['student_assistant_email'] = 'student.assistant@ncst.edu.ph';
}

// Get current student assistant info
$studentAssistant = [
    'id' => $_SESSION['student_assistant_id'],
    'name' => $_SESSION['student_assistant_name'],
    'department' => $_SESSION['student_assistant_department'],
    'email' => $_SESSION['student_assistant_email']
];
?>
<!DOCTYPE html>
<html lang="en">
<?php include '../../includes/header.php'; ?>
<body class="student-assistant-dashboard">
    <?php include '../../includes/navbar.php'; ?>
    
    <div class="container py-5">
        <!-- Welcome Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card dashboard-card welcome-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="fw-bold mb-1">Queue Management</h2>
                                <p class="mb-0">NCST Enrollment System - Student Assistant</p>
                            </div>
                            <div>
                                <i class="bi bi-people-fill text-white" style="font-size: 3rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Queue Management Content -->
        <div class="row">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Manage Queue</h5>
                    </div>
                    <div class="card-body">
                        <p>Queue management functionality will be implemented here.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS and SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Placeholder for future functionality
    </script>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>
