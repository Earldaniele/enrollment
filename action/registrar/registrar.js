// Registrar Dashboard JavaScript Functions

// Global variables
let studentsData = [];
let currentFilters = {
    type: 'all',
    paymentStatus: 'all',
    documentStatus: 'all',
    search: ''
};

// Initialize dashboard
document.addEventListener('DOMContentLoaded', function() {
    loadDashboardStats();
});

// Load dashboard statistics
function loadDashboardStats() {
    // Show loading state
    const countElements = [
        'newStudentsCount', 'oldStudentsCount', 
        'shiftingStudentsCount', 'transfereeStudentsCount',
        'completeDocsCount', 'incompleteDocsCount', 
        'pendingValidationCount', 'rejectedDocsCount'
    ];
    
    countElements.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.innerHTML = '<i class="bi bi-hourglass-split"></i>';
        }
    });

    fetch('/enrollmentsystem/action/registrar/general_handler.php?action=get_dashboard_stats')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateDashboardStats(data.data);
            } else {
                console.error('Failed to load dashboard stats:', data.message);
                // Reset to 0 on error
                countElements.forEach(id => {
                    const element = document.getElementById(id);
                    if (element) element.textContent = '0';
                });
            }
        })
        .catch(error => {
            console.error('Error loading dashboard stats:', error);
            // Reset to 0 on error
            countElements.forEach(id => {
                const element = document.getElementById(id);
                if (element) element.textContent = '0';
            });
        });
}

// Update dashboard statistics in UI
function updateDashboardStats(stats) {
    // Update student type counts
    if (document.getElementById('newStudentsCount')) {
        document.getElementById('newStudentsCount').textContent = stats.student_types.new;
    }
    if (document.getElementById('oldStudentsCount')) {
        document.getElementById('oldStudentsCount').textContent = stats.student_types.old;
    }
    if (document.getElementById('shiftingStudentsCount')) {
        document.getElementById('shiftingStudentsCount').textContent = stats.student_types.shifting;
    }
    if (document.getElementById('transfereeStudentsCount')) {
        document.getElementById('transfereeStudentsCount').textContent = stats.student_types.transferee;
    }
    
    // Update document status counts
    if (document.getElementById('completeDocsCount')) {
        document.getElementById('completeDocsCount').textContent = stats.document_status?.complete || 0;
    }
    if (document.getElementById('incompleteDocsCount')) {
        document.getElementById('incompleteDocsCount').textContent = stats.document_status?.incomplete || 0;
    }
    if (document.getElementById('pendingValidationCount')) {
        document.getElementById('pendingValidationCount').textContent = stats.document_status?.pending || 0;
    }
    if (document.getElementById('rejectedDocsCount')) {
        document.getElementById('rejectedDocsCount').textContent = stats.document_status?.rejected || 0;
    }
    
    // Update enrollment status
    if (document.getElementById('enrolledCount')) {
        document.getElementById('enrolledCount').textContent = stats.enrollment_status.enrolled;
    }
    if (document.getElementById('pendingEnrollmentCount')) {
        document.getElementById('pendingEnrollmentCount').textContent = stats.enrollment_status.pending;
    }
    
    // Update recent activities if container exists
    const recentActivitiesContainer = document.getElementById('recentActivities');
    if (recentActivitiesContainer && stats.recent_activities) {
        updateRecentActivities(stats.recent_activities);
    }
}

// Update recent activities
function updateRecentActivities(activities) {
    const container = document.getElementById('recentActivities');
    container.innerHTML = '';
    
    activities.forEach(activity => {
        const activityElement = document.createElement('div');
        activityElement.className = 'activity-item d-flex justify-content-between align-items-center py-2 border-bottom';
        activityElement.innerHTML = `
            <div>
                <strong>${activity.full_name}</strong> (${activity.student_id})
                <br>
                <small class="text-muted">${activity.activity}</small>
            </div>
            <small class="text-muted">${formatDate(activity.activity_date)}</small>
        `;
        container.appendChild(activityElement);
    });
}

// Load students for enrollment
function loadStudentsForEnrollment(filters = {}) {
    const params = new URLSearchParams({
        action: 'get_students_for_enrollment',
        ...filters
    });
    
    fetch(`/enrollmentsystem/action/registrar/enrollment_handler.php?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                studentsData = data.data;
                updateStudentsTable(data.data, 'enrollmentTable');
                updateStudentCount(data.data.length, 'studentCount');
            } else {
                console.error('Failed to load students:', data.message);
                showAlert('error', 'Failed to load students: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error loading students:', error);
            showAlert('error', 'Error loading students');
        });
}

// Load all students
function loadAllStudents(filters = {}) {
    const params = new URLSearchParams({
        action: 'get_all_students',
        ...filters
    });
    
    fetch(`/enrollmentsystem/action/registrar/student_handler.php?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                studentsData = data.data;
                updateStudentsTable(data.data, 'studentsListTable');
                updateStudentCount(data.data.length, 'studentCount');
            } else {
                console.error('Failed to load students:', data.message);
                showAlert('error', 'Failed to load students: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error loading students:', error);
            showAlert('error', 'Error loading students');
        });
}

