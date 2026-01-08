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
                                <h2 class="fw-bold mb-1">Notification Center</h2>
                                <p class="text-muted mb-0">Send notifications to students and manage communication</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="bi bi-person-exclamation text-warning" style="font-size: 2rem;"></i>
                        <h5 class="mt-3">Payment Reminder</h5>
                        <button class="btn btn-warning btn-sm" onclick="sendQuickNotification('payment_reminder')">
                            Send to Unpaid Students
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                        <h5 class="mt-3">Payment Verified</h5>
                        <button class="btn btn-success btn-sm" onclick="sendQuickNotification('payment_verified')">
                            Send to Recent Payments
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="bi bi-info-circle text-info" style="font-size: 2rem;"></i>
                        <h5 class="mt-3">System Notice</h5>
                        <button class="btn btn-info btn-sm" onclick="openSystemNoticeModal()">
                            Send to All Students
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="bi bi-envelope text-primary" style="font-size: 2rem;"></i>
                        <h5 class="mt-3">Custom Message</h5>
                        <button class="btn btn-primary btn-sm" onclick="openCustomMessageModal()">
                            Create Custom
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Notifications -->
        <div class="row">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Recent Notifications Sent</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date/Time</th>
                                        <th>Recipient</th>
                                        <th>Type</th>
                                        <th>Title</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="recentNotificationsTable">
                                    <!-- Dynamic content will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Message Modal -->
    <div class="modal fade" id="customMessageModal" tabindex="-1" aria-labelledby="customMessageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customMessageModalLabel">Send Custom Notification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="customMessageForm">
                        <div class="mb-3">
                            <label for="recipientType" class="form-label">Send To</label>
                            <select class="form-select" id="recipientType" required>
                                <option value="">Select Recipients</option>
                                <option value="all_students">All Students</option>
                                <option value="unpaid_students">Students with Unpaid Balance</option>
                                <option value="partial_students">Students with Partial Payment</option>
                                <option value="paid_students">Students with Full Payment</option>
                                <option value="specific_student">Specific Student</option>
                            </select>
                        </div>
                        <div class="mb-3" id="specificStudentDiv" style="display: none;">
                            <label for="specificStudentId" class="form-label">Student ID</label>
                            <input type="text" class="form-control" id="specificStudentId" placeholder="Enter student ID">
                        </div>
                        <div class="mb-3">
                            <label for="notificationTitle" class="form-label">Title</label>
                            <input type="text" class="form-control" id="notificationTitle" required maxlength="200">
                        </div>
                        <div class="mb-3">
                            <label for="notificationMessage" class="form-label">Message</label>
                            <textarea class="form-control" id="notificationMessage" rows="4" required maxlength="500"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="notificationType" class="form-label">Type</label>
                            <select class="form-select" id="notificationType" required>
                                <option value="info">Information</option>
                                <option value="success">Success</option>
                                <option value="warning">Warning</option>
                                <option value="danger">Important</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="sendCustomNotification()">Send Notification</button>
                </div>
            </div>
        </div>
    </div>

    <!-- System Notice Modal -->
    <div class="modal fade" id="systemNoticeModal" tabindex="-1" aria-labelledby="systemNoticeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="systemNoticeModalLabel">Send System Notice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="systemNoticeForm">
                        <div class="mb-3">
                            <label for="systemNoticeTitle" class="form-label">Notice Title</label>
                            <input type="text" class="form-control" id="systemNoticeTitle" value="Important System Notice" required>
                        </div>
                        <div class="mb-3">
                            <label for="systemNoticeMessage" class="form-label">Notice Message</label>
                            <textarea class="form-control" id="systemNoticeMessage" rows="4" required placeholder="Enter your system-wide notice here..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" onclick="sendSystemNotice()">Send to All Students</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Load recent notifications when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadRecentNotifications();
            
            // Show/hide specific student input
            document.getElementById('recipientType').addEventListener('change', function() {
                const specificDiv = document.getElementById('specificStudentDiv');
                if (this.value === 'specific_student') {
                    specificDiv.style.display = 'block';
                } else {
                    specificDiv.style.display = 'none';
                }
            });
        });

        // Open custom message modal
        function openCustomMessageModal() {
            new bootstrap.Modal(document.getElementById('customMessageModal')).show();
        }

        // Open system notice modal
        function openSystemNoticeModal() {
            new bootstrap.Modal(document.getElementById('systemNoticeModal')).show();
        }

        // Send quick notification
        function sendQuickNotification(type) {
            let title, message, notificationType, recipientType;
            
            switch(type) {
                case 'payment_reminder':
                    title = 'Payment Reminder';
                    message = 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.';
                    notificationType = 'warning';
                    recipientType = 'unpaid_students';
                    break;
                case 'payment_verified':
                    title = 'Payment Verified';
                    message = 'Your recent payment has been verified and processed successfully. Thank you for your payment!';
                    notificationType = 'success';
                    recipientType = 'recent_payments';
                    break;
            }
            
            if (confirm(`Send "${title}" to ${recipientType.replace('_', ' ')}?`)) {
                sendNotificationRequest({
                    recipient_type: recipientType,
                    title: title,
                    message: message,
                    notification_type: notificationType
                });
            }
        }

        // Send custom notification
        function sendCustomNotification() {
            const form = document.getElementById('customMessageForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            const data = {
                recipient_type: document.getElementById('recipientType').value,
                specific_student_id: document.getElementById('specificStudentId').value,
                title: document.getElementById('notificationTitle').value,
                message: document.getElementById('notificationMessage').value,
                notification_type: document.getElementById('notificationType').value
            };
            
            sendNotificationRequest(data);
            bootstrap.Modal.getInstance(document.getElementById('customMessageModal')).hide();
        }

        // Send system notice
        function sendSystemNotice() {
            const form = document.getElementById('systemNoticeForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            const data = {
                recipient_type: 'all_students',
                title: document.getElementById('systemNoticeTitle').value,
                message: document.getElementById('systemNoticeMessage').value,
                notification_type: 'info'
            };
            
            if (confirm('Send this notice to ALL students?')) {
                sendNotificationRequest(data);
                bootstrap.Modal.getInstance(document.getElementById('systemNoticeModal')).hide();
            }
        }

        // Send notification request to backend
        function sendNotificationRequest(data) {
            console.log('Sending notification request:', data);
            
            fetch('/enrollmentsystem/action/cashier/notification_sender.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response ok:', response.ok);
                // Check if response is ok
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text();
            })
            .then(text => {
                console.log('Raw response text:', text);
                try {
                    const result = JSON.parse(text);
                    if (result.success) {
                        Swal.fire('Success!', result.message, 'success');
                        loadRecentNotifications();
                    } else {
                        Swal.fire('Error!', result.message, 'error');
                    }
                } catch (e) {
                    console.error('Invalid JSON response:', text);
                    Swal.fire('Error!', 'Invalid response from server: ' + text.substring(0, 200), 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error!', 'Network error: ' + error.message, 'error');
            });
        }

        // Load recent notifications
        function loadRecentNotifications() {
            console.log('Loading recent notifications...');
            fetch('/enrollmentsystem/action/cashier/notification_sender.php?action=recent')
                .then(response => {
                    console.log('Recent notifications response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.text();
                })
                .then(text => {
                    console.log('Recent notifications raw response:', text);
                    try {
                        const data = JSON.parse(text);
                        if (data.success) {
                            displayRecentNotifications(data.notifications);
                        } else {
                            console.error('Failed to load notifications:', data.message);
                        }
                    } catch (e) {
                        console.error('Invalid JSON response:', text);
                    }
                })
                .catch(error => {
                    console.error('Error loading recent notifications:', error);
                });
        }

        // Display recent notifications
        function displayRecentNotifications(notifications) {
            const tbody = document.getElementById('recentNotificationsTable');
            tbody.innerHTML = '';

            if (notifications.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center">No notifications sent yet</td></tr>';
                return;
            }

            notifications.forEach(notification => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${notification.created_at}</td>
                    <td>${notification.recipient}</td>
                    <td><span class="badge bg-${getTypeColor(notification.type)}">${notification.type}</span></td>
                    <td>${notification.title}</td>
                    <td><span class="badge bg-success">Sent</span></td>
                `;
                tbody.appendChild(row);
            });
        }

        // Get type color for badge
        function getTypeColor(type) {
            const colors = {
                'info': 'primary',
                'success': 'success',
                'warning': 'warning',
                'danger': 'danger'
            };
            return colors[type] || 'secondary';
        }
    </script>

    <!-- Notifications Modal -->
    <?php include '../../includes/notifications-modal.php'; ?>

    <!-- Include notification JavaScript -->
    <script src="../../../assets/js/notifications.js?v=<?php echo time(); ?>"></script>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>
