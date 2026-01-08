<?php
// Optional: Check if user is logged in for conditional display
require_once 'frontend/includes/auth.php';

$current_user = getCurrentUserEmail();
$isLoggedIn = !empty($current_user);
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'frontend/includes/header.php'; ?>

<body>        
    <?php include 'frontend/includes/navbar.php'; ?>

    <!-- Hero Section -->
    <div class="hero">
        <div class="hero-content">
        <h1 class="display-4 fw-bold">
            ARE YOU READY FOR <span class="highlight">SUCCESS?</span>
        </h1>
        <p class="lead">Your future is built here. Endless opportunities to lead, learn and grow set the stage<br>
            for you to carve your path to success and leave your mark on the world.</p>
        </div>
    </div>

    <!-- NCST Educational System Section -->
    <section class="section-bg ncst-educ-bg" style="position:relative;">
        <div class="pattern-bg"></div>
        <div class="fade-x"></div>
        <div class="container ncst-content">
            <div class="row align-items-center justify-content-center">
                <div class="col-md-6 d-flex flex-column align-items-center mb-3 mb-md-0">
                    <h2 class="fw-bold mb-0 blue display-1" style="font-size: 9.5rem;">
                        NCST
                    </h2>
                    <h5 class="fw-bold mt-2" style="color: #FFD700;">EDUCATIONAL SYSTEM</h5>
                </div>
                <div class="col-md-6 d-flex align-items-center">
                    <p class="mt-4 mt-md-0 mx-md-0 fs-5" style="max-width: 800px;">
                        The <span class="blue fw-semibold">NCST Educational System</span> is a structured framework through which education is delivered. It includes institutions, policies, curricula, and methods used to impart knowledge, skills, and values to students at various levels.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Programs Section -->
    <section class="section-bg jigsaw-bg">
        <div class="jigsaw-pattern"></div>
        <div class="container">
            <h2 class="fw-bold mb-4 text-center">
                <i class="bi bi-search me-2"></i>Find Your Course
            </h2>
            <div class="row justify-content-center mb-4">
                <div class="col-md-8">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="courseSearch" placeholder="Search for a course...">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    </div>
                    <select class="form-select text-center" id="courseDropdown" size="8">
                        <option class="text-center">No courses found</option>
                    </select>
                    <div id="courseIndicator" class="mt-2 text-center fw-bold"></div>
                </div>
            </div>
        </div>
    </section>
    <script>
    // List of all courses
    const courses = [
        "Bachelor of Arts in Communication",
        "Associate in Computer Technology",
        "Associate in Office Management",
        "Bachelor of Science in Architecture",
        "Bachelor Of Science In Business Administration-Operations Management",
        "Bachelor of Science in Electronics Engineering",
        "Bachelor of Science in Entrepreneurship",
        "Bachelor Of Science In Hospitality Management",
        "Bachelor of Science in Industrial Engineering",
        "Bachelor of Science in Industrial Security Management",
        "Bachelor of Science in Management Accounting",
        "Bachelor Of Science In Public Administration",
        "Bachelor Science in Real Estate Management",
        "Bachelor of Science in Accountancy",
        "Bachelor of Science in Computer Engineering",
        "Bachelor of Science in Computer Science",
        "Bachelor of Science in Criminology",
        "Bachelor of Science in Customs Administration",
        "Bachelor of Science in Information Technology",
        "Bachelor of Science in Office Administration",
        "Bachelor of Science in Psychology",
        "Bachelor of Science in Tourism Management",
        "Bachelor of Science in Business Administration Major in Financial Management",
        "Bachelor of Science in Business Administration Major in Marketing-Management",
        "Bachelor of Secondary Education Major in English",
        "Bachelor of Secondary Education Major in Filipino",
        "Bachelor of Secondary Education Major in Mathematics",
        "Bachelor of Secondary Education Major in Social Studies",
        "Professional Educational Units",
        "Teacher Certificate Program"
    ];
    const searchInput = document.getElementById('courseSearch');
    const dropdown = document.getElementById('courseDropdown');
    const indicator = document.getElementById('courseIndicator');
    // Make dropdown view-only
    dropdown.setAttribute('disabled', 'disabled');
    // Default: show only "No courses found" centered
    dropdown.innerHTML = '<option class="text-center">No courses found</option>';
    indicator.textContent = '';
    searchInput.addEventListener('input', function() {
        const val = this.value.trim().toLowerCase();
        dropdown.innerHTML = '';
        indicator.textContent = '';
        if(val === '') {
            dropdown.innerHTML = '<option class="text-center">No courses found</option>';
            return;
        }
        let found = false;
        courses.forEach(course => {
            if(course.toLowerCase().includes(val)) {
                const opt = document.createElement('option');
                opt.textContent = course;
                opt.className = 'text-center';
                dropdown.appendChild(opt);
                found = true;
            }
        });
        if(found) {
            indicator.innerHTML = '<span class="text-success"><i class="bi bi-check-circle-fill"></i> Course available!</span>';
        } else {
            dropdown.innerHTML = '<option class="text-center">No courses found</option>';
            indicator.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle-fill"></i> Course not available.</span>';
        }
    });
    </script>

    <!-- Campus Life Section -->
    <section class="section-bg py-5" style="background-color: rgb(37, 52, 117);">
        <div class="container">
            <div class="row align-items-center g-4">
                <!-- Left Column: Carousel -->
                <div class="col-lg-6">
                    <div id="campusLifeCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
                        <div class="carousel-inner rounded shadow">
                            <div class="carousel-item active">
                                <img src="/enrollmentsystem/assets/images/org.jpg" class="d-block w-100" alt="Student Organizations" style="height: 400px; object-fit: cover;">
                            </div>
                            <div class="carousel-item">
                                <img src="/enrollmentsystem/assets/images/events.jpg" class="d-block w-100" alt="Events & Activities" style="height: 400px; object-fit: cover;">
                            </div>
                            <div class="carousel-item">
                                <img src="/enrollmentsystem/assets/images/campus.jpg" class="d-block w-100" alt="Campus" style="height: 400px; object-fit: cover;">
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#campusLifeCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#campusLifeCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                        <!-- Indicators -->
                        <div class="carousel-indicators">
                            <button type="button" data-bs-target="#campusLifeCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                            <button type="button" data-bs-target="#campusLifeCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                            <button type="button" data-bs-target="#campusLifeCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column: Dynamic Text Content -->
                <div class="col-lg-6 text-white">
                    <div class="ps-lg-4">
                        <!-- Text Block 1 - Student Organizations -->
                        <div id="textBlock0" class="campus-text-content">
                            <h2 class="fw-bold mb-3" style="color: #FFD700;">Student Organizations</h2>
                            <p class="lead mb-4">Join clubs and organizations to develop your interests, leadership, and friendships. Our diverse range of student organizations provides opportunities for personal growth, networking, and making lasting connections.</p>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-warning me-2"></i>Academic Organizations</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-warning me-2"></i>Cultural & Arts Clubs</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-warning me-2"></i>Sports Teams</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-warning me-2"></i>Community Service Groups</li>
                            </ul>
                        </div>
                        
                        <!-- Text Block 2 - Events & Activities -->
                        <div id="textBlock1" class="campus-text-content d-none">
                            <h2 class="fw-bold mb-3" style="color: #FFD700;">Events & Activities</h2>
                            <p class="lead mb-4">Participate in sports, cultural events, seminars, and community outreach programs. Our calendar is packed with exciting activities that enhance your college experience and build lasting memories.</p>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="bi bi-calendar-event-fill text-warning me-2"></i>Annual Sports Festival</li>
                                <li class="mb-2"><i class="bi bi-music-note-beamed text-warning me-2"></i>Cultural Nights</li>
                                <li class="mb-2"><i class="bi bi-book-fill text-warning me-2"></i>Academic Symposiums</li>
                                <li class="mb-2"><i class="bi bi-heart-fill text-warning me-2"></i>Community Outreach</li>
                            </ul>
                        </div>
                        
                        <!-- Text Block 3 - Campus -->
                        <div id="textBlock2" class="campus-text-content d-none">
                            <h2 class="fw-bold mb-3" style="color: #FFD700;">Campus Life</h2>
                            <p class="lead mb-4">Explore our beautiful campus and vibrant student community. Our modern facilities and green spaces provide the perfect environment for learning, socializing, and personal development.</p>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="bi bi-building-fill text-warning me-2"></i>Modern Classrooms</li>
                                <li class="mb-2"><i class="bi bi-book text-warning me-2"></i>Well-Equipped Library</li>
                                <li class="mb-2"><i class="bi bi-tree-fill text-warning me-2"></i>Green Spaces</li>
                                <li class="mb-2"><i class="bi bi-cup-hot-fill text-warning me-2"></i>Student Lounges</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Bootstrap JavaScript for dynamic text switching
        document.addEventListener('DOMContentLoaded', function() {
            const carousel = document.getElementById('campusLifeCarousel');
            
            if (carousel) {
                carousel.addEventListener('slide.bs.carousel', function(event) {
                    // Hide all text blocks
                    const textBlocks = document.querySelectorAll('.campus-text-content');
                    textBlocks.forEach(block => {
                        block.classList.add('d-none');
                    });
                    
                    // Show the corresponding text block
                    const activeTextBlock = document.getElementById('textBlock' + event.to);
                    if (activeTextBlock) {
                        activeTextBlock.classList.remove('d-none');
                    }
                });
            }
        });
    </script>

    <!-- Admissions Section -->
    <section class="section-bg jigsaw-bg" id="admissionsSection">
        <div class="jigsaw-pattern"></div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-xl-6">
                    <div class="card shadow-lg auth-card">
                        <div class="card-body text-center py-5 px-4">
                            <div class="mb-4">
                                <i class="bi bi-mortarboard-fill" style="font-size: 4rem; color: rgb(37, 52, 117);"></i>
                            </div>
                            <h2 class="fw-bold mb-4 blue">Ready to Join NCST?</h2>
                            <div class="mb-4">
                                <h3 class="fw-bold text-primary mb-3" style="font-size: 1.75rem;">
                                    "Your Future Starts Here — Begin Your NCST Journey Today!"
                                </h3>
                                <p class="lead text-muted">
                                    Take the first step towards your dreams. Join thousands of successful graduates who started their journey right here at NCST.
                                </p>
                            </div>
                            <div class="d-grid gap-3 col-8 mx-auto">
                                <a href="frontend/pages/admissions.php" class="btn btn-primary btn-lg py-3">
                                    <i class="bi bi-rocket-takeoff me-2"></i>Start Your Application
                                </a>
                                <small class="text-muted">
                                    Questions? <a href="#" class="text-primary">Contact our admissions team</a>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Section -->
    <?php include 'frontend/includes/footer.php'; ?>
</body>
</html>