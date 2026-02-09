<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('../login.php');
}

$user_id = $_SESSION['user_id'];

// Get statistics
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM applications WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_applications = $stmt->get_result()->fetch_assoc()['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as pending FROM applications WHERE user_id = ? AND status = 'pending'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$pending_applications = $stmt->get_result()->fetch_assoc()['pending'];

$stmt = $conn->prepare("SELECT COUNT(*) as approved FROM applications WHERE user_id = ? AND status = 'approved'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$approved_applications = $stmt->get_result()->fetch_assoc()['approved'];

$stmt = $conn->prepare("SELECT COUNT(*) as rejected FROM applications WHERE user_id = ? AND status = 'rejected'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$rejected_applications = $stmt->get_result()->fetch_assoc()['rejected'];

// Get recent applications
$stmt = $conn->prepare("SELECT * FROM applications WHERE user_id = ? ORDER BY date_applied DESC LIMIT 5");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$recent_applications = $stmt->get_result();

$page_title = 'Dashboard';
include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container" style="padding: 3rem 0;">
    <h1 class="mb-4">Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</h1>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <h3><?php echo $total_applications; ?></h3>
                <p>Total Applications</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);">
                <h3><?php echo $pending_applications; ?></h3>
                <p>Pending</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);">
                <h3><?php echo $approved_applications; ?></h3>
                <p>Approved</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);">
                <h3><?php echo $rejected_applications; ?></h3>
                <p>Rejected</p>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="card mb-4">
        <div class="card-header">
            <h3>Quick Actions</h3>
        </div>
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <a href="apply.php" class="btn btn-primary">Apply for New Permit</a>
            <a href="my-applications.php" class="btn btn-secondary">View All Applications</a>
            <a href="track.php" class="btn btn-secondary">Track Application</a>
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
                        <th>Reference Number</th>
                        <th>Business Name</th>
                        <th>Business Type</th>
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
                        <td><?php echo htmlspecialchars($app['business_type']); ?></td>
                        <td><?php echo formatDate($app['date_applied']); ?></td>
                        <td>
                            <span class="badge <?php echo getStatusBadge($app['status']); ?>">
                                <?php echo strtoupper($app['status']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($app['status'] == 'approved'): ?>
                                <a href="view-permit.php?id=<?php echo $app['id']; ?>" class="btn btn-sm btn-success">View Permit</a>
                            <?php else: ?>
                                <a href="my-applications.php?id=<?php echo $app['id']; ?>" class="btn btn-sm btn-primary">View Details</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p style="text-align: center; padding: 2rem; color: #6c757d;">
            No applications yet. <a href="apply.php">Apply for your first permit</a>
        </p>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
