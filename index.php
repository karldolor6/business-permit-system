<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/functions.php';

$page_title = 'Home';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<section class="hero">
    <div class="container">
        <h1>Welcome to <?php echo SITE_NAME; ?></h1>
        <p>Fast, Efficient, and Convenient Business Permit Processing for Local Government Units</p>
        <div style="margin-top: 2rem;">
            <?php if (isLoggedIn()): ?>
                <a href="applicant/dashboard.php" class="btn btn-primary" style="margin-right: 1rem;">Go to Dashboard</a>
                <a href="applicant/apply.php" class="btn btn-success">Apply for Permit</a>
            <?php else: ?>
                <a href="register.php" class="btn btn-primary" style="margin-right: 1rem;">Register Now</a>
                <a href="login.php" class="btn btn-success">Login</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="features">
    <div class="container">
        <h2 class="text-center mb-5">How It Works</h2>
        
        <div class="row">
            <div class="col-md-4">
                <div class="feature-box">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">📝</div>
                    <h3>1. Register & Apply</h3>
                    <p>Create an account and fill out the business permit application form online. Upload required documents.</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-box">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">⏱️</div>
                    <h3>2. Processing</h3>
                    <p>Our staff will review your application and documents. Track the status in real-time from your dashboard.</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-box">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">✅</div>
                    <h3>3. Get Your Permit</h3>
                    <p>Once approved, download your business permit directly from the system. It's that easy!</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section style="padding: 4rem 0; background: white;">
    <div class="container">
        <h2 class="text-center mb-4">About Business Permits</h2>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <h3>What is a Business Permit?</h3>
                    <p>A business permit, also known as Mayor's Permit, is a mandatory requirement for all businesses operating within a Local Government Unit (LGU). It ensures that your business complies with local regulations and standards.</p>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <h3>Required Documents</h3>
                    <ul style="padding-left: 1.5rem;">
                        <li>DTI/SEC/CDA Registration Certificate</li>
                        <li>Barangay Clearance</li>
                        <li>Fire Safety Inspection Certificate</li>
                        <li>Sanitary Permit (for food establishments)</li>
                        <li>Occupancy Permit</li>
                        <li>Location Plan/Vicinity Map</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="card mt-4">
            <h3>Business Types We Support</h3>
            <div class="row">
                <div class="col-md-3">
                    <ul style="padding-left: 1.5rem;">
                        <li>Single Proprietorship</li>
                        <li>Partnership</li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <ul style="padding-left: 1.5rem;">
                        <li>Corporation</li>
                        <li>Cooperative</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul style="padding-left: 1.5rem;">
                        <li>And other business types as recognized by the LGU</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section style="padding: 4rem 0; background: #f8f9fa;">
    <div class="container text-center">
        <h2 class="mb-4">Ready to Get Started?</h2>
        <p style="font-size: 1.2rem; margin-bottom: 2rem;">Join hundreds of business owners who have simplified their permit processing.</p>
        <?php if (!isLoggedIn()): ?>
            <a href="register.php" class="btn btn-primary" style="margin-right: 1rem;">Create Account</a>
            <a href="login.php" class="btn btn-secondary">Login</a>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
