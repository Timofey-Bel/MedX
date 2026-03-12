<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="format-detection" content="telephone=no">
    
    <title>@yield('title', 'Творческий Центр СФЕРА')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- CSS Variables -->
    <style>
        :root {
            --mainFont: 'Roboto', sans-serif;
            --cbPrimaryColor: #0096ff;
            --iconColor: #0096ff;
        }
    </style>
    
    <!-- CSS Assets -->
    <link rel="stylesheet" href="{{ asset('assets/sfera/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/sfera/css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/sfera/css/city-modal.css') }}">
    
    @stack('styles')
    @stack('head')
</head>
<body>
    <!-- Header Component -->
    @include('components.header', [
        'categories' => [], // Временно отключено для диагностики производительности
        'menu_items' => $menu_items ?? []
    ])
    
    <!-- Main Content -->
    <main class="l-content">
        @yield('content')
    </main>
    
    <!-- Footer Component -->
    @include('components.footer')
    
    <!-- Mobile Menu Component -->
    @include('components.mobile-menu', [
        'categories' => [] // Временно отключено для диагностики производительности
    ])
    
    <!-- City Modal Component -->
    @include('components.city-modal')
    
    <!-- JavaScript Assets -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/knockoutjs/dist/knockout.js') }}"></script>
    <script src="{{ asset('js/knockout-mapping/knockout.mapping.js') }}"></script>
    
    <!-- Инициализация глобальной переменной server_cart.data для Knockout.js модели корзины -->
    <!-- Миграция из legacy системы: данные корзины передаются из PHP в JavaScript -->
    @php
        $defaultCartData = [
            'items' => [],
            'total_cart_sum' => 0,
            'total_cart_amount' => 0,
            'cart_sum' => 0,
            'cart_discount' => 0,
            'promocode' => ''
        ];
    @endphp
    <script>
        var server_cart = {
            data: @json($cart_data ?? $defaultCartData)
        };
    </script>
    
    <!-- Подключение Knockout.js модели корзины -->
    <!-- Миграция из legacy системы: модульная модель корзины с AJAX запросами -->
    @include('js.models.cart_new.model_cart')
    
    <!-- Подключение ViewModel для счетчиков корзины -->
    <!-- Отдельная ViewModel для избежания конфликтов с множественными applyBindings -->
    @include('js.models.cart_new.cart_counter_viewmodel')
    
    <!-- Подключение ViewModel для счетчика избранного -->
    <!-- Глобальная ViewModel для отображения счетчика избранного в header и mobile nav -->
    <script src="{{ asset('assets/sfera/js/favorites-counter.js') }}"></script>
    
    <!-- Инициализация обработчиков кнопок "В корзину" -->
    <script src="{{ asset('js/cart-init.js') }}"></script>
    
    <script src="{{ asset('assets/sfera/script.js') }}"></script>
    <script src="{{ asset('assets/sfera/js/header.js') }}"></script>
    <script src="{{ asset('assets/sfera/js/city-modal.js') }}"></script>
    
    @stack('scripts')
</body>
</html>
