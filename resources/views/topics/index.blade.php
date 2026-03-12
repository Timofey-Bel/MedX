{{-- 
    Страница списка тематик
    Миграция из legacy: site/modules/sfera/topics/topics.tpl
    Дата создания: 2026-02-27
--}}
@extends('layouts.app')

@section('title', 'Тематики - Творческий Центр СФЕРА')

@push('styles')
    <link rel="stylesheet" href="/assets/sfera/css/catalog.css">
@endpush

@section('content')

<!-- Breadcrumbs -->
<div class="breadcrumbs">
    <div class="container">
        <a href="/">Главная</a>
        <span>/</span>
        <a href="/catalog/">Каталог</a>
        <span>/</span>
        <span>Тематики</span>
    </div>
</div>

<!-- Topics Page -->
<main class="catalog-page">
    <div class="container">
        <div class="catalog-header">
            <h1>Тематики</h1>
            <p class="catalog-description">Список всех тематик в алфавитном порядке</p>
        </div>

        <div class="authors-list">
@if(count($groupedTopics) > 0)
    @foreach($groupedTopics as $letter => $topicsInGroup)
            <div class="authors-group">
                <h2 class="authors-letter">{{ $letter }}</h2>
                <div class="authors-grid">
        @foreach($topicsInGroup as $topic)
                    <div class="author-item">
                        <a href="/topic/?topic_id={{ $topic['id'] }}" class="author-link">
                            <span class="author-name">{{ $topic['name'] }}</span>
                            <span class="author-count">({{ $topic['count'] }} книг)</span>
                        </a>
                    </div>
        @endforeach
                </div>
            </div>
    @endforeach
@else
            <p>Тематики не найдены.</p>
@endif
        </div>
    </div>
</main>
@endsection
