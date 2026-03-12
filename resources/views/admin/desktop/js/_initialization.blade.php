<script>
{{-- ============================================ --}}
{{-- Initialization - Инициализация системы и обработчиков --}}
{{-- ============================================ --}}

document.addEventListener('DOMContentLoaded', function() {
    var startBtn = document.getElementById('start-button');
    if (startBtn) {
        startBtn.addEventListener('click', toggleStartMenu);
    }
    
    // Инициализация часов
    updateClock();
    setInterval(updateClock, 1000);
});

{{-- Закрытие меню при клике вне его --}}
document.addEventListener('click', function(e) {
    var menu = document.getElementById('start-menu-modal');
    var startBtn = document.getElementById('start-button');
    if (!menu.contains(e.target) && e.target !== startBtn && !startBtn.contains(e.target)) {
        closeStartMenu();
    }
});

{{-- Закрытие меню пользователя при клике вне его --}}
document.addEventListener('click', function(e) {
    var userMenu = document.getElementById('user-menu');
    var userBtn = document.querySelector('.tray-button[onclick="showUserMenu()"]');
    if (userMenu && userBtn && !userMenu.contains(e.target) && e.target !== userBtn && !userBtn.contains(e.target)) {
        closeUserMenu();
    }
});

{{-- Глобальные переменные --}}
var profileWindow = null;
</script>