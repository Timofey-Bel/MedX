# Исправление ошибки "CSRF token mismatch" для API endpoint `/api/cart`

## Проблема
При добавлении товара через AJAX к `/api/cart` возникала ошибка "CSRF token mismatch".

## Причина
В Laravel 12 метод `withoutMiddleware()` в `routes/api.php` не работает. Конфигурация middleware перенесена в `bootstrap/app.php`.

## Решение

### 1. Обновлен файл `bootstrap/app.php`
Добавлено исключение для `/api/cart` из проверки CSRF токена:

```php
->withMiddleware(function (Middleware $middleware): void {
    // Исключаем /api/cart из проверки CSRF токена
    // Это API endpoint для AJAX операций с корзиной
    $middleware->validateCsrfTokens(except: [
        'api/cart',
    ]);
})
```

### 2. Обновлен файл `routes/api.php`
Убран устаревший метод `withoutMiddleware()`:

**Было:**
```php
Route::post('/cart', [CartController::class, 'handleAjax'])
    ->middleware(['web'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->name('api.cart');
```

**Стало:**
```php
// API endpoint для корзины - обрабатывает все AJAX операции
// Использует session middleware для сохранения данных корзины
// Исключен из проверки CSRF токена (настроено в bootstrap/app.php)
Route::post('/cart', [CartController::class, 'handleAjax'])
    ->middleware(['web'])
    ->name('api.cart');
```

## Результат
- ✅ AJAX запросы к `/api/cart` работают без ошибки "CSRF token mismatch"
- ✅ Сессия сохраняется корректно (через `web` middleware)
- ✅ Данные корзины сохраняются между запросами
- ✅ Тесты подтверждают корректную работу

## Тестирование

### Автоматические тесты
```bash
php artisan test --filter=CartCsrfExemptionTest
```

Результат: **2 теста прошли успешно**

### Ручное тестирование
1. Добавьте товар на странице каталога
2. Проверьте, что запрос к `/api/cart` возвращает данные товара (без ошибки CSRF)
3. Перезагрузите страницу - счетчик должен остаться актуальным
4. Перейдите на `/cart` - корзина должна показать товар

## Объяснение
В Laravel 12 конфигурация middleware централизована в `bootstrap/app.php`. Метод `validateCsrfTokens(except: [...])` позволяет добавить исключения для CSRF проверки, что дает возможность использовать сессию (через `web` middleware) без необходимости передавать CSRF токен в AJAX запросах.
