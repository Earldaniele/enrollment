-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 17, 2025 at 11:50 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `enrollment_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `document_submissions`
--

CREATE TABLE `document_submissions` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `document_name` varchar(100) NOT NULL,
  `submission_status` enum('Missing','Submitted','Under Review','Approved','Rejected') DEFAULT 'Missing',
  `is_required` tinyint(1) DEFAULT 1,
  `submitted_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `document_submissions`
--

INSERT INTO `document_submissions` (`id`, `student_id`, `document_name`, `submission_status`, `is_required`, `submitted_date`, `created_at`, `updated_at`) VALUES
(1, '2025-00001', 'Form 138', 'Submitted', 1, NULL, '2025-08-13 17:19:18', '2025-08-13 17:19:18'),
(2, '2025-00001', 'PSA Birth Certificate', 'Submitted', 1, NULL, '2025-08-13 17:19:18', '2025-08-13 17:19:18'),
(3, '2025-00001', '2x2 ID Photo', 'Missing', 1, NULL, '2025-08-13 17:19:18', '2025-08-13 17:19:18'),
(4, '2024-61582', 'Certificate of Good Moral Character', 'Submitted', 1, NULL, '2025-08-13 17:19:18', '2025-08-13 17:19:18'),
(5, '2024-61582', '2x2 ID Photo', 'Submitted', 1, NULL, '2025-08-13 17:19:18', '2025-08-13 17:19:18'),
(6, '2025-00002', 'Form 138', 'Submitted', 1, '2025-08-17 21:46:53', '2025-08-17 16:32:31', '2025-08-17 21:46:53'),
(7, '2025-00002', 'PSA Birth Certificate', 'Submitted', 1, '2025-08-17 21:46:53', '2025-08-17 16:32:31', '2025-08-17 21:46:53'),
(8, '2025-00002', '2x2 ID Photo', 'Submitted', 1, '2025-08-17 21:46:53', '2025-08-17 16:32:31', '2025-08-17 21:46:53'),
(9, '2025-00002', 'Certificate of Good Moral Character', 'Submitted', 1, '2025-08-17 21:46:53', '2025-08-17 16:32:31', '2025-08-17 21:46:53'),
(10, '2025-00002', 'Medical Certificate', 'Submitted', 0, '2025-08-17 21:46:53', '2025-08-17 16:32:31', '2025-08-17 21:46:53'),
(11, '2025-00003', 'Form 137', 'Submitted', 1, NULL, '2025-08-17 16:48:25', '2025-08-17 16:48:25'),
(12, '2025-00003', 'Birth Certificate', 'Submitted', 1, NULL, '2025-08-17 16:48:25', '2025-08-17 16:48:25'),
(13, '2025-00004', 'Form 137', 'Missing', 1, NULL, '2025-08-17 16:48:25', '2025-08-17 16:48:25'),
(14, '2025-00005', 'Birth Certificate', 'Submitted', 1, NULL, '2025-08-17 16:48:25', '2025-08-17 16:48:25'),
(15, '2025-00007', 'Form 137', 'Missing', 1, NULL, '2025-08-17 17:05:49', '2025-08-17 17:05:49'),
(16, '2025-00007', 'Birth Certificate', 'Missing', 1, NULL, '2025-08-17 17:05:49', '2025-08-17 17:05:49'),
(17, '2025-00007', 'Good Moral Certificate', 'Missing', 1, NULL, '2025-08-17 17:05:49', '2025-08-17 17:05:49'),
(18, '2025-00007', '2x2 ID Picture', 'Missing', 1, NULL, '2025-08-17 17:05:49', '2025-08-17 17:05:49'),
(19, '2025-00007', 'Medical Certificate', 'Missing', 0, NULL, '2025-08-17 17:05:49', '2025-08-17 17:05:49'),
(20, '2025-00008', 'Form 137', 'Submitted', 1, '2025-08-17 21:48:35', '2025-08-17 21:03:12', '2025-08-17 21:48:35'),
(21, '2025-00008', 'Birth Certificate', 'Submitted', 1, '2025-08-17 21:48:35', '2025-08-17 21:03:12', '2025-08-17 21:48:35'),
(22, '2025-00008', 'Good Moral Certificate', 'Submitted', 1, '2025-08-17 21:48:35', '2025-08-17 21:03:12', '2025-08-17 21:48:35'),
(23, '2025-00008', '2x2 ID Picture', 'Submitted', 1, '2025-08-17 21:48:35', '2025-08-17 21:03:12', '2025-08-17 21:48:35'),
(24, '2025-00008', 'Medical Certificate', 'Submitted', 0, '2025-08-17 21:48:35', '2025-08-17 21:03:12', '2025-08-17 21:48:35'),
(25, '2025-00008', 'Form 137', 'Submitted', 1, '2025-08-17 21:48:35', '2025-08-17 21:42:34', '2025-08-17 21:48:35'),
(26, '2025-00008', 'Birth Certificate', 'Submitted', 1, '2025-08-17 21:48:35', '2025-08-17 21:42:34', '2025-08-17 21:48:35'),
(27, '2025-00008', 'Good Moral Certificate', 'Submitted', 1, '2025-08-17 21:48:35', '2025-08-17 21:42:34', '2025-08-17 21:48:35'),
(28, '2025-00008', '2x2 ID Picture', 'Submitted', 1, '2025-08-17 21:48:35', '2025-08-17 21:42:34', '2025-08-17 21:48:35'),
(29, '2025-00008', 'Medical Certificate', 'Submitted', 0, '2025-08-17 21:48:35', '2025-08-17 21:42:34', '2025-08-17 21:48:35');

-- --------------------------------------------------------

--
-- Table structure for table `document_validations`
--

CREATE TABLE `document_validations` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `document_name` varchar(200) NOT NULL,
  `status` enum('submitted','pending','missing','verified') DEFAULT 'pending',
  `remarks` text DEFAULT NULL,
  `validated_by` varchar(255) DEFAULT NULL,
  `validated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `document_validations`
--

INSERT INTO `document_validations` (`id`, `student_id`, `document_name`, `status`, `remarks`, `validated_by`, `validated_at`, `created_at`, `updated_at`) VALUES
(1, '2025-00001', 'Form 138 (Report Card)', 'submitted', NULL, 'NCST Registrar', '2025-08-13 15:40:08', '2025-08-13 15:40:08', '2025-08-13 15:40:08'),
(2, '2025-00001', 'PSA Birth Certificate', 'submitted', NULL, 'NCST Registrar', '2025-08-13 15:40:08', '2025-08-13 15:40:08', '2025-08-13 15:40:08'),
(3, '2025-00001', '2x2 ID Photo (2 copies)', 'submitted', NULL, 'NCST Registrar', '2025-08-13 15:40:08', '2025-08-13 15:40:08', '2025-08-13 15:40:08'),
(4, '2025-00001', 'Certificate of Good Moral Character', 'missing', NULL, NULL, NULL, '2025-08-13 15:40:08', '2025-08-13 15:40:08'),
(5, '2025-00001', 'Form 138 (Report Card)', 'submitted', NULL, 'NCST Registrar', '2025-08-13 15:51:28', '2025-08-13 15:51:28', '2025-08-13 15:51:28'),
(6, '2025-00001', 'PSA Birth Certificate', 'submitted', NULL, 'NCST Registrar', '2025-08-13 15:51:28', '2025-08-13 15:51:28', '2025-08-13 15:51:28'),
(7, '2025-00001', '2x2 ID Photo (2 copies)', 'submitted', NULL, 'NCST Registrar', '2025-08-13 15:51:28', '2025-08-13 15:51:28', '2025-08-13 15:51:28'),
(8, '2025-00001', 'Certificate of Good Moral Character', 'missing', NULL, NULL, NULL, '2025-08-13 15:51:28', '2025-08-13 15:51:28'),
(9, '2024-61583', 'Form 138 (Report Card)', 'verified', 'All grades verified and authentic', 'NCST Registrar', '2025-08-15 06:30:00', '2025-08-16 12:10:49', '2025-08-16 12:10:49'),
(10, '2024-61583', 'PSA Birth Certificate', 'verified', 'Document authenticated', 'NCST Registrar', '2025-08-15 06:30:00', '2025-08-16 12:10:49', '2025-08-16 12:10:49'),
(11, '2024-61583', '2x2 ID Photo (2 copies)', 'submitted', 'Photos clear and acceptable', 'NCST Registrar', '2025-08-15 06:30:00', '2025-08-16 12:10:49', '2025-08-16 12:10:49'),
(12, '2025-00002', 'Form 138 (Report Card)', 'verified', 'High school records complete', 'NCST Registrar', '2025-08-15 08:00:00', '2025-08-16 12:10:49', '2025-08-16 12:10:49'),
(13, '2025-00002', 'PSA Birth Certificate', 'verified', 'Birth certificate verified', 'NCST Registrar', '2025-08-15 08:00:00', '2025-08-16 12:10:49', '2025-08-16 12:10:49'),
(14, '2025-00002', '2x2 ID Photo (2 copies)', 'submitted', 'ID photos submitted', 'NCST Registrar', '2025-08-15 08:00:00', '2025-08-16 12:10:49', '2025-08-16 12:10:49'),
(15, '2024-61584', 'Transfer Credentials', 'verified', 'Previous school credentials verified', 'NCST Registrar', '2025-08-14 02:00:00', '2025-08-16 12:10:49', '2025-08-16 12:10:49'),
(16, '2024-61584', 'Certificate of Good Moral Character', 'verified', 'Good moral character verified', 'NCST Registrar', '2025-08-14 02:00:00', '2025-08-16 12:10:49', '2025-08-16 12:10:49');

-- --------------------------------------------------------

--
-- Table structure for table `enrolled_subjects`
--

CREATE TABLE `enrolled_subjects` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `grade` decimal(3,2) DEFAULT NULL,
  `status` enum('enrolled','dropped','completed') DEFAULT 'enrolled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrolled_subjects`
--

INSERT INTO `enrolled_subjects` (`id`, `student_id`, `subject_id`, `grade`, `status`, `created_at`) VALUES
(1, '2025-00001', 1, NULL, 'enrolled', '2025-08-11 13:11:21'),
(2, '2025-00001', 2, NULL, 'enrolled', '2025-08-11 13:11:21'),
(3, '2025-00001', 3, NULL, 'enrolled', '2025-08-11 13:11:21'),
(4, '2025-00001', 4, NULL, 'enrolled', '2025-08-11 13:11:21'),
(5, '2025-00001', 5, NULL, 'enrolled', '2025-08-11 13:11:21'),
(6, '2025-00001', 6, NULL, 'enrolled', '2025-08-11 13:11:21'),
(7, '2025-00001', 7, NULL, 'enrolled', '2025-08-11 13:11:21'),
(8, '2025-00001', 8, NULL, 'enrolled', '2025-08-11 13:11:21'),
(9, '2025-00004', 9, NULL, 'enrolled', '2025-08-16 09:48:06'),
(10, '2025-00004', 10, NULL, 'enrolled', '2025-08-16 09:48:06'),
(11, '2025-00004', 11, NULL, 'enrolled', '2025-08-16 09:48:06'),
(12, '2025-00004', 12, NULL, 'enrolled', '2025-08-16 09:48:06'),
(13, '2025-00004', 13, NULL, 'enrolled', '2025-08-16 09:48:06'),
(14, '2025-00004', 14, NULL, 'enrolled', '2025-08-16 09:48:06'),
(15, '2025-00004', 15, NULL, 'enrolled', '2025-08-16 09:48:06'),
(16, '2025-00004', 16, NULL, 'enrolled', '2025-08-16 09:48:06'),
(17, '2025-00002', 35, NULL, 'enrolled', '2025-08-17 15:59:13'),
(18, '2025-00002', 36, NULL, 'enrolled', '2025-08-17 15:59:13'),
(19, '2025-00002', 38, NULL, 'enrolled', '2025-08-17 15:59:13'),
(20, '2025-00008', 6, NULL, 'enrolled', '2025-08-17 21:42:34');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `section` varchar(10) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `year_level` enum('1st Year','2nd Year','3rd Year','4th Year','5th Year') NOT NULL,
  `semester` enum('1st Semester','2nd Semester','Summer') NOT NULL,
  `school_year` varchar(10) NOT NULL,
  `enrollment_status` enum('enrolled','dropped','graduated','on_leave') DEFAULT 'enrolled',
  `total_units` decimal(5,2) DEFAULT 0.00,
  `total_assessment` decimal(10,2) DEFAULT 0.00,
  `payment_status` enum('unpaid','partial','paid') DEFAULT 'unpaid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `student_id`, `section`, `section_id`, `year_level`, `semester`, `school_year`, `enrollment_status`, `total_units`, `total_assessment`, `payment_status`, `created_at`, `updated_at`) VALUES
(2, '2025-00004', NULL, NULL, '2nd Year', '1st Semester', '2024-2025', 'enrolled', 23.00, 25000.00, 'partial', '2025-08-16 09:47:57', '2025-08-17 18:56:29'),
(4, '2025-00002', '31A2', 29, '1st Year', '1st Semester', '2024-2025', 'enrolled', 9.00, 24700.00, 'paid', '2025-08-17 15:59:13', '2025-08-17 19:26:48'),
(5, '2025-00003', NULL, NULL, '1st Year', '1st Semester', '2024-2025', 'enrolled', 0.00, 25000.00, 'partial', '2025-08-17 17:01:42', '2025-08-17 17:03:34'),
(6, '2025-00005', NULL, NULL, '1st Year', '1st Semester', '2024-2025', 'enrolled', 0.00, 23000.00, 'unpaid', '2025-08-17 17:01:42', '2025-08-17 17:01:42'),
(7, '2025-00006', NULL, NULL, '1st Year', '1st Semester', '2024-2025', 'enrolled', 0.00, 25000.00, 'unpaid', '2025-08-17 17:01:42', '2025-08-17 17:01:42'),
(8, '2024-00001', NULL, NULL, '2nd Year', '1st Semester', '2024-2025', 'enrolled', 0.00, 25000.00, 'partial', '2025-08-17 17:01:42', '2025-08-17 17:02:06'),
(12, '2025-00007', NULL, NULL, '1st Year', '1st Semester', '2024-2025', 'enrolled', 0.00, 25000.00, 'unpaid', '2025-08-17 17:05:49', '2025-08-17 17:05:49'),
(13, '2025-00008', NULL, NULL, '1st Year', '1st Semester', '2024-2025', 'enrolled', 0.00, 25000.00, 'paid', '2025-08-17 21:03:12', '2025-08-17 21:43:18'),
(14, '2025-00008', '21E1', 6, '2nd Year', '2nd Semester', '2025-2026', 'enrolled', 3.00, 12566.66, 'paid', '2025-08-17 21:42:34', '2025-08-17 21:43:18');

