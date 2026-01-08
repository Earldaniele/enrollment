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
                                <h2 class="fw-bold mb-1">QR Scanner</h2>
                                <p class="mb-0">Scan student QR codes to verify and process their queue</p>
                            </div>
                            <div>
                                <i class="bi bi-qr-code-scan text-white" style="font-size: 3rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- QR Scanner -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">QR Code Scanner</h5>
                    </div>
                    <div class="card-body">
                        <div id="reader" class="mb-3" style="width: 100%; min-height: 400px;"></div>
                        <div class="d-flex justify-content-between">
                            <button id="startButton" class="btn btn-primary">
                                <i class="bi bi-camera-video"></i> Start Scanner
                            </button>
                            <button id="stopButton" class="btn btn-danger" disabled>
                                <i class="bi bi-stop-circle"></i> Stop Scanner
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Upload QR Code</h5>
                    </div>
                    <div class="card-body">
                        <p>If the QR code can't be scanned directly, upload an image:</p>
                        <form id="qrUploadForm" enctype="multipart/form-data">
                            <div class="mb-3">
                                <input type="file" id="qrCodeImage" name="qr_file" class="form-control" accept="image/*">
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-upload"></i> Process QR Image
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Recently Scanned</h5>
                    </div>
                    <div class="card-body">
                        <div id="recentScans">
                            <p class="text-muted">No recent scans</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Student Details Modal -->
        <div class="modal fade" id="studentDetailsModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Student Details</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div id="studentDetails">
                            <!-- Student details will be populated here -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="verifyStudent" class="btn btn-success">Verify and Proceed</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- JavaScript libraries and scripts -->
    <script src="/enrollmentsystem/assets/js/html5-qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize variables
            let html5QrCode;
            let currentStudentId = null;
            const recentScansContainer = document.getElementById('recentScans');
            const scannedStudents = [];
            
            // Handle Start Scanner button
            document.getElementById('startButton').addEventListener('click', function() {
                html5QrCode = new Html5Qrcode("reader");
                const config = { fps: 10, qrbox: { width: 250, height: 250 } };
                
                html5QrCode.start(
                    { facingMode: "environment" }, 
                    config,
                    onScanSuccess,
                    onScanFailure
                ).then(() => {
                    document.getElementById('startButton').disabled = true;
                    document.getElementById('stopButton').disabled = false;
                }).catch(err => {
                    console.error('Error starting scanner:', err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Scanner Error',
                        text: 'Could not start the scanner. Please check camera permissions.',
                    });
                });
            });
            
            // Handle Stop Scanner button
            document.getElementById('stopButton').addEventListener('click', function() {
                if (html5QrCode) {
                    html5QrCode.stop().then(() => {
                        document.getElementById('startButton').disabled = false;
                        document.getElementById('stopButton').disabled = true;
                    }).catch(err => {
                        console.error('Error stopping scanner:', err);
                    });
                }
            });
            
            // QR Code scan success handler
            function onScanSuccess(decodedText, decodedResult) {
                // Play success sound
                const audio = new Audio('/enrollmentsystem/assets/sounds/beep.mp3');
                audio.play();
                
                // Process the scanned QR code
                processScannedQRCode(decodedText);
            }
            
            // QR Code scan failure handler
            function onScanFailure(error) {
                // Silent failure - no need to show errors for each failed frame
                // console.warn(`QR Code scanning failure: ${error}`);
            }
            
            // Handle QR code image upload
            document.getElementById('qrUploadForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const fileInput = document.getElementById('qrCodeImage');
                if (!fileInput.files || fileInput.files.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No File Selected',
                        text: 'Please select a QR code image to upload.',
                    });
                    return;
                }
                
                const formData = new FormData();
                formData.append('qr_file', fileInput.files[0]);
                
                // Show loading state
                Swal.fire({
                    title: 'Processing...',
                    text: 'Analyzing QR code image',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Send the image to the server for processing
                fetch('/enrollmentsystem/action/student-assistant/process_qr_upload.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    Swal.close();
                    
                    if (data.success) {
                        // Process the successful QR code scan result
                        const student = data.student;
                        const ticket = data.ticket;
                        
                        // Add to recent scans
                        addToRecentScans({
                            id: student.student_id,
                            name: student.student_name,
                            queueNumber: ticket.queue_number,
                            status: ticket.status,
                            department: ticket.department
                        });
                        
                        // Show student details
                        showStudentDetails({
                            id: student.student_id,
                            name: student.student_name,
                            course: 'Fetched from database',
                            year: 'Fetched from database',
                            contactNumber: 'Fetched from database',
                            email: student.student_id + '@student.ncst.edu.ph',
                            status: ticket.status,
                            queueNumber: ticket.queue_number,
                            department: ticket.department,
                            ticketId: ticket.id
                        });
                        
                        // Reset the file input
                        fileInput.value = '';
                    } else {
                        // Show error message
                        Swal.fire({
                            icon: 'error',
                            title: 'QR Code Processing Failed',
                            text: data.message || 'Could not process the QR code image.'
                        });
                    }
                })
                .catch(error => {
                    Swal.close();
                    console.error('Error processing QR code image:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Upload Error',
                        text: 'There was a problem processing the image. Please try again.'
                    });
                });
            });
            
            // Process QR code data from scanner
            function processScannedQRCode(qrData) {
                console.log('Processing QR data:', qrData);
                
                // Show loading state
                Swal.fire({
                    title: 'Processing...',
                    text: 'Verifying QR code',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Try to parse the QR data
                let parsedData;
                try {
                    // Check if it's a URL with query parameters (from a simplified QR code)
                    if (qrData.includes('?')) {
                        const url = new URL(qrData);
                        parsedData = {
                            student_id: url.searchParams.get('s'),
                            queue_id: url.searchParams.get('q'),
                            queue_number: url.searchParams.get('n')
                        };
                    } else if (qrData.startsWith('{') && qrData.endsWith('}')) {
                        // Try to parse as JSON
                        parsedData = JSON.parse(qrData);
                    } else {
                        // Treat as plain text (probably a student ID)
                        parsedData = { student_id: qrData };
                    }
                } catch (error) {
                    console.warn('Error parsing QR data:', error);
                    parsedData = { student_id: qrData };
                }
                
                // Send to server for processing
                fetch('/enrollmentsystem/action/student-assistant/process_qr_scan.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ qr_data: parsedData })
                })
                .then(response => response.json())
                .then(data => {
                    Swal.close();
                    
                    if (data.success) {
                        // Process the successful QR code scan result
                        const student = data.student;
                        const ticket = data.ticket;
                        
                        // Add to recent scans
                        addToRecentScans({
                            id: student.student_id,
                            name: student.student_name,
                            queueNumber: ticket.queue_number,
                            status: ticket.status,
                            department: ticket.department
                        });
                        
                        // Show student details
                        showStudentDetails({
                            id: student.student_id,
                            name: student.student_name,
                            course: 'Fetched from database',
                            year: 'Fetched from database',
                            contactNumber: 'Fetched from database',
                            email: student.student_id + '@student.ncst.edu.ph',
                            status: ticket.status,
                            queueNumber: ticket.queue_number,
                            department: ticket.department,
                            ticketId: ticket.id
                        });
                    } else {
                        // Show error message
                        Swal.fire({
                            icon: 'warning',
                            title: 'QR Code Error',
                            text: data.message || 'Could not process the QR code.'
                        });
                        
                        if (data.student) {
                            // Still show some information if available
                            console.log('Student found but not in queue:', data.student);
                        }
                    }
                })
                .catch(error => {
                    Swal.close();
                    console.error('Error processing QR code:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Processing Error',
                        text: 'There was a problem verifying the QR code. Please try again.'
                    });
                });
            }
            
            // Add scanned student to recent scans list
            function addToRecentScans(student) {
                // Add to the beginning of the array
                scannedStudents.unshift(student);
                
                // Keep only the last 5 scans
                if (scannedStudents.length > 5) {
                    scannedStudents.pop();
                }
                
                // Update the UI
                updateRecentScansUI();
            }
            
            // Update the recent scans UI
            function updateRecentScansUI() {
                if (scannedStudents.length === 0) {
                    recentScansContainer.innerHTML = '<p class="text-muted">No recent scans</p>';
                    return;
                }
                
                let html = '';
                scannedStudents.forEach(student => {
                    // Determine badge color based on status
                    let statusClass = 'bg-info';
                    if (student.status === 'verified' || student.status === 'in_progress') {
                        statusClass = 'bg-success';
                    } else if (student.status === 'expired') {
                        statusClass = 'bg-danger';
                    }
                    
                    html += `
                        <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
                            <div>
                                <strong>${student.name}</strong><br>
                                <small>${student.id}</small>
                            </div>
                            <span class="badge ${statusClass}">#${student.queueNumber}</span>
                        </div>
                    `;
                });
                
                recentScansContainer.innerHTML = html;
            }
            
            // Show student details in modal
            function showStudentDetails(student) {
                const detailsContainer = document.getElementById('studentDetails');
                
                // Format status for display
                let statusText = 'In Queue';
                let statusClass = 'info';
                
                switch(student.status) {
                    case 'waiting':
                        statusText = 'Waiting in Queue';
                        statusClass = 'info';
                        break;
                    case 'ready':
                        statusText = 'Ready to Proceed';
                        statusClass = 'warning';
                        break;
                    case 'in_progress':
                        statusText = 'Currently Being Served';
                        statusClass = 'success';
                        break;
                    case 'completed':
                        statusText = 'Service Completed';
                        statusClass = 'primary';
                        break;
                    case 'expired':
                        statusText = 'Queue Expired';
                        statusClass = 'danger';
                        break;
                    case 'cancelled':
                        statusText = 'Queue Cancelled';
                        statusClass = 'secondary';
                        break;
                }
                
                detailsContainer.innerHTML = `
                    <div class="row">
                        <div class="col-md-8">
                            <h4>${student.name}</h4>
                            <p class="text-muted mb-2">${student.id}</p>
                            <div class="mb-3">
                                <strong>Course:</strong> ${student.course}<br>
                                <strong>Year Level:</strong> ${student.year}<br>
                                <strong>Contact:</strong> ${student.contactNumber}<br>
                                <strong>Email:</strong> ${student.email}
                            </div>
                            <div class="alert alert-${statusClass}">
                                <strong>Queue Status:</strong> ${statusText}<br>
                                <strong>Queue Number:</strong> #${student.queueNumber}<br>
                                <strong>Department:</strong> ${student.department || 'N/A'}
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="border rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; background-color: #f8f9fa;">
                                <i class="bi bi-person" style="font-size: 3rem;"></i>
                            </div>
                            <div class="mt-2">
                                <span class="badge bg-primary">${student.course}</span>
                            </div>
                        </div>
                    </div>
                `;
                
                // Show the modal
                const modal = new bootstrap.Modal(document.getElementById('studentDetailsModal'));
                modal.show();
                
                // Handle verify button - only enable for valid queue statuses
                const verifyButton = document.getElementById('verifyStudent');
                
                if (student.status === 'waiting' || student.status === 'ready') {
                    verifyButton.disabled = false;
                    verifyButton.onclick = function() {
                        verifyAndProcessStudent(student);
                        modal.hide();
                    };
                } else {
                    verifyButton.disabled = true;
                }
            }
            
            // Verify and process student in queue
            function verifyAndProcessStudent(student) {
                console.log('Verifying and processing student:', student);
                
                // Show loading state
                Swal.fire({
                    title: 'Processing...',
                    text: 'Updating queue status',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Call the API to verify the queue ticket
                fetch('/enrollmentsystem/action/student-assistant/verify_queue.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        student_id: student.id,
                        ticket_id: student.ticketId,
                        queue_number: student.queueNumber,
                        action: 'verify'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    Swal.close();
                    
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Student Verified',
                            text: `${student.name} (Queue #${student.queueNumber}) has been verified and can proceed.`,
                        });
                        
                        // Update the student's status in the recent scans list
                        const index = scannedStudents.findIndex(s => s.id === student.id);
                        if (index !== -1) {
                            scannedStudents[index].status = 'verified';
                            updateRecentScansUI();
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Verification Failed',
                            text: data.message || 'Could not verify the student queue ticket.',
                        });
                    }
                })
                .catch(error => {
                    Swal.close();
                    console.error('Error verifying student:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Processing Error',
                        text: 'There was a problem verifying the student. Please try again.'
                    });
                });
            }
        });
    </script>
</body>
</html>