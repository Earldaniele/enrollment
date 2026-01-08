<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../includes/auth.php';

// Check if user is logged in using the correct session variable
if (!isset($_SESSION['email'])) {
    header('Location: ../pages/login.php');
    exit;
}

$user_email = $_SESSION['email'];
?>
<!DOCTYPE html>
<html lang="en">
<?php include '../includes/header.php'; ?>
<body class="student-dashboard">
    <?php include '../includes/navbar.php'; ?>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="fw-bold mb-4 text-center">College Registration Form</h2>
                        <?php if(isset($_SESSION['registration_error'])): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $_SESSION['registration_error']; ?>
                                <?php unset($_SESSION['registration_error']); ?>
                            </div>
                        <?php endif; ?>
                        <form method="POST" action="../../action/student/process_registration.php">
                            <!-- Desired Course Section -->
                            <h4 class="mb-3" style="color: rgb(37, 52, 117);">Desired Course</h4>
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label for="DesiredCourse" class="form-label">Select Course</label>
                                    <select name="DesiredCourse" id="DesiredCourse" class="form-control" required>
                                        <option value="" disabled selected>-- Select --</option>
                                        <?php
                                            $courses = json_decode(file_get_contents(__DIR__ . '/../../assets/courses.json'), true);
                                            foreach ($courses as $course) {
                                                if ($course['name'] !== '-- Select --') {
                                                    echo '<option value="' . htmlspecialchars($course['name']) . '">' . htmlspecialchars($course['name']) . '</option>';
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <!-- Personal Information Section -->
                            <h4 class="mb-3" style="color: rgb(37, 52, 117);">Personal Information</h4>
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="LastName" class="form-label">Last Name</label>
                                    <input type="text" name="LastName" id="LastName" class="form-control" required maxlength="100" pattern="^[A-Za-z .'-]+$" title="Letters only, max 100 characters">
                                </div>
                                <div class="col-md-3">
                                    <label for="FirstName" class="form-label">First Name</label>
                                    <input type="text" name="FirstName" id="FirstName" class="form-control" required maxlength="100" pattern="^[A-Za-z .'-]+$" title="Letters only, max 100 characters">
                                </div>
                                <div class="col-md-3">
                                    <label for="MiddleName" class="form-label">Middle Name</label>
                                    <input type="text" name="MiddleName" id="MiddleName" class="form-control" required maxlength="100" pattern="^[A-Za-z .'-]+$" title="Letters only, max 100 characters">
                                </div>
                                <div class="col-md-3">
                                    <label for="Suffix" class="form-label">Suffix</label>
                                    <input type="text" name="Suffix" id="Suffix" class="form-control" maxlength="10" pattern="^[A-Za-z .'-]+$" title="Letters only, max 10 characters">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-10">
                                    <label for="CompleteAddress" class="form-label">Complete Address</label>
                                    <input type="text" name="CompleteAddress" id="CompleteAddress" class="form-control" required maxlength="300" title="Max 300 characters">
                                </div>
                                <div class="col-md-2">
                                    <label for="ZipCode" class="form-label">Zip Code</label>
                                    <input type="number" name="ZipCode" id="ZipCode" class="form-control" required min="1000" max="9999" inputmode="numeric" title="4 digit zip code">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="Region" class="form-label">Region</label>
                                    <select name="Region" id="Region" class="form-control" required>
                                        <option value="" disabled selected>-- Select --</option>
                                        <?php
                                            $regions = json_decode(file_get_contents(__DIR__ . '/../../assets/refregion.json'), true);
                                            foreach ($regions as $region) {
                                                echo '<option value="' . htmlspecialchars($region['regDesc']) . '" data-code="' . htmlspecialchars($region['regCode']) . '">' . htmlspecialchars($region['regDesc']) . '</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="Province" class="form-label">Province</label>
                                    <select name="Province" id="Province" class="form-control" required>
                                        <option value="" disabled selected>-- Select --</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="Town" class="form-label">Town/Municipality/City</label>
                                    <select name="Town" id="Town" class="form-control" required>
                                        <option value="" disabled selected>-- Select --</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="Barangay" class="form-label">Barangay</label>
                                    <select name="Barangay" id="Barangay" class="form-control" required>
                                        <option value="" disabled selected>-- Select --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="LandLine" class="form-label">Land Line</label>
                                    <input type="text" name="LandLine" id="LandLine" class="form-control" maxlength="20" pattern="^[0-9- ]*$" title="Numbers and dashes only, max 20 characters">
                                </div>
                                <div class="col-md-3">
                                    <label for="MobileNo" class="form-label">Mobile No</label>
                                    <input type="text" name="MobileNo" id="MobileNo" class="form-control" required maxlength="12" pattern="^(09\d{9}|639\d{9})$" inputmode="numeric" title="11 digits starting with 09 or 12 digits starting with 639">
                                </div>
                                <div class="col-md-3">
                                    <label for="Gender" class="form-label">Gender</label>
                                    <select name="Gender" id="Gender" class="form-control" required>
                                        <option value="" disabled selected>-- Select --</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="CivilStatus" class="form-label">Civil Status</label>
                                    <select name="CivilStatus" id="CivilStatus" class="form-control" required>
                                        <option value="" disabled selected>-- Select --</option>
                                        <option value="Single">Single</option>
                                        <option value="Married">Married</option>
                                        <option value="Widowed">Widowed</option>
                                        <option value="Separated">Separated</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="Nationality" class="form-label">Nationality</label>
                                    <select name="Nationality" id="Nationality" class="form-control" required>
                                        <option value="" disabled selected>-- Select --</option>
                                        <option value="Filipino">Filipino</option>
                                        <option value="American">American</option>
                                        <option value="Chinese">Chinese</option>
                                        <option value="Japanese">Japanese</option>
                                        <option value="Korean">Korean</option>
                                        <option value="Indian">Indian</option>
                                        <option value="British">British</option>
                                        <option value="German">German</option>
                                        <option value="French">French</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="DateOfBirth" class="form-label">Date Of Birth</label>
                                    <input type="date" name="DateOfBirth" id="DateOfBirth" class="form-control" required max="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="PlaceOfBirth" class="form-label">Place of Birth</label>
                                    <input type="text" name="PlaceOfBirth" id="PlaceOfBirth" class="form-control" required maxlength="50" pattern="^[A-Za-z .'-]+$" title="Letters only, max 50 characters">
                                </div>
                                <div class="col-md-3">
                                    <label for="EmailAddress" class="form-label">Email Address</label>
                                    <input type="email" name="EmailAddress" id="EmailAddress" class="form-control" value="<?php echo htmlspecialchars($user_email); ?>" readonly required maxlength="100" title="Valid email, max 100 characters">
                                    <small class="text-muted">This is your registered account email</small>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="Religion" class="form-label">Religion</label>
                                    <select name="Religion" id="Religion" class="form-control" required>
                                        <option value="" disabled selected>-- Select --</option>
                                        <option value="Roman Catholic">Roman Catholic</option>
                                        <option value="Iglesia ni Cristo">Iglesia ni Cristo</option>
                                        <option value="Islam">Islam</option>
                                        <option value="Protestant">Protestant</option>
                                        <option value="Evangelical">Evangelical</option>
                                        <option value="Buddhist">Buddhist</option>
                                        <option value="Hindu">Hindu</option>
                                        <option value="Born Again">Born Again</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <!-- Educational Background -->
                            <h4 class="mb-3" style="color: rgb(37, 52, 117);">Educational Background</h4>
                            <div class="row mb-3">
                                <div class="col-md-10">
                                    <label for="TertiarySchoolName" class="form-label">Tertiary School Name</label>
                                    <select name="TertiarySchoolName" id="TertiarySchoolName" class="form-control" required>
                                        <option value="" disabled selected>-- Select --</option>
                                        <?php
                                            $schools = json_decode(file_get_contents(__DIR__ . '/../../assets/tertiary-schools.json'), true);
                                            foreach ($schools as $school) {
                                                if ($school['name'] !== '-- Select --') {
                                                    echo '<option value="' . htmlspecialchars($school['name']) . '">' . htmlspecialchars($school['name']) . '</option>';
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="TertiaryYearGrad" class="form-label">Year Graduated</label>
                                    <input type="number" name="TertiaryYearGrad" id="TertiaryYearGrad" class="form-control" required min="1900" max="2030" inputmode="numeric" title="4 digit year">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label for="AchievementID" class="form-label">Academic Achievement</label>
                                    <select name="AchievementID" id="AchievementID" class="form-control">
                                        <option value="0" disabled selected>-- Select --</option>
                                        <option value="4">None</option>
                                        <option value="3">Honorable Mention</option>
                                        <option value="2">Salutatorian</option>
                                        <option value="1">Valedictorian</option>
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <h4 class="mb-3" style="color: rgb(37, 52, 117);">Work Information</h4>
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <label for="working" class="form-label">Working?</label>
                                    <input type="checkbox" name="working" id="working" class="form-check-input">
                                </div>
                                <div class="col-md-5">
                                    <label for="Employer" class="form-label">Employer</label>
                                    <input type="text" name="Employer" id="Employer" class="form-control" maxlength="100" pattern="^[A-Za-z .'-]+$" title="Letters only, max 100 characters" disabled>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <label for="WorkinShifts" class="form-label">Work in Shifts?</label>
                                    <input type="checkbox" name="WorkinShifts" id="WorkinShifts" class="form-check-input" disabled>
                                </div>
                                <div class="col-md-4">
                                    <label for="WorkPosition" class="form-label">Work Position</label>
                                    <input type="text" name="WorkPosition" id="WorkPosition" class="form-control" maxlength="100" pattern="^[A-Za-z .'-]+$" title="Letters only, max 100 characters" disabled>
                                </div>
                            </div>
                            <hr>
                            <h4 class="mb-3" style="color: rgb(37, 52, 117);">Family Connected to NCST</h4>
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <label for="StudentConnection" class="form-label">NCST Student</label>
                                    <input type="checkbox" name="StudentConnection" id="StudentConnection" class="form-check-input">
                                </div>
                                <div class="col-md-auto">
                                    <label for="NoOfSiblings" class="form-label">No of Siblings</label>
                                    <input type="number" name="NoOfSiblings" id="NoOfSiblings" class="form-control" disabled>
                                </div>
                                <div class="col-md-2">
                                    <label for="EmployeeConnection" class="form-label">NCST Employee</label>
                                    <input type="checkbox" name="EmployeeConnection" id="EmployeeConnection" class="form-check-input">
                                </div>
                                <div class="col-md-3">
                                    <label for="SchoolRelationID" class="form-label">Relationship</label>
                                    <select name="SchoolRelationID" id="SchoolRelationID" class="form-control" disabled>
                                        <option value="0" disabled selected>-- Select --</option>
                                        <option value="7">Aunt-NCST Employee</option>
                                        <option value="4">Brother-NCST Employee</option>
                                        <option value="2">Father-NCST Employee</option>
                                        <option value="3">Mother-NCST Employee</option>
                                        <option value="1">No NCST Relations</option>
                                        <option value="5">Sister-NCST Employee</option>
                                        <option value="6">Uncle-NCST Employee</option>
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <h4 class="mb-3" style="color: rgb(37, 52, 117);">How did Student come to know about NCST?</h4>
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="StudentMKtg" class="form-label">Source</label>
                                    <select name="StudentMKtg" id="StudentMKtg" class="form-control" required>
                                        <option value="" disabled selected>-- Select --</option>
                                        <option value="CareerTalk">CareerTalk</option>
                                        <option value="Posters">Posters</option>
                                        <option value="Leaflets">Leaflets</option>
                                        <option value="Friends/Relatives">Friends/Relatives</option>
                                        <option value="Billboards/Streamers">Billboards/Streamers</option>
                                        <option value="Others">Others</option>
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <h4 class="mb-3" style="color: rgb(37, 52, 117);">Other Information</h4>
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <label for="Transferee" class="form-label">Transferee?</label>
                                    <input type="checkbox" name="Transferee" id="Transferee" class="form-check-input">
                                </div>
                                <div class="col-md-2">
                                    <label for="Shifting" class="form-label">Shifting?</label>
                                    <input type="checkbox" name="Shifting" id="Shifting" class="form-check-input">
                                </div>
                            </div>
                            <hr>
                            <h5 class="mb-2" style="color: rgb(37, 52, 117);">Father Information</h5>
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <label for="FathersName" class="form-label">Family Name</label>
                                    <input type="text" name="FathersName" id="FathersName" class="form-control" required maxlength="100" pattern="^[A-Za-z .'-]+$" title="Letters only, max 100 characters">
                                </div>
                                <div class="col-md-2">
                                    <label for="FathersGivenName" class="form-label">Given Name</label>
                                    <input type="text" name="FathersGivenName" id="FathersGivenName" class="form-control" required maxlength="100" pattern="^[A-Za-z .'-]+$" title="Letters only, max 100 characters">
                                </div>
                                <div class="col-md-2">
                                    <label for="FathersMiddleName" class="form-label">Middle Name</label>
                                    <input type="text" name="FathersMiddleName" id="FathersMiddleName" class="form-control" required maxlength="100" pattern="^[A-Za-z .'-]+$" title="Letters only, max 100 characters">
                                </div>
                                <div class="col-md-2">
                                    <label for="isDeceasedFather" class="form-label">Deceased?</label>
                                    <input type="checkbox" name="isDeceasedFather" id="isDeceasedFather" class="form-check-input">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-10">
                                    <label for="FathersAddress" class="form-label">Father's Complete Address</label>
                                    <input type="text" name="FathersAddress" id="FathersAddress" class="form-control" required maxlength="500" title="Max 500 characters">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="FathersPhoneNo" class="form-label">Father's Land Line</label>
                                    <input type="text" name="FathersPhoneNo" id="FathersPhoneNo" class="form-control" maxlength="20" pattern="^[0-9- ]*$" title="Numbers and dashes only, max 20 characters">
                                </div>
                                <div class="col-md-3">
                                    <label for="FathersMobileNo" class="form-label">Father's Mobile No</label>
                                    <input type="text" name="FathersMobileNo" id="FathersMobileNo" class="form-control" required maxlength="12" pattern="^(09\d{9}|639\d{9})$" inputmode="numeric" title="11 digits starting with 09 or 12 digits starting with 639">
                                </div>
                                <div class="col-md-3">
                                    <label for="FathersOccupation" class="form-label">Father's Occupation</label>
                                    <input type="text" name="FathersOccupation" id="FathersOccupation" class="form-control" required maxlength="100" pattern="^[A-Za-z .'-]+$" title="Letters only, max 100 characters">
                                </div>
                            </div>
                            <hr>
                            <h5 class="mb-2" style="color: rgb(37, 52, 117);">Mother Information</h5>
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <label for="MotherFamilyName" class="form-label">Family Name</label>
                                    <input type="text" name="MotherFamilyName" id="MotherFamilyName" class="form-control" required maxlength="100" pattern="^[A-Za-z .'-]+$" title="Letters only, max 100 characters">
                                </div>
                                <div class="col-md-2">
                                    <label for="MotherGivenName" class="form-label">Given Name</label>
                                    <input type="text" name="MotherGivenName" id="MotherGivenName" class="form-control" required maxlength="100" pattern="^[A-Za-z .'-]+$" title="Letters only, max 100 characters">
                                </div>
                                <div class="col-md-2">
                                    <label for="MotherMiddleName" class="form-label">Middle Name</label>
                                    <input type="text" name="MotherMiddleName" id="MotherMiddleName" class="form-control" required maxlength="100" pattern="^[A-Za-z .'-]+$" title="Letters only, max 100 characters">
                                </div>
                                <div class="col-md-2">
                                    <label for="isDeceasedMother" class="form-label">Deceased?</label>
                                    <input type="checkbox" name="isDeceasedMother" id="isDeceasedMother" class="form-check-input">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-10">
                                    <label for="MothersAddress" class="form-label">Mother's Complete Address</label>
                                    <input type="text" name="MothersAddress" id="MothersAddress" class="form-control" required maxlength="500" title="Max 500 characters">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="MothersPhoneNo" class="form-label">Mother's Land Line</label>
                                    <input type="text" name="MothersPhoneNo" id="MothersPhoneNo" class="form-control" maxlength="20" pattern="^[0-9- ]*$" title="Numbers and dashes only, max 20 characters">
                                </div>
                                <div class="col-md-3">
                                    <label for="MothersMobileNo" class="form-label">Mother's Mobile No</label>
                                    <input type="text" name="MothersMobileNo" id="MothersMobileNo" class="form-control" required maxlength="12" pattern="^(09\d{9}|639\d{9})$" inputmode="numeric" title="11 digits starting with 09 or 12 digits starting with 639">
                                </div>
                                <div class="col-md-3">
                                    <label for="MothersOccupation" class="form-label">Mother's Occupation</label>
                                    <input type="text" name="MothersOccupation" id="MothersOccupation" class="form-control" required maxlength="100" pattern="^[A-Za-z .'-]+$" title="Letters only, max 100 characters">
                                </div>
                            </div>
                            <hr>
                            <h4 class="mb-3" style="color: rgb(37, 52, 117);">Guardian Information</h4>
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <label for="GuardianFamilyName" class="form-label">Family Name</label>
                                    <input type="text" name="GuardianFamilyName" id="GuardianFamilyName" class="form-control" required maxlength="100" pattern="^[A-Za-z .'-]+$" title="Letters only, max 100 characters">
                                </div>
                                <div class="col-md-2">
                                    <label for="GuardianGivenName" class="form-label">Given Name</label>
                                    <input type="text" name="GuardianGivenName" id="GuardianGivenName" class="form-control" required maxlength="100" pattern="^[A-Za-z .'-]+$" title="Letters only, max 100 characters">
                                </div>
                                <div class="col-md-2">
                                    <label for="GuardianMiddleName" class="form-label">Middle Name</label>
                                    <input type="text" name="GuardianMiddleName" id="GuardianMiddleName" class="form-control" required maxlength="100" pattern="^[A-Za-z .'-]+$" title="Letters only, max 100 characters">
                                </div>
                                <div class="col-md-3">
                                    <label for="GuardianPhoneNo" class="form-label">Guardian Land Line</label>
                                    <input type="text" name="GuardianPhoneNo" id="GuardianPhoneNo" class="form-control" maxlength="20" pattern="^[0-9- ]*$" title="Numbers and dashes only, max 20 characters">
                                </div>
                                <div class="col-md-3">
                                    <label for="GuardianMobileNo" class="form-label">Guardian Mobile No</label>
                                    <input type="text" name="GuardianMobileNo" id="GuardianMobileNo" class="form-control" required maxlength="12" pattern="^(09\d{9}|639\d{9})$" inputmode="numeric" title="11 digits starting with 09 or 12 digits starting with 639">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="GuardianAddress" class="form-label">Guardian Complete Address</label>
                                    <input type="text" name="GuardianAddress" id="GuardianAddress" class="form-control" required maxlength="500" title="Max 500 characters">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="GuardianOccupation" class="form-label">Guardian Occupation</label>
                                    <input type="text" name="GuardianOccupation" id="GuardianOccupation" class="form-control" required maxlength="100" pattern="^[A-Za-z .'-]+$" title="Letters only, max 100 characters">
                                </div>
                                <div class="col-md-6">
                                    <label for="GuardianRelationship" class="form-label">Guardian Relationship</label>
                                    <input type="text" name="GuardianRelationship" id="GuardianRelationship" class="form-control" required maxlength="100" pattern="^[A-Za-z .'-]+$" title="Letters only, max 100 characters">
                                </div>
                            </div>
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-success btn-lg">Submit Application</button>
                            </div>
                        </form>
                    </div> <!-- card-body -->
                </div> <!-- card -->
            </div> <!-- col-lg-10 -->
        </div> <!-- row -->
    </div> <!-- container -->
    <script>
        // Improved: async/await, error handling, loading indicators, robust element checks
        document.addEventListener('DOMContentLoaded', function() {
            let provinces = [];

            // Utility to set loading state
            function setLoading(select, msg = 'Loading...') {
                if (select) select.innerHTML = `<option value="" disabled selected>${msg}</option>`;
            }

            // Utility to set empty state
            function setEmpty(select, msg = '-- Select --') {
                if (select) select.innerHTML = `<option value="" disabled selected>${msg}</option>`;
            }

            // Async fetch wrapper
            async function fetchJSON(url) {
                try {
                    const res = await fetch(url);
                    if (!res.ok) throw new Error('Network error');
                    return await res.json();
                } catch (e) {
                    console.error('Error fetching', url, e);
                    return null;
                }
            }

            // Phone number input restriction - only allow numbers
            const phoneInputs = ['MobileNo', 'FathersMobileNo', 'MothersMobileNo', 'GuardianMobileNo'];
            phoneInputs.forEach(inputId => {
                const input = document.getElementById(inputId);
                if (input) {
                    input.addEventListener('input', function(e) {
                        // Remove any non-digit characters
                        this.value = this.value.replace(/\D/g, '');
                    });
                }
            });

            // Number input validation and restriction
            const numberInputs = [
                { id: 'ZipCode', min: 1000, max: 9999, maxLength: 4 },
                { id: 'TertiaryYearGrad', min: 1900, max: 2030, maxLength: 4 },
                { id: 'NoOfSiblings', min: 0, max: 20, maxLength: 2 }
            ];

            numberInputs.forEach(config => {
                const input = document.getElementById(config.id);
                if (input) {
                    // Prevent typing beyond max length
                    input.addEventListener('input', function(e) {
                        // Remove any non-digit characters for number inputs
                        let value = this.value.replace(/\D/g, '');
                        
                        // Limit length
                        if (value.length > config.maxLength) {
                            value = value.substring(0, config.maxLength);
                        }
                        
                        this.value = value;
                        
                        // Validate range if value is complete
                        if (value.length === config.maxLength || value.length > 0) {
                            const numValue = parseInt(value);
                            if (numValue < config.min || numValue > config.max) {
                                this.setCustomValidity(`Value must be between ${config.min} and ${config.max}`);
                            } else {
                                this.setCustomValidity('');
                            }
                        }
                    });

                    // Also validate on blur
                    input.addEventListener('blur', function(e) {
                        const value = parseInt(this.value);
                        if (this.value && (value < config.min || value > config.max)) {
                            this.setCustomValidity(`Value must be between ${config.min} and ${config.max}`);
                            this.reportValidity();
                        } else {
                            this.setCustomValidity('');
                        }
                    });

                    // Prevent pasting invalid content
                    input.addEventListener('paste', function(e) {
                        e.preventDefault();
                        const paste = (e.clipboardData || window.clipboardData).getData('text');
                        const numericPaste = paste.replace(/\D/g, '').substring(0, config.maxLength);
                        this.value = numericPaste;
                        
                        // Trigger input event to validate
                        this.dispatchEvent(new Event('input'));
                    });
                }
            });

            // Working checkbox functionality
            const workingCheckbox = document.getElementById('working');
            const employerInput = document.getElementById('Employer');
            const workPositionInput = document.getElementById('WorkPosition');
            const workShiftsCheckbox = document.getElementById('WorkinShifts');

            if (workingCheckbox) {
                workingCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        // Enable work-related fields
                        employerInput.disabled = false;
                        workPositionInput.disabled = false;
                        workShiftsCheckbox.disabled = false;
                    } else {
                        // Disable work-related fields and clear values
                        employerInput.disabled = true;
                        employerInput.value = '';
                        workPositionInput.disabled = true;
                        workPositionInput.value = '';
                        workShiftsCheckbox.disabled = true;
                        workShiftsCheckbox.checked = false;
                    }
                });
            }

            // NCST Student connection checkbox functionality
            const studentConnectionCheckbox = document.getElementById('StudentConnection');
            const siblingsInput = document.getElementById('NoOfSiblings');

            if (studentConnectionCheckbox) {
                studentConnectionCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        // Enable siblings field
                        siblingsInput.disabled = false;
                    } else {
                        // Disable siblings field and clear value
                        siblingsInput.disabled = true;
                        siblingsInput.value = '';
                    }
                });
            }

            // NCST Employee connection checkbox functionality
            const employeeConnectionCheckbox = document.getElementById('EmployeeConnection');
            const relationshipSelect = document.getElementById('SchoolRelationID');

            if (employeeConnectionCheckbox) {
                employeeConnectionCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        // Enable relationship field
                        relationshipSelect.disabled = false;
                    } else {
                        // Disable relationship field and reset value
                        relationshipSelect.disabled = true;
                        relationshipSelect.value = '0';
                    }
                });
            }

            // Age calculation from date of birth
            const dobInput = document.getElementById('DateOfBirth');
            if (dobInput) {
                dobInput.addEventListener('change', function() {
                    const dob = new Date(this.value);
                    const today = new Date();
                    let age = today.getFullYear() - dob.getFullYear();
                    const monthDiff = today.getMonth() - dob.getMonth();
                    
                    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
                        age--;
                    }
                    
                    // You can display the age or use it for validation
                    console.log('Calculated age:', age);
                    
                    // Optional: Show age below the date input
                    let ageDisplay = document.getElementById('ageDisplay');
                    if (!ageDisplay) {
                        ageDisplay = document.createElement('div');
                        ageDisplay.id = 'ageDisplay';
                        ageDisplay.className = 'form-text text-muted';
                        dobInput.parentNode.appendChild(ageDisplay);
                    }
                    ageDisplay.textContent = `Age: ${age} years old`;
                });
            }

            // Populate provinces
            async function loadProvinces() {
                provinces = await fetchJSON('/enrollmentsystem/assets/refprovince.json') || [];
            }

            // Initial load
            (async function() {
                await loadProvinces();
            })();

            // Region change
            const regionSelect = document.getElementById('Region');
            const provinceSelect = document.getElementById('Province');
            const townSelect = document.getElementById('Town');
            const barangaySelect = document.getElementById('Barangay');

            if (regionSelect) {
                regionSelect.addEventListener('change', function() {
                    setLoading(provinceSelect);
                    setEmpty(townSelect);
                    setEmpty(barangaySelect);
                    window.currentCities = null;
                    window.currentBarangays = null;
                    const selectedOption = this.options[this.selectedIndex];
                    const regCode = selectedOption.getAttribute('data-code');
                    const filteredProvinces = provinces.filter(p => p.regCode === regCode);
                    setEmpty(provinceSelect);
                    filteredProvinces.forEach(p => {
                        if (p.provDesc) {
                            provinceSelect.innerHTML += `<option value="${p.provDesc}" data-code="${p.provCode}">${p.provDesc}</option>`;
                        }
                    });
                });
            }

            if (provinceSelect) {
                provinceSelect.addEventListener('change', async function() {
                    setLoading(townSelect);
                    setEmpty(barangaySelect);
                    const selectedOption = this.options[this.selectedIndex];
                    const provCode = selectedOption.getAttribute('data-code');
                    // Load cities
                    const cities = await fetchJSON(`/enrollmentsystem/assets/citymun-by-province/citymun-${provCode}.json`);
                    window.currentCities = cities || [];
                    setEmpty(townSelect);
                    window.currentCities.forEach(c => {
                        if (c.citymunDesc) {
                            townSelect.innerHTML += `<option value="${c.citymunDesc}" data-code="${c.citymunCode}">${c.citymunDesc}</option>`;
                        }
                    });
                    // Load barangays
                    window.currentBarangays = null;
                    const barangays = await fetchJSON(`/enrollmentsystem/assets/barangay-by-province/barangays-${provCode}.json`);
                    window.currentBarangays = barangays || [];
                });
            }

            if (townSelect) {
                townSelect.addEventListener('change', function() {
                    setLoading(barangaySelect);
                    const selectedOption = this.options[this.selectedIndex];
                    const citymunCode = selectedOption.getAttribute('data-code');
                    setEmpty(barangaySelect);
                    if (window.currentBarangays) {
                        window.currentBarangays.filter(b => b.citymunCode === citymunCode).forEach(b => {
                            if (b.brgyDesc) {
                                barangaySelect.innerHTML += `<option value="${b.brgyDesc}" data-code="${b.brgyCode}">${b.brgyDesc}</option>`;
                            }
                        });
                    }
                });
            }
        });
    </script>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
