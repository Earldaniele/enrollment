// Enrollment Form JavaScript Functions

console.log('Enrollment form JavaScript loaded successfully');

let currentStudent = null;
let availableSubjects = [];
let selectedSubjects = [];

// Initialize enrollment form
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const studentId = urlParams.get('student_id');
    
    if (studentId) {
        loadEnrollmentFormData(studentId);
    }
});

// Load enrollment form data
function loadEnrollmentFormData(studentId) {
    fetch(`/enrollmentsystem/action/registrar/general_handler.php?action=get_enrollment_form_data&student_id=${studentId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentStudent = data.data.student;
                availableSubjects = data.data.subjects;
                updateEnrollmentFormUI(data.data);
            } else {
                console.error('Failed to load enrollment form data:', data.message);
                showAlert('error', 'Failed to load enrollment form data: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error loading enrollment form data:', error);
            showAlert('error', 'Error loading enrollment form data');
        });
}

// Update enrollment form UI
function updateEnrollmentFormUI(data) {
    // Update student information
    updateStudentInfo(data.student);
    
    // Set suggested year level
    const yearLevelSelect = document.getElementById('yearLevel');
    if (yearLevelSelect) {
        yearLevelSelect.value = data.suggested_year_level;
    }
    
    // Update academic period display
    const academicPeriodElement = document.getElementById('academicPeriod');
    if (academicPeriodElement) {
        academicPeriodElement.textContent = `${data.academic_period.school_year} - ${data.academic_period.semester}`;
    }
    
    // Load subjects
    updateSubjectsList(data.subjects);
}

// Update student information
function updateStudentInfo(student) {
    const elements = {
        'studentId': student.student_id,
        'studentName': `${student.first_name} ${student.last_name}`,
        'studentCourse': student.desired_course,
        'studentEmail': student.email_address,
        'studentPhone': student.mobile_no
    };
    
    Object.keys(elements).forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = elements[id];
        }
    });
}

// Update subjects list
function updateSubjectsList(subjects) {
    const subjectsContainer = document.getElementById('subjectsList');
    if (!subjectsContainer) return;
    
    subjectsContainer.innerHTML = '';
    
    subjects.forEach(subject => {
        const subjectElement = document.createElement('div');
        subjectElement.className = 'col-md-6 mb-3';
        subjectElement.innerHTML = `
            <div class="card subject-card h-100" onclick="toggleSubjectSelection(${subject.id})">
                <div class="card-body">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="subject_${subject.id}" 
                               onchange="handleSubjectChange(${subject.id})">
                        <label class="form-check-label w-100" for="subject_${subject.id}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">${subject.subject_code}</h6>
                                    <p class="mb-1 text-muted small">${subject.subject_name}</p>
                                    <small class="text-info">${subject.schedule}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-primary">${subject.units} units</span>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        `;
        subjectsContainer.appendChild(subjectElement);
    });
}

// Toggle subject selection
function toggleSubjectSelection(subjectId) {
    const checkbox = document.getElementById(`subject_${subjectId}`);
    if (checkbox) {
        checkbox.checked = !checkbox.checked;
        handleSubjectChange(subjectId);
    }
}

// Handle subject change
function handleSubjectChange(subjectId) {
    const checkbox = document.getElementById(`subject_${subjectId}`);
    const subject = availableSubjects.find(s => s.id == subjectId);
    
    if (!subject) return;
    
    if (checkbox.checked) {
        if (!selectedSubjects.includes(subjectId)) {
            selectedSubjects.push(subjectId);
        }
    } else {
        selectedSubjects = selectedSubjects.filter(id => id !== subjectId);
    }
    
    updateEnrollmentSummary();
}

// Update enrollment summary
function updateEnrollmentSummary() {
    const totalUnits = selectedSubjects.reduce((sum, subjectId) => {
        const subject = availableSubjects.find(s => s.id == subjectId);
        return sum + (subject ? parseFloat(subject.units) : 0);
    }, 0);
    
    const selectedCount = selectedSubjects.length;
    
    // Update summary display
    const summaryContainer = document.getElementById('enrollmentSummary');
    if (summaryContainer) {
        summaryContainer.innerHTML = `
            <div class="row text-center">
                <div class="col-4">
                    <h5 class="text-primary">${selectedCount}</h5>
                    <small class="text-muted">Subjects</small>
                </div>
                <div class="col-4">
                    <h5 class="text-success">${totalUnits}</h5>
                    <small class="text-muted">Total Units</small>
                </div>
                <div class="col-4">
                    <h5 class="text-info">₱${calculateAssessment(totalUnits).toLocaleString()}</h5>
                    <small class="text-muted">Assessment</small>
                </div>
            </div>
        `;
    }
    
    // Update selected subjects list
    updateSelectedSubjectsList();
    
    // Enable/disable submit button
    const submitButton = document.getElementById('submitEnrollment');
    if (submitButton) {
        submitButton.disabled = selectedCount === 0;
    }
}

