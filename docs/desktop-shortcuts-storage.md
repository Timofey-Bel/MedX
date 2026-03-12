# Хранение данных ярлыков рабочего стола

## Обзор

Все данные о ярлыках рабочего стола (названия и позиции) теперь хранятся в базе данных в таблице `desktop_shortcuts`.

## Структура таблицы

```sql
CREATE TABLE desktop_shortcuts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    shortcut_id VARCHAR(100) NOT NULL,
    custom_name VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NULL,
    position_x INT NULL,
    position_y INT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY unique_user_shortcut (user_id, shortcut_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Поля

- `user_id` - ID пользователя (из сессии `admin_user`)
- `shortcut_id` - Уникальный идентификатор ярлыка (из атрибута `data-shortcut-id`)
- `custom_name` - Пользовательское название ярлыка (после переименования)
- `original_name` - Оригинальное название ярлыка (для возможности сброса)
- `position_x` - X координата ярлыка на рабочем столе
- `position_y` - Y координата ярлыка на рабочем столе

## API Endpoints

### Сохранение пользовательского названия

```
POST /admin/desktop-shortcut/save
```

**Параметры:**
- `shortcut_id` (required) - ID ярлыка
- `custom_name` (required) - Новое название
- `original_name` (optional) - Оригинальное название
- `position_x` (optional) - X координата
- `position_y` (optional) - Y координата

### Сохранение позиции

```
POST /admin/desktop-shortcut/save-position
```

**Параметры:**
- `shortcut_id` (required) - ID ярлыка
- `position_x` (required) - X координата
- `position_y` (required) - Y координата
- `original_name` (optional) - Оригинальное название

### Получение всех данных

```
GET /admin/desktop-shortcuts
```

**Ответ:**
```json
{
    "success": true,
    "data": {
        "shortcut-1": "Пользовательское название"
    }
}
```

## Функционал

### Переименование ярлыков

1. ПКМ на ярлыке → "Переименовать"
2. Ввод нового названия
3. Enter - сохранить, Escape - отменить
4. Название сохраняется в БД через API

### Перемещение ярлыков

1. Зажать ЛКМ на ярлыке на 1 секунду (или сдвинуть мышь)
2. Перетащить ярлык в нужное место
3. Отпустить ЛКМ
4. Позиция автоматически сохраняется в БД

### Восстановление при загрузке

При загрузке страницы:
1. Контроллер `AdminDesktopController` загружает данные из БД
2. JavaScript применяет пользовательские названия
3. JavaScript применяет сохраненные позиции

### Миграция из localStorage

При первой загрузке после обновления:
- Если позиция есть в localStorage, но нет в БД
- Позиция автоматически мигрирует в БД
- Запись из localStorage удаляется

## Файлы

### Backend

- `app/Models/DesktopShortcut.php` - Модель
- `app/Http/Controllers/Admin/DesktopShortcutController.php` - Контроллер API
- `app/Http/Controllers/Admin/AdminDesktopController.php` - Загрузка данных
- `routes/admin.php` - Маршруты API

### Frontend

- `resources/views/admin/desktop/index.blade.php` - Атрибуты `data-shortcut-id`
- `resources/views/admin/desktop/js/_drag-drop.blade.php` - Перемещение и сохранение позиций
- `resources/views/admin/desktop/js/_context-menu.blade.php` - Переименование

## История

- 2026-03-04: Создана таблица `desktop_shortcuts`
- 2026-03-04: Добавлены поля `original_name`, `position_x`, `position_y`
- 2026-03-04: Реализовано сохранение позиций в БД
- 2026-03-04: Реализована миграция из localStorage
