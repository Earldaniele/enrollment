<?php
// Start session and require authentication
session_start();

// Temporary bypass authentication for development
if (!isset($_SESSION['student_assistant_id'])) {
    // Set session variables for development
    $_SESSION['student_assistant_id'] = 'SA001';
    $_SESSION['student_assistant_name'] = 'Student Assistant';
    $_SESSION['student_assistant_department'] = 'Main Office';
}
?>

<script>
    // Function to verify queue ticket with server
    function verifyQueueTicket(studentId, queueId) {
        const url = queueId ? 
            `/enrollmentsystem/action/student-assistant/verify_queue.php?student_id=${encodeURIComponent(studentId)}&queue_id=${encodeURIComponent(queueId)}` :
            `/enrollmentsystem/action/student-assistant/verify_queue.php?student_id=${encodeURIComponent(studentId)}`;
        
        return fetch(url)
            .then(response => response.text()) // Get as text first to debug
            .then(text => {
                console.log("Raw server response:", text);
                
                try {
                    // Try to parse as JSON
                    return JSON.parse(text);
                } catch (e) {
                    console.error("Invalid JSON response:", text);
                    console.error("JSON parse error:", e);
                    
                    // If it looks like HTML (probably an error page)
                    if (text.includes("<!DOCTYPE html>") || text.includes("<html>")) {
                        throw new Error("Server returned HTML instead of JSON. Check server configuration.");
                    }
                    
                    throw new Error("Server returned an invalid JSON response. See console for details.");
                }
            })
            .then(data => {
                // Handle the response data
                if (data.success) {
                    // If student found with ticket, update the UI
                    if (data.ticket) {
                        displayTicketDetails(data.ticket, data.student);
                        updateActionButtons(data.ticket.status);
                        showScanSuccess("Queue ticket found");
                    } else {
                        // Student found but no active ticket
                        showScanError(data.message || "No active queue ticket found");
                        clearTicketDetails();
                    }
                } else {
                    // Error response
                    showScanError(data.message || "Error verifying ticket");
                    clearTicketDetails();
                }
                return data;
            })
            .catch(error => {
                console.error("Error verifying queue ticket:", error);
                showScanError(error.message || "Network error while verifying ticket");
                clearTicketDetails();
                throw error;
            });
    }            // Declaration for the current ticket variable
            let currentTicket = null;
            
            // Function to update action buttons based on ticket status
            function updateActionButtons(status) {
                // Get all action buttons
                const callStudentBtn = document.getElementById('callStudentBtn');
                const startProcessingBtn = document.getElementById('startProcessingBtn');
                const completeBtn = document.getElementById('completeBtn');
                const noShowBtn = document.getElementById('noShowBtn');
                
                if (!callStudentBtn || !startProcessingBtn || !completeBtn || !noShowBtn) {
                    console.error("One or more action buttons not found");
                    return;
                }
                
                // Reset all buttons first
                callStudentBtn.disabled = true;
                startProcessingBtn.disabled = true;
                completeBtn.disabled = true;
                noShowBtn.disabled = true;
                
                // Enable appropriate buttons based on ticket status
                switch(status) {
                    case 'waiting':
                        callStudentBtn.disabled = false;
                        noShowBtn.disabled = false;
                        break;
                    case 'ready':
                        startProcessingBtn.disabled = false;
                        noShowBtn.disabled = false;
                        break;
                    case 'in_progress':
                        completeBtn.disabled = false;
                        break;
                    // No buttons enabled for completed, no_show, or cancelled statuses
                }
            }
    
    // Function to display ticket details in the UI
    function displayTicketDetails(ticket, student) {
        // Show result section and hide empty state
        document.getElementById('scannedResult').classList.remove('d-none');
        document.getElementById('emptyResult').classList.add('d-none');
        
        // Update UI with ticket details
        document.getElementById('studentId').textContent = ticket.student_id;
        document.getElementById('studentName').textContent = student ? student.student_name : 'Unknown';
        document.getElementById('queueNumber').textContent = ticket.queue_number;
        document.getElementById('queueStatus').textContent = formatStatus(ticket.status);
        document.getElementById('department').textContent = ticket.department;
        
        // Store the current ticket ID for actions
        document.getElementById('currentTicketId').value = ticket.id;
    }
    
    // Function to clear ticket details from the UI
    function clearTicketDetails() {
        document.getElementById('scannedResult').classList.add('d-none');
        document.getElementById('emptyResult').classList.remove('d-none');
        document.getElementById('currentTicketId').value = '';
    }
    
    // Function to show scan success message
    function showScanSuccess(message) {
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: message,
            timer: 2000,
            showConfirmButton: false
        });
    }
    
    // Function to show scan error message
    function showScanError(message) {
        Swal.fire({
            icon: 'error',
            title: 'Scan Error',
            text: message
        });
    }
