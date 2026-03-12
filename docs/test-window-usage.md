# Тестовое окно ExtJS - Инструкция по использованию

## Назначение

Тестовое окно создано для проверки корректности работы всех состояний окон ExtJS в админ-панели.

## Расположение файлов

- **JavaScript**: `public/site/modules/admin/desktop/test_window.js`
- **Интеграция**: `resources/views/admin/desktop/index.blade.php`

## Как открыть

1. Перейдите на `http://sfera/admin`
2. Кликните на ярлык **"Test Window"** (иконка bug_report) на рабочем столе
3. Или откройте через меню Пуск → Test Window

## Что проверять

### 1. Minimize (Минимизация)
- Нажмите кнопку минимизации (—)
- **Ожидается**: Окно скрывается, кнопка на панели задач становится серой

### 2. Maximize (Максимизация)
- Нажмите кнопку максимизации (□)
- **Ожидается**: Окно разворачивается на весь экран

### 3. Restore (Восстановление)
- После maximize нажмите кнопку восстановления (⧉)
- **Ожидается**: Окно возвращается к исходному размеру

### 4. Drag (Перетаскивание)
- Начните перетаскивать окно за заголовок
- **Ожидается**: Окно поднимается наверх (z-index увеличивается)

### 5. Z-index (Порядок окон)
- Откройте несколько тестовых окон
- Кликайте по разным окнам
- **Ожидается**: Активное окно всегда поверх остальных

### 6. Taskbar (Панель задач)
- Проверьте, что кнопка на панели задач отражает состояние окна:
  - **Активное окно**: кнопка подсвечена
  - **Неактивное окно**: кнопка обычная
  - **Минимизированное**: кнопка серая

### 7. Close (Закрытие)
- Нажмите кнопку закрытия (×)
- **Ожидается**: Окно закрывается, кнопка удаляется с панели задач

## Отладка

### Проверка z-index в консоли

Откройте консоль браузера (F12) и выполните:

```javascript
debugZIndex()
```

Это покажет список всех открытых окон с их z-index значениями.

### Проверка списка открытых окон

```javascript
console.log(window.openWindows)
```

Покажет объект со всеми открытыми окнами.

### Проверка состояния окна

```javascript
var win = window.openWindows['test_window'];
console.log('Visible:', win.isVisible());
console.log('Hidden:', win.hidden);
console.log('Z-index:', win.getEl().getStyle('z-index'));
```

## Известные проблемы

Если обнаружите проблемы с поведением окон, проверьте:

1. **Окно не поднимается наверх при клике**
   - Проверьте обработчики `activate` в `test_window.js`
   - Проверьте `ZIndexManager` в `index.blade.php`

2. **Кнопка на панели задач не обновляется**
   - Проверьте функции `updateTaskbarButton()` и `removeWindowFromTaskbar()`
   - Убедитесь, что ID окна правильный

3. **Минимизация не работает**
   - Проверьте обработчик `minimize` в listeners
   - Убедитесь, что `this.hide()` вызывается

## Интеграция в другие окна

Чтобы добавить такое же поведение в другие окна, используйте этот шаблон:

```javascript
var win = Ext.create('Ext.window.Window', {
    title: 'Название окна',
    width: 800,
    height: 600,
    maximizable: true,
    minimizable: true,
    constrain: true,
    listeners: {
        close: function() {
            if (window.openWindows) {
                delete window.openWindows['window_id'];
            }
            removeWindowFromTaskbar('window_id');
        },
        activate: function() {
            updateTaskbarButton('window_id', true, false);
        },
        deactivate: function() {
            updateTaskbarButton('window_id', false, false);
        },
        minimize: function() {
            this.hide();
            updateTaskbarButton('window_id', false, true);
        },
        maximize: function() {
            updateTaskbarButton('window_id', true, false);
        },
        restore: function() {
            updateTaskbarButton('window_id', true, false);
        }
    }
});

if (!window.openWindows) {
    window.openWindows = {};
}
window.openWindows['window_id'] = win;
addWindowToTaskbar(win);
win.show();
```

## История изменений

- 2026-03-02: Создано тестовое окно для проверки всех состояний ExtJS Windows
