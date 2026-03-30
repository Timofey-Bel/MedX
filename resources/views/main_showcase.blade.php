@extends('layouts.app')

@section('title', 'MedX учись практикуй расти')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/main_styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main_header.css') }}">
@endsection

@section('content')
@include('partials.main_header')
@include('partials.main_mobile_menu')
<!-- Затемняющий фон -->

<div class="overlay" id="overlay"></div>

<main class="showcase">
        <img src="https://medx/images/ui/Vector-96.png" alt="" class="showcase-bg-vector">
    <!-- Приветствие -->
    <section class="greeting-section">
        <div class="weather-icon">
            <img src="{{ asset('images/main/sun.png') }}" alt="sun">
        </div>
        <div class="greeting-text">
            <h1>ДОБРЫЙ ДЕНЬ, КРИСТИНА!</h1>
            <p>СТАНЕМ СЕГОДНЯ БЛИЖЕ К ЦЕЛИ?</p>
        </div>
    </section>

    <!-- Последняя активность -->
    <section class="activity-section">
        <div class="section-header">
            <h2>Последняя активность</h2>
            <a href="#" class="view-all">
                смотреть всё
                <div class="arrow-icon"></div>
            </a>
        </div>
        <div class="activity-card">
            <div class="activity-image"></div>
            <div class="activity-content">
                <div class="activity-header">
                    <div class="activity-tags">
                        <span class="tag">Кардиология / </span>
                    </div>
                    <div class="activity-notification" onclick="showOopsModal()" id="favorite-btn">
                        <div class="heart-icon" id="heartIcon"></div>
                    </div>
                </div>
                <h3>Расшифровка ЭКГ: <br> Наджелудочные аритмии</h3>
                <div class="activity-footer">
                    <div class="progress-section">
                        <span class="progress-label">Ещё не начато</span>
                        <div class="progress-bar">
                            <div class="progress-fill"></div>
                        </div>
                    </div>
                    <button class="continue-btn">Изучить</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Статьи -->
    <section class="articles-section">
        <div class="section-header">
            <h2>Статьи</h2>
            <a href="#" class="view-all">
                <div class="arrow-icon"></div>
            </a>
        </div>
        <div class="article-card">
            <p>В 1980 году в Джорджии врачи зафиксировали у мужчины температуру тела в 46,5°C. Это казалось
                несовместимым с жизнью, однако пациент выжил. Что позволило ему выстоять перед лицом смерти?</p>
            <a href="#" class="read-more">ЧИТАТЬ ДАЛЕЕ...</a>
        </div>
    </section>

    <!-- Мои результаты -->
    <section class="results-section">
        <div class="section-header">
            <h2>Мои результаты</h2>
            <span class="tag">Этот месяц
                <div>
                    <svg width="8" height="4" viewBox="0 0 8 4" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.601562 0.599609L3.24801 3.24606C3.44327 3.44132 3.75985 3.44132 3.95512 3.24606L6.60156 0.599609"
                              stroke="#419AA4" stroke-width="1.2" stroke-linecap="round"/>
                    </svg>
                </div>
            </span>
        </div>
        <div class="results-card">
            <div class="gauge-chart">
                <div class="gauge-background"></div>
            </div>
            <button class="full-stats-btn">полная статистика</button>
        </div>
    </section>

    <!-- Ежедневный вход -->
    <section class="daily-section">
        <div class="section-header">
            <h2>Ежедневный вход</h2>
            <a href="#" class="view-all" id="openFullCalendar">
                <div class="arrow-icon"></div>
            </a>
        </div>
        <div class="daily-card">
            <div class="calendar">
                <div class="calendar-nav">
                    <div class="nav-arrow prev swiper-button-prev-daily"></div>
                    <div class="swiper daily-swiper">
                        <div class="swiper-wrapper days-container">
                            <!-- Дни генерируются динамически через JS -->
                        </div>
                    </div>
                    <div class="nav-arrow next swiper-button-next-daily"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Модальное окно полного календаря -->
    <div class="calendar-modal" id="calendarModal">
        <div class="calendar-modal-content">
            <div class="calendar-modal-header">
                <h2>История посещений</h2>
                <button class="calendar-modal-close" id="closeCalendarModal">&times;</button>
            </div>
             <div class="calendar-modal-header-2">
                <h2>История посещений</h2>
            </div>
            <div class="calendar-modal-body" id="fullCalendarContainer">
                <!-- Календарь генерируется через JS -->
            </div>
        </div>
    </div>

    <!-- Таймер -->
    <section class="timer-section">
        <div class="section-header">
            <h2>Помодоро – таймер</h2>
            <div class="help-icon" id="pomodoroHelp">?</div>
        </div>
        <div class="timer-card">
            <div class="timer-background">
                <div class="timer-decoration-left">
                    <svg width="49" height="53" viewBox="0 0 49 53" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 4C16.4985 4 45.3959 10.528 44.9959 52" stroke="#CCE5E7" stroke-width="8"/>
                    </svg>
                </div>
                <div class="timer-decoration-right">
                    <svg width="49" height="64" viewBox="0 0 49 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6.10799 0.411133C6.10799 6.60493 45.1717 47.3807 24.1748 57.7037C0.571592 69.3081 -13.9873 17.9602 49 17.9602"
                              stroke="#CCE5E7" stroke-width="8"/>
                    </svg>
                </div>
            </div>
            <!-- Модальное окно помодоро информация -->
            <div class="timer-background-info" id="pomodoroInfoModal">
                <p style="margin-bottom: 8px;">
                    Помодоро – таймер — это техника управления временем, основанная на чередовании коротких периодов сфокусированной работы (25 минут) и коротких перерывов (5 минут). После 4х таких рабочих интервалов делается более длительный перерыв (15–30 минут).
                </p>
                <p>
                    Таймер помогает бороться с прокрастинацией и повышать продуктивность, разбивая крупные задачи на управляемые части.
                </p>
            </div>
            <div class="timer-label" id="timerRound">#1</div>
            <div class="timer-display">
                <div class="timer-value-container">
                    <input type="text" class="timer-value-input" id="timerMinutes" maxlength="2" value="25" />
                    <span class="timer-colon">:</span>
                    <input type="text" class="timer-value-input" id="timerSeconds" maxlength="2" value="00" />
                </div>
                <div class="timer-value-display hidden" id="timerValue">25:00</div>
                <div class="timer-divider"></div>
            </div>
            <div class="timer-controls">
                <div class="timer-indicator" id="timerControls">
                    <button class="timer-btn play-btn" id="playBtn" title="Запустить">
                        <img src="{{ asset('images/main/play-icon.svg') }}" alt="Play" />
                    </button>
                    <button class="timer-btn pause-btn hidden" id="pauseBtn" title="Пауза">
                        <img src="{{ asset('images/main/stop-icon.svg') }}" alt="Pause" />
                    </button>
                    <button class="timer-btn reset-btn hidden" id="resetBtn" title="Сброс">
                        <img src="{{ asset('images/main/return-icon.svg') }}" alt="Reset" />
                    </button>
                </div>
            </div>
        </div>
    </section>


    <!-- Новости -->
    <section class="news-section">
        <div class="section-header">
            <h2>Новости</h2>
            <a href="#" class="faq-link">
                btn FAQ
                <div class="arrow-icon"></div>
            </a>
        </div>
        <div class="swiper news-swiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="news-card">
                        <div class="news-item">
                            <div class="news-image"></div>
                            <div class="news-date">27.09.2025</div>
                            <h3 class="news-title">Команда MedX посетила детский дом</h3>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="news-card">
                        <div class="news-item">
                            <div class="news-image" style="background-image: url('/assets/medx/img/main/showcase/news-bg.png');"></div>
                            <div class="news-date">15.09.2025</div>
                            <h3 class="news-title">Новое обновление платформы MedX</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="swiper-pagination news-pagination"></div>
        </div>
    </section>

