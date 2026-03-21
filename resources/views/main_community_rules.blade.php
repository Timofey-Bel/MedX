@extends('layouts.app')

@section('title', 'MedX Правила сообщества')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/main_styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main_header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main_community_rules.css') }}">
@endsection

@section('content')
@include('partials.main_header')
@include('partials.main_mobile_menu')

<div class="overlay" id="overlay"></div>

<main>
    <section>
        <h1 class="breadcrumbs-title">Сообщество</h1>
        <nav class="breadcrumbs">
            <a href="{{ url('/main_community') }}">про сообщество</a>
            <a class="active" href="{{ url('/main_community_rules') }}">правила</a>
            <a href="{{ url('/main_confidentiality') }}">Конфиденциальность</a>
        </nav>
    </section>

    <section class="accordion-section">
        <h2 class="section-title">Правила общения в чате MedX</h2>
        
        <div class="accordion">
            <div class="accordion-item">
                <div class="accordion-header">
                    <div class="accordion-question">Уважение к друг другу</div>
                    <div class="accordion-toggle"></div>
                </div>
                <div class="accordion-content">
                    <div class="accordion-answer">
                        Относитесь к другим участникам с уважением. Будьте вежливы и корректны в общении.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <div class="accordion-header">
                    <div class="accordion-question">Польза</div>
                    <div class="accordion-toggle"></div>
                </div>
                <div class="accordion-content">
                    <div class="accordion-answer">
                        Делитесь полезной информацией, помогайте другим участникам сообщества.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <div class="accordion-header">
                    <div class="accordion-question">Аргументация</div>
                    <div class="accordion-toggle"></div>
                </div>
                <div class="accordion-content">
                    <div class="accordion-answer">
                        Подкрепляйте свои утверждения фактами и аргументами.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <div class="accordion-header">
                    <div class="accordion-question">Если не уверены — спрашивайте</div>
                    <div class="accordion-toggle"></div>
                </div>
                <div class="accordion-content">
                    <div class="accordion-answer">
                        Не стесняйтесь задавать вопросы, если что-то непонятно.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="accordion-section">
        <h2 class="section-title forbidden">Участникам запрещается:</h2>
        
        <div class="accordion">
            <div class="accordion-item">
                <div class="accordion-header">
                    <div class="accordion-question">Оскорбления, грубость и дискриминация</div>
                    <div class="accordion-toggle"></div>
                </div>
                <div class="accordion-content">
                    <div class="accordion-answer">
                        Запрещены любые формы оскорблений, грубости и дискриминации по любым признакам.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <div class="accordion-header">
                    <div class="accordion-question">Обсуждать политические и религиозные темы</div>
                    <div class="accordion-toggle"></div>
                </div>
                <div class="accordion-content">
                    <div class="accordion-answer">
                        Политические и религиозные темы не обсуждаются в сообществе.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <div class="accordion-header">
                    <div class="accordion-question">Публиковать Шокирующий контент</div>
                    <div class="accordion-toggle"></div>
                </div>
                <div class="accordion-content">
                    <div class="accordion-answer">
                        Запрещено публиковать шокирующий, жестокий или неприемлемый контент.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <div class="accordion-header">
                    <div class="accordion-question">Призывыть к насилию, незаконным действиям или экстремизму</div>
                    <div class="accordion-toggle"></div>
                </div>
                <div class="accordion-content">
                    <div class="accordion-answer">
                        Строго запрещены призывы к насилию, незаконным действиям или экстремизму.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <div class="accordion-header">
                    <div class="accordion-question">Публиковать Ложную или антинаучную информацию</div>
                    <div class="accordion-toggle"></div>
                </div>
                <div class="accordion-content">
                    <div class="accordion-answer">
                        Запрещено распространять ложную или антинаучную информацию.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <div class="accordion-header">
                    <div class="accordion-question">Осуществлять Рекламу и коммерческую деятельность</div>
                    <div class="accordion-toggle"></div>
                </div>
                <div class="accordion-content">
                    <div class="accordion-answer">
                        Реклама и коммерческая деятельность без согласования запрещены.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <div class="accordion-header">
                    <div class="accordion-question">Флуд, спам и бессмысленные сообщения</div>
                    <div class="accordion-toggle"></div>
                </div>
                <div class="accordion-content">
                    <div class="accordion-answer">
                        Запрещен флуд, спам и публикация бессмысленных сообщений.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <div class="accordion-header">
                    <div class="accordion-question">Обсуждение наркотических средств</div>
                    <div class="accordion-toggle"></div>
                </div>
                <div class="accordion-content">
                    <div class="accordion-answer">
                        Обсуждение наркотических средств строго запрещено.
                    </div>
                </div>
            </div>
        </div>
    </section>

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

</main>
@endsection

@section('scripts')
<script src="{{ asset('assets/medx/main_script.js') }}"></script>
@endsection
