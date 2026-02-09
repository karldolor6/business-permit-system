<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

$application = null;
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reference_number = sanitize($_POST['reference_number']);
    
    if (empty($reference_number)) {
        $error = 'Please enter a reference number.';
    } else {
        // Search for application
        $stmt = $conn->prepare("SELECT * FROM applications WHERE reference_number = ?");
        $stmt->bind_param("s", $reference_number);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $application = $result->fetch_assoc();
        } else {
            $error = 'No application found with this reference number.';
        }
        $stmt->close();
    }
}

$page_title = 'Track Application';
include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container" style="padding: 3rem 0;">
    <div class="card">
        <div class="card-header">
            <h2>Track Your Application</h2>
            <p>Enter your reference number to check the status of your application</p>
        </div>
        
        <?php if ($error): ?>
            <?php displayAlert('error', $error); ?>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="reference_number">Reference Number</label>
                <input type="text" class="form-control" id="reference_number" name="reference_number" 
                       value="<?php echo isset($_POST['reference_number']) ? htmlspecialchars($_POST['reference_number']) : ''; ?>" 
                       placeholder="BP-YYYY-XXXXXXXX" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Track Application</button>
        </form>
    </div>
    
    <?php if ($application): ?>
    <div class="card mt-4">
        <div class="card-header">
            <h3>Application Details</h3>
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
                <p><strong>Date Applied:</strong><br><?php echo formatDate($application['date_applied']); ?></p>
                <p><strong>Status:</strong><br>
                    <span class="badge <?php echo getStatusBadge($application['status']); ?>">
                        <?php echo strtoupper($application['status']); ?>
                    </span>
                </p>
            </div>
        </div>
        
        <?php if ($application['date_processed']): ?>
        <p><strong>Date Processed:</strong> <?php echo formatDateTime($application['date_processed']); ?></p>
        <?php endif; ?>
        
        <?php if ($application['remarks']): ?>
        <p><strong>Remarks:</strong><br><?php echo nl2br(htmlspecialchars($application['remarks'])); ?></p>
        <?php endif; ?>
        
        <?php if ($application['status'] == 'approved' && $application['permit_number']): ?>
        <div class="alert alert-success">
            <strong>Congratulations!</strong> Your business permit has been approved.<br>
            <strong>Permit Number:</strong> <?php echo htmlspecialchars($application['permit_number']); ?><br>
            <?php if (isLoggedIn() && $_SESSION['user_id'] == $application['user_id']): ?>
            <a href="view-permit.php?id=<?php echo $application['id']; ?>" class="btn btn-success mt-2">View/Download Permit</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
