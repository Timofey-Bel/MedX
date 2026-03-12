<script>
{{-- ============================================ --}}
{{-- Context Menu IIFE - Контекстное меню рабочего стола --}}
{{-- ============================================ --}}

{{-- ======================================================================== --}}
{{-- Контекстное меню - Windows 10 Style --}}
{{-- ======================================================================== --}}

{{-- Ждем полной загрузки DOM перед инициализацией контекстного меню --}}
document.addEventListener('DOMContentLoaded', function() {
    initializeContextMenu();
});

{{-- Также инициализируем при готовности ExtJS --}}
Ext.onReady(function() {
    {{-- Небольшая задержка для гарантии что все элементы созданы --}}
    setTimeout(function() {
        initializeContextMenu();
    }, 100);
});

function initializeContextMenu() {
    var desktopContextMenu = document.getElementById('desktop-context-menu');
    var iconContextMenu = document.getElementById('icon-context-menu');
    
    {{-- Проверяем, что элементы существуют --}}
    if (!desktopContextMenu || !iconContextMenu) {
        console.warn('Context menu elements not found');
        return;
    }
    
    {{-- Проверяем, не инициализировано ли уже --}}
    if (desktopContextMenu.hasAttribute('data-initialized')) {
        return;
    }
    
    {{-- Помечаем как инициализированное --}}
    desktopContextMenu.setAttribute('data-initialized', 'true');
    iconContextMenu.setAttribute('data-initialized', 'true');
    
    var currentIcon = null;
    var gridSize = 100; {{-- Размер сетки для автоматического размещения --}}
    
    console.log('Context menu initialized'); {{-- Отладочное сообщение --}}
    
    {{-- Закрытие контекстного меню при клике вне его --}}
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.context-menu')) {
            if (desktopContextMenu) desktopContextMenu.classList.remove('show');
            if (iconContextMenu) iconContextMenu.classList.remove('show');
        }
    });
    
    {{-- Закрытие контекстного меню при скролле --}}
    document.addEventListener('scroll', function() {
        if (desktopContextMenu) desktopContextMenu.classList.remove('show');
        if (iconContextMenu) iconContextMenu.classList.remove('show');
    });
    
    {{-- Контекстное меню для рабочего стола --}}
    var desktop = document.querySelector('.desktop');
    if (desktop) {
        desktop.addEventListener('contextmenu', function(e) {
            console.log('Desktop context menu triggered', e.target); {{-- Отладка --}}
            
            {{-- Проверяем, что клик был по рабочему столу, а не по иконке --}}
            if (e.target.classList.contains('shortcut') || 
                e.target.closest('.shortcut')) {
                console.log('Click on shortcut, skipping desktop menu'); {{-- Отладка --}}
                return; {{-- Пропускаем, обрабатывает меню иконки --}}
            }
            
            e.preventDefault();
            
            {{-- Закрываем меню иконки --}}
            iconContextMenu.classList.remove('show');
            
            {{-- Показываем меню рабочего стола --}}
            desktopContextMenu.style.left = e.pageX + 'px';
            desktopContextMenu.style.top = e.pageY + 'px';
            desktopContextMenu.classList.add('show');
            
            console.log('Desktop context menu shown at', e.pageX, e.pageY); {{-- Отладка --}}
        });
    }
    
    {{-- Контекстное меню для иконок --}}
    function attachIconContextMenu() {
        var shortcuts = document.querySelectorAll('.shortcut');
        console.log('Found shortcuts:', shortcuts.length); {{-- Отладка --}}
        
        shortcuts.forEach(function(shortcut) {
            {{-- Проверяем, не добавлен ли уже обработчик --}}
            if (shortcut.hasAttribute('data-context-menu-attached')) {
                return;
            }
            shortcut.setAttribute('data-context-menu-attached', 'true');
            
            shortcut.addEventListener('contextmenu', function(e) {
                console.log('Icon context menu triggered', this); {{-- Отладка --}}
                
                e.preventDefault();
                e.stopPropagation();
                
                currentIcon = this;
                
                {{-- Закрываем меню рабочего стола --}}
                desktopContextMenu.classList.remove('show');
                
                {{-- Показываем меню иконки --}}
                iconContextMenu.style.left = e.pageX + 'px';
                iconContextMenu.style.top = e.pageY + 'px';
                iconContextMenu.classList.add('show');
                
                console.log('Icon context menu shown at', e.pageX, e.pageY); {{-- Отладка --}}
            });
        });
    }
    
    {{-- Инициализируем контекстное меню для существующих иконок --}}
    attachIconContextMenu();
    
    {{-- Наблюдаем за добавлением новых иконок --}}
    var observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1 && (node.classList.contains('shortcut') || node.querySelector('.shortcut'))) {
                        attachIconContextMenu();
                    }
                });
            }
        });
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
    
    {{-- Обработка действий контекстного меню рабочего стола --}}
    desktopContextMenu.addEventListener('click', function(e) {
        var item = e.target.closest('.context-menu-item');
        if (!item) return;
        
        var action = item.getAttribute('data-action');
        
        if (action === 'arrange-icons') {
            arrangeIconsToGrid();
        }
        
        this.classList.remove('show');
    });
    
    {{-- Обработка действий контекстного меню иконки --}}
    iconContextMenu.addEventListener('click', function(e) {
        var item = e.target.closest('.context-menu-item');
        if (!item) return;
        
        var action = item.getAttribute('data-action');
        
        if (action === 'open' && currentIcon) {
            var functionName = currentIcon.getAttribute('data-function');
            if (functionName && typeof window[functionName] === 'function') {
                window[functionName]();
            }
        } else if (action === 'rename' && currentIcon) {
            renameIcon(currentIcon);
        } else if (action === 'delete' && currentIcon) {
            deleteIcon(currentIcon);
        }
        
        this.classList.remove('show');
        currentIcon = null;
    });
    
    {{-- Функция автоматического размещения иконок по сетке --}}
    function arrangeIconsToGrid() {
        {{-- Устанавливаем callback для обработки ответа --}}
        window.currentModalCallback = function(action) {
            if (action === 'arrange') {
                    var shortcuts = document.querySelectorAll('.shortcut');
                    var col = 0;
                    var row = 0;
                    var maxRows = Math.floor((window.innerHeight - 100) / gridSize);
                    
                    shortcuts.forEach(function(shortcut, index) {
                        var left = col * gridSize + 20;
                        var top = row * gridSize + 20;
                        
                        {{-- Анимация перемещения --}}
                        shortcut.style.transition = 'left 0.5s ease, top 0.5s ease';
                        shortcut.style.left = left + 'px';
                        shortcut.style.top = top + 'px';
                        
                        {{-- Сохраняем новую позицию в БД --}}
                        var shortcutId = shortcut.getAttribute('data-shortcut-id');
                        var originalName = shortcut.getAttribute('data-original-name');
                        if (shortcutId) {
                            saveShortcutPositionFromContextMenu(shortcutId, left, top, originalName);
                        }
                        
                        row++;
                        if (row >= maxRows) {
                            row = 0;
                            col++;
                        }
                    });
                    
                    {{-- Убираем анимацию через некоторое время --}}
                    setTimeout(function() {
                        shortcuts.forEach(function(shortcut) {
                            shortcut.style.transition = '';
                        });
                    }, 600);
            }
        };
        
        {{-- Показываем кастомное модальное окно --}}
        showCustomModal({
            title: 'Упорядочить значки',
            message: 'Расположить все ярлыки по сетке? Текущие позиции будут сброшены.',
            icon: 'question',
            buttons: [
                {
                    text: 'Отмена',
                    type: 'secondary',
                    action: 'cancel'
                },
                {
                    text: 'Упорядочить',
                    type: 'primary',
                    action: 'arrange'
                }
            ]
        });
    }
    
    {{-- Функция сохранения позиции из контекстного меню (доступна глобально) --}}
    function saveShortcutPositionFromContextMenu(shortcutId, x, y, originalName) {
        fetch('/admin/desktop-shortcut/save-position', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                shortcut_id: shortcutId,
                position_x: x,
                position_y: y,
                original_name: originalName
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('✓ Позиция ярлыка сохранена (упорядочивание):', shortcutId, '->', x, y);
            } else {
                console.error('✗ Ошибка сохранения позиции:', data.message);
            }
        })
        .catch(error => {
            console.error('✗ Ошибка сохранения позиции ярлыка:', error);
        });
    }
    
    {{-- Функция удаления иконки --}}
    function deleteIcon(icon) {
        {{-- Получаем название приложения для отображения в диалоге --}}
        var iconText = icon.querySelector('.shortcut-text');
        var appName = iconText ? iconText.textContent : 'этот ярлык';
        
        {{-- Устанавливаем callback для обработки ответа --}}
        window.currentModalCallback = function(action) {
            if (action === 'delete') {
                {{-- Удаляем сохраненную позицию --}}
                var shortcutId = icon.getAttribute('data-shortcut-id');
                if (shortcutId) {
                    localStorage.removeItem('shortcut-pos-' + shortcutId);
                }
                
                {{-- Анимация исчезновения --}}
                icon.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                icon.style.opacity = '0';
                icon.style.transform = 'scale(0.8)';
                
                {{-- Удаляем элемент из DOM после анимации --}}
                setTimeout(function() {
                    icon.remove();
                }, 300);
            }
        };
        
        {{-- Показываем кастомное модальное окно --}}
        showCustomModal({
            title: 'Удаление ярлыка',
            message: 'Вы уверены, что хотите удалить ярлык "' + appName + '"?',
            icon: 'warning',
            buttons: [
                {
                    text: 'Отмена',
                    type: 'secondary',
                    action: 'cancel'
                },
                {
                    text: 'Удалить',
                    type: 'primary',
                    action: 'delete'
                }
            ]
        });
    }
    
    {{-- Функция переименования иконки --}}
    function renameIcon(icon) {
        var iconTextElement = icon.querySelector('.shortcut-text');
        if (!iconTextElement) return;
        
        var currentName = iconTextElement.textContent.trim();
        var shortcutId = icon.getAttribute('data-shortcut-id');
        var originalName = icon.getAttribute('data-original-name') || currentName;
        
        {{-- Создаем поле ввода для редактирования --}}
        var input = document.createElement('input');
        input.type = 'text';
        input.value = currentName;
        input.style.cssText = 'width: 100%; background: white; color: black; border: 1px solid #0078d7; padding: 2px 4px; font-size: 11px; text-align: center; font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;';
        
        {{-- Заменяем текст на поле ввода --}}
        iconTextElement.style.display = 'none';
        iconTextElement.parentNode.appendChild(input);
        input.focus();
        input.select();
        
        {{-- Функция завершения редактирования --}}
        function finishRename(save) {
            var newName = input.value.trim();
            
            if (save && newName && newName !== currentName) {
                {{-- Обновляем текст --}}
                iconTextElement.textContent = newName;
                
                {{-- Сохраняем новое имя в БД через API --}}
                if (shortcutId) {
                    fetch('/admin/desktop-shortcut/save', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            shortcut_id: shortcutId,
                            custom_name: newName,
                            original_name: originalName
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('✓ Название ярлыка сохранено:', shortcutId, '->', newName);
                        } else {
                            console.error('✗ Ошибка сохранения:', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('✗ Ошибка сохранения названия ярлыка:', error);
                    });
                }
            }
            
            {{-- Удаляем поле ввода и показываем текст --}}
            input.remove();
            iconTextElement.style.display = '';
        }
        
        {{-- Обработчики событий --}}
        input.addEventListener('blur', function() {
            finishRename(true);
        });
        
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                finishRename(true);
            } else if (e.key === 'Escape') {
                e.preventDefault();
                finishRename(false);
            }
        });
    }
}
</script>