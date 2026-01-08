<?php /* Main Navbar: School Name */ ?>
<nav class="navbar navbar-light border-bottom" style="background: rgb(37, 52, 117);">
  <div class="container py-2 d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-2">
      <img src="/enrollmentsystem/assets/images/ncst-logo.png" alt="School Logo" style="height: 50px;">
      <a class="navbar-brand fw-bold mb-0" href="/enrollmentsystem/index.php" style="font-size: 1.2rem; color: #fff;">
        NATIONAL COLLEGE OF SCIENCE AND TECHNOLOGY
      </a>
    </div>
    <?php
      $hideNav = false;
      $currentPage = basename($_SERVER['PHP_SELF']);
      $currentDir = basename(dirname($_SERVER['PHP_SELF']));
      
      $isAdmissionsOrReg = ($currentPage === 'admissions.php' || $currentPage === 'register.php' || ($currentPage === 'college-registration.php' && $currentDir !== 'student'));
      $isLoginPage = ($currentPage === 'login.php');
      $isStudentPage = ($currentDir === 'student' && ($currentPage === 'dashboard.php' || $currentPage === 'view-enrollment.php' || $currentPage === 'queue-demo.php' || $currentPage === 'college-registration.php' || $currentPage === 'payment.php' || $currentPage === 'payment-success.php'));
      $isEvaluatorPage = ($currentDir === 'evaluator' && $currentPage !== 'login.php');
      $isRegistrarPage = ($currentDir === 'registrar' && $currentPage !== 'login.php');
      $isCashierPage = ($currentDir === 'cashier' && $currentPage !== 'login.php');
      $isStudentAssistantPage = ($currentDir === 'student-assistant' && $currentPage !== 'login.php');
      
      if ($isAdmissionsOrReg || $isLoginPage) {
        $hideNav = true;
      } elseif ($isStudentPage) {
        $hideNav = true;
      } elseif ($isEvaluatorPage) {
        $hideNav = true;
      } elseif ($isRegistrarPage) {
        $hideNav = true;
      } elseif ($isCashierPage) {
        $hideNav = true;
      } elseif ($isStudentAssistantPage) {
        $hideNav = true;
      }
    ?>
    <?php if ($isAdmissionsOrReg): ?>
      <?php 
        // Check if there's a previous page in the session history
        $showGoBack = isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER']);
      ?>
      <?php if ($showGoBack): ?>
        <a href="#" onclick="window.history.back();" class="btn btn-apply-now ms-3">
          <i class="bi bi-arrow-left-circle me-1"></i>Go Back
        </a>
      <?php endif; ?>
    <?php elseif ($isLoginPage): ?>
      <a href="/enrollmentsystem/index.php" class="btn btn-apply-now ms-3">
        <i class="bi bi-arrow-left-circle me-1"></i>Go Back
      </a>
    <?php elseif ($isStudentPage): ?>
      <div class="d-flex gap-2">
        <a href="#" class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#notificationsModal">
          <i class="bi bi-bell me-1"></i>Notifications
        </a>
        <a href="#" class="btn btn-logout" onclick="logoutStudent(); return false;">
          <i class="bi bi-box-arrow-right me-1"></i>Logout
        </a>
      </div>
    <?php elseif ($isEvaluatorPage): ?>
      <div class="d-flex gap-2">
        <a href="#" class="btn btn-logout" onclick="logoutStaff(); return false;">
          <i class="bi bi-box-arrow-right me-1"></i>Logout
        </a>
      </div>
    <?php elseif ($isRegistrarPage): ?>
      <div class="d-flex gap-2">
        <a href="#" class="btn btn-logout" onclick="logoutStaff(); return false;">
          <i class="bi bi-box-arrow-right me-1"></i>Logout
        </a>
      </div>
    <?php elseif ($isCashierPage): ?>
      <div class="d-flex gap-2">
        <a href="#" class="btn btn-logout" onclick="logoutStaff(); return false;">
          <i class="bi bi-box-arrow-right me-1"></i>Logout
        </a>
      </div>
    <?php elseif ($isStudentAssistantPage): ?>
      <div class="d-flex gap-2">
        <a href="#" class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#notificationsModal">
          <i class="bi bi-bell me-1"></i>Notifications
        </a>
        <a href="#" class="btn btn-logout" onclick="logoutStaff(); return false;">
          <i class="bi bi-box-arrow-right me-1"></i>Logout
        </a>
      </div>
    <?php elseif (!$hideNav): ?>
      <div class="d-flex gap-2">
        <a href="/enrollmentsystem/frontend/pages/login.php" class="btn btn-outline-light navbar-login">
          <i class="bi bi-box-arrow-in-right me-1"></i>Login
        </a>
        <a href="/enrollmentsystem/frontend/pages/admissions.php" class="btn btn-apply-now">
          <i class="bi bi-send-plus me-1"></i>Apply Now
        </a>
      </div>
    <?php endif; ?>
  </div>
