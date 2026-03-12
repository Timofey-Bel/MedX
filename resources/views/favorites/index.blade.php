{{-- Конвертировано из legacy/site/modules/sfera/favorites/favorites.tpl --}}
@extends('layouts.app')

@section('title', 'Избранное - Творческий Центр СФЕРА')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/sfera/css/favorites.css') }}">
@endpush

@section('content')
<!-- Main Content -->
<main class="favorites-page">
    <div class="container">
        <div class="page-header">
            <h1>Избранное</h1>
            <div class="header-actions-bar">
                <span class="items-count" data-bind="text: itemsCountText"></span>
                <button class="btn btn-outline" data-bind="click: clearFavorites, visible: itemsCount() > 0">Очистить все</button>
            </div>
        </div>

        <div class="favorites-grid" id="favoritesGrid" data-bind="foreach: items">
            <!-- Товар (knockout template, стиль как в каталоге) -->
            <article class="favorite-card" data-bind="attr: {'data-id': id}">
                <button class="remove-btn" data-bind="click: $root.removeItem" title="Удалить из избранного">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>

                <a data-bind="attr: {href: '/product/' + id() + '/'}" class="product-link">
                    <div class="product-image">
                        <img data-bind="attr: {src: image, alt: name}" onerror="this.onerror=null; this.src='/assets/img/product_empty.jpg';">
                        <!-- ko if: badge() -->
                        <span class="badge sale" data-bind="text: badge"></span>
                        <!-- /ko -->
                    </div>
                    <div class="product-info">
                        <h3 class="product-title" data-bind="text: name"></h3>

                        <div class="vi_24 vi0_24 p6b3_0_4-a p6b3_0_4-a0 p6b3_0_4-a1 tsBodyMBold"
                             style="text-align: left;height: 22px;">

                            <!-- ko if: rating() > 0 || reviews_count() > 0 -->
                            <!-- ko if: rating() > 0 -->
                            <span class="p6b3_0_4-a4">
                                <svg
                                        xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        class="p6b3_0_4-a6 p6b3_0_4-a5" style="color: var(--graphicRating);"><path
                                            fill="currentColor"
                                            d="M8 2a1 1 0 0 1 .87.508l1.538 2.723 2.782.537a1 1 0 0 1 .538 1.667L11.711 9.58l.512 3.266A1 1 0 0 1 10.8 13.9L8 12.548 5.2 13.9a1 1 0 0 1-1.423-1.055l.512-3.266-2.017-2.144a1 1 0 0 1 .538-1.667l2.782-.537 1.537-2.723A1 1 0 0 1 8 2"></path>
                                </svg>
                                <span style="color:var(--textPremium);" data-bind="text: rating().toFixed(1)"></span>
                            </span>
                            <!-- /ko -->
                            <!-- ko if: reviews_count() > 0 -->
                            <span class="p6b3_0_4-a4">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                 height="16" class="p6b3_0_4-a5"
                                                                 style="color: var(--graphicTertiary);"><path
                                            fill="currentColor"
                                            d="M8.545 13C11.93 13 14 11.102 14 8s-2.07-5-5.455-5C5.161 3 3.091 4.897 3.091 8c0 1.202.31 2.223.889 3.023-.2.335-.42.643-.656.899-.494.539-.494 1.077.494 1.077.89 0 1.652-.15 2.308-.394.703.259 1.514.394 2.42.394"></path>
                                </svg>
                                <span style="color: var(--textSecondary);">
                                    <span data-bind="text: reviews_count().toLocaleString('ru-RU')"></span>&nbsp;отзыв<span data-bind="text: reviews_count() === 1 ? '' : (reviews_count() >= 2 && reviews_count() <= 4 ? 'а' : 'ов')"></span>
                                </span>
                            </span>
                            <!-- /ko -->
                            <!-- /ko -->
                        </div>

                        <div class="product-price">
                            <div class="price-main">
                                <span class="current-price" data-bind="text: formattedPrice"></span>
                                <!-- ko if: hasDiscount -->
                                <span class="old-price" data-bind="text: formattedOldPrice"></span>
                                <!-- /ko -->
                            </div>
                            <!-- ko if: hasDiscount -->
                            <span class="discount" data-bind="text: formattedDiscount"></span>
                            <!-- /ko -->
                        </div>
                    </div>
                </a>
                
                <div class="product-actions">
                    <!-- ko if: !inCart() -->
                    <button class="btn-add-to-cart" data-bind="attr: {'data-product-id': id}, click: $root.addToCart" type="button">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 3h2l1 4m0 0l3 11h10l3-11H6z"/>
                            <circle cx="9" cy="20" r="1" fill="currentColor"/>
                            <circle cx="17" cy="20" r="1" fill="currentColor"/>
                        </svg>
                        <span>В корзину</span>
                    </button>
                    <!-- /ko -->
                    
                    <!-- ko if: inCart() -->
                    <div class="product-quantity-control" data-bind="attr: {'data-product-id': id}">
                        <button class="qty-btn qty-minus" data-bind="attr: {'data-product-id': id}, click: $root.decreaseQuantity" type="button">−</button>
                        <input type="number" class="qty-input" min="1" data-bind="attr: {'data-product-id': id, max: quantity}, value: currentQuantity, event: {change: $root.quantityChanged}">
                        <button class="qty-btn qty-plus" data-bind="attr: {'data-product-id': id}, click: $root.increaseQuantity" type="button">+</button>
                    </div>
                    <!-- /ko -->
                    
                    <a href="#" class="btn-buy-all" data-bind="attr: {'data-product-id': id, 'data-max-quantity': quantity}, click: $root.buyAll, visible: inCart()">Купить всё</a>
                </div>
            </article>
        </div>
        
        <!-- Пустое избранное -->
        <!-- ko if: itemsCount() === 0 -->
        <div class="empty-favorites" style="text-align: center; padding: 60px 20px;">
            <svg width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="#cccccc" stroke-width="1" style="margin: 0 auto;">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
            </svg>
            <h3>Избранное пусто</h3>
            <p>Добавьте товары, чтобы не потерять их</p>
            <a href="/catalog/" class="btn btn-primary">Перейти к покупкам</a>
        </div>
        <!-- /ko -->
    </div>
