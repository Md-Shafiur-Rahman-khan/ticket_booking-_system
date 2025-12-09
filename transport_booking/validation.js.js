// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;
    
    let isValid = true;
    const inputs = form.querySelectorAll('input[required], select[required]');
    
    inputs.forEach(input => {
        input.classList.remove('error');
        
        if (!input.value.trim()) {
            isValid = false;
            input.classList.add('error');
            showError(input, 'This field is required');
        } else if (input.type === 'email' && !validateEmail(input.value)) {
            isValid = false;
            input.classList.add('error');
            showError(input, 'Please enter a valid email address');
        } else if (input.type === 'tel' && !validatePhone(input.value)) {
            isValid = false;
            input.classList.add('error');
            showError(input, 'Please enter a valid phone number');
        }
    });
    
    return isValid;
}

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validatePhone(phone) {
    const re = /^[\+]?[1-9][\d]{0,15}$/;
    return re.test(phone.replace(/[\s\-\(\)]/g, ''));
}

function showError(element, message) {
    let errorDiv = element.parentElement.querySelector('.error-message');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        element.parentElement.appendChild(errorDiv);
    }
    errorDiv.textContent = message;
    errorDiv.style.color = '#f56565';
    errorDiv.style.fontSize = '14px';
    errorDiv.style.marginTop = '5px';
}

// Add CSS for error state
const style = document.createElement('style');
style.textContent = `
    .error {
        border-color: #f56565 !important;
        background-color: #fff5f5 !important;
    }
    
    .error:focus {
        box-shadow: 0 0 0 3px rgba(245, 101, 101, 0.1) !important;
    }
`;
document.head.appendChild(style);