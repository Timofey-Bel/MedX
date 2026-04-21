@extends('layouts.app')

@section('title', 'Тарифы - MedX')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/styles.css') }}">
<link rel="stylesheet" href="{{ asset('css/tariffs.css') }}">
@endsection

@section('content')
@include('partials.header')

<main>
    <!-- Hero Section with Cards Inside -->
    <section class="tariffs-hero">
        <div class="hero-background">
            <img src="{{ asset('images/tariffs/vector-background.svg') }}" alt="" class="hero-vector">
        </div>
        <div class="hero-content">
            <h1 class="hero-title">учись эффективно с medx</h1>
            <h2 class="hero-subtitle">и делай мир лучше</h2>
        </div>

        <!-- Pricing Cards Inside Hero -->
        <div class="pricing-cards">
            <!-- Card 1: 1 месяц -->
            <div class="pricing-card pricing-card-month">
                <h3 class="card-period">1 месяц</h3>
                <p class="card-subtitle">стартовая точка</p>
                <div class="card-divider"></div>
                <div class="card-price-wrapper">
                    <div class="card-price">99₽</div>
                    <div class="card-price-old">
                        <span class="price-old-text">199₽</span>
                        <svg class="price-strikethrough" viewBox="0 0 76 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <line x1="0" y1="13" x2="76" y2="13" stroke="rgba(255, 255, 255, 0.6)" stroke-width="3"/>
                        </svg>
                    </div>
                </div>
                <p class="card-description">Идеально для старта, чтобы познакомиться с medx</p>
                <a href="{{ url('/login') }}"><button class="card-btn">Выбрать этот тариф</button></a>
            </div>

            <!-- Card 2: 1 год (Рекомендуем) -->
            <div class="pricing-card pricing-card-year">
                <div class="card-badge">Рекомендуем</div>
                <h3 class="card-period">1 год</h3>
                <p class="card-subtitle">самый популярный</p>
                <div class="card-divider"></div>
                <div class="card-price-wrapper">
                    <div class="card-price">799₽</div>
                    <div class="card-price-old">
                        <span class="price-old-text">999₽</span>
                        <svg class="price-strikethrough" viewBox="0 0 76 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <line x1="0" y1="13" x2="76" y2="13" stroke="rgba(255, 255, 255, 0.6)" stroke-width="3"/>
                        </svg>
                    </div>
                </div>
                <p class="card-description">самое выгодное предложение — экономия больше 30%</p>
                <a href="{{ url('/login') }}"><button class="card-btn">Выбрать этот тариф</button></a>
            </div>

            <!-- Card 3: 6 месяцев -->
            <div class="pricing-card pricing-card-halfyear">
                <h3 class="card-period">6 месяцeв</h3>
                <p class="card-subtitle">подтянуть знания</p>
                <div class="card-divider"></div>
                <div class="card-price-wrapper">
                    <div class="card-price">399₽</div>
                    <div class="card-price-old">
                        <span class="price-old-text">599₽</span>
                        <svg class="price-strikethrough" viewBox="0 0 76 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <line x1="0" y1="13" x2="76" y2="13" stroke="rgba(255, 255, 255, 0.6)" stroke-width="3"/>
                        </svg>
                    </div>
                </div>
                <p class="card-description">закрой семестр на отлично  вместе с нами</p>
                <a href="{{ url('/login') }}"><button class="card-btn">Выбрать этот тариф</button></a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="tariffs-features">
        <div class="container">
            <h2 class="features-title">Подписка на MEdx включает в себя</h2>
            <div class="features-grid">
                <div class="feature-item">
                    <div class="feature-icon">
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 5V35M5 20H35" stroke="#419AA4" stroke-width="3" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <h3 class="feature-title">100+ материалов</h3>
                    <p class="feature-description">Доступ к обширной базе знаний по медицине</p>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="5" y="10" width="30" height="25" rx="2" stroke="#419AA4" stroke-width="2"/>
                            <path d="M5 15H35" stroke="#419AA4" stroke-width="2"/>
                        </svg>
                    </div>
                    <h3 class="feature-title">Карточки</h3>
                    <p class="feature-description">Интерактивные карточки для запоминания</p>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="20" cy="20" r="15" stroke="#419AA4" stroke-width="2"/>
                            <path d="M20 12V20L26 23" stroke="#419AA4" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <h3 class="feature-title">Тесты</h3>
                    <p class="feature-description">Проверка знаний с подробными объяснениями</p>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 12L20 5L32 12V28L20 35L8 28V12Z" stroke="#419AA4" stroke-width="2"/>
                            <path d="M14 18L18 22L26 14" stroke="#419AA4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h3 class="feature-title">Видео-лекции</h3>
                    <p class="feature-description">Качественные видеоматериалы от экспертов</p>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="20" cy="20" r="15" stroke="#419AA4" stroke-width="2"/>
                            <path d="M16 15L25 20L16 25V15Z" fill="#419AA4"/>
                        </svg>
                    </div>
                    <h3 class="feature-title">Подкасты</h3>
                    <p class="feature-description">Аудиоматериалы для обучения в любое время</p>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="15" cy="15" r="5" stroke="#419AA4" stroke-width="2"/>
                            <circle cx="28" cy="15" r="5" stroke="#419AA4" stroke-width="2"/>
                            <circle cx="15" cy="28" r="5" stroke="#419AA4" stroke-width="2"/>
                            <path d="M20 15H23M15 20V23M20 28H23" stroke="#419AA4" stroke-width="2"/>
                        </svg>
                    </div>
                    <h3 class="feature-title">Сообщество</h3>
                    <p class="feature-description">Общение с коллегами и обмен опытом</p>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 5L22 15H32L24 21L27 31L20 25L13 31L16 21L8 15H18L20 5Z" stroke="#419AA4" stroke-width="2"/>
                        </svg>
                    </div>
                    <h3 class="feature-title">Дежурства</h3>
                    <p class="feature-description">Практические кейсы и разборы</p>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 30L10 20L15 25L20 15L25 22L30 12L35 20" stroke="#419AA4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h3 class="feature-title">Прогресс</h3>
                    <p class="feature-description">Отслеживание вашего развития</p>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 8L22 18L32 20L22 22L20 32L18 22L8 20L18 18L20 8Z" stroke="#419AA4" stroke-width="2"/>
                        </svg>
                    </div>
                    <h3 class="feature-title">AI рекомендации</h3>
                    <p class="feature-description">Персонализированный план обучения</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Charity Section -->
    <section class="tariffs-charity">
        <div class="container">
            <div class="charity-content">
                <h2 class="charity-title">
                    Выбирай свой тариф и совершай
                    добрые дела вместе с нами!</h2>
                <p class="charity-description">
                    Часть средств от твоей подписки мы направляем
                    на благотворительность, поддерживая детей с
                    особенностями развития и онкологией, а также приюты для животных.
                </p>
            </div>
            <div class='charity-white-bg'></div>
        </div>
    </section>
</main>

@include('partials.footer')
@endsection
