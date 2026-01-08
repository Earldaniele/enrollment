<?php
// Start session and require authentication
session_start();
require_once '../../includes/registrar_auth.php';
requireRegistrarAuth();

// Get current registrar info
$registrar = getCurrentRegistrar();

// Get student ID from URL parameter (for single student enrollment)
$studentId = isset($_GET['id']) ? $_GET['id'] : '';

// Initialize data
$students = [];
$error = null;
$studentType = isset($_GET['type']) ? $_GET['type'] : 'all';

// Fetch students data for enrollment
if (empty($studentId)) {
    // Load all students ready for enrollment
    try {
        require_once '../../includes/db_config.php';
        
        $sql = "SELECT 
                    sr.student_id,
                    sr.first_name,
                    sr.last_name,
                    sr.desired_course,
                    sr.email_address,
                    sr.student_type,
                    sr.tertiary_school,
                    sr.status as registration_status,
                    sr.created_at
                FROM student_registrations sr
                WHERE sr.status = 'approved'";
        
        // Add student type filter using the student_type column
        if ($studentType !== 'all') {
            if ($studentType === 'new') {
                $sql .= " AND sr.student_type = 'New'";
            } elseif ($studentType === 'old') {
                $sql .= " AND sr.student_type = 'Old'";
            } elseif ($studentType === 'shifting') {
                $sql .= " AND sr.student_type = 'Shifting'";
            } elseif ($studentType === 'transferee') {
                $sql .= " AND sr.student_type = 'Transferee'";
            }
        }
        
        $sql .= " ORDER BY sr.created_at DESC";
        
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Use student_type from database or fallback to detection logic
                if (!empty($row['student_type'])) {
                    $row['type'] = $row['student_type'];
                    switch($row['student_type']) {
                        case 'New': $row['type_class'] = 'bg-success'; break;
                        case 'Old': $row['type_class'] = 'bg-primary'; break;
                        case 'Shifting': $row['type_class'] = 'bg-warning'; break;
                        case 'Transferee': $row['type_class'] = 'bg-info'; break;
                        default: $row['type_class'] = 'bg-secondary'; break;
                    }
                } else {
                    // Fallback logic for legacy data
                    if (!empty($row['tertiary_school']) && $row['tertiary_school'] != '') {
                        $row['type'] = 'Transferee';
                        $row['type_class'] = 'bg-info';
                    } else {
                        $year = substr($row['student_id'], 0, 4);
                        if ($year == '2025') {
                            $row['type'] = 'New';
                            $row['type_class'] = 'bg-success';
                        } elseif ($year == '2024') {
                            $row['type'] = 'Old';
                            $row['type_class'] = 'bg-primary';
                        } else {
                            $row['type'] = 'Other';
                            $row['type_class'] = 'bg-secondary';
                        }
                    }
                }
                
                $row['full_name'] = $row['first_name'] . ' ' . $row['last_name'];
                $students[] = $row;
            }
        }
    } catch (Exception $e) {
        $error = 'Failed to load students: ' . $e->getMessage();
    }
}

