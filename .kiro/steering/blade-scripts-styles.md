# Подключение скриптов и стилей в Blade шаблонах

## Критическое правило

**ВСЕГДА используйте `@push` / `@stack` для подключения скриптов и стилей в компонентах и страницах.**

**НЕ используйте `@section` / `@yield` для скриптов и стилей!**

## Почему это важно

В нашем проекте layout (`resources/views/layouts/app.blade.php`) использует директивы `@stack`:

```php
<head>
    <!-- ... -->
    @stack('styles')
    @stack('head')
</head>
<body>
    <!-- ... -->
    @stack('scripts')
</body>
```

Это означает, что **множественные компоненты и страницы могут добавлять свои скрипты и стили**.

## Разница между директивами

### `@section` / `@yield` - Замена содержимого (один раз)

```php
// Layout:
@yield('content')

// Страница:
@section('content')
    <h1>Контент</h1>
@endsection
```

**Проблема:** Если несколько компонентов попытаются использовать `@section('scripts')`, только последний будет работать. Остальные будут перезаписаны.

### `@push` / `@stack` - Добавление содержимого (множественное)

```php
// Layout:
@stack('scripts')

// Страница:
@push('scripts')
    <script src="page.js"></script>
@endpush

// Компонент 1:
@push('scripts')
    <script src="component1.js"></script>
@endpush

// Компонент 2:
@push('scripts')
    <script src="component2.js"></script>
@endpush
```

**Результат:** Все три скрипта будут добавлены в правильном порядке.

## Правильное использование

### ✅ ПРАВИЛЬНО - Используйте @push

```php
{{-- resources/views/showcase/index.blade.php --}}
@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/sfera/css/carousel.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/sfera/css/categories.css') }}">
@endpush

@section('content')
    <!-- Контент страницы -->
@endsection

@push('scripts')
    <script src="{{ asset('assets/sfera/js/carousel.js') }}"></script>
    <script src="{{ asset('assets/sfera/js/catalog.js') }}"></script>
@endpush
```

### ❌ НЕПРАВИЛЬНО - НЕ используйте @section для скриптов

```php
{{-- НЕПРАВИЛЬНО! --}}
@section('scripts')
    <script src="{{ asset('assets/sfera/js/catalog.js') }}"></script>
@endsection
```

**Проблема:** Скрипт не загрузится, потому что layout использует `@stack('scripts')`, а не `@yield('scripts')`.

## Использование в компонентах

Компоненты также могут добавлять свои скрипты и стили:

```php
{{-- resources/views/components/showcase/top10-slider.blade.php --}}
<section class="top10-slider-section">
    <!-- HTML компонента -->
</section>

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/sfera/css/top10-slider.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('assets/sfera/js/top10-slider.js') }}"></script>
@endpush
```

## Порядок загрузки

Скрипты и стили добавляются в том порядке, в котором они встречаются в шаблоне:

1. Сначала загружаются стили/скрипты из главной страницы
2. Затем из компонентов в порядке их включения
3. Все добавляется в `@stack` в layout

## Отладка проблем с загрузкой скриптов

### Симптомы проблемы

1. JavaScript функции не определены (`typeof functionName === 'undefined'`)
2. Скрипты не видны во вкладке Network в DevTools
3. Функционал страницы не работает

### Проверка

1. **Откройте исходный код страницы** (Ctrl+U в браузере)
2. **Найдите тег `<script>`** с вашим файлом
3. **Если тега нет** - проблема в подключении

### Решение

1. Проверьте, что используете `@push('scripts')`, а не `@section('scripts')`
2. Проверьте, что путь к файлу правильный: `{{ asset('path/to/file.js') }}`
3. Проверьте, что файл существует в `public/` директории

## Пример из реального проекта

### Проблема (2026-02-28)

На главной странице (showcase) не работали кнопки корзины и избранного.

**Причина:** В `resources/views/showcase/index.blade.php` использовалась директива `@section('scripts')`:

```php
{{-- БЫЛО (неправильно): --}}
@section('scripts')
    <script src="{{ asset('assets/sfera/js/catalog.js') }}"></script>
@endsection
```

**Результат:** Скрипт `catalog.js` не загружался, функции не были определены.

**Решение:** Заменили на `@push('scripts')`:

```php
{{-- СТАЛО (правильно): --}}
@push('scripts')
    <script src="{{ asset('assets/sfera/js/catalog.js') }}"></script>
@endpush
```

**Результат:** Все заработало корректно.

## Когда использовать @section

`@section` / `@yield` используется только для **уникального контента**, который должен быть **один раз** на странице:

```php
// Layout:
<title>@yield('title', 'Default Title')</title>

<main>
    @yield('content')
</main>

// Страница:
@section('title', 'Главная страница')

@section('content')
    <h1>Контент страницы</h1>
@endsection
```

## Чек-лист для разработчиков

При создании новой страницы или компонента:

- [ ] Используете `@push('styles')` для CSS
- [ ] Используете `@push('scripts')` для JavaScript
- [ ] Используете `@push('head')` для мета-тегов и других элементов `<head>`
- [ ] НЕ используете `@section('scripts')` или `@section('styles')`
- [ ] Проверили, что скрипты загружаются (DevTools → Network)
- [ ] Проверили, что функции определены (Console → `typeof functionName`)

## Дополнительные директивы

### @prepend - Добавить в начало стека

```php
@prepend('scripts')
    <script src="first.js"></script>
@endprepend
```

Скрипт будет добавлен **в начало** стека, перед всеми остальными.

### @once - Добавить только один раз

```php
@once
    @push('scripts')
        <script src="library.js"></script>
    @endpush
@endonce
```

Полезно для библиотек, которые должны загружаться только один раз, даже если компонент используется несколько раз.

## Ссылки

- [Laravel Blade Documentation - Stacks](https://laravel.com/docs/11.x/blade#stacks)
- Пример исправления: `docs/showcase-product-cards-fix.md`
- Layout проекта: `resources/views/layouts/app.blade.php`

## История

- 2026-02-28: Создан документ после исправления проблемы на главной странице
- Проблема: `@section('scripts')` не работал с `@stack('scripts')` в layout
- Решение: Заменили все `@section` на `@push` для скриптов и стилей
