# Online Business Permit Processing System

A complete web-based business permit processing system designed for Local Government Units (LGUs) using PHP, MySQL, HTML, CSS, and JavaScript.

## 🌟 Features

### Public Portal (Applicant Side)
- **User Registration & Login**
  - Secure account creation with password encryption
  - Session-based authentication
  - Profile management

- **Business Permit Application**
  - Online application form with validation
  - Multiple document upload support
  - Real-time form validation (client and server-side)
  - Unique reference number generation

- **Application Tracking**
  - Track application status using reference number
  - View application history
  - Download approved permits

- **Applicant Dashboard**
  - View all submitted applications
  - Check real-time status updates
  - Access approved permits
  - Statistics overview

### Admin Portal
- **Secure Admin Authentication**
  - Role-based access control
  - Separate admin login system

- **Admin Dashboard**
  - Overview statistics (total, pending, approved, rejected)
  - Recent applications overview
  - Quick action buttons

- **Application Management**
  - View all applications with filters
  - Search by reference number, business name, or applicant
  - Review application details and documents
  - Approve/Reject applications
  - Request revisions with remarks
  - Generate permit numbers automatically

- **User Management**
  - View all registered users
  - User statistics and activity

- **Reports & Analytics**
  - Generate reports by date range
  - Application statistics
  - Business type distribution
  - Approval/rejection rates
  - Printable reports

## 🛠️ Technology Stack

- **Frontend:** HTML5, CSS3, JavaScript
- **Backend:** PHP 7.4+
- **Database:** MySQL 5.7+
- **Server:** Apache/Nginx with PHP support

## 📋 System Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Modern web browser (Chrome, Firefox, Safari, Edge)

## 🚀 Installation Instructions

### 1. Clone the Repository
```bash
git clone https://github.com/karldolor6/business-permit-system.git
cd business-permit-system
```

### 2. Database Setup

1. Create a MySQL database:
```sql
CREATE DATABASE business_permit_db;
```

2. Import the database schema:
```bash
mysql -u root -p business_permit_db < database/schema.sql
```

Alternatively, you can manually run the SQL file through phpMyAdmin or MySQL Workbench.

### 3. Configure Database Connection

Edit `config/database.php` and update the database credentials:
```php
define('DB_HOST', 'localhost');     // Database host
define('DB_USER', 'root');          // Database username
define('DB_PASS', '');              // Database password
define('DB_NAME', 'business_permit_db'); // Database name
```

### 4. Configure Site Settings

Edit `config/config.php` to update site URL:
```php
define('SITE_URL', 'http://localhost/business-permit-system');
```

### 5. Set Directory Permissions

Ensure the uploads directory is writable:
```bash
chmod 755 uploads/
```

### 6. Access the System

- **Public Portal:** http://localhost/business-permit-system/
- **Admin Portal:** http://localhost/business-permit-system/admin/

## 🔐 Default Credentials

### Admin Account
- **Username:** admin
- **Password:** admin123

**⚠️ Important:** Change the default admin password after first login!

### Test User Account
- **Username:** testuser
- **Email:** test@example.com
- **Password:** password123

## 📁 File Structure

```
business-permit-system/
├── config/
│   ├── database.php       # Database configuration
│   └── config.php         # General settings
├── includes/
│   ├── header.php         # Common header
│   ├── footer.php         # Common footer
│   ├── navbar.php         # Navigation bar
│   └── functions.php      # Helper functions
├── assets/
│   ├── css/
│   │   ├── style.css      # Main stylesheet
│   │   └── admin.css      # Admin styles
│   ├── js/
│   │   ├── main.js        # Main JavaScript
│   │   └── validation.js  # Form validation
│   └── images/
├── admin/
│   ├── login.php          # Admin login
│   ├── index.php          # Admin dashboard
│   ├── applications.php   # Manage applications
│   ├── view-application.php
│   ├── process-application.php
│   ├── users.php          # User management
│   ├── reports.php        # Reports & analytics
│   └── logout.php
├── applicant/
│   ├── dashboard.php      # Applicant dashboard
│   ├── apply.php          # Application form
│   ├── track.php          # Track application
│   ├── my-applications.php
│   └── view-permit.php    # View/print permit
├── database/
│   └── schema.sql         # Database schema
├── uploads/               # Document uploads
│   └── .htaccess          # Security
├── index.php              # Landing page
├── login.php              # User login
├── register.php           # User registration
├── logout.php
└── README.md
```

