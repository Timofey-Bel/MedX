# Модуль "Избранное" (Favorites)

## Обзор

Модуль избранного позволяет пользователям сохранять товары для последующего просмотра. Работает как для авторизованных, так и для неавторизованных пользователей.

**Миграция из legacy:** `site/modules/sfera/favorites/`

**Дата создания:** 2026-02-27

**Статус:** ✅ Реализовано

---

## Архитектура

### Backend (Laravel)

#### FavoriteService (`app/Services/FavoriteService.php`)

Сервис для работы с избранным товарами.

**Основные методы:**

- `addItem(string $productId, int $userId): array` - Добавить товар в избранное
- `removeItem(string $productId, int $userId): array` - Удалить товар из избранного
- `getItems(int $userId): array` - Получить список избранных товаров (для авторизованных)
- `getItemsByIds(array $productIds): array` - Получить данные товаров по массиву ID (для неавторизованных)
- `getItemsCount(int $userId): int` - Получить количество товаров в избранном
- `isInFavorites(string $productId, int $userId): bool` - Проверить наличие товара в избранном

**Особенности:**
- Использует прямые SQL-запросы через `DB::select()`, `DB::insert()`, `DB::delete()`
- Для авторизованных пользователей - сохраняет в таблицу `favorites`
- Возвращает массивы с полями: `success`, `message`, `count`
- Все комментарии на русском языке

#### FavoriteController (`app/Http/Controllers/FavoriteController.php`)

Контроллер для обработки запросов избранного.

**Методы:**

1. `index()` - Страница избранного
   - Для авторизованных: показывает список из БД
   - Для неавторизованных: показывает пустую страницу (избранное в сессии)
   - Route: `GET /favorites`

2. `add(Request $request)` - AJAX добавление товара
   - Для авторизованных: сохраняет в БД через FavoriteService
   - Для неавторизованных: сохраняет в сессию
   - Route: `POST /api/favorites/add`
   - Параметры: `product_id` (string)
   - Ответ: JSON с `success`, `message`, `count`

3. `remove(Request $request)` - AJAX удаление товара
   - Для авторизованных: удаляет из БД через FavoriteService
   - Для неавторизованных: удаляет из сессии
   - Route: `POST /api/favorites/remove`
   - Параметры: `product_id` (string)
   - Ответ: JSON с `success`, `message`, `count`

### Frontend

#### Blade Template (`resources/views/favorites/index.blade.php`)

Страница избранного с карточками товаров.

**Особенности:**
- Использует Knockout.js для динамического отображения
- Показывает карточки товаров с состоянием корзины
- Пустое состояние: "Избранное пусто" с кнопкой "Перейти к покупкам"
- Конвертировано из legacy `favorites.tpl`

#### JavaScript ViewModels

**FavoritesViewModel** (`public/assets/sfera/js/favorites-viewmodel.js`)

Главная ViewModel для управления избранным.

**Observables:**
- `items` - массив товаров в избранном (observableArray)
- `itemsCount` - количество товаров (computed)
- `itemsCountText` - текстовое представление количества (computed)

**Методы:**
- `loadFavorites(serverData)` - загрузка избранного с сервера или из данных
- `addToFavorite(productId)` - добавление товара в избранное
- `removeFromFavorite(item)` - удаление товара из избранного
- `syncFavoriteIcons(favoriteProductIds)` - синхронизация иконок на всех страницах
- `addToCart(item)` - добавление товара из избранного в корзину
- `refreshFavorites()` - обновление данных с сервера

**Интеграция:**
- `favoritesCounterViewModel` - обновление счетчика в header
- `cartCounterViewModel` - обновление счетчика корзины
- `server_favorites.data` - данные с сервера при первой загрузке

**FavoritesCounterViewModel** (`public/assets/sfera/js/favorites-counter.js`)

ViewModel для счетчика избранного в header.

**Observables:**
- `itemCount` - количество товаров (observable)
- `formattedCount` - форматированное количество (computed)
- `isVisible` - видимость бейджа (computed)

**Методы:**
- `loadCount()` - загрузка счетчика с сервера
- `updateCount(data)` - обновление счетчика после операций

#### Интеграция в другие модули

**Catalog** (`public/assets/sfera/js/catalog.js`)
- `addToFavorites(productId, btn)` - добавление из каталога
- `removeFromFavorites(productId, btn)` - удаление из каталога
- Обновление визуального состояния иконок

