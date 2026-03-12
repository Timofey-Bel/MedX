# Cart Knockout Integration Fix - Bugfix Design

## Overview

Корзина не работает из-за отсутствия интеграции Knockout.js модели из legacy системы. Товары не добавляются, счетчики не обновляются, страница корзины остается пустой. Проблема связана с тем, что после миграции на Laravel:
- Модель корзины (`model_cart.js.tpl`) не подключена в layouts
- AJAX endpoints не реализованы в CartController
- Глобальная переменная `server_cart.data` не инициализирована
- Счетчики в header не привязаны к Knockout.js модели

Стратегия исправления: адаптировать legacy модульную модель корзины для Laravel, убрав Smarty синтаксис и реализовав необходимые AJAX endpoints. Сохранить структуру legacy модели для обратной совместимости.

## Glossary

- **Bug_Condition (C)**: Условие, при котором корзина не работает - отсутствие интеграции Knockout.js модели и AJAX endpoints
- **Property (P)**: Желаемое поведение - корзина должна работать с добавлением товаров, обновлением счетчиков и отображением содержимого
- **Preservation**: Существующая функциональность каталога, поиска, навигации, которая должна остаться неизменной
- **model_cart**: Глобальная Knockout.js модель корзины из `public/js/models/cart_new/model_cart.js.tpl`
- **server_cart.data**: Глобальная переменная с состоянием корзины, инициализируемая на каждой странице
- **CartController**: Laravel контроллер для обработки AJAX запросов корзины
- **ko.mapping**: Knockout.js плагин для автоматического преобразования данных в observables
- **Модульная архитектура**: Каждый метод модели в отдельном файле (put_into_cart.js, remove_from_cart.js и т.д.)

## Bug Details

### Fault Condition

Баг проявляется когда пользователь пытается взаимодействовать с корзиной (добавить товар, изменить количество, удалить товар). Система не реагирует, так как:
1. Модель корзины не подключена в layout
2. AJAX endpoints возвращают 404/500 ошибки
3. Глобальная переменная `server_cart.data` не инициализирована
4. Счетчики не привязаны к Knockout.js модели

**Formal Specification:**
```
FUNCTION isBugCondition(input)
  INPUT: input of type UserAction (click, input change, page load)
  OUTPUT: boolean
  
  RETURN (input.action == "add_to_cart" OR 
          input.action == "remove_from_cart" OR 
          input.action == "update_quantity" OR
          input.action == "view_cart" OR
          input.action == "page_load")
         AND (model_cart NOT initialized OR 
              server_cart.data NOT initialized OR
              AJAX_endpoint NOT implemented)
END FUNCTION
```

### Examples

- **Добавление товара**: Пользователь кликает "В корзину" на карточке товара → ничего не происходит, счетчик остается 0, товар не добавлен
- **Просмотр корзины**: Пользователь переходит на /cart → страница показывает "Корзина пуста" даже если товары были добавлены в сессию
- **Изменение количества**: Пользователь кликает +/- на странице корзины → количество не изменяется, нет реакции
- **Загрузка страницы**: При загрузке любой страницы → `server_cart.data` undefined, модель не инициализирована, счетчики показывают 0

## Expected Behavior

### Preservation Requirements

**Unchanged Behaviors:**
- Просмотр каталога товаров должен продолжать работать без изменений в производительности
- Поиск, фильтры, навигация должны продолжать работать независимо от корзины
- Страницы без товаров (статические, профиль) должны работать без необходимости инициализации модели корзины
- Избранное и другие списки товаров должны работать независимо от состояния корзины
- Другие AJAX запросы (не связанные с корзиной) должны обрабатываться без конфликтов
- Существующая структура данных корзины в сессии Laravel должна сохраниться

**Scope:**
Все взаимодействия, НЕ связанные с корзиной, должны быть полностью не затронуты этим исправлением. Это включает:
- Навигацию по сайту
- Просмотр товаров
- Использование поиска и фильтров
- Работу с профилем пользователя
- Работу с избранным

