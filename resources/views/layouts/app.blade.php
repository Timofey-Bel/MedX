<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MedX')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    @yield('styles')
</head>
<body>
    @yield('content')
    @include('components.mobile-bottom-nav')
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    @auth
    <script src="{{ asset('js/api-client.js') }}"></script>
    <script src="{{ asset('js/user-data-sync.js') }}"></script>
    @endauth
    <script src="{{ asset('js/achievements.js') }}"></script>
    @yield('scripts')
</body>
</html>
