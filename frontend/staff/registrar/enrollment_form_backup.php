<?php
// Start session and require authentication
session_start();
require_once '../../includes/registrar_auth.php';
requireRegistrarAuth();

// Get current registrar info
$registrar = getCurrentRegistrar();

// Get student ID from URL parameter
$studentId = isset($_GET['student_id']) ? $_GET['student_id'] : '';

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
                                <h2 class="dashboard-title mb-2">Student Enrollment Form</h2>
                                <p class="text-muted mb-0">Assign subjects to officially enroll the student</p>
                            </div>
                            <button type="button" class="btn btn-outline-primary" onclick="goBackToStudentList()">
                                <i class="fas fa-arrow-left me-2"></i>Back to List
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Information -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Student Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Student ID:</strong> <span id="studentId">Loading...</span></p>
                                <p><strong>Name:</strong> <span id="studentName">Loading...</span></p>
                                <p><strong>Course:</strong> <span id="studentCourse">Loading...</span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Email:</strong> <span id="studentEmail">Loading...</span></p>
                                <p><strong>Phone:</strong> <span id="studentPhone">Loading...</span></p>
                                <p><strong>Academic Period:</strong> <span id="academicPeriod">Loading...</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enrollment Form -->
        <form id="enrollmentForm">
            <!-- Year Level Selection -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card dashboard-card">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Year Level Assignment</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="yearLevel" class="form-label">Select Year Level</label>
                                    <select class="form-select" id="yearLevel" onchange="loadSubjectsByYearLevel(); loadSectionsByYearLevel();">
                                        <option value="">-- Select Year Level --</option>
                                        <option value="1st Year">1st Year</option>
                                        <option value="2nd Year">2nd Year</option>
                                        <option value="3rd Year">3rd Year</option>
                                        <option value="4th Year">4th Year</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="section" class="form-label">Select Section</label>
                                    <select class="form-select" id="section">
                                        <option value="">-- Select Year Level First --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subject Selection -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="card dashboard-card">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Subject Selection</h5>
                        </div>
                        <div class="card-body">
                            <div class="row" id="subjectsList">
                                <div class="col-12">
                                    <p class="text-muted text-center">Please select a year level to view available subjects.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card dashboard-card">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Enrollment Summary</h5>
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <h2 id="selectedSubjectsCount" class="text-primary">0</h2>
                                <p class="text-muted mb-0">Subjects</p>
                            </div>
                            <div class="mb-3">
                                <h2 id="totalUnitsCount" class="text-info">0</h2>
                                <p class="text-muted mb-0">Total Units</p>
                            </div>
                            <div class="mb-3">
                                <h2 id="assessmentTotal" class="text-success">₱0</h2>
                                <p class="text-muted mb-0">Assessment</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row">
                <div class="col-12">
                    <div class="card dashboard-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-secondary" onclick="goBackToStudentList()">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </button>
                                <button type="button" class="btn btn-primary" id="submitEnrollmentBtn" onclick="submitEnrollment()">
                                    <i class="fas fa-check me-2"></i>Enroll Student
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Include Enrollment Form JavaScript -->
    <script>
        // Enrollment Form JavaScript Functions
        console.log('Enrollment form JavaScript loaded successfully');

        let currentStudent = null;
        let availableSubjects = [];
        let selectedSubjects = [];

        // Load sections based on year level and course
        function loadSectionsByYearLevel() {
            const yearLevel = document.getElementById('yearLevel').value;
            const sectionSelect = document.getElementById('section');
            
            if (!yearLevel || !currentStudent || !sectionSelect) {
                console.log('Missing required elements for section loading');
                return;
            }
            
            const courseCode = determineCourseCode(currentStudent.desired_course);
            console.log('Loading sections for year level:', yearLevel, 'course code:', courseCode);
            
            // Show loading state
            sectionSelect.disabled = true;
            sectionSelect.innerHTML = '<option value="">Loading sections...</option>';
            
            const url = '/enrollmentsystem/action/registrar/general_handler.php?action=get_sections&year_level=' + encodeURIComponent(yearLevel) + '&course=' + courseCode;
            
            fetch(url, {
                method: 'GET',
                credentials: 'include'
            })
                .then(response => response.json())
                .then(data => {
                    console.log('Sections API response:', data);
                    if (data.success && data.data && data.data.length > 0) {
                        sectionSelect.innerHTML = '<option value="">-- Select Section --</option>';
                        data.data.forEach(function(section) {
                            const option = document.createElement('option');
                            option.value = section.id;
                            option.textContent = section.section_code + ' (Max: ' + section.max_students + ' students)';
                            sectionSelect.appendChild(option);
                        });
                        sectionSelect.disabled = false;
                        console.log('Successfully loaded', data.data.length, 'sections');
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

        // Determine course code from desired course
        function determineCourseCode(desiredCourse) {
            console.log('Original course:', desiredCourse);
            
            // Comprehensive course mapping
            const courseMap = {
                'Bachelor of Science in Computer Science (BSCS)': 'BSCS',
                'Bachelor of Science in Information Technology (BSIT)': 'BSIT',
                'Bachelor of Science in Computer Engineering (BSCpE)': 'BSCpE',
                'Bachelor of Science in Electronics Engineering (BSEE)': 'BSEE',
                'Bachelor of Science in Electrical Engineering (BSEE)': 'BSEE',
                'Bachelor of Science in Mechanical Engineering (BSME)': 'BSME',
                'Bachelor of Science in Civil Engineering (BSCE)': 'BSCE',
                'Bachelor of Science in Industrial Engineering (BSIE)': 'BSIE',
                'Bachelor of Science in Chemical Engineering (BSChE)': 'BSChE',
                'Bachelor of Science in Architecture (BSArch)': 'BSArch',
                'Bachelor of Science in Business Administration (BSBA)': 'BSBA',
                'Bachelor of Science in Accountancy (BSA)': 'BSA',
                'Bachelor of Science in Marketing Management (BSMM)': 'BSMM',
                'Bachelor of Science in Financial Management (BSFM)': 'BSFM',
                'Bachelor of Science in Human Resource Management (BSHRM)': 'BSHRM',
                'Bachelor of Science in Entrepreneurship (BSE)': 'BSE',
                'Bachelor of Science in Hospitality Management (BSHM)': 'BSHM',
                'Bachelor of Science in Tourism Management (BSTM)': 'BSTM',
                'Bachelor of Elementary Education (BEEd)': 'BEEd',
                'Bachelor of Secondary Education (BSEd)': 'BSEd',
                'Bachelor of Science in Nursing (BSN)': 'BSN',
                'Bachelor of Science in Medical Technology (BSMT)': 'BSMT',
                'Bachelor of Science in Physical Therapy (BSPT)': 'BSPT',
                'Bachelor of Science in Psychology (BS Psychology)': 'BS Psychology',
                'Bachelor of Science in Criminology (BS Criminology)': 'BS Criminology',
                'Bachelor of Science in Mathematics (BS Mathematics)': 'BS Mathematics',
                'Bachelor of Science in Biology (BS Biology)': 'BS Biology',
                'BSCS': 'BSCS',
                'BSIT': 'BSIT',
                'BSCpE': 'BSCpE'
            };
            
            // Check for exact match first
            if (courseMap[desiredCourse]) {
                console.log('Exact match found:', courseMap[desiredCourse]);
                return courseMap[desiredCourse];
            }
            
            // Check for partial matches as fallback
            if (desiredCourse.includes('Computer Science')) {
                return 'BSCS';
            }
            if (desiredCourse.includes('Information Technology')) {
                return 'BSIT';
            }
            
            console.log('No match found, returning original:', desiredCourse);
            return desiredCourse;
        }

        // Initialize enrollment form
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM Content Loaded - initializing enrollment form');
            const urlParams = new URLSearchParams(window.location.search);
            const studentId = urlParams.get('student_id');
            
            console.log('Student ID from URL:', studentId);
            
            if (studentId) {
                loadEnrollmentFormData(studentId);
            } else {
                console.error('No student_id found in URL parameters');
                showAlert('error', 'No student ID provided');
            }
        });

        // Load enrollment form data
        function loadEnrollmentFormData(studentId) {
            console.log('Loading enrollment form data for student:', studentId);
            fetch('/enrollmentsystem/action/registrar/general_handler.php?action=get_enrollment_form_data&student_id=' + studentId)
                .then(response => response.json())
                .then(data => {
                    console.log('Enrollment form data response:', data);
                    if (data.success) {
                        currentStudent = data.student;
                        updateEnrollmentFormUI(data);
                    } else {
                        console.error('Failed to load enrollment form data:', data.message);
                        showAlert('error', 'Failed to load student data: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error loading enrollment form data:', error);
                    showAlert('error', 'Error loading enrollment form data');
                });
        }

        // Update enrollment form UI
        function updateEnrollmentFormUI(data) {
            console.log('Updating enrollment form UI with data:', data);
            
            // Update student information
            updateStudentInfo(data.student);
            
            // Set suggested year level
            const yearLevelSelect = document.getElementById('yearLevel');
            if (yearLevelSelect) {
                yearLevelSelect.value = data.suggested_year_level;
                console.log('Set year level to:', data.suggested_year_level);
            }
            
            // Update academic period display
            const academicPeriodElement = document.getElementById('academicPeriod');
            if (academicPeriodElement) {
                academicPeriodElement.textContent = data.academic_period.school_year + ' - ' + data.academic_period.semester;
            }
            
            // Load subjects for the suggested year level
            console.log('Loading subjects and sections...');
            loadSubjectsByYearLevel();
            loadSectionsByYearLevel();
        }

        // Update student information display
        function updateStudentInfo(student) {
            console.log('Updating student info with:', student);
            
            const elements = {
                'studentName': student.last_name + ', ' + student.first_name + ' ' + (student.middle_name || ''),
                'studentId': student.student_id,
                'studentCourse': student.desired_course,
                'studentEmail': student.email_address,
                'studentPhone': student.mobile_no
            };
            
            console.log('Student info elements:', elements);
            
            for (const elementId in elements) {
                const element = document.getElementById(elementId);
                if (element) {
                    element.textContent = elements[elementId];
                    console.log('Updated ' + elementId + ' with: ' + elements[elementId]);
                } else {
                    console.warn('Element ' + elementId + ' not found');
                }
            }
        }

        // Load subjects based on year level
        function loadSubjectsByYearLevel() {
            const yearLevel = document.getElementById('yearLevel').value;
            if (!yearLevel || !currentStudent) {
                console.log('Cannot load subjects - yearLevel:', yearLevel, 'currentStudent:', currentStudent);
                return;
            }
            
            const courseCode = determineCourseCode(currentStudent.desired_course);
            console.log('Loading subjects for year level:', yearLevel, 'course:', courseCode);
            
            fetch('/enrollmentsystem/action/registrar/general_handler.php?action=get_available_subjects&year_level=' + encodeURIComponent(yearLevel) + '&course=' + encodeURIComponent(courseCode))
                .then(response => {
                    console.log('Subjects API response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Subjects API response data:', data);
                    if (data.success) {
                        availableSubjects = data.data || [];
                        console.log('Available subjects loaded:', availableSubjects.length, 'subjects');
                        updateSubjectsTable();
                    } else {
                        console.error('Failed to load subjects:', data.message);
                        availableSubjects = [];
                        updateSubjectsTable();
                        showAlert('error', 'Failed to load subjects: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error loading subjects:', error);
                    availableSubjects = [];
                    updateSubjectsTable();
                    showAlert('error', 'Error loading subjects');
                });
        }

        // Update subjects table
        function updateSubjectsTable() {
            const subjectsList = document.getElementById('subjectsList');
            if (!subjectsList) {
                console.error('subjectsList element not found');
                return;
            }
            
            console.log('Updating subjects list with', availableSubjects.length, 'subjects');
            subjectsList.innerHTML = '';
            
            if (availableSubjects.length === 0) {
                subjectsList.innerHTML = '<div class="col-12"><p class="text-muted text-center">No subjects available for this year level and course.</p></div>';
                return;
            }
            
            availableSubjects.forEach(function(subject) {
                const subjectCard = document.createElement('div');
                subjectCard.className = 'col-md-6 mb-3';
                
                const subjectId = subject.id || '';
                const subjectCode = subject.subject_code || 'N/A';
                const subjectName = subject.subject_name || 'No name';
                const subjectUnits = subject.units || 0;
                const subjectType = subject.type || 'Regular';
                const subjectSchedule = subject.schedule || 'TBA';
                
                subjectCard.innerHTML = 
                    '<div class="card subject-card">' +
                        '<div class="card-body">' +
                            '<div class="form-check">' +
                                '<input class="form-check-input" type="checkbox" value="' + subjectId + '" ' +
                                       'id="subject_' + subjectId + '" onchange="toggleSubjectSelection(this)">' +
                                '<label class="form-check-label" for="subject_' + subjectId + '">' +
                                    '<strong>' + subjectCode + '</strong>' +
                                    '<br>' +
                                    '<small class="text-muted">' + subjectName + '</small>' +
                                    '<br>' +
                                    '<span class="badge bg-primary">' + subjectUnits + ' units</span>' +
                                    '<span class="badge bg-info">' + subjectType + '</span>' +
                                '</label>' +
                            '</div>' +
                            '<small class="text-muted d-block mt-1">Schedule: ' + subjectSchedule + '</small>' +
                        '</div>' +
                    '</div>';
                
                subjectsList.appendChild(subjectCard);
            });
        }

        // Toggle subject selection
        function toggleSubjectSelection(checkbox) {
            const subjectId = parseInt(checkbox.value);
            const isChecked = checkbox.checked;
            
            if (isChecked) {
                if (!selectedSubjects.includes(subjectId)) {
                    selectedSubjects.push(subjectId);
                }
            } else {
                const index = selectedSubjects.indexOf(subjectId);
                if (index > -1) {
                    selectedSubjects.splice(index, 1);
                }
            }
            
            updateSelectedSubjectsCount();
        }

        // Update selected subjects count
        function updateSelectedSubjectsCount() {
            const countElement = document.getElementById('selectedSubjectsCount');
            if (countElement) {
                countElement.textContent = selectedSubjects.length;
            }
        }

        // Submit enrollment
        function submitEnrollment() {
            if (!currentStudent) {
                showAlert('error', 'Student information not loaded');
                return;
            }
            
            const yearLevel = document.getElementById('yearLevel').value;
            const sectionId = document.getElementById('section').value;
            
            if (!yearLevel) {
                showAlert('error', 'Please select a year level');
                return;
            }
            
            if (!sectionId) {
                showAlert('error', 'Please select a section');
                return;
            }
            
            if (selectedSubjects.length === 0) {
                showAlert('error', 'Please select at least one subject');
                return;
            }
            
            const enrollmentData = {
                student_id: currentStudent.student_id,
                year_level: yearLevel,
                section_id: sectionId,
                subjects: selectedSubjects
            };
            
            // Show loading state
            const submitButton = document.getElementById('submitEnrollmentBtn');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
            }
            
            fetch('/enrollmentsystem/action/registrar/enrollment_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(enrollmentData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', 'Student enrolled successfully!');
                        setTimeout(function() {
                            goBackToStudentList();
                        }, 2000);
                    } else {
                        showAlert('error', 'Failed to enroll student: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error submitting enrollment:', error);
                    showAlert('error', 'Error submitting enrollment');
                })
                .finally(function() {
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.innerHTML = '<i class="fas fa-check me-2"></i>Enroll Student';
                    }
                });
        }

        // Go back to student list
        function goBackToStudentList() {
            window.location.href = 'enrollment.php';
        }

        // Show alert
        function showAlert(type, message) {
            // Create alert element
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-' + (type === 'error' ? 'danger' : type) + ' alert-dismissible fade show';
            alertDiv.innerHTML = 
                message +
                '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
            
            // Insert at top of container
            const container = document.querySelector('.container');
            if (container) {
                container.insertBefore(alertDiv, container.firstChild);
                
                // Auto-dismiss after 5 seconds
                setTimeout(function() {
                    alertDiv.remove();
                }, 5000);
            }
        }
    </script>
</body>
</html>
