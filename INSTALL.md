# Installation Guide

## Quick Start Guide for Business Permit System

Follow these steps to install and set up the Business Permit System on your local machine or server.

### Prerequisites

Before you begin, ensure you have:
- PHP 7.4 or higher installed
- MySQL 5.7 or higher installed
- Apache or Nginx web server
- phpMyAdmin (optional, for easier database management)

### Step-by-Step Installation

#### 1. Download/Clone the Project

```bash
git clone https://github.com/karldolor6/business-permit-system.git
cd business-permit-system
```

Or download the ZIP file and extract it to your web server directory (e.g., `htdocs` for XAMPP, `www` for WAMP).

#### 2. Database Setup

**Option A: Using phpMyAdmin**
1. Open phpMyAdmin in your browser (usually http://localhost/phpmyadmin)
2. Click "New" to create a new database
3. Name it `business_permit_db`
4. Select "utf8mb4_general_ci" as collation
5. Click "Create"
6. Select the newly created database
7. Click "Import" tab
8. Click "Choose File" and select `database/schema.sql` from the project
9. Click "Go" at the bottom

**Option B: Using MySQL Command Line**
```bash
mysql -u root -p
CREATE DATABASE business_permit_db;
exit;

mysql -u root -p business_permit_db < database/schema.sql
```

#### 3. Configure Database Connection

1. Open `config/database.php`
2. Update the following settings:
```php
define('DB_HOST', 'localhost');      // Usually localhost
define('DB_USER', 'root');           // Your MySQL username
define('DB_PASS', '');               // Your MySQL password
define('DB_NAME', 'business_permit_db');
```

#### 4. Configure Site URL

1. Open `config/config.php`
2. Update the SITE_URL:
```php
define('SITE_URL', 'http://localhost/business-permit-system');
```
Replace with your actual URL if different.

#### 5. Set Directory Permissions

Make sure the uploads directory is writable:

**On Linux/Mac:**
```bash
chmod -R 755 uploads/
```

**On Windows:**
Right-click the `uploads` folder → Properties → Security → Edit → Allow "Write" permission

#### 6. Test the Installation

1. Open your browser
2. Navigate to: http://localhost/business-permit-system/
3. You should see the landing page

#### 7. Login to Admin Panel

1. Go to: http://localhost/business-permit-system/admin/login.php
2. Use default credentials:
   - Username: `admin`
   - Password: `admin123`
3. **Important:** Change this password immediately after first login!

#### 8. Test User Registration

1. Go back to the homepage
2. Click "Register Now"
3. Create a test account
4. Login and test the application process

### Verification

To verify everything is working:
1. Can you access the homepage?
2. Can you register a new user?
3. Can you login as admin?
4. Can you submit a test application?

### Common Issues and Solutions

#### Issue: "Connection failed" error
**Solution:** 
- Check MySQL is running
- Verify database credentials in `config/database.php`
- Ensure `business_permit_db` database exists

#### Issue: "Cannot upload files"
**Solution:**
- Check `uploads/` directory permissions (755)
- Increase `upload_max_filesize` in php.ini if needed
- Ensure `post_max_size` is larger than `upload_max_filesize`

#### Issue: Blank page or errors
**Solution:**
- Check PHP error log
- Enable error reporting in `config/config.php`
- Ensure all required PHP extensions are enabled (mysqli, session)

#### Issue: CSS/JS not loading
**Solution:**
- Verify SITE_URL in `config/config.php` matches your actual URL
- Check browser console for 404 errors
- Clear browser cache

#### Issue: Session errors
**Solution:**
- Check session.save_path in php.ini
- Ensure session directory exists and is writable
- Check PHP session configuration

### Server Requirements

**Minimum:**
- PHP 7.4+
- MySQL 5.7+
- 512MB RAM
- 100MB disk space

**Recommended:**
- PHP 8.0+
- MySQL 8.0+
- 1GB RAM
- 500MB disk space

### Production Deployment

For production deployment:

1. **Disable error display:**
   In `config/config.php`:
   ```php
   error_reporting(0);
   ini_set('display_errors', 0);
   ```

2. **Enable HTTPS:**
   Update `config/config.php`:
   ```php
   ini_set('session.cookie_secure', 1);
   ```

3. **Change default admin password**

4. **Regular backups:**
   - Database backup regularly
   - Backup uploaded files

5. **Update file permissions:**
   - Set proper permissions (644 for files, 755 for directories)
   - Keep uploads directory secured

6. **Monitor logs:**
   - Check application logs
   - Monitor PHP error logs

### Support

If you encounter any issues:
1. Check the README.md file
2. Review this installation guide
3. Check the GitHub issues page
4. Contact support

### Next Steps

After successful installation:
1. ✅ Change admin password
2. ✅ Test all features
3. ✅ Configure email settings (if needed)
4. ✅ Customize for your LGU
5. ✅ Add your LGU logo
6. ✅ Update terms and conditions

---

**Congratulations!** Your Business Permit System is now ready to use.