</nav>

<?php if ($isStudentPage): ?>
<!-- Student Sub Navbar: Student Navigation Links -->
<nav class="navbar navbar-expand-lg navbar-light" style="background: #fff;">
  <div class="container py-0" style="font-size: 0.97rem;">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#studentNavbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="studentNavbarNav">
      <ul class="navbar-nav d-flex justify-content-start w-100">
        <!-- Dashboard -->
        <li class="nav-item border-end">
          <a class="nav-link px-4 <?php echo ($currentPage === 'dashboard.php') ? 'active fw-bold' : ''; ?>" 
             href="dashboard.php" style="color: rgb(37, 52, 117) !important;">
            <i class="bi bi-house-door me-2"></i>DASHBOARD
          </a>
        </li>
        
        <!-- View Enrollment -->
        <li class="nav-item border-end">
          <a class="nav-link px-4 <?php echo ($currentPage === 'view-enrollment.php') ? 'active fw-bold' : ''; ?>" 
             href="view-enrollment.php" style="color: rgb(37, 52, 117) !important;">
            <i class="bi bi-file-text me-2"></i>VIEW ENROLLMENT
          </a>
        </li>
        
        <!-- Get Queue -->
        <li class="nav-item border-end">
          <a class="nav-link px-4" 
             href="dashboard.php#queue-management" style="color: rgb(37, 52, 117) !important;">
            <i class="bi bi-people me-2"></i>GET QUEUE
          </a>
        </li>
        
        <!-- Start Registration -->
        <li class="nav-item border-end">
          <a class="nav-link px-4 <?php echo ($currentPage === 'college-registration.php') ? 'active fw-bold' : ''; ?>" 
             href="college-registration.php" style="color: rgb(37, 52, 117) !important;">
            <i class="bi bi-pencil-square me-2"></i>START REGISTRATION
          </a>
        </li>
        
        <!-- Payment -->
        <li class="nav-item border-end">
          <a class="nav-link px-4 <?php echo ($currentPage === 'payment.php') ? 'active fw-bold' : ''; ?>" 
             href="payment.php" style="color: rgb(37, 52, 117) !important;">
            <i class="bi bi-credit-card me-2"></i>PAYMENT
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<?php endif; ?>

