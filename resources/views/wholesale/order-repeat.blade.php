@extends('wholesale.layout')

@section('page-title', 'Повторить заказ #' . ($order['order_code'] ?? $order['id']))

@section('content')
<div class="section">
    <div class="section-header">
        <h2>Повторить заказ #{{ $order['order_code'] ?? $order['id'] }}</h2>
        <div style="display: flex; gap: 12px;">
            <a href="{{ route('lk.orders.show', $order['id']) }}" class="btn btn-ghost">Назад к заказу</a>
        </div>
    </div>

    <div class="card" style="margin-bottom: 24px;">
        <div class="card-body" style="padding: 16px;">
            <p style="color: var(--muted-fg); margin-bottom: 0;">
                Вы можете изменить количество товаров или удалить ненужные позиции перед оформлением нового заказа.
            </p>
        </div>
    </div>
</div>

<div class="section">
    <div class="section-header">
        <h2>Состав заказа</h2>
        <div class="order-summary">
            <span style="font-size: 14px; color: var(--muted-fg);">Итого:</span>
            <span id="total-amount" style="font-size: 20px; font-weight: 600; margin-left: 8px;">{{ number_format($totalWholesale, 0, ',', ' ') }} ₽</span>
        </div>
    </div>
    <div class="file-list">
        <div class="file-list-head" style="display: grid; grid-template-columns: 2fr 0.8fr 0.8fr 0.8fr 1fr; gap: 16px;">
            <div>Товар</div>
            <div>РРЦ</div>
            <div>Цена</div>
            <div>Кол-во</div>
            <div>Сумма</div>
        </div>
@foreach($items as $item)
        <div class="file-row order-item" data-item-id="{{ $item->id }}" data-wholesale-price="{{ $item->wholesale_price }}" data-retail-price="{{ $item->retail_price }}" style="display: grid; grid-template-columns: 2fr 0.8fr 0.8fr 0.8fr 1fr; gap: 16px;">
            <div class="file-info">
                <img src="{{ $item->image ?? '/assets/img/product_empty.jpg' }}" alt="{{ $item->product_name }}">
                <div class="file-info-text">
                    <p>{{ $item->product_name }}</p>
                    <span>ID: {{ $item->product_id ?? 'N/A' }}</span>
                </div>
            </div>
            <div class="file-cell">
@if($item->retail_price > $item->wholesale_price)
                {{ number_format($item->retail_price, 0, ',', ' ') }} ₽
@else
                —
@endif
            </div>
            <div class="file-cell">{{ number_format($item->wholesale_price, 0, ',', ' ') }} ₽</div>
            <div class="file-cell">
                <div class="quantity-controls">
                    <button type="button" class="btn-quantity btn-quantity-minus" data-action="decrease">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    </button>
                    <input type="number" class="quantity-input" value="{{ $item->quantity }}" min="1" max="999" data-original="{{ $item->quantity }}">
                    <button type="button" class="btn-quantity btn-quantity-plus" data-action="increase">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    </button>
                </div>
            </div>
            <div class="file-cell" style="display: flex; align-items: center; justify-content: space-between;">
                <strong class="item-total">{{ number_format($item->wholesale_total, 0, ',', ' ') }} ₽</strong>
                <button type="button" class="btn-icon-sm btn-remove-item" title="Удалить товар">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                </button>
            </div>
        </div>
@endforeach
    </div>
</div>

<div class="section">
    <div class="card" style="max-width: 500px; margin-left: auto;">
        <div class="card-body" style="padding: 24px;">
            <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 16px;">Итого по заказу</h3>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: var(--muted-fg);">Розничная стоимость:</span>
                    <span style="text-decoration: line-through; color: var(--muted-fg);" id="total-retail">{{ number_format($totalRetail, 0, ',', ' ') }} ₽</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: var(--muted-fg);">Скидка (<span id="discount-percent">{{ $discountPercent }}</span>%):</span>
                    <span style="color: #10b981; font-weight: 600;" id="total-discount">-{{ number_format($totalDiscount, 0, ',', ' ') }} ₽</span>
                </div>
                <div style="height: 1px; background: var(--border); margin: 8px 0;"></div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 18px; font-weight: 600;">Оптовая стоимость:</span>
                    <span style="font-size: 24px; font-weight: 700; color: var(--primary);" id="total-wholesale">{{ number_format($totalWholesale, 0, ',', ' ') }} ₽</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="section">
    <div style="display: flex; justify-content: flex-end; gap: 12px;">
        <a href="{{ route('lk.orders.show', $order['id']) }}" class="btn btn-outline">Отменить</a>
        <button type="button" id="btn-place-order" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
            Оформить заказ
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/sfera/js/order-repeat.js') }}"></script>
@endpush
