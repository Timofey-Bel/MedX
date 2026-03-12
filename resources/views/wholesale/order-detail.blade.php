@extends('wholesale.layout')

@section('page-title', 'Заказ #' . ($order['order_code'] ?? $order['id']))

@section('content')
<div class="section">
    <div class="section-header">
        <h2>Заказ #{{ $order['order_code'] ?? $order['id'] }}</h2>
        <div style="display: flex; gap: 12px;">
            <a href="{{ route('lk.orders') }}" class="btn btn-ghost">Назад к заказам</a>
            <a href="{{ route('lk.orders.repeat', $order['id']) }}" class="btn btn-primary">Повторить заказ</a>
        </div>
    </div>

    <div class="grid-2">
        <div class="card">
            <div class="card-header">
                <h3>Информация о заказе</h3>
            </div>
            <div class="card-body">
                <p><strong>Дата:</strong> {{ date('d.m.Y H:i', strtotime($order['date_init'])) }}</p>
                <p><strong>Статус:</strong> <span class="badge">{{ $order['status'] }}</span></p>
                <p><strong>Сумма:</strong> {{ number_format($order['full_sum'], 0, ',', ' ') }} ₽</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>Контактные данные</h3>
            </div>
            <div class="card-body">
                <p><strong>Имя:</strong> {{ $order['name'] }}</p>
                <p><strong>Телефон:</strong> {{ $order['phone'] }}</p>
@if(!empty($order['email']))
                <p><strong>Email:</strong> {{ $order['email'] }}</p>
@endif
            </div>
        </div>
    </div>
</div>

<div class="section">
    <div class="section-header">
        <h2>Состав заказа</h2>
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
@if(!empty($item->product_id))
        <a href="{{ route('lk.product.show', $item->product_id) }}" class="file-row" style="text-decoration: none; color: inherit; display: grid; grid-template-columns: 2fr 0.8fr 0.8fr 0.8fr 1fr; gap: 16px; cursor: pointer;">
@else
        <div class="file-row" style="display: grid; grid-template-columns: 2fr 0.8fr 0.8fr 0.8fr 1fr; gap: 16px;">
@endif
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
            <div class="file-cell">{{ $item->quantity }} шт</div>
            <div class="file-cell"><strong>{{ number_format($item->wholesale_total, 0, ',', ' ') }} ₽</strong></div>
@if(!empty($item->product_id))
        </a>
@else
        </div>
@endif
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
                    <span style="text-decoration: line-through; color: var(--muted-fg);">{{ number_format($totalRetail, 0, ',', ' ') }} ₽</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: var(--muted-fg);">Скидка ({{ $discountPercent }}%):</span>
                    <span style="color: #10b981; font-weight: 600;">-{{ number_format($totalDiscount, 0, ',', ' ') }} ₽</span>
                </div>
                <div style="height: 1px; background: var(--border); margin: 8px 0;"></div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 18px; font-weight: 600;">Оптовая стоимость:</span>
                    <span style="font-size: 24px; font-weight: 700; color: var(--primary);">{{ number_format($totalWholesale, 0, ',', ' ') }} ₽</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