<?php if ($isRegistrarPage): ?>
<!-- Registrar Sub Navbar: Registrar Navigation Links -->
<nav class="navbar navbar-expand-lg navbar-light" style="background: #fff;">
  <div class="container py-0" style="font-size: 0.97rem;">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#registrarNavbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="registrarNavbarNav">
      <ul class="navbar-nav d-flex justify-content-start w-100">
        <!-- Dashboard - Always visible -->
        <li class="nav-item border-end">
          <a class="nav-link px-4 <?php echo ($currentPage === 'index.php') ? 'active fw-bold' : ''; ?>" 
             href="index.php" style="color: rgb(37, 52, 117) !important;">
            <i class="bi bi-house-door me-2"></i>DASHBOARD
          </a>
        </li>
        
        <!-- Student List -->
        <li class="nav-item border-end">
          <a class="nav-link px-4 <?php echo ($currentPage === 'student_list.php') ? 'active fw-bold' : ''; ?>" 
             href="student_list.php" style="color: rgb(37, 52, 117) !important;">
            <i class="bi bi-people me-2"></i>STUDENT LIST
          </a>
        </li>
        
        <!-- Student Search -->
        <li class="nav-item border-end">
          <a class="nav-link px-4 <?php echo ($currentPage === 'student_search.php') ? 'active fw-bold' : ''; ?>" 
             href="student_search.php" style="color: rgb(37, 52, 117) !important;">
            <i class="bi bi-search me-2"></i>SEARCH
          </a>
        </li>
        
        <!-- Document Validation -->
        <li class="nav-item border-end">
          <a class="nav-link px-4 <?php echo ($currentPage === 'document_validation.php') ? 'active fw-bold' : ''; ?>" 
             href="document_validation.php" style="color: rgb(37, 52, 117) !important;">
            <i class="bi bi-file-check me-2"></i>VALIDATION
          </a>
        </li>
        
        <!-- Student Enrollment -->
        <li class="nav-item">
          <a class="nav-link px-4 <?php echo ($currentPage === 'enrollment.php') ? 'active fw-bold' : ''; ?>" 
             href="enrollment.php" style="color: rgb(37, 52, 117) !important;">
            <i class="bi bi-check2-circle me-2"></i>ENROLLMENT
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<?php endif; ?>

<?php if ($isCashierPage): ?>
<!-- Cashier Sub Navbar: Cashier Navigation Links -->
<nav class="navbar navbar-expand-lg navbar-light" style="background: #fff;">
  <div class="container py-0" style="font-size: 0.97rem;">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#cashierNavbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="cashierNavbarNav">
      <ul class="navbar-nav d-flex justify-content-start w-100">
        <!-- Dashboard - Always visible -->
        <li class="nav-item border-end">
          <a class="nav-link px-4 <?php echo ($currentPage === 'index.php') ? 'active fw-bold' : ''; ?>" 
             href="index.php" style="color: rgb(37, 52, 117) !important;">
            <i class="bi bi-house-door me-2"></i>DASHBOARD
          </a>
        </li>
        
        <!-- Student List -->
        <li class="nav-item border-end">
          <a class="nav-link px-4 <?php echo ($currentPage === 'student_list.php') ? 'active fw-bold' : ''; ?>" 
             href="student_list.php" style="color: rgb(37, 52, 117) !important;">
            <i class="bi bi-people me-2"></i>STUDENT LIST
          </a>
        </li>
        
        <!-- Record Payment -->
        <li class="nav-item border-end">
          <a class="nav-link px-4 <?php echo ($currentPage === 'record_payment.php') ? 'active fw-bold' : ''; ?>" 
             href="record_payment.php" style="color: rgb(37, 52, 117) !important;">
            <i class="bi bi-cash-coin me-2"></i>RECORD PAYMENT
          </a>
        </li>
        
        <!-- Payment Verification -->
        <li class="nav-item border-end">
          <a class="nav-link px-4 <?php echo ($currentPage === 'payment_verification.php') ? 'active fw-bold' : ''; ?>" 
             href="payment_verification.php" style="color: rgb(37, 52, 117) !important;">
            <i class="bi bi-shield-check me-2"></i>VERIFY PAYMENTS
          </a>
        </li>
        
        <!-- Installment Tracking -->
        <li class="nav-item border-end">
          <a class="nav-link px-4 <?php echo ($currentPage === 'installment_tracking.php') ? 'active fw-bold' : ''; ?>" 
             href="installment_tracking.php" style="color: rgb(37, 52, 117) !important;">
            <i class="bi bi-calendar-check me-2"></i>INSTALLMENTS
          </a>
        </li>
        
        <!-- Official Receipt -->
        <li class="nav-item">
          <a class="nav-link px-4 <?php echo ($currentPage === 'official_receipt.php') ? 'active fw-bold' : ''; ?>" 
             href="official_receipt.php" style="color: rgb(37, 52, 117) !important;">
            <i class="bi bi-receipt me-2"></i>RECEIPT
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<?php endif; ?>

