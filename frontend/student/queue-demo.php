<!DOCTYPE html>
<html lang="en">
<?php include '../includes/header.php'; ?>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2 class="fw-bold text-primary mb-0">Queue System Demo</h2>
                        </div>
                        <p class="text-muted">All queue states visible for design preview</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- State 1: Get Queue Number Buttons -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">State 1: Get Queue Number</h5>
                    </div>
                    <div class="card-body">
                        <div id="getQueueSection" class="text-center">
                            <p class="text-muted mb-3">Get your queue number for faster service</p>
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <button class="btn btn-primary w-100" onclick="alert('Registrar Queue Demo')">
                                        <i class="bi bi-file-earmark-text me-2"></i>Registrar
                                    </button>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <button class="btn btn-primary w-100" onclick="alert('Treasury Queue Demo')">
                                        <i class="bi bi-cash-coin me-2"></i>Treasury
                                    </button>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <button class="btn btn-primary w-100" onclick="alert('Enrollment Queue Demo')">
                                        <i class="bi bi-people me-2"></i>Enrollment
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- State 2: Active Queue Ticket -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">State 2: Active Queue Ticket</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h3 class="text-primary mb-3">Your Queue Number</h3>
                                        <div class="display-3 fw-bold text-success mb-3">RE-027</div>
                                        <p class="mb-2"><strong>Department:</strong> <span>Registrar</span></p>
                                        <p class="mb-0"><strong>Status:</strong> <span class="badge bg-warning">Waiting</span></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h5 class="mb-3">QR Code</h5>
                                        <div class="mb-3">
                                            <!-- Static QR Code Placeholder -->
                                            <div style="width: 150px; height: 150px; background: #f8f9fa; border: 2px dashed #dee2e6; 
                                                 display: flex; align-items: center; justify-content: center; margin: 0 auto; border-radius: 8px;">
                                                <div class="text-center">
                                                    <i class="bi bi-qr-code display-4 text-muted"></i>
                                                    <br><small class="text-muted">QR Code</small>
                                                </div>
                                            </div>
                                        </div>
                                        <small class="text-muted">Show this QR code at the counter</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="card border-warning">
                                    <div class="card-body text-center">
                                        <h5 class="text-warning mb-3">
                                            <i class="bi bi-clock me-2"></i>Time Remaining
                                        </h5>
                                        <div class="display-4 fw-bold text-success">01:45</div>
                                        <small class="text-muted">Please be ready when your number is called</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex align-items-center">
                                <div class="w-100 text-center">
                                    <button class="btn btn-danger btn-lg mb-2" onclick="alert('Cancel Queue Demo')">
                                        <i class="bi bi-x-circle me-2"></i>Cancel Queue
                                    </button>
                                    <br>
                                    <small class="text-muted">You can cancel your queue anytime</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- State 3: Low Time Warning (30 seconds or less) -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">State 3: Low Time Warning (≤30 seconds)</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h3 class="text-primary mb-3">Your Queue Number</h3>
                                        <div class="display-3 fw-bold text-success mb-3">TR-042</div>
                                        <p class="mb-2"><strong>Department:</strong> <span>Treasury</span></p>
                                        <p class="mb-0"><strong>Status:</strong> <span class="badge bg-danger">Almost Expired</span></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h5 class="mb-3">QR Code</h5>
                                        <div class="mb-3">
                                            <!-- Static QR Code Placeholder -->
                                            <div style="width: 150px; height: 150px; background: #f8f9fa; border: 2px dashed #dee2e6; 
                                                 display: flex; align-items: center; justify-content: center; margin: 0 auto; border-radius: 8px;">
                                                <div class="text-center">
                                                    <i class="bi bi-qr-code display-4 text-muted"></i>
                                                    <br><small class="text-muted">QR Code</small>
                                                </div>
                                            </div>
                                        </div>
                                        <small class="text-muted">Show this QR code at the counter</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="card border-danger">
                                    <div class="card-body text-center">
                                        <h5 class="text-danger mb-3">
                                            <i class="bi bi-clock me-2"></i>Time Remaining
                                        </h5>
                                        <div class="display-4 fw-bold text-danger">00:25</div>
                                        <small class="text-danger fw-bold">⚠️ Queue expires soon! Be ready!</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex align-items-center">
                                <div class="w-100 text-center">
                                    <button class="btn btn-danger btn-lg mb-2" onclick="alert('Cancel Queue Demo')">
                                        <i class="bi bi-x-circle me-2"></i>Cancel Queue
                                    </button>
                                    <br>
                                    <small class="text-muted">You can cancel your queue anytime</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- State 4: No Active Queue Message -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">State 4: No Active Queue Message</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info text-center">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>No active queue ticket.</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- State 5: Queue Cancelled Message -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">State 5: Queue Cancelled Message</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info text-center">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Queue ticket cancelled.</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Color Variations for Different Time States -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Timer Color Variations</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4 mb-3">
                                <div class="card border-success">
                                    <div class="card-body">
                                        <h6 class="text-success">More than 1 minute</h6>
                                        <div class="display-4 fw-bold text-success">01:30</div>
                                        <small class="text-muted">Plenty of time</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card border-warning">
                                    <div class="card-body">
                                        <h6 class="text-warning">30-60 seconds</h6>
                                        <div class="display-4 fw-bold text-warning">00:45</div>
                                        <small class="text-muted">Get ready</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card border-danger">
                                    <div class="card-body">
                                        <h6 class="text-danger">Less than 30 seconds</h6>
                                        <div class="display-4 fw-bold text-danger">00:15</div>
                                        <small class="text-danger fw-bold">Almost expired!</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Interactive Demo Note -->
        <div class="row">
            <div class="col-12">
                <div class="alert alert-primary">
                    <h5><i class="bi bi-info-circle me-2"></i>Demo Information</h5>
                    <p class="mb-2"><strong>This is a design preview showing all queue states.</strong></p>
                    <ul class="mb-0">
                        <li>State 1: Initial state with department selection buttons</li>
                        <li>State 2: Active ticket with normal time (green timer)</li>
                        <li>State 3: Active ticket with low time warning (red timer)</li>
                        <li>State 4: No active queue message</li>
                        <li>State 5: Queue cancelled message</li>
                        <li>Timer colors change based on remaining time</li>
                    </ul>
                    <hr>
                    <p class="mb-0"><strong>To test the full interactive functionality, go back to the main dashboard.</strong></p>
                </div>
            </div>
        </div>
    </div>


    <?php include '../includes/footer.php'; ?>
        <!-- QRCode.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
</body>
</html>