</main>
@endsection

@section('scripts')
<script src="{{ asset('assets/medx/main_script.js') }}"></script>
<script src="{{ asset('js/pomodoro-timer.js') }}"></script>
<script src="{{ asset('js/daily-calendar.js') }}"></script>
<script>
    // Переключение между таймером и информацией
    document.addEventListener('DOMContentLoaded', function () {
        const pomodoroHelp = document.getElementById('pomodoroHelp');
        const timerBackground = document.querySelector('.timer-background');
        const timerBackgroundInfo = document.querySelector('.timer-background-info');
        const timerLabel = document.getElementById('timerRound');
        const timerDisplay = document.querySelector('.timer-display');
        const timerControls = document.querySelector('.timer-controls');
        
        if (pomodoroHelp && timerBackground && timerBackgroundInfo) {
            pomodoroHelp.addEventListener('click', function() {
                pomodoroHelp.classList.toggle('active');
                timerBackground.classList.toggle('hidden');
                timerBackgroundInfo.classList.toggle('active');
                
                if (timerLabel) timerLabel.classList.toggle('hidden');
                if (timerDisplay) timerDisplay.classList.toggle('hidden');
                if (timerControls) timerControls.classList.toggle('hidden');
            });
        }
        
        // Инициализация Swiper для новостей (только на мобильных)
        if (window.innerWidth <= 768) {
            const newsSwiper = new Swiper('.news-swiper', {
                slidesPerView: 1,
                spaceBetween: 16,
                pagination: {
                    el: '.news-pagination',
                    clickable: true,
                },
            });
        }
    });
</script>
@endsection
