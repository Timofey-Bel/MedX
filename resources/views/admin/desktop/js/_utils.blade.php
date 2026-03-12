<script>
// ============================================
// Utility Functions and ExtJS Configuration
// ============================================

// Настройка ZIndexManager для окон - чтобы при перетаскивании окно было поверх всех
Ext.onReady(function() {
    // Увеличиваем базовый z-index для окон
    if (Ext.WindowManager) {
        Ext.WindowManager.zseed = 20000;
    }
    
    // Функция для получения максимального z-index всех окон
    function getMaxWindowZIndex() {
        var maxZIndex = 20000; // Базовое значение
        var allWindows = Ext.ComponentQuery.query('window');
        
        Ext.each(allWindows, function(win) {
            if (win.getEl && win.getEl()) {
                var zIndex = parseInt(win.getEl().getStyle('z-index'), 10);
                if (!isNaN(zIndex) && zIndex > maxZIndex) {
                    maxZIndex = zIndex;
                }
            }
        });
        
        return maxZIndex;
    }
    
    // Переопределяем класс Window - отключаем ghost-элемент при перетаскивании
    Ext.override(Ext.window.Window, {
        initComponent: function() {
            // Отключаем ghost-элемент (прозрачный дубликат) при перетаскивании
            this.ghost = false;
            
            this.callParent(arguments);
            
            // При начале перетаскивания поднимаем окно наверх
            this.on('beforemove', function(win, x, y) {
                win.toFront();
            });
        }
    });
    
    // Функция для отладки - проверка текущего состояния z-index
    window.debugZIndex = function() {
        console.log('=== ОТЛАДКА Z-INDEX ===');
        
        // Проверяем все окна
        var allWindows = Ext.ComponentQuery.query('window');
        console.log('Всего окон:', allWindows.length);
        Ext.each(allWindows, function(w) {
            if (w.getEl()) {
                var winZIndex = w.getEl().getStyle('z-index');
                var isVisible = w.isVisible();
                console.log('Окно:', w.title || w.id, 'z-index:', winZIndex, 'видимо:', isVisible);
            }
        });
        
        // Проверяем прокси-элементы
        var proxies = Ext.query('.x-window-proxy');
        console.log('Прокси-элементов:', proxies ? proxies.length : 0);
        if (proxies && proxies.length > 0) {
            Ext.each(proxies, function(proxy, index) {
                var proxyZIndex = proxy.style ? proxy.style.zIndex : 'не определен';
                console.log('Прокси-элемент #' + index, 'z-index:', proxyZIndex);
            });
        }
        
        console.log('=== КОНЕЦ ОТЛАДКИ ===');
    };
    
    // ============================================
    // Сохранение позиций и размеров окон в БД
    // ============================================
    
    /**
     * Сохранить состояние окна в БД через API
     */
    window.saveWindowState = function(win) {
        if (!win || !win.id) return;
        
        try {
            var state = {
                window_id: win.id,
                x: win.getX(),
                y: win.getY(),
                width: win.getWidth(),
                height: win.getHeight(),
                maximized: win.maximized || false
            };
            
            // Отправляем на сервер
            fetch('/admin/window-state/save', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(state)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('✓ Состояние окна сохранено:', win.id);
                } else {
                    console.error('✗ Ошибка сохранения:', data.message);
                }
            })
            .catch(error => {
                console.error('✗ Ошибка сохранения состояния окна:', error);
            });
        } catch (e) {
            console.error('✗ Ошибка при подготовке данных:', e);
        }
    };
    
    /**
     * Восстановить состояние окна из БД через API
     */
    window.restoreWindowState = function(win, callback) {
        if (!win || !win.id) {
            if (callback) callback(false);
            return;
        }
        
        // Устанавливаем флаг, что идёт восстановление
        win._restoringState = true;
        
        fetch('/admin/window-state/' + win.id, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                var state = data.data;
                
                // Проверяем, что позиция в пределах экрана
                var viewSize = Ext.getBody().getViewSize();
                if (state.x < 0 || state.x > viewSize.width - 100) state.x = 50;
                if (state.y < 0 || state.y > viewSize.height - 100) state.y = 50;
                
                // Проверяем размеры
                if (state.width < 200) state.width = 500;
                if (state.height < 150) state.height = 350;
                if (state.width > viewSize.width) state.width = viewSize.width - 40;
                if (state.height > viewSize.height - 80) state.height = viewSize.height - 80;
                
                // Применяем состояние
                win.setPosition(state.x, state.y);
                win.setSize(state.width, state.height);
                
                if (state.maximized) {
                    win.maximize();
                }
                
                // Снимаем флаг восстановления
                win._restoringState = false;
                
                console.log('✓ Состояние окна восстановлено из БД:', win.id, state);
                if (callback) callback(true);
            } else {
                // Снимаем флаг восстановления
                win._restoringState = false;
                
                console.log('ℹ Сохраненное состояние не найдено для окна:', win.id);
                if (callback) callback(false);
            }
        })
        .catch(error => {
            // Снимаем флаг восстановления
            win._restoringState = false;
            
            console.error('✗ Ошибка восстановления состояния окна:', error);
            if (callback) callback(false);
        });
    };
    
    /**
     * Добавить автосохранение для окна
     */
    window.enableWindowStateSaving = function(win) {
        if (!win || !win.id) return;
        
        // Debounce для сохранения (не чаще раза в секунду)
        var saveTimeout = null;
        var debouncedSave = function() {
            if (saveTimeout) clearTimeout(saveTimeout);
            saveTimeout = setTimeout(function() {
                // Не сохраняем, если идёт восстановление
                if (!win._restoringState) {
                    saveWindowState(win);
                }
            }, 1000);
        };
        
        // Сохраняем при перемещении
        win.on('move', function() {
            if (!this.maximized && !this._restoringState) {
                debouncedSave();
            }
        });
        
        // Сохраняем при изменении размера
        win.on('resize', function() {
            if (!this.maximized && !this._restoringState) {
                debouncedSave();
            }
        });
        
        // Сохраняем при максимизации/восстановлении
        win.on('maximize', function() {
            if (!this._restoringState) {
                saveWindowState(this);
            }
        });
        
        win.on('restore', function() {
            if (!this._restoringState) {
                saveWindowState(this);
            }
        });
        
        console.log('✓ Автосохранение включено для окна:', win.id);
    };
});

</script>