$pageTitle = "Student Enrollment";
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
                                <h2 class="fw-bold mb-1"><?php echo $pageTitle; ?></h2>
                                <p class="text-muted mb-0">Officially enroll students after payment verification</p>
                            </div>
                            <div>
                                <button class="btn btn-primary" onclick="refreshStudentList()">
                                    <i class="bi bi-arrow-clockwise me-1"></i> Refresh
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
                                <label class="form-label">Student Type</label>
                                <select class="form-select" id="studentTypeFilter" onchange="filterStudents()">
                                    <option value="all" <?php echo $studentType == 'all' ? 'selected' : ''; ?>>All Types</option>
                                    <option value="new" <?php echo $studentType == 'new' ? 'selected' : ''; ?>>New Students</option>
                                    <option value="old" <?php echo $studentType == 'old' ? 'selected' : ''; ?>>Old Students</option>
                                    <option value="shifting" <?php echo $studentType == 'shifting' ? 'selected' : ''; ?>>Shifting Students</option>
                                    <option value="transferee" <?php echo $studentType == 'transferee' ? 'selected' : ''; ?>>Transferees</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Payment Status</label>
                                <select class="form-select" id="paymentStatusFilter" onchange="filterStudents()">
                                    <option value="all">All Statuses</option>
                                    <option value="fully_paid">Fully Paid</option>
                                    <option value="has_balance">Has Balance</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Search</label>
                                <input type="text" class="form-control" id="searchInput" placeholder="Student ID or Name" onkeyup="filterStudents()">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <button class="btn btn-outline-secondary border w-100" onclick="refreshPage()">
                                    <i class="bi bi-arrow-clockwise me-1"></i> Refresh
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($error): ?>
        <!-- Error Display -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Students List Table -->
        <div class="row">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Students for Enrollment</h5>
                        <span class="badge bg-primary" id="studentCount"><?php echo count($students); ?> students</span>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($students)): ?>
                        <div class="text-center py-4">
                            <i class="bi bi-person-x text-muted" style="font-size: 2rem;"></i>
                            <p class="mt-2 text-muted">No students found for enrollment.</p>
                        </div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="enrollmentTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Course</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $student): ?>
                                    <tr>
                                        <td class="fw-bold"><?php echo htmlspecialchars($student['student_id']); ?></td>
                                        <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                        <td>
                                            <span class="badge <?php echo $student['type_class']; ?>">
                                                <?php echo $student['type']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($student['desired_course']); ?></td>
                                        <td><?php echo htmlspecialchars($student['email_address']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $student['registration_status'] == 'approved' ? 'success' : 'warning'; ?>">
                                                <?php echo ucfirst($student['registration_status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="document_validation.php?id=<?php echo $student['student_id']; ?>" 
                                                   class="btn btn-outline-primary" title="Validate Documents">
                                                    <i class="bi bi-file-earmark-check me-1"></i> Validate
                                                </a>
                                                <button class="btn btn-success" onclick="enrollStudent('<?php echo $student['student_id']; ?>')" title="Enroll Student">
                                                    <i class="bi bi-person-check me-1"></i> Enroll
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Filter students function
        function filterStudents() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const typeFilter = document.getElementById('studentTypeFilter').value;
            const rows = document.querySelectorAll('#enrollmentTable tbody tr');
            let visibleCount = 0;
            
            rows.forEach(row => {
                const studentId = row.cells[0].textContent.toLowerCase();
                const studentName = row.cells[1].textContent.toLowerCase();
                const studentType = row.cells[2].textContent.toLowerCase();
                
                const matchesSearch = studentId.includes(searchInput) || studentName.includes(searchInput);
                const matchesType = typeFilter === 'all' || 
                    (typeFilter === 'new' && studentType.includes('new')) ||
                    (typeFilter === 'old' && studentType.includes('old')) ||
                    (typeFilter === 'shifting' && studentType.includes('shifting')) ||
                    (typeFilter === 'transferee' && studentType.includes('transferee'));
                
                if (matchesSearch && matchesType) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Update count
            const countElement = document.getElementById('studentCount');
            if (countElement) {
                countElement.textContent = visibleCount + ' students';
            }
        }
        
        // Refresh page function
        function refreshPage() {
            window.location.reload();
        }
        
        // Refresh student list function
        function refreshStudentList() {
            window.location.reload();
        }
        
        // Enroll student function - Redirect to subject selection
        function enrollStudent(studentId) {
            if (confirm(`Are you sure you want to enroll student ${studentId}? You will be redirected to subject selection.`)) {
                // Show processing message
                const button = event.target.closest('button');
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Redirecting...';
                button.disabled = true;
                
                // Redirect to enrollment form for subject selection
                window.location.href = `enrollment_form.php?student_id=${studentId}`;
            }
        }
    </script>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>
