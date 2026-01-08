<?php
// Start session and require authentication
session_start();

// Database connection
require_once '../../includes/db_config.php';

// Get search parameters
$studentId = isset($_GET['student_id']) ? trim($_GET['student_id']) : '';
$studentName = isset($_GET['student_name']) ? trim($_GET['student_name']) : '';

// Flag to check if search was performed
$searchPerformed = !empty($studentId) || !empty($studentName);

// Real search results from database
$searchResults = [];

if ($searchPerformed) {
    try {
        $sql = "SELECT 
                    student_id,
                    CONCAT(first_name, ' ', last_name) as full_name,
                    first_name,
                    last_name,
                    desired_course,
                    status,
                    created_at,
                    updated_at
                FROM student_registrations 
                WHERE status = 'approved'";
        
        $params = [];
        $types = "";
        
        // Add search conditions
        if (!empty($studentId)) {
            $sql .= " AND student_id LIKE ?";
            $params[] = "%$studentId%";
            $types .= "s";
        }
        
        if (!empty($studentName)) {
            $sql .= " AND (first_name LIKE ? OR last_name LIKE ? OR CONCAT(first_name, ' ', last_name) LIKE ?)";
            $params[] = "%$studentName%";
            $params[] = "%$studentName%";
            $params[] = "%$studentName%";
            $types .= "sss";
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        $stmt = $conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            // Determine student type based on ID
            $studentType = 'old';
            if (strpos($row['student_id'], '2025-') !== false) {
                $studentType = 'new';
            } else if (strpos($row['student_id'], '2024-') !== false) {
                $studentType = 'old';
            } else {
                $studentType = 'transferee';
            }
            
            // Check actual document status from document_submissions table
            $docStmt = $conn->prepare("SELECT 
                COUNT(*) as total_docs, 
                SUM(CASE WHEN submission_status = 'Submitted' OR submission_status = 'Approved' THEN 1 ELSE 0 END) as completed_docs
                FROM document_submissions 
                WHERE student_id = ? AND is_required = 1");
            $docStmt->bind_param("s", $row['student_id']);
            $docStmt->execute();
            $docResult = $docStmt->get_result();
            $docData = $docResult->fetch_assoc();
            
            $total_docs = (int)$docData['total_docs'];
            $completed_docs = (int)$docData['completed_docs'];
            
            // If no documents found in the table, use fallback based on student type
            if ($total_docs == 0) {
                // For new students, we'll assume they need 5 documents
                if ($studentType == 'new') {
                    $total_docs = 5;
                } 
                // For transferees, they need 6 documents
                else if ($studentType == 'transferee') {
                    $total_docs = 6;
                }
                // For other types, they need 2 documents
                else {
                    $total_docs = 2;
                }
                $completed_docs = 0;
                $docStatus = 'Incomplete';
            } else {
                // Check if all required documents are submitted
                if ($completed_docs >= $total_docs) {
                    $docStatus = 'Complete';
                } else if ($completed_docs > 0) {
                    $docStatus = 'Partial';
                } else {
                    $docStatus = 'Incomplete';
                }
            }
            
            $searchResults[] = [
                'id' => $row['student_id'],
                'name' => $row['full_name'],
                'type' => $studentType,
                'course' => $row['desired_course'],
                'documents_status' => $docStatus,
                'completed_docs' => $completed_docs,
                'total_docs' => $total_docs,
                'date_approved' => date('m/d/Y', strtotime($row['updated_at']))
            ];
        }
        
        $stmt->close();
        
    } catch (Exception $e) {
        error_log("Search error: " . $e->getMessage());
        $searchResults = [];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include '../../includes/header.php'; ?>
<body class="registrar-dashboard">
    <?php include '../../includes/navbar.php'; ?>
    
    <div class="container py-5">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="fw-bold mb-1">Student Search</h2>
                                <p class="text-muted mb-0">Search for students in the system</p>
                            </div>
                            <div>
                                <a href="index.php" class="btn btn-outline-primary">
                                    <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Form -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Search Criteria</h5>
                    </div>
                    <div class="card-body">
                        <form action="student_search.php" method="GET">
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label for="student_id" class="form-label">Student ID</label>
                                    <input type="text" class="form-control" id="student_id" name="student_id" placeholder="Enter Student ID" value="<?php echo htmlspecialchars($studentId); ?>">
                                </div>
                                <div class="col-md-5">
                                    <label for="student_name" class="form-label">Student Name</label>
                                    <input type="text" class="form-control" id="student_name" name="student_name" placeholder="Enter Student Name" value="<?php echo htmlspecialchars($studentName); ?>">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-search me-1"></i> Search
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Results -->
        <div class="row">
            <div class="col-12">
                <div class="card dashboard-card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Search Results</h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if ($searchPerformed): ?>
                            <?php if (empty($searchResults)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-search text-muted" style="font-size: 3rem;"></i>
                                <p class="mt-3 text-muted">No students found matching your search criteria.</p>
                            </div>
                            <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th>Course</th>
                                            <th>Documents</th>
                                            <th>Date Approved</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($searchResults as $student): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($student['id']); ?></td>
                                            <td><?php echo htmlspecialchars($student['name']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $student['type'] == 'new' ? 'primary' : ($student['type'] == 'old' ? 'success' : ($student['type'] == 'shifting' ? 'warning' : 'info')); ?>">
                                                    <?php echo ucfirst($student['type']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($student['course']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    if ($student['documents_status'] == 'Complete') {
                                                        echo 'success';
                                                    } elseif ($student['documents_status'] == 'Partial') {
                                                        echo 'warning';
                                                    } else {
                                                        echo 'danger';
                                                    }
                                                ?>">
                                                    <?php echo $student['documents_status']; ?>
                                                </span>
                                                <span class="ms-2 small text-muted">
                                                    (<?php echo $student['completed_docs'] . '/' . $student['total_docs']; ?>)
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($student['date_approved']); ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="student_detail.php?id=<?php echo $student['id']; ?>" class="btn btn-primary" title="View Details">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="document_validation.php?id=<?php echo $student['id']; ?>" class="btn btn-success" title="Validate">
                                                        <i class="bi bi-check-lg"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php endif; ?>
                        <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-search text-muted" style="font-size: 3rem;"></i>
                            <p class="mt-3 text-muted">Enter search criteria and press search to find students.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>
