<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'MedX - Интерактивная образовательная платформа для медиков')</title>
    
    <link rel="stylesheet" href="{{ asset('assets/medx/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/medx/css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/medx/css/footer.css') }}">
    
@stack('styles')
@stack('head')
</head>
<body>
    @include('medx.components.header')
    
    <main>
        @yield('content')
    </main>
    
    @include('medx.components.footer')
    
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    
@stack('scripts')
</body>
</html>
