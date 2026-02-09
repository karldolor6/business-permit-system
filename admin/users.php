<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!isAdminLoggedIn()) {
    redirect('login.php');
}

// Get all users
$users = $conn->query("SELECT u.*, 
                       (SELECT COUNT(*) FROM applications WHERE user_id = u.id) as total_applications,
                       (SELECT COUNT(*) FROM applications WHERE user_id = u.id AND status = 'approved') as approved_applications
                       FROM users u 
                       ORDER BY u.created_at DESC");

$page_title = 'User Management';
$admin_page = true;
include '../includes/header.php';
?>

<div class="admin-wrapper">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="logo"><?php echo SITE_NAME; ?></div>
        <ul class="menu">
            <li><a href="index.php">📊 Dashboard</a></li>
            <li><a href="applications.php">📝 Applications</a></li>
            <li><a href="users.php" class="active">👥 Users</a></li>
            <li><a href="reports.php">📈 Reports</a></li>
            <li><a href="logout.php">🚪 Logout</a></li>
        </ul>
    </aside>
    
    <!-- Main Content -->
    <main class="admin-content">
        <div class="admin-header">
            <h1>User Management</h1>
            <div class="user-info">
                <span class="user-name"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
            </div>
        </div>
        
        <!-- Statistics -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <h3><?php echo $users->num_rows; ?></h3>
                    <p>Total Registered Users</p>
                </div>
            </div>
        </div>
        
        <!-- Users Table -->
        <div class="card">
            <div class="card-header">
                <h3>All Users</h3>
            </div>
            
            <?php if ($users->num_rows > 0): ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>Registered</th>
                            <th>Applications</th>
                            <th>Approved</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $users->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                            <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['contact_number']); ?></td>
                            <td><?php echo formatDate($user['created_at']); ?></td>
                            <td><?php echo $user['total_applications']; ?></td>
                            <td><?php echo $user['approved_applications']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p style="text-align: center; padding: 2rem; color: #6c757d;">No users registered yet.</p>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
