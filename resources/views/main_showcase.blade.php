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
<!-- Модальное окно (скрыто по умолчанию) -->
<div class="oops-modal" id="oopsModal">
    <div class="oops-modal-overlay"></div>
    <div class="oops-modal-content">
        <!-- Кнопка закрытия -->
        <button class="oops-modal-close" id="closeOopsModal">
            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_4495_2345)">
                    <path d="M10.9999 10.9999L6 6M6 6L1 1M6 6L11 1M6 6L1 11" stroke="white" stroke-width="2"
                          stroke-linecap="round" stroke-linejoin="round"/>
                </g>
                <defs>
                    <clipPath id="clip0_4495_2345">
                        <rect width="12" height="12" fill="white"/>
                    </clipPath>
                </defs>
            </svg>
        </button>

        <!-- Контент -->
        <div class="oops-modal-body">
            <div class="oops-decoration-line"></div>
            <h3 class="oops-modal-title">Упс!</h3>
            <p class="oops-modal-subtitle">
                Эта функция доступна только для зарегестрированных пользователей.
            </p>
            <div class="oops-button">
                <a href="{{ url('/register') }}" class="register-btn">
                    ЗАРЕГЕСТРИРОВАТЬСЯ
                </a>
            </div>
        </div>
    </div>
</div>


<main class="showcase">
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
            <a href="#" class="view-all">
                <div class="arrow-icon"></div>
            </a>
        </div>
        <div class="daily-card">
            <div class="calendar">
                <div class="calendar-nav">
                    <div class="nav-arrow prev"></div>
                    <div class="days-container">
                        <div class="day active">
                            <div class="day-dot"></div>
                            <span>29.11</span>
                        </div>
                        <div class="day active">
                            <div class="day-dot"></div>
                            <span>30.11</span>
                        </div>
                        <div class="day missed">
                            <div class="day-dot"></div>
                            <span>01.12</span>
                        </div>
                        <div class="day active">
                            <div class="day-dot"></div>
                            <span>02.12</span>
                        </div>
                        <div class="day missed">
                            <div class="day-dot"></div>
                            <span>03.12</span>
                        </div>
                        <div class="day missed">
                            <div class="day-dot"></div>
                            <span>04.12</span>
                        </div>
                        <div class="day active">
                            <div class="day-dot"></div>
                            <span>05.12</span>
                        </div>
                    </div>
                    <div class="nav-arrow next"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Таймер -->
    <section class="timer-section">
        <div class="section-header">
            <h2>Помодоро – таймер</h2>
            <div class="help-icon">?</div>
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
            <div class="timer-label">#1</div>
            <div class="timer-display">
                <div class="timer-value">25:00</div>
                <div class="timer-divider"></div>
            </div>
            <div class="timer-controls">
                <div class="timer-indicator">
                    <div class="play-icon"></div>
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
        <div class="news-card">
            <div class="news-image"></div>
            <div class="news-date">27.09.2025</div>
            <h3 class="news-title">Команда MedX посетила детский дом</h3>
            <div class="news-pagination">
                <div class="dot active"></div>
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </div>
        </div>
    </section>

</main>
@endsection

@section('scripts')
<script src="{{ asset('assets/medx/main_script.js') }}"></script>
<script>
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggleBtn');
    const overlay = document.getElementById('overlay');

    function openSidebar() {
        sidebar.classList.add('expanded');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden'; // Блокируем скролл
    }

    function closeSidebar() {
        sidebar.classList.remove('expanded');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    toggleBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        if (sidebar.classList.contains('expanded')) {
            closeSidebar();
        } else {
            openSidebar();
        }
    });

    // Закрытие кликом на оверлей или вне сайдбара
    overlay.addEventListener('click', closeSidebar);
    document.addEventListener('click', (e) => {
        if (!sidebar.contains(e.target) && sidebar.classList.contains('expanded')) {
            closeSidebar();
        }
    });

    // Закрытие на ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && sidebar.classList.contains('expanded')) {
            closeSidebar();
        }
    });


    // Сердечко
    const favoriteBtn = document.getElementById('favorite-btn');
    if (favoriteBtn) {
        favoriteBtn.addEventListener('click', function () {
            const heart = document.getElementById('heartIcon');
            heart.classList.toggle('active');
        });
    }

    // Меню навигация
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => {
        item.addEventListener('click', function () {
            navItems.forEach(nav => nav.classList.remove('active'));
            this.classList.add('active');
        });
    });


    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('oopsModal');
        const closeBtn = document.getElementById('closeOopsModal');

        // Функция показа модалки
        window.showOopsModal = function () {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        };

        // Функция закрытия
        function closeModal() {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }

        // События закрытия
        closeBtn.addEventListener('click', closeModal);
        modal.querySelector('.oops-modal-overlay').addEventListener('click', closeModal);

        // ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modal.classList.contains('active')) {
                closeModal();
            }
        });
    });
</script>
@endsection
