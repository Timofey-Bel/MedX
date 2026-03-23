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
            <!-- <a href="#">Q&A</a>
            <a href="#">О НАС</a>
            <a href="#">КОНТАКТЫ</a> -->
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
        <img src="{{ asset('images/community/vector-26.svg') }}" alt="" class="banner-vector-left">
        <img src="{{ asset('images/community/vector-27.svg') }}" alt="" class="banner-vector-right">
        <h2 class="questions-title">НЕ НАШЛОСЬ ОТВЕТА НА ТВОЙ ВОПРОС?</h2>
        <p class="questions-description">Напишите нам в чат поддержки наша команда с радостью поможет!</p>
        <a href="#" class="btn-support">
            <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M14 0C6.2685 0 0 6.2685 0 14C0 21.7315 6.2685 28 14 28C21.7315 28 28 21.7315 28 14C28 6.2685 21.7315 0 14 0ZM20.489 9.52117C20.279 11.7343 19.3667 17.1068 18.9035 19.586C18.7075 20.636 18.3202 20.9872 17.9468 21.021C17.1337 21.0957 16.5165 20.4832 15.729 19.9675C14.497 19.1602 13.8005 18.6573 12.6047 17.8687C11.2222 16.9575 12.1182 16.457 12.9057 15.6392C13.1122 15.4245 16.6938 12.166 16.7638 11.8708C16.772 11.8335 16.7813 11.6958 16.6985 11.6235C16.6157 11.5512 16.4955 11.5757 16.4092 11.5955C16.2855 11.6235 14.3173 12.9243 10.5035 15.498C9.94467 15.8818 9.43833 16.0685 8.9845 16.0592C8.48517 16.0487 7.52383 15.7768 6.80867 15.5435C5.9325 15.2588 5.23483 15.1072 5.2955 14.6242C5.327 14.3722 5.6735 14.1143 6.33617 13.8507C10.4172 12.0727 13.139 10.9002 14.5005 10.3332C18.389 8.71617 19.1963 8.435 19.7237 8.42567C20.384 8.41633 20.5427 8.96117 20.489 9.52117Z" fill="#009AAB"/>
            </svg>
            ЧАТ ПОДДЕРЖКИ
        </a>
    </section>

@endsection

@section('scripts')
<script src="{{ asset('assets/medx/main_script.js') }}"></script>
@endsection
