# Отладка функционала карточек товаров на главной странице

## Проблема

Карточки товаров на главной странице (витрине) не имеют полного функционала:
1. Кнопка "Добавить в избранное" не работает - нет AJAX запроса, счетчик не обновляется, иконка сердечка не меняет цвет
2. Кнопка "В корзину" работает частично - счетчик корзины в header обновляется, НО кнопка не меняется на контролы количества (+/- кнопки)

## Архитектура

### Компоненты главной страницы с карточками товаров

1. **TOP-10 слайдер** (`resources/views/components/showcase/top10-slider.blade.php`)
   - Отображает 10 товаров в слайдере
   - Каждая карточка имеет кнопку избранного и кнопку "В корзину"
   - Класс карточки: `.top10-product-card`
   - Класс кнопки избранного: `.product-favorite .top10-favorite-btn`

2. **Случайные товары** (`resources/views/components/showcase/random-products.blade.php`)
   - Отображает 12 случайных товаров в сетке
   - Каждая карточка имеет кнопку избранного и кнопку "В корзину"
   - Класс карточки: `.product-card`
   - Класс кнопки избранного: `.product-favorite`

### JavaScript файлы

1. **catalog.js** (`public/assets/sfera/js/catalog.js`)
   - Содержит все функции для работы с карточками товаров
   - Функции:
     - `setupFavoriteButtons()` - инициализация кнопок избранного
     - `addToFavorites(productId, btn)` - добавление в избранное
     - `removeFromFavorites(productId, btn)` - удаление из избранного
     - `setupAddToCart()` - инициализация кнопок "В корзину"
     - `addToCart(productId, quantity)` - добавление в корзину
     - `setupQuantityControls()` - инициализация контролов количества
     - `updateCartItemQuantity(productId, quantity)` - обновление количества
     - `setupBuyAll()` - инициализация кнопок "Купить всё"
     - `refreshCartState()` - загрузка актуального состояния корзины с сервера
     - `updateProductCardsFromCart(cartData)` - обновление состояния карточек на основе данных корзины
   - Автоматическая инициализация при загрузке DOM

2. **showcase-init.js** (`public/assets/sfera/js/showcase-init.js`)
   - Диагностический скрипт для отладки
   - Выводит в консоль информацию о найденных элементах
   - Проверяет наличие необходимых функций из catalog.js

### Контроллер

**ShowcaseController** (`app/Http/Controllers/ShowcaseController.php`)
- Метод `index()` передает в view:
  - `$cart` - данные корзины из сессии
  - `$favorites` - данные избранного из сессии (с преобразованием формата)
  - `$top10Products` - TOP-10 товаров
  - `$randomProducts` - случайные товары

## Чек-лист для отладки

### 1. Проверка загрузки скриптов

Откройте главную страницу и проверьте в DevTools (F12) → Network:

- [ ] `catalog.js` загружается без ошибок (статус 200)
- [ ] `showcase-init.js` загружается без ошибок (статус 200)
- [ ] `carousel.js` загружается без ошибок (статус 200)
- [ ] `top10-slider.js` загружается без ошибок (статус 200)

### 2. Проверка консоли браузера

Откройте DevTools (F12) → Console и проверьте:

- [ ] Нет JavaScript ошибок (красных сообщений)
- [ ] Есть сообщение "=== Showcase initialization started ==="
- [ ] Есть сообщение "All required functions are available."
- [ ] Есть сообщение "=== Showcase initialization completed ==="
- [ ] Выводится количество найденных элементов:
  - Favorite buttons: должно быть > 0 (обычно 22: 10 в TOP-10 + 12 в случайных)
  - Add to cart buttons: должно быть > 0
  - Quantity controls: должно быть > 0
  - Buy all buttons: должно быть > 0

### 3. Проверка HTML структуры

Откройте DevTools (F12) → Elements и проверьте:

#### Кнопка избранного в TOP-10:
```html
<button class="product-favorite top10-favorite-btn" 
        data-product-id="00-00006779" 
        title="Добавить в избранное">
    <svg>...</svg>
</button>
```

- [ ] Есть класс `product-favorite`
- [ ] Есть атрибут `data-product-id` с ID товара
- [ ] ID товара не пустой

#### Кнопка избранного в случайных товарах:
```html
<button class="product-favorite" 
        data-product-id="00-00006779" 
        title="Добавить в избранное">
    <svg>...</svg>
</button>
```

- [ ] Есть класс `product-favorite`
- [ ] Есть атрибут `data-product-id` с ID товара
- [ ] ID товара не пустой

#### Кнопка "В корзину":
```html
<button class="btn-add-to-cart" 
        data-product-id="00-00006779" 
        type="button">
    <svg>...</svg>
    <span>В корзину</span>
</button>
```

- [ ] Есть класс `btn-add-to-cart`
- [ ] Есть атрибут `data-product-id` с ID товара
- [ ] ID товара не пустой

#### Контрол количества:
```html
<div class="product-quantity-control hidden" 
     data-product-id="00-00006779">
    <button class="qty-btn qty-minus" data-product-id="00-00006779">−</button>
    <input type="number" class="qty-input" value="1" min="1" max="99" 
           data-product-id="00-00006779">
    <button class="qty-btn qty-plus" data-product-id="00-00006779">+</button>
</div>
```

- [ ] Есть класс `product-quantity-control`
- [ ] Есть класс `hidden` (должен быть скрыт по умолчанию)
- [ ] Есть атрибут `data-product-id` с ID товара

### 4. Проверка обработчиков событий

Откройте DevTools (F12) → Console и выполните:

