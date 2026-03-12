<script>
{{-- ============================================ --}}
{{-- Taskbar Management Functions - Функции управления панелью задач --}}
{{-- ============================================ --}}

/**
 * Add window button to taskbar
 * Creates a button for the window in the taskbar
 * 
 * @param {Ext.window.Window} win - ExtJS window object
 */
function addWindowToTaskbar(win) {
    var taskbar = document.getElementById('taskbar-windows');
    if (!taskbar) return;
    
    // Проверяем, не существует ли уже кнопка для этого окна
    var existingBtn = document.getElementById('taskbar-btn-' + win.id);
    if (existingBtn) {
        console.log('Кнопка для окна', win.id, 'уже существует на панели задач');
        return;
    }
    
    var btn = document.createElement('button');
    btn.className = 'window-button open'; // Добавляем класс open при создании
    btn.id = 'taskbar-btn-' + win.id;
    btn.textContent = win.title;
    btn.onclick = function() {
        if (win.isVisible() && !win.minimized) {
            win.minimize();
        } else {
            win.show();
            win.toFront();
        }
    };
    
    taskbar.appendChild(btn);
}

/**
 * Remove window button from taskbar
 * 
 * @param {string} winId - Window ID
 */
function removeWindowFromTaskbar(winId) {
    var btn = document.getElementById('taskbar-btn-' + winId);
    if (btn) {
        btn.parentNode.removeChild(btn);
    }
}

/**
 * Update taskbar button state
 * Updates button styling based on window state
 * 
 * @param {string} winId - Window ID
 * @param {boolean} isActive - Is window active
 * @param {boolean} isMinimized - Is window minimized
 */
function updateTaskbarButton(winId, isActive, isMinimized) {
    var btn = document.getElementById('taskbar-btn-' + winId);
    console.log('updateTaskbarButton:', winId, 'isActive:', isActive, 'isMinimized:', isMinimized, 'btn found:', !!btn);
    if (!btn) return;
    
    // Убираем все классы состояния
    btn.classList.remove('active', 'minimized', 'open');
    
    if (isMinimized) {
        // Минимизированное окно - линия + прозрачность
        btn.classList.add('minimized');
    } else if (isActive) {
        // Активное окно - подсвечено + яркая линия
        btn.classList.add('active');
    } else {
        // Открытое неактивное окно - просто линия
        btn.classList.add('open');
    }
}

/**
 * Add taskbar button (alternative implementation)
 * 
 * @param {string} windowId - Window ID
 * @param {string} title - Window title
 * @param {Ext.window.Window} win - ExtJS window object
 * @return {HTMLElement} Created button element
 */
function addTaskbarButton(windowId, title, win) {
    var container = document.getElementById('taskbar-windows');
    var btn = document.createElement('button');
    btn.className = 'window-button active';
    btn.textContent = title;
    btn.id = 'taskbar-' + windowId;
    btn.onclick = function() {
        if (win.hidden) {
            win.show();
            win.toFront();
            btn.classList.remove('minimized');
            setActiveWindow(windowId);
        } else if (win === Ext.WindowManager.getActive()) {
            win.hide();
            btn.classList.add('minimized');
            btn.classList.remove('active');
        } else {
            win.show();
            win.toFront();
            setActiveWindow(windowId);
        }
    };
    container.appendChild(btn);
    return btn;
}

/**
 * Remove taskbar button (alternative implementation)
 * 
 * @param {string} windowId - Window ID
 */
function removeTaskbarButton(windowId) {
    var btn = document.getElementById('taskbar-' + windowId);
    if (btn) btn.remove();
}

/**
 * Set active window styling
 * Updates all taskbar buttons to reflect active window
 * 
 * @param {string} windowId - Window ID to set as active
 */
function setActiveWindow(windowId) {
    var buttons = document.querySelectorAll('.window-button');
    buttons.forEach(function(btn) {
        btn.classList.remove('active');
    });
    var activeBtn = document.getElementById('taskbar-' + windowId);
    if (activeBtn) {
        activeBtn.classList.add('active');
        activeBtn.classList.remove('minimized');
    }
}
</script>