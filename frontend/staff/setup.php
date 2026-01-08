<?php
// Start session
session_start();

// Simple protection to prevent direct access except from admin
$auth_key = isset($_GET['key']) ? $_GET['key'] : '';
if ($auth_key !== 'setup-ncst-staff') {
    echo "Unauthorized access. Please use the proper setup link.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NCST Staff Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="bi bi-gear-fill me-2"></i>NCST Staff Database Setup</h4>
                    </div>
                    <div class="card-body">
                        <div id="status" class="alert alert-info">
                            <i class="bi bi-info-circle-fill me-2"></i>Preparing to initialize staff database tables...
                        </div>
                        
                        <div class="progress mb-4" style="height: 25px;">
                            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" 
                                 role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                        </div>
                        
                        <div id="setupLog" class="border rounded p-3 bg-light" style="max-height: 300px; overflow-y: auto;">
                            <div class="text-muted">Setup log will appear here...</div>
                        </div>
                        
                        <div class="mt-4 text-center">
                            <button id="setupBtn" class="btn btn-primary px-4">
                                <i class="bi bi-database-fill-gear me-2"></i>Initialize Staff Database
                            </button>
                            <a href="../staff/index.php" class="btn btn-outline-secondary ms-2 px-4">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Go to Login Page
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-4 shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-person-badge-fill me-2"></i>Staff Accounts</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Staff Type</th>
                                        <th>Email</th>
                                        <th>Password</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span class="badge bg-primary">Evaluator</span></td>
                                        <td>evaluator@ncst.edu.ph</td>
                                        <td>password</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-success">Registrar</span></td>
                                        <td>registrar@ncst.edu.ph</td>
                                        <td>password</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-warning text-dark">Cashier</span></td>
                                        <td>cashier@ncst.edu.ph</td>
                                        <td>password</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-info">Student Assistant</span></td>
                                        <td>student-assistant@ncst.edu.ph</td>
                                        <td>password</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const setupBtn = document.getElementById('setupBtn');
            const status = document.getElementById('status');
            const setupLog = document.getElementById('setupLog');
            const progressBar = document.getElementById('progressBar');
            
            setupBtn.addEventListener('click', async function() {
                try {
                    // Disable button
                    setupBtn.disabled = true;
                    setupBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Setting Up...';
                    
                    // Update status
                    status.className = 'alert alert-info';
                    status.innerHTML = '<i class="bi bi-arrow-clockwise me-2"></i>Setting up staff database tables...';
                    
                    // Log start
                    addToLog('Starting staff database setup...');
                    updateProgress(10);
                    
                    // Call setup script
                    addToLog('Creating database tables for staff roles...');
                    updateProgress(30);
                    
                    const response = await fetch('../../action/staff/setup_db.php');
                    let result;
                    
                    try {
                        result = await response.json();
                        addToLog('Database response received: ' + result.message);
                    } catch (e) {
                        const text = await response.text();
                        addToLog('Warning: Non-JSON response from server');
                        addToLog('Server response: ' + text);
                        throw new Error('Server returned an invalid response');
                    }
                    
                    updateProgress(70);
                    
                    if (result && result.success) {
                        addToLog('Staff database setup completed successfully!');
                        addToLog('Demo accounts have been created and are ready to use.');
                        updateProgress(100);
                        
                        // Update status
                        status.className = 'alert alert-success';
                        status.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>Setup completed successfully! You can now login using the demo accounts.';
                        
                        // Update button
                        setupBtn.className = 'btn btn-success px-4';
                        setupBtn.innerHTML = '<i class="bi bi-check-lg me-2"></i>Setup Complete';
                    } else {
                        throw new Error(result.message || 'Unknown error during setup');
                    }
                    
                } catch (error) {
                    console.error('Setup error:', error);
                    addToLog('Error: ' + error.message);
                    updateProgress(100, 'bg-danger');
                    
                    // Update status
                    status.className = 'alert alert-danger';
                    status.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i>Error during setup: ' + error.message;
                    
                    // Update button
                    setupBtn.className = 'btn btn-warning px-4';
                    setupBtn.disabled = false;
                    setupBtn.innerHTML = '<i class="bi bi-arrow-repeat me-2"></i>Try Again';
                }
            });
            
            function addToLog(message) {
                const timestamp = new Date().toLocaleTimeString();
                setupLog.innerHTML += `<div>[${timestamp}] ${message}</div>`;
                setupLog.scrollTop = setupLog.scrollHeight;
            }
            
            function updateProgress(percent, colorClass = 'bg-primary') {
                progressBar.style.width = percent + '%';
                progressBar.setAttribute('aria-valuenow', percent);
                progressBar.textContent = percent + '%';
                
                // Update color class if provided
                progressBar.className = `progress-bar progress-bar-striped progress-bar-animated ${colorClass}`;
            }
        });
    </script>
</body>
</html>
