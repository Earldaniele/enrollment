<?php
session_start();
require_once '../../frontend/includes/db_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['registration_error'] = 'Invalid request method';
    echo "<!DOCTYPE html>
<html>
<head>
    <title>Registration Error</title>
    <script>
        window.location.href = '../../frontend/student/college-registration.php';
    </script>
</head>
<body>
    <p>Invalid request method. If you are not redirected, <a href='../../frontend/student/college-registration.php'>click here</a>.</p>
</body>
</html>";
    exit;
}

// Validate required fields
$required_fields = [
    'DesiredCourse', 'LastName', 'FirstName', 'MiddleName', 
    'CompleteAddress', 'ZipCode', 'Region', 'Province', 'Town', 'Barangay',
    'MobileNo', 'Gender', 'CivilStatus', 'Nationality', 'DateOfBirth', 
    'PlaceOfBirth', 'EmailAddress', 'Religion'
];

$missing_fields = [];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
        $missing_fields[] = $field;
    }
}

if (!empty($missing_fields)) {
    $_SESSION['registration_error'] = 'Missing required fields: ' . implode(', ', $missing_fields);
    echo "<!DOCTYPE html>
<html>
<head>
    <title>Registration Error</title>
    <script>
        window.location.href = '../../frontend/student/college-registration.php';
    </script>
</head>
<body>
    <p>Missing required fields. If you are not redirected, <a href='../../frontend/student/college-registration.php'>click here</a>.</p>
</body>
</html>";
    exit;
}

