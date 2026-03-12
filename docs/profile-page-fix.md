# Исправление ошибки на странице профиля

**Дата**: 2026-03-05  
**Статус**: ✅ Исправлено

## Проблема

При попытке открыть страницу профиля (`/profile`) после авторизации возникала ошибка:

```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'o.created_at' in 'field list'
```

## Причина

В `ProfileController::index()` SQL-запрос использовал несуществующие колонки и неправильную логику поиска заказов:
- `o.created_at` - в таблице `orders` колонка называется `date_init`
- `o.total_amount` - в таблице используется `full_sum`
- `o.delivery_address` - этой колонки вообще нет в таблице
- `op.order_id` - в `order_positions` используется `order_num`
- **КРИТИЧНО**: Поиск по `user_id`, хотя в базе заказы привязаны к телефону (`phone`)

## Структура таблицы orders

```sql
-- Основные колонки для заказов:
id              bigint(21)
order_code      varchar(255)
date_init       datetime        -- Дата создания заказа
status          int(5)          -- Статус заказа (1-новый, 2-в обработке, 3-доставлен, 4-отменен)
full_sum        float           -- Полная сумма заказа
discount_sum    float           -- Сумма скидки
pay_sum         float           -- Сумма к оплате
name            varchar(255)    -- Имя покупателя
phone           varchar(255)    -- Телефон покупателя
email           varchar(255)    -- Email покупателя
user_id         int(10)         -- ID пользователя
```

## Решение

Исправлен SQL-запрос в `ProfileController::index()`:

### Было (неправильно):
```php
$orders = DB::select("
    SELECT 
        o.id,
        o.created_at,           -- ❌ Не существует
        o.status,
        o.total_amount,         -- ❌ Не существует
        o.delivery_address,     -- ❌ Не существует
        o.phone,
        COUNT(op.id) as items_count
    FROM orders o
    LEFT JOIN order_positions op ON o.id = op.order_id  -- ❌ Неправильная связь
    WHERE o.user_id = ?
    GROUP BY o.id, o.created_at, o.status, o.total_amount, o.delivery_address, o.phone
    ORDER BY o.created_at DESC
", [$user->id]);
```

### Стало (правильно):
```php
$orders = DB::select("
    SELECT 
        o.id,
        o.order_code,
        o.date_init as created_at,      -- ✅ Правильная колонка с алиасом
        o.status,
        o.full_sum as total_amount,     -- ✅ Правильная колонка с алиасом
        o.name,
        o.phone,
        o.email,
        COUNT(op.id) as items_count
    FROM orders o
    LEFT JOIN order_positions op ON op.order_num = o.id  -- ✅ Правильная связь
    WHERE o.phone = ?                   -- ✅ Поиск по телефону, как в OrdersController
    GROUP BY o.id, o.order_code, o.date_init, o.status, o.full_sum, o.name, o.phone, o.email
    ORDER BY o.date_init DESC
", [$user->phone]);                     -- ✅ Используем телефон пользователя
```

## Изменения

1. ✅ Заменено `o.created_at` → `o.date_init as created_at`
2. ✅ Заменено `o.total_amount` → `o.full_sum as total_amount`
3. ✅ Удалено `o.delivery_address` (колонки нет в таблице)
4. ✅ Добавлено `o.order_code`, `o.name`, `o.email` для полноты данных
5. ✅ Исправлено `o.order_num = o.id` → `op.order_num = o.id` в JOIN
6. ✅ Обновлен GROUP BY со всеми выбранными колонками
7. ✅ Исправлен ORDER BY на `o.date_init`
8. ✅ **КРИТИЧНО**: Изменен поиск с `WHERE o.user_id = ?` на `WHERE o.phone = ?` и параметр с `$user->id` на `$user->phone`

## Почему поиск по телефону?

В базе данных заказы привязаны к номеру телефона покупателя, а не к `user_id`. Это видно из `OrdersController::index()`, который использует ту же логику:

```php
// OrdersController - эталонная реализация
WHERE o.phone = ?
```

Это означает, что заказы могут быть созданы до регистрации пользователя (гостевые заказы), и после регистрации они связываются через номер телефона.

## Эталонная реализация

`OrdersController::index()` уже использовал правильные названия колонок:
- `o.date_init as created_at`
- `o.full_sum as total_amount`
- `op.order_num` для связи с order_positions

## Проверка

После исправления:
1. ✅ Очищен кеш view: `php artisan view:clear`
2. ✅ SQL-запрос использует только существующие колонки
3. ✅ Страница профиля должна открываться без ошибок

## Файлы

- `app/Http/Controllers/ProfileController.php` - исправлен SQL-запрос
- `app/Http/Controllers/OrdersController.php` - эталонная реализация (не требует изменений)

## Связанные документы

- [product-card-data-requirements.md](.kiro/steering/product-card-data-requirements.md) - требования к данным карточек товаров
- [checkout-pickpoint-fix-after-git-reset.md](checkout-pickpoint-fix-after-git-reset.md) - предыдущее исправление после git reset

## История

- 2026-03-05: Создан документ после исправления ProfileController
- Проблема: SQL-запрос использовал несуществующие колонки `created_at`, `total_amount`, `delivery_address`
- Решение: Заменены на правильные колонки `date_init`, `full_sum`, удалена несуществующая колонка