## Hypothesized Root Cause

На основе анализа кода, наиболее вероятные причины:

1. **Отсутствие подключения модели**: Файл `model_cart.js.tpl` использует Smarty синтаксис (`~~include file="..."~`) и не подключен в Laravel layout. Blade версия модели существует в `resources/views/js/models/cart_new/model_cart.blade.php`, но не включена в `layouts/app.blade.php`.

2. **Не реализованы AJAX endpoints**: CartController имеет только метод `index()` для отображения страницы. Отсутствуют методы для обработки AJAX запросов с параметром `task`:
   - `put_item` - добавление товара
   - `delete_product` - удаление товара
   - `update_amount` - изменение количества
   - `get_cart` - получение состояния корзины
   - `apply_promocode` - применение промокода
   - `cancel_promocode` - отмена промокода
   - `update_item_selected` - изменение выбора товара

3. **Не инициализирована server_cart.data**: Глобальная переменная `server_cart.data` должна инициализироваться на каждой странице с текущим состоянием корзины из сессии Laravel. Сейчас она не инициализируется, поэтому модель работает с пустыми данными.

4. **Счетчики не привязаны**: В header есть элементы для отображения счетчиков, но они не привязаны к Knockout.js модели через `data-bind`.

## Correctness Properties

Property 1: Fault Condition - Cart Functionality Works

_For any_ user action involving the cart (adding items, removing items, updating quantities, viewing cart, page load), the fixed system SHALL initialize the Knockout.js model, provide AJAX endpoints, initialize server_cart.data, and bind counters, resulting in correct cart functionality with visual feedback and updated state.

**Validates: Requirements 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 2.7, 2.8**

Property 2: Preservation - Non-Cart Functionality

_For any_ user action that does NOT involve the cart (browsing catalog, using search/filters, navigating, viewing profile, working with favorites), the fixed system SHALL produce exactly the same behavior as before, preserving all existing functionality without performance degradation or conflicts.

**Validates: Requirements 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 3.7**

## Fix Implementation

### Changes Required

Предполагая, что наш анализ корректен:

**File 1**: `resources/views/layouts/app.blade.php`

**Changes**:
1. **Добавить инициализацию server_cart.data**: В секции `<head>` или перед подключением модели добавить скрипт, который инициализирует глобальную переменную с данными из сессии Laravel
   ```php
   <script>
   var server_cart = {
       data: @json(session('cart_data', [
           'items' => [],
           'total_cart_sum' => 0,
           'total_cart_amount' => 0,
           'cart_sum' => 0,
           'cart_discount' => 0,
           'promocode' => ''
       ]))
   };
   </script>
   ```

2. **Подключить модель корзины**: В секции `@stack('scripts')` или перед ней добавить подключение blade версии модели
   ```php
   @include('js.models.cart_new.model_cart')
   ```

3. **Подключить ko.mapping**: Добавить подключение knockout-mapping.js после knockout.js
   ```html
   <script src="{{ asset('js/knockoutjs/build/output/knockout-latest.js') }}"></script>
   ```

**File 2**: `app/Http/Controllers/CartController.php`

**Function**: Добавить новые методы для обработки AJAX запросов

**Specific Changes**:
1. **Добавить метод handleAjax()**: Единый метод для обработки всех AJAX запросов с параметром `task`
   - Проверять параметр `task` и вызывать соответствующий метод
   - Возвращать JSON ответ с обновленным состоянием корзины

2. **Добавить метод putItem()**: Добавление товара в корзину
   - Получать `item` из request (JSON с guid и product_amount)
   - Добавлять/обновлять товар в сессии
   - Загружать данные товара из БД
   - Возвращать полное состояние корзины

3. **Добавить метод deleteProduct()**: Удаление товара из корзины
   - Получать `guid` из request
   - Удалять товар из сессии
   - Возвращать обновленное состояние корзины

