<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('../login.php');
}

$user_id = $_SESSION['user_id'];

// Get all applications for this user
$stmt = $conn->prepare("SELECT * FROM applications WHERE user_id = ? ORDER BY date_applied DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$applications = $stmt->get_result();

$page_title = 'My Applications';
include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container" style="padding: 3rem 0;">
    <h1 class="mb-4">My Applications</h1>
    
    <div class="card">
        <?php if ($applications->num_rows > 0): ?>
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
                    <?php while ($app = $applications->fetch_assoc()): ?>
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
                                <button onclick="showDetails(<?php echo $app['id']; ?>)" class="btn btn-sm btn-primary">View Details</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr id="details-<?php echo $app['id']; ?>" style="display: none;">
                        <td colspan="6" style="background: #f8f9fa; padding: 1.5rem;">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Business Address:</strong><br><?php echo htmlspecialchars($app['business_address']); ?></p>
                                    <p><strong>Owner Name:</strong><br><?php echo htmlspecialchars($app['owner_name']); ?></p>
                                    <p><strong>Owner Contact:</strong><br><?php echo htmlspecialchars($app['owner_contact']); ?></p>
                                    <p><strong>Owner Address:</strong><br><?php echo htmlspecialchars($app['owner_address']); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <?php if ($app['date_processed']): ?>
                                    <p><strong>Date Processed:</strong><br><?php echo formatDateTime($app['date_processed']); ?></p>
                                    <?php endif; ?>
                                    
                                    <?php if ($app['remarks']): ?>
                                    <p><strong>Remarks:</strong><br><?php echo nl2br(htmlspecialchars($app['remarks'])); ?></p>
                                    <?php endif; ?>
                                    
                                    <?php if ($app['permit_number']): ?>
                                    <p><strong>Permit Number:</strong><br><?php echo htmlspecialchars($app['permit_number']); ?></p>
                                    <?php endif; ?>
                                    
                                    <!-- Get documents -->
                                    <?php
                                    $doc_stmt = $conn->prepare("SELECT * FROM documents WHERE application_id = ?");
                                    $doc_stmt->bind_param("i", $app['id']);
                                    $doc_stmt->execute();
                                    $documents = $doc_stmt->get_result();
                                    
                                    if ($documents->num_rows > 0):
                                    ?>
                                    <p><strong>Uploaded Documents:</strong></p>
                                    <ul class="document-list">
                                        <?php while ($doc = $documents->fetch_assoc()): ?>
                                        <li>
                                            📄 <?php echo htmlspecialchars($doc['document_type']); ?>
                                            <small>(<?php echo formatDate($doc['uploaded_at']); ?>)</small>
                                        </li>
                                        <?php endwhile; ?>
                                    </ul>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div style="text-align: center; padding: 3rem;">
            <p style="font-size: 1.2rem; color: #6c757d; margin-bottom: 1.5rem;">
                You haven't submitted any applications yet.
            </p>
            <a href="apply.php" class="btn btn-primary">Apply for Your First Permit</a>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function showDetails(id) {
    const detailsRow = document.getElementById('details-' + id);
    if (detailsRow.style.display === 'none') {
        detailsRow.style.display = 'table-row';
    } else {
        detailsRow.style.display = 'none';
    }
}
</script>

<?php include '../includes/footer.php'; ?>