<?php if ($isStudentAssistantPage): ?>
<!-- Student Assistant Sub Navbar: Student Assistant Navigation Links -->
<nav class="navbar navbar-expand-lg navbar-light" style="background: #fff;">
  <div class="container py-0" style="font-size: 0.97rem;">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#studentAssistantNavbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="studentAssistantNavbarNav">
      <ul class="navbar-nav d-flex justify-content-start w-100">
        <!-- Dashboard - Always visible -->
        <li class="nav-item border-end">
          <a class="nav-link px-4 <?php echo ($currentPage === 'index.php') ? 'active fw-bold' : ''; ?>" 
             href="index.php" style="color: rgb(37, 52, 117) !important;">
            <i class="bi bi-house-door me-2"></i>DASHBOARD
          </a>
        </li>
        
        <!-- Queue Management -->
        <li class="nav-item border-end">
          <a class="nav-link px-4 <?php echo ($currentPage === 'queue_management.php') ? 'active fw-bold' : ''; ?>" 
             href="queue_management.php" style="color: rgb(37, 52, 117) !important;">
            <i class="bi bi-people me-2"></i>QUEUE MANAGEMENT
          </a>
        </li>
        
        <!-- Scanner -->
        <li class="nav-item">
          <a class="nav-link px-4 <?php echo ($currentPage === 'scanner.php') ? 'active fw-bold' : ''; ?>" 
             href="scanner.php" style="color: rgb(37, 52, 117) !important;">
            <i class="bi bi-qr-code-scan me-2"></i>QR SCANNER
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<?php endif; ?>

<?php if (!$hideNav): ?>
<!-- Sub Navbar: Navigation Links -->
<nav class="navbar navbar-expand-lg navbar-light" style="background: #fff;">
  <div class="container py-0" style="font-size: 0.97rem;">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#subNavbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="subNavbarNav">
      <ul class="navbar-nav d-flex justify-content-between w-100">
        <li class="nav-item border-end flex-fill text-center w-100"><a class="nav-link px-3" href="/enrollmentsystem/index.php" style="color: rgb(37, 52, 117) !important;">HOME</a></li>
        <li class="nav-item border-end flex-fill text-center w-100"><a class="nav-link px-3" href="#" style="color: rgb(37, 52, 117) !important;">ABOUT</a></li>
        <li class="nav-item border-end flex-fill text-center w-100"><a class="nav-link px-3" href="/enrollmentsystem/frontend/pages/admissions.php" style="color: rgb(37, 52, 117) !important;">ADMISSIONS</a></li>
        <li class="nav-item border-end flex-fill text-center w-100"><a class="nav-link px-3" href="#" style="color: rgb(37, 52, 117) !important;">ACADEMICS</a></li>
        <li class="nav-item border-end flex-fill text-center w-100"><a class="nav-link px-3" href="#" style="color: rgb(37, 52, 117) !important;">RESEARCH</a></li>
        <li class="nav-item flex-fill text-center w-100"><a class="nav-link px-3" href="#" style="color: rgb(37, 52, 117) !important;">CAMPUS LIFE</a></li>
      </ul>
    </div>
  </div>
</nav>
<?php endif; ?>

