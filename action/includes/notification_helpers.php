<?php
/**
 * Notification Helper Functions
 * Handles automatic notification creation throughout the system
 */

require_once __DIR__ . '/../../frontend/includes/db_config.php';

/**
 * Create a notification for a student by student ID
 */
function createNotification($student_id, $title, $message, $type = 'info') {
    global $conn;
    
    try {
        // Get student email from student_id
        $email_stmt = $conn->prepare("SELECT email_address FROM student_registrations WHERE student_id = ? LIMIT 1");
        $email_stmt->bind_param("s", $student_id);
        $email_stmt->execute();
        $email_result = $email_stmt->get_result();
        
        if ($email_result->num_rows === 0) {
            error_log("No email found for student ID: " . $student_id);
            return false;
        }
        
        $email_row = $email_result->fetch_assoc();
        $student_email = $email_row['email_address'];
        $email_stmt->close();
        
        // Insert notification
        $stmt = $conn->prepare("
            INSERT INTO notifications (user_email, user_type, title, message, type, icon, created_at) 
            VALUES (?, 'student', ?, ?, ?, 'bi-bell-fill', NOW())
        ");
        
        $stmt->bind_param("ssss", $student_email, $title, $message, $type);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
        
    } catch (Exception $e) {
        error_log("Error creating notification: " . $e->getMessage());
        return false;
    }
}

/**
 * Create a notification for staff by email
 */
function createStaffNotification($staff_email, $title, $message, $type = 'info') {
    global $conn;
    
    try {
        $stmt = $conn->prepare("
            INSERT INTO notifications (user_email, user_type, title, message, type, icon, created_at) 
            VALUES (?, 'evaluator', ?, ?, ?, 'bi-bell-fill', NOW())
        ");
        
        $stmt->bind_param("ssss", $staff_email, $title, $message, $type);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
        
    } catch (Exception $e) {
        error_log("Error creating staff notification: " . $e->getMessage());
        return false;
    }
}

/**
 * SPECIFIC NOTIFICATION FUNCTIONS
 */

// Notify registrar of new student registration
function notifyRegistrarNewRegistration($student_id, $student_name, $course) {
    $title = 'New Student Registration';
    $message = "New registration from {$student_name} (ID: {$student_id}) for {$course}. Please review and approve.";
    return createStaffNotification('registrar@ncst.edu.ph', $title, $message, 'info');
}

// Notify student that registration was approved
function notifyStudentRegistrationApproved($student_id, $student_name) {
    $title = 'Registration Approved';
    $message = "Congratulations {$student_name}! Your registration has been approved. You may now proceed to payment.";
    return createNotification($student_id, $title, $message, 'success');
}

// Notify student that registration was rejected
function notifyStudentRegistrationRejected($student_id, $student_name, $reason = '') {
    $title = 'Registration Update Required';
    $message = "Hello {$student_name}, your registration requires attention. " . ($reason ? "Reason: {$reason}" : "Please contact the registrar office for details.");
    return createNotification($student_id, $title, $message, 'warning');
}

// Notify cashier of new payment
function notifyCashierNewPayment($student_id, $student_name, $amount, $payment_method, $reference_number = '') {
    $title = 'New Payment Received';
    $message = "Payment of ₱{$amount} from {$student_name} (ID: {$student_id}) via {$payment_method}" . ($reference_number ? " - Ref: {$reference_number}" : "") . ". Please verify payment.";
    return createStaffNotification('cashier@ncst.edu.ph', $title, $message, 'info');
}

// Notify student that payment was verified
function notifyStudentPaymentVerified($student_id, $student_name, $amount) {
    $title = 'Payment Verified';
    $message = "Your payment of ₱{$amount} has been verified and processed successfully. Thank you for your payment!";
    return createNotification($student_id, $title, $message, 'success');
}

// Notify student about payment reminder
function notifyStudentPaymentReminder($student_id, $student_name, $due_amount) {
    $title = 'Payment Reminder';
    $message = "Hello {$student_name}, this is a friendly reminder that you have a balance of ₱{$due_amount}. Please visit the cashier office to complete your payment.";
    return createNotification($student_id, $title, $message, 'warning');
}

// Notify student about document status
function notifyStudentDocumentStatus($student_id, $student_name, $document_name, $status, $remarks = '') {
    $title = 'Document Status Update';
    $status_text = ($status === 'approved') ? 'approved' : 'requires attention';
    $message = "Your {$document_name} has been {$status_text}." . ($remarks ? " Note: {$remarks}" : "");
    $type = ($status === 'approved') ? 'success' : 'warning';
    return createNotification($student_id, $title, $message, $type);
}

// Notify evaluator of new documents to review
function notifyEvaluatorNewDocuments($student_id, $student_name, $document_count) {
    $title = 'Documents for Review';
    $message = "{$student_name} (ID: {$student_id}) has submitted {$document_count} document(s) for evaluation. Please review when available.";
    return createStaffNotification('evaluator@ncst.edu.ph', $title, $message, 'info');
}

?>
    
    /**
     * STUDENT REGISTRATION NOTIFICATIONS
     */
    
    // When student submits registration
    public function notifyRegistrarNewRegistration($student_id, $student_name, $course) {
        $registrar_email = 'registrar@ncst.edu.ph';
        $title = 'New Student Registration';
        $message = "New registration from {$student_name} (ID: {$student_id}) for {$course}. Please review and approve.";
        
        return $this->sendNotification($registrar_email, 'staff', $title, $message, 'info', 'bi-person-plus-fill');
    }
    
    // When registrar approves registration
    public function notifyStudentRegistrationApproved($student_email, $student_name) {
        $title = 'Registration Approved!';
        $message = "Congratulations {$student_name}! Your registration has been approved. You can now proceed with enrollment and payment.";
        
        return $this->sendNotification($student_email, 'student', $title, $message, 'success', 'bi-check-circle-fill');
    }
    
    // When registrar rejects registration
    public function notifyStudentRegistrationRejected($student_email, $student_name, $reason = '') {
        $title = 'Registration Status Update';
        $message = "Dear {$student_name}, your registration requires additional review. " . ($reason ? "Reason: {$reason}" : "Please contact the registrar for more information.");
        
        return $this->sendNotification($student_email, 'student', $title, $message, 'warning', 'bi-exclamation-triangle-fill');
    }
    
    /**
     * PAYMENT NOTIFICATIONS
     */
    
    // When student makes payment
    public function notifyCashierNewPayment($student_id, $student_name, $amount, $payment_method, $reference_number = '') {
        $cashier_email = 'cashier@ncst.edu.ph';
        $title = 'New Payment Received';
        $message = "Payment of ₱{$amount} received from {$student_name} (ID: {$student_id}) via {$payment_method}." . ($reference_number ? " Reference: {$reference_number}" : "");
        
        return $this->sendNotification($cashier_email, 'staff', $title, $message, 'info', 'bi-credit-card-fill');
    }
    
    // When cashier verifies payment
    public function notifyStudentPaymentVerified($student_email, $student_name, $amount) {
        $title = 'Payment Verified';
        $message = "Your payment of ₱{$amount} has been verified and processed successfully. Thank you!";
        
        return $this->sendNotification($student_email, 'student', $title, $message, 'success', 'bi-check-circle-fill');
    }
    
    // When payment is pending verification
    public function notifyStudentPaymentPending($student_email, $student_name, $amount) {
        $title = 'Payment Under Review';
        $message = "Your payment of ₱{$amount} is currently under review. We will notify you once it's verified.";
        
        return $this->sendNotification($student_email, 'student', $title, $message, 'info', 'bi-clock-fill');
    }
    
    // When payment has issues
    public function notifyStudentPaymentIssue($student_email, $student_name, $amount, $issue_reason) {
        $title = 'Payment Verification Issue';
        $message = "There's an issue with your payment of ₱{$amount}. Reason: {$issue_reason}. Please contact the cashier.";
        
        return $this->sendNotification($student_email, 'student', $title, $message, 'warning', 'bi-exclamation-triangle-fill');
    }
    
    /**
     * DOCUMENT NOTIFICATIONS
     */
    
    // When student submits documents
    public function notifyEvaluatorNewDocuments($student_id, $student_name, $document_count) {
        $evaluator_email = 'evaluator@ncst.edu.ph';
        $title = 'Documents for Review';
        $message = "{$student_name} (ID: {$student_id}) has submitted {$document_count} document(s) for evaluation.";
        
        return $this->sendNotification($evaluator_email, 'staff', $title, $message, 'info', 'bi-file-earmark-text-fill');
    }
    
    // When documents are verified
    public function notifyStudentDocumentsVerified($student_email, $student_name, $document_name) {
        $title = 'Document Verified';
        $message = "Your {$document_name} has been verified and approved.";
        
        return $this->sendNotification($student_email, 'student', $title, $message, 'success', 'bi-check-circle-fill');
    }
    
    // When documents need revision
    public function notifyStudentDocumentIssue($student_email, $student_name, $document_name, $issue_reason) {
        $title = 'Document Requires Attention';
        $message = "Issue with your {$document_name}: {$issue_reason}. Please resubmit or contact us for assistance.";
        
        return $this->sendNotification($student_email, 'student', $title, $message, 'warning', 'bi-exclamation-triangle-fill');
    }
    
    /**
     * ENROLLMENT NOTIFICATIONS
     */
    
    // When enrollment is completed
    public function notifyStudentEnrollmentComplete($student_email, $student_name, $course, $year_level, $semester) {
        $title = 'Enrollment Completed!';
        $message = "Congratulations {$student_name}! Your enrollment for {$course} - {$year_level}, {$semester} is now complete.";
        
        return $this->sendNotification($student_email, 'student', $title, $message, 'success', 'bi-mortarboard-fill');
    }
    
    // When enrollment deadline is approaching
    public function notifyStudentEnrollmentDeadline($student_email, $student_name, $days_remaining) {
        $title = 'Enrollment Deadline Reminder';
        $message = "Dear {$student_name}, you have {$days_remaining} day(s) left to complete your enrollment. Please process your requirements soon.";
        
        return $this->sendNotification($student_email, 'student', $title, $message, 'warning', 'bi-clock-fill');
    }
    
    /**
     * QUEUE NOTIFICATIONS
     */
    
    // When student gets queue number
    public function notifyStudentQueueAssigned($student_email, $student_name, $queue_number, $department) {
        $title = 'Queue Number Assigned';
        $message = "Your queue number is {$queue_number} for {$department}. Please wait for your turn.";
        
        return $this->sendNotification($student_email, 'student', $title, $message, 'info', 'bi-list-ol');
    }
    
    // When it's student's turn in queue
    public function notifyStudentQueueTurn($student_email, $student_name, $queue_number, $department) {
        $title = 'Your Turn - Queue #{$queue_number}';
        $message = "It's your turn! Please proceed to the {$department} office now.";
        
        return $this->sendNotification($student_email, 'student', $title, $message, 'success', 'bi-bell-fill');
    }
    
    /**
     * SYSTEM NOTIFICATIONS
     */
    
    // Welcome notification for new users
    public function sendWelcomeNotification($user_email, $user_type, $user_name) {
        if ($user_type === 'student') {
            $title = 'Welcome to NCST!';
            $message = "Welcome {$user_name}! Start your enrollment journey by completing your registration and submitting required documents.";
            $icon = 'bi-mortarboard-fill';
        } else {
            $title = 'Welcome to NCST Staff Portal';
            $message = "Welcome {$user_name}! You now have access to the staff dashboard and management tools.";
            $icon = 'bi-shield-check-fill';
        }
        
        return $this->sendNotification($user_email, $user_type, $title, $message, 'success', $icon);
    }
    
    // System maintenance notifications
    public function notifySystemMaintenance($start_time, $end_time, $description = '') {
        $title = 'Scheduled Maintenance';
        $message = "System maintenance scheduled from {$start_time} to {$end_time}. " . ($description ? $description : "Services may be temporarily unavailable.");
        
        // Send to all active users
        $stmt = $this->conn->prepare("
            SELECT DISTINCT email, 'student' as user_type FROM student_accounts WHERE status = 'active'
            UNION
            SELECT DISTINCT email, 'staff' as user_type FROM staff_accounts WHERE status = 'active'
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        
        $sent_count = 0;
        while ($row = $result->fetch_assoc()) {
            $this->sendNotification($row['email'], $row['user_type'], $title, $message, 'warning', 'bi-exclamation-triangle-fill');
            $sent_count++;
        }
        
        return ['success' => true, 'message' => "Maintenance notification sent to {$sent_count} users"];
    }
    
    /**
     * BULK NOTIFICATIONS
     */
    
    // Send to all students of specific course/year
    public function notifyStudentsByCourse($course, $year_level, $title, $message, $type = 'info', $icon = 'bi-info-circle-fill') {
        $stmt = $this->conn->prepare("
            SELECT sa.email 
            FROM student_accounts sa
            JOIN student_registrations sr ON sa.email = sr.email_address
            JOIN enrollments e ON sr.student_id = e.student_id
            WHERE sr.desired_course LIKE ? 
            AND e.year_level = ? 
            AND sa.status = 'active'
            AND sr.status = 'approved'
        ");
        
        $course_pattern = "%{$course}%";
        $stmt->bind_param("ss", $course_pattern, $year_level);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $sent_count = 0;
        while ($row = $result->fetch_assoc()) {
            $this->sendNotification($row['email'], 'student', $title, $message, $type, $icon);
            $sent_count++;
        }
        
        return ['success' => true, 'message' => "Notification sent to {$sent_count} students"];
    }
    
    // Send to all staff of specific role
    public function notifyStaffByRole($role, $title, $message, $type = 'info', $icon = 'bi-info-circle-fill') {
        $stmt = $this->conn->prepare("
            SELECT email 
            FROM staff_accounts 
            WHERE role = ? AND status = 'active'
        ");
        
        $stmt->bind_param("s", $role);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $sent_count = 0;
        while ($row = $result->fetch_assoc()) {
            $this->sendNotification($row['email'], 'staff', $title, $message, $type, $icon);
            $sent_count++;
        }
        
        return ['success' => true, 'message' => "Notification sent to {$sent_count} {$role}(s)"];
    }
}

// Global helper functions for easy use
function getNotificationHelper() {
    return new NotificationHelper();
}

// Quick notification functions
function notifyRegistrarNewRegistration($student_id, $student_name, $course) {
    $helper = getNotificationHelper();
    return $helper->notifyRegistrarNewRegistration($student_id, $student_name, $course);
}

function notifyStudentRegistrationApproved($student_email, $student_name) {
    $helper = getNotificationHelper();
    return $helper->notifyStudentRegistrationApproved($student_email, $student_name);
}

function notifyCashierNewPayment($student_id, $student_name, $amount, $payment_method, $reference_number = '') {
    $helper = getNotificationHelper();
    return $helper->notifyCashierNewPayment($student_id, $student_name, $amount, $payment_method, $reference_number);
}

function notifyStudentPaymentVerified($student_email, $student_name, $amount) {
    $helper = getNotificationHelper();
    return $helper->notifyStudentPaymentVerified($student_email, $student_name, $amount);
}

// Add more quick functions as needed...

?>