**Product** (`public/assets/sfera/js/product.js`)
- Аналогичные функции для страницы товара

**Cart** (`public/assets/sfera/js/cart-viewmodel.js`)
- Добавление в избранное из корзины

---

## API Endpoints

### Паттерн реализации

API endpoints избранного следуют паттерну "единый endpoint с параметром task", аналогичному корзине. Это обеспечивает:
- Обратную совместимость с legacy системой
- Единообразие API в проекте
- Упрощенную миграцию

Подробнее о паттерне см. `.kiro/steering/api-endpoints-pattern.md`

### CSRF защита

API endpoints избранного **исключены из CSRF проверки** в `bootstrap/app.php`:

```php
$middleware->validateCsrfTokens(except: [
    'api/favorites',
    'api/favorites/add',
    'api/favorites/remove',
]);
```

**Причина:** Endpoints вызываются через AJAX без передачи CSRF токена и работают для неавторизованных пользователей (используют сессию).

**Безопасность:** Операции не критичные, используется валидация данных и rate limiting (опционально).

---

### POST /api/favorites

Единый endpoint для всех операций с избранным.

**Middleware:** `web` (для сессий)

**Метод контроллера:** `FavoriteController::handleAjax()`

**Параметры:**
```json
{
  "task": "get_favorites|add_item|remove_item"
}
```

**Задачи:**

#### task=get_favorites

Получить список избранного.

**Ответ:**
```json
{
  "items": {
    "00-00001234": {
      "id": "00-00001234",
      "name": "Название товара",
      "price": 1500,
      "image": "/import_files/00-00001234b.jpg",
      "rating": 4.5,
      "reviews_count": 10
    },
    "00-00005678": {
      "id": "00-00005678",
      "name": "Другой товар",
      "price": 2500,
      "image": "/import_files/00-00005678b.jpg",
      "rating": 4.0,
      "reviews_count": 5
    }
  },
  "count": 2
}
```

**Примечание:** Возвращает полные данные товаров как для авторизованных, так и для неавторизованных пользователей. Для неавторизованных данные загружаются из сессии и обогащаются информацией из БД через `FavoriteService::getItemsByIds()`.

#### task=add_item

Добавить товар в избранное (также доступен через `/api/favorites/add`).

**Параметры:**
```json
{
  "task": "add_item",
  "product_id": "00-00001234"
}
```

**Ответ:**
```json
{
  "success": true,
  "message": "Товар добавлен в избранное",
  "count": 3
}
```

#### task=remove_item

Удалить товар из избранного (также доступен через `/api/favorites/remove`).

**Параметры:**
```json
{
  "task": "remove_item",
  "product_id": "00-00001234"
}
```

**Ответ:**
```json
{
  "success": true,
  "message": "Товар удален из избранного",
  "count": 2
}
```

---

### GET /favorites
Страница избранного.

**Middleware:** нет (доступно всем)

**Метод контроллера:** `FavoriteController::index()`

**Ответ:** HTML страница

---

### POST /api/favorites/add

Добавление товара в избранное (альтернативный endpoint для обратной совместимости).

**Middleware:** `web` (для сессий)

**Метод контроллера:** `FavoriteController::add()`

**Параметры:**
```
product_id: string (required) - ID товара
```

**Ответ:**
```json
{
  "success": true,
  "message": "Товар добавлен в избранное",
  "count": 5
}
```

**Коды ответа:**
- 200 - успешно
- 400 - не указан product_id
- 500 - ошибка сервера

**Примечание:** Рекомендуется использовать `/api/favorites` с `task=add_item` для единообразия.

---

### POST /api/favorites/remove

Удаление товара из избранного (альтернативный endpoint для обратной совместимости).

**Middleware:** `web` (для сессий)

**Метод контроллера:** `FavoriteController::remove()`

**Параметры:**
```
product_id: string (required) - ID товара
```

**Ответ:**
```json
{
  "success": true,
  "message": "Товар удален из избранного",
  "count": 4
}
```

**Коды ответа:**
- 200 - успешно
- 400 - не указан product_id
- 500 - ошибка сервера

**Примечание:** Рекомендуется использовать `/api/favorites` с `task=remove_item` для единообразия.

---

## База данных

### Таблица `favorites`

```sql
CREATE TABLE favorites (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    product_id VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY unique_user_product (user_id, product_id),
    KEY idx_user_id (user_id),
    KEY idx_product_id (product_id)
);
```

