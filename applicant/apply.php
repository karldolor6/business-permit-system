<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('../login.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid request. Please try again.';
    } else {
        // Get form data
        $user_id = $_SESSION['user_id'];
        $business_name = sanitize($_POST['business_name']);
        $business_type = sanitize($_POST['business_type']);
        $business_address = sanitize($_POST['business_address']);
        $owner_name = sanitize($_POST['owner_name']);
        $owner_contact = sanitize($_POST['owner_contact']);
        $owner_address = sanitize($_POST['owner_address']);
        
        // Validation
        if (empty($business_name) || empty($business_type) || empty($business_address) || 
            empty($owner_name) || empty($owner_contact) || empty($owner_address)) {
            $error = 'Please fill in all required fields.';
        } elseif (!validatePhone($owner_contact)) {
            $error = 'Please enter a valid phone number.';
        } else {
            // Generate reference number
            $reference_number = generateReferenceNumber();
            
            // Insert application
            $stmt = $conn->prepare("INSERT INTO applications (user_id, reference_number, business_name, business_type, business_address, owner_name, owner_contact, owner_address, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
            $stmt->bind_param("isssssss", $user_id, $reference_number, $business_name, $business_type, $business_address, $owner_name, $owner_contact, $owner_address);
            
            if ($stmt->execute()) {
                $application_id = $conn->insert_id;
                
                // Handle file uploads
                $documents = [
                    'dti_sec_registration' => 'DTI/SEC Registration',
                    'barangay_clearance' => 'Barangay Clearance',
                    'fire_safety_certificate' => 'Fire Safety Certificate',
                    'sanitary_permit' => 'Sanitary Permit',
                    'occupancy_permit' => 'Occupancy Permit',
                    'location_plan' => 'Location Plan'
                ];
                
                foreach ($documents as $key => $doc_type) {
                    if (isset($_FILES[$key]) && $_FILES[$key]['error'] == 0) {
                        $upload_result = uploadFile($_FILES[$key], $application_id);
                        
                        if ($upload_result['success']) {
                            // Insert document record
                            $stmt = $conn->prepare("INSERT INTO documents (application_id, document_type, file_name, file_path) VALUES (?, ?, ?, ?)");
                            $stmt->bind_param("isss", $application_id, $doc_type, $upload_result['file_name'], $upload_result['file_path']);
                            $stmt->execute();
                        }
                    }
                }
                
                $success = 'Application submitted successfully! Your reference number is: ' . $reference_number;
                // Redirect to dashboard after 3 seconds
                header("refresh:3;url=dashboard.php");
            } else {
                $error = 'Failed to submit application. Please try again.';
            }
            $stmt->close();
        }
    }
}

$page_title = 'Apply for Business Permit';
include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container" style="padding: 3rem 0;">
    <div class="card">
        <div class="card-header">
            <h2>Business Permit Application Form</h2>
            <p>Please fill in all required information accurately</p>
        </div>
        
        <?php if ($error): ?>
            <?php displayAlert('error', $error); ?>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <?php displayAlert('success', $success); ?>
        <?php endif; ?>
        
        <form method="POST" action="" enctype="multipart/form-data" id="applicationForm">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            
            <h3 class="mb-3">Business Information</h3>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="business_name">Business Name *</label>
                        <input type="text" class="form-control" id="business_name" name="business_name" 
                               value="<?php echo isset($_POST['business_name']) ? htmlspecialchars($_POST['business_name']) : ''; ?>" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="business_type">Business Type *</label>
                        <select class="form-control" id="business_type" name="business_type" required>
                            <option value="">Select Business Type</option>
                            <option value="Single Proprietorship">Single Proprietorship</option>
                            <option value="Partnership">Partnership</option>
                            <option value="Corporation">Corporation</option>
                            <option value="Cooperative">Cooperative</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="business_address">Business Address *</label>
                <textarea class="form-control" id="business_address" name="business_address" rows="3" required><?php echo isset($_POST['business_address']) ? htmlspecialchars($_POST['business_address']) : ''; ?></textarea>
            </div>
            
            <h3 class="mb-3 mt-4">Owner Information</h3>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="owner_name">Owner Name *</label>
                        <input type="text" class="form-control" id="owner_name" name="owner_name" 
                               value="<?php echo isset($_POST['owner_name']) ? htmlspecialchars($_POST['owner_name']) : ''; ?>" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="owner_contact">Owner Contact Number *</label>
                        <input type="text" class="form-control" id="owner_contact" name="owner_contact" 
                               value="<?php echo isset($_POST['owner_contact']) ? htmlspecialchars($_POST['owner_contact']) : ''; ?>" 
                               placeholder="09XXXXXXXXX" required>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="owner_address">Owner Address *</label>
                <textarea class="form-control" id="owner_address" name="owner_address" rows="3" required><?php echo isset($_POST['owner_address']) ? htmlspecialchars($_POST['owner_address']) : ''; ?></textarea>
            </div>
            
            <h3 class="mb-3 mt-4">Document Uploads</h3>
            <p>Accepted file types: PDF, JPG, PNG, DOC, DOCX (Maximum 5MB per file)</p>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="dti_sec_registration">DTI/SEC Registration Certificate</label>
                        <input type="file" class="form-control" id="dti_sec_registration" name="dti_sec_registration" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="barangay_clearance">Barangay Clearance</label>
                        <input type="file" class="form-control" id="barangay_clearance" name="barangay_clearance" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="fire_safety_certificate">Fire Safety Inspection Certificate</label>
                        <input type="file" class="form-control" id="fire_safety_certificate" name="fire_safety_certificate" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="sanitary_permit">Sanitary Permit (for food establishments)</label>
                        <input type="file" class="form-control" id="sanitary_permit" name="sanitary_permit" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="occupancy_permit">Occupancy Permit</label>
                        <input type="file" class="form-control" id="occupancy_permit" name="occupancy_permit" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="location_plan">Location Plan/Vicinity Map</label>
                        <input type="file" class="form-control" id="location_plan" name="location_plan" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                    </div>
                </div>
            </div>
            
            <div class="mt-4" style="display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-primary">Submit Application</button>
                <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
