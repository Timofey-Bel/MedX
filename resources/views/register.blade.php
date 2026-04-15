@extends('layouts.app')

@section('title', 'MedX — Регистрация в личный кабинет')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
@include('partials.header')
<main class="main">
    <section class="login-mainblock">
        <div class="features-grid">
            <div class="left-column">
                <div class="feature-card card-1">
                    <div class="card-title">100+ авторских материалов</div>
                    <div class="bg-img"></div>
                </div>
                <div class="feature-card card-2">
                    <div class="card-title">попробуй MedX бесплатно</div>
                </div>
                <div class="feature-card card-3">
                    <div class="card-title">3000+ клинических задач и тестов</div>
                </div>
                <div class="feature-card card-4">
                    <div class="card-title">дежурства и волонтёрство</div>
                </div>
                <div class="feature-card card-5">
                    <div class="card-title">Персональные рекомендации (AI)</div>
                </div>
            </div>

            <div class="mid-column">
                <div class="feature-card card-6">
                    <div class="card-title">карточки для повторения</div>
                </div>
            </div>

            <div class="right-column">
                <div class="feature-card card-7">
                    <div class="card-title">Видео-лекции</div>
                </div>
                <div class="feature-card card-8">
                    <div class="card-title">сообще- ства и чаты</div>
                </div>
                <div class="feature-card card-9">
                    <div class="card-title">подкасты</div>
                    <div class="bg-img-1"></div>
                    <div class="bg-img-2"></div>
                </div>
                <div class="feature-card card-10">
                    <div class="card-title">прогресс и рейтинг</div>
                    <div class="bg-img"></div>
                </div>
            </div>
        </div>
        <div class="login">
            <div class="login__card">
                <h1 class="login__title">Регистрация</h1>
                <!-- <p class="login__subtitle">Выбери удобный способ регистрации</p> -->

                <form class="login__form" action="{{ route('register') }}" method="post">
                    @csrf
                    
                    @if ($errors->any())
                        <div style="color: #e74c3c; margin-bottom: 15px; padding: 10px; background: #fee; border-radius: 8px;">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <label class="login__field">
                        <span class="login__field-label">Логин</span>
                        <input
                                class="login__input"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="Введите логин"
                                required
                        />
                    </label>

                    <label class="login__field">
                        <span class="login__field-label">Пароль</span>
                        <input
                                class="login__input"
                                type="password"
                                name="password"
                                placeholder="Введите пароль"
                                required
                        />
                        <button type="button" class="password-toggle" aria-label="Показать пароль">
                            <img src="{{ asset('images/medical/eye.png') }}" alt="eye" class="password-toggle__icon">
                        </button>
                    </label>

                    <div class="login__divider">
                        <span class="login__divider-line"></span>
                        <span class="login__divider-text">или</span>
                        <span class="login__divider-line"></span>
                    </div>

                    <!-- <p class="login__subtitle">Зарегестрируйся через удобный сервис</p> -->

                    <div class="login__alternative">
                        <div class="login__alternative-icons">
                            <a href="#" class="login__alternative-icon">
                                <img src="{{ asset('images/social/vk.svg') }}" alt="VK"/>
                            </a>
                            <a href="#" class="login__alternative-icon">
                                <img src="{{ asset('images/social/google.svg') }}" alt="Google"/>
                            </a>
                            <a href="#" class="login__alternative-icon">
                                <img src="{{ asset('images/social/telegram.svg') }}" alt="Telegram"/>
                            </a>
                        </div>
                    </div>
                    <div class="checkbox-container">
                        <div class="custom-checkbox" id="policyCheckbox"></div>
                        <span class="checkbox-text">Нажимая «Зарегестрироваться», я соглашаюсь с
                        <a href="#" class="policy-link">Политикой конфиденциальности</a>.
                    </span>
                    </div>
                    <button class="login__submit" type="submit">
                        <span class="login__submit-text" id="registerBtn">Зарегестрироваться</span>
                    </button>
                </form>

                <p class="login__register">
                    Уже есть аккаунт?
                    <a href="{{ url('/login') }}" class="login__register-link">Войти</a>
                </p>
            </div>
        </div>
    </section>
</main>

@include('partials.footer')
@endsection

@section('scripts')
<script src="{{ asset('assets/medx/script.js') }}"></script>
<script src="{{ asset('assets/medx/js/register.js') }}"></script>
<script src="{{ asset('js/password-toggle.js') }}"></script>
<script>
    const checkbox = document.getElementById('policyCheckbox');
    const registerBtn = document.getElementById('registerBtn');

    checkbox.addEventListener('click', function() {
        this.classList.toggle('checked');
        this.classList.remove('error');
    });

    document.querySelector('.checkbox-text').addEventListener('click', function() {
        checkbox.click();
    });

    registerBtn.addEventListener('click', function(e) {
        if (!checkbox.classList.contains('checked')) {
            e.preventDefault();
            checkbox.classList.add('error');
            checkbox.style.animation = 'none';
            setTimeout(() => {
                checkbox.style.animation = 'pulse 0.5s ease-in-out';
            }, 10);
            checkbox.focus();
            return false;
        }
    });
</script>
@endsection
