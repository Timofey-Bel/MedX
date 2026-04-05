@extends('layouts.app')

@section('title', 'MedX — Вход в личный кабинет')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
@endsection

@section('content')
@include('partials.header')
<main class="main">
    <section class="login">
        <div class="login__card">
            <h1 class="login__title">Личный кабинет</h1>
            <p class="login__subtitle">Войди в личный кабинет удобным способом</p>

            <form class="login__form" action="{{ route('login') }}" method="post">
                @csrf
                
                @if ($errors->any())
                    <div style="color: #e74c3c; margin-bottom: 15px; padding: 10px; background: #fee; border-radius: 8px;">
                        {{ $errors->first() }}
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

                <div class="login__remember">
                    <input type="checkbox" id="remember" name="remember" value="1" class="login__remember-checkbox">
                    <label for="remember" class="login__remember-label">
                        <span class="login__remember-box"></span>
                        <span class="login__remember-text">Запомнить меня</span>
                    </label>
                </div>

                <div class="login__divider">
                    <span class="login__divider-line"></span>
                    <span class="login__divider-text">или</span>
                    <span class="login__divider-line"></span>
                </div>

                <p class="login__subtitle">Войдите через удобный сервис</p>

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

                <button class="login__submit" type="submit">
                    <span class="login__submit-text">войти</span>
                </button>
            </form>

            <p class="login__register">
                Ещё нет аккаунта?
                <a href="{{ url('/register') }}" class="login__register-link">Зарегестрироваться</a>
            </p>
        </div>
    </section>
</main>
@include('partials.footer')
@endsection
