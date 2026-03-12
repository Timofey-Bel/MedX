// Login Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    // Real-time validation
    function validateField(input) {
        const formGroup = input.closest('.form-group');
        const errorMessage = formGroup.querySelector('.error-message');
        
        let isValid = true;
        let message = '';
        
        if (input.hasAttribute('required') && !input.value.trim()) {
            isValid = false;
            message = 'Это поле обязательно для заполнения';
        }
        
        if (input.type === 'email' && input.value.trim()) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(input.value)) {
                isValid = false;
                message = 'Введите корректный email';
            }
        }
        
        if (isValid) {
            formGroup.classList.remove('error');
            if (errorMessage) errorMessage.textContent = '';
        } else {
            formGroup.classList.add('error');
            if (errorMessage) errorMessage.textContent = message;
        }
        
        return isValid;
    }

    // Add validation on blur
    if (emailInput) {
        emailInput.addEventListener('blur', () => validateField(emailInput));
        emailInput.addEventListener('input', () => {
            if (emailInput.closest('.form-group').classList.contains('error')) {
                validateField(emailInput);
            }
        });
    }

    if (passwordInput) {
        passwordInput.addEventListener('blur', () => validateField(passwordInput));
        passwordInput.addEventListener('input', () => {
            if (passwordInput.closest('.form-group').classList.contains('error')) {
                validateField(passwordInput);
            }
        });
    }

    // Form submission
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            // Validate all fields before submission
            let isFormValid = true;
            
            if (emailInput && !validateField(emailInput)) {
                isFormValid = false;
            }
            
            if (passwordInput && !validateField(passwordInput)) {
                isFormValid = false;
            }
            
            if (!isFormValid) {
                e.preventDefault();
                showToast('Пожалуйста, исправьте ошибки в форме', 'error');
                return false;
            }
            
            // Disable submit button to prevent double submission
            const submitBtn = this.querySelector('.btn-submit');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Вход...';
            }
        });
    }

    // Toast notification function
    function showToast(message, type = 'info') {
        // Remove existing toast
        const existing = document.querySelector('.toast-notification');
        if (existing) {
            existing.remove();
        }
        
        const toast = document.createElement('div');
        toast.className = `toast-notification ${type}`;
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        // Auto remove after 4 seconds
        setTimeout(() => {
            toast.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

    // Show toast for server errors (if any)
    const alertError = document.querySelector('.alert-error');
    if (alertError) {
        const message = alertError.textContent.trim();
        if (message) {
            showToast(message, 'error');
        }
    }

    console.log('Login page initialized');
});
