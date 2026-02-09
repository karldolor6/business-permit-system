<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!isAdminLoggedIn()) {
    redirect('login.php');
}

$error = '';
$success = '';
$application = null;

// Get application ID
if (!isset($_GET['id'])) {
    redirect('applications.php');
}

$application_id = intval($_GET['id']);

// Get application details
$stmt = $conn->prepare("SELECT a.*, u.full_name as applicant_name 
                        FROM applications a 
                        LEFT JOIN users u ON a.user_id = u.id 
                        WHERE a.id = ?");
$stmt->bind_param("i", $application_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $application = $result->fetch_assoc();
} else {
    $error = 'Application not found.';
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $application) {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid request. Please try again.';
    } else {
        $action = sanitize($_POST['action']);
        $remarks = sanitize($_POST['remarks']);
        $admin_id = $_SESSION['admin_id'];
        
        if ($action == 'approve') {
            // Generate permit number
            $permit_number = generatePermitNumber();
            
            // Update application
            $stmt = $conn->prepare("UPDATE applications SET status = 'approved', remarks = ?, date_processed = NOW(), permit_number = ? WHERE id = ?");
            $stmt->bind_param("ssi", $remarks, $permit_number, $application_id);
            
            if ($stmt->execute()) {
                // Log activity
                $log_action = 'Application Approved';
                $stmt = $conn->prepare("INSERT INTO application_logs (application_id, admin_id, action, remarks) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iiss", $application_id, $admin_id, $log_action, $remarks);
                $stmt->execute();
                
                $success = 'Application has been approved successfully! Permit Number: ' . $permit_number;
                header("refresh:2;url=applications.php");
            } else {
                $error = 'Failed to approve application. Please try again.';
            }
        } elseif ($action == 'reject') {
            // Update application
            $stmt = $conn->prepare("UPDATE applications SET status = 'rejected', remarks = ?, date_processed = NOW() WHERE id = ?");
            $stmt->bind_param("si", $remarks, $application_id);
            
            if ($stmt->execute()) {
                // Log activity
                $log_action = 'Application Rejected';
                $stmt = $conn->prepare("INSERT INTO application_logs (application_id, admin_id, action, remarks) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iiss", $application_id, $admin_id, $log_action, $remarks);
                $stmt->execute();
                
                $success = 'Application has been rejected.';
                header("refresh:2;url=applications.php");
            } else {
                $error = 'Failed to reject application. Please try again.';
            }
        } elseif ($action == 'request_revision') {
            // Update application
            $stmt = $conn->prepare("UPDATE applications SET status = 'for_revision', remarks = ? WHERE id = ?");
            $stmt->bind_param("si", $remarks, $application_id);
            
            if ($stmt->execute()) {
                // Log activity
                $log_action = 'Revision Requested';
                $stmt = $conn->prepare("INSERT INTO application_logs (application_id, admin_id, action, remarks) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iiss", $application_id, $admin_id, $log_action, $remarks);
                $stmt->execute();
                
                $success = 'Revision has been requested. The applicant will be notified.';
                header("refresh:2;url=applications.php");
            } else {
                $error = 'Failed to request revision. Please try again.';
            }
        }
    }
}

$page_title = 'Process Application';
$admin_page = true;
include '../includes/header.php';
?>

<div class="admin-wrapper">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="logo"><?php echo SITE_NAME; ?></div>
        <ul class="menu">
            <li><a href="index.php">📊 Dashboard</a></li>
            <li><a href="applications.php" class="active">📝 Applications</a></li>
            <li><a href="users.php">👥 Users</a></li>
            <li><a href="reports.php">📈 Reports</a></li>
            <li><a href="logout.php">🚪 Logout</a></li>
        </ul>
    </aside>
    
    <!-- Main Content -->
    <main class="admin-content">
        <div class="admin-header">
            <h1>Process Application</h1>
            <div class="user-info">
                <span class="user-name"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
            </div>
        </div>
        
        <?php if ($error): ?>
            <?php displayAlert('error', $error); ?>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <?php displayAlert('success', $success); ?>
        <?php endif; ?>
        
        <?php if ($application && $application['status'] == 'pending'): ?>
        
        <div style="margin-bottom: 1.5rem;">
            <a href="view-application.php?id=<?php echo $application['id']; ?>" class="btn btn-secondary">← View Full Details</a>
        </div>
        
        <!-- Application Summary -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Application Summary</h3>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Reference Number:</strong><br><?php echo htmlspecialchars($application['reference_number']); ?></p>
                    <p><strong>Business Name:</strong><br><?php echo htmlspecialchars($application['business_name']); ?></p>
                    <p><strong>Business Type:</strong><br><?php echo htmlspecialchars($application['business_type']); ?></p>
                    <p><strong>Business Address:</strong><br><?php echo htmlspecialchars($application['business_address']); ?></p>
                </div>
                
                <div class="col-md-6">
                    <p><strong>Owner Name:</strong><br><?php echo htmlspecialchars($application['owner_name']); ?></p>
                    <p><strong>Owner Contact:</strong><br><?php echo htmlspecialchars($application['owner_contact']); ?></p>
                    <p><strong>Applicant:</strong><br><?php echo htmlspecialchars($application['applicant_name']); ?></p>
                    <p><strong>Date Applied:</strong><br><?php echo formatDate($application['date_applied']); ?></p>
                </div>
            </div>
        </div>
        
        <!-- Process Form -->
        <div class="card">
            <div class="card-header">
                <h3>Process Application</h3>
            </div>
            
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <div class="form-group">
                    <label>Action *</label>
                    <select class="form-control" name="action" id="action" required onchange="updateRemarksLabel()">
                        <option value="">Select Action</option>
                        <option value="approve">Approve Application</option>
                        <option value="reject">Reject Application</option>
                        <option value="request_revision">Request Revision</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="remarks" id="remarksLabel">Remarks</label>
                    <textarea class="form-control" name="remarks" id="remarks" rows="5" 
                              placeholder="Enter any remarks or notes about this decision..."></textarea>
                    <small class="form-text">Provide detailed feedback, especially for rejections or revision requests.</small>
                </div>
                
                <div style="display: flex; gap: 1rem;">
                    <button type="submit" class="btn btn-primary">Submit Decision</button>
                    <a href="applications.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
        
        <?php elseif ($application): ?>
        
        <div class="card">
            <p style="text-align: center; padding: 2rem;">
                This application has already been processed.<br>
                <strong>Status:</strong> 
                <span class="badge <?php echo getStatusBadge($application['status']); ?>">
                    <?php echo strtoupper(str_replace('_', ' ', $application['status'])); ?>
                </span>
            </p>
            <div style="text-align: center;">
                <a href="view-application.php?id=<?php echo $application['id']; ?>" class="btn btn-primary">View Details</a>
                <a href="applications.php" class="btn btn-secondary">Back to Applications</a>
            </div>
        </div>
        
        <?php endif; ?>
    </main>
</div>

<script>
function updateRemarksLabel() {
    const action = document.getElementById('action').value;
    const remarksLabel = document.getElementById('remarksLabel');
    
    if (action === 'approve') {
        remarksLabel.textContent = 'Approval Notes (Optional)';
    } else if (action === 'reject') {
        remarksLabel.textContent = 'Rejection Reason *';
    } else if (action === 'request_revision') {
        remarksLabel.textContent = 'Revision Requirements *';
    } else {
        remarksLabel.textContent = 'Remarks';
    }
}
</script>

<?php include '../includes/footer.php'; ?>
