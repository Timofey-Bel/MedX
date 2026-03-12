# Исправление функционала карточек товаров на главной странице

## Проблема

Карточки товаров на главной странице (витрине) не имели полного функционала:
1. Кнопка "В корзину" не меняется на контролы количества после добавления товара
2. Кнопка "Добавить в избранное" не работала

## Причина

JavaScript файл `catalog.js` не загружался на странице из-за неправильного использования директив Blade.

В шаблоне `resources/views/showcase/index.blade.php` использовались директивы `@section('scripts')` и `@section('styles')`, но в layout `resources/views/layouts/app.blade.php` используется `@stack('scripts')` и `@stack('styles')`.

**Разница между директивами:**
- `@section` / `@yield` - для замены содержимого (один раз)
- `@push` / `@stack` - для добавления содержимого (множественное)

## Решение

Заменили `@section('scripts')` на `@push('scripts')` и `@section('styles')` на `@push('styles')` в файле `resources/views/showcase/index.blade.php`:

```php
// БЫЛО (неправильно):
@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/sfera/css/carousel.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/sfera/css/categories.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/sfera/css/top10-slider.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/sfera/css/product-reviews.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/sfera/css/catalog.css') }}">
@endsection

@section('scripts')
    <script src="{{ asset('assets/sfera/js/carousel.js') }}"></script>
    <script src="{{ asset('assets/sfera/js/top10-slider.js') }}"></script>
    <script src="{{ asset('assets/sfera/js/catalog.js') }}"></script>
    <script src="{{ asset('assets/sfera/js/showcase-init.js') }}"></script>
@endsection

// СТАЛО (правильно):
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/sfera/css/carousel.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/sfera/css/categories.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/sfera/css/top10-slider.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/sfera/css/product-reviews.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/sfera/css/catalog.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('assets/sfera/js/carousel.js') }}"></script>
    <script src="{{ asset('assets/sfera/js/top10-slider.js') }}"></script>
    <script src="{{ asset('assets/sfera/js/catalog.js') }}"></script>
    <script src="{{ asset('assets/sfera/js/showcase-init.js') }}"></script>
@endpush
```

## Результат

После исправления:
- ✅ Кнопка "В корзину" сразу меняется на контролы количества (+/-)
- ✅ Счетчик корзины в header обновляется
- ✅ Кнопка "Добавить в избранное" работает
- ✅ Счетчик избранного обновляется
- ✅ Появляются уведомления о добавлении товаров

## Проверка работоспособности

### 1. Проверка загрузки скриптов

В консоли браузера (F12 → Console) выполните:
```javascript
typeof setupFavoriteButtons
```

Должно вернуть: `"function"`

### 2. Проверка количества кнопок

```javascript
document.querySelectorAll('.btn-add-to-cart').length
```

Должно вернуть: `22` (10 в TOP-10 + 12 в случайных товарах)

### 3. Тестирование корзины

1. Нажмите "В корзину" на любой карточке
2. Кнопка должна сразу измениться на контролы количества
3. Счетчик корзины в header должен обновиться
4. Должно появиться уведомление "Товар добавлен в корзину (1 шт.)"

### 4. Тестирование избранного

1. Нажмите на сердечко на любой карточке
2. Сердечко должно стать розовым (#ff0080)
3. Счетчик избранного в header должен обновиться
4. Должно появиться уведомление "Товар добавлен в избранное"

## Дополнительные улучшения

### Диагностический скрипт

Создан файл `public/assets/sfera/js/showcase-init.js` для диагностики инициализации:
- Проверяет наличие необходимых функций
- Выводит количество найденных элементов
- Помогает отлаживать проблемы с инициализацией

### Отладочные логи

В `catalog.js` добавлены (временно) отладочные console.log для функции `setupAddToCart()`:
- Выводит Product ID
- Показывает, найден ли quantity control
- Помогает отлаживать проблемы с селекторами

## Важные замечания

### Blade директивы

При работе с шаблонами Laravel всегда проверяйте, какая директива используется в layout:
- Если в layout `@yield('section_name')` - используйте `@section('section_name')`
- Если в layout `@stack('stack_name')` - используйте `@push('stack_name')`

### Порядок загрузки скриптов

Важно соблюдать порядок загрузки:
1. `carousel.js` - для каруселей
2. `top10-slider.js` - для TOP-10 слайдера
3. `catalog.js` - основной функционал карточек (ОБЯЗАТЕЛЬНО!)
4. `showcase-init.js` - диагностика (опционально)

### Зависимости

`catalog.js` зависит от:
- jQuery (загружается в layout)
- Knockout.js (для модели корзины)
- `cartCounterViewModel` (глобальная переменная)
- `favoritesCounterViewModel` (глобальная переменная)

Все эти зависимости загружаются в `layouts/app.blade.php` до `@stack('scripts')`.

## Файлы, затронутые исправлением

1. `resources/views/showcase/index.blade.php` - изменена директива с `@section` на `@push`
2. `public/assets/sfera/js/catalog.js` - добавлены отладочные логи (временно)
3. `public/assets/sfera/js/showcase-init.js` - создан новый диагностический скрипт
4. `docs/showcase-product-cards-debug.md` - создана документация для отладки

## Ссылки

- Документация по отладке: `docs/showcase-product-cards-debug.md`
- Требования к данным карточек: `.kiro/steering/product-card-data-requirements.md`
- Эталонная реализация: `app/Http/Controllers/CatalogController.php`

## Дата исправления

2026-02-28