// Update selected subjects list
function updateSelectedSubjectsList() {
    const container = document.getElementById('selectedSubjectsList');
    if (!container) return;
    
    container.innerHTML = '';
    
    selectedSubjects.forEach(subjectId => {
        const subject = availableSubjects.find(s => s.id == subjectId);
        if (!subject) return;
        
        const subjectElement = document.createElement('div');
        subjectElement.className = 'card mb-2';
        subjectElement.innerHTML = `
            <div class="card-body py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>${subject.subject_code}</strong> - ${subject.subject_name}
                        <br>
                        <small class="text-muted">${subject.schedule}</small>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-primary">${subject.units} units</span>
                        <button class="btn btn-sm btn-outline-danger ms-2" onclick="removeSubject(${subject.id})">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.appendChild(subjectElement);
    });
}

// Remove subject from selection
function removeSubject(subjectId) {
    const checkbox = document.getElementById(`subject_${subjectId}`);
    if (checkbox) {
        checkbox.checked = false;
    }
    selectedSubjects = selectedSubjects.filter(id => id !== subjectId);
    updateEnrollmentSummary();
}

// Calculate assessment (mock calculation)
function calculateAssessment(totalUnits) {
    const tuitionPerUnit = 350;
    const miscFees = 1000;
    return (totalUnits * tuitionPerUnit) + miscFees;
}

// Submit enrollment
function submitEnrollment() {
    if (!currentStudent) {
        showAlert('error', 'No student selected');
        return;
    }
    
    if (selectedSubjects.length === 0) {
        showAlert('error', 'Please select at least one subject');
        return;
    }
    
    const yearLevel = document.getElementById('yearLevel')?.value;
    if (!yearLevel) {
        showAlert('error', 'Please select a year level');
        return;
    }
    
    const sectionId = document.getElementById('section')?.value;
    if (!sectionId) {
        showAlert('error', 'Please select a section');
        return;
    }
    
    if (!confirm('Are you sure you want to enroll this student with the selected subjects and section?')) {
        return;
    }
    
    const submitButton = document.getElementById('submitEnrollment');
    if (submitButton) {
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Processing...';
    }
    
    const formData = new FormData();
    formData.append('action', 'enroll_student');
    formData.append('student_id', currentStudent.student_id);
    formData.append('year_level', yearLevel);
    formData.append('section_id', sectionId);
    formData.append('subject_ids', JSON.stringify(selectedSubjects));
    
    fetch('/enrollmentsystem/action/registrar/enrollment_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Student enrolled successfully');
            setTimeout(() => {
                window.location.href = `enrollment_success.php?student_id=${currentStudent.student_id}`;
            }, 2000);
        } else {
            showAlert('error', 'Enrollment failed: ' + data.message);
            resetSubmitButton();
        }
    })
    .catch(error => {
        console.error('Error enrolling student:', error);
        showAlert('error', 'Error enrolling student');
        resetSubmitButton();
    });
}

// Reset submit button
function resetSubmitButton() {
    const submitButton = document.getElementById('submitEnrollment');
    if (submitButton) {
        submitButton.disabled = selectedSubjects.length === 0;
        submitButton.innerHTML = '<i class="bi bi-person-check me-1"></i> Enroll Student';
    }
}

// Load subjects based on year level
function loadSubjectsByYearLevel() {
    const yearLevel = document.getElementById('yearLevel')?.value;
    if (!yearLevel || !currentStudent) return;
    
    const courseCode = determineCourseCode(currentStudent.desired_course);
    
    fetch(`/enrollmentsystem/action/registrar/general_handler.php?action=get_available_subjects&year_level=${yearLevel}&course=${courseCode}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                availableSubjects = data.data;
                selectedSubjects = [];
                updateSubjectsList(data.data);
                updateEnrollmentSummary();
            } else {
                showAlert('error', 'Failed to load subjects: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error loading subjects:', error);
            showAlert('error', 'Error loading subjects');
        });
}

// Load sections based on year level and course
function loadSectionsByYearLevel() {
    const yearLevel = document.getElementById('yearLevel')?.value;
    const sectionSelect = document.getElementById('section');
    
    if (!yearLevel || !currentStudent || !sectionSelect) {
        console.log('Missing required elements for section loading');
        return;
    }
    
    const courseCode = determineCourseCode(currentStudent.desired_course);
    console.log('Loading sections for:', yearLevel, courseCode);
    
    // Show loading state
    sectionSelect.disabled = true;
    sectionSelect.innerHTML = '<option value="">Loading sections...</option>';
    
    fetch(`/enrollmentsystem/action/registrar/general_handler.php?action=get_sections&year_level=${encodeURIComponent(yearLevel)}&course=${courseCode}`, {
        method: 'GET',
        credentials: 'include' // Include session cookies
    })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Sections API response:', data);
            if (data.success && data.data && data.data.length > 0) {
                sectionSelect.innerHTML = '<option value="">-- Select Section --</option>';
                data.data.forEach(section => {
                    const option = document.createElement('option');
                    option.value = section.id;
                    option.textContent = `${section.section_code} (Max: ${section.max_students} students)`;
                    sectionSelect.appendChild(option);
                });
                sectionSelect.disabled = false;
            } else {
                sectionSelect.innerHTML = '<option value="">No sections available</option>';
                console.warn('No sections found or API error:', data.message);
                showAlert('warning', data.message || 'No sections found for this year level and course');
            }
        })
        .catch(error => {
            console.error('Error loading sections:', error);
            sectionSelect.innerHTML = '<option value="">Error loading sections</option>';
            showAlert('error', 'Error loading sections');
        });
}

// Go back to student list
function goBackToStudentList() {
    window.location.href = 'enrollment.php';
}

// Utility functions
function determineCourseCode(desiredCourse) {
    if (desiredCourse.includes('BSIT')) return 'BSIT';
    if (desiredCourse.includes('BSCS')) return 'BSCS';
    if (desiredCourse.includes('BSIS')) return 'BSIS';
    return 'BSIT'; // Default
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
