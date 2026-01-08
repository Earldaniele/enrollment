<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header('Location: ../../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queue Management - NCST Enrollment System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            padding-top: 2rem;
            padding-bottom: 2rem;
        }
        .queue-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .department-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .department-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        .department-card.selected {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }
        .queue-btn {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .queue-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
        }
        .back-btn {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            border: none;
            padding: 10px 25px;
            border-radius: 20px;
            color: white;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 2rem;
            transition: all 0.3s ease;
        }
        .back-btn:hover {
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.4);
        }
        .queue-status {
            background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%);
            padding: 1rem;
            border-radius: 10px;
            color: white;
            text-align: center;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Back Button -->
        <a href="dashboard.php" class="back-btn">
            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
        </a>
        
        <div class="queue-card">
            <h2 class="text-center mb-4">
                <i class="fas fa-clock me-2"></i>Queue Management
            </h2>
            <p class="text-center text-muted mb-4">
                Choose the department where you need service. You will receive a queue number and estimated waiting time.
            </p>
            
            <!-- Department Selection -->
            <div class="row">
                <div class="col-md-4">
                    <div class="department-card" data-department="registrar">
                        <div class="text-center">
                            <i class="fas fa-file-alt fa-3x mb-3 text-primary"></i>
                            <h5>Registrar</h5>
                            <p class="text-muted small">Transcripts, Certifications, Records</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="department-card" data-department="treasury">
                        <div class="text-center">
                            <i class="fas fa-money-bill-wave fa-3x mb-3 text-success"></i>
                            <h5>Treasury</h5>
                            <p class="text-muted small">Payments, Billing, Financial Services</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="department-card" data-department="enrollment">
                        <div class="text-center">
                            <i class="fas fa-user-graduate fa-3x mb-3 text-info"></i>
                            <h5>Enrollment</h5>
                            <p class="text-muted small">Course Registration, Class Schedules</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Get Queue Button -->
            <div class="text-center mt-4">
                <button id="getQueueBtn" class="btn queue-btn" disabled>
                    <i class="fas fa-ticket-alt me-2"></i>Get Queue Number
                </button>
            </div>
            
            <!-- Queue Status (hidden initially) -->
            <div id="queueStatus" class="queue-status" style="display: none;">
                <!-- Queue information will be displayed here -->
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let selectedDepartments = [];
        
        // Department selection
        document.querySelectorAll('.department-card').forEach(card => {
            card.addEventListener('click', function() {
                const department = this.dataset.department;
                
                if (this.classList.contains('selected')) {
                    // Deselect
                    this.classList.remove('selected');
                    selectedDepartments = selectedDepartments.filter(d => d !== department);
                } else {
                    // Select
                    this.classList.add('selected');
                    selectedDepartments.push(department);
                }
                
                // Enable/disable button based on selection
                const getQueueBtn = document.getElementById('getQueueBtn');
                getQueueBtn.disabled = selectedDepartments.length === 0;
            });
        });
        
        // Get Queue
        document.getElementById('getQueueBtn').addEventListener('click', function() {
            if (selectedDepartments.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Department Selected',
                    text: 'Please select at least one department.'
                });
                return;
            }
            
            // Show loading
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Getting Queue...';
            this.disabled = true;
            
            // Send request to get queue
            fetch('../../action/student/get_queue.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    departments: selectedDepartments
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show queue information
                    showQueueStatus(data.queues);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Failed to get queue number'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to connect to server'
                });
            })
            .finally(() => {
                // Reset button
                this.innerHTML = '<i class="fas fa-ticket-alt me-2"></i>Get Queue Number';
                this.disabled = false;
            });
        });
        
        function showQueueStatus(queues) {
            const statusDiv = document.getElementById('queueStatus');
            let statusHTML = '<h5><i class="fas fa-check-circle me-2"></i>Queue Numbers Assigned!</h5>';
            
            queues.forEach(queue => {
                statusHTML += `
                    <div class="mb-2">
                        <strong>${queue.department.toUpperCase()}</strong>: 
                        Queue #${queue.queue_number} 
                        <small class="text-muted">(Estimated wait: ${queue.estimated_wait})</small>
                    </div>
                `;
            });
            
            statusDiv.innerHTML = statusHTML;
            statusDiv.style.display = 'block';
            
            // Smooth scroll to status
            statusDiv.scrollIntoView({ behavior: 'smooth' });
        }
    </script>
</body>
</html>
