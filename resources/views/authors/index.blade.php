{{-- 
    Страница списка авторов
    Миграция из legacy: site/modules/sfera/authors/authors.tpl
    Дата создания: 2026-02-27
--}}
@extends('layouts.app')

@section('title', 'Авторы - Творческий Центр СФЕРА')

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
        <span>Авторы</span>
    </div>
</div>

<!-- Authors Page -->
<main class="catalog-page">
    <div class="container">
        <div class="catalog-header">
            <h1>Авторы</h1>
            <p class="catalog-description">Список всех авторов в алфавитном порядке</p>
        </div>

        <div class="authors-list">
@if(count($groupedAuthors) > 0)
    @foreach($groupedAuthors as $letter => $authorsInGroup)
            <div class="authors-group">
                <h2 class="authors-letter">{{ $letter }}</h2>
                <div class="authors-grid">
        @foreach($authorsInGroup as $author)
                    <div class="author-item">
                        <a href="/author/?author_name={{ urlencode($author['name']) }}" class="author-link">
                            <span class="author-name">{{ $author['name'] }}</span>
                            <span class="author-count">({{ $author['count'] }} книг)</span>
                        </a>
                    </div>
        @endforeach
                </div>
            </div>
    @endforeach
@else
            <p>Авторы не найдены.</p>
@endif
        </div>
    </div>
</main>
@endsection