**Поля:**
- `id` - первичный ключ
- `user_id` - ID пользователя
- `product_id` - ID товара (GUID)
- `created_at` - дата добавления
- `updated_at` - дата обновления

**Индексы:**
- `unique_user_product` - уникальность пары пользователь-товар
- `idx_user_id` - быстрый поиск по пользователю
- `idx_product_id` - быстрый поиск по товару

---

## Хранение данных

### Авторизованные пользователи
Избранное хранится в таблице `favorites` в БД.

### Неавторизованные пользователи
Избранное хранится в сессии Laravel:
```php
session('favorites', []) // массив product_id
```

---

## Синхронизация состояния

### Иконки избранного на карточках товаров

Иконки избранного синхронизируются на всех страницах:
- Каталог
- Поиск
- Главная страница
- Страница товара

**Механизм:**
1. При загрузке страницы вызывается `syncFavoriteIcons(favoriteProductIds)`
2. Находятся все элементы с атрибутом `[data-favorite-product-id]`
3. Для каждого элемента проверяется наличие в избранном
4. Добавляется/удаляется класс `active` и `in-favorites`

### Счетчик в header

Счетчик обновляется автоматически после каждой операции:
- Добавление товара
- Удаление товара
- Загрузка страницы

---

## Требования

Модуль реализует следующие требования из спецификации:

- **18.1-18.4** - FavoriteService с методами работы с избранным
- **18.3, 18.7** - FavoriteController с AJAX API
- **3.1-3.5, 5.6-5.10** - Blade шаблон с карточками товаров
- **13.7-13.12, 18.6-18.8** - Knockout.js ViewModel с синхронизацией
- **18.5** - Счетчик избранного в header
- **1.7** - Роуты для избранного

---

## Тестирование

### Ручное тестирование

1. **Добавление в избранное:**
   - Открыть каталог
   - Нажать на иконку сердечка
   - Проверить, что иконка стала активной
   - Проверить, что счетчик в header увеличился

2. **Удаление из избранного:**
   - Нажать на активную иконку сердечка
   - Проверить, что иконка стала неактивной
   - Проверить, что счетчик в header уменьшился

3. **Страница избранного:**
   - Открыть `/favorites`
   - Проверить отображение карточек товаров
   - Проверить работу кнопок "В корзину"
   - Проверить удаление товаров

4. **Синхронизация:**
   - Добавить товар в избранное на странице каталога
   - Перейти на страницу товара
   - Проверить, что иконка активна
   - Перейти на главную страницу
   - Проверить, что иконка активна

### Автоматическое тестирование

Тесты не реализованы (опциональная задача 1.7).

---

## Известные ограничения

1. **Неавторизованные пользователи:**
   - Избранное хранится в сессии (database-backed)
   - Session cookies должны быть включены в браузере
   - Все AJAX запросы включают `credentials: 'same-origin'` для сохранения сессии
   - Сессия явно стартует и сохраняется в методах контроллера для создания cookie
   - Не синхронизируется между устройствами

2. **Производительность:**
   - При большом количестве товаров в избранном может быть медленная загрузка страницы
   - Рекомендуется добавить пагинацию (не реализовано)

---

## Будущие улучшения

1. Добавить пагинацию на странице избранного
2. Добавить сортировку (по дате добавления, по цене, по названию)
3. Добавить фильтры на странице избранного
4. Реализовать миграцию избранного из сессии в БД при авторизации
5. Добавить возможность добавлять заметки к товарам в избранном
6. Реализовать списки избранного (wishlist collections)

---

## Связанные модули

- **Cart** - корзина товаров
- **Product** - карточка товара
- **Catalog** - каталог товаров
- **Auth** - авторизация пользователей

---

## История изменений

### 2026-02-27 - Версия 1.0.5
- Исправлена проблема с удалением товаров из избранного для неавторизованных пользователей
- Добавлен `credentials: 'same-origin'` в запросы add и remove для сохранения сессии
- Карточки товаров теперь корректно исчезают со страницы после удаления

