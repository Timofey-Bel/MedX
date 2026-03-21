@extends('layouts.app')

@section('title', 'MedX учись практикуй расти')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/style_fixed.css') }}">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
@endsection

@section('content')
@include('partials.header')

<!-- Main Content -->
<main class="main-content">

    <!-- Скопировано с https://igrobeauty.ru/catalog/zagars/%D0%B0%D0%BF%D1%80%D0%BE%D1%80%D0%BF%D1%80l/ -->
    <!-- Время копирования: 09.11.2025, 16:43:26 -->
    <link rel="stylesheet" href="https://igrobeauty.ru/local/templates/igro/css/style.css?1761136762188524">
    <style nonce="" data-href="">
        .bx-404-container { text-align: center; }
        .bx-404-text-block { margin-top: 16px; }
        .bx-404-text-block h1 { color: rgb(145, 162, 168); font-size: 86px; font-weight: 300; }
        .bx-404-text-block h2 { font-weight: 400; margin-bottom: 32px; font-size: 18px; }
        .bx-404-block { margin-top: 16px; }
        .button_404 { border-radius: 16px; color: rgb(255, 255, 255); background-color: rgb(145, 162, 168); padding: 12px 24px; transition: 0.5s; }
        .button_404:hover { text-decoration: none; opacity: 0.8; }

    </style>
    <div class="bx-404-container">
        <div class="bx-404-text-block"><h1>Oops</h1><h2>Неправильный адрес или такой страницы на сайте больше не существует.</h2></div>
        <div class=""><a class="button_404" href="/">На главную</a></div>
        <div class="bx-404-block"><img src="/images/img_cats.png" alt=""></div>
    </div>

</main>
@include('partials.footer')
@endsection