--
-- Triggers `enrollments`
--
DELIMITER $$
CREATE TRIGGER `auto_create_documents_after_enrollment` AFTER INSERT ON `enrollments` FOR EACH ROW BEGIN
    
    INSERT IGNORE INTO document_submissions (student_id, document_name, submission_status, is_required, created_at, updated_at)
    VALUES
        (NEW.student_id, 'Form 137', 'Missing', 1, NOW(), NOW()),
        (NEW.student_id, 'Birth Certificate', 'Missing', 1, NOW(), NOW()),
        (NEW.student_id, 'Good Moral Certificate', 'Missing', 1, NOW(), NOW()),
        (NEW.student_id, '2x2 ID Picture', 'Missing', 1, NOW(), NOW()),
        (NEW.student_id, 'Medical Certificate', 'Missing', 0, NOW(), NOW());
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `user_type` enum('student','evaluator','admin') DEFAULT 'student',
  `title` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','success','warning','danger') DEFAULT 'info',
  `icon` varchar(50) DEFAULT 'bi-info-circle-fill',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_email`, `user_type`, `title`, `message`, `type`, `icon`, `is_read`, `created_at`, `updated_at`) VALUES
(1, 'student@example.com', 'student', 'Welcome to NCST!', 'Complete your college registration to proceed with enrollment.', 'info', 'bi-info-circle-fill', 0, '2025-08-11 13:48:12', '2025-08-16 13:48:12'),
(2, 'student@example.com', 'student', 'Registration Approved', 'Your college registration has been approved by the admissions office.', 'success', 'bi-check-circle-fill', 0, '2025-08-14 13:48:12', '2025-08-16 13:48:12'),
(3, 'student@example.com', 'student', 'Payment Reminder', 'Please settle your enrollment fees before the deadline.', 'warning', 'bi-exclamation-triangle-fill', 0, '2025-08-16 10:48:12', '2025-08-16 13:48:12'),
(4, 'justinlemuel@gmail.com', 'student', 'Welcome to NCST!', 'Complete your college registration to proceed with enrollment.', 'info', 'bi-info-circle-fill', 0, '2025-08-11 12:56:59', '2025-08-11 13:00:29'),
(5, 'justinlemuel@gmail.com', 'student', 'Registration Approved', 'Your college registration has been approved by the admissions office.', 'success', 'bi-check-circle-fill', 0, '2025-08-11 12:56:59', '2025-08-11 13:00:29'),
(6, 'justinlemuel@gmail.com', 'student', 'Payment Reminder', 'Please settle your enrollment fees before the deadline.', 'warning', 'bi-exclamation-triangle-fill', 0, '2025-08-11 12:56:59', '2025-08-11 13:00:29'),
(7, 'cashier@ncst.edu.ph', '', 'Welcome to NCST Cashier System', 'You now have access to the cashier dashboard and payment management tools.', 'success', 'bi-check-circle-fill', 0, '2025-08-16 12:18:11', '2025-08-16 12:18:11'),
(8, 'cashier@ncst.edu.ph', '', 'New Payment Received', 'Student 2024-61582 has made a payment of 7,000.00. Please verify and process.', 'info', 'bi-credit-card-fill', 0, '2025-08-16 12:18:11', '2025-08-16 12:18:11'),
(9, 'registrar@ncst.edu.ph', '', 'Document Verification Required', 'Please verify documents for student ID: 2025-00001', 'warning', 'bi-exclamation-triangle-fill', 0, '2025-08-16 12:18:11', '2025-08-16 12:18:11'),
(10, 'evaluator@ncst.edu.ph', '', 'New Registration for Evaluation', 'A new student registration requires your evaluation and approval.', 'info', 'bi-person-check-fill', 0, '2025-08-16 12:18:11', '2025-08-16 12:18:11'),
(11, 'justin@gmail.com', 'student', 'System Test', 'This is a test notification to verify the notification system is working properly.', 'info', 'bi-bell-fill', 1, '2025-08-09 07:50:37', '2025-08-16 13:50:37'),
(12, 'maria.santos@student.edu.ph', 'student', 'Payment Reminder', 'Your tuition payment is due. Please visit the cashier office.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:38:28', '2025-08-16 12:38:28'),
(13, 'registrar@ncst.edu.ph', 'evaluator', 'Test Staff Notification', 'This is a test notification for staff members.', 'info', 'bi-bell-fill', 0, '2025-08-16 12:38:28', '2025-08-16 12:38:28'),
(14, 'justin@gmail.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 1, '2025-08-10 07:50:37', '2025-08-16 13:50:37'),
(15, 'maria.santos@student.edu.ph', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:50:11', '2025-08-16 12:50:11'),
(16, 'anna.garcia@student.edu.ph', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:50:11', '2025-08-16 12:50:11'),
(17, 'jose.reyes@student.edu.ph', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:50:11', '2025-08-16 12:50:11'),
(18, 'lisa.torres@student.edu.ph', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:50:11', '2025-08-16 12:50:11'),
(19, 'john.new@example.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:50:11', '2025-08-16 12:50:11'),
(20, 'anna.old@example.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:50:11', '2025-08-16 12:50:11'),
(21, 'carlos.shift@example.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:50:11', '2025-08-16 12:50:11'),
(22, 'lisa.shift@example.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:50:11', '2025-08-16 12:50:11'),
(23, 'mark.shift@example.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:50:11', '2025-08-16 12:50:11'),
(24, 'justin@gmail.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 1, '2025-08-11 07:50:37', '2025-08-16 13:50:37'),
(25, 'maria.santos@student.edu.ph', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:50:55', '2025-08-16 12:50:55'),
(26, 'anna.garcia@student.edu.ph', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:50:55', '2025-08-16 12:50:55'),
(27, 'jose.reyes@student.edu.ph', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:50:55', '2025-08-16 12:50:55'),
(28, 'lisa.torres@student.edu.ph', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:50:55', '2025-08-16 12:50:55'),
(29, 'john.new@example.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:50:55', '2025-08-16 12:50:55'),
(30, 'anna.old@example.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:50:55', '2025-08-16 12:50:55'),
(31, 'carlos.shift@example.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:50:55', '2025-08-16 12:50:55'),
(32, 'lisa.shift@example.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:50:55', '2025-08-16 12:50:55'),
(33, 'mark.shift@example.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:50:55', '2025-08-16 12:50:55'),
(34, 'justin@gmail.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 1, '2025-08-12 07:50:37', '2025-08-16 13:50:37'),
(35, 'maria.santos@student.edu.ph', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:53:30', '2025-08-16 12:53:30'),
(36, 'anna.garcia@student.edu.ph', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:53:30', '2025-08-16 12:53:30'),
(37, 'jose.reyes@student.edu.ph', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:53:30', '2025-08-16 12:53:30'),
(38, 'lisa.torres@student.edu.ph', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:53:30', '2025-08-16 12:53:30'),
(39, 'john.new@example.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:53:30', '2025-08-16 12:53:30'),
(40, 'anna.old@example.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:53:30', '2025-08-16 12:53:30'),
(41, 'carlos.shift@example.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:53:30', '2025-08-16 12:53:30'),
(42, 'lisa.shift@example.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:53:30', '2025-08-16 12:53:30'),
(43, 'mark.shift@example.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:53:30', '2025-08-16 12:53:30'),
(44, 'justin@gmail.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 1, '2025-08-13 07:50:37', '2025-08-16 13:50:37'),
(45, 'maria.santos@student.edu.ph', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:54:45', '2025-08-16 12:54:45'),
(46, 'anna.garcia@student.edu.ph', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:54:45', '2025-08-16 12:54:45'),
(47, 'jose.reyes@student.edu.ph', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:54:45', '2025-08-16 12:54:45'),
(48, 'lisa.torres@student.edu.ph', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:54:45', '2025-08-16 12:54:45'),
(49, 'john.new@example.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:54:45', '2025-08-16 12:54:45'),
(50, 'anna.old@example.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:54:45', '2025-08-16 12:54:45'),
(51, 'carlos.shift@example.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:54:45', '2025-08-16 12:54:45'),
(52, 'lisa.shift@example.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:54:45', '2025-08-16 12:54:45'),
(53, 'mark.shift@example.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:54:45', '2025-08-16 12:54:45'),
(54, 'justin@gmail.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 1, '2025-08-14 07:50:37', '2025-08-16 13:50:37'),
(55, 'maria.santos@student.edu.ph', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:55:30', '2025-08-16 12:55:30'),
(56, 'anna.garcia@student.edu.ph', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:55:30', '2025-08-16 12:55:30'),
(57, 'jose.reyes@student.edu.ph', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:55:30', '2025-08-16 12:55:30'),
(58, 'lisa.torres@student.edu.ph', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:55:30', '2025-08-16 12:55:30'),
(59, 'john.new@example.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:55:30', '2025-08-16 12:55:30'),
(60, 'anna.old@example.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:55:30', '2025-08-16 12:55:30'),
(61, 'carlos.shift@example.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:55:30', '2025-08-16 12:55:30'),
(62, 'lisa.shift@example.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:55:30', '2025-08-16 12:55:30'),
(63, 'mark.shift@example.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 12:55:30', '2025-08-16 12:55:30'),
(64, 'maria.santos@student.edu.ph', 'student', 'Payment Verified', 'Your recent payment has been verified and processed successfully. Thank you for your payment!', 'success', 'bi-bell-fill', 0, '2025-08-16 12:55:47', '2025-08-16 12:55:47'),
(65, 'justin@gmail.com', 'student', 'Payment Verified', 'Your recent payment has been verified and processed successfully. Thank you for your payment!', 'success', 'bi-bell-fill', 1, '2025-08-15 07:50:37', '2025-08-16 13:50:37'),
(66, 'mark.shift@example.com', 'student', 'Payment Verified', 'Your recent payment has been verified and processed successfully. Thank you for your payment!', 'success', 'bi-bell-fill', 0, '2025-08-16 12:55:47', '2025-08-16 12:55:47'),
(67, 'justin@gmail.com', 'student', 'Important System Notice', '1313', 'info', 'bi-bell-fill', 1, '2025-08-16 07:50:37', '2025-08-16 13:50:37'),
(68, 'maria.santos@student.edu.ph', 'student', 'Important System Notice', '1313', 'info', 'bi-bell-fill', 0, '2025-08-16 12:55:57', '2025-08-16 12:55:57'),
(69, 'anna.garcia@student.edu.ph', 'student', 'Important System Notice', '1313', 'info', 'bi-bell-fill', 0, '2025-08-16 12:55:57', '2025-08-16 12:55:57'),
(70, 'jose.reyes@student.edu.ph', 'student', 'Important System Notice', '1313', 'info', 'bi-bell-fill', 0, '2025-08-16 12:55:57', '2025-08-16 12:55:57'),
(71, 'lisa.torres@student.edu.ph', 'student', 'Important System Notice', '1313', 'info', 'bi-bell-fill', 0, '2025-08-16 12:55:57', '2025-08-16 12:55:57'),
(72, 'john.new@example.com', 'student', 'Important System Notice', '1313', 'info', 'bi-bell-fill', 0, '2025-08-16 12:55:57', '2025-08-16 12:55:57'),
(73, 'anna.old@example.com', 'student', 'Important System Notice', '1313', 'info', 'bi-bell-fill', 0, '2025-08-16 12:55:57', '2025-08-16 12:55:57'),
(74, 'carlos.shift@example.com', 'student', 'Important System Notice', '1313', 'info', 'bi-bell-fill', 0, '2025-08-16 12:55:57', '2025-08-16 12:55:57'),
(75, 'lisa.shift@example.com', 'student', 'Important System Notice', '1313', 'info', 'bi-bell-fill', 0, '2025-08-16 12:55:57', '2025-08-16 12:55:57'),
(76, 'mark.shift@example.com', 'student', 'Important System Notice', '1313', 'info', 'bi-bell-fill', 0, '2025-08-16 12:55:57', '2025-08-16 12:55:57'),
(78, 'maria.santos@student.edu.ph', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 13:17:43', '2025-08-16 13:17:43'),
(79, 'anna.garcia@student.edu.ph', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 13:17:43', '2025-08-16 13:17:43'),
(80, 'jose.reyes@student.edu.ph', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 13:17:43', '2025-08-16 13:17:43'),
(81, 'lisa.torres@student.edu.ph', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 13:17:43', '2025-08-16 13:17:43'),
(82, 'john.new@example.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 13:17:43', '2025-08-16 13:17:43'),
(83, 'anna.old@example.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 13:17:43', '2025-08-16 13:17:43'),
(84, 'carlos.shift@example.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 13:17:43', '2025-08-16 13:17:43'),
(85, 'lisa.shift@example.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 13:17:43', '2025-08-16 13:17:43'),
(86, 'mark.shift@example.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 13:17:43', '2025-08-16 13:17:43'),
(88, 'cashier@ncst.edu.ph', 'evaluator', 'Payment Approved', 'Student payment of ₱15,000 has been approved and processed successfully.', 'success', 'bi-check-circle-fill', 0, '2025-08-16 13:24:55', '2025-08-16 13:24:55'),
(89, 'cashier@ncst.edu.ph', 'evaluator', 'System Reminder', 'Please process pending payment verifications before end of business day.', 'warning', 'bi-exclamation-triangle-fill', 0, '2025-08-16 13:24:55', '2025-08-16 13:24:55'),
(90, 'justin@gmail.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 14:03:41', '2025-08-16 14:03:41'),
(91, 'maria.santos@student.edu.ph', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 14:03:41', '2025-08-16 14:03:41'),
(92, 'anna.garcia@student.edu.ph', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 14:03:41', '2025-08-16 14:03:41'),
(93, 'jose.reyes@student.edu.ph', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 14:03:41', '2025-08-16 14:03:41'),
(94, 'lisa.torres@student.edu.ph', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 14:03:41', '2025-08-16 14:03:41'),
(95, 'john.new@example.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 14:03:41', '2025-08-16 14:03:41'),
(96, 'anna.old@example.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 14:03:41', '2025-08-16 14:03:41'),
(97, 'carlos.shift@example.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 14:03:41', '2025-08-16 14:03:41'),
(98, 'lisa.shift@example.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 14:03:41', '2025-08-16 14:03:41'),
(99, 'mark.shift@example.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-16 14:03:41', '2025-08-16 14:03:41'),
(100, 'maria.santos@student.edu.ph', 'student', 'Payment Verified', 'Your recent payment has been verified and processed successfully. Thank you for your payment!', 'success', 'bi-bell-fill', 0, '2025-08-16 14:03:45', '2025-08-16 14:03:45'),
(101, 'justin@gmail.com', 'student', 'Payment Verified', 'Your recent payment has been verified and processed successfully. Thank you for your payment!', 'success', 'bi-bell-fill', 1, '2025-08-16 14:03:45', '2025-08-16 14:04:25'),
(102, 'mark.shift@example.com', 'student', 'Payment Verified', 'Your recent payment has been verified and processed successfully. Thank you for your payment!', 'success', 'bi-bell-fill', 0, '2025-08-16 14:03:45', '2025-08-16 14:03:45'),
(104, 'maria.santos@student.edu.ph', 'student', 'Important System Notice', '43124124', 'info', 'bi-bell-fill', 0, '2025-08-16 14:03:51', '2025-08-16 14:03:51'),
(105, 'anna.garcia@student.edu.ph', 'student', 'Important System Notice', '43124124', 'info', 'bi-bell-fill', 0, '2025-08-16 14:03:51', '2025-08-16 14:03:51'),
(106, 'jose.reyes@student.edu.ph', 'student', 'Important System Notice', '43124124', 'info', 'bi-bell-fill', 0, '2025-08-16 14:03:51', '2025-08-16 14:03:51'),
(107, 'lisa.torres@student.edu.ph', 'student', 'Important System Notice', '43124124', 'info', 'bi-bell-fill', 0, '2025-08-16 14:03:51', '2025-08-16 14:03:51'),
(108, 'john.new@example.com', 'student', 'Important System Notice', '43124124', 'info', 'bi-bell-fill', 0, '2025-08-16 14:03:51', '2025-08-16 14:03:51'),
(109, 'anna.old@example.com', 'student', 'Important System Notice', '43124124', 'info', 'bi-bell-fill', 0, '2025-08-16 14:03:51', '2025-08-16 14:03:51'),
(110, 'carlos.shift@example.com', 'student', 'Important System Notice', '43124124', 'info', 'bi-bell-fill', 0, '2025-08-16 14:03:51', '2025-08-16 14:03:51'),
(111, 'lisa.shift@example.com', 'student', 'Important System Notice', '43124124', 'info', 'bi-bell-fill', 0, '2025-08-16 14:03:51', '2025-08-16 14:03:51'),
(112, 'mark.shift@example.com', 'student', 'Important System Notice', '43124124', 'info', 'bi-bell-fill', 0, '2025-08-16 14:03:51', '2025-08-16 14:03:51'),
(113, 'registrar@ncst.edu.ph', 'evaluator', 'New Student Registration', 'New registration from justin Gonzales domingo (ID: 2025-00002) for Bachelor of Science in Computer Science (BSCS). Please review and approve.', 'info', 'bi-bell-fill', 0, '2025-08-17 13:33:35', '2025-08-17 13:33:35'),
(115, 'maria.santos@email.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-17 19:37:49', '2025-08-17 19:37:49'),
(116, 'jose.delacruz@email.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-17 19:37:49', '2025-08-17 19:37:49'),
(117, 'ana.garcia@email.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-17 19:37:49', '2025-08-17 19:37:49'),
(118, 'carlos.reyes@email.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-17 19:37:49', '2025-08-17 19:37:49'),
(119, 'pedro.gonzales@email.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-17 19:37:49', '2025-08-17 19:37:49'),
(120, 'test.student@email.com', 'student', 'Payment Reminder', 'This is a friendly reminder that your tuition payment is still pending. Please visit the cashier office to complete your payment.', 'warning', 'bi-bell-fill', 0, '2025-08-17 19:37:49', '2025-08-17 19:37:49'),
(121, 'registrar@ncst.edu.ph', 'evaluator', 'New Student Registration', 'New registration from fritz fabula cholo (ID: 2025-00008) for Bachelor of Science in Information Technology (BSIT). Please review and approve.', 'info', 'bi-bell-fill', 0, '2025-08-17 20:26:23', '2025-08-17 20:26:23'),
(122, 'registrar@ncst.edu.ph', 'evaluator', 'New Student Registration', 'New registration from fritz fabula cholo (ID: 2025-00008) for Bachelor of Science in Information Technology (BSIT). Please review and approve.', 'info', 'bi-bell-fill', 0, '2025-08-17 20:32:32', '2025-08-17 20:32:32'),
(123, 'registrar@ncst.edu.ph', 'evaluator', 'New Student Registration', 'New registration from fritz alvarez cholo (ID: 2025-00008) for Bachelor of Science in Information Technology (BSIT). Please review and approve.', 'info', 'bi-bell-fill', 0, '2025-08-17 20:44:17', '2025-08-17 20:44:17'),
(124, 'registrar@ncst.edu.ph', 'evaluator', 'New Student Registration', 'New registration from fritz alvarez cholo (ID: 2025-00008) for Bachelor of Science in Information Technology (BSIT). Please review and approve.', 'info', 'bi-bell-fill', 0, '2025-08-17 20:45:30', '2025-08-17 20:45:30'),
(125, 'registrar@ncst.edu.ph', 'evaluator', 'New Student Registration', 'New registration from fritz alvarez cholo (ID: 2025-00008) for Bachelor of Science in Information Technology (BSIT). Please review and approve.', 'info', 'bi-bell-fill', 0, '2025-08-17 20:46:30', '2025-08-17 20:46:30'),
(126, 'registrar@ncst.edu.ph', 'evaluator', 'New Student Registration', 'New registration from fritz alvarez cholo (ID: 2025-00008) for Bachelor of Science in Information Technology (BSIT). Please review and approve.', 'info', 'bi-bell-fill', 0, '2025-08-17 20:51:20', '2025-08-17 20:51:20'),
(127, 'registrar@ncst.edu.ph', 'evaluator', 'New Student Registration', 'New registration from fritz alvarez cholo (ID: 2025-00008) for Bachelor of Science in Electronics Engineering (BSEE). Please review and approve.', 'info', 'bi-bell-fill', 0, '2025-08-17 20:54:03', '2025-08-17 20:54:03'),
(128, 'registrar@ncst.edu.ph', 'evaluator', 'New Student Registration', 'New registration from fritz alvarez cholo (ID: 2025-00008) for Bachelor of Science in Electronics Engineering (BSEE). Please review and approve.', 'info', 'bi-bell-fill', 0, '2025-08-17 20:57:55', '2025-08-17 20:57:55'),
(129, 'registrar@ncst.edu.ph', 'evaluator', 'New Student Registration', 'New registration from fritz alvarez cholo (ID: 2025-00008) for Bachelor of Science in Electronics Engineering (BSEE). Please review and approve.', 'info', 'bi-bell-fill', 0, '2025-08-17 21:00:36', '2025-08-17 21:00:36'),
(130, 'registrar@ncst.edu.ph', 'evaluator', 'New Student Registration', 'New registration from fritz alvarez cholo (ID: 2025-00008) for Bachelor of Science in Information Technology (BSIT). Please review and approve.', 'info', 'bi-bell-fill', 0, '2025-08-17 21:01:40', '2025-08-17 21:01:40');

-- --------------------------------------------------------

--
-- Table structure for table `queue_tickets`
--

CREATE TABLE `queue_tickets` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `department` enum('Registrar','Treasury','Enrollment') NOT NULL,
  `queue_number` varchar(10) NOT NULL,
  `qr_data` text DEFAULT NULL,
  `status` enum('waiting','ready','in_progress','completed','cancelled','expired') DEFAULT 'waiting',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `expires_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `queue_tickets`
