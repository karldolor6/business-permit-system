<?php
/**
 * General Configuration Settings
 */

// Site configuration
define('SITE_NAME', 'Business Permit System');
define('SITE_URL', 'http://localhost/business-permit-system');
define('SITE_DESCRIPTION', 'Online Business Permit Processing System for Local Government Units');

// File upload configuration
define('MAX_FILE_SIZE', 5242880); // 5MB in bytes
define('ALLOWED_FILE_TYPES', ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx']);
define('UPLOAD_PATH', dirname(__DIR__) . '/uploads/');

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Timezone
date_default_timezone_set('Asia/Manila');

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
