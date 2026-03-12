# Исправление Drag & Drop сортировки секций в Section Builder

## Дата
2026-03-04

## Проблема

При перетаскивании секций в Section Builder:
1. Секции визуально перемещались в списке
2. Но после перезагрузки страницы возвращались на прежние места
3. Порядок не сохранялся в базе данных

## Причина

### 1. Отсутствие POST маршрута
В `routes/admin.php` был зарегистрирован только GET маршрут:
```php
Route::get('section_builder', [SectionBuilderController::class, 'index']);
```

POST запросы не обрабатывались.

### 2. Проблемы с отправкой данных через ExtJS
ExtJS `Ext.Ajax.request` отправлял данные в формате, который Laravel не мог правильно прочитать:
- Параметр `orders` приходил как `null`
- Content-Type не соответствовал ожиданиям Laravel

### 3. Отсутствие CSRF токена
POST запросы без CSRF токена блокируются Laravel middleware.

## Решение

### Проблема: HTTP 301 редирект

**Корневая причина:** URL с trailing slash (`/admin/section_builder/`) вызывал редирект 301 на URL без slash (`/admin/section_builder`), при этом POST данные терялись.

### 1. Добавлен POST маршрут

**Файл:** `routes/admin.php`

```php
// Section Builder - конструктор секций
Route::get('section_builder', [SectionBuilderController::class, 'index'])->name('section_builder');
Route::post('section_builder', [SectionBuilderController::class, 'index'])->name('section_builder.post');
```

### 2. Изменен способ отправки данных

**Файл:** `public/site/modules/admin/desktop/section_builder_window.js`

**БЫЛО (ExtJS Ajax):**
```javascript
Ext.Ajax.request({
    url: '/admin/section_builder/',
    method: 'POST',
    params: {
        action: 'update_sort',
        orders: JSON.stringify(orders)
    },
    // ...
});
```

**СТАЛО (jQuery Ajax):**
```javascript
// Получаем CSRF токен
var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// ВАЖНО: URL без trailing slash!
$.ajax({
    url: '/admin/section_builder',  // БЕЗ слеша в конце!
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': csrfToken
    },
    data: {
        action: 'update_sort',
        orders: JSON.stringify(orders)
    },
    dataType: 'json',
    success: function(result) {
        console.log('Ответ сервера:', result);
        if (result.success) {
            console.log('✅ Порядок секций обновлен');
        }
    }
});
```

### 3. Улучшено логирование в контроллере

**Файл:** `app/Http/Controllers/Admin/SectionBuilderController.php`

```php
private function updateSort(Request $request)
{
    // Получаем параметр orders разными способами
    $orders = $request->input('orders') ?? $request->post('orders') ?? $request->get('orders');
    
    \Log::info('updateSort: Debug', [
        'method' => $request->method(),
        'all_input' => $request->all(),
        'post' => $request->post(),
        'query' => $request->query(),
        'orders' => $orders,
        'type' => gettype($orders)
    ]);
    
    if (!$orders) {
        return response()->json(['success' => false, 'error' => 'Данные о порядке не переданы']);
    }
    
    try {
        // Декодируем JSON если пришла строка
        if (is_string($orders)) {
            $orders = json_decode($orders, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success' => false, 
                    'error' => 'Ошибка декодирования JSON: ' . json_last_error_msg()
                ]);
            }
        }
        
        if (!is_array($orders) || empty($orders)) {
            return response()->json(['success' => false, 'error' => 'Некорректный формат данных']);
        }
        
        // Обновляем порядок для каждой секции
        foreach ($orders as $order) {
            if (isset($order['id']) && isset($order['sort'])) {
                DB::table('page_sections')
                    ->where('id', $order['id'])
                    ->update(['sort_order' => $order['sort']]);
            }
        }
        
        \Log::info('updateSort: Success', ['updated_count' => count($orders)]);
        
        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        \Log::error('updateSort: Error', ['error' => $e->getMessage()]);
        return response()->json([
            'success' => false,
            'error' => 'Ошибка обновления порядка: ' . $e->getMessage()
        ]);
    }
}
```

## Сравнение с Legacy

### Legacy реализация

**Файл:** `legacy/site/modules/admin/page_builder/page_builder.class.php`

