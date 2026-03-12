# Стили кнопок панели задач в стиле Windows 10

## Дата: 2026-03-02

## Проблема

Кнопки на панели задач не отображали правильное состояние окон в стиле Windows 10:
- Не было различия между открытым неактивным и активным окном
- Минимизированные окна выглядели почти как обычные
- Отсутствовала характерная линия подчёркивания снизу

## Решение

Реализованы стили кнопок панели задач в стиле Windows 10 с тремя состояниями:

### 1. Открытое окно (неактивное) - класс `.open`
- Тонкая полупрозрачная линия подчёркивания снизу
- `border-bottom-color: rgba(255, 255, 255, 0.4)`

### 2. Активное окно - класс `.active`
- Подсвеченный фон + яркая линия подчёркивания
- `background: rgba(255, 255, 255, 0.15)`
- `border-bottom-color: rgba(255, 255, 255, 0.9)`

### 3. Минимизированное окно - класс `.minimized`
- Тусклая линия + уменьшенная прозрачность
- `opacity: 0.6`
- `border-bottom-color: rgba(255, 255, 255, 0.3)`

### 4. Hover - для всех состояний
- Лёгкая подсветка фона при наведении
- `background: rgba(255, 255, 255, 0.1)`

## Изменённые файлы

### 1. `resources/views/admin/desktop/index.blade.php`

#### CSS стили (строки ~201-229)

**БЫЛО:**
```css
.window-button {
    border-bottom: 2px solid transparent;
    transition: all 0.1s ease;
}

.window-button.active {
    background: rgba(255, 255, 255, 0.05);
    border-bottom-color: rgba(245, 245, 245, 0.651);
}

.window-button.minimized {
    opacity: 0.7;
}
```

**СТАЛО:**
```css
.window-button {
    border-bottom: 3px solid transparent;
    transition: all 0.15s ease;
    position: relative;
}

/* Hover - лёгкая подсветка */
.window-button:hover {
    background: rgba(255, 255, 255, 0.1);
}

/* Открытое окно (неактивное) - линия подчёркивания */
.window-button.open {
    border-bottom-color: rgba(255, 255, 255, 0.4);
}

/* Активное окно - подсвечено + яркая линия */
.window-button.active {
    background: rgba(255, 255, 255, 0.15);
    border-bottom-color: rgba(255, 255, 255, 0.9);
}

/* Минимизированное окно - линия + прозрачность */
.window-button.minimized {
    opacity: 0.6;
    border-bottom-color: rgba(255, 255, 255, 0.3);
}

/* Минимизированное + hover */
.window-button.minimized:hover {
    opacity: 0.8;
}
```

#### Функция `updateTaskbarButton` (строки ~1275-1290)

**БЫЛО:**
```javascript
function updateTaskbarButton(winId, isActive, isMinimized) {
    var btn = document.getElementById('taskbar-btn-' + winId);
    if (!btn) return;
    
    if (isActive) {
        btn.classList.add('active');
    } else {
        btn.classList.remove('active');
    }
    
    if (isMinimized) {
        btn.classList.add('minimized');
    } else {
        btn.classList.remove('minimized');
    }
}
```

**СТАЛО:**
```javascript
function updateTaskbarButton(winId, isActive, isMinimized) {
    var btn = document.getElementById('taskbar-btn-' + winId);
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
```

#### Функция `addWindowToTaskbar` (строки ~1239-1260)

**БЫЛО:**
```javascript
var btn = document.createElement('button');
btn.className = 'window-button';
```

**СТАЛО:**
```javascript
var btn = document.createElement('button');
btn.className = 'window-button open'; // Добавляем класс open при создании
```

## Поведение

### Жизненный цикл кнопки на панели задач:

1. **Создание окна** → кнопка получает класс `.open` (тонкая линия)
2. **Активация окна** → класс меняется на `.active` (подсветка + яркая линия)
3. **Деактивация окна** → класс меняется на `.open` (тонкая линия)
4. **Минимизация окна** → класс меняется на `.minimized` (тусклая линия + прозрачность)
5. **Восстановление окна** → класс меняется на `.active` (подсветка + яркая линия)

### Обработчики событий в тестовых окнах:

Все тестовые окна (`test_window_red.js`, `test_window_blue.js`, `test_window_green.js`) уже имеют правильные обработчики:

```javascript
listeners: {
    activate: function() {
        updateTaskbarButton('test_red', true, false); // active
    },
    deactivate: function() {
        updateTaskbarButton('test_red', false, false); // open
    },
    minimize: function() {
        this.hide();
        updateTaskbarButton('test_red', false, true); // minimized
    },
    restore: function() {
        updateTaskbarButton('test_red', true, false); // active
    }
}
```

## Проверка

### Шаги для тестирования:

1. Откройте страницу `http://sfera/admin` (Ctrl+F5 для полной перезагрузки)
2. Откройте тестовое окно (красное, синее или зеленое)
3. **Проверка открытого окна:** Должна появиться тонкая белая линия снизу
4. **Проверка деактивации:** Кликните по рабочему столу → линия станет тусклее
5. **Проверка активации:** Кликните по окну → линия станет яркой + фон подсветится
6. **Проверка минимизации:** Нажмите кнопку минимизации → линия станет ещё тусклее + прозрачность
7. **Проверка hover:** Наведите на кнопку → должна появиться лёгкая подсветка

### Ожидаемое поведение (как в Windows 10):

| Состояние | Фон | Линия снизу | Прозрачность |
|-----------|-----|-------------|--------------|
| Открытое неактивное | Прозрачный | Тонкая белая (40%) | 100% |
| Активное | Подсвеченный (15%) | Яркая белая (90%) | 100% |
| Минимизированное | Прозрачный | Тусклая белая (30%) | 60% |
| Hover | Лёгкая подсветка (10%) | Без изменений | Без изменений |

## Технические детали

### Толщина линии подчёркивания
- Увеличена с `2px` до `3px` для лучшей видимости

### Время анимации
- Увеличено с `0.1s` до `0.15s` для более плавного перехода

### Логика переключения классов
- Используется `classList.remove('active', 'minimized', 'open')` для очистки всех состояний
- Затем добавляется только один класс в зависимости от состояния
- Это предотвращает конфликты между классами

## Связанные файлы

- `resources/views/admin/desktop/index.blade.php` - основной файл с CSS и JavaScript
- `public/site/modules/admin/desktop/test_window_red.js` - тестовое красное окно
- `public/site/modules/admin/desktop/test_window_blue.js` - тестовое синее окно
- `public/site/modules/admin/desktop/test_window_green.js` - тестовое зеленое окно

## Команды для очистки кеша

```bash
& "C:\OS\modules\PHP-8.5\php.exe" artisan view:clear
```

## История изменений

- 2026-03-02: Реализованы стили кнопок панели задач в стиле Windows 10
- Добавлены три состояния: open, active, minimized
- Обновлена логика переключения классов в `updateTaskbarButton`
- Добавлен класс `open` при создании кнопки в `addWindowToTaskbar`
