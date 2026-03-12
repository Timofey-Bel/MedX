# Исправление сохранения данных корзины в сессию

## Проблема

При добавлении товара через AJAX к `/api/cart`, данные не сохранялись в сессию Laravel. При перезагрузке страницы или запросе `task=get_cart`, сервер возвращал пустую корзину.

**Причина**: API routes в Laravel по умолчанию не используют сессию, так как они находятся в middleware group `api`, который не включает `StartSession` middleware.

## Решение

Добавлен `web` middleware к route `/api/cart` в файле `routes/api.php`, что включает поддержку сессий для этого API endpoint.

### Изменения в `routes/api.php`

**Было:**
```php
Route::post('/cart', [CartController::class, 'handleAjax'])->name('api.cart');
```

**Стало:**
```php
// API endpoint для корзины - обрабатывает все AJAX операции
// Использует session middleware для сохранения данных корзины
// НЕ требует CSRF токен (VerifyCsrfToken исключен для API routes)
Route::post('/cart', [CartController::class, 'handleAjax'])
    ->middleware(['web'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->name('api.cart');
```

### Объяснение

- `->middleware(['web'])` - добавляет web middleware group, который включает:
  - `StartSession` - инициализация и сохранение сессии
  - `EncryptCookies` - шифрование cookies
  - `AddQueuedCookiesToResponse` - добавление cookies в ответ
  - `ShareErrorsFromSession` - передача ошибок из сессии в views
  
- `->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])` - отключает проверку CSRF токена для этого route, так как это API endpoint, вызываемый через AJAX

## Результат

После применения исправления:
- ✅ Данные корзины сохраняются в сессию Laravel при AJAX запросах
- ✅ При перезагрузке страницы счетчик корзины остается актуальным
- ✅ При переходе на `/cart`, корзина показывает добавленные товары
- ✅ Запрос `/api/cart` с `task=get_cart` возвращает актуальные данные из сессии

## Тестирование

Создан тест `tests/Feature/CartSessionPersistenceTest.php`, который проверяет:
1. Сохранение данных в сессию после добавления товара через API
2. Доступность данных корзины после перезагрузки (между запросами)
3. Обновление счетчика корзины после добавления товара

## Дата исправления

{{ date }}
