<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Оптовый кабинет - @yield('page-title', 'Главная') - {{ config('app.name') }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('assets/sfera/css/wholesale-cabinet.css') }}">
    
    @stack('styles')
</head>
<body>
    <div class="animated-bg"></div>
    <div class="mobile-overlay" id="mobileOverlay" onclick="closeMobile()"></div>

    <div class="app">
        {{-- Sidebar --}}
        <aside class="sidebar" id="sidebar">
            <a href="{{ route('lk.index') }}" class="sidebar-brand">
                <div class="sidebar-brand-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9h18v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9Z"/><path d="m3 9 2.45-4.9A2 2 0 0 1 7.24 3h9.52a2 2 0 0 1 1.8 1.1L21 9"/><path d="M12 3v6"/></svg>
                </div>
                <div class="sidebar-brand-text">
                    <h2>{{ $organization->display_name }}</h2>
                </div>
            </a>

            <nav class="sidebar-nav">
                <div class="sidebar-nav-item">
                    <a href="{{ route('lk.index') }}" class="sidebar-nav-link {{ request()->routeIs('lk.index') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        <span>Главная</span>
                    </a>
                </div>
                <div class="sidebar-nav-item">
                    <a href="{{ route('lk.orders') }}" class="sidebar-nav-link {{ request()->routeIs('lk.orders') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a2 2 0 0 0-2 2v4"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        <span>Заказы</span>
                    </a>
                </div>
                <div class="sidebar-nav-item">
                    <a href="{{ route('lk.organization') }}" class="sidebar-nav-link {{ request()->routeIs('lk.organization') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="20" x="4" y="2" rx="2" ry="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01"/><path d="M16 6h.01"/><path d="M12 6h.01"/><path d="M12 10h.01"/><path d="M12 14h.01"/><path d="M16 10h.01"/><path d="M16 14h.01"/><path d="M8 10h.01"/><path d="M8 14h.01"/></svg>
                        <span>Организация</span>
                    </a>
                </div>
                <div class="sidebar-nav-item">
                    <a href="{{ route('catalog.index') }}" class="sidebar-nav-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                        <span>Каталог</span>
                    </a>
                </div>
            </nav>

            <div class="sidebar-manager">
                <div class="sidebar-manager-title">Ваш менеджер</div>
                <div class="sidebar-manager-info">
                    <div class="sidebar-manager-name">Иван Иванов</div>
                    <a href="tel:+79000000000" class="sidebar-manager-contact">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        +7 (900) 000-00-00
                    </a>
                    <a href="mailto:ivan@sferabook.ru" class="sidebar-manager-contact">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m2 7 8.97 5.7a1.94 1.94 0 0 0 2.06 0L22 7"/></svg>
                        ivan@sferabook.ru
                    </a>
                </div>
            </div>

            <div class="sidebar-footer">
                <a href="{{ route('lk.organization') }}" class="sidebar-footer-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
                    Настройки
                </a>
                <a href="{{ route('logout') }}" class="sidebar-footer-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <div class="avatar-small">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                    {{ $user->name }}
                    <span class="badge-outline">Опт</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </aside>

        {{-- Main Content --}}
        <div class="main-wrap" id="mainWrap">
            <header class="header">
                <button class="icon-btn toggle-sidebar-mobile" onclick="openMobile()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
                </button>
                <button class="icon-btn toggle-sidebar-desktop" onclick="toggleSidebar()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M9 3v18"/></svg>
                </button>
                <h1>@yield('page-title', 'Оптовый кабинет')</h1>
                <div class="header-actions">
                    <div class="user-menu-wrapper">
                        <button class="avatar-header" id="userMenuBtn">{{ strtoupper(substr($user->name, 0, 2)) }}</button>
                        <div class="user-menu" id="userMenu">
                            <div class="user-menu-header">
                                <div class="avatar-small">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                                <div>
                                    <div class="user-menu-name">{{ $user->name }}</div>
                                    <div class="user-menu-email">{{ $user->email }}</div>
                                </div>
                            </div>
                            <div class="user-menu-divider"></div>
                            <a href="{{ route('lk.profile') }}" class="user-menu-item">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                <span>Профиль</span>
                            </a>
                            <a href="{{ route('logout') }}" class="user-menu-item" onclick="event.preventDefault(); document.getElementById('logout-form-header').submit();">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                                <span>Выход</span>
                            </a>
                        </div>
                        <form id="logout-form-header" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </header>

            <main class="main-content">
                @if(session('success'))
                    <div class="alert alert-success" style="margin-bottom: 24px; padding: 16px; background: #d1fae5; border: 1px solid #6ee7b7; border-radius: var(--radius-2xl); color: #065f46;">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error" style="margin-bottom: 24px; padding: 16px; background: #fee2e2; border: 1px solid #fca5a5; border-radius: var(--radius-2xl); color: #991b1b;">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script src="{{ asset('assets/sfera/js/wholesale-cabinet.js') }}"></script>
    @stack('scripts')
</body>
</html>
