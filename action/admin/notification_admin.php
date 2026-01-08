<?php
session_start();
require_once '../../config/database.php';
require_once '../student/notification_helpers.php';

// For testing - in production this should be protected with admin authentication
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add_notification':
            $success = addNotification(
                $_POST['user_email'],
                $_POST['user_type'],
                $_POST['title'],
                $_POST['message'],
                $_POST['type'],
                $_POST['icon']
            );
            
            echo json_encode(['success' => $success]);
            exit;
            
        case 'welcome':
            $success = addWelcomeNotification($_POST['user_email']);
            echo json_encode(['success' => $success]);
            exit;
            
        case 'approval':
            $success = addRegistrationApprovalNotification($_POST['user_email']);
            echo json_encode(['success' => $success]);
            exit;
            
        case 'payment':
            $success = addPaymentReminderNotification($_POST['user_email']);
            echo json_encode(['success' => $success]);
            exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification Admin - NCST</title>
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="bi bi-bell me-2"></i>Notification Admin Panel</h4>
                    </div>
                    <div class="card-body">
                        
                        <!-- Quick Actions -->
                        <div class="mb-4">
                            <h5>Quick Actions</h5>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <input type="email" id="quickEmail" class="form-control" placeholder="Enter student email">
                                </div>
                                <div class="col-md-6">
                                    <div class="btn-group w-100" role="group">
                                        <button type="button" class="btn btn-success" onclick="sendQuickNotification('welcome')">
                                            Welcome
                                        </button>
                                        <button type="button" class="btn btn-info" onclick="sendQuickNotification('approval')">
                                            Approval
                                        </button>
                                        <button type="button" class="btn btn-warning" onclick="sendQuickNotification('payment')">
                                            Payment
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Custom Notification Form -->
                        <form id="notificationForm">
                            <h5>Send Custom Notification</h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="userEmail" class="form-label">User Email</label>
                                    <input type="email" class="form-control" id="userEmail" name="user_email" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="userType" class="form-label">User Type</label>
                                    <select class="form-select" id="userType" name="user_type">
                                        <option value="student">Student</option>
                                        <option value="evaluator">Evaluator</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="notificationType" class="form-label">Type</label>
                                    <select class="form-select" id="notificationType" name="type">
                                        <option value="info">Info</option>
                                        <option value="success">Success</option>
                                        <option value="warning">Warning</option>
                                        <option value="danger">Danger</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="icon" class="form-label">Icon</label>
                                    <select class="form-select" id="icon" name="icon">
                                        <option value="bi-info-circle-fill">Info Circle</option>
                                        <option value="bi-check-circle-fill">Check Circle</option>
                                        <option value="bi-exclamation-triangle-fill">Warning Triangle</option>
                                        <option value="bi-x-circle-fill">X Circle</option>
                                        <option value="bi-heart-fill">Heart</option>
                                        <option value="bi-bell-fill">Bell</option>
                                        <option value="bi-trophy-fill">Trophy</option>
                                        <option value="bi-credit-card-fill">Credit Card</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send me-1"></i>Send Notification
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/js/bootstrap.min.js"></script>
    <script>
        function sendQuickNotification(type) {
            const email = document.getElementById('quickEmail').value;
            if (!email) {
                alert('Please enter an email address');
                return;
            }

            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=${type}&user_email=${encodeURIComponent(email)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`${type.charAt(0).toUpperCase() + type.slice(1)} notification sent successfully!`);
                    document.getElementById('quickEmail').value = '';
                } else {
                    alert('Failed to send notification');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error occurred while sending notification');
            });
        }

        document.getElementById('notificationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'add_notification');
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Notification sent successfully!');
                    this.reset();
                } else {
                    alert('Failed to send notification');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error occurred while sending notification');
            });
        });
    </script>
</body>
</html>