4. **Добавить метод updateAmount()**: Изменение количества товара
   - Получать `guid` и `amount` из request
   - Обновлять количество в сессии
   - Возвращать обновленное состояние корзины

5. **Добавить метод getCart()**: Получение текущего состояния корзины
   - Загружать данные из сессии
   - Возвращать полное состояние корзины

6. **Добавить вспомогательный метод getCartData()**: Формирование структуры данных корзины
   - Загружать товары из БД по ID из сессии
   - Формировать объект items в формате {guid: {id, name, cost, product_amount, img_url, ...}}
   - Вычислять total_cart_sum, total_cart_amount, cart_sum
   - Возвращать структуру, совместимую с legacy моделью

**File 3**: `routes/web.php`

**Changes**:
1. **Добавить POST route для AJAX**: Добавить маршрут для обработки AJAX запросов
   ```php
   Route::post('/cart/', [CartController::class, 'handleAjax'])->name('cart.ajax');
   ```

**File 4**: `resources/views/components/header.blade.php`

**Changes**:
1. **Добавить data-bind к счетчикам**: Привязать счетчики корзины к Knockout.js модели
   - Найти элементы с классами `.cart-counter`, `.total_cart_amount`
   - Добавить `data-bind="text: formatted_total_cart_amount"`
   - Убедиться, что bindings применяются отдельно для счетчиков (не через model_cart)

2. **Создать отдельную ViewModel для счетчиков**: Создать простую ViewModel `cartCounterViewModel`, которая подписывается на изменения `model_cart.total_cart_amount()`
   - Это избежит конфликтов с множественными applyBindings

**File 5**: `app/Providers/AppServiceProvider.php` или создать новый `CartServiceProvider`

**Changes**:
1. **Добавить View Composer для cart_data**: Создать composer, который на каждом запросе формирует `cart_data` из сессии и передает во все views
   ```php
   View::composer('*', function ($view) {
       $cart = session('cart', []);
       $cartData = app(CartService::class)->getCartData($cart);
       $view->with('cart_data', $cartData);
   });
   ```

2. **Создать CartService**: Вынести логику работы с корзиной в отдельный сервис для переиспользования
   - Метод `getCartData($cart)` - формирование структуры данных
   - Метод `addItem($guid, $amount)` - добавление товара
   - Метод `removeItem($guid)` - удаление товара
   - Метод `updateAmount($guid, $amount)` - изменение количества

## Testing Strategy

### Validation Approach

Стратегия тестирования следует двухфазному подходу: сначала выявить контрпримеры, демонстрирующие баг на неисправленном коде, затем проверить, что исправление работает корректно и сохраняет существующее поведение.

### Exploratory Fault Condition Checking

**Goal**: Выявить контрпримеры, демонстрирующие баг ДО реализации исправления. Подтвердить или опровергнуть анализ первопричины. Если опровергнем, нужно будет пересмотреть гипотезу.

**Test Plan**: Написать тесты, которые симулируют действия пользователя с корзиной и проверяют, что модель инициализирована, AJAX endpoints работают, server_cart.data инициализирована. Запустить эти тесты на НЕИСПРАВЛЕННОМ коде, чтобы наблюдать ошибки и понять первопричину.

**Test Cases**:
1. **Model Not Loaded Test**: Проверить, что `window.model_cart` undefined на странице (провалится на неисправленном коде)
2. **Server Cart Not Initialized Test**: Проверить, что `window.server_cart.data` undefined или пустой (провалится на неисправленном коде)
3. **AJAX Endpoint Missing Test**: Отправить POST запрос к `/cart/` с `task=get_cart` → получить 404 или 500 (провалится на неисправленном коде)
4. **Counter Not Bound Test**: Проверить, что счетчик в header не обновляется при изменении корзины (провалится на неисправленном коде)

