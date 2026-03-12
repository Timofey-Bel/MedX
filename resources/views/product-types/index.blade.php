{{-- 
    Страница списка типов товаров
    Миграция из legacy: site/modules/sfera/product_types/product_types.tpl
    Дата создания: 2026-02-27
--}}
@extends('layouts.app')

@section('title', 'Типы товаров - Творческий Центр СФЕРА')

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
        <span>Типы товаров</span>
    </div>
</div>

<!-- Product Types Page -->
<main class="catalog-page">
    <div class="container">
        <div class="catalog-header">
            <h1>Типы товаров</h1>
            <p class="catalog-description">Список всех типов товаров в алфавитном порядке</p>
        </div>

        <div class="authors-list">
@if(count($groupedProductTypes) > 0)
    @foreach($groupedProductTypes as $letter => $productTypesInGroup)
            <div class="authors-group">
                <h2 class="authors-letter">{{ $letter }}</h2>
                <div class="authors-grid">
        @foreach($productTypesInGroup as $productType)
                    <div class="author-item">
                        <a href="/product_type/?product_type_id={{ $productType['id'] }}" class="author-link">
                            <span class="author-name">{{ $productType['name'] }}</span>
                            <span class="author-count">({{ $productType['count'] }} книг)</span>
                        </a>
                    </div>
        @endforeach
                </div>
            </div>
    @endforeach
@else
            <p>Типы товаров не найдены.</p>
@endif
        </div>
    </div>
</main>
@endsection
