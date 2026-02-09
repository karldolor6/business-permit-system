<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!isAdminLoggedIn()) {
    redirect('login.php');
}

// Get statistics
$stmt = $conn->query("SELECT COUNT(*) as total FROM applications");
$total_applications = $stmt->fetch_assoc()['total'];

$stmt = $conn->query("SELECT COUNT(*) as pending FROM applications WHERE status = 'pending'");
$pending_applications = $stmt->fetch_assoc()['pending'];

$stmt = $conn->query("SELECT COUNT(*) as approved FROM applications WHERE status = 'approved'");
$approved_applications = $stmt->fetch_assoc()['approved'];

$stmt = $conn->query("SELECT COUNT(*) as rejected FROM applications WHERE status = 'rejected'");
$rejected_applications = $stmt->fetch_assoc()['rejected'];

$stmt = $conn->query("SELECT COUNT(*) as total_users FROM users");
$total_users = $stmt->fetch_assoc()['total_users'];

// Get recent applications
$recent_applications = $conn->query("SELECT a.*, u.full_name as applicant_name 
                                      FROM applications a 
                                      LEFT JOIN users u ON a.user_id = u.id 
                                      ORDER BY a.date_applied DESC 
                                      LIMIT 10");

$page_title = 'Admin Dashboard';
$admin_page = true;
include '../includes/header.php';
?>

<div class="admin-wrapper">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="logo"><?php echo SITE_NAME; ?></div>
        <ul class="menu">
            <li><a href="index.php" class="active">📊 Dashboard</a></li>
            <li><a href="applications.php">📝 Applications</a></li>
            <li><a href="users.php">👥 Users</a></li>
            <li><a href="reports.php">📈 Reports</a></li>
            <li><a href="logout.php">🚪 Logout</a></li>
        </ul>
    </aside>
    
    <!-- Main Content -->
    <main class="admin-content">
        <div class="admin-header">
            <h1>Dashboard</h1>
            <div class="user-info">
                <span class="user-name"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
            </div>
        </div>
        
        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-box primary">
                <div class="stat-info">
                    <h3><?php echo $total_applications; ?></h3>
                    <p>Total Applications</p>
                </div>
                <div class="stat-icon">📝</div>
            </div>
            
            <div class="stat-box warning">
                <div class="stat-info">
                    <h3><?php echo $pending_applications; ?></h3>
                    <p>Pending Applications</p>
                </div>
                <div class="stat-icon">⏱️</div>
            </div>
            
            <div class="stat-box success">
                <div class="stat-info">
                    <h3><?php echo $approved_applications; ?></h3>
                    <p>Approved Applications</p>
                </div>
                <div class="stat-icon">✅</div>
            </div>
            
            <div class="stat-box danger">
                <div class="stat-info">
                    <h3><?php echo $rejected_applications; ?></h3>
                    <p>Rejected Applications</p>
                </div>
                <div class="stat-icon">❌</div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Quick Actions</h3>
            </div>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <a href="applications.php?status=pending" class="btn btn-warning">Review Pending Applications</a>
                <a href="applications.php" class="btn btn-primary">View All Applications</a>
                <a href="users.php" class="btn btn-secondary">Manage Users (<?php echo $total_users; ?>)</a>
                <a href="reports.php" class="btn btn-secondary">Generate Reports</a>
            </div>
        </div>
        
        <!-- Recent Applications -->
        <div class="card">
            <div class="card-header">
                <h3>Recent Applications</h3>
            </div>
            
            <?php if ($recent_applications->num_rows > 0): ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Reference No.</th>
                            <th>Business Name</th>
                            <th>Applicant</th>
                            <th>Type</th>
                            <th>Date Applied</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($app = $recent_applications->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($app['reference_number']); ?></strong></td>
                            <td><?php echo htmlspecialchars($app['business_name']); ?></td>
                            <td><?php echo htmlspecialchars($app['applicant_name']); ?></td>
                            <td><?php echo htmlspecialchars($app['business_type']); ?></td>
                            <td><?php echo formatDate($app['date_applied']); ?></td>
                            <td>
                                <span class="badge <?php echo getStatusBadge($app['status']); ?>">
                                    <?php echo strtoupper($app['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="view-application.php?id=<?php echo $app['id']; ?>" class="btn btn-sm btn-primary">View</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p style="text-align: center; padding: 2rem; color: #6c757d;">No applications yet.</p>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
