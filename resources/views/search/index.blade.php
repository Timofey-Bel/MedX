@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="/assets/sfera/styles.css">
    <link rel="stylesheet" href="/assets/sfera/css/header.css">
    <link rel="stylesheet" href="/assets/sfera/css/catalog.css">
    <link rel="stylesheet" href="/assets/sfera/css/search.css">
@endpush

@section('content')
<!-- Breadcrumbs -->
<div class="breadcrumbs">
    <div class="container">
        <a href="/">Главная</a>
        <span>/</span>
        <a href="{{ route('search') }}">Поиск</a>
        @if (!empty($search_query))
            <span>/</span>
            <span>{{ $search_query }}</span>
        @endif
    </div>
</div>

<!-- Search Page -->
<main class="catalog-page search-page">
    <div class="container">
        <div class="catalog-layout">
            <!-- Sidebar Filters (optional for search) -->
            <div class="sidebar-filters">
                <form method="GET" action="{{ route('search') }}">
                    <!-- Сохраняем поисковый запрос -->
                    <input type="hidden" name="query" value="{{ $search_query }}">
                    
                    {{-- Фильтр по авторам (если есть результаты) --}}
                    @if (!empty($products))
                        <div class="filter-section">
                            <h4>Уточнить поиск</h4>
                            <p class="filter-hint">Используйте фильтры для уточнения результатов</p>
                        </div>
                    @endif
                    
                    {{-- Кнопка применения фильтров --}}
                    @if (!empty($products))
                        <button type="submit" class="btn btn-primary mt-3">Применить фильтр</button>
                    @endif
                </form>
            </div>

            <!-- Main Content -->
            <div class="catalog-content">
                <!-- Toolbar -->
                <div class="catalog-toolbar">
                    <div class="toolbar-left">
                        @if (!empty($search_query))
                            <h1>Результаты поиска: "{{ $search_query }}"</h1>
                            <span class="results-count" id="resultsCount">Найдено {{ $total }}</span>
                        @else
                            <h1>Поиск товаров</h1>
                            <p>Введите запрос в поле поиска</p>
                        @endif
                    </div>
                    <div class="toolbar-right">
                        {{-- Здесь могут быть сортировки --}}
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="products-grid" id="productsGrid">
                    @if (count($products) > 0)
                        @foreach ($products as $product)
                            <article class="product-card">
                                <button class="product-favorite @if (isset($favorites['items'][$product['id']])) favorite-filled @endif" 
                                        data-product-id="{{ $product['id'] }}" 
                                        title="@if (isset($favorites['items'][$product['id']]))Удалить из избранного@elseДобавить в избранное@endif">
                                    <svg width="20" height="20" viewBox="0 0 24 24">
                                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" 
                                              stroke="currentColor" stroke-width="1.5" fill="none"/>
                                    </svg>
                                </button>
                                <a href="/product/{{ $product['id'] }}/" class="product-link">
                                    <div class="product-image">
                                        <img src="{{ $product['image'] }}" 
                                             alt="{{ $product['name'] }}" 
                                             onerror="this.onerror=null; this.src='/assets/img/product_empty.jpg';">
                                    </div>
                                    <div class="product-info">
                                        <h3 class="product-title">{{ $product['name'] }}</h3>

                                        @if ($product['price'] && $product['price'] > 0)
                                            <div class="product-price">
                                                <span class="price-current">{{ $product['price'] }} ₽</span>
                                            </div>
                                        @endif
                                    </div>
                                </a>
                                <div class="product-actions">
                                    @if (isset($cart['items'][$product['id']]) && isset($cart['items'][$product['id']]['product_amount']))
                                        <button class="btn-add-to-cart" data-product-id="{{ $product['id'] }}" type="button" style="display: none;">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M3 3h2l1 4m0 0l3 11h10l3-11H6z"/>
                                                <circle cx="9" cy="20" r="1" fill="currentColor"/>
                                                <circle cx="17" cy="20" r="1" fill="currentColor"/>
                                            </svg>
                                            <span>В корзину</span>
                                        </button>
                                        <a href="#" class="btn-buy-all" data-product-id="{{ $product['id'] }}" data-max-quantity="{{ $product['quantity'] }}">Купить всё</a>
                                        <div class="product-quantity-control" data-product-id="{{ $product['id'] }}">
                                            <button class="qty-btn qty-minus" data-product-id="{{ $product['id'] }}" type="button">−</button>
                                            <input type="number" class="qty-input" value="{{ $cart['items'][$product['id']]['product_amount'] }}" 
                                                   min="1" max="{{ $product['quantity'] }}" data-product-id="{{ $product['id'] }}">
                                            <button class="qty-btn qty-plus" data-product-id="{{ $product['id'] }}" type="button">+</button>
                                        </div>
                                    @else
                                        <button class="btn-add-to-cart" data-product-id="{{ $product['id'] }}" type="button">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M3 3h2l1 4m0 0l3 11h10l3-11H6z"/>
                                                <circle cx="9" cy="20" r="1" fill="currentColor"/>
                                                <circle cx="17" cy="20" r="1" fill="currentColor"/>
                                            </svg>
                                            <span>В корзину</span>
                                        </button>
                                        <a href="#" class="btn-buy-all" data-product-id="{{ $product['id'] }}" data-max-quantity="{{ $product['quantity'] }}">Купить всё</a>
                                        <div class="product-quantity-control hidden" data-product-id="{{ $product['id'] }}">
                                            <button class="qty-btn qty-minus" data-product-id="{{ $product['id'] }}" type="button">−</button>
                                            <input type="number" class="qty-input" value="1" min="1" max="{{ $product['quantity'] }}" data-product-id="{{ $product['id'] }}">
                                            <button class="qty-btn qty-plus" data-product-id="{{ $product['id'] }}" type="button">+</button>
                                        </div>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    @else
                        <div class="no-products">
                            @if (!empty($search_query))
                                <p>По запросу «{{ $search_query }}» ничего не найдено</p>
                                <p class="search-hint">Попробуйте изменить запрос или использовать другие ключевые слова</p>
                            @else
                                <p>Введите запрос в поле поиска</p>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Pagination -->
                @if ($pages > 1)
                    <div class="pagination">
                        @if ($hasPrevGroup)
                            <a href="{{ route('search', ['query' => $search_query, 'page' => $prevGroupEnd]) }}" class="pagination-btn">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <path d="M10 4l-4 4 4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </a>
                        @else
                            <button class="pagination-btn" disabled>
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <path d="M10 4l-4 4 4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </button>
                        @endif
                        
                        @for ($i = $startPage; $i <= $endPage; $i++)
                            @if ($i == $page)
                                <button class="pagination-btn active">{{ $i }}</button>
                            @else
                                <a href="{{ route('search', ['query' => $search_query, 'page' => $i]) }}" class="pagination-btn">{{ $i }}</a>
                            @endif
                        @endfor
                        
                        @if ($hasNextGroup)
                            <a href="{{ route('search', ['query' => $search_query, 'page' => $nextGroupStart]) }}" class="pagination-btn">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <path d="M6 4l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </a>
                        @else
                            <button class="pagination-btn" disabled>
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <path d="M6 4l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
    <script src="/assets/sfera/js/catalog.js"></script>
@endpush
