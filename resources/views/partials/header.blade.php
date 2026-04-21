<header>
    <div class="container">
        <div class="header-container">
            <div class="logo">
                <a href="{{ url('/') }}">
                <img class="logo" alt="MedX" src="{{ asset('images/branding/logo@2x.png') }}" style="width: 124px; height: 34px;" />
                </a>
            </div>

            <nav class="nav-links">
                <a href="{{ url('/main_showcase') }}" class="nav-link with-arrow">База знаний <svg width="14" height="9" viewBox="0 0 14 9" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M0.681641 0.586967L6.70406 7.58697L12.6816 0.586967" stroke="#28282F" stroke-width="1.8"/>
</svg>
</a>
                <a href="{{ url('/tariffs') }}" class="nav-link">Тарифы</a>
                <a href="{{ url('/faq') }}" class="nav-link">FAQ</a>
                <a href="#" class="nav-link">Добрые дела</a>
            </nav>

            <div class="auth-buttons">
                <a href="{{ url('/login') }}" class="btn-login">Войти</a>
                <a href="{{ url('/register') }}" class="btn-trial">3 дня бесплатно</a>
            </div>
        </div>
    </div>
</header>
