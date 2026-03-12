{{--
    Компонент карточки товара
    
    Props:
    - $product (object): Объект товара с полями id, name, image, price, in_stock
    
    Используется в карусели похожих товаров и других списках товаров.
    Структура сохраняет все CSS классы из legacy системы для совместимости с JavaScript.
--}}

<div class="product-card" data-product-id="{{ $product->id }}">
    <a href="/product/{{ $product->id }}" class="product-link">
        <div class="product-image">
            <img src="{{ $product->image ?? '/assets/img/product_empty.jpg' }}" 
                 alt="{{ $product->name }}"
                 loading="lazy">
        </div>
        <div class="product-info">
            <h4 class="product-name">{{ $product->name }}</h4>
            <div class="product-price">{{ number_format($product->price, 0, ',', ' ') }} ₽</div>
        </div>
    </a>
    
    @if($product->in_stock)
        <button class="btn-add-to-cart-mini" 
                data-bind="click: addToCart" 
                data-product-id="{{ $product->id }}"
                type="button"
                aria-label="Добавить {{ $product->name }} в корзину">
            <svg class="cart-icon" width="16" height="16" viewBox="0 0 20 20" fill="none">
                <path d="M7 18C7.55228 18 8 17.5523 8 17C8 16.4477 7.55228 16 7 16C6.44772 16 6 16.4477 6 17C6 17.5523 6.44772 18 7 18Z" fill="currentColor"/>
                <path d="M16 18C16.5523 18 17 17.5523 17 17C17 16.4477 16.5523 16 16 16C15.4477 16 15 16.4477 15 17C15 17.5523 15.4477 18 16 18Z" fill="currentColor"/>
                <path d="M2 2H3.5L5.5 13H17L19 5H5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            В корзину
        </button>
    @else
        <div class="product-out-of-stock">Нет в наличии</div>
    @endif
</div>
