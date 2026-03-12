<script>
// ============================================
// Global Variables and Configuration
// ============================================

// Передаём права доступа в JavaScript для проверок в окнах
var moduleAccess = {
    permissions: 
@if(isset($moduleAccess['permissions']) && $moduleAccess['permissions'])
true
@else
false
@endif
    ,
    users: 
@if(isset($moduleAccess['users']) && $moduleAccess['users'])
true
@else
false
@endif
    ,
    products: 
@if(isset($moduleAccess['products']) && $moduleAccess['products'])
true
@else
false
@endif
    ,
    import: 
@if(isset($moduleAccess['import']) && $moduleAccess['import'])
true
@else
false
@endif
    ,
    app_installer: 
@if(isset($moduleAccess['app_installer']) && $moduleAccess['app_installer'])
true
@else
false
@endif
@foreach($installed_apps as $app)
    ,
    {{ $app['app_id'] }}: 
@if(isset($moduleAccess[$app['app_id']]) && $moduleAccess[$app['app_id']])
true
@else
false
@endif
@endforeach
};

// Управление окнами
var openWindows = {};

// Окно профиля пользователя
var profileWindow = null;

// Окно просмотра баннера
var bannerPreviewWindow = null;

{{-- ============================================ --}}
{{-- Custom Modal Dialog Functions - Windows 10 Style --}}
{{-- ============================================ --}}

/**
 * Показать кастомное модальное окно в стиле Windows 10
 * 
 * @param {Object} options - Параметры модального окна
 * @param {string} options.title - Заголовок окна
 * @param {string} options.message - Текст сообщения
 * @param {string} options.icon - Тип иконки: 'question', 'warning', 'error', 'info'
 * @param {Array} options.buttons - Массив кнопок [{text: 'Текст', type: 'primary|secondary', callback: function}]
 * @param {function} options.onClose - Callback при закрытии окна
 */
function showCustomModal(options) {
    {{-- Закрываем все меню --}}
    closeStartMenu();
    closeUserMenu();
    
    {{-- Создаем HTML структуру модального окна --}}
    var modalHtml = `
        <div class="custom-modal-overlay" id="customModalOverlay">
            <div class="custom-modal">
                <div class="custom-modal-header">
                    <div class="custom-modal-icon ${options.icon || 'question'}">
                        <span class="material-icons">${getIconName(options.icon)}</span>
                    </div>
                    <h3 class="custom-modal-title">${options.title || 'Подтверждение'}</h3>
                </div>
                <div class="custom-modal-body">
                    <p class="custom-modal-message">${options.message || ''}</p>
                </div>
                <div class="custom-modal-actions">
                    ${generateButtons(options.buttons || [])}
                </div>
            </div>
        </div>
    `;
    
    {{-- Добавляем модальное окно в DOM --}}
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    var overlay = document.getElementById('customModalOverlay');
    
    {{-- Показываем модальное окно с анимацией --}}
    setTimeout(function() {
        overlay.classList.add('show');
    }, 10);
    
    {{-- Обработчик клика по overlay для закрытия --}}
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) {
            closeCustomModal();
        }
    });
    
    {{-- Обработчик ESC для закрытия --}}
    document.addEventListener('keydown', handleModalKeydown);
    
    {{-- Фокус на первую кнопку --}}
    setTimeout(function() {
        var firstButton = overlay.querySelector('.custom-modal-button');
        if (firstButton) {
            firstButton.focus();
        }
    }, 200);
    
    return overlay;
}

/**
 * Закрыть кастомное модальное окно
 */
function closeCustomModal() {
    var overlay = document.getElementById('customModalOverlay');
    if (overlay) {
        overlay.classList.remove('show');
        document.removeEventListener('keydown', handleModalKeydown);
        
        setTimeout(function() {
            if (overlay.parentNode) {
                overlay.parentNode.removeChild(overlay);
            }
        }, 200);
    }
}

/**
 * Обработчик нажатий клавиш в модальном окне
 */
function handleModalKeydown(e) {
    if (e.key === 'Escape') {
        closeCustomModal();
    }
}

/**
 * Получить имя иконки Material Icons по типу
 */
function getIconName(iconType) {
    switch (iconType) {
        case 'question': return 'help_outline';
        case 'warning': return 'warning';
        case 'error': return 'error_outline';
        case 'info': return 'info_outline';
        default: return 'help_outline';
    }
}

/**
 * Генерировать HTML для кнопок
 */
function generateButtons(buttons) {
    return buttons.map(function(button) {
        return `<button class="custom-modal-button ${button.type || 'secondary'}" 
                        onclick="handleModalButtonClick('${button.action || ''}', this)">
                    ${button.text || 'OK'}
                </button>`;
    }).join('');
}

/**
 * Обработчик клика по кнопке модального окна
 */
function handleModalButtonClick(action, buttonElement) {
    {{-- Вызываем callback если есть --}}
    if (window.currentModalCallback && typeof window.currentModalCallback === 'function') {
        window.currentModalCallback(action);
    }
    
    closeCustomModal();
}

</script>
