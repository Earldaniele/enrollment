<?php
// Start session and require authentication
session_start();
require_once '../../includes/cashier_auth.php';
requireCashierAuth();

// Get current cashier info
$cashier = getCurrentCashier();
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
                                <h2 class="fw-bold mb-1">Students Enrolled by Registrar</h2>
                                <p class="text-muted mb-0">Manage payment records for students approved by registrar</p>
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
                                <label class="form-label">Payment Status</label>
                                <select class="form-select" id="paymentStatusFilter" onchange="filterStudents()">
                                    <option value="all">All Statuses</option>
                                    <option value="unpaid">Unpaid</option>
                                    <option value="installment">Installment</option>
                                    <option value="paid">Fully Paid</option>
                                    <option value="pending">Pending Verification</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Course</label>
                                <select class="form-select" id="courseFilter" onchange="filterStudents()">
                                    <option value="all">All Courses</option>
                                    <!-- Options will be loaded dynamically -->
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Year Level</label>
                                <select class="form-select" id="yearLevelFilter" onchange="filterStudents()">
                                    <option value="all">All Years</option>
                                    <option value="1">1st Year</option>
                                    <option value="2">2nd Year</option>
                                    <option value="3">3rd Year</option>
                                    <option value="4">4th Year</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Search</label>
                                <input type="text" class="form-control" id="searchInput" placeholder="Student ID or Name" onkeyup="filterStudents()">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button class="btn btn-outline-secondary" onclick="clearFilters()">
                                        <i class="bi bi-x-circle me-1"></i> Clear
                                    </button>
                                </div>
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
                        <h5 class="mb-0">Student Payment Records</h5>
                        <span class="badge bg-primary" id="studentCount">Loading...</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>Course</th>
                                        <th>Year</th>
                                        <th>Total Fee</th>
                                        <th>Payment Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="studentsTable">
                                    <!-- Dynamic content will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center mb-0" id="pagination">
                                <!-- Pagination will be loaded dynamically -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS -->
    <script>
        let currentPage = 1;
        let totalPages = 1;
        
        // Load data when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadCourses();
            loadStudents();
        });
        
        // Load courses for filter dropdown
        function loadCourses() {
            fetch('/enrollmentsystem/action/cashier/student_list_handler.php?action=get_courses')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.text();
                })
                .then(text => {
                    try {
                        const data = JSON.parse(text);
                        if (data.success) {
                            const courseFilter = document.getElementById('courseFilter');
                            // Keep the "All Courses" option
                            const allOption = courseFilter.querySelector('option[value="all"]');
                            courseFilter.innerHTML = '';
                            courseFilter.appendChild(allOption);
                            
                            data.data.forEach(course => {
                                const option = document.createElement('option');
                                option.value = course.value;
                                option.textContent = course.label;
                                courseFilter.appendChild(option);
                            });
                        }
                    } catch (e) {
                        console.error('Invalid JSON response for courses:', text);
                        throw new Error('Invalid JSON response');
                    }
                })
                .catch(error => {
                    console.error('Error loading courses:', error);
                });
        }
        
        // Load students based on filters
        function loadStudents(page = 1) {
            const filters = {
                payment_status: document.getElementById('paymentStatusFilter').value,
                course: document.getElementById('courseFilter').value,
                year_level: document.getElementById('yearLevelFilter').value,
                search: document.getElementById('searchInput').value,
                page: page,
                limit: 10
            };
            
            const queryString = new URLSearchParams(filters).toString();
            
            fetch(`/enrollmentsystem/action/cashier/student_list_handler.php?action=get_students&${queryString}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.text();
                })
                .then(text => {
                    try {
                        const data = JSON.parse(text);
                        if (data.success) {
                            displayStudents(data.data.students);
                            updatePagination(data.data.current_page, data.data.total_pages);
                            updateStudentCount(data.data.total_count);
                            currentPage = data.data.current_page;
                            totalPages = data.data.total_pages;
                        } else {
                            console.error('Error loading students:', data.message);
                        }
                    } catch (e) {
                        console.error('Invalid JSON response for students:', text);
                        throw new Error('Invalid JSON response');
                    }
                })
                .catch(error => {
                    console.error('Error loading students:', error);
                });
        }
        
        // Display students in table
        function displayStudents(students) {
            const tbody = document.getElementById('studentsTable');
            tbody.innerHTML = '';
            
            if (students.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center">No students found</td></tr>';
                return;
            }
            
            students.forEach(student => {
                const row = document.createElement('tr');
                const statusClass = getStatusClass(student.payment_status);
                
                row.innerHTML = `
                    <td>${student.student_id}</td>
                    <td>${student.student_name}</td>
                    <td>${student.course}</td>
                    <td>${student.year_level}</td>
                    <td>₱${student.total_fee}</td>
                    <td><span class="badge bg-${statusClass}">${student.status_display.label}</span></td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="payment_details.php?id=${student.student_id}" class="btn btn-primary btn-sm" title="View Payment">
                                <i class="bi bi-eye"></i>
                            </a>
                            ${student.payment_status === 'paid' ? 
                                `<a href="official_receipt.php?id=${student.student_id}" class="btn btn-info btn-sm" title="View Receipt">
                                    <i class="bi bi-receipt"></i>
                                </a>` :
                                `<span class="badge bg-warning">Pending Payment</span>`
                            }
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }
        
        // Get status class for badge
        function getStatusClass(status) {
            const classes = {
                'unpaid': 'danger',
                'partial': 'warning',
                'paid': 'success',
                'pending_verification': 'info'
            };
            return classes[status] || 'secondary';
        }
        
        // Update pagination
        function updatePagination(current, total) {
            const pagination = document.getElementById('pagination');
            pagination.innerHTML = '';
            
            if (total <= 1) return;
            
            // Previous button
            const prevItem = document.createElement('li');
            prevItem.className = `page-item ${current === 1 ? 'disabled' : ''}`;
            prevItem.innerHTML = `
                <a class="page-link" href="#" onclick="changePage(${current - 1})" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            `;
            pagination.appendChild(prevItem);
            
            // Page numbers
            const startPage = Math.max(1, current - 2);
            const endPage = Math.min(total, current + 2);
            
            for (let i = startPage; i <= endPage; i++) {
                const pageItem = document.createElement('li');
                pageItem.className = `page-item ${i === current ? 'active' : ''}`;
                pageItem.innerHTML = `<a class="page-link" href="#" onclick="changePage(${i})">${i}</a>`;
                pagination.appendChild(pageItem);
            }
            
            // Next button
            const nextItem = document.createElement('li');
            nextItem.className = `page-item ${current === total ? 'disabled' : ''}`;
            nextItem.innerHTML = `
                <a class="page-link" href="#" onclick="changePage(${current + 1})" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            `;
            pagination.appendChild(nextItem);
        }
        
        // Update student count
        function updateStudentCount(count) {
            document.getElementById('studentCount').textContent = `${count} students`;
        }
        
        // Change page
        function changePage(page) {
            if (page >= 1 && page <= totalPages && page !== currentPage) {
                loadStudents(page);
            }
        }
        
        // Filter students based on selected criteria
        function filterStudents() {
            currentPage = 1; // Reset to first page when filtering
            loadStudents();
        }
        
        // Clear all filters
        function clearFilters() {
            document.getElementById('paymentStatusFilter').value = 'all';
            document.getElementById('courseFilter').value = 'all';
            document.getElementById('yearLevelFilter').value = 'all';
            document.getElementById('searchInput').value = '';
            filterStudents();
        }
        
        // Refresh student list
        function refreshStudentList() {
            loadStudents(currentPage);
        }
    </script>

    <!-- Notifications Modal -->
    <?php include '../../includes/notifications-modal.php'; ?>

    <!-- Include notification JavaScript -->
    <script src="../../../assets/js/notifications.js?v=<?php echo time(); ?>"></script>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>
