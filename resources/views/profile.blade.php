@extends('layouts.app')

@section('title', 'MedX - Профиль')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/main_styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main_header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
@include('partials.main_header')
@include('partials.main_mobile_menu')

<div class="overlay" id="overlay"></div>

<main class="profile-container">
    <section style="margin: 0 40px 40px;">
        <h1 style="font-size: 2rem; font-weight: 600; color: #28282F; margin-bottom: 1.25rem; margin: 27px 0;">МОЙ ПРОФИЛЬ</h1>
    </section>

    <section class="page-header" style="margin: 0 40px 40px; position: relative; padding: 12px; background-color: var(--white); border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-sm); max-width: 1280px; height: 167px; text-align: center;">
        <div style="position: absolute; width: 560px; height: 167px; left: 0; top: 0; background-image: url('/assets/medx/img/main/settings/page-header-bg-1.png'); background-repeat: no-repeat; z-index: 0; pointer-events: none;"></div>
        <div style="position: absolute; width: 860px; height: 167px; left: 430px; top: 0; background-image: url('/assets/medx/img/main/settings/page-header-bg-2.png'); background-repeat: no-repeat; z-index: 0; pointer-events: none;"></div>
        <div style="position: relative;">
            @if(Auth::user()->avatar_url)
                <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <clipPath id="avatarClip">
                            <circle cx="40" cy="40" r="40"/>
                        </clipPath>
                    </defs>
                    <image href="{{ Auth::user()->avatar_url }}" width="80" height="80" clip-path="url(#avatarClip)" preserveAspectRatio="xMidYMid slice"/>
                </svg>
            @else
                <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="40" cy="40" r="40" fill="#3B97A2"/>
                    <text x="40" y="48" font-family="Involve-Medium, Helvetica" font-size="42" font-weight="500" fill="white" text-anchor="middle" dominant-baseline="middle">{{ Auth::user()->avatar_letter }}</text>
                </svg>
            @endif
        </div>
        <div style="position: relative; display: flex; justify-content: center; font-family: 'Involve-Medium', Helvetica; font-size: 20px; gap: 10px;">
            <h2>{{ Auth::user()->display_name }}</h2>
        </div>
        <h2 style="position: relative; font-family: 'Involve-Medium', Helvetica; font-size: 12px;">{{ Auth::user()->email }}</h2>
    </section>

    <section class="section-main-information" style="margin: 0 40px 40px; position: relative; padding: 28px; background-color: var(--white); border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-sm); max-width: 1280px; min-height: 267px;">
        <h2 style="font-size: 1.25rem; font-weight: 600; color: #1e2c41; margin-bottom: 1.25rem; padding: 0; display: flex; align-items: center; justify-content: flex-start;">Основная информация</h2>
        <div style="content: ''; position: absolute; top: 75px; left: 50%; transform: translateX(-50%); width: 1224px; height: 1px; background-color: rgba(0, 124, 137, 0.2);"></div>
        
        <div class="profile-info-display" style="margin-top: 35px;">
            <div class="info-row">
                <div class="info-item">
                    <label>Имя</label>
                    <p>{{ Auth::user()->first_name ?: 'Не указано' }}</p>
                </div>
                <div class="info-item">
                    <label>Фамилия</label>
                    <p>{{ Auth::user()->last_name ?: 'Не указано' }}</p>
                </div>
            </div>
            <div class="info-row">
                <div class="info-item">
                    <label>Пол</label>
                    <p>
                        @if(Auth::user()->gender === 'male')
                            Мужской
                        @elseif(Auth::user()->gender === 'female')
                            Женский
                        @elseif(Auth::user()->gender === 'other')
                            Другой
                        @else
                            Не указан
                        @endif
                    </p>
                </div>
                <div class="info-item">
                    <label>Дата рождения</label>
                    <p>{{ Auth::user()->birthdate ? Auth::user()->birthdate->format('d.m.Y') : 'Не указана' }}</p>
                </div>
            </div>
            <div class="info-row">
                <div class="info-item">
                    <label>Email</label>
                    <p>{{ Auth::user()->email }}</p>
                </div>
                <div class="info-item">
                    <label>Часовой пояс</label>
                    <p>{{ Auth::user()->timezone ?: 'Не указан' }}</p>
                </div>
            </div>
            <a href="{{ route('main_settings') }}" class="btn btn-primary" style="margin-top: 20px; display: inline-block; text-decoration: none;">Изменить данные</a>
        </div>
    </section>

    <section class="section-achievements" style="margin: 0 40px 40px; position: relative; padding: 28px; background-color: var(--white); border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-sm); max-width: 1280px; min-height: 267px;">
        <h2 style="font-size: 1.25rem; font-weight: 600; color: #1e2c41; margin-bottom: 1.25rem; padding: 0; display: flex; align-items: center; justify-content: flex-start;">Мои достижения</h2>
        <div style="content: ''; position: absolute; top: 75px; left: 50%; transform: translateX(-50%); width: 1224px; height: 1px; background-color: rgba(0, 124, 137, 0.2);"></div>
        
        <div class="achievements-grid" id="achievementsGrid" style="margin-top: 35px;">
            <!-- Достижения генерируются через JS -->
        </div>
    </section>
</main>
@endsection

@section('scripts')
<script src="{{ asset('assets/medx/main_script.js') }}"></script>
<script src="{{ asset('js/achievements.js') }}"></script>
@endsection
