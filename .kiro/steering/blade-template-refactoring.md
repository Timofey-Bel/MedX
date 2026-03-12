---
inclusion: auto
fileMatchPattern: '**/*.blade.php'
---

# Структура Blade-шаблонов с JavaScript и CSS

## Критическое правило

**НИКОГДА не пишите CSS и JavaScript непосредственно в Blade-шаблонах!**

Всегда используйте модульную структуру с отдельными файлами для каждой функции.

## Правильная структура проекта

### Для нового модуля/страницы

```
resources/views/[module]/
├── index.blade.php          # Главный шаблон (только HTML и @include)
└── js/
    ├── _globals.blade.php   # Глобальные переменные
    ├── _utils.blade.php     # Утилиты
    ├── _init.blade.php      # Инициализация
    ├── _feature1.blade.php  # Функции для фичи 1
    ├── _feature2.blade.php  # Функции для фичи 2
    └── ...

public/css/
└── [module].css             # Все стили модуля
```

## Главный шаблон (index.blade.php)

Главный файл должен содержать ТОЛЬКО:
- HTML разметку
- Blade директивы (@if, @foreach, @include)
- Подключение CSS и JS модулей

### ✅ ПРАВИЛЬНО

```php
<!DOCTYPE html>
<html>
<head>
    <title>Module Name</title>
    
    <!-- Подключение CSS -->
    <link rel="stylesheet" href="{{ asset('css/module.css') }}">
</head>
<body>
    <!-- HTML разметка -->
    <div class="container">
        @foreach($items as $item)
        <div class="item">{{ $item->name }}</div>
        @endforeach
    </div>
    
    <!-- Подключение JavaScript модулей -->
    <script>
    
    {{-- Global Variables --}}
    @include('module.js._globals')
    
    {{-- Utilities --}}
    @include('module.js._utils')
    
    {{-- Initialization --}}
    @include('module.js._init')
    
    {{-- Feature Modules --}}
    @include('module.js._feature1')
    @include('module.js._feature2')
    
    </script>
</body>
</html>
```

### ❌ НЕПРАВИЛЬНО

```php
<!DOCTYPE html>
<html>
<head>
    <title>Module Name</title>
    
    <!-- НЕ ДЕЛАЙТЕ ТАК! -->
    <style>
        .container { padding: 20px; }
        .item { margin: 10px; }
    </style>
</head>
<body>
    <div class="container">...</div>
    
    <!-- НЕ ДЕЛАЙТЕ ТАК! -->
    <script>
        function openWindow() {
            // 100 строк кода...
        }
        
        function closeWindow() {
            // 50 строк кода...
        }
        
        // Еще 500 строк...
    </script>
</body>
</html>
```

## Структура JavaScript модулей

### 1. Globals (_globals.blade.php)

Глобальные переменные, используемые во всех модулях:

```php
<script>
// ============================================
// Global Variables
// ============================================

// Объект с правами доступа
var moduleAccess = {
@if(isset($moduleAccess['feature1']) && $moduleAccess['feature1'])
    feature1: true,
@else
    feature1: false,
@endif
@foreach($features as $feature)
    {{ $feature['id'] }}: 
@if(isset($moduleAccess[$feature['id']]) && $moduleAccess[$feature['id']])
true
@else
false
@endif
    ,
@endforeach
};

// Хранилище открытых окон
var openWindows = {};

// Ссылки на специальные окна
var profileWindow = null;
var settingsWindow = null;

</script>
```

### 2. Utils (_utils.blade.php)

Утилиты и вспомогательные функции:

```php
<script>
// ============================================
// Utility Functions
// ============================================

/**
 * Получить максимальный z-index среди окон
 * 
 * @return {number} Максимальный z-index
 */
function getMaxZIndex() {
    var maxZ = 1000;
    for (var key in openWindows) {
        if (openWindows[key] && openWindows[key].getEl()) {
            var z = parseInt(openWindows[key].getEl().getStyle('z-index'));
            if (z > maxZ) maxZ = z;
        }
    }
    return maxZ;
}

/**
 * Форматировать дату
 * 
 * @param {Date} date - Дата для форматирования
 * @return {string} Отформатированная дата
 */
function formatDate(date) {
    return date.toLocaleDateString('ru-RU');
}

</script>
```

### 3. Initialization (_init.blade.php)

Код инициализации (DOMContentLoaded, настройка библиотек):

```php
<script>
// ============================================
// Initialization
// ============================================

// Настройка ExtJS (если используется)
Ext.onReady(function() {
    Ext.QuickTips.init();
});

// Инициализация при загрузке DOM
document.addEventListener('DOMContentLoaded', function() {
    // Инициализация кнопок
    var buttons = document.querySelectorAll('.action-button');
    buttons.forEach(function(btn) {
        btn.addEventListener('click', handleButtonClick);
    });
    
    // Запуск часов
    updateClock();
    setInterval(updateClock, 1000);
});

</script>
```

### 4. Feature Module (_feature.blade.php)

Один файл = одна фича (группа связанных функций):

