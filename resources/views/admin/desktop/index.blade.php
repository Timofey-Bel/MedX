<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Админ-панель - Творческий Центр СФЕРА</title>
    
    <!-- Google Material Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    
    <!-- jQuery (нужен для AJAX запросов) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- ExtJS 4.2.1 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/extjs/4.2.1/resources/css/ext-all.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/extjs/4.2.1/ext-all.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/extjs/4.2.1/locale/ext-lang-ru.js"></script>
    
    <!-- Установщик приложений -->
    {{-- TODO: Создать app_installer_window.js --}}
    {{-- <script src="/site/modules/admin/desktop/app_installer_window.js"></script> --}}
    
    <!-- Окно настроек -->
    {{-- TODO: Создать settings_window.js --}}
    {{-- <script src="/site/modules/admin/desktop/settings_window.js"></script> --}}
    
    <!-- Section Builder (встроенное приложение) -->
    <link rel="stylesheet" href="https://unpkg.com/grapesjs/dist/css/grapes.min.css">
    <script src="https://unpkg.com/grapesjs"></script>
    <script src="/site/modules/admin/desktop/section_builder_window.js"></script>
    
    <!-- Динамическое подключение JS файлов установленных приложений -->
@foreach($installed_apps as $app)
@if(isset($moduleAccess[$app['app_id']]) && $moduleAccess[$app['app_id']] && $app['has_js_file'])
    <script src="/site/modules/admin/desktop/{{ $app['app_id'] }}_window.js?v={{ $app['version'] }}"></script>
@endif
@endforeach
    
    <!-- Admin Desktop Styles -->
    <link rel="stylesheet" href="{{ asset('css/admin-desktop.css') }}">
</head>
<body>
    <!-- Рабочий стол -->
    <div class="desktop">
        <!-- Ярлыки -->
        <div class="desktop-shortcuts">
            <!-- Динамические ярлыки из установленных приложений (show_on_desktop = 1) -->
            @foreach($desktop_shortcuts as $shortcut)
            @if(isset($moduleAccess[$shortcut['app_id']]) && $moduleAccess[$shortcut['app_id']])
            <div class="shortcut" data-function="{{ $shortcut['function_name'] }}" data-shortcut-id="{{ $shortcut['id'] }}">
                <div class="shortcut-icon"><span class="material-icons" style="color: {{ $shortcut['icon_color'] }};">{{ $shortcut['icon'] }}</span></div>
                <div class="shortcut-text">{{ $shortcut['title'] }}</div>
            </div>
            @endif
            @endforeach
            
            <!-- Встроенное приложение: Section Builder -->
            <div class="shortcut" data-function="openSectionBuilder" data-shortcut-id="section-builder">
                <div class="shortcut-icon"><span class="material-icons" style="color: #9C27B0;">view_module</span></div>
                <div class="shortcut-text">Конструктор секций</div>
            </div>
        </div>
        
        <!-- Логотип -->
        <div class="desktop-logo">
            <div class="desktop-logo-main">СФЕРА</div>
            <div class="desktop-logo-sub">Творческий Центр</div>
        </div>
    </div>
    
    <!-- Панель задач -->
    <div class="taskbar">
        <!-- Кнопка Пуск -->
        <button class="start-button" id="start-button">
            <span class="material-icons">apps</span>
        </button>
        
        <!-- Разделитель -->
        <div class="taskbar-separator"></div>
        
        <!-- Контейнер открытых окон -->
        <div class="taskbar-windows" id="taskbar-windows"></div>
        
        <!-- Системный трей -->
        <div class="system-tray">
            <button class="tray-button" onclick="alert('Новых уведомлений нет')" title="Уведомления">
                <span class="material-icons">notifications</span>
            </button>
            <button class="tray-button" onclick="showUserMenu()" title="{{ $admin_user['name'] }}">
                <span class="material-icons">person</span>
            </button>
        </div>
        
        <div class="taskbar-separator"></div>
        
        <!-- Часы -->
        <div class="clock" id="clock">
            <div class="clock-time" id="clock-time">00:00</div>
            <div class="clock-date" id="clock-date">01.01.2024</div>
        </div>
    </div>
    
    <!-- Меню Пуск - Windows 10 Style -->
    <div id="start-menu-modal">
        <!-- Левая панель - иконки пользователя -->
        <div id="user">
            <a class="push" href="#" onclick="closeStartMenu(); return false;">
                <span class="material-icons">menu</span>
            </a>
            <a href="#" onclick="showProfile(); return false;" title="{{ $admin_user['name'] }}">
                <span class="material-icons">person</span>
            </a>
            <a href="#" onclick="openSettings(); return false;" title="Настройки">
                <span class="material-icons">settings</span>
            </a>
            <a href="#" onclick="logout(); return false;" title="Выход">
                <span class="material-icons">power_settings_new</span>
            </a>
        </div>
        
        <!-- Центральная панель - список приложений -->
        <div id="apps">
            <a class="category">Приложения</a>
            
            <!-- Список установленного (всегда) -->
            @foreach($start_apps as $app)
            @if(isset($moduleAccess[$app['app_id']]) && $moduleAccess[$app['app_id']])
            <a href="#" onclick="{{ $app['function_name'] }}(); closeStartMenu(); return false;">
                <span class="app-icon"><span class="material-icons" style="color: {{ $app['icon_color'] }};">{{ $app['icon'] }}</span></span>
                <span class="app-text">{{ $app['app_name'] }}</span>
            </a>
            @endif
            @endforeach
        </div>
        
        <!-- Правая панель - плитки приложений (Быстрый доступ) -->
        @if(count($quick_access_shortcuts) > 0)
        <div id="others">
            <div class="title-others">
                Быстрый доступ
            </div>
            <div class="box-others">
                <!-- Динамический быстрый доступ из установленных приложений (show_in_quick_access = 1) -->
                @foreach($quick_access_shortcuts as $shortcut)
                @if(isset($moduleAccess[$shortcut['app_id']]) && $moduleAccess[$shortcut['app_id']])
                <div class="box" onclick="{{ $shortcut['function_name'] }}(); closeStartMenu();">
                    <div class="box-icon"><span class="material-icons" style="color: {{ $shortcut['icon_color'] }};">{{ $shortcut['icon'] }}</span></div>
                    <span>{{ $shortcut['title'] }}</span>
                </div>
                @endif
                @endforeach
            </div>
        </div>
        @endif
    </div>
    
    <!-- Меню пользователя - Windows 10 Style -->
    <div id="user-menu">
        <div class="user-menu-header">
            <div class="user-menu-avatar">
                <span class="material-icons">person</span>
            </div>
            <div class="user-menu-name">{{ $admin_user['name'] }}</div>
            <div class="user-menu-email">{{ $admin_user['login'] }}</div>
        </div>
        <div class="user-menu-actions">
            <a href="#" class="user-menu-item" onclick="showProfile(); closeUserMenu(); return false;">
                <span class="material-icons">account_circle</span>
                <span class="user-menu-item-text">Профиль</span>
            </a>
            <a href="#" class="user-menu-item" onclick="openSettings(); closeUserMenu(); return false;">
                <span class="material-icons">settings</span>
                <span class="user-menu-item-text">Настройки</span>
            </a>
            <div class="user-menu-separator"></div>
            <a href="#" class="user-menu-item" onclick="logout(); return false;">
                <span class="material-icons">power_settings_new</span>
                <span class="user-menu-item-text">Выход</span>
            </a>
        </div>
    </div>
    
