<?php
// Start session and require authentication
session_start();
require_once '../../includes/registrar_auth.php';
requireRegistrarAuth();

// Get current registrar info
$registrar = getCurrentRegistrar();

// Get student type from URL parameter
$studentType = isset($_GET['type']) ? $_GET['type'] : 'all';
$studentTypeTitle = ucfirst($studentType);

// Convert student type to display title
if ($studentType == 'all') {
    $pageTitle = "All Students";
} else {
    $pageTitle = "$studentTypeTitle Students";
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
                                <h2 class="fw-bold mb-1"><?php echo $pageTitle; ?></h2>
                                <p class="text-muted mb-0">Manage student records approved for registrar processing</p>
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
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Document Status</label>
                                <select class="form-select" id="documentStatusFilter" onchange="filterStudents()">
                                    <option value="all">All Statuses</option>
                                    <option value="complete">Complete</option>
                                    <option value="incomplete">Incomplete</option>
                                    <option value="pending">Pending Validation</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Search</label>
                                <input type="text" class="form-control" id="searchInput" placeholder="Student ID or Name" onkeyup="filterStudents()">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <button class="btn btn-outline-secondary border w-100" onclick="clearFilters()">
                                    <i class="bi bi-x-circle me-1"></i> Clear
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students List Table -->
        <div class="row">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Student Records</h5>
                        <span class="badge bg-primary" id="studentCount">0 students</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="studentsListTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Course</th>
                                        <th>Document Status</th>
                                        <th>Enrollment Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be loaded dynamically -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Local JavaScript Files -->
    <script src="../../../action/registrar/registrar.js"></script>
    
    <script>
        // Ensure DOM is loaded before executing
        document.addEventListener('DOMContentLoaded', function() {
            // Get initial filter from URL parameter
            const urlParams = new URLSearchParams(window.location.search);
            const initialType = urlParams.get('type') || 'all';
            
            // Set the filter dropdown to match URL parameter
            const typeFilter = document.getElementById('studentTypeFilter');
            if (typeFilter) {
                typeFilter.value = initialType;
            }
            
            // Load students with the initial filter
            if (typeof loadAllStudents === 'function') {
                loadAllStudents({ type: initialType });
            } else {
                console.error('loadAllStudents function not found');
                // Fallback: try to load students manually with filter
                loadStudentsManually({ type: initialType });
            }
        });
        
        // Simple alert function to replace SweetAlert2
        function showAlert(type, message) {
            alert(message);
        }
        
        // Fallback function to load students
        function loadStudentsManually(filters = {}) {
            // Show loading state
            const tableBody = document.querySelector('#studentsListTable tbody');
            if (tableBody) {
                tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>';
            }
            
            // Build URL with filters
            let url = '/enrollmentsystem/action/registrar/simple_student_handler.php?action=get_all_students';
            if (filters.type && filters.type !== 'all') {
                url += `&type=${encodeURIComponent(filters.type)}`;
            }
            if (filters.document_status && filters.document_status !== 'all') {
                url += `&document_status=${encodeURIComponent(filters.document_status)}`;
            }
            if (filters.search) {
                url += `&search=${encodeURIComponent(filters.search)}`;
            }
            
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        updateStudentCountManual(data.data.length);
                        updateStudentsTableManual(data.data);
                    } else {
                        console.error('Failed to load students:', data.message);
                        if (tableBody) {
                            tableBody.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-4">Error: ${data.message}</td></tr>`;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading students:', error);
                    if (tableBody) {
                        tableBody.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-4">Error loading students: ${error.message}</td></tr>`;
                    }
                });
        }
        
        // Manual update functions
        function updateStudentCountManual(count) {
            const element = document.getElementById('studentCount');
            if (element) {
                element.textContent = `${count} student${count !== 1 ? 's' : ''}`;
            }
        }
        
        function updateStudentsTableManual(students) {
            const tableBody = document.querySelector('#studentsListTable tbody');
            if (!tableBody) return;
            
            tableBody.innerHTML = '';
            
            if (students.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">No students found</td></tr>';
                return;
            }
            
            students.forEach(student => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${student.student_id}</td>
                    <td>${student.first_name} ${student.last_name}</td>
                    <td>
                        <span class="badge ${getStudentTypeBadgeClass(student.type)}" title="${getStudentTypeDescription(student)}">
                            ${student.type}
                        </span>
                        ${student.tertiary_school ? `<br><small class="text-muted">From: ${student.tertiary_school}</small>` : ''}
                    </td>
                    <td>${student.desired_course}</td>
                    <td><span class="badge bg-success">Complete</span></td>
                    <td><span class="badge bg-primary">Enrolled</span></td>
                    <td>
                        <div class="d-flex gap-1" role="group">
                            <button class="btn btn-outline-primary btn-sm" onclick="viewStudent('${student.student_id}')" title="View Student Details">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-outline-info btn-sm" onclick="validateStudent('${student.student_id}')" title="Validate Documents">
                                <i class="bi bi-file-earmark-check"></i>
                            </button>
                            <button class="btn btn-outline-success btn-sm" onclick="enrollStudent('${student.student_id}')" title="Enroll Student">
                                <i class="bi bi-person-plus"></i>
                            </button>
                        </div>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        }
        
        function getStudentTypeBadgeClass(studentType) {
            switch(studentType) {
                case 'New Student': return 'bg-success';
                case 'Old Student': return 'bg-primary';
                case 'Shifting Student': return 'bg-warning';
                default: return 'bg-secondary';
            }
        }
        
        function getStudentTypeDescription(student) {
            if (student.student_id.includes('SHIFT')) {
                return 'Shifting Student - Changed course/program';
            } else if (student.student_id.includes('2025-')) {
                return 'New Student - First time enrollee for AY 2025';
            } else if (student.student_id.includes('2024-')) {
                return 'Old Student - Continuing from previous year';
            }
            return 'Student type determined by enrollment history';
        }
        
        // Legacy function for backward compatibility
        function getStudentType(studentId) {
            if (studentId.includes('2025-')) return 'New Student';
            if (studentId.includes('2024-')) return 'Old Student';
            return 'Shifting Student';
        }
        
        // Filter students function
        function filterStudents() {
            const type = document.getElementById('studentTypeFilter')?.value || 'all';
            const documentStatus = document.getElementById('documentStatusFilter')?.value || 'all';
            const search = document.getElementById('searchInput')?.value || '';
            
            if (typeof loadAllStudents === 'function') {
                loadAllStudents({ type, document_status: documentStatus, search });
            } else {
                // Use simple handler for filtering
                const params = new URLSearchParams({ action: 'get_all_students' });
                if (type !== 'all') params.append('type', type);
                if (documentStatus !== 'all') params.append('document_status', documentStatus);
                if (search) params.append('search', search);
                
                fetch(`/enrollmentsystem/action/registrar/simple_student_handler.php?${params}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            updateStudentCountManual(data.data.length);
                            updateStudentsTableManual(data.data);
                        }
                    })
                    .catch(error => console.error('Filter error:', error));
            }
        }
        
        // Clear filters function
        function clearFilters() {
            document.getElementById('studentTypeFilter').value = 'all';
            document.getElementById('documentStatusFilter').value = 'all';
            document.getElementById('searchInput').value = '';
            filterStudents();
        }
        
        // Refresh function
        function refreshStudentList() {
            // Get current filter values
            const type = document.getElementById('studentTypeFilter')?.value || 'all';
            const documentStatus = document.getElementById('documentStatusFilter')?.value || 'all';
            const search = document.getElementById('searchInput')?.value || '';
            
            if (typeof loadAllStudents === 'function') {
                loadAllStudents({ type, document_status: documentStatus, search });
            } else {
                loadStudentsManually({ type, document_status: documentStatus, search });
            }
        }
        
        // View student function
        function viewStudent(studentId) {
            window.location.href = `student_details.php?id=${studentId}`;
        }
        
        // Validate student documents function
        function validateStudent(studentId) {
            window.location.href = `document_validation.php?id=${studentId}`;
        }
        
        // Enroll student function
        function enrollStudent(studentId) {
            window.location.href = `enrollment_form.php?student_id=${studentId}`;
        }
    </script>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>
