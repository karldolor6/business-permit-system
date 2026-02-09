<?php
// Determine if user is on admin or public page
$is_admin = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false);
?>

<header>
    <nav class="navbar container">
        <div class="logo">
            <?php if (!$is_admin): ?>
            <a href="<?php echo SITE_URL; ?>/index.php" style="color: white;">
                <?php echo SITE_NAME; ?>
            </a>
            <?php else: ?>
                Admin Panel
            <?php endif; ?>
        </div>
        
        <?php if (!$is_admin): ?>
        <!-- Public Navigation -->
        <ul class="nav-links">
            <li><a href="<?php echo SITE_URL; ?>/index.php">Home</a></li>
            
            <?php if (isLoggedIn()): ?>
                <li><a href="<?php echo SITE_URL; ?>/applicant/dashboard.php">Dashboard</a></li>
                <li><a href="<?php echo SITE_URL; ?>/applicant/apply.php">Apply for Permit</a></li>
                <li><a href="<?php echo SITE_URL; ?>/applicant/my-applications.php">My Applications</a></li>
                <li><a href="<?php echo SITE_URL; ?>/applicant/track.php">Track Application</a></li>
                <li><a href="<?php echo SITE_URL; ?>/logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="<?php echo SITE_URL; ?>/login.php">Login</a></li>
                <li><a href="<?php echo SITE_URL; ?>/register.php">Register</a></li>
                <li><a href="<?php echo SITE_URL; ?>/admin/login.php">Admin Login</a></li>
            <?php endif; ?>
        </ul>
        <?php else: ?>
        <!-- Admin Navigation -->
        <div class="user-info">
            <span class="user-name">
                <?php echo isset($_SESSION['admin_name']) ? htmlspecialchars($_SESSION['admin_name']) : 'Admin'; ?>
            </span>
            <a href="<?php echo SITE_URL; ?>/admin/logout.php" class="btn btn-sm btn-secondary">Logout</a>
        </div>
        <?php endif; ?>
    </nav>
</header>
