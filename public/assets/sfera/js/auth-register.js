// Register Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('registerForm');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');

    // Phone mask
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = this.value.replace(/\D/g, '');
            
            // Ограничиваем длину
            if (value.length > 11) {
                value = value.slice(0, 11);
            }
            
            // Форматируем номер
            let formatted = '';
            if (value.length > 0) {
                formatted = '+7';
                if (value.length > 1) {
                    formatted += ' (' + value.slice(1, 4);
                }
                if (value.length >= 4) {
                    formatted += ') ' + value.slice(4, 7);
                }
                if (value.length >= 7) {
                    formatted += '-' + value.slice(7, 9);
                }
                if (value.length >= 9) {
                    formatted += '-' + value.slice(9, 11);
                }
            }
            
            this.value = formatted;
        });
        
        // Автоматически добавляем +7 при фокусе
        phoneInput.addEventListener('focus', function() {
            if (!this.value) {
                this.value = '+7 ';
            }
        });
    }

    // Password strength checker
    if (passwordInput) {
        const strengthFill = document.getElementById('strengthFill');
        const strengthText = document.getElementById('strengthText');
        
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = checkPasswordStrength(password);
            
            if (!strengthFill || !strengthText) return;
            
            // Reset classes
            strengthFill.className = 'strength-fill';
            strengthText.className = 'strength-text';
            
            if (password.length === 0) {
                strengthText.textContent = '';
                return;
            }
            
            if (strength < 3) {
                strengthFill.classList.add('weak');
                strengthText.classList.add('weak');
                strengthText.textContent = 'Слабый пароль';
            } else if (strength < 4) {
                strengthFill.classList.add('medium');
                strengthText.classList.add('medium');
                strengthText.textContent = 'Средний пароль';
            } else {
                strengthFill.classList.add('strong');
                strengthText.classList.add('strong');
                strengthText.textContent = 'Надёжный пароль';
            }
        });
    }

    function checkPasswordStrength(password) {
        let strength = 0;
        
        if (password.length >= 6) strength++;
        if (password.length >= 10) strength++;
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
        if (/\d/.test(password)) strength++;
        if (/[^a-zA-Z0-9]/.test(password)) strength++;
        
        return strength;
    }

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
        
        if (input.id === 'name' && input.value.trim().length > 0 && input.value.trim().length < 2) {
            isValid = false;
            message = 'Имя должно содержать минимум 2 символа';
        }
        
        if (input.type === 'email' && input.value.trim()) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(input.value)) {
                isValid = false;
                message = 'Введите корректный email';
            }
        }
        
        if (input.type === 'tel' && input.value.trim()) {
            // Проверяем что номер полный (11 цифр)
            const digitsOnly = input.value.replace(/\D/g, '');
            if (digitsOnly.length < 11) {
                isValid = false;
                message = 'Введите полный номер телефона';
            }
        }
        
        if (input.id === 'password' && input.value) {
            if (input.value.length < 6) {
                isValid = false;
                message = 'Пароль должен содержать минимум 6 символов';
            }
        }
        
        if (input.id === 'password_confirmation' && input.value) {
            if (passwordInput && input.value !== passwordInput.value) {
                isValid = false;
                message = 'Пароли не совпадают';
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

    // Add validation on blur and input
    const fields = [nameInput, emailInput, phoneInput, passwordInput, confirmPasswordInput];
    fields.forEach(field => {
        if (field) {
            field.addEventListener('blur', () => validateField(field));
            field.addEventListener('input', () => {
                if (field.closest('.form-group').classList.contains('error')) {
                    validateField(field);
                }
            });
        }
    });

    // Form submission
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            // Validate all fields before submission
            let isFormValid = true;
            
            fields.forEach(field => {
                if (field && !validateField(field)) {
                    isFormValid = false;
                }
            });
            
            if (!isFormValid) {
                e.preventDefault();
                showToast('Пожалуйста, исправьте ошибки в форме', 'error');
                return false;
            }
            
            // Disable submit button to prevent double submission
            const submitBtn = this.querySelector('.btn-submit');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Регистрация...';
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

    console.log('Register page initialized');
});
