<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('../login.php');
}

$error = '';
$permit = null;

// Get application ID
if (!isset($_GET['id'])) {
    redirect('my-applications.php');
}

$application_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Get application details (ensure it belongs to logged-in user and is approved)
$stmt = $conn->prepare("SELECT * FROM applications WHERE id = ? AND user_id = ? AND status = 'approved'");
$stmt->bind_param("ii", $application_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $permit = $result->fetch_assoc();
} else {
    $error = 'Permit not found or not yet approved.';
}

$page_title = 'View Business Permit';
include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container" style="padding: 3rem 0;">
    <?php if ($error): ?>
        <?php displayAlert('error', $error); ?>
        <a href="my-applications.php" class="btn btn-primary">Back to Applications</a>
    <?php elseif ($permit): ?>
    
    <div style="text-align: right; margin-bottom: 1rem;">
        <button onclick="window.print()" class="btn btn-primary btn-print">Print Permit</button>
        <a href="my-applications.php" class="btn btn-secondary">Back to Applications</a>
    </div>
    
    <div class="card" id="permit-content" style="max-width: 800px; margin: 0 auto; border: 3px double #2c3e50;">
        <div style="text-align: center; padding: 2rem; border-bottom: 2px solid #2c3e50;">
            <h1 style="color: #2c3e50; margin-bottom: 0.5rem;">BUSINESS PERMIT</h1>
            <h2 style="color: #0066cc; font-size: 1.5rem;">Local Government Unit</h2>
            <p style="font-size: 1.1rem; color: #6c757d;">Business Permit Processing System</p>
        </div>
        
        <div style="padding: 2rem;">
            <div style="text-align: center; background: #f8f9fa; padding: 1rem; margin-bottom: 2rem; border-radius: 8px;">
                <p style="margin: 0; font-size: 0.9rem; color: #6c757d;">Permit Number</p>
                <h2 style="margin: 0.5rem 0; color: #2c3e50; font-size: 2rem;"><?php echo htmlspecialchars($permit['permit_number']); ?></h2>
            </div>
            
            <div style="margin-bottom: 2rem;">
                <h3 style="color: #2c3e50; border-bottom: 2px solid #e9ecef; padding-bottom: 0.5rem; margin-bottom: 1rem;">Business Information</h3>
                
                <table style="width: 100%; border: none;">
                    <tr style="border: none;">
                        <td style="padding: 0.5rem 0; width: 200px; font-weight: 600; border: none;">Business Name:</td>
                        <td style="padding: 0.5rem 0; border: none;"><?php echo htmlspecialchars($permit['business_name']); ?></td>
                    </tr>
                    <tr style="border: none;">
                        <td style="padding: 0.5rem 0; font-weight: 600; border: none;">Business Type:</td>
                        <td style="padding: 0.5rem 0; border: none;"><?php echo htmlspecialchars($permit['business_type']); ?></td>
                    </tr>
                    <tr style="border: none;">
                        <td style="padding: 0.5rem 0; font-weight: 600; border: none;">Business Address:</td>
                        <td style="padding: 0.5rem 0; border: none;"><?php echo htmlspecialchars($permit['business_address']); ?></td>
                    </tr>
                </table>
            </div>
            
            <div style="margin-bottom: 2rem;">
                <h3 style="color: #2c3e50; border-bottom: 2px solid #e9ecef; padding-bottom: 0.5rem; margin-bottom: 1rem;">Owner Information</h3>
                
                <table style="width: 100%; border: none;">
                    <tr style="border: none;">
                        <td style="padding: 0.5rem 0; width: 200px; font-weight: 600; border: none;">Owner Name:</td>
                        <td style="padding: 0.5rem 0; border: none;"><?php echo htmlspecialchars($permit['owner_name']); ?></td>
                    </tr>
                    <tr style="border: none;">
                        <td style="padding: 0.5rem 0; font-weight: 600; border: none;">Contact Number:</td>
                        <td style="padding: 0.5rem 0; border: none;"><?php echo htmlspecialchars($permit['owner_contact']); ?></td>
                    </tr>
                    <tr style="border: none;">
                        <td style="padding: 0.5rem 0; font-weight: 600; border: none;">Owner Address:</td>
                        <td style="padding: 0.5rem 0; border: none;"><?php echo htmlspecialchars($permit['owner_address']); ?></td>
                    </tr>
                </table>
            </div>
            
            <div style="margin-bottom: 2rem;">
                <h3 style="color: #2c3e50; border-bottom: 2px solid #e9ecef; padding-bottom: 0.5rem; margin-bottom: 1rem;">Permit Details</h3>
                
                <table style="width: 100%; border: none;">
                    <tr style="border: none;">
                        <td style="padding: 0.5rem 0; width: 200px; font-weight: 600; border: none;">Reference Number:</td>
                        <td style="padding: 0.5rem 0; border: none;"><?php echo htmlspecialchars($permit['reference_number']); ?></td>
                    </tr>
                    <tr style="border: none;">
                        <td style="padding: 0.5rem 0; font-weight: 600; border: none;">Date Applied:</td>
                        <td style="padding: 0.5rem 0; border: none;"><?php echo formatDate($permit['date_applied']); ?></td>
                    </tr>
                    <tr style="border: none;">
                        <td style="padding: 0.5rem 0; font-weight: 600; border: none;">Date Approved:</td>
                        <td style="padding: 0.5rem 0; border: none;"><?php echo formatDate($permit['date_processed']); ?></td>
                    </tr>
                    <tr style="border: none;">
                        <td style="padding: 0.5rem 0; font-weight: 600; border: none;">Valid Until:</td>
                        <td style="padding: 0.5rem 0; border: none;"><?php echo formatDate(date('Y-m-d', strtotime($permit['date_processed'] . ' +1 year'))); ?></td>
                    </tr>
                </table>
            </div>
            
            <div style="margin-top: 3rem; padding-top: 2rem; border-top: 2px solid #e9ecef;">
                <p style="text-align: center; font-style: italic; color: #6c757d; font-size: 0.9rem;">
                    This permit is valid for one (1) year from the date of approval and must be renewed annually.<br>
                    This is a computer-generated document and does not require a signature.
                </p>
            </div>
        </div>
        
        <div style="background: #2c3e50; color: white; padding: 1rem; text-align: center;">
            <p style="margin: 0; font-size: 0.9rem;">
                For inquiries, please contact your Local Government Unit<br>
                Generated on: <?php echo formatDateTime(date('Y-m-d H:i:s')); ?>
            </p>
        </div>
    </div>
    
    <?php endif; ?>
</div>

<style>
@media print {
    header, footer, .btn, .navbar {
        display: none !important;
    }
    
    body {
        background: white;
    }
    
    #permit-content {
        box-shadow: none;
        border: 3px double #2c3e50 !important;
    }
}
</style>

<?php include '../includes/footer.php'; ?>
