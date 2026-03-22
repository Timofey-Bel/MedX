@extends('layouts.app')

@section('title', 'MedX Оформление')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/main_styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main_header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main_settings.css') }}">
    <link rel="stylesheet" href="{{ asset('css/design_settings.css') }}">
@endsection

@section('content')
@include('partials.main_header')
@include('partials.main_mobile_menu')

<div class="overlay" id="overlay"></div>

<main>
    <img src="https://medx/images/icons/settings/design-settings-line.png" alt="" class="design-bg-vector">
    <section>
        <h1 class="settings-title">НАСТРОЙКИ</h1>
        <nav class="breadcrumbs">
            <a href="{{ url('/main_settings') }}">МОЙ ПРОФИЛЬ</a>
            <a class="active" href="{{ url('/design_settings') }}">ОФОРМЛЕНИЕ</a>
            <a href="{{ url('/support_settings') }}">ПОДДЕРЖКА</a>
            <!-- <a href="#">Q&A</a>
            <a href="#">О НАС</a>
            <a href="#">КОНТАКТЫ</a> -->
        </nav>
    </section>
    <section class="topics-section">
        <h2>Выбор темы:</h2>
        <div class="topics">
            <div class="topic">
                <img class="light" alt="light_topic" src="{{ asset('images/icons/settings/light_topic.png') }}">
                <div class="topic-remember">
                    <input type="checkbox" id="light-topic" name="light-topic" value="1" class="topic-remember-checkbox">
                    <label for="light-topic" class="topic-remember-label">
                        <span class="check-topic-box"></span>
                        <span class="topic-remember-text">Светлая</span>
                    </label>
                </div>
            </div>
            <div class="topic">
                <img class="dark" alt="dark_topic" src="{{ asset('images/icons/settings/dark_topic.png') }}">
                <div class="topic-remember">
                    <input type="checkbox" id="dark-topic" name="dark-topic" value="1" class="topic-remember-checkbox">
                    <label for="dark-topic" class="topic-remember-label">
                        <span class="check-topic-box"></span>
                        <span class="topic-remember-text">Тёмная</span>
                    </label>
                </div>
            </div>
            <div class="topic">
                <img class="auto" alt="auto_topic" src="{{ asset('images/icons/settings/auto_topic.png') }}">
                <div class="topic-remember">
                    <input type="checkbox" id="auto-topic" name="auto-topic" value="1" class="topic-remember-checkbox">
                    <label for="auto-topic" class="topic-remember-label">
                        <span class="check-topic-box"></span>
                        <span class="topic-remember-text">Авто</span>
                    </label>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection

@section('scripts')
<script src="{{ asset('assets/medx/main_script.js') }}"></script>
@endsection
