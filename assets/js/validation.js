// Form Validation JavaScript

// Email validation
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Phone validation (Philippine format)
function validatePhone(phone) {
    const re = /^(09|\+639)\d{9}$/;
    return re.test(phone);
}

// Password strength validation
function validatePassword(password) {
    return password.length >= 8;
}

// Show error message
function showError(input, message) {
    const formGroup = input.parentElement;
    input.classList.add('error');
    
    // Remove existing error message
    const existingError = formGroup.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }
    
    // Add new error message
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.textContent = message;
    formGroup.appendChild(errorDiv);
}

// Clear error message
function clearError(input) {
    const formGroup = input.parentElement;
    input.classList.remove('error');
    
    const existingError = formGroup.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }
}

// Registration form validation
function validateRegistrationForm() {
    const form = document.getElementById('registrationForm');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Username validation
        const username = document.getElementById('username');
        if (username) {
            if (username.value.trim().length < 4) {
                showError(username, 'Username must be at least 4 characters');
                isValid = false;
            } else {
                clearError(username);
            }
        }
        
        // Email validation
        const email = document.getElementById('email');
        if (email) {
            if (!validateEmail(email.value)) {
                showError(email, 'Please enter a valid email address');
                isValid = false;
            } else {
                clearError(email);
            }
        }
        
        // Password validation
        const password = document.getElementById('password');
        if (password) {
            if (!validatePassword(password.value)) {
                showError(password, 'Password must be at least 8 characters');
                isValid = false;
            } else {
                clearError(password);
            }
        }
        
        // Confirm password validation
        const confirmPassword = document.getElementById('confirm_password');
        if (confirmPassword && password) {
            if (confirmPassword.value !== password.value) {
                showError(confirmPassword, 'Passwords do not match');
                isValid = false;
            } else {
                clearError(confirmPassword);
            }
        }
        
        // Full name validation
        const fullName = document.getElementById('full_name');
        if (fullName) {
            if (fullName.value.trim().length < 3) {
                showError(fullName, 'Please enter your full name');
                isValid = false;
            } else {
                clearError(fullName);
            }
        }
        
        // Contact number validation
        const contact = document.getElementById('contact_number');
        if (contact) {
            if (contact.value && !validatePhone(contact.value)) {
                showError(contact, 'Please enter a valid Philippine phone number (e.g., 09123456789)');
                isValid = false;
            } else {
                clearError(contact);
            }
        }
        
        if (!isValid) {
            e.preventDefault();
        }
    });
}

// Login form validation
function validateLoginForm() {
    const form = document.getElementById('loginForm');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Username validation
        const username = document.getElementById('username');
        if (username) {
            if (username.value.trim().length === 0) {
                showError(username, 'Username is required');
                isValid = false;
            } else {
                clearError(username);
            }
        }
        
        // Password validation
        const password = document.getElementById('password');
        if (password) {
            if (password.value.length === 0) {
                showError(password, 'Password is required');
                isValid = false;
            } else {
                clearError(password);
            }
        }
        
        if (!isValid) {
            e.preventDefault();
        }
    });
}

// Application form validation
function validateApplicationForm() {
    const form = document.getElementById('applicationForm');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Business name
        const businessName = document.getElementById('business_name');
        if (businessName && businessName.value.trim().length < 3) {
            showError(businessName, 'Business name is required');
            isValid = false;
        } else if (businessName) {
            clearError(businessName);
        }
        
        // Business type
        const businessType = document.getElementById('business_type');
        if (businessType && businessType.value === '') {
            showError(businessType, 'Please select a business type');
            isValid = false;
        } else if (businessType) {
            clearError(businessType);
        }
        
        // Business address
        const businessAddress = document.getElementById('business_address');
        if (businessAddress && businessAddress.value.trim().length < 10) {
            showError(businessAddress, 'Please enter a complete business address');
            isValid = false;
        } else if (businessAddress) {
            clearError(businessAddress);
        }
        
        // Owner name
        const ownerName = document.getElementById('owner_name');
        if (ownerName && ownerName.value.trim().length < 3) {
            showError(ownerName, 'Owner name is required');
            isValid = false;
        } else if (ownerName) {
            clearError(ownerName);
        }
        
        // Owner contact
        const ownerContact = document.getElementById('owner_contact');
        if (ownerContact && !validatePhone(ownerContact.value)) {
            showError(ownerContact, 'Please enter a valid phone number');
            isValid = false;
        } else if (ownerContact) {
            clearError(ownerContact);
        }
        
        // Owner address
        const ownerAddress = document.getElementById('owner_address');
        if (ownerAddress && ownerAddress.value.trim().length < 10) {
            showError(ownerAddress, 'Please enter a complete owner address');
            isValid = false;
        } else if (ownerAddress) {
            clearError(ownerAddress);
        }
        
        if (!isValid) {
            e.preventDefault();
        }
    });
}

// File upload validation
function validateFileUpload() {
    const fileInputs = document.querySelectorAll('input[type="file"]');
    
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;
            
            // Check file size (5MB)
            const maxSize = 5 * 1024 * 1024;
            if (file.size > maxSize) {
                showError(this, 'File size must not exceed 5MB');
                this.value = '';
                return;
            }
            
            // Check file type
            const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            if (!allowedTypes.includes(file.type)) {
                showError(this, 'Invalid file type. Allowed: PDF, JPG, PNG, DOC, DOCX');
                this.value = '';
                return;
            }
            
            clearError(this);
        });
    });
}

// Initialize all validations
document.addEventListener('DOMContentLoaded', function() {
    validateRegistrationForm();
    validateLoginForm();
    validateApplicationForm();
    validateFileUpload();
});
