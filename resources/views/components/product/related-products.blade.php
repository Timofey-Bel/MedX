{{--
    Компонент карусели похожих товаров
    
    Props:
    - $products (array): Массив похожих товаров
    - $title (string): Заголовок блока (по умолчанию "Похожие товары")
    
    Структура сохраняет все CSS классы из legacy системы для совместимости с JavaScript карусели.
--}}

@php
    $title = $title ?? 'Похожие товары';
@endphp

@if(count($products) > 0)
<div class="related-products">
    <h3 class="related-products-title">{{ $title }}</h3>
    
    <div class="products-carousel">
        {{-- Кнопка навигации назад --}}
        @if(count($products) > 4)
        <button class="carousel-btn carousel-prev" aria-label="Предыдущие товары">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
        @endif
        
        {{-- Трек карусели с карточками товаров --}}
        <div class="carousel-track">
            @foreach($products as $product)
                <x-product.product-card :product="$product" />
            @endforeach
        </div>
        
        {{-- Кнопка навигации вперед --}}
        @if(count($products) > 4)
        <button class="carousel-btn carousel-next" aria-label="Следующие товары">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
        @endif
    </div>
    
    {{-- Точки навигации --}}
    @if(count($products) > 4)
    <div class="carousel-dots">
        @php
            $dotsCount = ceil(count($products) / 4);
        @endphp
        @for($i = 0; $i < $dotsCount; $i++)
            <button class="carousel-dot {{ $i === 0 ? 'active' : '' }}" 
                    data-slide="{{ $i }}"
                    aria-label="Перейти к слайду {{ $i + 1 }}"></button>
        @endfor
    </div>
    @endif
</div>
@endif
