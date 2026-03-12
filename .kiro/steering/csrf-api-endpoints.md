---
inclusion: auto
---

# CSRF защита и API endpoints

## Обзор

Laravel использует CSRF (Cross-Site Request Forgery) защиту для всех POST, PUT, PATCH, DELETE запросов. Это защищает приложение от атак с подделкой межсайтовых запросов.

## Проблема с AJAX запросами

При выполнении AJAX запросов к API endpoints без передачи CSRF токена Laravel возвращает ошибку **419 Page Expired**.

## Решение

Есть два подхода:

### 1. Передача CSRF токена в запросах (рекомендуется для форм)

Для обычных форм добавляйте CSRF токен:

```html
<form method="POST" action="/endpoint">
    @csrf
    <!-- поля формы -->
</form>
```

Для AJAX запросов добавляйте токен в заголовки:

```javascript
fetch('/endpoint', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify(data)
})
```

### 2. Исключение API endpoints из CSRF проверки (для публичных API)

Для публичных API endpoints, которые не требуют CSRF защиты (например, AJAX операции с корзиной и избранным), исключите их из проверки.

## Как исключить endpoint из CSRF проверки

### Шаг 1: Откройте файл `bootstrap/app.php`

Найдите секцию `withMiddleware`:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->validateCsrfTokens(except: [
        // список исключений
    ]);
})
```

### Шаг 2: Добавьте ваш endpoint в массив `except`

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->validateCsrfTokens(except: [
        'api/cart',
        'api/favorites/add',
        'api/favorites/remove',
        'api/favorites',
        'api/your-new-endpoint',  // ← добавьте сюда
    ]);
})
```

### Шаг 3: Добавьте комментарий

Всегда добавляйте комментарий, объясняющий зачем исключен endpoint:

```php
'api/your-new-endpoint',  // AJAX операции с вашим модулем
```

## Когда исключать из CSRF проверки

### ✅ Исключайте:

1. **Публичные API endpoints** - доступные без авторизации
2. **AJAX операции** - вызываемые из JavaScript без форм
3. **Операции с сессией** - корзина, избранное для неавторизованных
4. **Webhook endpoints** - вызываемые внешними сервисами
5. **Mobile API** - для мобильных приложений

### ❌ НЕ исключайте:

1. **Операции с деньгами** - платежи, переводы
2. **Изменение данных пользователя** - профиль, пароль
3. **Административные операции** - управление пользователями, настройки
4. **Критичные операции** - удаление аккаунта, изменение прав

## Примеры из проекта

### Корзина (Cart)

```php
'api/cart',  // AJAX операции с корзиной (добавление, удаление, обновление)
```

**Почему исключен:**
- Публичный API (работает для неавторизованных)
- Использует сессию для хранения данных
- Вызывается через AJAX без форм
- Не критичная операция (можно откатить)

### Избранное (Favorites)

```php
'api/favorites/add',     // AJAX добавление в избранное
'api/favorites/remove',  // AJAX удаление из избранного
'api/favorites',         // AJAX получение списка избранного
```

**Почему исключен:**
- Публичный API (работает для неавторизованных)
- Использует сессию для неавторизованных
- Вызывается через AJAX без форм
- Не критичная операция

## Альтернативный подход: API токены

Для более безопасного API можно использовать токены вместо CSRF:

### Laravel Sanctum (рекомендуется)

```php
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/favorites/add', [FavoriteController::class, 'add']);
});
```

```javascript
// JavaScript
fetch('/api/favorites/add', {
    method: 'POST',
    headers: {
        'Authorization': 'Bearer ' + token,
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({ product_id: '123' })
})
```

**Преимущества:**
- Более безопасно для API
- Подходит для мобильных приложений
- Не требует cookies
- Можно ограничить срок действия токена

**Недостатки:**
- Требует дополнительной настройки
- Нужно управлять токенами
- Сложнее для простых AJAX запросов

## Контрольный список для субагентов

При создании нового API endpoint:

- [ ] Определите, нужна ли CSRF защита
- [ ] Если endpoint публичный и вызывается через AJAX - исключите из CSRF
- [ ] Добавьте endpoint в `bootstrap/app.php` в массив `except`
- [ ] Добавьте комментарий, объясняющий причину исключения
- [ ] Задокументируйте endpoint в `docs/`
- [ ] Протестируйте работу endpoint без CSRF токена
- [ ] Убедитесь, что endpoint не выполняет критичные операции

## Безопасность

### Риски исключения из CSRF

1. **CSRF атаки** - злоумышленник может выполнить запрос от имени пользователя
2. **Несанкционированные действия** - если endpoint критичный

### Как минимизировать риски

1. **Используйте дополнительную авторизацию** - проверяйте права пользователя
2. **Ограничьте rate limiting** - защита от спама
3. **Логируйте операции** - для аудита
4. **Валидируйте данные** - проверяйте все входные параметры
5. **Используйте HTTPS** - защита от перехвата

### Пример безопасного endpoint

```php
public function add(Request $request)
{
    // 1. Валидация данных
    $validated = $request->validate([
        'product_id' => 'required|string|exists:products,id'
    ]);
    
    // 2. Rate limiting (опционально)
    RateLimiter::attempt(
        'add-favorite:' . $request->ip(),
        $perMinute = 10,
        function() use ($validated) {
            // операция
        }
    );
    
    // 3. Дополнительная проверка (опционально)
    if (Auth::check()) {
        // Проверка прав пользователя
    }
    
    // 4. Логирование (опционально)
    Log::info('Favorite added', [
        'product_id' => $validated['product_id'],
        'user_id' => Auth::id(),
        'ip' => $request->ip()
    ]);
    
    // 5. Выполнение операции
    return $this->favoriteService->addItem($validated['product_id'], Auth::id());
}
```

## Отладка CSRF проблем

### Ошибка 419 Page Expired

**Причины:**
1. Endpoint не исключен из CSRF проверки
2. CSRF токен не передан в запросе
3. Сессия истекла
4. Неправильный домен/subdomain

**Решение:**
1. Проверьте `bootstrap/app.php` - есть ли endpoint в `except`
2. Проверьте JavaScript - передается ли CSRF токен
3. Проверьте `.env` - правильно ли настроен `SESSION_DOMAIN`
4. Очистите кэш: `php artisan config:clear`

### Проверка CSRF токена

```javascript
// В консоли браузера
console.log(document.querySelector('meta[name="csrf-token"]').content);
```

Если токен пустой - проверьте layout:

```blade
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
```

## Дополнительные ресурсы

- [Laravel CSRF Protection](https://laravel.com/docs/11.x/csrf)
- [Laravel Sanctum](https://laravel.com/docs/11.x/sanctum)
- [OWASP CSRF Prevention](https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html)

---

## Важно запомнить

1. **CSRF защита важна** - не отключайте её без необходимости
2. **Документируйте исключения** - объясняйте почему endpoint исключен
3. **Используйте дополнительную защиту** - валидация, rate limiting, логирование
4. **Тестируйте безопасность** - проверяйте, что endpoint не уязвим
5. **Следуйте принципу наименьших привилегий** - исключайте только то, что необходимо

**При сомнениях - НЕ исключайте endpoint из CSRF проверки!**
