@extends('layouts.app')

@section('title', 'MedX Сообщество')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/main_styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main_header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main_community.css') }}">
@endsection

@section('content')
@include('partials.main_header')
@include('partials.main_mobile_menu')

<div class="overlay" id="overlay"></div>

<main class="community-container">
    <!-- Декоративный фон -->
    <img src="{{ asset('images/community/vector-139.svg') }}" alt="" class="community-bg-vector">
    
    <!-- Поиск
    <div class="search-container">
        <input type="text" class="search-box" placeholder="Поиск">
        <img src="{{ asset('images/icons/menu/note-icon.svg') }}" alt="Search" class="search-icon">
    </div> -->
    
    <!-- Заголовок -->
   
    <section>
         <h1 class="breadcrumbs-title">Сообщество</h1>
        <nav class="breadcrumbs">
            <a class="active" href="{{ url('/main_community') }}">ПРО СООБЩЕСТВО</a>
            <a href="{{ url('/main_community_rules') }}">ПРАВИЛА</a>
            <a href="{{ url('/main_confidentiality') }}">КОНФИДЕЦИАЛЬНОСТЬ</a>
           
        </nav>
    </section>
    
    <!-- Описание -->
    <p class="community-description">
        MedX – это не только образовательная платформа с материалами для студентов-медиков, но и сообщество молодых, замотивированных и амбициозных  специалистов, которые готовы поделиться своим опытом.
    </p>
    
    <!-- Баннер -->
    <div class="community-banner">
        <img src="{{ asset('images/community/vector-26.svg') }}" alt="" class="banner-vector-left">
        <img src="{{ asset('images/community/vector-27.svg') }}" alt="" class="banner-vector-right">
        <div class="banner-content">
            <h2 class="banner-title">Становись частью MedX!</h2>
            <p class="banner-subtitle">Вместе мы построим фундамент твоего будущего как компетентного и эмпатичного врача</p>
            <a href="#" class="banner-btn">
                <img src="{{ asset('images/community/telegram-icon.svg') }}" alt="Telegram">
                вступить в сообщество
            </a>
        </div>
    </div>
    
    <!-- Миссии сообщества -->
    <h2 class="missions-title">Миссии сообщества</h2>
    
    <div class="missions-grid">
        <!-- Карточка 1 -->
        <div class="mission-card mission-card-1">
            <h3 class="mission-card-title">Развитие клинического мышления через практику</h3>
            
            <div class="mission-card-subtitle">Чат "Клинические случаи"</div>
            <p class="mission-card-text">
                Мы создаем пространство для детального анализа и обсуждения реальных клинических ситуаций. Это позволяет участникам учиться на чужом опыте, развивать дифференциальную диагностику, искать решения и понимать логику врача в сложных случаях.
            </p>
            
            <div class="mission-divider"></div>
            
            <div class="mission-card-subtitle">Чат "Дежурства"</div>
            <p class="mission-card-text">
                Мы поощряем обмен ценными наблюдениями, лайфхаками и разбор необычных или типовых ситуаций, возникающих во время дежурств. Это помогает подготовиться к реальной практике, узнать о "подводных камнях" и эффективных методах работы.
            </p>
        </div>
        
        <!-- Карточка 2 -->
        <div class="mission-card mission-card-2">
            <h3 class="mission-card-title">Накопление и систематизация уникальной базы знаний</h3>
            <p class="mission-card-text">
                Мы собираем ценный, практический контент, который часто остается "за бортом" учебников — реальные кейсы, алгоритмы действий, наблюдения из практики.
            </p>
            <p class="mission-card-text" style="margin-top: 24px;">
                Эта база становится уникальным ресурсом для всех участников, позволяя изучать медицину «изнутри», через призму реального опыта множества студентов.
            </p>
        </div>
        
        <!-- Карточка 3 -->
        <div class="mission-card mission-card-3">
            <h3 class="mission-card-title">Создание сообщества взаимопомощи и профессионального роста</h3>
            <p class="mission-card-text">
                Мы формируем поддерживающую среду, где студенты могут делиться своим опытом, получать обратную связь, задавать вопросы и находить поддержку в сложных ситуациях, связанных с учебой и практикой.
            </p>
        </div>
        
        <!-- Карточка 4 -->
        <div class="mission-card mission-card-4">
            <h3 class="mission-card-title">Подготовка к реальной медицинской практике</h3>
            <p class="mission-card-text">
                Наша цель — помочь студентам не просто получить диплом, но и быть готовыми к реальным вызовам профессии. Обсуждение случаев и дежурств дает представление о динамике работы, о том, как применять теоретические знания на практике, и развивает уверенность в своих силах.
            </p>
        </div>
    </div>
    
</main>
@endsection

@section('scripts')
<script src="{{ asset('assets/medx/main_script.js') }}"></script>
@endsection
