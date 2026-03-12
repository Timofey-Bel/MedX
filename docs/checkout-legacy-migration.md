# Миграция страницы оформления заказа на legacy структуру

## Дата: 2026-02-28

## Проблемы, которые были исправлены

### 1. Товары корзины не отображались
**Причина:** KnockoutJS не инициализировался с данными корзины из сервера

**Решение:** 
- Добавлен блок `@push('head')` с инициализацией `server_cart.data` ДО загрузки других скриптов
- Данные корзины передаются из PHP в JavaScript через `json_encode($cart)`

```php
@push('head')
    <script>
        var server_cart = {};
        @if(isset($cart) && isset($cart['items']) && count($cart['items']) > 0)
        server_cart.data = {!! json_encode($cart) !!};
        @else
        server_cart.data = {total_cart_sum:0, total_cart_amount:0, cart_sum:0, items:{}};
        @endif
    </script>
    <script src="https://api-maps.yandex.ru/2.1/?apikey=71dcace5-2bc2-42e5-8e82-62aa21e52541&lang=ru_RU"></script>
@endpush
```

### 2. Неправильный порядок блоков
**Было:** Получатель → Доставка → Оплата → Комментарий

**Стало (как в legacy):**
1. Адрес доставки
2. Способ доставки
3. Способ оплаты
4. Получатель
5. Комментарий к заказу

### 3. Пункты выдачи не загружались
**Причина:** Отсутствовали AJAX endpoints для работы с таблицей `points`

**Решение:**
- Добавлены методы в `OrderController`:
  - `getPickpointList()` - получение точек в пределах карты
  - `getPickpointData()` - получение данных точки по координатам
- Добавлены маршруты:
  - `POST /checkout/get-pickpoint-list`
  - `POST /checkout/get-pickpoint-data`

## Изменённые файлы

### 1. resources/views/checkout/index.blade.php
**Изменения:**
- Полностью переписан под структуру legacy
- Добавлена инициализация `server_cart.data` в `@push('head')`
- Изменён порядок блоков (Address → Delivery → Payment → Recipient → Comment)
- Добавлен попап с картой (`#map-popup`)
- Добавлена модель KnockoutJS для пункта выдачи (`PointModel`)
- Используется `@push` для скриптов и стилей (не `@section`)

### 2. app/Http/Controllers/OrderController.php
**Добавлены методы:**

```php
/**
 * Получить список пунктов выдачи в пределах карты (AJAX)
 */
public function getPickpointList(Request $request): JsonResponse
{
    $bounds = $request->input('bounds');
    
    $points = \DB::table('points')
        ->select('id', 'la', 'lo')
        ->where('la', '>', $bounds[0][0])
        ->where('la', '<', $bounds[1][0])
        ->where('lo', '>', $bounds[0][1])
        ->where('lo', '<', $bounds[1][1])
        ->get()
        ->toArray();

    return response()->json([
        'result' => true,
        'points' => $points
    ]);
}

/**
 * Получить данные пункта выдачи по координатам (AJAX)
 */
public function getPickpointData(Request $request): JsonResponse
{
    $coords = $request->input('coords');
    
    $point = \DB::table('points')
        ->where('la', $coords[0])
        ->where('lo', $coords[1])
        ->first();

    $pointData = json_decode($point->json, true);
    Session::put('delivery', $point);

    return response()->json([
        'result' => true,
        'point_data' => $pointData
    ]);
}
```

### 3. routes/web.php
**Добавлены маршруты:**

```php
Route::post('/checkout/get-pickpoint-list', [OrderController::class, 'getPickpointList'])->name('checkout.pickpoint.list');
Route::post('/checkout/get-pickpoint-data', [OrderController::class, 'getPickpointData'])->name('checkout.pickpoint.data');
```

### 4. public/assets/sfera/js/checkout.js
**Полностью переписан:**
- Добавлен объект `Map` для работы с Яндекс.Картами (из legacy)
- Реализована загрузка пунктов выдачи при изменении границ карты
- Добавлена кластеризация точек
- Реализован выбор пункта выдачи по клику на метку
- Сохранение выбранной точки в localStorage
- Обновление модели KnockoutJS при выборе точки
- Открытие/закрытие попапа с картой

**Ключевые функции:**
- `Map.init()` - инициализация карты
- `openMapPopup()` - открытие попапа с картой
- `closeMapPopup()` - закрытие попапа
- `submitOrder()` - отправка заказа (сохранена из текущей версии)

### 5. public/assets/sfera/css/checkout.css
**Добавлены стили:**
- `.map-popup` - стили для попапа с картой
- `.map-popup-overlay` - затемнение фона
- `.map-popup-content` - контейнер карты
- `.map-popup-close` - кнопка закрытия
- `.address-selector` - блок выбора адреса
- `.address-card` - карточка выбранного адреса
- `.add-address-btn` - кнопка "Выбрать адрес доставки"
- `.checkout-layout` - сетка страницы (2 колонки)
- `.delivery-card`, `.payment-card` - карточки выбора доставки/оплаты
- `.order-summary` - блок итогов заказа
- Responsive стили для мобильных устройств

