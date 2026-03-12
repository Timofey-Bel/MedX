---
inclusion: auto
---

# API Endpoints Pattern для AJAX операций

## Обзор

Для AJAX операций в проекте используется паттерн "единый endpoint с параметром task", аналогичный legacy системе. Это обеспечивает обратную совместимость и упрощает миграцию.

## Паттерн реализации

### 1. Роут в routes/api.php

```php
// API endpoint для [модуль] - обрабатывает все AJAX операции
// Использует session middleware для сохранения данных
// Исключен из проверки CSRF токена (настроено в bootstrap/app.php)
Route::post('/[module]', [ModuleController::class, 'handleAjax'])
    ->middleware(['web'])
    ->name('api.[module]');
```

**Важно:**
- Всегда используйте POST метод (даже для получения данных)
- Добавляйте middleware `web` для работы с сессией
- Исключайте из CSRF проверки в `bootstrap/app.php`

### 2. Метод handleAjax в контроллере

```php
/**
 * Обработка AJAX запросов [модуль] через API
 * Используется из routes/api.php - НЕ требует CSRF токен
 * Единый endpoint для всех операций с [модуль]
 * 
 * @param Request $request
 * @return \Illuminate\Http\JsonResponse
 */
public function handleAjax(Request $request)
{
    $task = $request->input('task');

    switch ($task) {
        case 'get_data':
            return $this->getData($request);
        
        case 'add_item':
            return $this->addItem($request);
        
        case 'remove_item':
            return $this->removeItem($request);
        
        default:
            return response()->json([
                'error' => 'Unknown task',
                'task' => $task
            ], 400);
    }
}

/**
 * Получить данные (task=get_data)
 * 
 * @param Request $request
 * @return \Illuminate\Http\JsonResponse
 */
protected function getData(Request $request)
{
    // Логика получения данных
    return response()->json([
        'success' => true,
        'data' => []
    ]);
}
```

**Важно:**
- Метод `handleAjax` должен быть public
- Методы для конкретных задач должны быть protected
- Всегда возвращайте JSON response
- Обрабатывайте неизвестные task с кодом 400

### 3. JavaScript запрос

```javascript
fetch('/api/[module]', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({ task: 'get_data' }),
    credentials: 'same-origin' // Важно: для сохранения сессии
})
.then(response => response.json())
.then(data => {
    console.log('Data received:', data);
})
.catch(error => {
    console.error('Error:', error);
});
```

**Важно:**
- Всегда используйте `credentials: 'same-origin'` для отправки cookies
- Используйте `Content-Type: application/json` для JSON данных
- Используйте `Content-Type: application/x-www-form-urlencoded` для form data

## Примеры реализации

### Корзина (Cart)

**Роут:** `POST /api/cart`

**Задачи:**
- `put_item` - добавить товар
- `delete_product` - удалить товар
- `update_amount` - изменить количество
- `update_item` - обновить товар
- `get_cart` - получить корзину
- `apply_promocode` - применить промокод
- `cancel_promocode` - отменить промокод
- `update_item_selected` - обновить выбор товара

**Контроллер:** `CartController::handleAjax()`

### Избранное (Favorites)

**Роут:** `POST /api/favorites`

**Задачи:**
- `get_favorites` - получить список избранного
- `add_item` - добавить товар (также доступен через `/api/favorites/add`)
- `remove_item` - удалить товар (также доступен через `/api/favorites/remove`)

**Контроллер:** `FavoriteController::handleAjax()`

**Примечание:** Для обратной совместимости также доступны отдельные endpoints:
- `POST /api/favorites/add` - добавить товар
- `POST /api/favorites/remove` - удалить товар

## Работа с сессией для неавторизованных пользователей

Для неавторизованных пользователей данные хранятся в сессии. Важно правильно работать с сессией:

```php
// Явно стартуем сессию если не запущена
if (!$request->session()->isStarted()) {
    $request->session()->start();
}

// Получаем данные из сессии
$data = $request->session()->get('key', []);

// Сохраняем данные в сессию
$request->session()->put('key', $data);

// Принудительно сохраняем сессию (для создания cookie)
$request->session()->save();
```

**Важно:**
- Используйте `$request->session()` вместо `session()` helper
- Всегда вызывайте `save()` после изменения данных
- Проверяйте `isStarted()` перед работой с сессией

## CSRF защита

API endpoints должны быть исключены из CSRF проверки в `bootstrap/app.php`:

```php
$middleware->validateCsrfTokens(except: [
    'api/cart',
    'api/favorites',
    'api/favorites/add',
    'api/favorites/remove',
]);
```

**Причина:** Endpoints вызываются через AJAX без передачи CSRF токена и работают для неавторизованных пользователей.

**Безопасность:** Операции не критичные, используется валидация данных и rate limiting (опционально).

## Документирование

При создании нового API endpoint:

1. Добавьте комментарии к роуту в `routes/api.php`
2. Добавьте PHPDoc к методу `handleAjax` и всем protected методам
3. Обновите документацию модуля в `docs/[module]-module.md`
4. Добавьте информацию о CSRF исключении в `.kiro/steering/csrf-api-endpoints.md`

## Проверка роутов

Для проверки зарегистрированных роутов используйте:

```bash
& "C:\OS\modules\PHP-8.5\php.exe" artisan route:list --path=[module]
```

Пример вывода:
```
POST       api/favorites api.favorites › FavoriteController@handleAjax
POST       api/favorites/add api.favorites.add › FavoriteController@add
POST       api/favorites/remove api.favorites.remove › FavoriteController@remove
GET|HEAD   favorites ............ favorites › FavoriteController@index
```

## Типичные ошибки

### 1. Возвращается HTML вместо JSON

**Причина:** Браузер делает GET запрос к `/api/[module]`, который не находит роут и возвращает HTML страницу.

**Решение:** Убедитесь, что JavaScript использует POST метод.

### 2. Сессия не сохраняется

**Причина:** Не вызывается `$request->session()->save()` или не используется `credentials: 'same-origin'` в fetch.

**Решение:** 
- Добавьте `$request->session()->save()` после изменения данных
- Добавьте `credentials: 'same-origin'` в fetch запрос

### 3. CSRF 419 ошибка

**Причина:** Endpoint не исключен из CSRF проверки.

**Решение:** Добавьте endpoint в `bootstrap/app.php` в список исключений.

## Миграция из legacy

При миграции legacy AJAX endpoints:

1. Найдите все вызовы `/?task=...` в JavaScript
2. Замените на `/api/[module]` с параметром `task`
3. Создайте метод `handleAjax` в контроллере
4. Добавьте роут в `routes/api.php`
5. Исключите из CSRF проверки
6. Обновите документацию

**Пример миграции:**

Legacy:
```javascript
fetch('/favorites/?task=add_item', {
    method: 'POST',
    body: 'product_id=' + productId
})
```

Laravel:
```javascript
fetch('/api/favorites/add', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: 'product_id=' + productId,
    credentials: 'same-origin'
})
```

Или через единый endpoint:
```javascript
fetch('/api/favorites', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({ 
        task: 'add_item',
        product_id: productId 
    }),
    credentials: 'same-origin'
})
```