```javascript
// Проверка количества кнопок избранного
document.querySelectorAll('.product-favorite').length

// Проверка первой кнопки избранного
const btn = document.querySelector('.product-favorite');
console.log('Product ID:', btn.getAttribute('data-product-id'));
console.log('Has onclick:', btn.onclick !== null);
console.log('Event listeners:', getEventListeners(btn)); // Chrome only

// Проверка кнопок "В корзину"
document.querySelectorAll('.btn-add-to-cart').length

// Проверка контролов количества
document.querySelectorAll('.product-quantity-control').length
```

- [ ] Количество кнопок избранного > 0
- [ ] У кнопок есть обработчики событий (event listeners)
- [ ] Product ID не пустой

### 5. Тестирование функционала избранного

1. Откройте главную страницу
2. Откройте DevTools (F12) → Console
3. Кликните на кнопку "Добавить в избранное" (сердечко)

Проверьте в консоли:

- [ ] Нет ошибок JavaScript
- [ ] Есть AJAX запрос к `/api/favorites/add` (Network tab)
- [ ] Запрос возвращает статус 200
- [ ] Ответ содержит JSON с данными избранного
- [ ] Иконка сердечка меняет цвет на розовый (#ff0080)
- [ ] Счетчик избранного в header обновляется
- [ ] Появляется уведомление "Товар добавлен в избранное"

4. Кликните на кнопку еще раз (удаление из избранного)

Проверьте:

- [ ] Есть AJAX запрос к `/api/favorites/remove`
- [ ] Иконка сердечка возвращается к исходному цвету
- [ ] Счетчик избранного уменьшается
- [ ] Появляется уведомление "Товар удален из избранного"

### 6. Тестирование функционала корзины

1. Откройте главную страницу
2. Откройте DevTools (F12) → Console
3. Кликните на кнопку "В корзину"

Проверьте в консоли:

- [ ] Нет ошибок JavaScript
- [ ] Есть AJAX запрос к `/api/cart` с task=put_item (Network tab)
- [ ] Запрос возвращает статус 200
- [ ] Ответ содержит JSON с данными корзины
- [ ] Кнопка "В корзину" скрывается (`display: none`)
- [ ] Контрол количества появляется (класс `hidden` удаляется)
- [ ] Счетчик корзины в header обновляется
- [ ] Появляется уведомление "Товар добавлен в корзину (1 шт.)"

4. Проверьте контролы количества:

- [ ] Кнопка "+" увеличивает количество
- [ ] Кнопка "−" уменьшает количество
- [ ] При каждом изменении отправляется AJAX запрос к `/api/cart` с task=update_item
- [ ] Счетчик корзины обновляется

### 7. Проверка данных сессии

Откройте DevTools (F12) → Application → Cookies и проверьте:

- [ ] Есть cookie `laravel_session`
- [ ] Cookie не пустой

Проверьте в консоли PHP (через Tinker или dd()):

```php
// В ShowcaseController::index() добавьте временно:
dd([
    'cart' => session('cart'),
    'favorites' => session('favorites'),
    'favorites_transformed' => $favorites
]);
```

- [ ] `session('cart')` содержит данные корзины
- [ ] `session('favorites')` содержит данные избранного
- [ ] `$favorites` правильно преобразован в формат `['items' => [...]]`

## Возможные проблемы и решения

### Проблема 1: Кнопки избранного не реагируют на клики

**Возможные причины:**
1. JavaScript не загружен или загружен с ошибкой
2. Обработчики событий не привязаны к кнопкам
3. CSS блокирует клики (pointer-events: none)
4. Кнопки создаются динамически после инициализации

**Решение:**
1. Проверьте консоль на ошибки JavaScript
2. Проверьте, что `setupFavoriteButtons()` вызывается после загрузки DOM
3. Проверьте CSS для `.product-favorite` - не должно быть `pointer-events: none`
4. Убедитесь, что кнопки существуют в DOM до вызова `setupFavoriteButtons()`

### Проблема 2: Кнопка "В корзину" не меняется на контролы количества

**Возможные причины:**
1. Контрол количества не найден в DOM
2. Классы не совпадают с селекторами в JavaScript
3. AJAX запрос не возвращает данные корзины
4. Функция `updateProductCardsFromCart()` не вызывается

**Решение:**
1. Проверьте, что контрол количества существует в HTML с правильным `data-product-id`
2. Проверьте, что класс `hidden` правильно удаляется/добавляется
3. Проверьте ответ от `/api/cart` - должен содержать `items` с ID товаров
4. Добавьте `console.log` в `setupAddToCart()` для отладки

### Проблема 3: Счетчики не обновляются

**Возможные причины:**
1. ViewModel не инициализирована
2. AJAX запрос не возвращает правильные данные
3. Функция `updateCount()` не вызывается

**Решение:**
1. Проверьте, что `cartCounterViewModel` и `favoritesCounterViewModel` определены глобально
2. Проверьте ответ от API - должен содержать `total_cart_amount` и `favorites_count`
3. Добавьте `console.log` в функции `addToCart()` и `addToFavorites()`

### Проблема 4: Данные избранного не передаются правильно

**Возможные причины:**
1. Формат избранного не преобразован в контроллере
2. В шаблоне используется неправильный ключ для проверки

**Решение:**
1. Убедитесь, что в `ShowcaseController::index()` есть преобразование формата избранного
2. В шаблоне используйте `isset($favorites['items'][$product->product_id])`
3. Проверьте, что `$favorites` передается в компоненты

## Следующие шаги

1. Откройте главную страницу в браузере
2. Откройте DevTools (F12)
3. Пройдите по чек-листу выше
4. Запишите все найденные проблемы
5. Исправьте проблемы по одной
6. Повторите тестирование

## Ссылки

- Эталонная реализация: `CatalogController::index()` и `resources/views/catalog/index.blade.php`
- Документация по требованиям: `.kiro/steering/product-card-data-requirements.md`
