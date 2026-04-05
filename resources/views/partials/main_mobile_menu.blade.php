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
                @if(Auth::user()->avatar_url)
                    <svg width="50" height="50" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <clipPath id="avatarClipMobile">
                                <circle cx="40" cy="40" r="40"/>
                            </clipPath>
                        </defs>
                        <image href="{{ Auth::user()->avatar_url }}" width="80" height="80" clip-path="url(#avatarClipMobile)" preserveAspectRatio="xMidYMid slice"/>
                    </svg>
                @else
                    <svg width="50" height="50" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="40" cy="40" r="40" fill="#3B97A2"/>
                        <text x="40" y="48" font-family="Involve-Medium, Helvetica" font-size="42" font-weight="500" fill="white" text-anchor="middle" dominant-baseline="middle">{{ Auth::user()->avatar_letter }}</text>
                    </svg>
                @endif
            </a>
        </div>
        <div class="user-info">
            <div class="user-name">{{ Auth::user()->display_name }}</div>
            <div class="user-email">{{ Auth::user()->email }}</div>
        </div>
    </div>

</nav>