{{-- Global Variables and Configuration --}}
@include('admin.desktop.js._globals')

{{-- Utility Functions --}}
@include('admin.desktop.js._utils')

{{-- Initialization --}}
@include('admin.desktop.js._initialization')

{{-- UI Component Functions --}}
@include('admin.desktop.js._clock')
@include('admin.desktop.js._start-menu')
@include('admin.desktop.js._user-menu')

{{-- Window Management --}}
@include('admin.desktop.js._taskbar')

{{-- Feature Modules --}}
@include('admin.desktop.js._banners')
@include('admin.desktop.js._wholesaler-banners')
@include('admin.desktop.js._menu')
@include('admin.desktop.js._permissions')
@include('admin.desktop.js._users')

{{-- IIFE Modules (Self-Executing) --}}
@include('admin.desktop.js._drag-drop')
@include('admin.desktop.js._context-menu')
    
    <!-- Контекстное меню для рабочего стола -->
    <div id="desktop-context-menu" class="context-menu">
        <div class="context-menu-item" data-action="arrange-icons">
            <span class="material-icons">grid_on</span>
            Упорядочить значки по сетке
        </div>
    </div>
    
    <!-- Контекстное меню для иконки -->
    <div id="icon-context-menu" class="context-menu">
        <div class="context-menu-item" data-action="open">
            <span class="material-icons">open_in_new</span>
            Открыть
        </div>
        <div class="context-menu-separator"></div>
        <div class="context-menu-item" data-action="rename">
            <span class="material-icons">edit</span>
            Переименовать
        </div>
        <div class="context-menu-separator"></div>
        <div class="context-menu-item" data-action="delete">
            <span class="material-icons">delete</span>
            Удалить
        </div>
    </div>
</body>
</html>