## 🔒 Security Features

- **Password Hashing:** Using PHP `password_hash()` with bcrypt
- **SQL Injection Prevention:** Prepared statements with parameterized queries
- **XSS Protection:** Input sanitization and output escaping
- **CSRF Protection:** Token-based form validation
- **Session Security:** Secure session configuration
- **File Upload Validation:** Type and size restrictions
- **Access Control:** Route protection for admin pages

## 📝 How to Use

### For Applicants

1. **Register an Account**
   - Visit the homepage
   - Click "Register Now"
   - Fill in your details
   - Submit the form

2. **Apply for a Business Permit**
   - Login to your account
   - Go to "Apply for Permit"
   - Fill in business and owner information
   - Upload required documents
   - Submit application

3. **Track Your Application**
   - Use "Track Application" with reference number
   - Or view from "My Applications" dashboard
   - Check status updates

4. **Download Approved Permit**
   - Once approved, view permit from dashboard
   - Print or save as PDF

### For Administrators

1. **Login to Admin Portal**
   - Access `/admin/login.php`
   - Use admin credentials

2. **Review Applications**
   - View pending applications
   - Click "View" to see details
   - Review documents and information

3. **Process Applications**
   - Click "Process" on pending applications
   - Choose: Approve, Reject, or Request Revision
   - Add remarks
   - Submit decision

4. **Generate Reports**
   - Go to Reports section
   - Select date range
   - View statistics and analytics
   - Print or export reports

## 🎨 Design Features

- **Responsive Design:** Works on desktop, tablet, and mobile
- **Clean UI:** Professional government-appropriate interface
- **Color Scheme:** Blue and white tones suitable for LGU
- **Intuitive Navigation:** Easy-to-use menu system
- **Form Validation:** Real-time validation with error messages
- **Loading States:** Visual feedback for async operations

## 📄 Required Documents

Applicants need to upload the following documents:
- DTI/SEC/CDA Registration Certificate
- Barangay Clearance
- Fire Safety Inspection Certificate
- Sanitary Permit (for food establishments)
- Occupancy Permit
- Location Plan/Vicinity Map

## 🔧 Troubleshooting

### Common Issues

**Database Connection Error:**
- Check database credentials in `config/database.php`
- Ensure MySQL service is running
- Verify database exists

**File Upload Errors:**
- Check `uploads/` directory permissions
- Verify `php.ini` upload limits
- Ensure `upload_max_filesize` and `post_max_size` are adequate

**Session Issues:**
- Check `session.save_path` in `php.ini`
- Ensure session directory is writable
- Clear browser cookies

**Styling Issues:**
- Clear browser cache
- Check CSS file paths in configuration
- Verify `SITE_URL` is correct

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📜 License

This project is open-source and available for use by Local Government Units.

## 👥 Support

For issues and questions, please open an issue in the GitHub repository.

## 🔄 Future Enhancements

- Email notifications for status updates
- SMS notifications
- Online payment integration
- PDF generation for permits
- Advanced analytics and reporting
- Mobile application
- API for third-party integrations

## 📊 Database Tables

### users
Stores applicant account information

### admins
Stores administrator accounts

### applications
Stores all business permit applications

### documents
Stores uploaded document information

### application_logs
Tracks all actions performed on applications

---

**Note:** This system is designed for Local Government Units to streamline their business permit processing. Always ensure proper security measures are in place when deploying to production.