try {
    // Generate student ID (format: YYYY-NNNNN)
    $year = date('Y');
    
    // Get next sequential number for this year (5 digits with leading zeros)
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM student_registrations WHERE YEAR(created_at) = ?");
    $stmt->bind_param("s", $year);
    $stmt->execute();
    $result = $stmt->get_result();
    $count_data = $result->fetch_assoc();
    
    $next_number = str_pad($count_data['count'] + 1, 5, '0', STR_PAD_LEFT);
    $student_id = $year . '-' . $next_number;
    
    // Prepare data for insertion
    $desired_course = trim($_POST['DesiredCourse']);
    $last_name = trim($_POST['LastName']);
    $first_name = trim($_POST['FirstName']);
    $middle_name = trim($_POST['MiddleName']);
    $suffix = isset($_POST['Suffix']) ? trim($_POST['Suffix']) : '';
    $complete_address = trim($_POST['CompleteAddress']);
    $zip_code = trim($_POST['ZipCode']);
    $region = trim($_POST['Region']);
    $province = trim($_POST['Province']);
    $town = trim($_POST['Town']);
    $barangay = trim($_POST['Barangay']);
    $land_line = isset($_POST['LandLine']) ? trim($_POST['LandLine']) : '';
    $mobile_no = trim($_POST['MobileNo']);
    $gender = trim($_POST['Gender']);
    $civil_status = trim($_POST['CivilStatus']);
    $nationality = trim($_POST['Nationality']);
    $date_of_birth = trim($_POST['DateOfBirth']);
    $place_of_birth = trim($_POST['PlaceOfBirth']);
    $email_address = trim($_POST['EmailAddress']);
    $religion = trim($_POST['Religion']);
    
    // Family Background (optional)
    $father_name = isset($_POST['FathersName']) ? trim($_POST['FathersName']) : '';
    $father_occupation = isset($_POST['FathersOccupation']) ? trim($_POST['FathersOccupation']) : '';
    $father_contact = isset($_POST['FathersMobileNo']) ? trim($_POST['FathersMobileNo']) : '';
    $mother_name = isset($_POST['MotherFamilyName']) ? trim($_POST['MotherFamilyName']) : '';
    $mother_occupation = isset($_POST['MothersOccupation']) ? trim($_POST['MothersOccupation']) : '';
    $mother_contact = isset($_POST['MothersMobileNo']) ? trim($_POST['MothersMobileNo']) : '';
    $guardian_name = isset($_POST['GuardianFamilyName']) ? trim($_POST['GuardianFamilyName']) : '';
    $guardian_relationship = isset($_POST['GuardianRelationship']) ? trim($_POST['GuardianRelationship']) : '';
    $guardian_contact = isset($_POST['GuardianMobileNo']) ? trim($_POST['GuardianMobileNo']) : '';
    
    // Educational Background (optional)
    $elementary_school = ''; // Not in current form
    $elementary_year = ''; // Not in current form
    $secondary_school = ''; // Not in current form  
    $secondary_year = ''; // Not in current form
    $tertiary_school = isset($_POST['TertiarySchoolName']) ? trim($_POST['TertiarySchoolName']) : '';
    $tertiary_year = isset($_POST['TertiaryYearGrad']) ? trim($_POST['TertiaryYearGrad']) : '';
    
    // Convert academic achievement ID to text
    $academic_achievement_id = isset($_POST['AchievementID']) ? trim($_POST['AchievementID']) : '';
    $academic_achievement = '';
    switch($academic_achievement_id) {
        case '1':
            $academic_achievement = 'Valedictorian';
            break;
        case '2':
            $academic_achievement = 'Salutatorian';
            break;
        case '3':
            $academic_achievement = 'Honorable Mention';
            break;
        case '4':
            $academic_achievement = 'None';
            break;
        default:
            $academic_achievement = '';
    }
    
    // Validate email format
    if (!filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['registration_error'] = 'Invalid email address format';
        echo "<!DOCTYPE html>
<html>
<head>
    <title>Registration Error</title>
    <script>
        window.location.href = '../../frontend/student/college-registration.php';
    </script>
</head>
<body>
    <p>Invalid email format. If you are not redirected, <a href='../../frontend/student/college-registration.php'>click here</a>.</p>
</body>
</html>";
        exit;
    }
    
    // Check if email already exists
    $email_check = $conn->prepare("SELECT id FROM student_registrations WHERE email_address = ?");
    $email_check->bind_param("s", $email_address);
    $email_check->execute();
    if ($email_check->get_result()->num_rows > 0) {
        $_SESSION['registration_error'] = 'Email address already registered';
        echo "<!DOCTYPE html>
<html>
<head>
    <title>Registration Error</title>
    <script>
        window.location.href = '../../frontend/student/college-registration.php';
    </script>
</head>
<body>
    <p>Email already registered. If you are not redirected, <a href='../../frontend/student/college-registration.php'>click here</a>.</p>
</body>
</html>";
        exit;
    }
    
    // Insert registration data
    $sql = "INSERT INTO student_registrations (
        student_id, desired_course, last_name, first_name, middle_name, suffix,
        complete_address, zip_code, region, province, town, barangay,
        land_line, mobile_no, gender, civil_status, nationality, 
        date_of_birth, place_of_birth, email_address, religion,
        father_name, father_occupation, father_contact,
        mother_name, mother_occupation, mother_contact,
        guardian_name, guardian_relationship, guardian_contact,
        tertiary_school, tertiary_year, academic_achievement, status, created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
    
    $insert_stmt = $conn->prepare($sql);
    $insert_stmt->bind_param("sssssssssssssssssssssssssssssssss",
        $student_id, $desired_course, $last_name, $first_name, $middle_name, $suffix,
        $complete_address, $zip_code, $region, $province, $town, $barangay,
        $land_line, $mobile_no, $gender, $civil_status, $nationality,
        $date_of_birth, $place_of_birth, $email_address, $religion,
        $father_name, $father_occupation, $father_contact,
        $mother_name, $mother_occupation, $mother_contact,
        $guardian_name, $guardian_relationship, $guardian_contact,
        $tertiary_school, $tertiary_year, $academic_achievement
    );
    
    if ($insert_stmt->execute()) {
        // Send notification to registrar about new registration
        require_once '../includes/notification_helpers.php';
        $student_full_name = $first_name . ' ' . $middle_name . ' ' . $last_name . ($suffix ? ' ' . $suffix : '');
        notifyRegistrarNewRegistration($student_id, $student_full_name, $desired_course);
        
        // Store registration success message in session
        $_SESSION['registration_success'] = true;
        $_SESSION['registration_message'] = 'Registration submitted successfully!';
        $_SESSION['registration_data'] = [
            'student_id' => $student_id,
            'full_name' => $student_full_name,
            'course' => $desired_course,
            'email' => $email_address
        ];
        
        // Output JavaScript to redirect
        echo "<!DOCTYPE html>
<html>
<head>
    <title>Registration Successful</title>
    <script>
        window.location.href = '../../frontend/student/dashboard.php';
    </script>
</head>
<body>
    <p>Registration successful. If you are not redirected, <a href='../../frontend/student/dashboard.php'>click here</a>.</p>
</body>
</html>";
        exit;
    } else {
        // Output JavaScript to redirect back with error
        $_SESSION['registration_error'] = 'Failed to submit registration';
        echo "<!DOCTYPE html>
<html>
<head>
    <title>Registration Failed</title>
    <script>
        window.location.href = '../../frontend/student/college-registration.php';
    </script>
</head>
<body>
    <p>Registration failed. If you are not redirected, <a href='../../frontend/student/college-registration.php'>click here</a>.</p>
</body>
</html>";
        exit;
    }
    
} catch (Exception $e) {
    $_SESSION['registration_error'] = 'Database error: ' . $e->getMessage();
    echo "<!DOCTYPE html>
<html>
<head>
    <title>Registration Error</title>
    <script>
        window.location.href = '../../frontend/student/college-registration.php';
    </script>
</head>
<body>
    <p>Database error. If you are not redirected, <a href='../../frontend/student/college-registration.php'>click here</a>.</p>
</body>
</html>";
    exit;
}

$conn->close();
?>
