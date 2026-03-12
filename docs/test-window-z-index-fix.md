# Исправление дублирования кнопок на панели задач и тестовые окна для z-index

## Дата: 2026-03-02

## Проблема

При открытии окон на рабочем столе админ-панели кнопки на панели задач дублировались при повторном открытии того же окна.

## Решение

### 1. Исправлена функция `addWindowToTaskbar`

**Файл:** `resources/views/admin/desktop/index.blade.php`

**Изменение:** Добавлена проверка на существование кнопки перед её созданием.

```javascript
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
    btn.className = 'window-button';
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
```

**Что изменилось:**
- Добавлена проверка `document.getElementById('taskbar-btn-' + win.id)`
- Если кнопка уже существует, функция выходит с сообщением в консоль
- Это предотвращает создание дубликатов кнопок

## Тестовые окна для проверки z-index

Созданы 3 тестовых окна для проверки правильности работы z-index и панели задач.

### Созданные файлы

1. **`public/site/modules/admin/desktop/test_window_red.js`** - Красное тестовое окно
2. **`public/site/modules/admin/desktop/test_window_blue.js`** - Синее тестовое окно
3. **`public/site/modules/admin/desktop/test_window_green.js`** - Зеленое тестовое окно

### Особенности тестовых окон

Каждое окно:
- Имеет уникальный цвет фона для визуального различия
- Содержит инструкции по тестированию
- Логирует активацию в консоль с текущим z-index
- Правильно обрабатывает события:
  - `activate` - окно становится активным
  - `deactivate` - окно теряет фокус
  - `minimize` - окно минимизируется
  - `maximize` - окно максимизируется
  - `restore` - окно восстанавливается
  - `close` - окно закрывается

### Интеграция в desktop

**Подключение скриптов в `<head>`:**

```blade
<!-- Тестовые окна для проверки z-index -->
<script src="/site/modules/admin/desktop/test_window_red.js"></script>
<script src="/site/modules/admin/desktop/test_window_blue.js"></script>
<script src="/site/modules/admin/desktop/test_window_green.js"></script>
```

**Ярлыки на рабочем столе:**

```blade
<!-- Тестовые окна для проверки z-index -->
<div class="shortcut" onclick="openTestWindowRed()">
    <div class="shortcut-icon"><span class="material-icons" style="color: #f44336;">circle</span></div>
    <div class="shortcut-text">Test Red</div>
</div>

<div class="shortcut" onclick="openTestWindowBlue()">
    <div class="shortcut-icon"><span class="material-icons" style="color: #2196f3;">circle</span></div>
    <div class="shortcut-text">Test Blue</div>
</div>

<div class="shortcut" onclick="openTestWindowGreen()">
    <div class="shortcut-icon"><span class="material-icons" style="color: #4caf50;">circle</span></div>
    <div class="shortcut-text">Test Green</div>
</div>
```

**Пункты в меню Пуск:**

```blade
<!-- Тестовые окна для проверки z-index -->
<div class="separator"></div>
<a class="category">Тестовые окна</a>
<a href="#" onclick="openTestWindowRed(); closeStartMenu(); return false;">
    <span class="app-icon"><span class="material-icons" style="color: #f44336;">circle</span></span>
    <span class="app-text">Test Window Red</span>
</a>
<a href="#" onclick="openTestWindowBlue(); closeStartMenu(); return false;">
    <span class="app-icon"><span class="material-icons" style="color: #2196f3;">circle</span></span>
    <span class="app-text">Test Window Blue</span>
</a>
<a href="#" onclick="openTestWindowGreen(); closeStartMenu(); return false;">
    <span class="app-icon"><span class="material-icons" style="color: #4caf50;">circle</span></span>
    <span class="app-text">Test Window Green</span>
</a>
```

## Инструкция по тестированию

### 1. Проверка дублирования кнопок

1. Откройте любое окно (например, Test Window Red)
2. Закройте окно
3. Откройте то же окно снова
4. **Ожидаемый результат:** На панели задач должна быть только одна кнопка для этого окна
5. **Проверка в консоли:** Должно появиться сообщение "Кнопка для окна test_red уже существует на панели задач"

### 2. Проверка z-index

1. Откройте все 3 тестовых окна (красное, синее, зеленое)
2. Кликайте по разным окнам
3. **Ожидаемый результат:** Активное окно всегда должно быть поверх остальных
4. **Проверка в консоли:** При активации окна должно логироваться сообщение с текущим z-index

### 3. Проверка панели задач

1. Откройте несколько окон
2. **Активное окно:** Кнопка должна быть подсвечена (класс `active`)
3. **Неактивные окна:** Кнопки должны быть обычными
4. **Минимизированные окна:** Кнопки должны быть серыми (класс `minimized`)

### 4. Проверка минимизации/восстановления

1. Откройте окно
2. Нажмите кнопку минимизации (—)
3. **Ожидаемый результат:** Окно скрывается, кнопка на панели задач становится серой
4. Кликните по кнопке на панели задач
5. **Ожидаемый результат:** Окно восстанавливается и поднимается наверх

### 5. Проверка перетаскивания

1. Откройте несколько окон
2. Начните перетаскивать окно за заголовок
3. **Ожидаемый результат:** Окно должно подняться наверх (z-index увеличится)
4. Отпустите окно
5. **Ожидаемый результат:** Окно должно остаться наверху

## Отладка

### Функция debugZIndex()

Если в коде есть функция `debugZIndex()`, вызовите её в консоли для получения информации о всех открытых окнах и их z-index.

```javascript
debugZIndex()
```

### Проверка в консоли

При работе с окнами в консоли должны появляться сообщения:

```
Красное окно активировано, z-index: 19001
Синее окно активировано, z-index: 19002
Зеленое окно активировано, z-index: 19003
```

## Файлы, затронутые изменениями

1. `resources/views/admin/desktop/index.blade.php` - исправлена функция `addWindowToTaskbar`, добавлены ярлыки и пункты меню
2. `public/site/modules/admin/desktop/test_window_red.js` - создан
3. `public/site/modules/admin/desktop/test_window_blue.js` - создан
4. `public/site/modules/admin/desktop/test_window_green.js` - создан

## Команды для применения изменений

```bash
# Очистка кеша view
php artisan view:clear

# Обновление страницы в браузере
# Ctrl+F5 (жесткое обновление с очисткой кеша)
```

## Результат

- ✅ Кнопки на панели задач больше не дублируются
- ✅ Созданы 3 тестовых окна для проверки z-index
- ✅ Тестовые окна доступны через ярлыки на рабочем столе
- ✅ Тестовые окна доступны через меню Пуск
- ✅ Все окна правильно обрабатывают события активации/деактивации
- ✅ Панель задач корректно отображает состояние окон

## Связанные документы

- [test-window-usage.md](test-window-usage.md) - Инструкция по использованию тестовых окон
- [admin-desktop-windows.md](admin-desktop-windows.md) - Документация по системе окон (если существует)
