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
        <!-- Welcome Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card welcome-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="fw-bold mb-2">Welcome, <?php echo htmlspecialchars($cashier['name'] ?? 'Cashier'); ?>!</h2>
                                <p class="mb-0">Treasury Dashboard - Manage student payments and financial records</p>
                            </div>
                            <div class="text-end">
                                <h6 class="mb-1">Today's Date</h6>
                                <p class="mb-0 fs-5"><?php echo date('F j, Y'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Fully Paid</h5>
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 2rem;"></i>
                        </div>
                        <h2 class="text-success fw-bold" id="fullyPaidCount">0</h2>
                        <small class="text-muted">Students completed payment</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Installment</h5>
                            <i class="bi bi-clock-fill text-warning" style="font-size: 2rem;"></i>
                        </div>
                        <h2 class="text-warning fw-bold" id="installmentCount">0</h2>
                        <small class="text-muted">Students with ongoing installments</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Pending Verification</h5>
                            <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 2rem;"></i>
                        </div>
                        <h2 class="text-danger fw-bold" id="pendingVerificationCount">0</h2>
                        <small class="text-muted">Payments awaiting verification</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-lightning-fill me-2"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="d-grid">
                                    <a href="record_payment.php" class="btn btn-primary">
                                        <i class="bi bi-cash-coin me-2"></i>Record New Payment
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="d-grid">
                                    <a href="student_list.php" class="btn btn-primary">
                                        <i class="bi bi-people me-2"></i>View All Students
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="d-grid">
                                    <a href="payment_verification.php" class="btn btn-warning text-white">
                                        <i class="bi bi-clipboard-check me-2"></i>Verify Payments
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="d-grid">
                                    <a href="installment_tracking.php" class="btn btn-info text-white">
                                        <i class="bi bi-calendar-check me-2"></i>Track Installments
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="d-grid">
                                    <a href="official_receipt.php" class="btn btn-success">
                                        <i class="bi bi-receipt me-2"></i>Generate OR
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="d-grid">
                                    <a href="notifications.php" class="btn btn-secondary">
                                        <i class="bi bi-bell me-2"></i>Notification Center
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Payment Activities -->
        <div class="row">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Payment Activities</h5>
                        <a href="student_list.php" class="btn btn-outline-primary btn-sm">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>Amount</th>
                                        <th>Payment Method</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody id="recentActivitiesTable">
                                    <!-- Dynamic content will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
    
    <script>
        // Load dashboard data when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadPaymentStatistics();
            loadRecentActivities();
        });
        
        // Load payment statistics
        function loadPaymentStatistics() {
            fetch('/enrollmentsystem/action/cashier/dashboard_handler.php?action=get_stats')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.text();
                })
                .then(text => {
                    try {
                        const data = JSON.parse(text);
                        if (data.success) {
                            document.getElementById('fullyPaidCount').textContent = data.data.fully_paid;
                            document.getElementById('installmentCount').textContent = data.data.installment;
                            document.getElementById('pendingVerificationCount').textContent = data.data.pending_verification;
                        }
                    } catch (e) {
                        console.error('Invalid JSON response:', text);
                        throw new Error('Invalid JSON response');
                    }
                })
                .catch(error => {
                    console.error('Error loading payment statistics:', error);
                });
        }
        
        // Load recent activities
        function loadRecentActivities() {
            fetch('/enrollmentsystem/action/cashier/dashboard_handler.php?action=get_recent_activities&limit=5')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.text();
                })
                .then(text => {
                    try {
                        const data = JSON.parse(text);
                        if (data.success) {
                            const tbody = document.getElementById('recentActivitiesTable');
                            tbody.innerHTML = '';
                            
                            data.data.forEach(activity => {
                                const row = document.createElement('tr');
                                row.innerHTML = `
                                    <td>${activity.student_id}</td>
                                    <td>${activity.student_name}</td>
                                    <td>₱${activity.amount}</td>
                                    <td><span class="badge bg-primary">${activity.payment_method}</span></td>
                                    <td><span class="badge bg-${activity.status === 'Paid' ? 'success' : 'warning'}">${activity.status}</span></td>
                                    <td>${activity.date}</td>
                                `;
                                tbody.appendChild(row);
                            });
                            
                            if (data.data.length === 0) {
                                tbody.innerHTML = '<tr><td colspan="6" class="text-center">No recent activities found</td></tr>';
                            }
                        }
                    } catch (e) {
                        console.error('Invalid JSON response:', text);
                        throw new Error('Invalid JSON response');
                    }
                })
                .catch(error => {
                    console.error('Error loading recent activities:', error);
                });
        }
    </script>

    <!-- Notifications Modal -->
    <?php include '../../includes/notifications-modal.php'; ?>

    <!-- Include notification JavaScript -->
    <script src="../../../assets/js/notifications.js?v=<?php echo time(); ?>"></script>
</body>
</html>
