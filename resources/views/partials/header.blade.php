<header>
    <div class="container">
        <div class="header-container">
            <div class="logo">
                <a href="{{ url('/') }}">
                <img class="logo" alt="MedX" src="{{ asset('images/branding/logo@2x.png') }}" style="width: 124px; height: 34px;" />
                </a>
            </div>

            <nav class="nav-links">
                <a href="{{ url('/main_showcase') }}" class="nav-link with-arrow">База знаний</a>
                <a href="#" class="nav-link">Тарифы</a>
                <a href="{{ url('/faq') }}" class="nav-link active">FAQ</a>
                <a href="#" class="nav-link">Добрые дела</a>
            </nav>

            <div class="auth-buttons">
                <a href="{{ url('/login') }}" class="btn-login">Войти</a>
                <a href="{{ url('/register') }}" class="btn-trial">3 дня бесплатно</a>
            </div>
        </div>
    </div>
</header>