**Expected Counterexamples**:
- `model_cart` не определен в глобальной области
- `server_cart.data` не инициализирован или пустой
- AJAX запросы возвращают ошибки 404/500
- Счетчики показывают 0 или не обновляются
- Возможные причины: модель не подключена, endpoints не реализованы, данные не инициализированы

### Fix Checking

**Goal**: Проверить, что для всех входных данных, где выполняется условие бага, исправленная функция производит ожидаемое поведение.

**Pseudocode:**
```
FOR ALL input WHERE isBugCondition(input) DO
  result := cart_system_fixed(input)
  ASSERT expectedBehavior(result)
END FOR
```

**Testing Approach**: Использовать комбинацию unit тестов и интеграционных тестов для проверки всех сценариев работы с корзиной.

**Test Cases**:
1. **Add Item Test**: Добавить товар через AJAX → проверить, что товар добавлен в сессию, счетчик обновлен, ответ содержит корректные данные
2. **Remove Item Test**: Удалить товар через AJAX → проверить, что товар удален из сессии, счетчик обновлен
3. **Update Amount Test**: Изменить количество через AJAX → проверить, что количество обновлено в сессии
4. **Get Cart Test**: Получить состояние корзины через AJAX → проверить, что возвращаются корректные данные
5. **Page Load Test**: Загрузить страницу → проверить, что `server_cart.data` инициализирован с данными из сессии
6. **Model Initialization Test**: Проверить, что `model_cart` инициализирован и доступен глобально
7. **Counter Binding Test**: Добавить товар → проверить, что счетчик в header обновился

### Preservation Checking

**Goal**: Проверить, что для всех входных данных, где условие бага НЕ выполняется, исправленная функция производит тот же результат, что и оригинальная.

**Pseudocode:**
```
FOR ALL input WHERE NOT isBugCondition(input) DO
  ASSERT original_system(input) = fixed_system(input)
END FOR
```

**Testing Approach**: Property-based тестирование рекомендуется для проверки сохранения поведения, так как:
- Автоматически генерирует множество тестовых случаев по всему домену входных данных
- Выявляет граничные случаи, которые могут быть упущены в ручных unit тестах
- Предоставляет сильные гарантии, что поведение не изменилось для всех не-багованных входных данных

**Test Plan**: Наблюдать поведение на НЕИСПРАВЛЕННОМ коде для действий, не связанных с корзиной, затем написать property-based тесты, фиксирующие это поведение.

**Test Cases**:
1. **Catalog Browsing Preservation**: Проверить, что просмотр каталога работает так же, как до исправления
2. **Search Preservation**: Проверить, что поиск работает так же, как до исправления
3. **Navigation Preservation**: Проверить, что навигация работает так же, как до исправления
4. **Profile Preservation**: Проверить, что страницы профиля работают так же, как до исправления
5. **Favorites Preservation**: Проверить, что избранное работает так же, как до исправления
6. **Other AJAX Preservation**: Проверить, что другие AJAX запросы работают так же, как до исправления

### Unit Tests

- Тестировать каждый метод CartController отдельно (putItem, deleteProduct, updateAmount, getCart)
- Тестировать CartService методы с mock данными
- Тестировать формирование структуры cart_data
- Тестировать граничные случаи (пустая корзина, несуществующий товар, отрицательное количество)

### Property-Based Tests

- Генерировать случайные состояния корзины и проверять, что AJAX endpoints возвращают корректные данные
- Генерировать случайные последовательности действий (добавить, удалить, изменить) и проверять консистентность состояния
- Тестировать, что все не-корзиночные действия продолжают работать при различных состояниях корзины

### Integration Tests

- Тестировать полный flow: загрузка страницы → добавление товара → просмотр корзины → изменение количества → удаление
- Тестировать работу счетчиков при различных действиях с корзиной
- Тестировать работу корзины в разных контекстах (главная страница, каталог, страница товара, корзина, checkout)
- Тестировать визуальную обратную связь (анимация добавления товара)
