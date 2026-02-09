<?php
/**
 * Helper Functions
 */

/**
 * Sanitize input data
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        return false;
    }
    return true;
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if admin is logged in
 */
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

/**
 * Redirect to a page
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Generate unique reference number
 */
function generateReferenceNumber() {
    return 'BP-' . date('Y') . '-' . strtoupper(substr(uniqid(), -8));
}

/**
 * Generate permit number
 */
function generatePermitNumber() {
    return 'PERMIT-' . date('Y') . '-' . strtoupper(substr(uniqid(), -8));
}

/**
 * Format date
 */
function formatDate($date) {
    return date('F d, Y', strtotime($date));
}

/**
 * Format datetime
 */
function formatDateTime($datetime) {
    return date('F d, Y h:i A', strtotime($datetime));
}

/**
 * Get status badge class
 */
function getStatusBadge($status) {
    $badges = [
        'pending' => 'badge-warning',
        'approved' => 'badge-success',
        'rejected' => 'badge-danger',
        'for_revision' => 'badge-info'
    ];
    return isset($badges[$status]) ? $badges[$status] : 'badge-secondary';
}

/**
 * Upload file
 */
function uploadFile($file, $application_id) {
    $target_dir = UPLOAD_PATH . $application_id . '/';
    
    // Create directory if not exists
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    
    $file_name = basename($file["name"]);
    $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $new_file_name = uniqid() . '_' . time() . '.' . $file_extension;
    $target_file = $target_dir . $new_file_name;
    
    // Check file size
    if ($file["size"] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'File is too large. Maximum size is 5MB.'];
    }
    
    // Check file type
    if (!in_array($file_extension, ALLOWED_FILE_TYPES)) {
        return ['success' => false, 'message' => 'Invalid file type. Allowed types: ' . implode(', ', ALLOWED_FILE_TYPES)];
    }
    
    // Upload file
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return [
            'success' => true, 
            'file_name' => $file_name,
            'file_path' => $application_id . '/' . $new_file_name
        ];
    } else {
        return ['success' => false, 'message' => 'Error uploading file.'];
    }
}

/**
 * Display alert message
 */
function displayAlert($type, $message) {
    $alertClass = [
        'success' => 'alert-success',
        'error' => 'alert-danger',
        'warning' => 'alert-warning',
        'info' => 'alert-info'
    ];
    
    $class = isset($alertClass[$type]) ? $alertClass[$type] : 'alert-info';
    
    echo '<div class="alert ' . $class . ' alert-dismissible fade show" role="alert">';
    echo htmlspecialchars($message);
    echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
    echo '<span aria-hidden="true">&times;</span>';
    echo '</button>';
    echo '</div>';
}

/**
 * Validate email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Validate phone number (Philippine format)
 */
function validatePhone($phone) {
    return preg_match('/^(09|\+639)\d{9}$/', $phone);
}
?>
