// Document Validation JavaScript Functions

let currentStudent = null;
let documentRequirements = [];

// Initialize document validation page
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const studentId = urlParams.get('id');
    
    if (studentId) {
        loadDocumentRequirements(studentId);
    }
});

// Load document requirements for student
function loadDocumentRequirements(studentId) {
    fetch(`/enrollmentsystem/action/registrar/document_handler.php?action=get_document_requirements&student_id=${studentId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentStudent = data.data.student;
                documentRequirements = data.data.documents;
                updateDocumentValidationUI(data.data);
            } else {
                console.error('Failed to load document requirements:', data.message);
                showAlert('error', 'Failed to load document requirements: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error loading document requirements:', error);
            showAlert('error', 'Error loading document requirements');
        });
}

// Update document validation UI
function updateDocumentValidationUI(data) {
    // Update student info
    updateStudentInfo(data.student);
    
    // Update document list
    updateDocumentsList(data.documents);
    
    // Update summary
    updateDocumentSummary(data.summary);
    
    // Update progress bar
    updateProgressBar(data.summary.completion_percentage);
}

// Update student information section
function updateStudentInfo(student) {
    const studentInfoContainer = document.getElementById('studentInfo');
    if (!studentInfoContainer) return;
    
    const studentType = determineStudentType(student.student_id);
    
    studentInfoContainer.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <p><strong>Student ID:</strong> ${student.student_id}</p>
                <p><strong>Name:</strong> ${student.first_name} ${student.last_name}</p>
                <p><strong>Course:</strong> ${student.desired_course}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Type:</strong> <span class="badge bg-info">${studentType}</span></p>
                <p><strong>Email:</strong> ${student.email_address}</p>
                <p><strong>Phone:</strong> ${student.mobile_no}</p>
            </div>
        </div>
    `;
}

// Update documents list
function updateDocumentsList(documents) {
    const documentsContainer = document.getElementById('documentsList');
    if (!documentsContainer) return;
    
    documentsContainer.innerHTML = '';
    
    documents.forEach((doc, index) => {
        const docElement = document.createElement('div');
        docElement.className = 'card mb-3';
        docElement.innerHTML = `
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h6 class="mb-1">${doc.name}</h6>
                        <small class="text-muted">
                            ${doc.required ? '<span class="badge bg-danger">Required</span>' : '<span class="badge bg-secondary">Optional</span>'}
                        </small>
                    </div>
                    <div class="col-md-3">
                        <span class="badge ${getDocumentStatusBadgeClass(doc.status)} w-100">
                            ${formatDocumentStatus(doc.status)}
                        </span>
                        ${doc.validated_at ? `<small class="text-muted d-block mt-1">Validated: ${formatDate(doc.validated_at)}</small>` : ''}
                    </div>
                    <div class="col-md-3">
                        <div class="btn-group w-100" role="group">
                            <button class="btn btn-sm btn-outline-success" onclick="updateDocumentStatus('${doc.name}', 'submitted')" 
                                    ${doc.status === 'submitted' ? 'disabled' : ''}>
                                <i class="bi bi-check-circle"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-warning" onclick="updateDocumentStatus('${doc.name}', 'pending')"
                                    ${doc.status === 'pending' ? 'disabled' : ''}>
                                <i class="bi bi-clock"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="updateDocumentStatus('${doc.name}', 'missing')"
                                    ${doc.status === 'missing' ? 'disabled' : ''}>
                                <i class="bi bi-x-circle"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        documentsContainer.appendChild(docElement);
    });
}

// Update document summary
function updateDocumentSummary(summary) {
    const summaryContainer = document.getElementById('documentSummary');
    if (!summaryContainer) return;
    
    summaryContainer.innerHTML = `
        <div class="row text-center">
            <div class="col-3">
                <h4 class="text-primary">${summary.total_required}</h4>
                <small class="text-muted">Required</small>
            </div>
            <div class="col-3">
                <h4 class="text-success">${summary.completed}</h4>
                <small class="text-muted">Completed</small>
            </div>
            <div class="col-3">
                <h4 class="text-warning">${summary.total_required - summary.completed}</h4>
                <small class="text-muted">Pending</small>
            </div>
            <div class="col-3">
                <h4 class="text-info">${summary.completion_percentage}%</h4>
                <small class="text-muted">Complete</small>
            </div>
        </div>
    `;
}

// Update progress bar
function updateProgressBar(percentage) {
    const progressBar = document.getElementById('documentProgress');
    if (!progressBar) return;
    
    progressBar.style.width = percentage + '%';
    progressBar.setAttribute('aria-valuenow', percentage);
    progressBar.textContent = percentage + '%';
    
    // Update progress bar color based on completion
    progressBar.className = 'progress-bar';
    if (percentage === 100) {
        progressBar.classList.add('bg-success');
    } else if (percentage >= 75) {
        progressBar.classList.add('bg-info');
    } else if (percentage >= 50) {
        progressBar.classList.add('bg-warning');
    } else {
        progressBar.classList.add('bg-danger');
    }
}

// Update document status
function updateDocumentStatus(documentName, status) {
    if (!currentStudent) {
        showAlert('error', 'No student selected');
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'update_document_status');
    formData.append('student_id', currentStudent.student_id);
    formData.append('document_name', documentName);
    formData.append('status', status);
    formData.append('remarks', 'Status updated by registrar');
    
    fetch('/enrollmentsystem/action/registrar/document_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Document status updated successfully');
            // Reload document requirements
            loadDocumentRequirements(currentStudent.student_id);
        } else {
            showAlert('error', 'Failed to update document status: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error updating document status:', error);
        showAlert('error', 'Error updating document status');
    });
}

// Approve all documents
function approveAllDocuments() {
    if (!currentStudent) {
        showAlert('error', 'No student selected');
        return;
    }
    
    if (!confirm('Are you sure you want to approve all documents for this student?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'approve_all_documents');
    formData.append('student_id', currentStudent.student_id);
    
    fetch('/enrollmentsystem/action/registrar/document_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'All documents approved successfully');
            // Reload document requirements
            loadDocumentRequirements(currentStudent.student_id);
        } else {
            showAlert('error', 'Failed to approve documents: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error approving documents:', error);
        showAlert('error', 'Error approving documents');
    });
}

// Navigate back to student list
function goBackToStudentList() {
    window.location.href = 'student_list.php';
}

// Proceed to enrollment
function proceedToEnrollment() {
    if (!currentStudent) {
        showAlert('error', 'No student selected');
        return;
    }
    
    window.location.href = `enrollment_form.php?student_id=${currentStudent.student_id}`;
}

// Utility functions
function determineStudentType(studentId) {
    const year = studentId.substring(0, 4);
    if (year === '2025') {
        return 'New Student';
    } else if (year === '2024') {
        return 'Old Student';
    } else {
        return 'Transferee';
    }
}

function getDocumentStatusBadgeClass(status) {
    switch (status) {
        case 'submitted': return 'bg-success';
        case 'pending': return 'bg-warning';
        case 'missing': return 'bg-danger';
        default: return 'bg-secondary';
    }
}

function formatDocumentStatus(status) {
    switch (status) {
        case 'submitted': return 'Submitted';
        case 'pending': return 'Pending';
        case 'missing': return 'Missing';
        default: return 'Unknown';
    }
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric'
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