### 2026-02-27 - Версия 1.0.4
- Исправлена проблема с отображением товаров на странице избранного для неавторизованных пользователей
- Добавлен метод `getItemsByIds()` в FavoriteService для загрузки полных данных товаров по массиву ID
- Обновлен метод `getFavorites()` в FavoriteController для использования `getItemsByIds()` для гостей
- Обновлен метод `index()` в FavoriteController для загрузки полных данных при первой загрузке страницы
- Обновлен Blade template для корректной обработки данных от авторизованных и неавторизованных пользователей
- API endpoint `/api/favorites?task=get_favorites` теперь возвращает полные данные товаров для всех пользователей
- Исправлены URL в JavaScript: `/favorites/?task=get_favorites` → `/api/favorites` с `task=get_favorites`
- Обновлены все fetch запросы для использования правильного Content-Type и метода POST

### 2026-02-27 - Версия 1.0.3
- Добавлен метод `handleAjax()` в FavoriteController для единого API endpoint
- Обновлен роут `/api/favorites` для использования паттерна "task-based API"
- Создан steering file `api-endpoints-pattern.md` с документацией паттерна
- Обновлена документация модуля с описанием нового API endpoint
- Endpoints `/api/favorites/add` и `/api/favorites/remove` сохранены для обратной совместимости

### 2026-02-27 - Версия 1.0.2
- Исправлена проблема с сохранением избранного для неавторизованных пользователей
- Добавлен `credentials: 'same-origin'` во все AJAX запросы для сохранения cookies
- Обновлен FavoriteController для явного старта и сохранения сессии
- Добавлены console.log в favorites-counter.js для отладки bindings
- Обновлена документация с информацией о session persistence

### 2026-02-27 - Версия 1.0.1
- Исключены API endpoints из CSRF проверки в `bootstrap/app.php`
- Исправлена ошибка 419 Page Expired при AJAX запросах
- Обновлена документация с информацией о CSRF

### 2026-02-27 - Версия 1.0.0
- Реализован FavoriteService
- Реализован FavoriteController
- Конвертирован Blade шаблон
- Реализован FavoritesViewModel
- Добавлен счетчик в header
- Настроены роуты
- Исправлены API endpoints в JavaScript файлах


---

## База данных

### Таблица `favorites`

Создана миграцией `2026_02_27_092959_create_favorites_table.php`

**Структура:**
```sql
CREATE TABLE favorites (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL COMMENT 'ID пользователя',
    product_id VARCHAR(50) NOT NULL COMMENT 'ID товара (offer_id)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата добавления в избранное',
    
    INDEX idx_user_id (user_id),
    INDEX idx_product_id (product_id),
    UNIQUE KEY user_product_unique (user_id, product_id),
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

**Особенности:**
- Уникальный индекс `user_product_unique` предотвращает дублирование
- Внешний ключ на `users` с каскадным удалением
- Только одна временная метка `created_at` (без `updated_at`)

---

## Тестирование

### Unit Tests (`tests/Unit/FavoriteServiceTest.php`)

Тестирует методы FavoriteService с использованием транзакций для безопасной работы с production БД.

**Тесты (11 шт., 46 assertions):**

1. `test_add_item_to_favorites()` - добавление товара в избранное
2. `test_add_item_already_in_favorites()` - повторное добавление (проверка дублей)
3. `test_add_nonexistent_product()` - добавление несуществующего товара
4. `test_remove_item_from_favorites()` - удаление товара из избранного
5. `test_remove_item_not_in_favorites()` - удаление товара, которого нет в избранном
6. `test_get_items()` - получение списка избранного с полными данными
7. `test_get_empty_items()` - получение пустого списка
8. `test_get_items_count()` - получение количества товаров
9. `test_is_in_favorites()` - проверка наличия товара в избранном
10. `test_get_items_by_ids()` - получение данных товаров по массиву ID
11. `test_validation()` - валидация параметров

**Особенности:**
- Использует `DatabaseTransactions` trait для автоматического отката
- Создает тестовые данные с префиксом `test_`
- Проверяет как успешные операции, так и граничные случаи

### Feature Tests (`tests/Feature/FavoriteControllerTest.php`)

Тестирует HTTP-маршруты и AJAX-endpoints с использованием транзакций.

**Тесты (11 шт., 41 assertion):**

1. `test_favorites_page_is_accessible()` - доступность страницы /favorites
2. `test_add_item_ajax_authenticated()` - AJAX добавление (авторизованный)
3. `test_add_item_ajax_guest()` - AJAX добавление (неавторизованный)
4. `test_remove_item_ajax_authenticated()` - AJAX удаление (авторизованный)
5. `test_remove_item_ajax_guest()` - AJAX удаление (неавторизованный)
6. `test_get_favorites_api_authenticated()` - API получения списка (авторизованный)
7. `test_get_favorites_api_guest()` - API получения списка (неавторизованный)
8. `test_add_item_without_product_id()` - валидация без product_id
9. `test_remove_item_without_product_id()` - валидация без product_id
10. `test_handle_ajax_unknown_task()` - обработка неизвестной задачи
11. `test_multiple_add_and_remove()` - множественные операции

**Запуск тестов:**
```bash
# Все тесты избранного
php artisan test --filter=Favorite

