{{-- 
    Страница списка серий
    Миграция из legacy: site/modules/sfera/series/series.tpl
    Дата создания: 2026-02-27
--}}
@extends('layouts.app')

@section('title', 'Серии - Творческий Центр СФЕРА')

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
        <span>Серии</span>
    </div>
</div>

<!-- Series Page -->
<main class="catalog-page">
    <div class="container">
        <div class="catalog-header">
            <h1>Серии</h1>
            <p class="catalog-description">Список всех серий в алфавитном порядке</p>
        </div>

        <div class="authors-list">
@if(count($groupedSeries) > 0)
    @foreach($groupedSeries as $letter => $seriesInGroup)
            <div class="authors-group">
                <h2 class="authors-letter">{{ $letter }}</h2>
                <div class="authors-grid">
        @foreach($seriesInGroup as $serie)
                    <div class="author-item">
                        <a href="/seriya/?seriya_id={{ $serie['id'] }}" class="author-link">
                            <span class="author-name">{{ $serie['name'] }}</span>
                            <span class="author-count">({{ $serie['count'] }} книг)</span>
                        </a>
                    </div>
        @endforeach
                </div>
            </div>
    @endforeach
@else
            <p>Серии не найдены.</p>
@endif
        </div>
    </div>
</main>
@endsection