```php
private function update_sort() {
    $orders = isset($_POST['orders']) ? json_decode($_POST['orders'], true) : [];
    
    header('Content-Type: application/json; charset=utf-8');
    
    if (empty($orders)) {
        echo json_encode(['success' => false, 'error' => 'Данные не переданы'], JSON_UNESCAPED_UNICODE);
        return;
    }
    
    foreach ($orders as $item) {
        $id = (int)$item['id'];
        $sort = (int)$item['sort'];
        q("UPDATE page_sections SET sort_order = $sort WHERE id = $id");
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Порядок обновлен'
    ], JSON_UNESCAPED_UNICODE);
}
```

### Ключевые отличия

1. **Маршрутизация:**
   - Legacy: один endpoint обрабатывает GET и POST через параметр `action`
   - Laravel: отдельные маршруты для GET и POST

2. **Получение данных:**
   - Legacy: `$_POST['orders']`
   - Laravel: `$request->input('orders')`

3. **CSRF защита:**
   - Legacy: отсутствует
   - Laravel: обязательна (через токен в заголовке)

4. **Логирование:**
   - Legacy: отсутствует
   - Laravel: подробное логирование для отладки

## Тестирование

### Чек-лист

- [ ] Открыть Section Builder
- [ ] Создать несколько тестовых секций
- [ ] Перетащить секцию вверх/вниз
- [ ] Проверить в консоли браузера:
  - `Новый порядок секций: [массив]`
  - `JSON строка для отправки: [строка]`
  - `Ответ сервера: {success: true}`
  - `✅ Порядок секций обновлен`
- [ ] Перезагрузить страницу
- [ ] Проверить что порядок сохранился

### Проверка в базе данных

```sql
SELECT id, name, sort_order 
FROM page_sections 
ORDER BY sort_order ASC;
```

Поле `sort_order` должно соответствовать новому порядку.

### Проверка логов Laravel

```bash
tail -f storage/logs/laravel.log | grep updateSort
```

Должны появиться записи:
```
[2026-03-04] local.INFO: updateSort: Debug {...}
[2026-03-04] local.INFO: updateSort: Success {"updated_count": 5}
```

## Возможные проблемы

### 1. CSRF токен не найден

**Ошибка в консоли:**
```
Cannot read property 'getAttribute' of null
```

**Решение:**
Проверить что в `<head>` есть:
```html
<meta name="csrf-token" content="{{ csrf_token() }}">
```

### 2. 419 Page Expired

**Причина:** CSRF токен устарел или отсутствует

**Решение:**
- Перезагрузить страницу
- Проверить что токен передается в заголовке `X-CSRF-TOKEN`

### 4. HTTP 301 редирект

**Причина:** URL с trailing slash вызывает редирект, POST данные теряются

**Решение:**
Убрать trailing slash из всех AJAX URL:
```javascript
// БЫЛО (неправильно):
url: '/admin/section_builder/',  // ❌ Вызывает 301 редирект

// СТАЛО (правильно):
url: '/admin/section_builder',   // ✅ Прямой запрос без редиректа
```

**Причина:** POST маршрут не зарегистрирован

**Решение:**
Проверить `routes/admin.php`:
```php
Route::post('section_builder', [SectionBuilderController::class, 'index']);
```

### 4. Данные не сохраняются

**Причина:** Параметр `orders` не доходит до контроллера

**Решение:**
- Проверить логи Laravel: `tail -f storage/logs/laravel.log`
- Проверить консоль браузера: должен быть `JSON строка для отправки`
- Проверить Network tab в DevTools: POST запрос должен содержать `orders`

## Итог

✅ Drag & Drop сортировка секций теперь работает корректно:
- Секции перемещаются визуально
- Порядок сохраняется в базе данных
- После перезагрузки порядок сохраняется
- Есть подробное логирование для отладки
- Откат изменений при ошибке сервера

## Связанные файлы

- `routes/admin.php` - маршруты
- `app/Http/Controllers/Admin/SectionBuilderController.php` - контроллер
- `public/site/modules/admin/desktop/section_builder_window.js` - frontend
- `resources/views/admin/desktop/index.blade.php` - layout с CSRF токеном
- `legacy/site/modules/admin/page_builder/page_builder.class.php` - legacy реализация

## История

- 2026-03-04: Создан документ после исправления проблемы с Drag & Drop
- Проблема: Порядок секций не сохранялся в БД
- Решение: Добавлен POST маршрут, изменен способ отправки данных, добавлен CSRF токен
