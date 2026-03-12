@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="/assets/sfera/styles.css">
    <link rel="stylesheet" href="/assets/sfera/css/header.css">
    <link rel="stylesheet" href="/assets/sfera/css/catalog.css">
@endpush

@section('content')
<!-- Breadcrumbs -->
<div class="breadcrumbs">
    <div class="container">
        <a href="/">Главная</a>
        <span>/</span>
        @if (isset($search_query) && $search_query)
            <a href="/search/">Поиск</a>
            <span>/</span>
            <span>{{ $search_query }}</span>
        @else
            <a href="{{ route('catalog.index') }}">Каталог</a>
            @if (isset($category['id']) && $category['id'])
                <span>/</span>
                <span>{{ $category['name'] }}</span>
            @endif
        @endif
    </div>
</div>

<!-- Catalog Page -->
<main class="catalog-page">
    <div class="container">
        <div class="catalog-layout">
            <!-- Sidebar Filters -->
            @php
                $action_url = route('catalog.index');
                if (isset($search_query) && $search_query) {
                    $action_url = route('search.index'); // Предполагаем, что у вас есть маршрут 'search.index'
                } elseif (isset($category['id']) && $category['id']) {
                    $action_url = route('catalog.category', ['category_id' => $category['id']]);
                }
            @endphp
            {{-- TODO: Мигрировать модуль filter. Пока это заглушка. --}}
            <div class="sidebar-filters">
                <form method="GET" action="{{ route('catalog.index', ['category_id' => $category['id'] ?? null]) }}">
                    {{-- Временно отключено для диагностики производительности
                    <h3>Категории</h3>
                    <ul class="category-list">
                        @foreach ($categories as $cat)
                            @include('catalog.partials.category_item', ['category' => $cat, 'current_category_id' => $category['id'] ?? null])
                        @endforeach
                    </ul>
                    --}}

                    {{-- Фильтр по авторам --}}
                    @if (!empty($authors))
                        <div class="filter-section">
                            <h4>Автор</h4>
                            @foreach ($authors as $author)
                                <label class="filter-checkbox">
                                    <input type="checkbox" name="author[]" value="{{ $author['name'] }}" @if(in_array($author['name'], $filter_authors)) checked @endif>
                                    {{ $author['name'] }} ({{ $author['count'] }})
                                </label>
                            @endforeach
                        </div>
                    @endif

                    {{-- Фильтр по возрасту --}}
                    @if (!empty($ages))
                        <div class="filter-section">
                            <h4>Возраст</h4>
                            @foreach ($ages as $age)
                                <label class="filter-checkbox">
                                    <input type="checkbox" name="age[]" value="{{ $age['value'] }}" @if(in_array($age['value'], $filter_ages)) checked @endif>
                                    {{ $age['value'] }} ({{ $age['count'] }})
                                </label>
                            @endforeach
                        </div>
                    @endif

                    {{-- Фильтр по сериям --}}
                    @if (!empty($series))
                        <div class="filter-section">
                            <h4>Серия</h4>
                            @foreach ($series as $seriya)
                                <label class="filter-checkbox">
                                    <input type="checkbox" name="seriya[]" value="{{ $seriya['id'] }}" @if(in_array($seriya['id'], $filter_series)) checked @endif>
                                    {{ $seriya['name'] }} ({{ $seriya['count'] }})
                                </label>
                            @endforeach
                        </div>
                    @endif

                    {{-- Фильтр по типам товаров --}}
                    @if (!empty($productTypes))
                        <div class="filter-section">
                            <h4>Тип товара</h4>
                            @foreach ($productTypes as $productType)
                                <label class="filter-checkbox">
                                    <input type="checkbox" name="product_type[]" value="{{ $productType['id'] }}" @if(in_array($productType['id'], $filter_product_types)) checked @endif>
                                    {{ $productType['name'] }} ({{ $productType['count'] }})
                                </label>
                            @endforeach
                        </div>
                    @endif

                    {{-- Фильтр по тематикам --}}
                    @if (!empty($topics))
                        <div class="filter-section">
                            <h4>Тематика</h4>
                            @foreach ($topics as $topic)
                                <label class="filter-checkbox">
                                    <input type="checkbox" name="topic[]" value="{{ $topic['id'] }}" @if(in_array($topic['id'], $filter_topics)) checked @endif>
                                    {{ $topic['name'] }} ({{ $topic['count'] }})
                                </label>
                            @endforeach
                        </div>
                    @endif

                <button type="submit" class="btn btn-primary mt-3">Применить фильтр</button>
                </form>
            </div>

            <!-- Main Content -->
            <div class="catalog-content">
                <!-- Toolbar -->
                <div class="catalog-toolbar">
                    <div class="toolbar-left">
                        @if (isset($search_query) && $search_query)
                            <h1>Результаты поиска: "{{ $search_query }}"</h1>
                        @else
                            <h1>{{ $category['name'] ?? 'Каталог' }}</h1>
                        @endif
                        <span class="results-count" id="resultsCount">Найдено {{ $total }} </span>
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
                                <button class="product-favorite @if (isset($favorites['items'][$product['id']])) favorite-filled @endif" data-product-id="{{ $product['id'] }}" title="@if (isset($favorites['items'][$product['id']]))Удалить из избранного@elseДобавить в избранное@endif">
                                    <svg width="20" height="20" viewBox="0 0 24 24">
                                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" stroke="currentColor" stroke-width="1.5" fill="none"/>
                                    </svg>
                                </button>
                                <a href="/product/{{ $product['id'] }}/" class="product-link">
                                    <div class="product-image">
                                        <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" onerror="this.onerror=null; this.src='/assets/img/product_empty.jpg';">
                                    </div>
                                    <div class="product-info">
                                        <h3 class="product-title">{{ $product['name'] }}</h3>

                                        <div class="vi_24 vi0_24 p6b3_0_4-a p6b3_0_4-a0 p6b3_0_4-a1 tsBodyMBold"
                                             style="text-align: left;height: 22px;">
                                            @if ($product['rating'] > 0 || $product['reviews_count'] > 0)
                                                @if ($product['rating'] > 0)
                                                    <span class="p6b3_0_4-a4">
                                                        <svg
                                                                xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                                class="p6b3_0_4-a6 p6b3_0_4-a5" style="color: var(--graphicRating);"><path
                                                                    fill="currentColor"
                                                                    d="M8 2a1 1 0 0 1 .87.508l1.538 2.723 2.782.537a1 1 0 0 1 .538 1.667L11.711 9.58l.512 3.266A1 1 0 0 1 10.8 13.9L8 12.548 5.2 13.9a1 1 0 0 1-1.423-1.055l.512-3.266-2.017-2.144a1 1 0 0 1 .538-1.667l2.782-.537 1.537-2.723A1 1 0 0 1 8 2"></path>
                                                        </svg>
                                                        <span style="color:var(--textPremium);">{{ $product['rating'] }}</span>
                                                    </span>
                                                @endif
                                                @if ($product['reviews_count'] > 0)
                                                    <span class="p6b3_0_4-a4">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                             height="16" class="p6b3_0_4-a5"
                                                                             style="color: var(--graphicTertiary);"><path
                                                                fill="currentColor"
                                                                d="M8.545 13C11.93 13 14 11.102 14 8s-2.07-5-5.455-5C5.161 3 3.091 4.897 3.091 8c0 1.202.31 2.223.889 3.023-.2.335-.42.643-.656.899-.494.539-.494 1.077.494 1.077.89 0 1.652-.15 2.308-.394.703.259 1.514.394 2.42.394"></path>
                                                        </svg>
                                                        {{-- TODO: Реализовать Smarty-модификатор |fins для склонения слов --}}
                                                        <span style="color: var(--textSecondary);">{{ number_format($product['reviews_count'], 0) }}&nbsp;отзыв(ов)</span>
                                                    </span>
                                                @endif
                                            @endif
                                        </div>

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
                                            <input type="number" class="qty-input" value="{{ $cart['items'][$product['id']]['product_amount'] }}" min="1" max="{{ $product['quantity'] }}" data-product-id="{{ $product['id'] }}">
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
                            <p>Товары не найдены</p>
                        </div>
                    @endif
                </div>

                <!-- Pagination -->
                @if ($pages > 1)
                    @php
                        // Формируем параметры для пагинации с сохранением всех фильтров
                        $paginationParams = [];
                        if (isset($category['id']) && $category['id']) {
                            $paginationParams['category_id'] = $category['id'];
                        }
                        if (!empty($filter_authors)) {
                            $paginationParams['author'] = $filter_authors;
                        }
                        if (!empty($filter_ages)) {
                            $paginationParams['age'] = $filter_ages;
                        }
                        if (!empty($filter_series)) {
                            $paginationParams['seriya'] = $filter_series;
                        }
                        if (!empty($filter_product_types)) {
                            $paginationParams['product_type'] = $filter_product_types;
                        }
                        if (!empty($filter_topics)) {
                            $paginationParams['topic'] = $filter_topics;
                        }
                    @endphp
                    <div class="pagination">
                        @if ($hasPrevGroup)
                            @if (isset($search_query) && $search_query)
                                <a href="{{ route('search.index', array_merge(['query' => $search_query, 'page' => $prevGroupEnd], $paginationParams)) }}" class="pagination-btn">
                            @else
                                <a href="{{ route('catalog.index', array_merge(['page' => $prevGroupEnd], $paginationParams)) }}" class="pagination-btn">
                            @endif
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
                                @if (isset($search_query) && $search_query)
                                    <a href="{{ route('search.index', array_merge(['query' => $search_query, 'page' => $i], $paginationParams)) }}" class="pagination-btn">{{ $i }}</a>
                                @else
                                    <a href="{{ route('catalog.index', array_merge(['page' => $i], $paginationParams)) }}" class="pagination-btn">{{ $i }}</a>
                                @endif
                            @endif
                        @endfor
                        @if ($hasNextGroup)
                            @if (isset($search_query) && $search_query)
                                <a href="{{ route('search.index', array_merge(['query' => $search_query, 'page' => $nextGroupStart], $paginationParams)) }}" class="pagination-btn">
                            @else
                                <a href="{{ route('catalog.index', array_merge(['page' => $nextGroupStart], $paginationParams)) }}" class="pagination-btn">
                            @endif
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