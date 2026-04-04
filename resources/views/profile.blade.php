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
        <h1 style="font-size: 1.5rem; font-weight: 600; color: #1e2c41; margin-bottom: 1.25rem;">МОЙ ПРОФИЛЬ</h1>
    </section>

    <section class="page-header" style="margin: 0 40px 40px; position: relative; padding: 12px; background-color: var(--white); border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-sm); max-width: 1280px; height: 167px; text-align: center;">
        <div style="position: absolute; width: 560px; height: 167px; left: 0; top: 0; background-image: url('/assets/medx/img/main/settings/page-header-bg-1.png'); background-repeat: no-repeat; z-index: 0; pointer-events: none;"></div>
        <div style="position: absolute; width: 860px; height: 167px; left: 430px; top: 0; background-image: url('/assets/medx/img/main/settings/page-header-bg-2.png'); background-repeat: no-repeat; z-index: 0; pointer-events: none;"></div>
        <div style="position: relative;">
            <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M40 0C62.0914 0 80 17.9086 80 40C80 45.6225 78.837 50.9728 76.7432 55.8271C74.0158 52.4174 69.8211 50.2325 65.1152 50.2324C56.8952 50.2324 50.2314 56.8962 50.2314 65.1162C50.2315 69.8219 52.4157 74.0167 55.8252 76.7441C50.9714 78.8374 45.6217 80 40 80C17.9086 80 0 62.0914 0 40C0 17.9086 17.9086 0 40 0Z" fill="#3B97A2"/>
                <path d="M28.3203 60.7817V19.5352H33.6226V38.0654L50.4226 19.5352H57.1761L38.4226 39.9073L53.2016 56.4004C52.2416 57.0404 51.2016 59.8671 50.8016 61.2004L33.6226 41.9166V60.7817H28.3203Z" fill="white"/>
                <circle cx="65.117" cy="65.116" r="13.0233" fill="#28282F"/>
                <rect width="17.3643" height="17.3643" transform="translate(56.4336 56.4336)" fill="#28282F"/>
                <path d="M63.047 61.4H60.489C59.6178 61.4 59.1819 61.4 58.8492 61.5744C58.5565 61.7278 58.3187 61.9724 58.1695 62.2734C58 62.6157 58 63.0641 58 63.9602V68.4402C58 69.3362 58 69.7837 58.1695 70.1259C58.3187 70.427 58.5565 70.6724 58.8492 70.8258C59.1816 71 59.617 71 60.4865 71H69.5135C70.383 71 70.8178 71 71.1502 70.8258C71.4429 70.6724 71.6815 70.427 71.8306 70.1259C72 69.784 72 69.3368 72 68.4425V63.9575C72 63.0632 72 62.6154 71.8306 62.2734C71.6815 61.9724 71.4429 61.7278 71.1502 61.5744C70.8174 61.4 70.3825 61.4 69.5113 61.4H66.9529M63.047 61.4H63.0951M63.047 61.4C63.0567 61.4 63.067 61.4 63.0778 61.4L63.0951 61.4M63.047 61.4C62.9644 61.4 62.9182 61.3995 62.8816 61.3953C62.4246 61.3427 62.1112 60.8953 62.2071 60.4327C62.2161 60.3896 62.2337 60.3352 62.2687 60.2273L62.2702 60.2227C62.3101 60.0995 62.3301 60.0379 62.3521 59.9836C62.5779 59.4271 63.0892 59.0487 63.6738 59.0043C63.7309 59 63.7937 59 63.9199 59H66.08C66.2063 59 66.2695 59 66.3266 59.0043C66.9113 59.0487 67.422 59.4271 67.6478 59.9836C67.6698 60.0379 67.6899 60.0995 67.7298 60.2227C67.7658 60.3336 67.7838 60.3891 67.7928 60.4327C67.8888 60.8953 67.5758 61.3427 67.1188 61.3953C67.0823 61.3995 67.0355 61.4 66.9529 61.4M63.0951 61.4H66.9048M66.9048 61.4H66.9529M66.9048 61.4L66.922 61.4C66.9328 61.4 66.9431 61.4 66.9529 61.4M65 68.6C63.7113 68.6 62.6667 67.5255 62.6667 66.2C62.6667 64.8745 63.7113 63.8 65 63.8C66.2887 63.8 67.3333 64.8745 67.3333 66.2C67.3333 67.5255 66.2887 68.6 65 68.6Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
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