<script>
function logoutEvaluator() {
  Swal.fire({
    title: 'Are you sure?',
    text: 'You will be logged out of your evaluator account.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, logout'
  }).then((result) => {
    if (result.isConfirmed) {
      // Show loading state
      Swal.fire({
        title: 'Logging out...',
        text: 'Please wait',
        icon: 'info',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });
      
      fetch('/enrollmentsystem/action/logout.php?format=json', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        credentials: 'same-origin'
      })
      .then(response => {
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
      })
      .then(data => {
        if (data.success) {
          Swal.fire({
            title: 'Logged Out',
            text: `${data.user_name}, you have been successfully logged out.`,
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
          }).then(() => {
            window.location.href = data.redirect_url;
          });
        } else {
          Swal.fire('Logout Error', data.message || 'Failed to logout. Redirecting to login page...', 'error').then(() => {
            window.location.href = data.redirect_url || 'login.php';
          });
        }
      })
      .catch(error => {
        console.error('Logout error:', error);
        Swal.fire({
          title: 'Logout Error', 
          text: 'Failed to logout. Redirecting to login page...', 
          icon: 'error',
          timer: 2000,
          showConfirmButton: false
        }).then(() => {
          window.location.href = 'login.php';
        });
      });
    }
  });
}

function logoutStudent() {
  Swal.fire({
    title: 'Are you sure?',
    text: 'You will be logged out of your student account.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, logout'
  }).then((result) => {
    if (result.isConfirmed) {
      // Show loading state
      Swal.fire({
        title: 'Logging out...',
        text: 'Please wait',
        icon: 'info',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });
      
      fetch('/enrollmentsystem/action/logout.php?format=json', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        credentials: 'same-origin'
      })
      .then(response => {
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
      })
      .then(data => {
        if (data.success) {
          Swal.fire({
            title: 'Logged Out',
            text: `${data.user_name}, you have been successfully logged out.`,
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
          }).then(() => {
            window.location.href = data.redirect_url;
          });
        } else {
          Swal.fire('Logout Error', data.message || 'Failed to logout. Redirecting to login page...', 'error').then(() => {
            window.location.href = data.redirect_url || '../pages/login.php';
          });
        }
      })
      .catch(error => {
        console.error('Logout error:', error);
        Swal.fire({
          title: 'Logout Error', 
          text: 'Failed to logout. Redirecting to login page...', 
          icon: 'error',
          timer: 2000,
          showConfirmButton: false
        }).then(() => {
          window.location.href = '../pages/login.php';
        });
      });
    }
  });
}

function logoutRegistrar() {
  Swal.fire({
    title: 'Are you sure?',
    text: 'You will be logged out of your registrar account.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, logout'
  }).then((result) => {
    if (result.isConfirmed) {
      // Show loading state
      Swal.fire({
        title: 'Logging out...',
        text: 'Please wait',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });
      
      fetch('/enrollmentsystem/action/logout.php?format=json', {
        method: 'POST',
        credentials: 'same-origin'
      })
      .then(response => {
        return response.json();
      })
      .then(data => {
        if (data.success) {
          window.location.href = '/enrollmentsystem/frontend/staff/index.php';
        } else {
          Swal.fire('Error', 'Logout failed. Please try again.', 'error');
        }
      })
      .catch(error => {
        console.error('Logout error:', error);
        Swal.fire('Error', 'Logout failed. Please try again.', 'error');
      });
    }
  });
}

function logoutStaff() {
  Swal.fire({
    title: 'Are you sure?',
    text: 'You will be logged out of your staff account.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, logout'
  }).then((result) => {
    if (result.isConfirmed) {
      // Show loading state
      Swal.fire({
        title: 'Logging out...',
        text: 'Please wait',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });
      
      fetch('/enrollmentsystem/action/staff/logout.php?format=json', {
        method: 'POST',
        credentials: 'same-origin'
      })
      .then(response => {
        return response.json();
      })
      .then(data => {
        if (data.success) {
          window.location.href = '/enrollmentsystem/frontend/staff/index.php';
        } else {
          Swal.fire('Error', 'Logout failed. Please try again.', 'error');
        }
      })
      .catch(error => {
        console.error('Logout error:', error);
        Swal.fire('Error', 'Logout failed. Please try again.', 'error');
      });
    }
  });
}
</script>