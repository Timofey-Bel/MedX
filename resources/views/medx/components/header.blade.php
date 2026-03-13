<header class="header">
    <div class="header-left">
        <div class="logo">
            <a href="{{ route('medx.showcase') }}">
                <img class="logo" alt="MedX" src="{{ asset('assets/medx/img/main/showcase/logo@2x.png') }}" style="width: 124px; height: 34px;" />
            </a>
        </div>
        <nav class="nav">
            <a href="#" class="nav-link">База знаний</a>
            <a href="#" class="nav-link">Тесты</a>
            <a href="#" class="nav-link">Тарифы</a>
        </nav>
    </div>
    <div class="header-right">
        <a href="{{ route('medx.register') }}" class="btn btn-register">Зарегистрироваться</a>
        <a href="{{ route('medx.login') }}" class="btn btn-login">Войти</a>
    </div>
</header>
