<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!isAdminLoggedIn()) {
    redirect('login.php');
}

$error = '';
$application = null;

// Get application ID
if (!isset($_GET['id'])) {
    redirect('applications.php');
}

$application_id = intval($_GET['id']);

// Get application details
$stmt = $conn->prepare("SELECT a.*, u.full_name as applicant_name, u.email as applicant_email, 
                        u.contact_number as applicant_contact, u.address as applicant_address 
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

// Get documents
$documents = [];
if ($application) {
    $stmt = $conn->prepare("SELECT * FROM documents WHERE application_id = ?");
    $stmt->bind_param("i", $application_id);
    $stmt->execute();
    $documents = $stmt->get_result();
}

// Get activity logs
$logs = [];
if ($application) {
    $stmt = $conn->prepare("SELECT al.*, ad.full_name as admin_name 
                            FROM application_logs al 
                            LEFT JOIN admins ad ON al.admin_id = ad.id 
                            WHERE al.application_id = ? 
                            ORDER BY al.created_at DESC");
    $stmt->bind_param("i", $application_id);
    $stmt->execute();
    $logs = $stmt->get_result();
}

$page_title = 'View Application';
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
            <h1>Application Details</h1>
            <div class="user-info">
                <span class="user-name"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
            </div>
        </div>
        
        <?php if ($error): ?>
            <?php displayAlert('error', $error); ?>
            <a href="applications.php" class="btn btn-primary">Back to Applications</a>
        <?php elseif ($application): ?>
        
        <div style="margin-bottom: 1.5rem;">
            <a href="applications.php" class="btn btn-secondary">← Back to Applications</a>
            <?php if ($application['status'] == 'pending'): ?>
            <a href="process-application.php?id=<?php echo $application['id']; ?>" class="btn btn-success">Process Application</a>
            <?php endif; ?>
        </div>
        
        <!-- Application Status -->
        <div class="card mb-4">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3>Reference Number: <?php echo htmlspecialchars($application['reference_number']); ?></h3>
                    <p style="margin: 0.5rem 0;">
                        Status: 
                        <span class="badge <?php echo getStatusBadge($application['status']); ?>">
                            <?php echo strtoupper(str_replace('_', ' ', $application['status'])); ?>
                        </span>
                    </p>
                    <?php if ($application['permit_number']): ?>
                    <p style="margin: 0.5rem 0;">
                        <strong>Permit Number:</strong> <?php echo htmlspecialchars($application['permit_number']); ?>
                    </p>
                    <?php endif; ?>
                </div>
                <div>
                    <p><strong>Date Applied:</strong><br><?php echo formatDate($application['date_applied']); ?></p>
                    <?php if ($application['date_processed']): ?>
                    <p><strong>Date Processed:</strong><br><?php echo formatDateTime($application['date_processed']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <!-- Business Information -->
                <div class="card mb-4">
                    <div class="detail-section">
                        <h4>Business Information</h4>
                        
                        <div class="detail-row">
                            <div class="detail-label">Business Name:</div>
                            <div class="detail-value"><?php echo htmlspecialchars($application['business_name']); ?></div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Business Type:</div>
                            <div class="detail-value"><?php echo htmlspecialchars($application['business_type']); ?></div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Business Address:</div>
                            <div class="detail-value"><?php echo htmlspecialchars($application['business_address']); ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Owner Information -->
                <div class="card mb-4">
                    <div class="detail-section">
                        <h4>Owner Information</h4>
                        
                        <div class="detail-row">
                            <div class="detail-label">Owner Name:</div>
                            <div class="detail-value"><?php echo htmlspecialchars($application['owner_name']); ?></div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Contact Number:</div>
                            <div class="detail-value"><?php echo htmlspecialchars($application['owner_contact']); ?></div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Owner Address:</div>
                            <div class="detail-value"><?php echo htmlspecialchars($application['owner_address']); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <!-- Applicant Information -->
                <div class="card mb-4">
                    <div class="detail-section">
                        <h4>Applicant Information</h4>
                        
                        <div class="detail-row">
                            <div class="detail-label">Full Name:</div>
                            <div class="detail-value"><?php echo htmlspecialchars($application['applicant_name']); ?></div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Email:</div>
                            <div class="detail-value"><?php echo htmlspecialchars($application['applicant_email']); ?></div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Contact:</div>
                            <div class="detail-value"><?php echo htmlspecialchars($application['applicant_contact']); ?></div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Address:</div>
                            <div class="detail-value"><?php echo htmlspecialchars($application['applicant_address']); ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Documents -->
                <div class="card mb-4">
                    <div class="detail-section">
                        <h4>Uploaded Documents</h4>
                        
                        <?php if ($documents->num_rows > 0): ?>
                        <ul class="document-list">
                            <?php while ($doc = $documents->fetch_assoc()): ?>
                            <li>
                                <strong><?php echo htmlspecialchars($doc['document_type']); ?></strong><br>
                                <small style="color: #6c757d;">
                                    File: <?php echo htmlspecialchars($doc['file_name']); ?><br>
                                    Uploaded: <?php echo formatDateTime($doc['uploaded_at']); ?>
                                </small>
                            </li>
                            <?php endwhile; ?>
                        </ul>
                        <?php else: ?>
                        <p style="color: #6c757d;">No documents uploaded.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Remarks -->
        <?php if ($application['remarks']): ?>
        <div class="card mb-4">
            <div class="detail-section">
                <h4>Remarks</h4>
                <p><?php echo nl2br(htmlspecialchars($application['remarks'])); ?></p>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Activity Logs -->
        <div class="card">
            <div class="detail-section">
                <h4>Activity Log</h4>
                
                <?php if ($logs->num_rows > 0): ?>
                    <?php while ($log = $logs->fetch_assoc()): ?>
                    <div class="log-item">
                        <div class="log-action"><?php echo htmlspecialchars($log['action']); ?></div>
                        <div class="log-time">
                            By: <?php echo htmlspecialchars($log['admin_name'] ?: 'System'); ?> | 
                            <?php echo formatDateTime($log['created_at']); ?>
                        </div>
                        <?php if ($log['remarks']): ?>
                        <div style="margin-top: 0.5rem;"><?php echo nl2br(htmlspecialchars($log['remarks'])); ?></div>
                        <?php endif; ?>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                <p style="color: #6c757d;">No activity logs yet.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <?php endif; ?>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
