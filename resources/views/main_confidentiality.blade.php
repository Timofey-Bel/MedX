@extends('layouts.app')

@section('title', 'MedX Конфиденциальность')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/main_styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main_header.css') }}">
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
            <a href="{{ url('/main_community_rules') }}">правила</a>
            <a class="active" href="{{ url('/main_confidentiality') }}">Конфиденциальность</a>
        </nav>
    </section>

    <section class="accordion-section">
        <h2 class="section-title">Конфиденциальность: правила</h2>
        
        <div class="accordion">
            <div class="accordion-item">
                <div class="accordion-header">
                    <div class="accordion-question">Что запрещено публиковать в меdx?</div>
                    <div class="accordion-toggle"></div>
                </div>
                <div class="accordion-content">
                    <div class="accordion-answer">
                        Запрещено публиковать персональные данные пациентов, конфиденциальную медицинскую информацию без согласия, а также любые материалы, нарушающие врачебную тайну.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <div class="accordion-header">
                    <div class="accordion-question">Правила оформления клиничесликих случаев и историй.</div>
                    <div class="accordion-toggle"></div>
                </div>
                <div class="accordion-content">
                    <div class="accordion-answer">
                        При публикации клинических случаев необходимо полностью анонимизировать данные пациента, удалить все идентифицирующие признаки и получить согласие на публикацию, если это требуется.
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
