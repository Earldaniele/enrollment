<?php
  $currentPage = basename($_SERVER['PHP_SELF']);
  $currentDir = basename(dirname($_SERVER['PHP_SELF']));
  $isStudentDirectory = ($currentDir === 'student');
  $isEvaluatorDirectory = ($currentDir === 'evaluator');
  $isRegistrarDirectory = ($currentDir === 'registrar');
  $isCashierDirectory = ($currentDir === 'cashier');
  $isStudentAssistantDirectory = ($currentDir === 'student-assistant');
  $isAuthPages = ($currentDir === 'pages' && ($currentPage === 'login.php' || $currentPage === 'register.php'));
  $showMinimalFooter = ($isStudentDirectory || $isEvaluatorDirectory || $isRegistrarDirectory || $isCashierDirectory || $isStudentAssistantDirectory || $isAuthPages);
?>

<?php if ($showMinimalFooter): ?>
<!-- Minimal Footer: Copyright Only -->
<footer class="footer-section py-2" style="background: rgb(37, 52, 117); color: #fff;">
  <div class="container">
    <div class="row">
      <div class="col text-center">
        <small>&copy; <?php echo date('Y'); ?> National College of Science and Technology. All rights reserved.</small>
      </div>
    </div>
  </div>
</footer>
<?php else: ?>
<!-- Regular Footer: Full Information -->
<footer class="footer-section pt-4 pb-1" style="background: rgb(37, 52, 117); color: #fff;">
  <div class="container">
    <div class="row">
      <div class="col-md-4 mb-3 mb-md-0">
        <h5 class="fw-bold mb-2">NATIONAL COLLEGE OF SCIENCE AND TECHNOLOGY</h5>
        <p class="mb-1">Governor's Drive, Dasmariñas, Cavite</p>
        <p class="mb-1">Phone: (046) 123-4567</p>
        <p class="mb-1">Email: info@ncst.edu.ph</p>
      </div>
      <div class="col-md-4 mb-3 mb-md-0">
        <h6 class="fw-bold mb-2">Quick Links</h6>
        <ul class="list-unstyled">
          <li><a href="/enrollmentsystem/index.php" class="footer-link">Home</a></li>
          <li><a href="#" class="footer-link">About</a></li>
          <li><a href="/enrollmentsystem/frontend/pages/admissions.php" class="footer-link">Admissions</a></li>
          <li><a href="#" class="footer-link">Academics</a></li>
          <li><a href="#" class="footer-link">Campus Life</a></li>
        </ul>
      </div>
      <div class="col-md-4">
        <h6 class="fw-bold mb-2">Connect With Us</h6>
        <p class="mb-1">Follow us on social media:</p>
        <div class="d-flex gap-3">
          <a href="#" class="footer-link"><i class="bi bi-facebook"></i> Facebook</a>
          <a href="#" class="footer-link"><i class="bi bi-twitter"></i> Twitter</a>
          <a href="#" class="footer-link"><i class="bi bi-instagram"></i> Instagram</a>
        </div>
      </div>
    </div>
    <hr class="my-2" style="border-color: #fff; opacity: 0.2;">
    <div class="row">
      <div class="col text-center">
        <small>&copy; <?php echo date('Y'); ?> National College of Science and Technology. All rights reserved.</small>
      </div>
    </div>
  </div>
</footer>
<?php endif; ?>

<!-- Include Notifications Modal for Student Pages -->
<?php if ($isStudentDirectory): ?>
  <?php include 'notifications-modal.php'; ?>
<?php endif; ?>

<script src="/enrollmentsystem/assets/js/bootstrap.min.js"></script>
<script src="/enrollmentsystem/assets/js/sweetalert2.min.js"></script>

<!-- Include Notifications JS for Student Pages -->
<?php if ($isStudentDirectory): ?>
  <script src="/enrollmentsystem/assets/js/notifications.js"></script>
<?php endif; ?>