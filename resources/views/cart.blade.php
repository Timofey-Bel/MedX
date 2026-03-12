@extends('layouts.app')

@section('title', 'Корзина - Творческий Центр СФЕРА')

{{-- Подключаем CSS для корзины из legacy системы --}}
@push('styles')
<link rel="stylesheet" href="/assets/sfera/css/cart.css">
@endpush

@section('content')
{{-- 
    МИГРАЦИЯ: Страница корзины из legacy Smarty в Laravel Blade
    Legacy: legacy/site/modules/sfera/cart/cart.tpl
    
    Изменения:
    - Smarty синтаксис (~~...~) заменен на Blade (@...)
    - Сохранены все Knockout.js биндинги (data-bind)
    - Сохранена структура HTML и CSS классы из legacy
    - Исправлены биндинги для передачи guid вместо всего объекта
    - Добавлена кнопка "Очистить корзину" в page-header
    - Сохранена структура cart-container с cart-items и cart-summary
    - Все CSS классы соответствуют legacy/site/modules/sfera/cart/cart.tpl
--}}

<!-- Main Content -->
<main class="cart-page">
    <div class="container">
        <div class="page-header">
            <h1>Корзина</h1>
            <button class="btn btn-outline" data-bind="click: clearCart">Очистить корзину</button>
        </div>

        <div class="cart-container">
            {{-- Товары в корзине --}}
            <!-- ko if: items().length > 0 -->
            <div class="cart-items" id="cartItems" data-bind="foreach: items">
                {{-- Товар (knockout template) --}}
                <div class="cart-item" data-bind="attr: {'data-id': id}">

                    {{-- Checkbox для выбора товара --}}
                    <label class="checkbox-wrapper">
                        <input type="checkbox" class="item-checkbox" data-bind="checked: selected">
                        <span class="checkmark"></span>
                    </label>

                    {{-- Изображение товара --}}
                    <div class="item-image">
                        <a target="_blank" data-bind="attr: {href: productUrl()}">
                            <img data-bind="attr: {src: image, alt: name}" onerror="this.src='/assets/img/product_empty.jpg'">
                        </a>
                    </div>

                    {{-- Детали товара --}}
                    <div class="item-details">
                        <h3 class="item-title">
                            <a target="_blank" data-bind="text: name(), attr: {href: productUrl()}" class="item-title-link"></a>
                        </h3>

                        {{-- Действия с товаром --}}
                        <div class="item-actions">
                            <button class="action-btn remove-btn" data-bind="click: function(data, event) { $root.removeItem(ko.unwrap(data.guid) || ko.unwrap(data.id), data, event); }">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                </svg>
                                Удалить
                            </button>
                        </div>
                    </div>

                    {{-- Контролы количества и цены --}}
                    <div class="item-controls">
                        <div class="quantity-control">
                            <button class="qty-btn minus" data-bind="click: function(data, event) { $root.decreaseAmount(ko.unwrap(data.guid) || ko.unwrap(data.id), data, event); }">−</button>
                            <input type="number" class="qty-input" data-bind="value: product_amount, attr: {min: 1, max: max_quantity() || 99}, event: {change: function(data, event) { $root.updateAmount(ko.unwrap(data.guid) || ko.unwrap(data.id), ko.unwrap(data.product_amount)); }}">
                            <button class="qty-btn plus" data-bind="click: function(data, event) { $root.increaseAmount(ko.unwrap(data.guid) || ko.unwrap(data.id), data, event); }">+</button>
                        </div>

                        <div class="item-price-section">
                            <div class="item-price" data-bind="text: formattedPrice"></div>
                            <!-- ko if: hasDiscount -->
                            <div class="item-old-price" data-bind="text: formattedOldPrice"></div>
                            <!-- /ko -->
                            <div class="item-total-price" data-bind="text: formattedTotal"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /ko -->
            
            {{-- Пустая корзина --}}
            <!-- ko if: items().length === 0 -->
            <div class="cart-empty">
                <p>Корзина пуста</p>
            </div>
            <!-- /ko -->

            {{-- Итого (sidebar) --}}
            <aside class="cart-summary">
                <div class="summary-card">
                    <h3>Ваш заказ</h3>

                    <div class="summary-row">
                        <span>Товары (<span data-bind="text: total_cart_amount"></span>)</span>
                        <span data-bind="text: formattedItemsTotal"></span>
                    </div>

                    <!-- ko if: cart_discount() > 0 -->
                    <div class="summary-row">
                        <span>Скидка</span>
                        <span class="discount" data-bind="text: formattedDiscountTotal"></span>
                    </div>
                    <!-- /ko -->

                    <div class="summary-divider"></div>

                    <div class="summary-row total">
                        <span>Итого</span>
                        <strong data-bind="text: formatted_total_cart_sum"></strong>
                    </div>

                    <button class="btn btn-primary btn-checkout" 
                        style="
                            width: 100%;
                            padding: 16px;
                            background: #005bff;
                            border: none;
                            border-radius: 12px;
                            color: #ffffff;
                            font-size: 16px;
                            font-weight: 500;
                            cursor: pointer;
                            transition: all 0.2s;
                            margin-bottom: 16px;
                        "
                        data-bind="click: function() { window.location.href = '{{ route('checkout') }}'; }, enable: total_cart_sum() > 0">
                        Перейти к оформлению
                    </button>

                    <p class="summary-note">
                        Доступные способы и время доставки можно выбрать при оформлении заказа
                    </p>
                </div>

                <div class="promo-card">
                    <h4>Промокод или сертификат</h4>
                    <div class="promo-input-group">
                        <input type="text" class="promo-input" placeholder="Введите код" data-bind="value: promoCode, valueUpdate: 'afterkeydown'">
                        <button class="btn btn-secondary" data-bind="click: try2apply_promocode, enable: promoCode().length > 0">Применить</button>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</main>
@endsection
