@extends('layouts.app')

@section('title', '{{ $authorName }} - Творческий Центр СФЕРА')

@section('content')
<div class="container" style="padding: 40px 20px; max-width: 1200px; margin: 0 auto;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('authors') }}" style="color: var(--cbPrimaryColor); text-decoration: none; font-size: 14px;">
            ← Все авторы
        </a>
    </div>
    
    <h1 style="font-size: 32px; margin-bottom: 30px; color: #333;">{{ $authorName }}</h1>
    
    @if(empty($books))
        <p style="color: #666; font-size: 16px;">У этого автора пока нет книг в каталоге</p>
    @else
        <div class="books-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
            @foreach($books as $book)
                <div class="product-card" style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px; background: white;">
                    <div class="product-card__image" style="margin-bottom: 15px;">
                        <img src="{{ $book['image'] }}" alt="{{ $book['name'] }}" style="width: 100%; height: 250px; object-fit: cover; border-radius: 4px;">
                    </div>
                    
                    <h3 style="font-size: 16px; margin-bottom: 10px; color: #333; min-height: 40px;">{{ Str::limit($book['name'], 60) }}</h3>
                    
                    @if($book['rating'] > 0)
                        <div style="display: flex; align-items: center; gap: 5px; margin-bottom: 10px; font-size: 14px; color: #666;">
                            <span style="color: #ffa500;">★</span>
                            <span>{{ $book['rating'] }}</span>
                            <span>({{ $book['reviews_count'] }})</span>
                        </div>
                    @endif
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
                        <div style="font-size: 20px; font-weight: 700; color: #333;">
                            {{ number_format($book['price'], 0, ',', ' ') }} ₽
                        </div>
                        
                        @if($book['quantity'] > 0)
                            <button class="btn-add-to-cart" data-product-id="{{ $book['id'] }}" style="padding: 8px 16px; background: var(--cbPrimaryColor); color: white; border: none; cursor: pointer; border-radius: 4px; font-size: 14px;">
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
    $('.btn-add-to-cart').on('click', function() {
        const productId = $(this).data('product-id');
        addToCart(productId);
    });
    
    function addToCart(productId) {
        // TODO: Реализовать AJAX запрос для добавления в корзину
        console.log('Add to cart:', productId);
        alert('Товар добавлен в корзину');
    }
});
</script>
@endpush
@endsection