--

INSERT INTO `queue_tickets` (`id`, `student_id`, `department`, `queue_number`, `qr_data`, `status`, `created_at`, `updated_at`, `expires_at`) VALUES
(1, '2025-00001', 'Registrar', 'RG-001', '{\"queue_number\":\"RG-001\",\"student_id\":\"2025-00001\",\"department\":\"registrar\",\"timestamp\":1754902441}', 'cancelled', '2025-08-11 08:54:01', '2025-08-11 11:06:18', '2025-08-11 09:24:01'),
(2, '2025-00001', 'Treasury', 'TR-001', '{\"queue_number\":\"TR-001\",\"student_id\":\"2025-00001\",\"department\":\"treasury\",\"timestamp\":1754910113}', 'cancelled', '2025-08-11 11:01:53', '2025-08-11 11:06:15', '2025-08-11 11:31:53'),
(3, '2025-00001', 'Treasury', 'TR-002', '{\"queue_number\":\"TR-002\",\"student_id\":\"2025-00001\",\"department\":\"treasury\",\"timestamp\":1754910441}', 'cancelled', '2025-08-11 11:07:21', '2025-08-11 11:08:01', '2025-08-11 11:37:21'),
(4, '2025-00001', 'Registrar', 'RG-002', '{\"queue_number\":\"RG-002\",\"student_id\":\"2025-00001\",\"department\":\"registrar\",\"timestamp\":1754910485}', 'cancelled', '2025-08-11 11:08:05', '2025-08-11 11:08:35', '2025-08-11 11:38:05'),
(5, '2025-00001', 'Registrar', 'RG-003', '{\"queue_number\":\"RG-003\",\"student_id\":\"2025-00001\",\"department\":\"registrar\",\"timestamp\":1754910560}', 'cancelled', '2025-08-11 11:09:20', '2025-08-11 11:09:27', '2025-08-11 11:39:20'),
(6, '2025-00001', 'Treasury', 'TR-003', '{\"queue_number\":\"TR-003\",\"student_id\":\"2025-00001\",\"department\":\"treasury\",\"timestamp\":1754910573}', 'cancelled', '2025-08-11 11:09:33', '2025-08-11 11:40:39', '2025-08-11 11:39:33'),
(7, '2025-00001', 'Enrollment', 'EN-001', '{\"queue_number\":\"EN-001\",\"student_id\":\"2025-00001\",\"department\":\"enrollment\",\"timestamp\":1754910859}', 'cancelled', '2025-08-11 11:14:19', '2025-08-11 12:37:02', '2025-08-11 11:44:19'),
(8, '2025-00001', 'Treasury', 'TR-004', '{\"queue_number\":\"TR-004\",\"student_id\":\"2025-00001\",\"department\":\"treasury\",\"timestamp\":1754912562}', 'cancelled', '2025-08-11 11:42:42', '2025-08-11 12:37:00', '2025-08-11 12:12:42'),
(9, '2025-00001', 'Registrar', 'RG-004', '{\"queue_number\":\"RG-004\",\"student_id\":\"2025-00001\",\"department\":\"registrar\",\"timestamp\":1754912589}', 'cancelled', '2025-08-11 11:43:09', '2025-08-11 12:36:54', '2025-08-11 12:13:09'),
(10, '2025-00001', 'Treasury', 'TR-005', '{\"queue_number\":\"TR-005\",\"student_id\":\"2025-00001\",\"department\":\"treasury\",\"timestamp\":1754917023}', 'cancelled', '2025-08-11 12:57:03', '2025-08-11 12:57:55', '2025-08-11 13:27:03'),
(11, '2025-00001', 'Enrollment', 'EN-002', '{\"queue_number\":\"EN-002\",\"student_id\":\"2025-00001\",\"department\":\"enrollment\",\"timestamp\":1754923271}', 'cancelled', '2025-08-11 14:41:11', '2025-08-11 14:44:56', '2025-08-11 15:11:11'),
(12, '2025-00001', 'Registrar', 'RG-005', '{\"queue_number\":\"RG-005\",\"student_id\":\"2025-00001\",\"department\":\"registrar\",\"timestamp\":1754923516}', 'cancelled', '2025-08-11 14:45:16', '2025-08-11 14:54:20', '2025-08-11 14:47:16'),
(13, '2025-00001', 'Treasury', 'TR-006', '{\"queue_number\":\"TR-006\",\"student_id\":\"2025-00001\",\"department\":\"treasury\",\"timestamp\":1754923527}', 'cancelled', '2025-08-11 14:45:27', '2025-08-11 16:11:55', '2025-08-11 14:47:27'),
(14, '2025-00001', 'Enrollment', 'EN-003', '{\"queue_number\":\"EN-003\",\"student_id\":\"2025-00001\",\"department\":\"enrollment\",\"timestamp\":1754924052}', 'cancelled', '2025-08-11 14:54:12', '2025-08-11 16:11:58', '2025-08-11 14:56:12'),
(15, '2025-00001', 'Registrar', 'GN-001', '{\"queue_number\":\"GN-001\",\"student_id\":\"2025-00001\",\"department\":\"Registrar\",\"timestamp\":1754934561}', 'cancelled', '2025-08-11 17:49:21', '2025-08-11 17:51:13', '2025-08-11 17:51:21'),
(16, '2025-00001', 'Treasury', 'GN-001', '{\"queue_number\":\"GN-001\",\"student_id\":\"2025-00001\",\"department\":\"Treasury\",\"timestamp\":1754934567}', 'cancelled', '2025-08-11 17:49:27', '2025-08-11 17:51:10', '2025-08-11 17:51:27'),
(17, '2025-00001', 'Treasury', 'GN-002', '{\"queue_number\":\"GN-002\",\"student_id\":\"2025-00001\",\"department\":\"Treasury\",\"timestamp\":1754934677}', 'cancelled', '2025-08-11 17:51:17', '2025-08-11 17:52:51', '2025-08-11 17:53:17'),
(18, '2025-00001', 'Treasury', 'GN-003', '{\"queue_number\":\"GN-003\",\"student_id\":\"2025-00001\",\"department\":\"Treasury\",\"timestamp\":1754934776}', 'cancelled', '2025-08-11 17:52:56', '2025-08-11 17:53:57', '2025-08-11 17:54:56'),
(19, '2025-00001', 'Enrollment', 'GN-001', '{\"queue_number\":\"GN-001\",\"student_id\":\"2025-00001\",\"department\":\"Enrollment\",\"timestamp\":1754934842}', 'cancelled', '2025-08-11 17:54:02', '2025-08-11 17:55:53', '2025-08-11 17:56:02'),
(20, '2025-00001', 'Treasury', 'GN-004', '{\"queue_number\":\"GN-004\",\"student_id\":\"2025-00001\",\"department\":\"Treasury\",\"timestamp\":1754934875}', 'cancelled', '2025-08-11 17:54:35', '2025-08-11 17:55:56', '2025-08-11 17:56:35'),
(21, '2025-00001', 'Treasury', 'GN-005', '{\"queue_number\":\"GN-005\",\"student_id\":\"2025-00001\",\"department\":\"Treasury\",\"timestamp\":1754934959}', 'cancelled', '2025-08-11 17:55:59', '2025-08-11 17:57:38', '2025-08-11 17:57:59'),
(22, '2025-00001', 'Treasury', 'GN-006', '{\"queue_number\":\"GN-006\",\"student_id\":\"2025-00001\",\"department\":\"Treasury\",\"timestamp\":1754935060}', 'cancelled', '2025-08-11 17:57:40', '2025-08-11 17:59:56', '2025-08-11 17:59:40'),
(23, '2025-00001', 'Registrar', 'GN-002', '{\"queue_number\":\"GN-002\",\"student_id\":\"2025-00001\",\"department\":\"Registrar\",\"timestamp\":1754935081}', 'cancelled', '2025-08-11 17:58:01', '2025-08-11 17:59:59', '2025-08-11 18:00:01'),
(24, '2025-00001', 'Enrollment', 'GN-002', '{\"queue_number\":\"GN-002\",\"student_id\":\"2025-00001\",\"department\":\"Enrollment\",\"timestamp\":1754935104}', 'cancelled', '2025-08-11 17:58:24', '2025-08-11 18:01:12', '2025-08-11 18:00:24'),
(25, '2025-00001', 'Treasury', 'GN-007', '{\"queue_number\":\"GN-007\",\"student_id\":\"2025-00001\",\"department\":\"Treasury\",\"timestamp\":1754935274}', 'cancelled', '2025-08-11 18:01:14', '2025-08-11 18:02:11', '2025-08-11 18:03:14'),
(26, '2025-00001', 'Treasury', 'GN-008', '{\"queue_number\":\"GN-008\",\"student_id\":\"2025-00001\",\"department\":\"Treasury\",\"timestamp\":1754935337}', 'cancelled', '2025-08-11 18:02:17', '2025-08-11 18:04:44', '2025-08-11 18:04:17'),
(27, '2025-00001', 'Treasury', 'GN-009', '{\"queue_number\":\"GN-009\",\"student_id\":\"2025-00001\",\"department\":\"Treasury\",\"timestamp\":1754935487}', 'cancelled', '2025-08-11 18:04:47', '2025-08-11 18:05:29', '2025-08-11 18:06:47'),
(28, '2025-00001', 'Enrollment', 'GN-003', '{\"queue_number\":\"GN-003\",\"student_id\":\"2025-00001\",\"department\":\"Enrollment\",\"timestamp\":1754935496}', 'cancelled', '2025-08-11 18:04:56', '2025-08-11 18:05:33', '2025-08-11 18:06:56'),
(29, '2025-00001', 'Registrar', 'GN-003', '{\"queue_number\":\"GN-003\",\"student_id\":\"2025-00001\",\"department\":\"Registrar\",\"timestamp\":1754935515}', 'cancelled', '2025-08-11 18:05:15', '2025-08-11 18:05:31', '2025-08-11 18:07:15'),
(30, '2025-00001', 'Registrar', 'GN-004', '{\"queue_number\":\"GN-004\",\"student_id\":\"2025-00001\",\"department\":\"Registrar\",\"timestamp\":1754935536}', 'cancelled', '2025-08-11 18:05:36', '2025-08-11 18:06:22', '2025-08-11 18:07:36'),
(31, '2025-00001', 'Treasury', 'GN-010', '{\"queue_number\":\"GN-010\",\"student_id\":\"2025-00001\",\"department\":\"Treasury\",\"timestamp\":1754935587}', 'cancelled', '2025-08-11 18:06:27', '2025-08-11 18:06:34', '2025-08-11 18:08:27'),
(32, '2025-00001', 'Registrar', 'GN-005', '{\"queue_number\":\"GN-005\",\"student_id\":\"2025-00001\",\"department\":\"Registrar\",\"timestamp\":1754935624}', 'cancelled', '2025-08-11 18:07:04', '2025-08-11 18:07:33', '2025-08-11 18:09:04'),
(33, '2025-00001', 'Treasury', 'GN-011', '{\"queue_number\":\"GN-011\",\"student_id\":\"2025-00001\",\"department\":\"Treasury\",\"timestamp\":1754935656}', 'cancelled', '2025-08-11 18:07:36', '2025-08-11 18:09:03', '2025-08-11 18:09:36'),
(34, '2025-00001', 'Registrar', 'GN-006', '{\"queue_number\":\"GN-006\",\"student_id\":\"2025-00001\",\"department\":\"Registrar\",\"timestamp\":1754935746}', 'cancelled', '2025-08-11 18:09:06', '2025-08-11 18:09:14', '2025-08-11 18:11:06'),
(35, '2025-00001', 'Treasury', 'GN-012', '{\"queue_number\":\"GN-012\",\"student_id\":\"2025-00001\",\"department\":\"Treasury\",\"timestamp\":1754935757}', 'cancelled', '2025-08-11 18:09:17', '2025-08-11 18:09:24', '2025-08-11 18:11:17'),
(36, '2025-00001', 'Treasury', 'GN-013', '{\"queue_number\":\"GN-013\",\"student_id\":\"2025-00001\",\"department\":\"Treasury\",\"timestamp\":1754935843}', 'cancelled', '2025-08-11 18:10:43', '2025-08-11 18:10:51', '2025-08-11 18:12:43'),
(37, '2025-00001', 'Enrollment', 'GN-004', '{\"queue_number\":\"GN-004\",\"student_id\":\"2025-00001\",\"department\":\"Enrollment\",\"timestamp\":1754935854}', 'cancelled', '2025-08-11 18:10:54', '2025-08-11 18:15:57', '2025-08-11 18:12:54'),
(38, '2025-00001', 'Enrollment', 'GN-005', '{\"queue_number\":\"GN-005\",\"student_id\":\"2025-00001\",\"department\":\"Enrollment\",\"timestamp\":1754936159}', 'cancelled', '2025-08-11 18:15:59', '2025-08-11 18:21:02', '2025-08-11 18:17:59'),
(39, '2025-00001', 'Treasury', 'GN-014', '{\"queue_number\":\"GN-014\",\"student_id\":\"2025-00001\",\"department\":\"Treasury\",\"timestamp\":1754936464}', 'cancelled', '2025-08-11 18:21:04', '2025-08-11 18:26:24', '2025-08-11 18:23:04'),
(40, '2025-00001', 'Registrar', 'GN-007', '{\"queue_number\":\"GN-007\",\"student_id\":\"2025-00001\",\"department\":\"Registrar\",\"timestamp\":1754936770}', 'cancelled', '2025-08-11 18:26:10', '2025-08-11 18:26:31', '2025-08-11 18:28:10'),
(41, '2025-00001', 'Enrollment', 'GN-006', '{\"queue_number\":\"GN-006\",\"student_id\":\"2025-00001\",\"department\":\"Enrollment\",\"timestamp\":1754936798}', 'cancelled', '2025-08-11 18:26:38', '2025-08-11 18:32:30', '2025-08-11 18:28:38'),
(42, '2025-00001', 'Treasury', 'GN-015', '{\"queue_number\":\"GN-015\",\"student_id\":\"2025-00001\",\"department\":\"Treasury\",\"timestamp\":1754936820}', 'cancelled', '2025-08-11 18:27:00', '2025-08-11 18:32:36', '2025-08-11 18:29:00'),
(43, '2025-00001', 'Registrar', 'GN-008', '{\"queue_number\":\"GN-008\",\"student_id\":\"2025-00001\",\"department\":\"Registrar\",\"timestamp\":1754937158}', 'cancelled', '2025-08-11 18:32:38', '2025-08-11 18:34:28', '2025-08-11 18:34:38'),
(44, '2025-00001', 'Enrollment', 'GN-007', '{\"queue_number\":\"GN-007\",\"student_id\":\"2025-00001\",\"department\":\"Enrollment\",\"timestamp\":1754937187}', 'cancelled', '2025-08-11 18:33:07', '2025-08-11 18:34:33', '2025-08-11 18:35:07'),
(45, '2025-00001', 'Treasury', 'GN-016', '{\"queue_number\":\"GN-016\",\"student_id\":\"2025-00001\",\"department\":\"Treasury\",\"timestamp\":1754937263}', 'cancelled', '2025-08-11 18:34:23', '2025-08-11 18:34:31', '2025-08-11 18:36:23'),
(46, '2025-00001', 'Registrar', 'GN-009', '{\"queue_number\":\"GN-009\",\"student_id\":\"2025-00001\",\"department\":\"Registrar\",\"timestamp\":1754937499}', 'cancelled', '2025-08-11 18:38:19', '2025-08-11 18:38:37', '2025-08-11 18:40:19'),
(47, '2025-00001', 'Treasury', 'GN-017', '{\"queue_number\":\"GN-017\",\"student_id\":\"2025-00001\",\"department\":\"Treasury\",\"timestamp\":1754937521}', 'cancelled', '2025-08-11 18:38:41', '2025-08-11 18:38:44', '2025-08-11 18:40:41'),
(48, '2025-00001', 'Treasury', 'GN-018', '{\"queue_number\":\"GN-018\",\"student_id\":\"2025-00001\",\"department\":\"Treasury\",\"timestamp\":1754937623}', 'cancelled', '2025-08-11 18:40:23', '2025-08-11 18:46:00', '2025-08-11 18:42:23'),
(49, '2025-00001', 'Treasury', 'TR-001', '{\"queue_number\":\"TR-001\",\"student_id\":\"2025-00001\",\"department\":\"treasury\",\"timestamp\":1755351678}', '', '2025-08-16 13:41:18', '2025-08-16 13:41:18', '2025-08-16 07:43:18'),
(50, '2025-00001', 'Enrollment', 'EN-001', '{\"queue_number\":\"EN-001\",\"student_id\":\"2025-00001\",\"department\":\"enrollment\",\"timestamp\":1755351683}', '', '2025-08-16 13:41:23', '2025-08-16 13:41:23', '2025-08-16 07:43:23'),
(51, '2025-00001', 'Enrollment', 'EN-002', '{\"queue_number\":\"EN-002\",\"student_id\":\"2025-00001\",\"department\":\"enrollment\",\"timestamp\":1755351698}', '', '2025-08-16 13:41:38', '2025-08-16 13:41:38', '2025-08-16 07:43:38'),
(52, '2025-00001', 'Enrollment', 'EN-003', '{\"queue_number\":\"EN-003\",\"student_id\":\"2025-00001\",\"department\":\"enrollment\",\"timestamp\":1755351699}', '', '2025-08-16 13:41:39', '2025-08-16 13:41:39', '2025-08-16 07:43:39'),
(53, '2025-00001', 'Enrollment', 'EN-004', '{\"queue_number\":\"EN-004\",\"student_id\":\"2025-00001\",\"department\":\"enrollment\",\"timestamp\":1755351699}', '', '2025-08-16 13:41:39', '2025-08-16 13:41:39', '2025-08-16 07:43:39'),
(54, '2025-00001', 'Enrollment', 'EN-005', '{\"queue_number\":\"EN-005\",\"student_id\":\"2025-00001\",\"department\":\"enrollment\",\"timestamp\":1755351700}', '', '2025-08-16 13:41:40', '2025-08-16 13:41:40', '2025-08-16 07:43:40'),
(55, '2025-00001', 'Enrollment', 'EN-006', '{\"queue_number\":\"EN-006\",\"student_id\":\"2025-00001\",\"department\":\"enrollment\",\"timestamp\":1755351700}', '', '2025-08-16 13:41:40', '2025-08-16 13:41:40', '2025-08-16 07:43:40'),
(56, '2025-00001', 'Treasury', 'TR-002', '{\"queue_number\":\"TR-002\",\"student_id\":\"2025-00001\",\"department\":\"treasury\",\"timestamp\":1755351724}', '', '2025-08-16 13:42:04', '2025-08-16 13:42:04', '2025-08-16 07:44:04'),
(57, '2025-00001', 'Enrollment', 'EN-007', '{\"queue_number\":\"EN-007\",\"student_id\":\"2025-00001\",\"department\":\"enrollment\",\"timestamp\":1755351754}', '', '2025-08-16 13:42:34', '2025-08-16 13:42:34', '2025-08-16 07:44:34'),
(58, '2025-00001', 'Treasury', 'TR-003', '{\"queue_number\":\"TR-003\",\"student_id\":\"2025-00001\",\"department\":\"treasury\",\"timestamp\":1755351795}', '', '2025-08-16 13:43:15', '2025-08-16 13:43:15', '2025-08-16 07:45:15'),
(59, '2025-00001', 'Registrar', 'RG-001', '{\"queue_number\":\"RG-001\",\"student_id\":\"2025-00001\",\"department\":\"registrar\",\"timestamp\":1755351798}', '', '2025-08-16 13:43:18', '2025-08-16 13:43:18', '2025-08-16 07:45:18'),
(60, '2025-00001', 'Enrollment', 'EN-008', '{\"queue_number\":\"EN-008\",\"student_id\":\"2025-00001\",\"department\":\"enrollment\",\"timestamp\":1755351874}', '', '2025-08-16 13:44:34', '2025-08-16 13:44:34', '2025-08-16 07:46:34'),
(61, '2025-00001', 'Registrar', 'RG-002', '{\"queue_number\":\"RG-002\",\"student_id\":\"2025-00001\",\"department\":\"registrar\",\"timestamp\":1755352275}', '', '2025-08-16 13:51:15', '2025-08-16 13:51:15', '2025-08-16 07:53:15'),
(62, '2025-00001', 'Treasury', 'TR-004', '{\"queue_number\":\"TR-004\",\"student_id\":\"2025-00001\",\"department\":\"treasury\",\"timestamp\":1755352567}', '', '2025-08-16 13:56:07', '2025-08-16 13:56:07', '2025-08-16 07:58:07'),
(63, '2025-00002', 'Registrar', 'RG-001', '{\"queue_number\":\"RG-001\",\"student_id\":\"2025-00002\",\"department\":\"registrar\",\"timestamp\":1755460891}', '', '2025-08-17 20:01:31', '2025-08-17 20:01:31', '2025-08-17 14:03:31'),
(64, '2025-00002', 'Enrollment', 'EN-001', '{\"queue_number\":\"EN-001\",\"student_id\":\"2025-00002\",\"department\":\"enrollment\",\"timestamp\":1755460906}', '', '2025-08-17 20:01:46', '2025-08-17 20:01:46', '2025-08-17 14:03:46'),
(65, '2025-00002', 'Registrar', 'RG-002', '{\"queue_number\":\"RG-002\",\"student_id\":\"2025-00002\",\"department\":\"registrar\",\"timestamp\":1755461931}', 'completed', '2025-08-17 20:18:51', '2025-08-17 20:20:03', '2025-08-17 14:20:51');