// Update students table
function updateStudentsTable(students, tableId) {
    const tableBody = document.querySelector(`#${tableId} tbody`);
    if (!tableBody) return;
    
    tableBody.innerHTML = '';
    
    students.forEach(student => {
        const row = document.createElement('tr');
        
        if (tableId === 'enrollmentTable') {
            row.innerHTML = `
                <td>${student.student_id}</td>
                <td>${student.full_name}</td>
                <td><span class="badge bg-info">${student.type}</span></td>
                <td>${student.desired_course}</td>
                <td>
                    <span class="badge ${getPaymentStatusClass(student.payment_status)}">
                        ${formatPaymentStatus(student.payment_status)}
                    </span>
                </td>
                <td>₱${parseFloat(student.total_assessment || 0).toLocaleString()}</td>
                <td>₱${parseFloat(student.balance || 0).toLocaleString()}</td>
                <td>
                    ${student.is_enrolled ? 
                        '<span class="badge bg-success">Enrolled</span>' : 
                        `<button class="btn btn-primary btn-sm" onclick="showEnrollmentForm('${student.student_id}')">
                            <i class="bi bi-person-plus me-1"></i> Enroll
                        </button>`
                    }
                </td>
            `;
        } else {
            row.innerHTML = `
                <td>${student.student_id}</td>
                <td>${student.full_name}</td>
                <td><span class="badge bg-info">${student.type}</span></td>
                <td>${student.desired_course}</td>
                <td>
                    <span class="badge ${getDocumentStatusClass(student.document_status)}">
                        ${student.document_status}
                    </span>
                </td>
                <td>
                    ${student.is_enrolled ? 
                        '<span class="badge bg-success">Enrolled</span>' : 
                        '<span class="badge bg-warning">Not Enrolled</span>'
                    }
                </td>
                <td>
                    <button class="btn btn-outline-primary btn-sm me-1" onclick="viewStudentDetails('${student.student_id}')">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" onclick="validateDocuments('${student.student_id}')">
                        <i class="bi bi-file-check"></i>
                    </button>
                </td>
            `;
        }
        
        tableBody.appendChild(row);
    });
}

// Filter functions
function filterStudents() {
    const type = document.getElementById('studentTypeFilter')?.value || 'all';
    const paymentStatus = document.getElementById('paymentStatusFilter')?.value || 'all';
    const documentStatus = document.getElementById('documentStatusFilter')?.value || 'all';
    const search = document.getElementById('searchInput')?.value || '';
    
    currentFilters = { type, payment_status: paymentStatus, document_status: documentStatus, search };
    
    // Reload data based on current page
    if (window.location.pathname.includes('enrollment.php')) {
        loadStudentsForEnrollment(currentFilters);
    } else if (window.location.pathname.includes('student_list.php')) {
        loadAllStudents(currentFilters);
    }
}

function clearFilters() {
    document.getElementById('studentTypeFilter').value = 'all';
    if (document.getElementById('paymentStatusFilter')) {
        document.getElementById('paymentStatusFilter').value = 'all';
    }
    if (document.getElementById('documentStatusFilter')) {
        document.getElementById('documentStatusFilter').value = 'all';
    }
    document.getElementById('searchInput').value = '';
    
    currentFilters = { type: 'all', payment_status: 'all', document_status: 'all', search: '' };
    
    // Reload data
    if (window.location.pathname.includes('enrollment.php')) {
        loadStudentsForEnrollment();
    } else if (window.location.pathname.includes('student_list.php')) {
        loadAllStudents();
    }
}

function refreshStudentList() {
    if (window.location.pathname.includes('enrollment.php')) {
        loadStudentsForEnrollment(currentFilters);
    } else if (window.location.pathname.includes('student_list.php')) {
        loadAllStudents(currentFilters);
    } else {
        loadDashboardStats();
    }
    
    showAlert('success', 'Data refreshed successfully');
}

// Navigation functions
function viewStudentDetails(studentId) {
    window.location.href = `student_detail.php?id=${studentId}`;
}

function validateDocuments(studentId) {
    window.location.href = `document_validation.php?id=${studentId}`;
}

function showEnrollmentForm(studentId) {
    window.location.href = `enrollment_form.php?student_id=${studentId}`;
}

// Utility functions
function updateStudentCount(count, elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.textContent = `${count} student${count !== 1 ? 's' : ''}`;
    }
}

function getPaymentStatusClass(status) {
    switch (status) {
        case 'fully_paid': return 'bg-success';
        case 'has_balance': return 'bg-warning';
        case 'unpaid': return 'bg-danger';
        default: return 'bg-secondary';
    }
}

function getDocumentStatusClass(status) {
    switch (status) {
        case 'Complete': return 'bg-success';
        case 'Incomplete': return 'bg-warning';
        case 'Pending': return 'bg-info';
        default: return 'bg-secondary';
    }
}

function formatPaymentStatus(status) {
    switch (status) {
        case 'fully_paid': return 'Fully Paid';
        case 'has_balance': return 'Has Balance';
        case 'unpaid': return 'Unpaid';
        default: return 'Unknown';
    }
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function showAlert(type, message) {
    // Create alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Initialize page-specific data
function initializePage() {
    const path = window.location.pathname;
    
    if (path.includes('enrollment.php')) {
        loadStudentsForEnrollment();
    } else if (path.includes('student_list.php')) {
        loadAllStudents();
    } else if (path.includes('index.php')) {
        loadDashboardStats();
    }
}

// Call initialization when DOM is loaded
document.addEventListener('DOMContentLoaded', initializePage);
