<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!isAdminLoggedIn()) {
    redirect('login.php');
}

// Get filter parameters
$status_filter = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

// Build query
$query = "SELECT a.*, u.full_name as applicant_name, u.email as applicant_email 
          FROM applications a 
          LEFT JOIN users u ON a.user_id = u.id 
          WHERE 1=1";

if ($status_filter) {
    $query .= " AND a.status = '" . $conn->real_escape_string($status_filter) . "'";
}

if ($search) {
    $query .= " AND (a.reference_number LIKE '%" . $conn->real_escape_string($search) . "%' 
                OR a.business_name LIKE '%" . $conn->real_escape_string($search) . "%' 
                OR u.full_name LIKE '%" . $conn->real_escape_string($search) . "%')";
}

$query .= " ORDER BY a.date_applied DESC";

$applications = $conn->query($query);

$page_title = 'Applications Management';
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
            <h1>Applications Management</h1>
            <div class="user-info">
                <span class="user-name"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="filters">
            <form method="GET" action="" style="display: flex; gap: 1rem; flex-wrap: wrap; width: 100%;">
                <div class="form-group" style="flex: 2; min-width: 250px;">
                    <label for="search">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="<?php echo htmlspecialchars($search); ?>" 
                           placeholder="Reference number, business name, applicant...">
                </div>
                
                <div class="form-group" style="flex: 1; min-width: 150px;">
                    <label for="status">Status</label>
                    <select class="form-control" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="approved" <?php echo $status_filter == 'approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="rejected" <?php echo $status_filter == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                        <option value="for_revision" <?php echo $status_filter == 'for_revision' ? 'selected' : ''; ?>>For Revision</option>
                    </select>
                </div>
                
                <div style="display: flex; gap: 0.5rem; align-items: end;">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="applications.php" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
        
        <!-- Applications Table -->
        <div class="card">
            <div class="card-header">
                <h3>All Applications (<?php echo $applications->num_rows; ?>)</h3>
            </div>
            
            <?php if ($applications->num_rows > 0): ?>
            <div class="table-responsive">
                <table id="applicationsTable">
                    <thead>
                        <tr>
                            <th>Reference No.</th>
                            <th>Business Name</th>
                            <th>Applicant</th>
                            <th>Business Type</th>
                            <th>Date Applied</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($app = $applications->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($app['reference_number']); ?></strong></td>
                            <td><?php echo htmlspecialchars($app['business_name']); ?></td>
                            <td>
                                <?php echo htmlspecialchars($app['applicant_name']); ?><br>
                                <small style="color: #6c757d;"><?php echo htmlspecialchars($app['applicant_email']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($app['business_type']); ?></td>
                            <td><?php echo formatDate($app['date_applied']); ?></td>
                            <td>
                                <span class="badge <?php echo getStatusBadge($app['status']); ?>">
                                    <?php echo strtoupper(str_replace('_', ' ', $app['status'])); ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="view-application.php?id=<?php echo $app['id']; ?>" class="btn btn-sm btn-primary">View</a>
                                    <?php if ($app['status'] == 'pending'): ?>
                                    <a href="process-application.php?id=<?php echo $app['id']; ?>" class="btn btn-sm btn-success">Process</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p style="text-align: center; padding: 2rem; color: #6c757d;">
                No applications found matching your criteria.
            </p>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
