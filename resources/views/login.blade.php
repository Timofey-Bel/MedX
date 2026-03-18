@extends('layouts.app')

@section('title', 'MedX — Вход в личный кабинет')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/medx/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/medx/css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/medx/css/header.css') }}">
@endsection

@section('content')
@include('partials.header')
<main class="main">
    <section class="login">
        <div class="login__card">
            <h1 class="login__title">Личный кабинет</h1>
            <p class="login__subtitle">Войди в личный кабинет удобным способом</p>

            <form class="login__form" action="#" method="post">
                <label class="login__field">
                    <span class="login__field-label">Логин</span>
                    <input
                            class="login__input"
                            type="email"
                            name="email"
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
                        <img src="{{ asset('site/modules/medx/showcase/img/eye.png') }}" alt="eye" class="password-toggle__icon">
                    </button>
                </label>

                <div class="login__divider">
                    <span class="login__divider-line"></span>
                    <span class="login__divider-text">или</span>
                    <span class="login__divider-line"></span>
                </div>

                <p class="login__subtitle">Войдите через удобный сервис</p>

                <div class="login__alternative">
                    <div class="login__alternative-icons">
                        <a href="#" class="login__alternative-icon">
                            <img src="{{ asset('site/modules/medx/showcase/img/vk.svg') }}" alt="VK"/>
                        </a>
                        <a href="#" class="login__alternative-icon">
                            <img src="{{ asset('site/modules/medx/showcase/img/google.svg') }}" alt="Google"/>
                        </a>
                        <a href="#" class="login__alternative-icon">
                            <img src="{{ asset('site/modules/medx/showcase/img/telegram.svg') }}" alt="Telegram"/>
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
