<script>
{{-- ============================================ --}}
{{-- User Menu Functions - Windows 10 Style --}}
{{-- ============================================ --}}

/**
 * Show user profile window
 * Opens profile in an ExtJS window with iframe
 */
function showProfile() {
    closeStartMenu();
    
    if (profileWindow && !profileWindow.isDestroyed) {
        profileWindow.show();
        profileWindow.toFront();
        return;
    }
    
    profileWindow = Ext.create('Ext.window.Window', {
        title: 'Профиль пользователя',
        width: 850,
        height: 600,
        layout: 'fit',
        modal: false,
        maximizable: true,
        minimizable: true,
        constrain: true,
        html: '<iframe src="/admin/profile/" style="width:100%; height:100%; border:0px;"></iframe>',
        listeners: {
            close: function() {
                profileWindow = null;
            },
            minimize: function() {
                this.hide();
            }
        }
    });
    
    profileWindow.show();
}

/**
 * Show user menu dropdown
 * Toggles visibility and closes Start Menu if open
 */
function showUserMenu() {
    var menu = document.getElementById('user-menu');
    var isOpen = menu.classList.contains('open');
    
    // Закрываем меню Пуск если оно открыто
    closeStartMenu();
    
    if (isOpen) {
        closeUserMenu();
    } else {
        menu.classList.add('open');
    }
}

/**
 * Close user menu dropdown
 */
function closeUserMenu() {
    var menu = document.getElementById('user-menu');
    menu.classList.remove('open');
}

/**
 * Logout with confirmation
 * Shows Windows 10 style confirmation dialog and redirects to logout URL
 */
function logout() {
    {{-- Устанавливаем callback для обработки ответа --}}
    window.currentModalCallback = function(action) {
        if (action === 'logout') {
            {{-- Сразу перенаправляем на logout --}}
            window.location.href = '/admin/logout';
        }
    };
    
    {{-- Показываем кастомное модальное окно --}}
    showCustomModal({
        title: 'Выход из системы',
        message: 'Вы действительно хотите завершить сеанс работы?',
        icon: 'question',
        buttons: [
            {
                text: 'Отмена',
                type: 'secondary',
                action: 'cancel'
            },
            {
                text: 'Выйти',
                type: 'primary',
                action: 'logout'
            }
        ]
    });
}
</script>