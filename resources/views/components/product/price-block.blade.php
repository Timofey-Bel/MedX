{{--
    Компонент блока цены и добавления в корзину
    
    Props:
    - $product (object): Объект товара с полями price, old_price, discount_percent, in_stock
    - $cartBindings (string): Knockout.js data-bind атрибуты для кнопки корзины
    
    Структура сохраняет все CSS классы из legacy системы для совместимости с Knockout.js
--}}

<div class="product-purchase-sidebar">
    <div class="purchase-sticky-content">
        {{-- Блок цены --}}
        <div class="product-price-block">
            <div class="price-main">
                <span class="price-current">{{ number_format($product->price, 0, ',', ' ') }} ₽</span>
                
                @if(isset($product->old_price) && $product->old_price > 0)
                    <span class="price-old">{{ number_format($product->old_price, 0, ',', ' ') }} ₽</span>
                @endif
                
                @if(isset($product->discount_percent) && $product->discount_percent > 0)
                    <span class="discount-badge">-{{ $product->discount_percent }}%</span>
                @endif
            </div>
        </div>

        {{-- Селектор количества --}}
        <div class="quantity-selector">
            <label for="productQuantity" class="quantity-label">Количество:</label>
            <div class="quantity-controls">
                <button type="button" class="quantity-btn quantity-minus" aria-label="Уменьшить количество">−</button>
                <input type="number" 
                       id="productQuantity" 
                       class="quantity-input" 
                       value="1" 
                       min="1" 
                       max="999"
                       aria-label="Количество товара">
                <button type="button" class="quantity-btn quantity-plus" aria-label="Увеличить количество">+</button>
            </div>
        </div>

        {{-- Кнопка добавления в корзину или сообщение о недоступности --}}
        @if($product->in_stock)
            <button class="btn-add-to-cart" 
                    data-bind="{{ $cartBindings }}"
                    data-product-id="{{ $product->id }}"
                    type="button">
                <svg class="cart-icon" width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <path d="M7 18C7.55228 18 8 17.5523 8 17C8 16.4477 7.55228 16 7 16C6.44772 16 6 16.4477 6 17C6 17.5523 6.44772 18 7 18Z" fill="currentColor"/>
                    <path d="M16 18C16.5523 18 17 17.5523 17 17C17 16.4477 16.5523 16 16 16C15.4477 16 15 16.4477 15 17C15 17.5523 15.4477 18 16 18Z" fill="currentColor"/>
                    <path d="M2 2H3.5L5.5 13H17L19 5H5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Добавить в корзину
            </button>
            
            <div class="stock-status in-stock">
                <svg class="status-icon" width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <circle cx="8" cy="8" r="8" fill="#4CAF50"/>
                    <path d="M5 8L7 10L11 6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                В наличии
            </div>
        @else
            <div class="out-of-stock">
                <svg class="status-icon" width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <circle cx="8" cy="8" r="8" fill="#F44336"/>
                    <path d="M5 5L11 11M11 5L5 11" stroke="white" stroke-width="2" stroke-linecap="round"/>
                </svg>
                Нет в наличии
            </div>
        @endif

        {{-- Дополнительная информация --}}
        <div class="product-delivery-info">
            <div class="delivery-item">
                <svg class="delivery-icon" width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <path d="M13 2H3C2.44772 2 2 2.44772 2 3V13C2 13.5523 2.44772 14 3 14H13C13.5523 14 14 13.5523 14 13V3C14 2.44772 13.5523 2 13 2Z" stroke="currentColor" stroke-width="2"/>
                    <path d="M14 6H16L18 8V13C18 13.5523 17.5523 14 17 14H14" stroke="currentColor" stroke-width="2"/>
                </svg>
                <span>Доставка по Москве</span>
            </div>
        </div>
    </div>
</div>
