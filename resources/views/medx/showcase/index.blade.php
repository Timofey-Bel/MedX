@extends('layouts.medx')

@section('title', 'MedX - Учись, практикуй, расти')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/medx/css/showcase.css') }}">
<link rel="stylesheet" href="{{ asset('assets/medx/css/disciplines.css') }}">
<link rel="stylesheet" href="{{ asset('assets/medx/css/features.css') }}">
<link rel="stylesheet" href="{{ asset('assets/medx/css/learn-section.css') }}">
@endpush

@section('content')
{{-- Hero Section --}}
<section class="mainblock">
    <div class="mainblock-bg"></div>
    <div class="textmain">
        <img class="logo-2" src="{{ asset('assets/medx/img/main/showcase/logo-2@3x.png') }}" alt="MedX"/>
        <p class="h">ИНТЕРАКТИВНАЯ ОБРАЗОВАТЕЛЬНАЯ ПЛАТФОРМА ДЛЯ МЕДИКОВ</p>
        <p class="h-2">Знания, практика и сообщество <br/>для студентов в один клик</p>
    </div>
    <a href="{{ route('medx.register') }}" class="btn_free">
        <div class="text-wrapper-29">НАЧАТЬ БЕСПЛАТНО</div>
    </a>
</section>

{{-- Promo Banner --}}
<section class="conveyor-container">
    <div class="conveyor-bar">
        <p class="element">РЕГИСТРИРУЙСЯ СЕЙЧАС И ПОЛУЧИ ДОСТУП КО ВСЕМ МАТЕРИАЛАМ БЕСПЛАТНО НА 3 ДНЯ</p>
        <img class="vector-8" src="{{ asset('assets/medx/img/main/showcase/vector-11.png') }}" alt=""/>
        <p class="element">РЕГИСТРИРУЙСЯ СЕЙЧАС И ПОЛУЧИ ДОСТУП КО ВСЕМ МАТЕРИАЛАМ БЕСПЛАТНО НА 3 ДНЯ</p>
        <img class="vector-8" src="{{ asset('assets/medx/img/main/showcase/vector-11.png') }}" alt=""/>
        <p class="element">РЕГИСТРИРУЙСЯ СЕЙЧАС И ПОЛУЧИ ДОСТУП КО ВСЕМ МАТЕРИАЛАМ БЕСПЛАТНО НА 3 ДНЯ</p>
        <img class="vector-8" src="{{ asset('assets/medx/img/main/showcase/vector-11.png') }}" alt=""/>
        <p class="element">РЕГИСТРИРУЙСЯ СЕЙЧАС И ПОЛУЧИ ДОСТУП КО ВСЕМ МАТЕРИАЛАМ БЕСПЛАТНО НА 3 ДНЯ</p>
        <img class="vector-8" src="{{ asset('assets/medx/img/main/showcase/vector-11.png') }}" alt=""/>
    </div>
</section>

{{-- About Section --}}
<section class="block-about-us">
    <div class="text">
        <div class="text-2">
            <p class="med-x-2">ДОБРО ПОЖАЛОВАТЬ В MEDX —</p>
            <p class="text-wrapper-17">ТВОЙ ИНСТРУМЕНТ ДЛЯ ЭФФЕКТИВНОГО И ПРОДУКТИВНОГО ОБУЧЕНИЯ</p>
            <p class="text-wrapper-18">Забудь о скучных учебниках, утомительном поиске и хаосе в интернете.</p>
            <p class="text-wrapper-19">
                MedX — это структурированная база знаний, где эксклюзивные материалы созданы и собраны в одном месте,
                чтобы ты мог учиться удобно и интересно
            </p>
        </div>
    </div>
    <div class="scroll-cards">
        <div class="card-wrapper">
            <div class="frame-wrapper">
                <div class="frame-19">
                    <div class="rectangle-4"></div>
                    <div class="frame-20">
                        <div class="text-wrapper-20">21.09.2025</div>
                        <div class="text-wrapper-21">СТРОЕНИЕ ЧЕРЕПА</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-wrapper">
            <div class="card-4">
                <div class="frame-19">
                    <div class="rectangle-4"></div>
                    <div class="frame-20">
                        <div class="text-wrapper-22">21.09.2025</div>
                        <div class="text-wrapper-21">СТРОЕНИЕ ЧЕРЕПА</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-wrapper">
            <div class="card-5">
                <div class="frame-19">
                    <div class="rectangle-4"></div>
                    <div class="frame-20">
                        <div class="text-wrapper-22">21.09.2025</div>
                        <div class="text-wrapper-21">СТРОЕНИЕ ЧЕРЕПА</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Disciplines Section --}}
@include('medx.showcase.disciplines')

{{-- Features Section --}}
@include('medx.showcase.feature-cards')

{{-- Learn Section --}}
@include('medx.showcase.learn-section')

{{-- CTA Section --}}
<section class="frame-30">
    <a href="{{ route('medx.register') }}" class="text-wrapper-29">НАЧАТЬ БЕСПЛАТНО</a>
</section>

{{-- Devices Section --}}
<section class="under">
    <p class="med-x">MEDX ВСЕГДА С СОБОЙ.<br/>УЧИСЬ НА ЛЮБОМ УСТРОЙСТВЕ.</p>
    <img class="free-mobile-friendly" src="{{ asset('assets/medx/img/main/showcase/devices.png') }}" alt="Devices"/>
</section>
@endsection

@push('scripts')
<script src="{{ asset('assets/medx/js/showcase.js') }}"></script>
@endpush
