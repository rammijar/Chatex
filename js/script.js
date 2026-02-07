// Chatex Social Media Platform
// Basic JavaScript for client-side functionality

// Function to confirm before deleting
function confirmDelete(itemName) {
    return confirm('Are you sure you want to delete ' + itemName + '?');
}

// Function to toggle elements visibility
function toggleElement(elementId) {
    var element = document.getElementById(elementId);
    if (element.style.display === 'none') {
        element.style.display = 'block';
    } else {
        element.style.display = 'none';
    }
}

// Function to validate email format
function validateEmail(email) {
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Function to validate username format
function validateUsername(username) {
    var usernameRegex = /^[a-zA-Z0-9_]{3,20}$/;
    return usernameRegex.test(username);
}

// Function to show notification
function showNotification(message, type) {
    // Create notification element
    var notification = document.createElement('div');
    notification.className = type + '-box';
    notification.innerHTML = '<p>' + message + '</p>';
    
    // Append to body
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(function() {
        notification.remove();
    }, 3000);
}

// Auto-resize textarea
function autoResizeTextarea(element) {
    element.style.height = 'auto';
    element.style.height = element.scrollHeight + 'px';
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners to textareas for auto-resize
    var textareas = document.querySelectorAll('textarea');
    textareas.forEach(function(textarea) {
        textarea.addEventListener('input', function() {
            autoResizeTextarea(this);
        });
    });
    
    // Add form validation
    var forms = document.querySelectorAll('form');
    forms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            // Basic form validation can be added here
            // Currently handled by HTML5 required attribute
        });
    });
});

// Function to format date and time
function formatDateTime(dateString) {
    var date = new Date(dateString);
    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
}

// Function to scroll to bottom of messages
function scrollToBottom(elementId) {
    var element = document.getElementById(elementId);
    if (element) {
        element.scrollTop = element.scrollHeight;
    }
}

// Function to validate password strength
function validatePasswordStrength(password) {
    var strength = 0;
    
    // Check for minimum length
    if (password.length >= 6) strength++;
    
    // Check for uppercase
    if (/[A-Z]/.test(password)) strength++;
    
    // Check for lowercase
    if (/[a-z]/.test(password)) strength++;
    
    // Check for numbers
    if (/[0-9]/.test(password)) strength++;
    
    // Check for special characters
    if (/[!@#$%^&*]/.test(password)) strength++;
    
    return strength;
}

// Function to limit character count in textarea
function limitCharacters(elementId, maxLength) {
    var element = document.getElementById(elementId);
    if (element) {
        element.addEventListener('input', function() {
            if (this.value.length > maxLength) {
                this.value = this.value.substring(0, maxLength);
            }
        });
    }
}

// Function to check browser support
function checkBrowserSupport() {
    if (!window.addEventListener) {
        alert('Your browser is too old. Please update to a modern browser.');
        return false;
    }
    return true;
}

// Run browser check on load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', checkBrowserSupport);
} else {
    checkBrowserSupport();
}
