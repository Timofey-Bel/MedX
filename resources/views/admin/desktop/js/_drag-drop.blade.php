<script>
{{-- ============================================ --}}
{{-- Drag & Drop IIFE - Перетаскивание иконок рабочего стола --}}
{{-- ============================================ --}}

{{-- ======================================================================== --}}
{{-- Drag & Drop для иконок рабочего стола - Windows 10 Style --}}
{{-- ======================================================================== --}}

{{-- Ждем полной загрузки DOM перед инициализацией drag & drop --}}
document.addEventListener('DOMContentLoaded', function() {
    initializeDragDrop();
});

{{-- Также инициализируем при готовности ExtJS --}}
Ext.onReady(function() {
    {{-- Небольшая задержка для гарантии что все элементы созданы --}}
    setTimeout(function() {
        initializeDragDrop();
    }, 150); {{-- Чуть больше задержка чем у контекстного меню --}}
});

function initializeDragDrop() {
    {{-- Проверяем, не инициализировано ли уже --}}
    if (document.body.hasAttribute('data-dragdrop-initialized')) {
        return;
    }
    document.body.setAttribute('data-dragdrop-initialized', 'true');
    
    {{-- Получаем данные ярлыков из PHP --}}
    var shortcutData = @json($custom_shortcut_names ?? []);
    
    var draggedElement = null;
    var isDragging = false;
    var isActuallyDragging = false; {{-- Флаг реального перетаскивания --}}
    var dragTimer = null; {{-- Таймер для задержки перед перетаскиванием --}}
    var startX = 0; {{-- Начальная позиция мыши X --}}
    var startY = 0; {{-- Начальная позиция мыши Y --}}
    var offsetX = 0;
    var offsetY = 0;
    var dragThreshold = 5; {{-- Минимальное расстояние для начала перетаскивания (в пикселях) --}}
    var dragDelay = 1000; {{-- Задержка 1 секунда (1000 мс) --}}
    
    console.log('Drag & Drop initialized'); {{-- Отладочное сообщение --}}
    
    function initializeShortcuts() {
        var shortcuts = document.querySelectorAll('.shortcut');
        console.log('Initializing drag & drop for shortcuts:', shortcuts.length); {{-- Отладка --}}
        
        shortcuts.forEach(function(shortcut, index) {
            {{-- Проверяем, не инициализирован ли уже --}}
            if (shortcut.hasAttribute('data-dragdrop-attached')) {
                return;
            }
            shortcut.setAttribute('data-dragdrop-attached', 'true');
            
            {{-- Получаем ID ярлыка --}}
            var shortcutId = shortcut.getAttribute('data-shortcut-id') || 'shortcut-' + index;
            shortcut.setAttribute('data-shortcut-id', shortcutId);
            
            {{-- Получаем оригинальное название для сохранения --}}
            var iconTextElement = shortcut.querySelector('.shortcut-text');
            var originalName = iconTextElement ? iconTextElement.textContent.trim() : '';
            shortcut.setAttribute('data-original-name', originalName);
            
            {{-- Двойной клик для запуска приложения --}}
            shortcut.addEventListener('dblclick', function(e) {
                e.preventDefault();
                var functionName = this.getAttribute('data-function');
                if (functionName && typeof window[functionName] === 'function') {
                    window[functionName]();
                }
            });
            
            {{-- Начало перетаскивания --}}
            shortcut.addEventListener('mousedown', function(e) {
                {{-- Обрабатываем только левую кнопку мыши --}}
                if (e.button !== 0) return;
                
                {{-- Предотвращаем выделение текста --}}
                e.preventDefault();
                
                {{-- Снимаем выделение со всех иконок --}}
                var allShortcuts = document.querySelectorAll('.shortcut');
                allShortcuts.forEach(function(s) { 
                    s.classList.remove('selected'); 
                });
                
                {{-- Выделяем текущую --}}
                this.classList.add('selected');
                
                {{-- Подготовка к перетаскиванию --}}
                draggedElement = this;
                var element = this;
                
                var rect = this.getBoundingClientRect();
                var desktop = document.querySelector('.desktop');
                if (!desktop) return;
                
                var desktopRect = desktop.getBoundingClientRect();
                
                offsetX = e.clientX - rect.left;
                offsetY = e.clientY - rect.top;
                
                {{-- Сохраняем начальную позицию мыши для проверки порога --}}
                startX = e.clientX;
                startY = e.clientY;
                isActuallyDragging = false;
                
                {{-- Запускаем таймер на 1 секунду --}}
                dragTimer = setTimeout(function() {
                    {{-- Разрешаем режим перетаскивания через 1 секунду --}}
                    isDragging = true;
                    element.classList.add('dragging');
                    element.style.cursor = 'move';
                }, dragDelay);
            });
            
            {{-- Применяем данные из БД если они есть --}}
            if (shortcutData[shortcutId]) {
                var data = shortcutData[shortcutId];
                
                {{-- Применяем пользовательское название если оно есть --}}
                if (data.custom_name && iconTextElement) {
                    iconTextElement.textContent = data.custom_name;
                    console.log('✓ Применено пользовательское название:', shortcutId, '->', data.custom_name);
                }
                
                {{-- Применяем сохраненную позицию если она есть --}}
                if (data.position_x !== null && data.position_y !== null) {
                    shortcut.style.position = 'absolute';
                    shortcut.style.left = data.position_x + 'px';
                    shortcut.style.top = data.position_y + 'px';
                    console.log('✓ Применена сохраненная позиция:', shortcutId, '->', data.position_x, data.position_y);
                    return; {{-- Пропускаем установку позиции по умолчанию --}}
                }
            }
            
            {{-- Если нет сохраненной позиции в БД, проверяем localStorage (для обратной совместимости) --}}
            var savedPos = localStorage.getItem('shortcut-pos-' + shortcutId);
            
            if (savedPos) {
                var pos = JSON.parse(savedPos);
                shortcut.style.position = 'absolute';
                shortcut.style.left = pos.left + 'px';
                shortcut.style.top = pos.top + 'px';
                
                {{-- Мигрируем позицию из localStorage в БД --}}
                saveShortcutPosition(shortcutId, pos.left, pos.top, originalName);
                localStorage.removeItem('shortcut-pos-' + shortcutId);
            } else {
                {{-- Устанавливаем начальную позицию в центре рабочего стола --}}
                var desktop = document.querySelector('.desktop');
                if (desktop) {
                    var desktopRect = desktop.getBoundingClientRect();
                    var centerX = (desktopRect.width / 2) - 50; {{-- 50 - половина ширины иконки --}}
                    var centerY = (desktopRect.height / 2) - 50; {{-- 50 - половина высоты иконки --}}
                    
                    shortcut.style.position = 'absolute';
                    shortcut.style.left = centerX + 'px';
                    shortcut.style.top = centerY + 'px';
                }
            }
        });
    }
    
    {{-- Инициализируем существующие ярлыки --}}
    initializeShortcuts();
    
    {{-- Наблюдаем за добавлением новых ярлыков --}}
    var observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1 && (node.classList.contains('shortcut') || node.querySelector('.shortcut'))) {
                        initializeShortcuts();
                    }
                });
            }
        });
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
    
    {{-- Перемещение мыши - обработка движения и проверка порогов --}}
    document.addEventListener('mousemove', function(e) {
        {{-- Если мышь сдвинулась на заданное расстояние - отменяем таймер --}}
        if (dragTimer && !isDragging && draggedElement) {
            var deltaX = Math.abs(e.clientX - startX);
            var deltaY = Math.abs(e.clientY - startY);
            
            if (deltaX > dragThreshold || deltaY > dragThreshold) {
                {{-- Мышь сдвинулась - отменяем таймер и разрешаем перетаскивание сразу --}}
                clearTimeout(dragTimer);
                dragTimer = null;
                isDragging = true;
                draggedElement.classList.add('dragging');
                draggedElement.style.cursor = 'move';
            }
        }
        
        if (!isDragging || !draggedElement) return;
        
        {{-- Проверяем, перетаскивает ли мышь на минимальное расстояние --}}
        if (!isActuallyDragging) {
            var deltaX = Math.abs(e.clientX - startX);
            var deltaY = Math.abs(e.clientY - startY);
            
            if (deltaX < dragThreshold && deltaY < dragThreshold) {
                {{-- Если еще не преодолели минимальное расстояние - не двигаем иконку --}}
                return;
            }
            
            {{-- Если преодолели расстояние - включаем реальное перетаскивание --}}
            isActuallyDragging = true;
        }
        
        var desktop = document.querySelector('.desktop');
        if (!desktop) return;
        
        var desktopRect = desktop.getBoundingClientRect();
        
        {{-- Вычисляем новую позицию --}}
        var newLeft = e.clientX - desktopRect.left - offsetX;
        var newTop = e.clientY - desktopRect.top - offsetY;
        
        {{-- Ограничиваем движение границами экрана --}}
        newLeft = Math.max(0, Math.min(newLeft, desktopRect.width - 100));
        newTop = Math.max(0, Math.min(newTop, desktopRect.height - 100));
        
        {{-- Применяем позицию сразу (без задержки!) --}}
        draggedElement.style.left = newLeft + 'px';
        draggedElement.style.top = newTop + 'px';
    });
    
    {{-- Отпускание кнопки мыши - завершение перетаскивания --}}
    document.addEventListener('mouseup', function(e) {
        {{-- Отменяем таймер если он еще активен --}}
        if (dragTimer) {
            clearTimeout(dragTimer);
            dragTimer = null;
        }
        
        if (!isDragging || !draggedElement) return;
        
        isDragging = false;
        isActuallyDragging = false; {{-- Сбрасываем флаг реального перетаскивания --}}
        draggedElement.classList.remove('dragging');
        draggedElement.classList.remove('selected');
        draggedElement.style.cursor = '';
        
        {{-- Сохраняем конечную позицию в БД --}}
        var shortcutId = draggedElement.getAttribute('data-shortcut-id');
        var originalName = draggedElement.getAttribute('data-original-name');
        if (shortcutId) {
            var finalLeft = parseInt(draggedElement.style.left);
            var finalTop = parseInt(draggedElement.style.top);
            
            saveShortcutPosition(shortcutId, finalLeft, finalTop, originalName);
        }
        
        draggedElement = null;
    });
    
    {{-- Функция сохранения позиции ярлыка в БД --}}
    function saveShortcutPosition(shortcutId, x, y, originalName) {
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
                console.log('✓ Позиция ярлыка сохранена:', shortcutId, '->', x, y);
            } else {
                console.error('✗ Ошибка сохранения позиции:', data.message);
            }
        })
        .catch(error => {
            console.error('✗ Ошибка сохранения позиции ярлыка:', error);
        });
    }
    
    {{-- Клик по пустому месту рабочего стола - снимаем выделение со всех иконок --}}
    var desktop = document.querySelector('.desktop');
    if (desktop) {
        desktop.addEventListener('mousedown', function(e) {
            {{-- Проверяем, что клик был именно по рабочему столу, а не по иконке --}}
            if (e.target === desktop || e.target.classList.contains('desktop-logo') || 
                e.target.classList.contains('desktop-logo-main') || 
                e.target.classList.contains('desktop-logo-sub')) {
                var shortcuts = document.querySelectorAll('.shortcut');
                shortcuts.forEach(function(s) { 
                    s.classList.remove('selected'); 
                });
            }
        });
    }
}
</script>