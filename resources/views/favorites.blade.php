@extends('layouts.app')

@section('title', 'Избранное - Творческий Центр СФЕРА')

@section('content')
<div class="container" style="padding: 40px 20px; max-width: 1200px; margin: 0 auto;">
    <h1 style="font-size: 32px; margin-bottom: 30px; color: #333;">Избранное</h1>
    
    @if($isEmpty)
        <div style="text-align: center; padding: 60px 20px;">
            <p style="font-size: 18px; color: #666; margin-bottom: 20px;">У вас пока нет избранных товаров</p>
            <a href="{{ route('catalog.index') }}" style="display: inline-block; padding: 12px 30px; background: var(--cbPrimaryColor); color: white; text-decoration: none; border-radius: 4px;">
                Перейти в каталог
            </a>
        </div>
    @else
        <div class="favorites-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
            @foreach($favoriteItems as $item)
                <div class="product-card" style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px; background: white; position: relative;">
                    <button class="btn-remove-favorite" data-product-id="{{ $item['id'] }}" style="position: absolute; top: 10px; right: 10px; width: 32px; height: 32px; border: none; background: #fff; cursor: pointer; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.1); color: #e74c3c; font-size: 18px;">
                        ❤️
                    </button>
                    
                    <div class="product-card__image" style="margin-bottom: 15px;">
                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" style="width: 100%; height: 250px; object-fit: cover; border-radius: 4px;">
                    </div>
                    
                    <h3 style="font-size: 16px; margin-bottom: 10px; color: #333; min-height: 40px;">{{ Str::limit($item['name'], 60) }}</h3>
                    
                    @if($item['rating'] > 0)
                        <div style="display: flex; align-items: center; gap: 5px; margin-bottom: 10px; font-size: 14px; color: #666;">
                            <span style="color: #ffa500;">★</span>
                            <span>{{ $item['rating'] }}</span>
                            <span>({{ $item['reviews_count'] }})</span>
                        </div>
                    @endif
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
                        <div style="font-size: 20px; font-weight: 700; color: #333;">
                            {{ number_format($item['price'], 0, ',', ' ') }} ₽
                        </div>
                        
                        @if($item['quantity'] > 0)
                            <button class="btn-add-to-cart" data-product-id="{{ $item['id'] }}" style="padding: 8px 16px; background: var(--cbPrimaryColor); color: white; border: none; cursor: pointer; border-radius: 4px; font-size: 14px;">
                                В корзину
                            </button>
                        @else
                            <span style="color: #999; font-size: 14px;">Нет в наличии</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Удаление из избранного
    $('.btn-remove-favorite').on('click', function() {
        const productId = $(this).data('product-id');
        removeFromFavorites(productId);
    });
    
    // Добавление в корзину
    $('.btn-add-to-cart').on('click', function() {
        const productId = $(this).data('product-id');
        addToCart(productId);
    });
    
    function removeFromFavorites(productId) {
        // TODO: Реализовать AJAX запрос для удаления из избранного
        console.log('Remove from favorites:', productId);
        location.reload();
    }
    
    function addToCart(productId) {
        // TODO: Реализовать AJAX запрос для добавления в корзину
        console.log('Add to cart:', productId);
        alert('Товар добавлен в корзину');
    }
});
</script>
@endpush
@endsection
