// Переключение видимости пароля
document.addEventListener('DOMContentLoaded', function() {
    const passwordToggles = document.querySelectorAll('.password-toggle');
    
    passwordToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const passwordInput = this.previousElementSibling;
            const icon = this.querySelector('.password-toggle__icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                // Меняем на открытый глаз (eye-v2)
                icon.src = icon.src.replace('eye.png', 'eye-v2.png');
            } else {
                passwordInput.type = 'password';
                // Возвращаем закрытый глаз (eye)
                icon.src = icon.src.replace('eye-v2.png', 'eye.png');
            }
        });
    });
});