-- --------------------------------------------------------

--
-- Table structure for table `registration_logs`
--

CREATE TABLE `registration_logs` (
  `id` int(11) NOT NULL,
  `registration_id` int(11) NOT NULL,
  `action` enum('approve','reject','review') NOT NULL,
  `remarks` text DEFAULT NULL,
  `processed_by` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` int(11) NOT NULL,
  `section_code` varchar(10) NOT NULL,
  `course` varchar(50) NOT NULL,
  `year_level` enum('1st Year','2nd Year','3rd Year','4th Year') NOT NULL,
  `max_students` int(11) DEFAULT 40,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `section_code`, `course`, `year_level`, `max_students`, `created_at`) VALUES
(1, '11A1', 'BSIT', '1st Year', 40, '2025-08-17 11:56:15'),
(2, '11M1', 'BSIT', '1st Year', 40, '2025-08-17 11:56:15'),
(3, '11E1', 'BSIT', '1st Year', 40, '2025-08-17 11:56:15'),
(4, '21A1', 'BSIT', '2nd Year', 40, '2025-08-17 11:56:15'),
(5, '21M1', 'BSIT', '2nd Year', 40, '2025-08-17 11:56:15'),
(6, '21E1', 'BSIT', '2nd Year', 40, '2025-08-17 11:56:15'),
(7, '31A1', 'BSIT', '3rd Year', 40, '2025-08-17 11:56:15'),
(8, '31M1', 'BSIT', '3rd Year', 40, '2025-08-17 11:56:15'),
(9, '31E1', 'BSIT', '3rd Year', 40, '2025-08-17 11:56:15'),
(10, '41A1', 'BSIT', '4th Year', 40, '2025-08-17 11:56:15'),
(11, '41M1', 'BSIT', '4th Year', 40, '2025-08-17 11:56:15'),
(12, '41E1', 'BSIT', '4th Year', 40, '2025-08-17 11:56:15'),
(25, '11A2', 'BSCS', '1st Year', 40, '2025-08-17 14:12:16'),
(26, '11M2', 'BSCS', '1st Year', 40, '2025-08-17 14:12:16'),
(27, '21A2', 'BSCS', '2nd Year', 40, '2025-08-17 14:12:16'),
(28, '21M2', 'BSCS', '2nd Year', 40, '2025-08-17 14:12:16'),
(29, '31A2', 'BSCS', '3rd Year', 40, '2025-08-17 14:12:16'),
(30, '31M2', 'BSCS', '3rd Year', 40, '2025-08-17 14:12:16'),
(31, '41A2', 'BSCS', '4th Year', 40, '2025-08-17 14:12:16'),
(32, '41M2', 'BSCS', '4th Year', 40, '2025-08-17 14:12:16'),
(58, 'CPE11A', 'BSCpE', '1st Year', 35, '2025-08-17 14:29:45'),
(59, 'CPE11B', 'BSCpE', '1st Year', 35, '2025-08-17 14:29:45'),
(60, 'CPE21A', 'BSCpE', '2nd Year', 35, '2025-08-17 14:29:45'),
(61, 'CPE21B', 'BSCpE', '2nd Year', 35, '2025-08-17 14:29:45'),
(62, 'CPE31A', 'BSCpE', '3rd Year', 35, '2025-08-17 14:29:45'),
(63, 'CPE31B', 'BSCpE', '3rd Year', 35, '2025-08-17 14:29:45'),
(64, 'CPE41A', 'BSCpE', '4th Year', 35, '2025-08-17 14:29:45'),
(65, 'CPE41B', 'BSCpE', '4th Year', 35, '2025-08-17 14:29:45'),
(66, 'EE11A', 'BSEE', '1st Year', 35, '2025-08-17 14:29:45'),
(67, 'EE11B', 'BSEE', '1st Year', 35, '2025-08-17 14:29:45'),
(68, 'EE21A', 'BSEE', '2nd Year', 35, '2025-08-17 14:29:45'),
(69, 'EE21B', 'BSEE', '2nd Year', 35, '2025-08-17 14:29:45'),
(70, 'EE31A', 'BSEE', '3rd Year', 35, '2025-08-17 14:29:45'),
(71, 'EE31B', 'BSEE', '3rd Year', 35, '2025-08-17 14:29:45'),
(72, 'EE41A', 'BSEE', '4th Year', 35, '2025-08-17 14:29:45'),
(73, 'EE41B', 'BSEE', '4th Year', 35, '2025-08-17 14:29:45'),
(74, 'ME11A', 'BSME', '1st Year', 35, '2025-08-17 14:29:45'),
(75, 'ME11B', 'BSME', '1st Year', 35, '2025-08-17 14:29:45'),
(76, 'ME21A', 'BSME', '2nd Year', 35, '2025-08-17 14:29:45'),
(77, 'ME21B', 'BSME', '2nd Year', 35, '2025-08-17 14:29:45'),
(78, 'ME31A', 'BSME', '3rd Year', 35, '2025-08-17 14:29:45'),
(79, 'ME31B', 'BSME', '3rd Year', 35, '2025-08-17 14:29:45'),
(80, 'ME41A', 'BSME', '4th Year', 35, '2025-08-17 14:29:45'),
(81, 'ME41B', 'BSME', '4th Year', 35, '2025-08-17 14:29:45'),
(82, 'CE11A', 'BSCE', '1st Year', 35, '2025-08-17 14:30:14'),
(83, 'CE11B', 'BSCE', '1st Year', 35, '2025-08-17 14:30:14'),
(84, 'CE21A', 'BSCE', '2nd Year', 35, '2025-08-17 14:30:14'),
(85, 'CE21B', 'BSCE', '2nd Year', 35, '2025-08-17 14:30:14'),
(86, 'CE31A', 'BSCE', '3rd Year', 35, '2025-08-17 14:30:14'),
(87, 'CE31B', 'BSCE', '3rd Year', 35, '2025-08-17 14:30:14'),
(88, 'CE41A', 'BSCE', '4th Year', 35, '2025-08-17 14:30:14'),
(89, 'CE41B', 'BSCE', '4th Year', 35, '2025-08-17 14:30:14'),
(90, 'IE11A', 'BSIE', '1st Year', 35, '2025-08-17 14:30:14'),
(91, 'IE11B', 'BSIE', '1st Year', 35, '2025-08-17 14:30:14'),
(92, 'IE21A', 'BSIE', '2nd Year', 35, '2025-08-17 14:30:14'),
(93, 'IE21B', 'BSIE', '2nd Year', 35, '2025-08-17 14:30:14'),
(94, 'IE31A', 'BSIE', '3rd Year', 35, '2025-08-17 14:30:14'),
(95, 'IE31B', 'BSIE', '3rd Year', 35, '2025-08-17 14:30:14'),
(96, 'IE41A', 'BSIE', '4th Year', 35, '2025-08-17 14:30:14'),
(97, 'IE41B', 'BSIE', '4th Year', 35, '2025-08-17 14:30:14'),
(98, 'CHE11A', 'BSChE', '1st Year', 35, '2025-08-17 14:30:14'),
(99, 'CHE11B', 'BSChE', '1st Year', 35, '2025-08-17 14:30:14'),
(100, 'CHE21A', 'BSChE', '2nd Year', 35, '2025-08-17 14:30:14'),
(101, 'CHE21B', 'BSChE', '2nd Year', 35, '2025-08-17 14:30:14'),
(102, 'CHE31A', 'BSChE', '3rd Year', 35, '2025-08-17 14:30:14'),
(103, 'CHE31B', 'BSChE', '3rd Year', 35, '2025-08-17 14:30:14'),
(104, 'CHE41A', 'BSChE', '4th Year', 35, '2025-08-17 14:30:14'),
(105, 'CHE41B', 'BSChE', '4th Year', 35, '2025-08-17 14:30:14'),
(106, 'BA11A', 'BSBA', '1st Year', 40, '2025-08-17 14:30:43'),
(107, 'BA11B', 'BSBA', '1st Year', 40, '2025-08-17 14:30:43'),
(108, 'BA21A', 'BSBA', '2nd Year', 40, '2025-08-17 14:30:43'),
(109, 'BA21B', 'BSBA', '2nd Year', 40, '2025-08-17 14:30:43'),
(110, 'BA31A', 'BSBA', '3rd Year', 40, '2025-08-17 14:30:43'),
(111, 'BA31B', 'BSBA', '3rd Year', 40, '2025-08-17 14:30:43'),
(112, 'BA41A', 'BSBA', '4th Year', 40, '2025-08-17 14:30:43'),
(113, 'BA41B', 'BSBA', '4th Year', 40, '2025-08-17 14:30:43'),
(114, 'ACC11A', 'BSA', '1st Year', 40, '2025-08-17 14:30:43'),
(115, 'ACC11B', 'BSA', '1st Year', 40, '2025-08-17 14:30:43'),
(116, 'ACC21A', 'BSA', '2nd Year', 40, '2025-08-17 14:30:43'),
(117, 'ACC21B', 'BSA', '2nd Year', 40, '2025-08-17 14:30:43'),
(118, 'ACC31A', 'BSA', '3rd Year', 40, '2025-08-17 14:30:43'),
(119, 'ACC31B', 'BSA', '3rd Year', 40, '2025-08-17 14:30:43'),
(120, 'ACC41A', 'BSA', '4th Year', 40, '2025-08-17 14:30:43'),
(121, 'ACC41B', 'BSA', '4th Year', 40, '2025-08-17 14:30:43'),
(122, 'MM11A', 'BSMM', '1st Year', 40, '2025-08-17 14:30:43'),
(123, 'MM11B', 'BSMM', '1st Year', 40, '2025-08-17 14:30:43'),
(124, 'MM21A', 'BSMM', '2nd Year', 40, '2025-08-17 14:30:43'),
(125, 'MM21B', 'BSMM', '2nd Year', 40, '2025-08-17 14:30:43'),
(126, 'MM31A', 'BSMM', '3rd Year', 40, '2025-08-17 14:30:43'),
(127, 'MM31B', 'BSMM', '3rd Year', 40, '2025-08-17 14:30:43'),
(128, 'MM41A', 'BSMM', '4th Year', 40, '2025-08-17 14:30:43'),
(129, 'MM41B', 'BSMM', '4th Year', 40, '2025-08-17 14:30:43'),
(130, 'FM11A', 'BSFM', '1st Year', 40, '2025-08-17 14:31:24'),
(131, 'FM11B', 'BSFM', '1st Year', 40, '2025-08-17 14:31:24'),
(132, 'FM21A', 'BSFM', '2nd Year', 40, '2025-08-17 14:31:24'),
(133, 'FM21B', 'BSFM', '2nd Year', 40, '2025-08-17 14:31:24'),
(134, 'FM31A', 'BSFM', '3rd Year', 40, '2025-08-17 14:31:24'),
(135, 'FM31B', 'BSFM', '3rd Year', 40, '2025-08-17 14:31:24'),
(136, 'FM41A', 'BSFM', '4th Year', 40, '2025-08-17 14:31:24'),
(137, 'FM41B', 'BSFM', '4th Year', 40, '2025-08-17 14:31:24'),
(138, 'HRM11A', 'BSHRM', '1st Year', 40, '2025-08-17 14:31:24'),
(139, 'HRM11B', 'BSHRM', '1st Year', 40, '2025-08-17 14:31:24'),
(140, 'HRM21A', 'BSHRM', '2nd Year', 40, '2025-08-17 14:31:24'),
(141, 'HRM21B', 'BSHRM', '2nd Year', 40, '2025-08-17 14:31:24'),
(142, 'HRM31A', 'BSHRM', '3rd Year', 40, '2025-08-17 14:31:24'),
(143, 'HRM31B', 'BSHRM', '3rd Year', 40, '2025-08-17 14:31:24'),
(144, 'HRM41A', 'BSHRM', '4th Year', 40, '2025-08-17 14:31:24'),
(145, 'HRM41B', 'BSHRM', '4th Year', 40, '2025-08-17 14:31:24'),
(146, 'ENT11A', 'BSE', '1st Year', 40, '2025-08-17 14:31:24'),
(147, 'ENT11B', 'BSE', '1st Year', 40, '2025-08-17 14:31:24'),
(148, 'ENT21A', 'BSE', '2nd Year', 40, '2025-08-17 14:31:24'),
(149, 'ENT21B', 'BSE', '2nd Year', 40, '2025-08-17 14:31:24'),
(150, 'ENT31A', 'BSE', '3rd Year', 40, '2025-08-17 14:31:24'),
(151, 'ENT31B', 'BSE', '3rd Year', 40, '2025-08-17 14:31:24'),
(152, 'ENT41A', 'BSE', '4th Year', 40, '2025-08-17 14:31:24'),
(153, 'ENT41B', 'BSE', '4th Year', 40, '2025-08-17 14:31:24'),
(154, 'HM11A', 'BSHM', '1st Year', 35, '2025-08-17 14:31:40'),
(155, 'HM11B', 'BSHM', '1st Year', 35, '2025-08-17 14:31:40'),
(156, 'HM21A', 'BSHM', '2nd Year', 35, '2025-08-17 14:31:40'),
(157, 'HM21B', 'BSHM', '2nd Year', 35, '2025-08-17 14:31:40'),
(158, 'HM31A', 'BSHM', '3rd Year', 35, '2025-08-17 14:31:40'),
(159, 'HM31B', 'BSHM', '3rd Year', 35, '2025-08-17 14:31:40'),
(160, 'HM41A', 'BSHM', '4th Year', 35, '2025-08-17 14:31:40'),
(161, 'HM41B', 'BSHM', '4th Year', 35, '2025-08-17 14:31:40'),
(162, 'TM11A', 'BSTM', '1st Year', 35, '2025-08-17 14:31:40'),
(163, 'TM11B', 'BSTM', '1st Year', 35, '2025-08-17 14:31:40'),
(164, 'TM21A', 'BSTM', '2nd Year', 35, '2025-08-17 14:31:40'),
(165, 'TM21B', 'BSTM', '2nd Year', 35, '2025-08-17 14:31:40'),
(166, 'TM31A', 'BSTM', '3rd Year', 35, '2025-08-17 14:31:40'),
(167, 'TM31B', 'BSTM', '3rd Year', 35, '2025-08-17 14:31:40'),
(168, 'TM41A', 'BSTM', '4th Year', 35, '2025-08-17 14:31:40'),
(169, 'TM41B', 'BSTM', '4th Year', 35, '2025-08-17 14:31:40'),
(170, 'NUR11A', 'BSN', '1st Year', 30, '2025-08-17 14:31:40'),
(171, 'NUR11B', 'BSN', '1st Year', 30, '2025-08-17 14:31:40'),
(172, 'NUR21A', 'BSN', '2nd Year', 30, '2025-08-17 14:31:40'),
(173, 'NUR21B', 'BSN', '2nd Year', 30, '2025-08-17 14:31:40'),
(174, 'NUR31A', 'BSN', '3rd Year', 30, '2025-08-17 14:31:40'),
(175, 'NUR31B', 'BSN', '3rd Year', 30, '2025-08-17 14:31:40'),
(176, 'NUR41A', 'BSN', '4th Year', 30, '2025-08-17 14:31:40'),
(177, 'NUR41B', 'BSN', '4th Year', 30, '2025-08-17 14:31:40'),
(178, 'ELED11A', 'BEEd', '1st Year', 35, '2025-08-17 14:31:57'),
(179, 'ELED11B', 'BEEd', '1st Year', 35, '2025-08-17 14:31:57'),
(180, 'ELED21A', 'BEEd', '2nd Year', 35, '2025-08-17 14:31:57'),
(181, 'ELED21B', 'BEEd', '2nd Year', 35, '2025-08-17 14:31:57'),
(182, 'ELED31A', 'BEEd', '3rd Year', 35, '2025-08-17 14:31:57'),
(183, 'ELED31B', 'BEEd', '3rd Year', 35, '2025-08-17 14:31:57'),
(184, 'ELED41A', 'BEEd', '4th Year', 35, '2025-08-17 14:31:57'),
(185, 'ELED41B', 'BEEd', '4th Year', 35, '2025-08-17 14:31:57'),
(186, 'SCED11A', 'BSEd', '1st Year', 35, '2025-08-17 14:31:57'),
(187, 'SCED11B', 'BSEd', '1st Year', 35, '2025-08-17 14:31:57'),
(188, 'SCED21A', 'BSEd', '2nd Year', 35, '2025-08-17 14:31:57'),
(189, 'SCED21B', 'BSEd', '2nd Year', 35, '2025-08-17 14:31:57'),
(190, 'SCED31A', 'BSEd', '3rd Year', 35, '2025-08-17 14:31:57'),
(191, 'SCED31B', 'BSEd', '3rd Year', 35, '2025-08-17 14:31:57'),
(192, 'SCED41A', 'BSEd', '4th Year', 35, '2025-08-17 14:31:57'),
(193, 'SCED41B', 'BSEd', '4th Year', 35, '2025-08-17 14:31:57'),
(194, 'MT11A', 'BSMT', '1st Year', 25, '2025-08-17 14:31:57'),
(195, 'MT11B', 'BSMT', '1st Year', 25, '2025-08-17 14:31:57'),
(196, 'MT21A', 'BSMT', '2nd Year', 25, '2025-08-17 14:31:57'),
(197, 'MT21B', 'BSMT', '2nd Year', 25, '2025-08-17 14:31:57'),
(198, 'MT31A', 'BSMT', '3rd Year', 25, '2025-08-17 14:31:57'),
(199, 'MT31B', 'BSMT', '3rd Year', 25, '2025-08-17 14:31:57'),
(200, 'MT41A', 'BSMT', '4th Year', 25, '2025-08-17 14:31:57'),
(201, 'MT41B', 'BSMT', '4th Year', 25, '2025-08-17 14:31:57'),
(202, 'PSY11A', 'BS Psychology', '1st Year', 35, '2025-08-17 14:32:35'),
(203, 'PSY11B', 'BS Psychology', '1st Year', 35, '2025-08-17 14:32:35'),
(204, 'PSY21A', 'BS Psychology', '2nd Year', 35, '2025-08-17 14:32:35'),
(205, 'PSY21B', 'BS Psychology', '2nd Year', 35, '2025-08-17 14:32:35'),
(206, 'PSY31A', 'BS Psychology', '3rd Year', 35, '2025-08-17 14:32:35'),
(207, 'PSY31B', 'BS Psychology', '3rd Year', 35, '2025-08-17 14:32:35'),
(208, 'PSY41A', 'BS Psychology', '4th Year', 35, '2025-08-17 14:32:35'),
(209, 'PSY41B', 'BS Psychology', '4th Year', 35, '2025-08-17 14:32:35'),
(210, 'CRIM11A', 'BS Criminology', '1st Year', 35, '2025-08-17 14:32:35'),
(211, 'CRIM11B', 'BS Criminology', '1st Year', 35, '2025-08-17 14:32:35'),
(212, 'CRIM21A', 'BS Criminology', '2nd Year', 35, '2025-08-17 14:32:35'),
(213, 'CRIM21B', 'BS Criminology', '2nd Year', 35, '2025-08-17 14:32:35'),
(214, 'CRIM31A', 'BS Criminology', '3rd Year', 35, '2025-08-17 14:32:35'),
(215, 'CRIM31B', 'BS Criminology', '3rd Year', 35, '2025-08-17 14:32:35'),
(216, 'CRIM41A', 'BS Criminology', '4th Year', 35, '2025-08-17 14:32:35'),
(217, 'CRIM41B', 'BS Criminology', '4th Year', 35, '2025-08-17 14:32:35'),
(218, 'BIO11A', 'BS Biology', '1st Year', 30, '2025-08-17 14:32:35'),
(219, 'BIO11B', 'BS Biology', '1st Year', 30, '2025-08-17 14:32:35'),
(220, 'BIO21A', 'BS Biology', '2nd Year', 30, '2025-08-17 14:32:35'),
(221, 'BIO21B', 'BS Biology', '2nd Year', 30, '2025-08-17 14:32:35'),
(222, 'BIO31A', 'BS Biology', '3rd Year', 30, '2025-08-17 14:32:35'),
(223, 'BIO31B', 'BS Biology', '3rd Year', 30, '2025-08-17 14:32:35'),
(224, 'BIO41A', 'BS Biology', '4th Year', 30, '2025-08-17 14:32:35'),
(225, 'BIO41B', 'BS Biology', '4th Year', 30, '2025-08-17 14:32:35');

-- --------------------------------------------------------

--
-- Table structure for table `staff_accounts`
--

CREATE TABLE `staff_accounts` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `role` enum('registrar','cashier','student-assistant','evaluator','admin') NOT NULL DEFAULT 'registrar',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff_accounts`
--

INSERT INTO `staff_accounts` (`id`, `email`, `password`, `name`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'registrar@ncst.edu.ph', '$2y$10$V3L5YQj8R/qu8JnPlLFy.OaYrukgVQnaLls.IQUEQ2Egr8d6DFqcW', 'NCST Registrar', 'registrar', 'active', '2025-08-13 15:40:08', '2025-08-16 11:55:07'),
(3, 'cashier@ncst.edu.ph', '$2y$10$V3L5YQj8R/qu8JnPlLFy.OaYrukgVQnaLls.IQUEQ2Egr8d6DFqcW', 'NCST Cashier', 'cashier', 'active', '2025-08-16 11:51:41', '2025-08-16 11:55:02'),
(4, 'evaluator@ncst.edu.ph', '$2y$10$V3L5YQj8R/qu8JnPlLFy.OaYrukgVQnaLls.IQUEQ2Egr8d6DFqcW', 'NCST Evaluator', 'evaluator', 'active', '2025-08-16 11:51:41', '2025-08-16 11:55:14'),
(5, 'studentassistant@ncst.edu.ph', '$2y$10$V3L5YQj8R/qu8JnPlLFy.OaYrukgVQnaLls.IQUEQ2Egr8d6DFqcW', 'NCST Student Assistant', 'student-assistant', 'active', '2025-08-16 11:51:41', '2025-08-16 11:55:18'),
(6, 'admin@ncst.edu.ph', '$2y$10$V3L5YQj8R/qu8JnPlLFy.OaYrukgVQnaLls.IQUEQ2Egr8d6DFqcW', 'System Administrator', 'admin', 'active', '2025-08-16 11:51:41', '2025-08-16 11:55:23');

-- --------------------------------------------------------

--
-- Table structure for table `student_accounts`
--

CREATE TABLE `student_accounts` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `user_type` enum('student','admin','staff') DEFAULT 'student',
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `email_verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_accounts`
--

INSERT INTO `student_accounts` (`id`, `email`, `password`, `first_name`, `last_name`, `phone`, `user_type`, `status`, `email_verified`, `created_at`, `updated_at`, `last_login`) VALUES
(5, 'justin1@gmail.com', '$2y$10$cCA0/e0vvQqrYrd/4upc7.bXZnd4OEz/Llp64DAYVBv1jGmoxGwlW', 'justin', 'domingo', '09512910476', 'student', 'active', 0, '2025-08-11 13:05:19', '2025-08-11 13:05:19', NULL),
(6, 'fritzcholo@gmail.com', '$2y$10$WVHywL2qNgrWx5DlCElVVui8MLtCCgtejTFsQ7AP.mtl3hAQ5Cbjq', 'fritz', 'cholo', '09512910476', 'student', 'active', 0, '2025-08-17 20:22:43', '2025-08-17 20:22:43', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_assessments`
--

CREATE TABLE `student_assessments` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `fee_type` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `school_year` varchar(10) NOT NULL,
  `semester` enum('1st Semester','2nd Semester','Summer') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_assessments`
--

INSERT INTO `student_assessments` (`id`, `student_id`, `fee_type`, `amount`, `school_year`, `semester`, `created_at`) VALUES
(1, '2024-61582', 'Tuition Fee (23 units ?? ???350.00)', 8050.00, '2024-2025', '1st Semester', '2025-08-11 07:35:30'),
(2, '2024-61582', 'Laboratory Fee', 2500.00, '2024-2025', '1st Semester', '2025-08-11 07:35:30'),
(3, '2024-61582', 'Registration Fee', 500.00, '2024-2025', '1st Semester', '2025-08-11 07:35:30'),
(4, '2024-61582', 'Library Fee', 300.00, '2024-2025', '1st Semester', '2025-08-11 07:35:30'),
(5, '2024-61582', 'Student Activities Fee', 200.00, '2024-2025', '1st Semester', '2025-08-11 07:35:30'),
(6, '2024-61582', 'Medical/Dental Fee', 150.00, '2024-2025', '1st Semester', '2025-08-11 07:35:30'),
(7, '2024-61582', 'ID Fee', 100.00, '2024-2025', '1st Semester', '2025-08-11 07:35:30'),
(8, '2024-61582', 'Insurance Fee', 50.00, '2024-2025', '1st Semester', '2025-08-11 07:35:30'),
(9, '2025-00001', 'Tuition Fee (18 units × ₱350.00)', 6300.00, '2024-2025', '1st Semester', '2025-08-13 15:40:08'),
(10, '2025-00001', 'Laboratory Fee', 2000.00, '2024-2025', '1st Semester', '2025-08-13 15:40:08'),
(11, '2025-00001', 'Registration Fee', 500.00, '2024-2025', '1st Semester', '2025-08-13 15:40:08'),
(12, '2025-00001', 'Library Fee', 300.00, '2024-2025', '1st Semester', '2025-08-13 15:40:08'),
(13, '2025-00001', 'Student Activities Fee', 200.00, '2024-2025', '1st Semester', '2025-08-13 15:40:08'),
(14, '2025-00001', 'Medical/Dental Fee', 150.00, '2024-2025', '1st Semester', '2025-08-13 15:40:08'),
(15, '2025-00001', 'ID Fee', 100.00, '2024-2025', '1st Semester', '2025-08-13 15:40:08'),
(16, '2025-00001', 'Insurance Fee', 50.00, '2024-2025', '1st Semester', '2025-08-13 15:40:08'),
(17, '2025-00001', 'Tuition Fee (18 units ├ù Ôé▒350.00)', 6300.00, '2024-2025', '1st Semester', '2025-08-13 15:51:28'),
(18, '2025-00001', 'Laboratory Fee', 2000.00, '2024-2025', '1st Semester', '2025-08-13 15:51:28'),
(19, '2025-00001', 'Registration Fee', 500.00, '2024-2025', '1st Semester', '2025-08-13 15:51:28'),
(20, '2025-00001', 'Library Fee', 300.00, '2024-2025', '1st Semester', '2025-08-13 15:51:28'),
(21, '2025-00001', 'Student Activities Fee', 200.00, '2024-2025', '1st Semester', '2025-08-13 15:51:28'),
(22, '2025-00001', 'Medical/Dental Fee', 150.00, '2024-2025', '1st Semester', '2025-08-13 15:51:28'),
(23, '2025-00001', 'ID Fee', 100.00, '2024-2025', '1st Semester', '2025-08-13 15:51:28'),
(24, '2025-00001', 'Insurance Fee', 50.00, '2024-2025', '1st Semester', '2025-08-13 15:51:28'),
(25, '2024-61584', 'Tuition Fee (23 units  350.00)', 8050.00, '2024-2025', '1st Semester', '2025-08-16 12:10:27'),
(26, '2024-61584', 'Laboratory Fee', 1500.00, '2024-2025', '1st Semester', '2025-08-16 12:10:27'),
(27, '2024-61584', 'Registration Fee', 400.00, '2024-2025', '1st Semester', '2025-08-16 12:10:27'),
(28, '2024-61584', 'Miscellaneous Fee', 350.00, '2024-2025', '1st Semester', '2025-08-16 12:10:27'),
(32, '2025-00002', 'Tuition Fee', 18200.00, '2024-2025', '1st Semester', '2025-08-17 16:04:55'),
(33, '2025-00002', 'Laboratory Fee', 2000.00, '2024-2025', '1st Semester', '2025-08-17 16:04:55'),
(34, '2025-00002', 'Miscellaneous', 3000.00, '2024-2025', '1st Semester', '2025-08-17 16:04:55'),
(35, '2025-00002', 'LMS', 500.00, '2024-2025', '1st Semester', '2025-08-17 16:04:55'),
(36, '2025-00002', 'NSTP/ROTC', 700.00, '2024-2025', '1st Semester', '2025-08-17 16:04:55'),
(37, '2025-00002', 'OMR', 300.00, '2024-2025', '1st Semester', '2025-08-17 16:04:55'),
(38, '2025-00008', 'Tuition Fee', 6066.66, '2025-2026', '2nd Semester', '2025-08-17 21:42:34'),
(39, '2025-00008', 'Laboratory Fee', 2000.00, '2025-2026', '2nd Semester', '2025-08-17 21:42:34'),
(40, '2025-00008', 'Miscellaneous', 3000.00, '2025-2026', '2nd Semester', '2025-08-17 21:42:34'),
(41, '2025-00008', 'LMS', 500.00, '2025-2026', '2nd Semester', '2025-08-17 21:42:34'),
(42, '2025-00008', 'NSTP/ROTC', 700.00, '2025-2026', '2nd Semester', '2025-08-17 21:42:34'),
(43, '2025-00008', 'OMR', 300.00, '2025-2026', '2nd Semester', '2025-08-17 21:42:34');

-- --------------------------------------------------------

--
-- Table structure for table `student_payments`
--

CREATE TABLE `student_payments` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','check','bank_transfer','online') NOT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `status` enum('pending','completed','failed','cancelled') DEFAULT 'pending',
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `cashier_name` varchar(100) DEFAULT 'Maria Reyes',
  `description` varchar(255) DEFAULT 'Tuition Fee',
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_payments`
--

INSERT INTO `student_payments` (`id`, `student_id`, `amount`, `payment_method`, `reference_number`, `status`, `paid_at`, `created_at`, `cashier_name`, `description`, `notes`) VALUES
(12, '2025-00002', 24700.00, 'cash', 'CASH-20250818-001', 'completed', '2025-08-17 16:00:00', '2025-08-17 16:58:44', 'Justin Domingo', 'Full payment', ''),
(13, '2025-00004', 10000.00, 'online', 'BPI-20250818-001', 'completed', '2025-08-17 16:00:00', '2025-08-17 16:58:44', 'Maria Reyes', 'Tuition Fee - BSCS', NULL),
(14, '2024-00001', 5.00, 'cash', 'CASH-20250818-002', 'completed', '2025-08-17 16:00:00', '2025-08-17 16:58:44', 'Maria Reyes', 'Other School Fee', NULL),
(15, '2025-00003', 15000.00, 'cash', 'CASH-20250818-TEST', 'completed', '2025-08-17 16:00:00', '2025-08-17 17:03:34', 'Maria Reyes', 'Tuition Fee - BSIT', NULL),
(16, '2025-00003', 5000.00, '', 'GCASH-20250818-002', 'pending', NULL, '2025-08-17 17:57:35', 'Maria Reyes', 'Miscellaneous Fee', NULL),
(17, '2025-00008', 12566.66, 'cash', 'CASH-20250817-210', 'completed', '2025-08-17 21:43:18', '2025-08-17 21:43:18', 'Maria Reyes', 'Tuition Fee', NULL);

--
-- Triggers `student_payments`
--
DELIMITER $$
CREATE TRIGGER `update_payment_status_after_payment_insert` AFTER INSERT ON `student_payments` FOR EACH ROW BEGIN
    UPDATE enrollments 
    SET payment_status = CASE 
        WHEN (SELECT COALESCE(SUM(amount), 0) 
              FROM student_payments 
              WHERE student_id = NEW.student_id AND status = 'completed') >= total_assessment 
        THEN 'paid'
        WHEN (SELECT COALESCE(SUM(amount), 0) 
              FROM student_payments 
              WHERE student_id = NEW.student_id AND status = 'completed') > 0 
        THEN 'partial'
        ELSE 'unpaid'
    END
    WHERE student_id = NEW.student_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_payment_status_after_payment_update` AFTER UPDATE ON `student_payments` FOR EACH ROW BEGIN
    UPDATE enrollments 
    SET payment_status = CASE 
        WHEN (SELECT COALESCE(SUM(amount), 0) 
              FROM student_payments 
              WHERE student_id = NEW.student_id AND status = 'completed') >= total_assessment 
        THEN 'paid'
        WHEN (SELECT COALESCE(SUM(amount), 0) 
              FROM student_payments 
              WHERE student_id = NEW.student_id AND status = 'completed') > 0 
        THEN 'partial'
        ELSE 'unpaid'
    END
    WHERE student_id = NEW.student_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `student_registrations`
--

CREATE TABLE `student_registrations` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `desired_course` varchar(200) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `suffix` varchar(10) DEFAULT NULL,
  `complete_address` varchar(300) DEFAULT NULL,
  `zip_code` varchar(10) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `town` varchar(100) DEFAULT NULL,
  `barangay` varchar(100) DEFAULT NULL,
  `land_line` varchar(20) DEFAULT NULL,
  `mobile_no` varchar(20) NOT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `civil_status` enum('Single','Married','Divorced','Widowed') DEFAULT NULL,
  `nationality` varchar(50) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `place_of_birth` varchar(200) DEFAULT NULL,
  `email_address` varchar(100) NOT NULL,
  `religion` varchar(100) DEFAULT NULL,
  `father_name` varchar(200) DEFAULT NULL,
  `father_occupation` varchar(100) DEFAULT NULL,
  `father_contact` varchar(20) DEFAULT NULL,
  `mother_name` varchar(200) DEFAULT NULL,
  `mother_occupation` varchar(100) DEFAULT NULL,
  `mother_contact` varchar(20) DEFAULT NULL,
  `guardian_name` varchar(200) DEFAULT NULL,
  `guardian_relationship` varchar(50) DEFAULT NULL,
  `guardian_contact` varchar(20) DEFAULT NULL,
  `tertiary_school` varchar(200) DEFAULT NULL,
  `tertiary_year` varchar(10) DEFAULT NULL,
  `academic_achievement` varchar(100) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `student_type` enum('New','Old','Shifting','Transferee') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_registrations`
--

INSERT INTO `student_registrations` (`id`, `student_id`, `desired_course`, `last_name`, `first_name`, `middle_name`, `suffix`, `complete_address`, `zip_code`, `region`, `province`, `town`, `barangay`, `land_line`, `mobile_no`, `gender`, `civil_status`, `nationality`, `date_of_birth`, `place_of_birth`, `email_address`, `religion`, `father_name`, `father_occupation`, `father_contact`, `mother_name`, `mother_occupation`, `mother_contact`, `guardian_name`, `guardian_relationship`, `guardian_contact`, `tertiary_school`, `tertiary_year`, `academic_achievement`, `status`, `created_at`, `updated_at`, `student_type`) VALUES
(22, '2025-00002', 'Bachelor of Science in Computer Science (BSCS)', 'domingo', 'justin', 'Gonzales', '', 'Indang', '4114', 'REGION IV-A (CALABARZON)', 'BATANGAS', 'LIAN', 'Barangay 5 (Pob.)', 'Philippines', '09561234565', 'Female', 'Single', 'American', '2002-12-13', 'Sampaloc, Manila Hospital', 'justin1@gmail.com', 'Born Again', 'domingo', 'Dentist', '09561234565', 'domingo', 'Flight Attendant', '09561234565', 'domingo', 'My wife', '09561234565', 'ACADEMIA DE SAN LORENZO', '2020', 'Honorable Mention', 'approved', '2025-08-17 13:33:35', '2025-08-17 16:45:10', 'New'),
(23, '2025-00003', 'BSIT', 'Santos', 'Maria', 'Cruz', NULL, '123 Main St, City', NULL, NULL, NULL, NULL, NULL, NULL, '09123456789', 'Female', NULL, NULL, '2003-05-15', NULL, 'maria.santos@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', '2025-08-17 16:46:19', '2025-08-17 16:52:59', 'New'),
(24, '2025-00004', 'BSCS', 'Dela Cruz', 'Jose', 'Ramos', NULL, '456 Side St, Town', NULL, NULL, NULL, NULL, NULL, NULL, '09987654321', 'Male', NULL, NULL, '2002-08-20', NULL, 'jose.delacruz@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', '2025-08-17 16:46:30', '2025-08-17 16:46:30', 'Shifting'),
(25, '2025-00005', 'BSBA', 'Garcia', 'Ana', 'Lopez', NULL, '789 Back St, Village', NULL, NULL, NULL, NULL, NULL, NULL, '09111222333', 'Female', NULL, NULL, '2003-12-10', NULL, 'ana.garcia@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', '2025-08-17 16:46:44', '2025-08-17 16:53:07', 'New'),
(26, '2025-00006', 'BSIT', 'Reyes', 'Carlos', 'Miguel', NULL, '321 Front St, District', NULL, NULL, NULL, NULL, NULL, NULL, '09444555666', 'Male', NULL, NULL, '2003-03-25', NULL, 'carlos.reyes@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', '2025-08-17 16:47:05', '2025-08-17 16:47:05', 'New'),
(27, '2024-00001', 'BSIT', 'Gonzales', 'Pedro', 'Rivera', NULL, '555 Old St, City', NULL, NULL, NULL, NULL, NULL, NULL, '09555666777', 'Male', NULL, NULL, '2002-01-15', NULL, 'pedro.gonzales@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', '2025-08-17 16:50:39', '2025-08-17 16:50:39', 'Old'),
(28, '2025-00007', 'BSIT', 'Student', 'Test', 'Auto', NULL, 'Test Address', NULL, NULL, NULL, NULL, NULL, NULL, '09999999999', 'Male', NULL, NULL, '2003-01-01', NULL, 'test.student@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', '2025-08-17 17:05:39', '2025-08-17 17:05:49', 'New'),
(38, '2025-00008', 'Bachelor of Science in Information Technology (BSIT)', 'cholo', 'fritz', 'alvarez', '', 'Imus', '4114', 'REGION VII (CENTRAL VISAYAS)', 'CEBU', 'CATMON', 'Ginabucan', 'Philippines', '09512910476', 'Female', 'Single', 'Filipino', '2002-02-10', 'Sampaloc, Manila Hospital', 'fritzcholo@gmail.com', 'Born Again', 'cholo', 'Dentist', '09561234565', 'cholo', 'Flight Attendant', '09512910476', 'cholo', 'mother', '09512910476', 'ACLC College of Taytay', '2021', 'Honorable Mention', 'approved', '2025-08-17 21:01:40', '2025-08-17 21:03:12', NULL);

--
-- Triggers `student_registrations`
--
DELIMITER $$
CREATE TRIGGER `auto_enroll_approved_student` AFTER UPDATE ON `student_registrations` FOR EACH ROW BEGIN
    
    IF NEW.status = 'approved' AND OLD.status != 'approved' THEN
        INSERT IGNORE INTO enrollments (
            student_id, 
            year_level, 
            semester, 
            school_year, 
            enrollment_status, 
            payment_status, 
            total_assessment,
            created_at,
            updated_at
        ) VALUES (
            NEW.student_id,
            CASE 
                WHEN NEW.student_type = 'Old' OR LEFT(NEW.student_id, 4) = '2024' THEN '2nd Year'
                WHEN NEW.student_type = 'Shifting' THEN '2nd Year'
                ELSE '1st Year'
            END,
            '1st Semester',
            '2024-2025',
            'enrolled',
            'unpaid',
            CASE 
                WHEN NEW.desired_course LIKE '%IT%' OR NEW.desired_course LIKE '%CS%' THEN 25000.00
                WHEN NEW.desired_course LIKE '%BA%' OR NEW.desired_course LIKE '%BS%' THEN 23000.00
                ELSE 24000.00
            END,
            NOW(),
            NOW()
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `subject_code` varchar(20) NOT NULL,
  `subject_name` varchar(200) NOT NULL,
  `units` decimal(3,1) NOT NULL,
  `schedule` varchar(100) DEFAULT NULL,
  `course_code` varchar(20) DEFAULT NULL,
  `year_level` enum('1st Year','2nd Year','3rd Year','4th Year','5th Year') DEFAULT NULL,
  `semester` enum('1st Semester','2nd Semester','Summer') DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `subject_code`, `subject_name`, `units`, `schedule`, `course_code`, `year_level`, `semester`, `is_active`, `created_at`) VALUES
(1, 'IT 201', 'Data Structures and Algorithms', 3.0, 'MWF 8:00-9:00 AM', 'BSIT', '2nd Year', '1st Semester', 1, '2025-08-11 07:35:30'),
(2, 'IT 202', 'Object-Oriented Programming', 3.0, 'TTh 10:00-11:30 AM', 'BSIT', '2nd Year', '1st Semester', 1, '2025-08-11 07:35:30'),
(3, 'IT 203', 'Database Management Systems', 3.0, 'MWF 1:00-2:00 PM', 'BSIT', '2nd Year', '1st Semester', 1, '2025-08-11 07:35:30'),
(4, 'IT 204', 'Web Development', 3.0, 'TTh 2:30-4:00 PM', 'BSIT', '2nd Year', '1st Semester', 1, '2025-08-11 07:35:30'),
(5, 'GE 105', 'Mathematics in the Modern World', 3.0, 'MWF 9:00-10:00 AM', 'ALL', '2nd Year', '1st Semester', 1, '2025-08-11 07:35:30'),
(6, 'GE 106', 'Science, Technology and Society', 3.0, 'TTh 8:00-9:30 AM', 'ALL', '2nd Year', '1st Semester', 1, '2025-08-11 07:35:30'),
(7, 'PE 201', 'Physical Education 2', 2.0, 'F 3:00-5:00 PM', 'ALL', '2nd Year', '1st Semester', 1, '2025-08-11 07:35:30'),
(8, 'NSTP 201', 'National Service Training Program 2', 3.0, 'S 8:00-11:00 AM', 'ALL', '2nd Year', '1st Semester', 1, '2025-08-11 07:35:30'),
(9, 'IT 101', 'Introduction to Computing', 3.0, 'MWF 8:00-9:00 AM', 'BSIT', '1st Year', '1st Semester', 1, '2025-08-13 15:40:08'),
(10, 'IT 102', 'Computer Programming 1', 3.0, 'TTh 10:00-11:30 AM', 'BSIT', '1st Year', '1st Semester', 1, '2025-08-13 15:40:08'),
(11, 'IT 103', 'Fundamentals of Programming', 3.0, 'MWF 1:00-2:00 PM', 'BSIT', '1st Year', '1st Semester', 1, '2025-08-13 15:40:08'),
(12, 'GE 101', 'Understanding the Self', 3.0, 'TTh 2:30-4:00 PM', 'ALL', '1st Year', '1st Semester', 1, '2025-08-13 15:40:08'),
(13, 'GE 102', 'Readings in Philippine History', 3.0, 'MWF 9:00-10:00 AM', 'ALL', '1st Year', '1st Semester', 1, '2025-08-13 15:40:08'),
(14, 'GE 103', 'The Contemporary World', 3.0, 'TTh 8:00-9:30 AM', 'ALL', '1st Year', '1st Semester', 1, '2025-08-13 15:40:08'),
(15, 'PE 101', 'Physical Education 1', 2.0, 'F 3:00-5:00 PM', 'ALL', '1st Year', '1st Semester', 1, '2025-08-13 15:40:08'),
(16, 'NSTP 101', 'National Service Training Program 1', 3.0, 'S 8:00-11:00 AM', 'ALL', '1st Year', '1st Semester', 1, '2025-08-13 15:40:08'),
(17, 'CS 101', 'Introduction to Computer Science', 3.0, 'MWF 8:00-9:00 AM', 'BSCS', '1st Year', '1st Semester', 1, '2025-08-13 15:40:08'),
(18, 'CS 102', 'Programming Fundamentals', 3.0, 'TTh 10:00-11:30 AM', 'BSCS', '1st Year', '1st Semester', 1, '2025-08-13 15:40:08'),
(19, 'CS 201', 'Data Structures', 3.0, 'MWF 8:00-9:00 AM', 'BSCS', '2nd Year', '1st Semester', 1, '2025-08-13 15:40:08'),
(20, 'CS 202', 'Algorithm Analysis', 3.0, 'TTh 10:00-11:30 AM', 'BSCS', '2nd Year', '1st Semester', 1, '2025-08-13 15:40:08'),
(35, 'CS 301', 'Software Engineering', 3.0, 'M,W 8:00AM-11:00AM Room 301', 'BSCS', '3rd Year', '1st Semester', 1, '2025-08-17 15:13:31'),
(36, 'CS 302', 'Database Systems', 3.0, 'T,TH 1:00PM-4:00PM Room 302', 'BSCS', '3rd Year', '1st Semester', 1, '2025-08-17 15:13:31'),
(37, 'CS 303', 'Web Development', 3.0, 'M,F 10:00AM-1:00PM Room 303', 'BSCS', '3rd Year', '1st Semester', 1, '2025-08-17 15:13:31'),
(38, 'CS 304', 'Computer Networks', 3.0, 'W,F 9:00AM-12:00PM Room 304', 'BSCS', '3rd Year', '2nd Semester', 1, '2025-08-17 15:13:31'),
(39, 'CS 305', 'Human Computer Interaction', 3.0, 'T,TH 8:00AM-11:00AM Room 305', 'BSCS', '3rd Year', '2nd Semester', 1, '2025-08-17 15:13:31'),
(40, 'CS 306', 'Mobile Application Development', 3.0, 'M,T 2:00PM-5:00PM', 'BSCS', '3rd Year', '2nd Semester', 1, '2025-08-17 15:13:31'),
(41, 'CS 401', 'Capstone Project 1', 3.0, 'W,F 1:00PM-4:00PM', 'BSCS', '4th Year', '1st Semester', 1, '2025-08-17 15:13:40'),
(42, 'CS 402', 'Systems Integration', 3.0, 'M,TH 9:00AM-12:00PM', 'BSCS', '4th Year', '1st Semester', 1, '2025-08-17 15:13:40'),
(43, 'CS 403', 'IT Project Management', 3.0, 'T,F 10:00AM-1:00PM', 'BSCS', '4th Year', '1st Semester', 1, '2025-08-17 15:13:40'),
(44, 'CS 404', 'Capstone Project 2', 3.0, 'W,TH 2:00PM-5:00PM', 'BSCS', '4th Year', '2nd Semester', 1, '2025-08-17 15:13:40'),
(45, 'CS 405', 'Professional Ethics in IT', 3.0, 'M,W 10:00AM-12:00PM', 'BSCS', '4th Year', '2nd Semester', 1, '2025-08-17 15:13:40'),
(46, 'CS 406', 'Internship/OJT', 6.0, 'M-F 8:00AM-5:00PM', 'BSCS', '4th Year', '2nd Semester', 1, '2025-08-17 15:13:40'),
(47, 'CS101', 'Introduction to Computer Science', 3.0, 'MWF', 'BSCS', '1st Year', '1st Semester', 1, '2025-08-17 15:52:54'),
(48, 'IT101', 'Fundamentals of Information Technology', 3.0, 'MWF', 'BSIT', '1st Year', '1st Semester', 1, '2025-08-17 15:52:54'),
(49, 'CpE101', 'Computer Engineering Fundamentals', 3.0, 'MWF', 'BSCpE', '1st Year', '1st Semester', 1, '2025-08-17 15:52:54'),
(50, 'ACT101', 'Basic Computer Operations', 3.0, 'MWF', 'ACT', '1st Year', '1st Semester', 1, '2025-08-17 15:52:54'),
(51, 'CCP101', 'Introduction to Programming', 3.0, 'MWF', 'Certificate in Compu', '1st Year', '1st Semester', 1, '2025-08-17 15:52:54'),
(52, 'CWD101', 'HTML & CSS Fundamentals', 3.0, 'MWF', 'Certificate in Web D', '1st Year', '1st Semester', 1, '2025-08-17 15:52:54'),
(53, 'EE101', 'Engineering Mathematics I', 3.0, 'MWF', 'BSEE', '1st Year', '1st Semester', 1, '2025-08-17 15:52:54'),
(54, 'ME101', 'Engineering Drawing', 3.0, 'MWF', 'BSME', '1st Year', '1st Semester', 1, '2025-08-17 15:52:54'),
(55, 'CE101', 'Introduction to Civil Engineering', 3.0, 'MWF', 'BSCE', '1st Year', '1st Semester', 1, '2025-08-17 15:52:54'),
(56, 'IE101', 'Industrial Engineering Overview', 3.0, 'MWF', 'BSIE', '1st Year', '1st Semester', 1, '2025-08-17 15:52:54'),
(57, 'ChE101', 'Chemical Engineering Principles', 3.0, 'MWF', 'BSChE', '1st Year', '1st Semester', 1, '2025-08-17 15:52:54'),
(58, 'Arch101', 'Architectural Design Fundamentals', 3.0, 'MWF', 'BSArch', '1st Year', '1st Semester', 1, '2025-08-17 15:52:54'),
(59, 'BA101', 'Introduction to Business Administration', 3.0, 'MWF', 'BSBA', '1st Year', '1st Semester', 1, '2025-08-17 15:52:54'),
(60, 'ACC101', 'Fundamentals of Accounting', 3.0, 'MWF', 'BSA', '1st Year', '1st Semester', 1, '2025-08-17 15:52:54'),
(61, 'MM101', 'Principles of Marketing', 3.0, 'MWF', 'BSMM', '1st Year', '1st Semester', 1, '2025-08-17 15:52:54'),
(62, 'FM101', 'Financial Management Basics', 3.0, 'MWF', 'BSFM', '1st Year', '1st Semester', 1, '2025-08-17 15:52:54'),
(63, 'HRM101', 'Human Resource Principles', 3.0, 'MWF', 'BSHRM', '1st Year', '1st Semester', 1, '2025-08-17 15:52:54'),
(64, 'ENT101', 'Entrepreneurship Fundamentals', 3.0, 'MWF', 'BSE', '1st Year', '1st Semester', 1, '2025-08-17 15:52:54'),
(65, 'CDM101', 'Digital Marketing Basics', 3.0, 'MWF', 'Certificate in Digit', '1st Year', '1st Semester', 1, '2025-08-17 15:52:54'),
(66, 'HM101', 'Introduction to Hospitality Industry', 3.0, 'MWF', 'BSHM', '1st Year', '1st Semester', 1, '2025-08-17 15:52:54'),
(67, 'TM101', 'Tourism Principles and Practices', 3.0, 'MWF', 'BSTM', '1st Year', '1st Semester', 1, '2025-08-17 15:52:54'),
(68, 'HRM102', 'Hotel and Restaurant Operations', 3.0, 'MWF', 'BSHRM', '1st Year', '1st Semester', 1, '2025-08-17 15:52:54'),
(69, 'EEd101', 'Child and Adolescent Development', 3.0, 'MWF', 'BEEd', '1st Year', '1st Semester', 1, '2025-08-17 15:52:54'),
(70, 'SEd101', 'The Teaching Profession', 3.0, 'MWF', 'BSEd', '1st Year', '1st Semester', 1, '2025-08-17 15:52:54'),
(71, 'NUR101', 'Fundamentals of Nursing', 3.0, 'MWF', 'BSN', '1st Year', '1st Semester', 1, '2025-08-17 15:52:54'),
(72, 'MT101', 'Introduction to Medical Technology', 3.0, 'MWF', 'BSMT', '1st Year', '1st Semester', 1, '2025-08-17 15:52:54'),
(73, 'PT101', 'Foundations of Physical Therapy', 3.0, 'MWF', 'BSPT', '1st Year', '1st Semester', 1, '2025-08-17 15:52:54'),
(74, 'RT101', 'Radiologic Science Basics', 3.0, 'MWF', 'BSRT', '1st Year', '1st Semester', 1, '2025-08-17 15:52:54'),
(75, 'PSY101', 'General Psychology', 3.0, 'MWF', 'BS Psychology', '1st Year', '1st Semester', 1, '2025-08-17 15:52:54'),
(76, 'ABPSY101', 'Introduction to Psychology', 3.0, 'MWF', 'AB Psychology', '1st Year', '1st Semester', 1, '2025-08-17 15:52:54'),
(77, 'CRIM101', 'Introduction to Criminology', 3.0, 'MWF', 'BS Criminology', '1st Year', '1st Semester', 1, '2025-08-17 15:52:54'),
(78, 'COMM101', 'Speech and Oral Communication', 3.0, 'MWF', 'AB Communication', '1st Year', '1st Semester', 1, '2025-08-17 15:52:54'),
(79, 'ENG101', 'College English', 3.0, 'MWF', 'AB English', '1st Year', '1st Semester', 1, '2025-08-17 15:52:54'),
(80, 'MATH101', 'College Algebra', 3.0, 'MWF', 'BS Mathematics', '1st Year', '1st Semester', 1, '2025-08-17 15:52:54'),
(81, 'BIO101', 'General Biology', 3.0, 'MWF', 'BS Biology', '1st Year', '1st Semester', 1, '2025-08-17 15:52:54'),
(82, 'CHEM101', 'General Chemistry', 3.0, 'MWF', 'BS Chemistry', '1st Year', '1st Semester', 1, '2025-08-17 15:52:54'),
(83, 'PHYS101', 'General Physics', 3.0, 'MWF', 'BS Physics', '1st Year', '1st Semester', 1, '2025-08-17 15:52:54'),
(84, 'ENV101', 'Environmental Science', 3.0, 'MWF', 'BS Environmental Sci', '1st Year', '1st Semester', 1, '2025-08-17 15:52:54'),
(85, 'SUMM 101', 'Summer Research', 3.0, 'T,TH 9:00AM-12:00PM', 'ALL', '3rd Year', 'Summer', 1, '2025-08-17 15:56:42'),
(86, 'SUMM 102', 'Practicum', 6.0, 'M-F 8:00AM-5:00PM', 'ALL', '4th Year', 'Summer', 1, '2025-08-17 15:56:42'),
(87, 'SUMM 103', 'Field Study', 2.0, 'W,F 1:00PM-4:00PM', 'ALL', '3rd Year', 'Summer', 1, '2025-08-17 15:56:42');

-- --------------------------------------------------------

--
-- Table structure for table `subject_schedules`
--

CREATE TABLE `subject_schedules` (
  `id` int(11) NOT NULL,
  `subject_code` varchar(10) NOT NULL,
  `day_of_week` varchar(20) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `room` varchar(10) NOT NULL,
  `type` enum('Lecture','Laboratory') DEFAULT 'Lecture',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subject_schedules`
--

INSERT INTO `subject_schedules` (`id`, `subject_code`, `day_of_week`, `start_time`, `end_time`, `room`, `type`, `created_at`) VALUES
(1, 'CS 301', 'Monday', '08:00:00', '11:00:00', '301', 'Lecture', '2025-08-17 16:17:08'),
(2, 'CS 301', 'Wednesday', '08:00:00', '11:00:00', '301', 'Lecture', '2025-08-17 16:17:08'),
(3, 'CS 302', 'Tuesday', '13:00:00', '16:00:00', '302', 'Lecture', '2025-08-17 16:17:08'),
(4, 'CS 302', 'Thursday', '13:00:00', '16:00:00', '302', 'Lecture', '2025-08-17 16:17:08'),
(5, 'CS 304', 'Monday', '13:00:00', '16:00:00', '304', 'Lecture', '2025-08-17 16:17:08'),
(6, 'CS 304', 'Friday', '09:00:00', '12:00:00', '304', 'Lecture', '2025-08-17 16:17:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `document_submissions`
--
ALTER TABLE `document_submissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `document_validations`
--
ALTER TABLE `document_validations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_student_id` (`student_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `enrolled_subjects`
--
ALTER TABLE `enrolled_subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_enrollment` (`student_id`,`subject_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `idx_student_id` (`student_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_enrollment` (`student_id`,`semester`,`school_year`),
  ADD KEY `idx_student_id` (`student_id`),
  ADD KEY `idx_school_year` (`school_year`),
  ADD KEY `idx_status` (`enrollment_status`),
  ADD KEY `fk_enrollments_section` (`section_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_email` (`user_email`),
  ADD KEY `idx_user_type` (`user_type`),
  ADD KEY `idx_is_read` (`is_read`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `queue_tickets`
--
ALTER TABLE `queue_tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_student_status` (`student_id`,`status`),
  ADD KEY `idx_department_date` (`department`,`created_at`),
  ADD KEY `idx_status_created` (`status`,`created_at`);

--
-- Indexes for table `registration_logs`
--
ALTER TABLE `registration_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_registration_id` (`registration_id`),
  ADD KEY `idx_action` (`action`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `section_code` (`section_code`);

--
-- Indexes for table `staff_accounts`
--
ALTER TABLE `staff_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `student_accounts`
--
ALTER TABLE `student_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_user_type` (`user_type`);

--
-- Indexes for table `student_assessments`
--
ALTER TABLE `student_assessments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_student_id` (`student_id`),
  ADD KEY `idx_school_year` (`school_year`);

--
-- Indexes for table `student_payments`
--
ALTER TABLE `student_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_student_id` (`student_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_reference` (`reference_number`);

--
-- Indexes for table `student_registrations`
--
ALTER TABLE `student_registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`),
  ADD UNIQUE KEY `email_address` (`email_address`),
  ADD KEY `idx_student_id` (`student_id`),
  ADD KEY `idx_email` (`email_address`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_course` (`desired_course`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subject_code` (`subject_code`),
  ADD KEY `idx_subject_code` (`subject_code`),
  ADD KEY `idx_course_year` (`course_code`,`year_level`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `subject_schedules`
--
ALTER TABLE `subject_schedules`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `document_submissions`
--
ALTER TABLE `document_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `document_validations`
--
ALTER TABLE `document_validations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `enrolled_subjects`
--
ALTER TABLE `enrolled_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=131;

--
-- AUTO_INCREMENT for table `queue_tickets`
--
ALTER TABLE `queue_tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `registration_logs`
--
ALTER TABLE `registration_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=226;

--
-- AUTO_INCREMENT for table `staff_accounts`
--
ALTER TABLE `staff_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `student_accounts`
--
ALTER TABLE `student_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `student_assessments`
--
ALTER TABLE `student_assessments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `student_payments`
--
ALTER TABLE `student_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `student_registrations`
--
ALTER TABLE `student_registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `subject_schedules`
--
ALTER TABLE `subject_schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `enrolled_subjects`
--
ALTER TABLE `enrolled_subjects`
  ADD CONSTRAINT `enrolled_subjects_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `fk_enrollments_section` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `registration_logs`
--
ALTER TABLE `registration_logs`
  ADD CONSTRAINT `registration_logs_ibfk_1` FOREIGN KEY (`registration_id`) REFERENCES `student_registrations` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