</main>

<!-- Mobile Bottom Navigation -->
@include('components.mobile-bottom-nav')
@endsection

@push('scripts')
<!-- Инициализация данных избранного с сервера -->
<script>
// console.log('=== Initializing server_favorites data ===');
var server_favorites = {};
@if($count > 0)
    @php
        // Преобразуем массив items в объект с ключами по product_id для совместимости с legacy JS
        // Для авторизованных пользователей items - это массив, для неавторизованных - уже объект
        $itemsObject = [];
        if (is_array($items) && !empty($items)) {
            // Проверяем, является ли это ассоциативным массивом (для гостей) или индексным (для авторизованных)
            $firstKey = array_key_first($items);
            if (is_string($firstKey) && !is_numeric($firstKey)) {
                // Уже ассоциативный массив [product_id => data] - используем как есть
                $itemsObject = $items;
            } else {
                // Индексный массив - преобразуем в ассоциативный
                foreach ($items as $item) {
                    $itemsObject[$item['id']] = $item;
                }
            }
        }
    @endphp
    server_favorites.data = @json(['count' => $count, 'items' => $itemsObject]);
@else
    server_favorites.data = {count: 0, items: {}};
@endif
// console.log('server_favorites.data initialized:', server_favorites.data);
// console.log('Items count:', Object.keys(server_favorites.data.items || {}).length);
</script>

<script src="{{ asset('assets/sfera/js/favorites-viewmodel.js') }}"></script>
<script src="{{ asset('assets/sfera/js/favorites.js') }}"></script>
@endpush
