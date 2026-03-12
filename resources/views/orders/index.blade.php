@extends('layouts.app')

@section('title', 'Мои заказы')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/sfera/css/orders.css') }}">
@endpush

@section('content')
<!-- Main Content -->
<main class="orders-page">
    <div class="container">
        <div class="page-header">
            <h1>Мои заказы</h1>
        </div>

        <div class="orders-container">
            <!-- Фильтры -->
            <aside class="orders-sidebar">
                <div class="filter-section">
                    <h3>Статус заказа</h3>
                    <div class="filter-group">
                        <label class="filter-item">
                            <input type="radio" name="status" value="all"
@if($statusFilter === 'all')
 checked
@endif
>
                            <span>Все заказы</span>
                        </label>
                        <label class="filter-item">
                            <input type="radio" name="status" value="active"
@if($statusFilter === 'active')
 checked
@endif
>
                            <span>Активные</span>
                        </label>
                        <label class="filter-item">
                            <input type="radio" name="status" value="delivered"
@if($statusFilter === 'delivered')
 checked
@endif
>
                            <span>Доставленные</span>
                        </label>
                        <label class="filter-item">
                            <input type="radio" name="status" value="cancelled"
@if($statusFilter === 'cancelled')
 checked
@endif
>
                            <span>Отмененные</span>
                        </label>
                    </div>
                </div>

                <div class="filter-section">
                    <h3>Период</h3>
                    <div class="filter-group">
                        <label class="filter-item">
                            <input type="radio" name="period" value="all"
@if($periodFilter === 'all')
 checked
@endif
>
                            <span>За все время</span>
                        </label>
                        <label class="filter-item">
                            <input type="radio" name="period" value="month"
@if($periodFilter === 'month')
 checked
@endif
>
                            <span>За месяц</span>
                        </label>
                        <label class="filter-item">
                            <input type="radio" name="period" value="year"
@if($periodFilter === 'year')
 checked
@endif
>
                            <span>За год</span>
                        </label>
                    </div>
                </div>
            </aside>

            <!-- Список заказов -->
            <div class="orders-list">
@if(count($orders) > 0)
@foreach($orders as $order)
                <div class="order-card">
                    <div class="order-header">
                        <div class="order-info">
                            <span class="order-number">Заказ № {{ $order->order_code }}</span>
                            <span class="order-date">{{ \Carbon\Carbon::parse($order->created_at)->format('d.m.Y') }}</span>
                        </div>
                        <span class="order-status status-{{ $order->status === 1 || $order->status === 2 ? 'active' : ($order->status === 3 ? 'delivered' : 'cancelled') }}">
@if($order->status === 1 || $order->status === 2)
В обработке
@elseif($order->status === 3)
Доставлен
@elseif($order->status === 4)
Отменен
@else
Статус {{ $order->status }}
@endif
                        </span>
                    </div>

                    <div class="order-delivery
@if($order->status === 4)
 cancelled
@endif
">
@if($order->status === 1 || $order->status === 2)
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#00a859" stroke-width="2">
                            <rect x="1" y="3" width="15" height="13"/>
                            <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
                            <circle cx="5.5" cy="18.5" r="2.5"/>
                            <circle cx="18.5" cy="18.5" r="2.5"/>
                        </svg>
                        <div>
                            <strong>В обработке</strong>
                            <p>Заказ оформлен {{ \Carbon\Carbon::parse($order->created_at)->format('d.m.Y') }}</p>
                        </div>
@elseif($order->status === 3)
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#00a859" stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                        <div>
                            <strong>Доставлен</strong>
                            <p>Заказ выполнен</p>
                        </div>
@elseif($order->status === 4)
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#999" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="15" y1="9" x2="9" y2="15"/>
                            <line x1="9" y1="9" x2="15" y2="15"/>
                        </svg>
                        <div>
                            <strong>Отменен</strong>
                            <p>Заказ отменен</p>
                        </div>
@endif
                    </div>

                    <div class="order-items">
@foreach($order->items as $item)
                        <div class="order-item">
                            <img src="/import_files/{{ $item->product_id }}b.jpg" alt="{{ $item->product_name }}" onerror="this.onerror=null; this.src='/assets/img/product_empty.jpg';">
                            <div class="item-info">
                                <h4>{{ $item->product_name }}</h4>
                            </div>
                            <div class="item-price">
                                <span class="price">{{ number_format($item->price, 0, ',', ' ') }} ₽</span>
                                <span class="quantity">× {{ $item->quantity }}</span>
                            </div>
                        </div>
@endforeach
                    </div>

                    <div class="order-footer">
                        <div class="order-total">
                            <span>Итого:</span>
                            <strong>{{ number_format($order->total_amount, 0, ',', ' ') }} ₽</strong>
                        </div>
                        <div class="order-actions">
@if($order->status === 1 || $order->status === 2)
                            <button class="btn btn-secondary">Детали заказа</button>
@elseif($order->status === 3)
                            <button class="btn btn-outline">Повторить заказ</button>
                            <button class="btn btn-secondary">Оставить отзыв</button>
@elseif($order->status === 4)
                            <button class="btn btn-outline">Повторить заказ</button>
@endif
                        </div>
                    </div>
                </div>
@endforeach
@else
                <div class="empty-orders">
                    <svg width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    <h3>У вас пока нет заказов</h3>
                    <p>Начните покупки в нашем каталоге</p>
                    <a href="{{ route('catalog.index') }}" class="btn btn-primary">Перейти в каталог</a>
                </div>
@endif
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script src="{{ asset('assets/sfera/js/orders.js') }}"></script>
@endpush
