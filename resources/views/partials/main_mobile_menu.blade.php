<!-- Mobile Menu Overlay -->
<div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>

<!-- Mobile Menu -->
<nav class="sidebar" id="sidebar">
    <div class="mobile-menu-header">
        <div class="logo-plus" id="toggleBtn"></div>
    </div>

    <div class="mobile-menu-section">
        <nav class="nav-menu">
            <div class="nav-item active">
                <a href="{{ url('/main_showcase') }}">
                    <div class="nav-icon home-icon"></div>
                    <span class="nav-text">Главная</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ url('/main_tests') }}">
                    <div class="nav-icon note-icon"></div>
                    <span class="nav-text">Тесты и задания</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ url('/main_base') }}">
                    <div class="nav-icon knowledge-icon"></div>
                    <span class="nav-text">База знаний</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ url('/main_community') }}">
                    <div class="nav-icon chat-icon"></div>
                    <span class="nav-text">Сообщество</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ url('/main_news') }}">
                    <div class="nav-icon news-icon"></div>
                    <span class="nav-text">Новости</span>
                </a>
            </div>
            <div class="nav-item settings">
                <a href="{{ url('/main_settings') }}">
                    <div class="nav-icon settings-icon"></div>
                    <span class="nav-text">Настройки</span>
                </a>
            </div>
        </nav>
    </div>

    <div class="user-section">
        <div class="user-avatar">
            <a href="{{ url('/profile') }}">
                <svg width="21" height="25" viewBox="0 0 21 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20 24C20 20.6127 15.7467 17.8667 10.5 17.8667C5.25329 17.8667 1 20.6127 1 24M10.5 13.2667C7.0022 13.2667 4.16667 10.5207 4.16667 7.13333C4.16667 3.74599 7.0022 1 10.5 1C13.9978 1 16.8333 3.74599 16.8333 7.13333C16.8333 10.5207 13.9978 13.2667 10.5 13.2667Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        </div>
        <div class="user-info">
            <div class="user-name">{{ Auth::user()->display_name }}</div>
            <div class="user-email">{{ Auth::user()->email }}</div>
        </div>
    </div>

</nav>
