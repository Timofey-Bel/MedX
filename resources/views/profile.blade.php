@extends('layouts.app')

@section('title', 'MedX - Профиль')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
@include('partials.header')

<main class="profile-container">
    <div class="container">
        <div class="profile-layout">
            <aside class="profile-sidebar">
                <div class="profile-user">
                    <div class="user-avatar">
                        <svg width="48" height="48" viewBox="0 0 48 48" fill="none">
                            <circle cx="24" cy="24" r="24" fill="#005bff"/>
                            <circle cx="24" cy="18" r="8" fill="white"/>
                            <path d="M8 40c0-8 7-14 16-14s16 6 16 14" fill="white"/>
                        </svg>
                    </div>
                    <div class="user-info">
                        <h2 class="profile-name">Гость</h2>
                        <p class="profile-email">Не указано</p>
                    </div>
                </div>

                <nav class="profile-nav">
                    <a href="#personal" class="nav-item active" data-tab="personal">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <circle cx="10" cy="7" r="4" stroke="currentColor" stroke-width="1.5"/>
                            <path d="M3 17c0-4 3-6 7-6s7 2 7 6" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                        <span>Личные данные</span>
                    </a>
                </nav>

                <a href="{{ url('/logout') }}" class="logout-btn">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M13 3h3a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-3M8 13l-5-5m0 0l5-5m-5 5h12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    Выйти
                </a>
            </aside>

            <div class="profile-content">
                <section class="tab-content active" id="personal">
                    <h1 class="content-title">Личные данные</h1>
                    <div class="profile-section">
                        <div class="section-header">
                            <h2>Основная информация</h2>
                        </div>
                        <form id="personal-form" class="profile-form">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="firstName">Имя</label>
                                    <input type="text" id="firstName" name="first_name" placeholder="Имя">
                                </div>
                                <div class="form-group">
                                    <label for="lastName">Фамилия</label>
                                    <input type="text" id="lastName" name="last_name" placeholder="Фамилия">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>
</main>

@include('partials.footer')
@endsection

@section('scripts')
<script src="{{ asset('assets/medx/script.js') }}"></script>
<script src="{{ asset('assets/medx/js/profile.js') }}"></script>
@endsection
