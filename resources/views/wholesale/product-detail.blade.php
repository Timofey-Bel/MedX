@extends('wholesale.layout')

@section('page-title', $product->name)

@section('content')
<div class="section">
    <div class="section-header">
        <h2>{{ $product->name }}</h2>
        <div style="display: flex; gap: 12px;">
            <a href="javascript:history.back()" class="btn btn-ghost">Назад</a>
            <a href="{{ $productUrl }}" target="_blank" class="btn btn-primary">Открыть на сайте</a>
        </div>
    </div>

    <div class="grid-2">
        {{-- Галерея изображений --}}
        <div class="card">
            <div class="card-body">
@if(count($images) > 0)
                <div class="product-gallery">
                    <div class="product-gallery-main">
                        <img id="mainImage" src="{{ $images[0]->url }}" alt="{{ $product->name }}" style="width: 100%; height: auto; border-radius: 8px;">
                    </div>
@if(count($images) > 1)
                    <div class="product-gallery-thumbs" style="display: flex; gap: 8px; margin-top: 16px; overflow-x: auto;">
@foreach($images as $index => $image)
                        <img src="{{ $image->url }}" alt="{{ $product->name }}" 
                             class="gallery-thumb" 
                             data-index="{{ $index }}"
                             style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px; cursor: pointer; border: 2px solid transparent;"
                             onclick="changeMainImage('{{ $image->url }}', this)">
@endforeach
                    </div>
@endif
                </div>
@else
                <img src="/assets/img/product_empty.jpg" alt="{{ $product->name }}" style="width: 100%; height: auto; border-radius: 8px;">
@endif
            </div>
        </div>

        {{-- Информация о товаре --}}
        <div>
            <div class="card">
                <div class="card-header">
                    <h3>Информация</h3>
                </div>
                <div class="card-body">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        <div>
                            <p style="font-size: 32px; font-weight: 700; color: var(--primary);">{{ number_format($product->price ?? 0, 0, ',', ' ') }} ₽</p>
@if($rating > 0)
                            <div style="display: flex; align-items: center; gap: 8px; margin-top: 8px;">
                                <span style="color: #f59e0b;">★ {{ $rating }}</span>
                                <span style="color: var(--muted-fg);">({{ $reviews_count }} отзывов)</span>
                            </div>
@endif
                        </div>
                    </div>

@if(!empty($product->description))
                    <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--border);">
                        <h4 style="margin-bottom: 8px;">Описание</h4>
                        <p style="color: var(--muted-fg);">{{ $product->description }}</p>
                    </div>
@endif

                    <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--border);">
                        <p><strong>Артикул:</strong> {{ $product->id }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(count($attributes) > 0)
<div class="section">
    <div class="section-header">
        <h2>Характеристики</h2>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="file-list">
@foreach($attributes as $attribute)
                <div class="file-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div><strong>{{ $attribute->name }}</strong></div>
                    <div>{{ $attribute->value }}</div>
                </div>
@endforeach
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
function changeMainImage(imageSrc, thumbElement) {
    // Меняем главное изображение
    document.getElementById('mainImage').src = imageSrc;
    
    // Убираем активный класс со всех миниатюр
    document.querySelectorAll('.gallery-thumb').forEach(thumb => {
        thumb.style.border = '2px solid transparent';
    });
    
    // Добавляем активный класс к выбранной миниатюре
    thumbElement.style.border = '2px solid var(--primary)';
}

// Устанавливаем первую миниатюру как активную при загрузке
document.addEventListener('DOMContentLoaded', function() {
    const firstThumb = document.querySelector('.gallery-thumb');
    if (firstThumb) {
        firstThumb.style.border = '2px solid var(--primary)';
    }
});
</script>
@endpush
