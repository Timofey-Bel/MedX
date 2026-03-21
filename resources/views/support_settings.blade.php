@extends('layouts.app')

@section('title', 'MedX Поддержка')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/main_styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main_header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main_settings.css') }}">
    <link rel="stylesheet" href="{{ asset('css/support_settings.css') }}">
@endsection

@section('content')
@include('partials.main_header')
@include('partials.main_mobile_menu')

<div class="overlay" id="overlay"></div>

<main>
    <section>
        <h1 class="settings-title">НАСТРОЙКИ</h1>
        <nav class="breadcrumbs">
            <a href="{{ url('/main_settings') }}">МОЙ ПРОФИЛЬ</a>
            <a href="{{ url('/design_settings') }}">ОФОРМЛЕНИЕ</a>
            <a class="active" href="{{ url('/support_settings') }}">ПОДДЕРЖКА</a>
            <a href="#">Q&A</a>
            <a href="#">О НАС</a>
            <a href="#">КОНТАКТЫ</a>
        </nav>
    </section>
    <section>
        <h2>Собрали вопросы в одном месте
            <a href="{{ url('/faq') }}">на странице FAQ.</a>
            Возможно, там найдётся ответ и на твой вопрос:)</h2>
    </section>
    <section class="faq-section">
        <h2 class="section-title">Самые популярные из них:</h2>

        <div class="accordion">
            <div class="accordion-item">
                <div class="accordion-header">
                    <div class="accordion-question">Как устроено взаимодействие внутри сообщества MedX?</div>
                    <div class="accordion-toggle"></div>
                </div>
                <div class="accordion-content">
                    <div class="accordion-answer">
                        Внутри сообщества MEDX взаимодействие строится на открытости и взаимопомощи:
                        участники общаются на форуме, в тематических чатах, участвуют в вебинарах, задают вопросы экспертам и обмениваются опытом.
                        Регулярно проходят онлайн-встречи и дискуссии.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <div class="accordion-header">
                    <div class="accordion-question">Можно ли делиться успехами и результатами в MedX?</div>
                    <div class="accordion-toggle"></div>
                </div>
                <div class="accordion-content">
                    <div class="accordion-answer">
                        Да, делиться успехами и результатами не только можно, но и приветствуется!
                        В сообществе создана поддерживающая среда: есть специальные рубрики (например, «Истории успеха»),
                        где участники рассказывают о своих достижениях, вдохновляя других.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <div class="accordion-header">
                    <div class="accordion-question">Как пользователи могут стать волонтёрами MedX?</div>
                    <div class="accordion-toggle"></div>
                </div>
                <div class="accordion-content">
                    <div class="accordion-answer">
                        Чтобы стать волонтёром, нужно заполнить анкету на сайте или написать в службу поддержки.
                        Волонтёры помогают в организации мероприятий, модерации, создании контента. Отбор проходит
                        по результатам собеседования или после ознакомительного курса.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <div class="accordion-header">
                    <div class="accordion-question">Есть ли у MedX оффлайн-встречи и мероприятия?</div>
                    <div class="accordion-toggle"></div>
                </div>
                <div class="accordion-content">
                    <div class="accordion-answer">
                        Да, MEDX проводит офлайн-встречи и мероприятия в разных городах, а также участвует в профильных конференциях.
                        Анонсы публикуются в календаре на сайте и в соцсетях. Участники могут предлагать свои города для встреч.
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="questions-block">
        <div class="vector-26"></div>
        <div class="vector-27"></div>
        <h2 class="questions-title">НЕ НАШЛОСЬ ОТВЕТА НА ТВОЙ ВОПРОС?</h2>
        <p class="questions-description">Напишите нам в чат поддержки наша команда с радостью поможет!</p>
        <a href="#" class="btn-support">
            <i class="fab fa-telegram"></i>
            ЧАТ ПОДДЕРЖКИ
        </a>
    </section>
</main>
@endsection

@section('scripts')
<script src="{{ asset('assets/medx/main_script.js') }}"></script>
@endsection
