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
    <section>
        <h1 class="settings-title">НАСТРОЙКИ</h1>
        <nav class="breadcrumbs">
            <a href="{{ url('/main_settings') }}">МОЙ ПРОФИЛЬ</a>
            <a class="active" href="{{ url('/design_settings') }}">ОФОРМЛЕНИЕ</a>
            <a href="{{ url('/support_settings') }}">ПОДДЕРЖКА</a>
            <a href="#">Q&A</a>
            <a href="#">О НАС</a>
            <a href="#">КОНТАКТЫ</a>
        </nav>
    </section>
    <section class="topics-section">
        <h2>Выбор темы:</h2>
        <div class="topics">
            <div class="topic">
                <img class="light" alt="light_topic" src="{{ asset('images/icons/settings/light_topic.png') }}">
                <input type="checkbox">светлая
            </div>
            <div class="topic">
                <img class="dark" alt="dark_topic" src="{{ asset('images/icons/settings/dark_topic.png') }}">
                <input type="checkbox">тёмная
            </div>
            <div class="topic">
                <img class="auto" alt="auto_topic" src="{{ asset('images/icons/settings/auto_topic.png') }}">
                <input type="checkbox">автоматическая
            </div>
        </div>
    </section>
</main>
@endsection

@section('scripts')
<script src="{{ asset('assets/medx/main_script.js') }}"></script>
@endsection