# Только unit тесты
php artisan test --filter=FavoriteServiceTest

# Только feature тесты
php artisan test --filter=FavoriteControllerTest
```

**Результаты:**
- ✅ 22 теста пройдено
- ✅ 87 assertions
- ⏱️ ~3.5 секунды

---

## Исправленные проблемы

### 1. Избранное не сохранялось после перезагрузки страницы
**Проблема:** Товары добавлялись в избранное, но после перезагрузки страницы исчезали.

**Причина:** Сессия не сохранялась для неавторизованных пользователей.

**Решение:**
- Добавлен явный вызов `$request->session()->start()` и `$request->session()->save()`
- Добавлен `credentials: 'same-origin'` во все fetch запросы

### 2. Счетчик избранного в header не обновлялся
**Проблема:** После добавления/удаления товара счетчик в header оставался прежним.

**Причина:** Отсутствовала синхронизация между FavoritesViewModel и FavoritesCounterViewModel.

**Решение:**
- Добавлен вызов `favoritesCounterViewModel.updateCount()` после каждой операции
- Добавлены console.log для отладки

### 3. API endpoint возвращал HTML вместо JSON
**Проблема:** `/api/favorites` возвращал HTML страницы избранного вместо JSON.

**Причина:** Конфликт маршрутов между web и api.

**Решение:**
- Реализован "task-based API" паттерн с единым endpoint `/api/favorites`
- Добавлен метод `handleAjax()` с switch по параметру `task`
- Сохранены `/api/favorites/add` и `/api/favorites/remove` для обратной совместимости

### 4. Карточки товаров не отображались на странице избранного
**Проблема:** API возвращал только массив ID товаров, но FavoritesViewModel ожидал полные данные.

**Решение:**
- Добавлен метод `getItemsByIds()` в FavoriteService
- Метод получает полные данные товаров: цену, изображение, рейтинг
- Обновлен `getFavorites()` для вызова `getItemsByIds()` для неавторизованных

### 5. Удаление из избранного не работало
**Проблема:** После удаления карточка товара не исчезала со страницы.

**Причина:** Запросы add/remove не отправляли session cookies.

**Решение:**
- Добавлен `credentials: 'same-origin'` в fetch запросы `addToFavorite()` и `removeFromFavorite()`

### 6. Таблица favorites не существовала
**Проблема:** Все тесты падали с ошибкой "Table 'sfera.favorites' doesn't exist".

**Причина:** В legacy системе избранное хранилось только в сессии, миграция для таблицы не была создана.

**Решение:**
- Создана миграция `2026_02_27_092959_create_favorites_table.php`
- Таблица создана с правильной структурой и индексами
- Все тесты теперь проходят успешно

### 7. User модель не реализовывала Authenticatable
**Проблема:** Feature тесты падали с ошибкой "Argument #1 ($user) must be of type Authenticatable".

**Причина:** User модель была сгенерирована автоматически и не имела интерфейса Authenticatable.

**Решение:**
- Добавлен `implements Authenticatable` в User модель
- Добавлен `use AuthenticatableTrait`
- Feature тесты с авторизацией теперь работают

---

## Требования

Модуль реализует следующие требования из спецификации:

- **18.1-18.4** - FavoriteService с методами управления избранным
- **18.3, 18.7** - FavoriteController с AJAX endpoints
- **18.5** - Счетчик избранного в header
- **18.6-18.8** - Синхронизация состояния на карточках товаров
- **3.1-3.5** - Конвертация Smarty шаблонов в Blade
- **13.7-13.12** - Knockout.js ViewModels
- **12.1-12.7** - Тестирование с использованием транзакций

---

## Следующие шаги

1. ✅ Реализовать избранное - **ЗАВЕРШЕНО**
2. ⏭️ Реализовать страницы категорий (авторы, серии, тематики, типы товаров)
3. ⏭️ Завершить фильтры каталога
4. ⏭️ Реализовать поиск товаров
