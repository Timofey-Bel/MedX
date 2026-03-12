{{-- 
    Карусель товаров (верхняя правая часть)
    Миграция из legacy: legacy/site/modules/sfera/product_carousel/product_carousel.tpl
    
    Props:
    - $products (array): Массив товаров с полями id, name, image, product_price
    - $carouselId (string): Уникальный ID карусели для JavaScript (по умолчанию 'product')
--}}
@props(['products' => [], 'carouselId' => 'product'])

<div class="product-carousel-container">
    <div class="product-carousel">
        @if(count($products) > 0)
            <button class="carousel-btn carousel-prev small" data-carousel="{{ $carouselId }}">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <path d="M10 12l-4-4 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            <div class="carousel-track" data-carousel="{{ $carouselId }}">
                @foreach($products as $index => $product)
                    <div class="carousel-slide{{ $index === 0 ? ' active' : '' }}">
                        <div class="product-mini-card">
                            <img src="{{ is_array($product) ? $product['image'] : $product->image }}" alt="{{ is_array($product) ? $product['name'] : $product->name }}">
                        </div>
                    </div>
                @endforeach
            </div>
            <button class="carousel-btn carousel-next small" data-carousel="{{ $carouselId }}">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <path d="M6 12l4-4-4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            <div class="carousel-dots small" data-carousel="{{ $carouselId }}">
                @foreach($products as $index => $product)
                    <button class="carousel-dot{{ $index === 0 ? ' active' : '' }}" data-index="{{ $index }}"></button>
                @endforeach
            </div>
        @else
            <div style="padding: 20px; text-align: center; background: #f5f5f5; border-radius: 8px;">
                <p>Товары не найдены</p>
            </div>
        @endif
    </div>
</div>
