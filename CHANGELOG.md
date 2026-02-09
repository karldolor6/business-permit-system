# Changelog

All notable changes to the Business Permit System will be documented in this file.

## [1.0.0] - 2026-02-09

### Added
- Initial release of Business Permit Processing System
- User registration and authentication system
- Business permit application form with document upload
- Application tracking by reference number
- Applicant dashboard with statistics
- Admin authentication and authorization
- Admin dashboard with overview statistics
- Application management (view, approve, reject, request revision)
- User management interface
- Reports and analytics with date filtering
- Responsive design for mobile and desktop
- CSRF protection for forms
- Password hashing with bcrypt
- SQL injection prevention with prepared statements
- XSS protection with input sanitization
- File upload validation
- Session security
- Print-friendly permit design
- Activity logging for all application actions
- Real-time form validation (client and server-side)

### Security Features
- Password hashing using PHP password_hash()
- CSRF token protection
- Prepared statements for SQL queries
- Input sanitization and output escaping
- Secure session configuration
- File type and size validation
- Access control for admin pages

### Database Tables
- `users` - Applicant accounts
- `admins` - Administrator accounts
- `applications` - Business permit applications
- `documents` - Uploaded document references
- `application_logs` - Activity tracking

### Default Accounts
- Admin: username `admin`, password `admin123`
- Test User: username `testuser`, password `password123`

### Supported Business Types
- Single Proprietorship
- Partnership
- Corporation
- Cooperative
- Other

### Required Documents
- DTI/SEC/CDA Registration Certificate
- Barangay Clearance
- Fire Safety Inspection Certificate
- Sanitary Permit
- Occupancy Permit
- Location Plan/Vicinity Map

---

## Future Versions

### Planned Features for v1.1.0
- Email notifications for status updates
- SMS notifications
- PDF generation for permits
- Advanced search and filtering
- Bulk application processing
- Document preview functionality

### Planned Features for v1.2.0
- Online payment integration
- Digital signature support
- API for third-party integrations
- Mobile application
- Multi-language support
- Enhanced analytics dashboard

### Planned Features for v2.0.0
- Business permit renewal system
- Automated fee calculation
- Integration with other LGU systems
- Advanced reporting with charts
- Document versioning
- Audit trail enhancements