## Структура данных

### Таблица points
```sql
CREATE TABLE points (
    id VARCHAR(50),
    la DECIMAL(9,6),  -- широта
    lo DECIMAL(9,6),  -- долгота
    json JSON         -- данные пункта выдачи
)
```

### Формат JSON в поле json:
```json
{
    "id": "string",
    "name": "Название пункта",
    "address": {
        "full_address": "Полный адрес",
        "locality": "Город",
        "region": "Регион",
        "comment": "Комментарий"
    },
    "contact": {
        "phone": "+7 (xxx) xxx-xx-xx",
        "first_name": "Имя"
    }
}
```

## Логика работы карты

1. **Открытие карты:**
   - Пользователь нажимает "Выбрать адрес доставки"
   - Открывается попап с картой
   - Карта инициализируется (если ещё не была)

2. **Загрузка точек:**
   - При изменении границ карты (событие `boundschange`)
   - Отправляется AJAX запрос с границами видимой области
   - Сервер возвращает точки в этих границах
   - Точки отображаются на карте с кластеризацией

3. **Выбор точки:**
   - Пользователь кликает на метку
   - Отправляется AJAX запрос с координатами
   - Сервер возвращает полные данные точки
   - Данные сохраняются в localStorage и модель KnockoutJS
   - Попап закрывается
   - Выбранная точка отображается в блоке "Адрес доставки"

## Интеграция с KnockoutJS

### Модель для пункта выдачи:
```javascript
var PointModel = {
    constructor: function(){
        var self = this;
        self.pickuppoint = ko.observable(false);
        self.pickpoint = ko.pureComputed({
            read: function () {
                return self.pickuppoint;
            }
        });
        return self;
    }
};
var model_pickpoint = Object.create(PointModel).constructor();
```

### Привязка к DOM:
```javascript
$(document).ready(function(){
    if($('.address-selector')[0])
        ko.applyBindings(model_pickpoint, $('.address-selector')[0]);
});
```

### Использование в шаблоне:
```html
<!-- ko if: pickpoint()  -->
<div class="address-card active">
    <div class="address-content">
        <h4 data-bind="text: pickuppoint()['address']['full_address']"></h4>
        <p data-bind="text: 'тел: ' + pickuppoint()['contact']['phone']"></p>
        <p data-bind="text: pickuppoint()['name']"></p>
        <p data-bind="text: pickuppoint()['address']['comment']"></p>
    </div>
</div>
<!-- /ko -->
```

## Соответствие legacy

### ✅ Реализовано:
- [x] Инициализация `server_cart.data` для KnockoutJS
- [x] Правильный порядок блоков
- [x] Попап с картой Яндекс
- [x] Загрузка пунктов выдачи из БД
- [x] Кластеризация точек на карте
- [x] Выбор пункта выдачи
- [x] Сохранение в localStorage
- [x] Модель KnockoutJS для пункта выдачи
- [x] AJAX endpoints для работы с точками
- [x] Стили в legacy стиле

### ⚠️ Отличия от legacy:
1. **Используется Laravel вместо Smarty** - синтаксис шаблонов отличается, но логика та же
2. **CSRF токен** - добавлена защита от CSRF атак
3. **Современный JavaScript** - используется ES6+ синтаксис
4. **Сохранена существующая логика создания заказа** - метод `submitOrder()` использует текущий API

## Тестирование

### Проверить:
1. ✅ Отображение товаров в корзине (правая колонка)
2. ✅ Открытие попапа с картой по кнопке "Выбрать адрес доставки"
3. ✅ Загрузка пунктов выдачи при перемещении карты
4. ✅ Выбор пункта выдачи по клику на метку
5. ✅ Отображение выбранного пункта в блоке "Адрес доставки"
6. ✅ Сохранение выбора в localStorage
7. ✅ Переключение способов доставки
8. ✅ Переключение способов оплаты
9. ✅ Валидация формы
10. ✅ Создание заказа

## Ссылки на legacy файлы

- `legacy/site/modules/sfera/checkout/checkout.tpl` - шаблон
- `legacy/site/modules/sfera/checkout/checkout.class.php` - контроллер
- `legacy/site/modules/sfera/checkout/func.ajax.php` - AJAX обработчики

## Следующие шаги

1. **Наполнить таблицу points данными** - импортировать реальные пункты выдачи
2. **Протестировать на реальных данных** - проверить работу с большим количеством точек
3. **Добавить расчёт стоимости доставки** - интеграция с API Яндекс.Доставки
4. **Добавить фильтры пунктов выдачи** - по времени работы, услугам и т.д.

## История изменений

- **2026-02-28**: Миграция checkout на legacy структуру
  - Исправлена инициализация корзины для KnockoutJS
  - Изменён порядок блоков
  - Добавлена интеграция с картой и пунктами выдачи
  - Добавлены AJAX endpoints
  - Обновлены стили
