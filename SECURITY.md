# Security Guidelines & Best Practices

## Security Features Implemented

### 1. Authentication & Authorization
- ✅ Secure password hashing using `password_hash()` with bcrypt
- ✅ Session-based authentication
- ✅ Role-based access control (User vs Admin)
- ✅ Session regeneration on login
- ✅ Secure session configuration

### 2. Input Validation
- ✅ Client-side validation (JavaScript)
- ✅ Server-side validation (PHP)
- ✅ Email format validation
- ✅ Phone number format validation
- ✅ Required field validation
- ✅ Input length validation

### 3. SQL Injection Prevention
- ✅ All database queries use prepared statements
- ✅ Parameterized queries with bound parameters
- ✅ No direct SQL concatenation with user input

### 4. XSS (Cross-Site Scripting) Prevention
- ✅ Input sanitization using `htmlspecialchars()`
- ✅ Output escaping for all user-generated content
- ✅ ENT_QUOTES flag for comprehensive escaping

### 5. CSRF (Cross-Site Request Forgery) Protection
- ✅ CSRF tokens generated for all forms
- ✅ Token verification on form submission
- ✅ Token stored in session

### 6. File Upload Security
- ✅ File type validation (whitelist approach)
- ✅ File size validation (5MB limit)
- ✅ Unique filename generation
- ✅ Files stored outside web root (recommended)
- ✅ .htaccess protection in uploads directory

### 7. Session Security
- ✅ HTTPOnly flag for session cookies
- ✅ Session timeout configuration
- ✅ Secure session storage
- ✅ Session destruction on logout

### 8. Access Control
- ✅ Authentication checks on protected pages
- ✅ Admin-only areas protected
- ✅ User can only view own applications
- ✅ Redirect to login for unauthorized access

## Security Recommendations for Production

### Critical - Must Implement

1. **Enable HTTPS**
   ```php
   // In config/config.php
   ini_set('session.cookie_secure', 1); // Enable when using HTTPS
   ```

2. **Change Default Credentials**
   - Immediately change the default admin password
   - Remove or change test user accounts

3. **Disable Error Display**
   ```php
   // In config/config.php for production
   error_reporting(0);
   ini_set('display_errors', 0);
   ```

4. **Database User Permissions**
   - Create a dedicated database user
   - Grant only necessary privileges (SELECT, INSERT, UPDATE, DELETE)
   - Do not use root user in production

5. **File Permissions**
   ```bash
   # Set proper permissions
   find . -type f -exec chmod 644 {} \;
   find . -type d -exec chmod 755 {} \;
   chmod 755 uploads/
   ```

### Important - Highly Recommended

1. **Rate Limiting**
   - Implement login attempt limits
   - Add CAPTCHA for repeated failed attempts
   - Throttle form submissions

2. **Password Policy**
   - Enforce strong passwords
   - Require password change on first login
   - Implement password expiry (e.g., 90 days)

3. **Logging & Monitoring**
   - Log all authentication attempts
   - Log administrative actions
   - Monitor for suspicious activity
   - Regular security audits

4. **Backup Strategy**
   ```bash
   # Database backup
   mysqldump -u username -p business_permit_db > backup.sql
   
   # Files backup
   tar -czf uploads_backup.tar.gz uploads/
   ```

5. **SSL/TLS Certificate**
   - Use Let's Encrypt for free SSL
   - Force HTTPS redirects

### Recommended - Additional Security

1. **Content Security Policy (CSP)**
   ```php
   header("Content-Security-Policy: default-src 'self'");
   ```

2. **Security Headers**
   ```php
   header("X-Frame-Options: DENY");
   header("X-Content-Type-Options: nosniff");
   header("X-XSS-Protection: 1; mode=block");
   ```

3. **Database Connection**
   - Use SSL for database connections
   - Separate database server if possible

4. **Two-Factor Authentication (2FA)**
   - Consider implementing 2FA for admin accounts

5. **Regular Updates**
   - Keep PHP updated
   - Keep MySQL updated
   - Update dependencies regularly

## Common Security Pitfalls to Avoid

### ❌ Don't Do This:

1. **Don't store passwords in plain text**
   ```php
   // WRONG
   $password = $_POST['password'];
   
   // CORRECT
   $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
   ```

2. **Don't concatenate SQL queries**
   ```php
   // WRONG
   $query = "SELECT * FROM users WHERE username = '$username'";
   
   // CORRECT
   $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
   $stmt->bind_param("s", $username);
   ```

3. **Don't trust user input**
   ```php
   // WRONG
   echo $_POST['name'];
   
   // CORRECT
   echo htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
   ```

4. **Don't expose sensitive information**
   ```php
   // WRONG
   die("MySQL Error: " . $conn->error);
   
   // CORRECT
   error_log("MySQL Error: " . $conn->error);
   die("An error occurred. Please try again.");
   ```

## Security Checklist

### Before Deployment

- [ ] Change all default passwords
- [ ] Remove test accounts
- [ ] Disable error display
- [ ] Enable HTTPS
- [ ] Set proper file permissions
- [ ] Configure secure session settings
- [ ] Review database user permissions
- [ ] Test all security features
- [ ] Review all input validation
- [ ] Check file upload restrictions
- [ ] Verify CSRF tokens on all forms
- [ ] Test authentication and authorization
- [ ] Review SQL queries for injection vulnerabilities
- [ ] Check for XSS vulnerabilities
- [ ] Configure backup strategy
- [ ] Set up monitoring and logging
- [ ] Review security headers
- [ ] Test on different browsers
- [ ] Perform security audit
- [ ] Document security procedures

### Regular Maintenance

- [ ] Weekly database backups
- [ ] Weekly file backups
- [ ] Monthly security reviews
- [ ] Quarterly password changes
- [ ] Monitor logs for suspicious activity
- [ ] Update system regularly
- [ ] Review user accounts
- [ ] Check file integrity
- [ ] Review access logs

## Security Incident Response

If you suspect a security breach:

1. **Immediate Actions**
   - Take the system offline if necessary
   - Change all passwords
   - Review recent logs
   - Identify the vulnerability

2. **Investigation**
   - Check all log files
   - Review recent changes
   - Identify affected data
   - Determine breach scope

3. **Remediation**
   - Fix the vulnerability
   - Restore from clean backup if needed
   - Update security measures
   - Document the incident

4. **Communication**
   - Notify affected users
   - Report to authorities if required
   - Update security documentation

## Contact for Security Issues

For security vulnerabilities or concerns:
- Create a private security advisory on GitHub
- Contact system administrator
- Do not publicly disclose vulnerabilities until fixed

## Additional Resources

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [PHP Security Guide](https://www.php.net/manual/en/security.php)
- [MySQL Security](https://dev.mysql.com/doc/refman/8.0/en/security.html)

---

**Remember:** Security is an ongoing process, not a one-time implementation. Regularly review and update security measures.
