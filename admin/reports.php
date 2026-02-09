<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!isAdminLoggedIn()) {
    redirect('login.php');
}

// Get report parameters
$report_type = isset($_GET['type']) ? sanitize($_GET['type']) : 'summary';
$start_date = isset($_GET['start_date']) ? sanitize($_GET['start_date']) : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? sanitize($_GET['end_date']) : date('Y-m-d');

// Get statistics for selected period
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM applications WHERE date_applied BETWEEN ? AND ?");
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as pending FROM applications WHERE status = 'pending' AND date_applied BETWEEN ? AND ?");
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$pending = $stmt->get_result()->fetch_assoc()['pending'];

$stmt = $conn->prepare("SELECT COUNT(*) as approved FROM applications WHERE status = 'approved' AND date_applied BETWEEN ? AND ?");
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$approved = $stmt->get_result()->fetch_assoc()['approved'];

$stmt = $conn->prepare("SELECT COUNT(*) as rejected FROM applications WHERE status = 'rejected' AND date_applied BETWEEN ? AND ?");
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$rejected = $stmt->get_result()->fetch_assoc()['rejected'];

// Get applications by business type
$business_types = $conn->query("SELECT business_type, COUNT(*) as count 
                                FROM applications 
                                WHERE date_applied BETWEEN '$start_date' AND '$end_date'
                                GROUP BY business_type 
                                ORDER BY count DESC");

// Get recent applications for the period
$applications = $conn->query("SELECT a.*, u.full_name as applicant_name 
                              FROM applications a 
                              LEFT JOIN users u ON a.user_id = u.id 
                              WHERE a.date_applied BETWEEN '$start_date' AND '$end_date'
                              ORDER BY a.date_applied DESC");

$page_title = 'Reports';
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
            <li><a href="users.php">👥 Users</a></li>
            <li><a href="reports.php" class="active">📈 Reports</a></li>
            <li><a href="logout.php">🚪 Logout</a></li>
        </ul>
    </aside>
    
    <!-- Main Content -->
    <main class="admin-content">
        <div class="admin-header">
            <h1>Reports & Analytics</h1>
            <div class="user-info">
                <span class="user-name"><?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
            </div>
        </div>
        
        <!-- Report Filters -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Report Parameters</h3>
            </div>
            
            <form method="GET" action="" style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <div class="form-group" style="flex: 1; min-width: 200px;">
                    <label for="start_date">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" 
                           value="<?php echo htmlspecialchars($start_date); ?>" required>
                </div>
                
                <div class="form-group" style="flex: 1; min-width: 200px;">
                    <label for="end_date">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" 
                           value="<?php echo htmlspecialchars($end_date); ?>" required>
                </div>
                
                <div style="display: flex; gap: 0.5rem; align-items: end;">
                    <button type="submit" class="btn btn-primary">Generate Report</button>
                    <button type="button" onclick="window.print()" class="btn btn-secondary">Print Report</button>
                </div>
            </form>
        </div>
        
        <!-- Statistics -->
        <div class="stats-grid mb-4">
            <div class="stat-box primary">
                <div class="stat-info">
                    <h3><?php echo $total; ?></h3>
                    <p>Total Applications</p>
                </div>
                <div class="stat-icon">📝</div>
            </div>
            
            <div class="stat-box warning">
                <div class="stat-info">
                    <h3><?php echo $pending; ?></h3>
                    <p>Pending</p>
                </div>
                <div class="stat-icon">⏱️</div>
            </div>
            
            <div class="stat-box success">
                <div class="stat-info">
                    <h3><?php echo $approved; ?></h3>
                    <p>Approved</p>
                </div>
                <div class="stat-icon">✅</div>
            </div>
            
            <div class="stat-box danger">
                <div class="stat-info">
                    <h3><?php echo $rejected; ?></h3>
                    <p>Rejected</p>
                </div>
                <div class="stat-icon">❌</div>
            </div>
        </div>
        
        <!-- Business Types Distribution -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Applications by Business Type</h3>
                    </div>
                    
                    <?php if ($business_types->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Business Type</th>
                                <th>Count</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($type = $business_types->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($type['business_type']); ?></td>
                                <td><?php echo $type['count']; ?></td>
                                <td><?php echo $total > 0 ? round(($type['count'] / $total) * 100, 1) : 0; ?>%</td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <p style="text-align: center; padding: 2rem; color: #6c757d;">No data for this period.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Summary Statistics</h3>
                    </div>
                    
                    <div style="padding: 1rem;">
                        <p><strong>Report Period:</strong> <?php echo formatDate($start_date); ?> to <?php echo formatDate($end_date); ?></p>
                        <p><strong>Total Applications:</strong> <?php echo $total; ?></p>
                        <p><strong>Approval Rate:</strong> <?php echo $total > 0 ? round(($approved / $total) * 100, 1) : 0; ?>%</p>
                        <p><strong>Rejection Rate:</strong> <?php echo $total > 0 ? round(($rejected / $total) * 100, 1) : 0; ?>%</p>
                        <p><strong>Pending Rate:</strong> <?php echo $total > 0 ? round(($pending / $total) * 100, 1) : 0; ?>%</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Applications List -->
        <div class="card">
            <div class="card-header">
                <h3>Applications for Selected Period</h3>
            </div>
            
            <?php if ($applications->num_rows > 0): ?>
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
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($app = $applications->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($app['reference_number']); ?></td>
                            <td><?php echo htmlspecialchars($app['business_name']); ?></td>
                            <td><?php echo htmlspecialchars($app['applicant_name']); ?></td>
                            <td><?php echo htmlspecialchars($app['business_type']); ?></td>
                            <td><?php echo formatDate($app['date_applied']); ?></td>
                            <td>
                                <span class="badge <?php echo getStatusBadge($app['status']); ?>">
                                    <?php echo strtoupper(str_replace('_', ' ', $app['status'])); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p style="text-align: center; padding: 2rem; color: #6c757d;">No applications for this period.</p>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