```php
<script>
// ============================================
// Feature Name - Краткое описание фичи
// ============================================

/**
 * Открыть окно фичи
 * Создает и отображает окно с функционалом фичи
 */
function openFeature() {
    // Проверяем права доступа
    if (!moduleAccess.feature) {
        Ext.Msg.alert('Ошибка', 'Нет доступа');
        return;
    }
    
    // Проверяем, не открыто ли уже
    if (openWindows['feature']) {
        openWindows['feature'].show();
        openWindows['feature'].toFront();
        return;
    }
    
    // Создаем окно
    var win = Ext.create('Ext.window.Window', {
        title: 'Feature Name',
        width: 800,
        height: 600,
        layout: 'fit',
        items: [/* ... */],
        listeners: {
            close: function() {
                delete openWindows['feature'];
            }
        }
    });
    
    openWindows['feature'] = win;
    win.show();
}

/**
 * Показать форму добавления/редактирования
 * 
 * @param {Object|null} record - Запись для редактирования (null для создания)
 * @param {Ext.data.Store} store - Store для обновления после сохранения
 */
function showFeatureForm(record, store) {
    var isEdit = record !== null;
    
    var formWindow = Ext.create('Ext.window.Window', {
        title: isEdit ? 'Редактировать' : 'Добавить',
        width: 500,
        modal: true,
        items: [/* ... */]
    });
    
    formWindow.show();
}

/**
 * Удалить запись
 * 
 * @param {Object} record - Запись для удаления
 * @param {Ext.data.Store} store - Store для обновления
 */
function deleteFeature(record, store) {
    Ext.Msg.confirm('Удаление', 'Вы уверены?', function(btn) {
        if (btn === 'yes') {
            Ext.Ajax.request({
                url: '/api/feature/delete',
                params: { id: record.get('id') },
                success: function() {
                    store.load();
                }
            });
        }
    });
}

</script>
```

### 5. IIFE Module (_module.blade.php)

Для самовыполняющихся модулей (Drag & Drop, Context Menu и т.д.):

```php
<script>
// ============================================
// Module Name - Описание модуля
// ============================================

(function() {
    // Приватные переменные
    var draggedElement = null;
    var dropZones = [];
    
    /**
     * Приватная функция инициализации
     */
    function init() {
        var elements = document.querySelectorAll('.draggable');
        elements.forEach(function(el) {
            el.addEventListener('dragstart', handleDragStart);
            el.addEventListener('dragend', handleDragEnd);
        });
    }
    
    /**
     * Обработчик начала перетаскивания
     */
    function handleDragStart(e) {
        draggedElement = this;
        e.dataTransfer.effectAllowed = 'move';
    }
    
    /**
     * Обработчик окончания перетаскивания
     */
    function handleDragEnd(e) {
        draggedElement = null;
    }
    
    // Автоматическая инициализация
    document.addEventListener('DOMContentLoaded', init);
})();

</script>
```

## Структура CSS файлов

Все стили в одном файле `public/css/[module].css`:

```css
/* ============================================
   Module Name Styles
   ============================================ */

/* Layout */
.container {
    padding: 20px;
    background: #fff;
}

/* Components */
.item {
    margin: 10px;
    padding: 15px;
    border: 1px solid #ddd;
}

.item:hover {
    background: #f5f5f5;
}

/* Buttons */
.action-button {
    padding: 10px 20px;
    background: #007bff;
    color: white;
    border: none;
    cursor: pointer;
}

.action-button:hover {
    background: #0056b3;
}
```

## Порядок загрузки модулей

**КРИТИЧНО**: Модули должны загружаться в правильном порядке!

```php
<script>

{{-- 1. Globals - ВСЕГДА ПЕРВЫМИ --}}
@include('module.js._globals')

{{-- 2. Utils - вспомогательные функции --}}
@include('module.js._utils')

{{-- 3. Initialization - настройка и инициализация --}}
@include('module.js._init')

{{-- 4. UI Components - компоненты интерфейса --}}
@include('module.js._clock')
@include('module.js._menu')

{{-- 5. Feature Modules - функции фич --}}
@include('module.js._feature1')
@include('module.js._feature2')

{{-- 6. IIFE Modules - самовыполняющиеся (ВСЕГДА ПОСЛЕДНИМИ) --}}
@include('module.js._drag-drop')
@include('module.js._context-menu')

</script>
```

## Правила именования

### Файлы модулей

- Начинаются с подчеркивания: `_module.blade.php`
- Используют kebab-case: `_user-menu.blade.php`
- Описательные имена: `_banners.blade.php`, `_permissions.blade.php`

### Функции

- camelCase: `openWindow()`, `showUserForm()`
- Глаголы в начале: `get`, `show`, `open`, `close`, `delete`, `update`
- Описательные: `showBannerPreview()`, `deleteUser()`

### Переменные

- camelCase: `openWindows`, `moduleAccess`
- Описательные: `profileWindow`, `bannerPreviewWindow`

## Шаблон для новой страницы

При создании новой страницы используйте этот шаблон:

```bash
# 1. Создать структуру
mkdir -p resources/views/mymodule/js
touch resources/views/mymodule/index.blade.php
touch resources/views/mymodule/js/_globals.blade.php
touch resources/views/mymodule/js/_utils.blade.php
touch resources/views/mymodule/js/_init.blade.php
touch public/css/mymodule.css

# 2. Заполнить index.blade.php по шаблону выше
# 3. Добавить функции в соответствующие модули
# 4. Добавить стили в CSS файл
```

## Преимущества такого подхода

1. **Читаемость** - легко найти нужную функцию
2. **Поддерживаемость** - изменения в одном месте
3. **Переиспользование** - модули можно использовать в других проектах
4. **Тестирование** - проще тестировать отдельные модули
5. **Производительность** - браузер кэширует CSS отдельно
6. **Командная работа** - нет конфликтов при работе над разными фичами

## Связанные документы

- [blade-directives-formatting.md](blade-directives-formatting.md) - Правила форматирования Blade
- [blade-scripts-styles.md](blade-scripts-styles.md) - Подключение скриптов и стилей

## История

- 2026-03-03: Создан документ на основе успешного рефакторинга Admin Desktop
- Определена обязательная структура для всех новых Blade-шаблонов
