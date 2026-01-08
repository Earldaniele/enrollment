<?php
require_once '../../frontend/includes/db_config.php';

/**
 * Add a notification for a user
 * 
 * @param string $user_email User's email address
 * @param string $user_type User type (student, evaluator, admin)
 * @param string $title Notification title
 * @param string $message Notification message
 * @param string $type Type (info, success, warning, danger)
 * @param string $icon Bootstrap icon class (optional)
 * @return bool Success status
 */
function addNotification($user_email, $user_type, $title, $message, $type = 'info', $icon = null) {
    global $servername, $username, $password, $dbname;
    
    try {
        $conn = new mysqli($servername, $username, $password, $dbname);
        
        if ($conn->connect_error) {
            return false;
        }

        // Set default icon based on type if not provided
        if (!$icon) {
            switch ($type) {
                case 'success':
                    $icon = 'bi-check-circle-fill';
                    break;
                case 'warning':
                    $icon = 'bi-exclamation-triangle-fill';
                    break;
                case 'danger':
                    $icon = 'bi-x-circle-fill';
                    break;
                default:
                    $icon = 'bi-info-circle-fill';
                    break;
            }
        }
        
        $stmt = $conn->prepare("
            INSERT INTO notifications (user_email, user_type, title, message, type, icon) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssss", $user_email, $user_type, $title, $message, $type, $icon);
        
        $success = $stmt->execute();
        $conn->close();
        
        return $success;
        
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Add a welcome notification for new students
 */
function addWelcomeNotification($user_email) {
    return addNotification(
        $user_email,
        'student',
        'Welcome to NCST!',
        'Your account has been created successfully. Complete your college registration to proceed with enrollment.',
        'success',
        'bi-heart-fill'
    );
}

/**
 * Add registration approval notification
 */
function addRegistrationApprovalNotification($user_email) {
    return addNotification(
        $user_email,
        'student',
        'Registration Approved!',
        'Congratulations! Your college registration has been approved. You can now proceed with enrollment.',
        'success',
        'bi-check-circle-fill'
    );
}

/**
 * Add registration rejection notification
 */
function addRegistrationRejectionNotification($user_email, $reason = '') {
    $message = 'Your registration has been rejected. Please review your information and resubmit.';
    if ($reason) {
        $message .= ' Reason: ' . $reason;
    }
    
    return addNotification(
        $user_email,
        'student',
        'Registration Needs Review',
        $message,
        'warning',
        'bi-exclamation-triangle-fill'
    );
}

/**
 * Add payment reminder notification
 */
function addPaymentReminderNotification($user_email, $amount = '', $due_date = '') {
    $message = 'Please settle your enrollment fees to avoid late payment penalties.';
    if ($amount) {
        $message = "Payment of $amount is due. " . $message;
    }
    if ($due_date) {
        $message .= " Due date: $due_date.";
    }
    
    return addNotification(
        $user_email,
        'student',
        'Payment Reminder',
        $message,
        'warning',
        'bi-credit-card-fill'
    );
}

/**
 * Add enrollment completion notification
 */
function addEnrollmentCompleteNotification($user_email) {
    return addNotification(
        $user_email,
        'student',
        'Enrollment Complete!',
        'Your enrollment has been completed successfully. Welcome to NCST! Check your enrolled subjects and class schedules.',
        'success',
        'bi-trophy-fill'
    );
}

/**
 * Add queue notification
 */
function addQueueNotification($user_email, $queue_number, $department) {
    return addNotification(
        $user_email,
        'student',
        'Queue Number Generated',
        "Your queue number is $queue_number for $department department. Please wait for your turn.",
        'info',
        'bi-clock-history'
    );
}

/**
 * Add system maintenance notification
 */
function addMaintenanceNotification($user_email) {
    return addNotification(
        $user_email,
        'student',
        'System Maintenance',
        'The enrollment system will undergo maintenance on [date]. Some services may be temporarily unavailable.',
        'info',
        'bi-gear-fill'
    );
}
?>