</script>

<?php
// Set student assistant email for development
if (!isset($_SESSION['student_assistant_email'])) {
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
                                <h2 class="fw-bold mb-1">Welcome, <?php echo htmlspecialchars($studentAssistant['name']); ?></h2>
                                <p class="mb-0">NCST Enrollment System - Student Assistant Dashboard</p>
                            </div>
                            <div>
                                <i class="bi bi-people-fill text-white" style="font-size: 3rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Queue Statistics -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Queue Overview</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <i class="bi bi-hourglass-split text-primary" style="font-size: 2rem;"></i>
                                    <h3 class="mt-2 mb-1" id="waitingCount">0</h3>
                                    <p class="text-muted mb-0">Waiting</p>
                                </div>
                            </div>
                                                         <div class="col-md-3">
                                 <div class="text-center">
                                     <i class="bi bi-person-check text-success" style="font-size: 2rem;"></i>
                                     <h3 class="mt-2 mb-1" id="readyCount">0</h3>
                                     <p class="text-muted mb-0">Ready</p>
                                 </div>
                             </div>
                                                         <div class="col-md-3">
                                 <div class="text-center">
                                     <i class="bi bi-person-gear text-warning" style="font-size: 2rem;"></i>
                                     <h3 class="mt-2 mb-1" id="inProgressCount">0</h3>
                                     <p class="text-muted mb-0">In Progress</p>
                                 </div>
                             </div>
                             <div class="col-md-3">
                                 <div class="text-center">
                                     <i class="bi bi-check-circle text-info" style="font-size: 2rem;"></i>
                                     <h3 class="mt-2 mb-1" id="completedCount">0</h3>
                                     <p class="text-muted mb-0">Completed Today</p>
                                 </div>
                             </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <i class="bi bi-x-circle text-danger" style="font-size: 2rem;"></i>
                                    <h3 class="mt-2 mb-1" id="noShowCount">0</h3>
                                    <p class="text-muted mb-0">No Shows</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- QR Scanner Section -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card dashboard-card h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">QR Code Scanner</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <i class="bi bi-qr-code-scan mb-3" style="font-size: 3rem; color: #6c757d;"></i>
                            <p>Scan student QR codes to verify their queue status</p>
                        </div>
                        <div id="qr-reader" class="mb-3" style="width: 100%;"></div>
                        <div class="d-grid gap-2">
                            <button id="startScanBtn" class="btn btn-primary mb-2">
                                <i class="bi bi-camera me-2"></i>Start Scanning
                            </button>
                            
                                                         <!-- File Upload for QR Code -->
                             <div class="mb-3">
                                 <label class="form-label">Upload QR Code Image</label>
                                 <div class="input-group">
                                     <input type="file" id="qrFileInput" class="form-control" accept="image/*" />
                                     <button id="uploadQrBtn" class="btn btn-success">
                                         <i class="bi bi-upload me-2"></i>Process
                                     </button>
                                 </div>
                                 <small class="text-muted">Upload a QR code image for direct scanning</small>
                                 <div class="form-text mt-1">
                                    For demo purposes: name images with student ID (e.g., student-2025-00001.jpg)
                                 </div>
                             </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card dashboard-card h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Scanned Student Information</h5>
                    </div>
                    <div class="card-body">
                        <div id="scannedResult" class="d-none">
                            <div class="mb-3">
                                <div class="alert alert-success">
                                    <i class="bi bi-check-circle-fill me-2"></i>
                                    <span id="scanStatus">QR Code successfully scanned!</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <strong>Student ID:</strong>
                                <span id="studentId"></span>
                            </div>
                            <div class="mb-3">
                                <strong>Student Name:</strong>
                                <span id="studentName"></span>
                            </div>
                            <div class="mb-3">
                                <strong>Queue Number:</strong>
                                <span id="queueNumber"></span>
                            </div>
                            <div class="mb-3">
                                <strong>Queue Status:</strong>
                                <span id="queueStatus" class="badge bg-primary"></span>
                            </div>
                            <div class="mb-3">
                                <strong>Department:</strong>
                                <span id="department"></span>
                            </div>
                            <input type="hidden" id="currentTicketId" value="">
                            <div class="d-grid gap-2">
                                                                 <button id="callStudentBtn" class="btn btn-success">
                                     <i class="bi bi-megaphone me-2"></i>Call Student
                                 </button>
                                 <button id="startProcessingBtn" class="btn btn-warning">
                                     <i class="bi bi-play-circle me-2"></i>Start Processing
                                 </button>
                                 <button id="completeBtn" class="btn btn-primary">
                                     <i class="bi bi-check-circle me-2"></i>Mark as Completed
                                 </button>
                                 <button id="noShowBtn" class="btn btn-danger">
                                     <i class="bi bi-x-circle me-2"></i>Mark as No Show
                                 </button>
                            </div>
                        </div>
                        <div id="emptyResult" class="text-center py-5">
                            <i class="bi bi-person-badge text-muted mb-3" style="font-size: 3rem;"></i>
                            <p>No student scanned yet. Scan a QR code to see student information.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Queue List -->
        <div class="row">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Current Queue</h5>
                        <button id="refreshQueueBtn" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Queue #</th>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th>Time in Queue</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="queueTableBody">
                                    <!-- Queue items will be populated dynamically -->
                                </tbody>
                            </table>
                        </div>
                        <div id="emptyQueue" class="text-center py-4 d-none">
                            <i class="bi bi-emoji-smile text-muted mb-3" style="font-size: 2rem;"></i>
                            <p>No students currently in queue.</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Code Scanner Modal -->
    <div class="modal fade" id="qrScannerModal" tabindex="-1" aria-labelledby="qrScannerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrScannerModalLabel">Scan Student QR Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="qr-reader-modal" style="width: 100%"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Include JS Libraries -->
    <script src="/enrollmentsystem/assets/js/html5-qrcode.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize variables
            let currentTicket = null;
            const department = "<?php echo htmlspecialchars($studentAssistant['department']); ?>";
            
            // Get elements
            const startScanBtn = document.getElementById('startScanBtn');
            const scannedResult = document.getElementById('scannedResult');
            const emptyResult = document.getElementById('emptyResult');
            const studentIdSpan = document.getElementById('studentId');
            const studentNameSpan = document.getElementById('studentName');
            const queueNumberSpan = document.getElementById('queueNumber');
            const queueStatusSpan = document.getElementById('queueStatus');
            const departmentSpan = document.getElementById('department');
                         const callStudentBtn = document.getElementById('callStudentBtn');
             const startProcessingBtn = document.getElementById('startProcessingBtn');
             const completeBtn = document.getElementById('completeBtn');
             const noShowBtn = document.getElementById('noShowBtn');
            const refreshQueueBtn = document.getElementById('refreshQueueBtn');
            const queueTableBody = document.getElementById('queueTableBody');
            const emptyQueue = document.getElementById('emptyQueue');
                         const manualEntryBtn = document.getElementById('manualEntryBtn');
             const qrFileInput = document.getElementById('qrFileInput');
             const uploadQrBtn = document.getElementById('uploadQrBtn');
            
            // Helper functions for status formatting
            function formatStatus(status) {
                switch(status) {
                    case 'waiting': return 'Waiting';
                    case 'ready': return 'Ready';
                    case 'in_progress': return 'In Progress';
                    case 'completed': return 'Completed';
                    case 'no_show': return 'No Show';
                    case 'cancelled': return 'Cancelled';
                    default: return status.charAt(0).toUpperCase() + status.slice(1).replace('_', ' ');
                }
            }
            
            function getStatusBadgeClass(status) {
                switch(status) {
                    case 'waiting': return 'bg-secondary';
                    case 'ready': return 'bg-success';
                    case 'in_progress': return 'bg-warning text-dark';
                    case 'completed': return 'bg-info';
                    case 'no_show': return 'bg-danger';
                    case 'cancelled': return 'bg-dark';
                    default: return 'bg-secondary';
                }
            }
            
            // Load queue on page load
            loadQueueData();
            
            // HTML5 QR code scanner setup
            const html5QrCode = new Html5Qrcode("qr-reader");
            let cameraId;
            
            // Get available cameras
            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length) {
                    cameraId = devices[0].id;
                    console.log('Camera found:', cameraId);
                } else {
                    console.log('No cameras found');
                }
            }).catch(err => {
                console.error('Error getting cameras', err);
                // For demo purposes, we can still use the file upload feature
            });
            
            // Start scanning
            startScanBtn.addEventListener('click', function() {
                if (cameraId) {
                    html5QrCode.start(
                        { deviceId: cameraId, facingMode: "environment" }, 
                        {
                            fps: 10,
                            qrbox: 250
                        },
                        onScanSuccess,
                        onScanFailure
                    ).catch(err => {
                        console.error("Error starting camera:", err);
                        
                        // More specific error message based on error type
                        let errorMessage = "Could not start camera. ";
                        
                        if (err.name === "NotReadableError") {
                            errorMessage += "The camera is in use by another application or not accessible.";
                        } else if (err.name === "NotAllowedError") {
                            errorMessage += "Camera access permission was denied.";
                        } else if (err.name === "NotFoundError") {
                            errorMessage += "No camera device was found.";
                        } else {
                            errorMessage += "Please check camera permissions.";
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Camera Error',
                            text: errorMessage,
                            footer: 'Try using the QR code image upload instead'
                        });
                    });
                    
                    this.disabled = true;
                    this.innerHTML = '<i class="bi bi-camera-video me-2"></i>Scanning...';
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'No Camera Found',
                        text: 'No camera device is available. Please check your device permissions.',
                        footer: 'Try using the QR code image upload instead'
                    });
                }
            });
            
            // QR code scan success handler
            function onScanSuccess(decodedText, decodedResult) {
                // Stop scanning after successful scan
                html5QrCode.stop().then(() => {
                    startScanBtn.disabled = false;
                    startScanBtn.innerHTML = '<i class="bi bi-camera me-2"></i>Start Scanning';
                    
                    try {
                        // Try to parse as JSON first
                        let qrData;
                        try {
                            qrData = JSON.parse(decodedText);
                        } catch (e) {
                            // If not JSON, check if it's a URL format
                            if (decodedText.includes('s=')) {
                                // URL format: extract student_id and queue_id
                                const sMatch = decodedText.match(/s=([^&]+)/);
                                const qMatch = decodedText.match(/q=([^&]+)/);
                                
                                qrData = { 
                                    student_id: sMatch ? sMatch[1] : null,
                                    queue_id: qMatch ? qMatch[1] : null
                                };
                            } else {
                                // Treat as plain text (assume it's a student ID)
                                qrData = { student_id: decodedText };
                            }
                        }
                        
                        if (qrData.student_id) {
                            verifyQueueTicket(qrData.student_id, qrData.queue_id);
                        } else {
                            showScanError("Invalid QR code format - couldn't extract student ID");
                        }
                    } catch (e) {
                        console.error("Scan processing error:", e);
                        showScanError("Could not process scanned data: " + e.message);
                    }
                }).catch(err => {
                    console.error("Error stopping camera:", err);
                    startScanBtn.disabled = false;
                    startScanBtn.innerHTML = '<i class="bi bi-camera me-2"></i>Start Scanning';
                });
            }
            
            // QR code scan failure handler
            function onScanFailure(error) {
                // Handle scan failure - Just log it, no need to show to user
                console.warn(`QR code scan error: ${error}`);
            }
            
            // Function to verify queue ticket with server
            function verifyQueueTicket(studentId, queueId) {
                const url = queueId ? 
                    `/enrollmentsystem/action/student-assistant/verify_queue.php?student_id=${encodeURIComponent(studentId)}&queue_id=${encodeURIComponent(queueId)}` :
                    `/enrollmentsystem/action/student-assistant/verify_queue.php?student_id=${encodeURIComponent(studentId)}`;
                
                fetch(url)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`Server returned ${response.status}: ${response.statusText}`);
                        }
                        return response.text(); // First get as text to inspect
                    })
                    .then(text => {
                        try {
                            // Try to parse as JSON
                            return JSON.parse(text);
                        } catch (e) {
                            console.error("Invalid JSON response:", text);
                            throw new Error("Server returned an invalid JSON response");
                        }
                    })
                    .then(data => {
                        if (data.success) {
                            displayStudentInfo(data.ticket);
                            currentTicket = data.ticket;
                            
                            // Show success notification
                            Swal.fire({
                                icon: 'success',
                                title: 'QR Code Scanned',
                                text: `Successfully scanned QR code for student ${data.ticket.student_name}`,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            if (data.student) {
                                // Student found but not in queue
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Student Found',
                                    text: data.message,
                                    footer: 'Student is not currently in queue.'
                                });
                            } else {
                                showScanError(data.message || "Could not verify queue ticket");
                            }
                        }
                    })
                    .catch(error => {
                        console.error("Error verifying ticket:", error);
                        showScanError("Error verifying ticket: " + error.message);
                    });
            }
            
            // Function to update action buttons based on ticket status
            function updateActionButtons(status) {
                // Reset all buttons first
                callStudentBtn.disabled = true;
                startProcessingBtn.disabled = true;
                completeBtn.disabled = true;
                noShowBtn.disabled = true;
                
                // Enable appropriate buttons based on status
                switch(status) {
                    case 'waiting':
                        callStudentBtn.disabled = false;
                        noShowBtn.disabled = false;
                        break;
                    case 'ready':
                        startProcessingBtn.disabled = false;
                        noShowBtn.disabled = false;
                        break;
                    case 'in_progress':
                        completeBtn.disabled = false;
                        break;
                    // No buttons enabled for completed, no_show, or cancelled statuses
                }
            }
            
            // Function to display student info
            function displayStudentInfo(ticket, student = null) {
                // Show result container
                scannedResult.classList.remove('d-none');
                emptyResult.classList.add('d-none');
                
                // Fill in student details
                studentIdSpan.textContent = ticket.student_id;
                studentNameSpan.textContent = ticket.student_name || (student ? student.student_name : 'N/A');
                queueNumberSpan.textContent = ticket.queue_number;
                departmentSpan.textContent = ticket.department;
                
                // Set queue status with appropriate styling
                queueStatusSpan.textContent = formatStatus(ticket.status);
                queueStatusSpan.className = 'badge ' + getStatusBadgeClass(ticket.status);
                
                // Store the current ticket ID for actions
                document.getElementById('currentTicketId').value = ticket.id;
                currentTicket = ticket;
                
                // Enable/disable buttons based on status
                updateActionButtons(ticket.status);
            }
            
            // Function to show scan error
            function showScanError(message) {
                Swal.fire({
                    icon: 'error',
                    title: 'Scan Error',
                    text: message
                });
                
                // Reset the scan result area
                scannedResult.classList.add('d-none');
                emptyResult.classList.remove('d-none');
            }
            
            // Function to load queue data
            function loadQueueData() {
                fetch('/enrollmentsystem/action/student-assistant/get_queue_list.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                                                         // Update stats
                             document.getElementById('waitingCount').textContent = data.stats.waiting || 0;
                             document.getElementById('readyCount').textContent = data.stats.ready || 0;
                             document.getElementById('inProgressCount').textContent = data.stats.in_progress || 0;
                             document.getElementById('completedCount').textContent = data.stats.completed || 0;
                             document.getElementById('noShowCount').textContent = data.stats.cancelled || 0;
                            
                            // Update queue table
                            updateQueueTable(data.queue);
                        } else {
                            console.error("Error loading queue data:", data.message);
                        }
                    })
                    .catch(error => {
                        console.error("Error fetching queue data:", error);
                    });
            }
            
            // Function to update queue table
            function updateQueueTable(queue) {
                queueTableBody.innerHTML = '';
                
                if (queue && queue.length > 0) {
                    emptyQueue.classList.add('d-none');
                    
                    queue.forEach(ticket => {
                        const row = document.createElement('tr');
                        
                        // Calculate time in queue
                        const createdAt = new Date(ticket.created_at);
                        const now = new Date();
                        const timeInQueue = Math.floor((now - createdAt) / (1000 * 60)); // minutes
                        
                        row.innerHTML = `
                            <td><span class="badge bg-primary">${ticket.queue_number}</span></td>
                            <td>${ticket.student_id}</td>
                            <td>${ticket.student_name}</td>
                            <td><span class="badge ${getStatusBadgeClass(ticket.status)}">${formatStatus(ticket.status)}</span></td>
                            <td>${timeInQueue} min</td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    ${ticket.status === 'waiting' ? 
                                        `<button class="btn btn-success call-btn" data-id="${ticket.id}" data-student="${ticket.student_id}">
                                            <i class="bi bi-megaphone"></i>
                                        </button>` : ''}
                                                                         ${ticket.status === 'ready' ? 
                                         `<button class="btn btn-warning start-processing-btn" data-id="${ticket.id}" data-student="${ticket.student_id}">
                                             <i class="bi bi-play-circle"></i>
                                         </button>
                                         <button class="btn btn-danger no-show-btn" data-id="${ticket.id}" data-student="${ticket.student_id}">
                                             <i class="bi bi-x-circle"></i>
                                         </button>` : ''}
                                     ${ticket.status === 'in_progress' ? 
                                         `<button class="btn btn-primary complete-btn" data-id="${ticket.id}" data-student="${ticket.student_id}">
                                             <i class="bi bi-check-circle"></i>
                                         </button>` : ''}
                                </div>
                            </td>
                        `;
                        
                        queueTableBody.appendChild(row);
                    });
                    
                    // Add event listeners to the newly created buttons
                    addTableButtonListeners();
                } else {
                    emptyQueue.classList.remove('d-none');
                }
            }
            
            // Function to add event listeners to table buttons
            function addTableButtonListeners() {
                // Call student buttons
                document.querySelectorAll('.call-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const ticketId = this.dataset.id;
                        callStudent(ticketId);
                    });
                });
                
                                 // Start processing buttons
                 document.querySelectorAll('.start-processing-btn').forEach(btn => {
                     btn.addEventListener('click', function() {
                         const ticketId = this.dataset.id;
                         startProcessing(ticketId);
                     });
                 });
                 
                 // Complete buttons
                 document.querySelectorAll('.complete-btn').forEach(btn => {
                     btn.addEventListener('click', function() {
                         const ticketId = this.dataset.id;
                         completeTicket(ticketId);
                     });
                 });
                
                // No show buttons
                document.querySelectorAll('.no-show-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const ticketId = this.dataset.id;
                        markNoShow(ticketId);
                    });
                });
            }
            
                         // Call student function
             function callStudent(ticketId) {
                 manageQueue(ticketId, 'call_next');
             }
             
             // Start processing function
             function startProcessing(ticketId) {
                 manageQueue(ticketId, 'start_processing');
             }
            
            // Complete ticket function
            function completeTicket(ticketId) {
                manageQueue(ticketId, 'complete');
            }
            
            // Mark as no show function
            function markNoShow(ticketId) {
                manageQueue(ticketId, 'no_show');
            }
            
            // Function to manage queue
            function manageQueue(ticketId, action) {
                // Show confirmation for no_show action
                if (action === 'no_show') {
                    Swal.fire({
                        title: 'Mark as No Show?',
                        text: 'Are you sure you want to mark this student as No Show?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, mark as No Show'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            performQueueAction(ticketId, action);
                        }
                    });
                } else {
                    performQueueAction(ticketId, action);
                }
            }
            
            // Function to perform queue action
            function performQueueAction(ticketId, action) {
                fetch('/enrollmentsystem/action/student-assistant/manage_queue.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        ticket_id: ticketId,
                        action: action
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        
                        // If current scanned ticket was updated, reset the scanned area
                        if (currentTicket && currentTicket.id == ticketId) {
                            scannedResult.classList.add('d-none');
                            emptyResult.classList.remove('d-none');
                            currentTicket = null;
                        }
                        
                        // Reload queue data
                        loadQueueData();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while processing your request.'
                    });
                });
            }
            
                         // Action button event listeners
             callStudentBtn.addEventListener('click', function() {
                 if (currentTicket) {
                     callStudent(currentTicket.id);
                 }
             });
             
             startProcessingBtn.addEventListener('click', function() {
                 if (currentTicket) {
                     startProcessing(currentTicket.id);
                 }
             });
            
            completeBtn.addEventListener('click', function() {
                if (currentTicket) {
                    completeTicket(currentTicket.id);
                }
            });
            
            noShowBtn.addEventListener('click', function() {
                if (currentTicket) {
                    markNoShow(currentTicket.id);
                }
            });
            
            // Refresh queue button
            refreshQueueBtn.addEventListener('click', function() {
                loadQueueData();
                Swal.fire({
                    icon: 'info',
                    title: 'Refreshing...',
                    text: 'Refreshing queue data...',
                    timer: 1000,
                    showConfirmButton: false
                });
            });
            
            // Set refresh interval (every 30 seconds)
            setInterval(loadQueueData, 30000);
            
                         // File Upload Button for QR Code Processing
             uploadQrBtn.addEventListener('click', function() {
                 const file = qrFileInput.files[0];
                 if (!file) {
                     Swal.fire({
                         icon: 'warning',
                         title: 'No File Selected',
                         text: 'Please select an image file to upload.'
                     });
                     return;
                 }
                 
                 // Show loading
                 Swal.fire({
                     title: 'Processing QR Code...',
                     text: 'Please wait while we process the uploaded image.',
                     allowOutsideClick: false,
                     didOpen: () => {
                         Swal.showLoading();
                     }
                 });
                 
                 // FIRST TRY: Client-side QR code scanning using html5-qrcode.min.js
                 const html5QrCode = new Html5Qrcode("qr-reader");
                 
                 html5QrCode.scanFile(file, true)
                     .then(decodedText => {
                         console.log("Client-side QR code scan successful:", decodedText);
                         
                         // Extract student_id from the decoded text (URL format or JSON)
                         let studentId = null;
                         let queueId = null;
                         
                         // Try to parse the QR data
                         if (decodedText.includes('s=')) {
                             // URL format: http://localhost/enrollmentsystem/scan.php?s=2025-00001&q=143&n=RG-024
                             const match = decodedText.match(/s=([^&]+)/);
                             if (match) {
                                 studentId = match[1];
                                 
                                 // Also try to get queue ID
                                 const qMatch = decodedText.match(/q=([^&]+)/);
                                 if (qMatch) {
                                     queueId = qMatch[1];
                                 }
                             }
                         } else if (decodedText.includes('student_id')) {
                             // JSON format
                             try {
                                 const jsonData = JSON.parse(decodedText);
                                 if (jsonData.student_id) {
                                     studentId = jsonData.student_id;
                                     queueId = jsonData.queue_id || null;
                                 }
                             } catch (e) {
                                 console.warn("Failed to parse JSON from QR code:", e);
                             }
                         }
                         
                         if (studentId) {
                             // We successfully extracted a student ID from the QR code
                             // Now verify with the server
                             verifyQueueTicket(studentId, queueId);
                             Swal.close();
                         } else {
                             // Fall back to server-side processing if we couldn't extract the student ID
                             serverSideProcessing(file);
                         }
                     })
                     .catch(error => {
                         console.warn("Client-side QR code scan failed:", error);
                         // Fall back to server-side processing
                         serverSideProcessing(file);
                     });
                 
                 // Function for server-side processing as fallback
                 function serverSideProcessing(file) {
                     const formData = new FormData();
                     formData.append('qr_file', file);
                     
                     fetch('/enrollmentsystem/action/student-assistant/process_qr_upload.php', {
                         method: 'POST',
                         body: formData
                     })
                     .then(response => response.json())
                     .then(data => {
                         Swal.close();
                         
                         if (data.success) {
                             displayStudentInfo(data.ticket, data.student);
                             
                             Swal.fire({
                                 icon: 'success',
                                 title: 'QR Code Processed',
                                 text: `Successfully processed QR code for student ${data.student.student_name}`,
                                 timer: 2000,
                                 showConfirmButton: false
                             });
                         } else {
                             if (data.student) {
                                 // Student found but not in queue
                                 Swal.fire({
                                     icon: 'info',
                                     title: 'Student Found',
                                     text: data.message,
                                     footer: 'Student is not currently in queue.'
                                 });
                             } else {
                                 showScanError(data.message);
                             }
                         }
                     })
                     .catch(error => {
                         Swal.close();
                         console.error("Error processing upload:", error);
                         
                         // Show a more helpful error message with guidance
                         Swal.fire({
                             icon: 'error',
                             title: 'QR Processing Error',
                             html: `
                                 <p>${error.message || "Network error while processing upload"}</p>
                                 <div class="alert alert-info mt-3">
                                    <strong>Troubleshooting:</strong><br>
                                    <ul class="text-start mt-2">
                                        <li>Make sure the QR code is clearly visible in the image</li>
                                        <li>Ensure the image is not blurry or distorted</li>
                                        <li>Try taking a new photo with better lighting</li>
                                        <li>For demo purposes, try naming the file with the student ID</li>
                                    </ul>
                                 </div>
                             `
                         });
                     });
                 }
             });
        });
    </script>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>


