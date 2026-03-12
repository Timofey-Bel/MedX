{{--
    Компонент галереи изображений товара
    
    Props:
    - $images (array): Массив изображений с полем url
    - $productName (string): Название товара для alt текста
    
    Структура сохраняет все CSS классы из legacy системы для совместимости с JavaScript
--}}

<div class="product-gallery">
    <div class="gallery-container">
        {{-- Вертикальные миниатюры --}}
        @if(count($images) > 1)
        <div class="gallery-thumbnails-vertical">
            @foreach($images as $index => $image)
            <div class="thumbnail-vertical {{ $index === 0 ? 'active' : '' }}" 
                 data-image="{{ $image->url }}"
                 data-index="{{ $index }}">
                <img src="{{ $image->url }}" alt="{{ $productName }} - изображение {{ $index + 1 }}">
            </div>
            @endforeach
        </div>
        @endif

        {{-- Главное изображение --}}
        <div class="gallery-main-wrapper">
            <div class="gallery-main">
                <img id="mainImage" 
                     src="{{ $images[0]->url ?? '/assets/img/product_empty.jpg' }}" 
                     alt="{{ $productName }}"
                     data-zoom="{{ $images[0]->url ?? '/assets/img/product_empty.jpg' }}">
            </div>
            
            {{-- Навигация для галереи (стрелки) --}}
            @if(count($images) > 1)
            <button class="gallery-nav gallery-prev" aria-label="Предыдущее изображение">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            <button class="gallery-nav gallery-next" aria-label="Следующее изображение">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            @endif
        </div>
    </div>
</div>
