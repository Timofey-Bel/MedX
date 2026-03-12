@extends('wholesale.layout')

@section('page-title', 'Главная')

@section('content')
{{-- Навигационные табы --}}
<div class="section">
    <div class="tabs-header">
        <div class="tabs-list" id="tabsList">
            <a href="{{ route('lk.index') }}" class="tab-link active">Обзор</a>
            <a href="{{ route('lk.orders') }}" class="tab-link">Заказы</a>
            <a href="{{ route('lk.organization') }}" class="tab-link">Организация</a>
        </div>
        <div class="tabs-actions">
            <button class="btn btn-ghost">Экспорт</button>
            <button class="btn btn-primary">Новый заказ</button>
        </div>
    </div>
</div>

<div class="section">
    <div class="hero-banner hero-home">
        <div class="hero-inner">
            <div class="hero-text">
                <h2>{{ $organization->display_name }}</h2>
                <p>ИНН: {{ $organization->inn }}
@if($organization->kpp)
 • КПП: {{ $organization->kpp }}
@endif
</p>
                <p style="margin-top: 8px; color: var(--muted-fg);">Управляйте заказами, просматривайте документы и отслеживайте статистику вашей организации.</p>
            </div>
            <div class="hero-spinner">
                <div class="r1"></div>
                <div class="r2"></div>
                <div class="r3"></div>
                <div class="r4"></div>
                <div class="r5"></div>
            </div>
        </div>
    </div>
</div>

<div class="section">
    <div class="grid-2">
        <div class="card card-highlight">
            <div class="card-header">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/><path d="M12 18V6"/></svg>
                <h3>Ваша скидка</h3>
            </div>
            <div class="card-body">
                <div class="stat-value">{{ $organization->discount_percent ?? 0 }}%</div>
                <p class="stat-label">Персональная скидка на весь ассортимент</p>
            </div>
        </div>
        <div class="card card-highlight">
            <div class="card-header">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                <h3>Отсрочка платежа</h3>
            </div>
            <div class="card-body">
                <div class="stat-value">{{ $organization->payment_delay_days ?? 0 }} дней</div>
@if($organization->payment_terms)
                <p class="stat-label">{{ $organization->payment_terms }}</p>
@else
                <p class="stat-label">Стандартные условия оплаты</p>
@endif
            </div>
        </div>
    </div>
</div>

<div class="section">
    <div class="section-header">
        <h2>Статистика</h2>
    </div>
    <div class="grid-3">
        <div class="card">
            <div class="card-header">
                <div class="card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a2 2 0 0 0-2 2v4"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
            </div>
            <div class="card-body">
                <h3>Всего заказов</h3>
                <p style="font-size: 32px; font-weight: 700; margin-top: 8px;">{{ $stats['total_orders'] }}</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-icon">
                    <svg width="24" height="24" viewBox="0 0 36 36" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20.57,20A8.23,8.23,0,0,0,29,12a8.23,8.23,0,0,0-8.43-8H12a1,1,0,0,0-1,1V18H9a1,1,0,0,0,0,2h2v2H9a1,1,0,0,0,0,2h2v7a1,1,0,0,0,2,0V24h9a1,1,0,0,0,0-2H13V20ZM13,6h7.57A6.24,6.24,0,0,1,27,12a6.23,6.23,0,0,1-6.43,6H13Z"/>
                    </svg>
                </div>
            </div>
            <div class="card-body">
                <h3>Общая сумма</h3>
                <p style="font-size: 32px; font-weight: 700; margin-top: 8px;">{{ number_format($stats['total_amount'], 0, ',', ' ') }} ₽</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
            </div>
            <div class="card-body">
                <h3>В обработке</h3>
                <p style="font-size: 32px; font-weight: 700; margin-top: 8px;">{{ $stats['pending_orders'] }}</p>
            </div>
        </div>
    </div>
</div>

<div class="section">
    <div class="grid-2-even">
        <div>
            <div class="section-header">
                <h2>Исполненные заказы</h2>
                <a href="{{ route('lk.orders') }}" class="btn btn-ghost">Все заказы</a>
            </div>
            <div class="simple-list">
@forelse(array_slice($orders, 0, 10) as $order)
                <a href="{{ route('lk.orders.show', $order['id']) }}" class="simple-list-item" style="text-decoration: none; color: inherit; display: block;">
                    <div class="simple-list-item-header">
                        <h3>Заказ #{{ $order['order_code'] ?? $order['id'] }}</h3>
                        <span class="badge">{{ $order['status'] }}</span>
                    </div>
                    <p>{{ $order['name'] }} • {{ $order['phone'] }}</p>
                    <div class="simple-list-item-footer">
                        <div class="meta">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                            {{ $order['items_count'] }} товаров
                        </div>
                        <div class="meta">
                            <strong>{{ number_format($order['total_amount'], 0, ',', ' ') }} ₽</strong>
                        </div>
                    </div>
                </a>
@empty
                <div class="simple-list-item">
                    <p style="text-align: center; color: var(--muted-fg);">Заказов пока нет</p>
                </div>
@endforelse
            </div>
        </div>
        <div>
            <div class="section-header">
                <h2>Активные заказы</h2>
                <a href="{{ route('lk.orders') }}" class="btn btn-ghost">Все заказы</a>
            </div>
            <div class="simple-list">
@forelse(array_slice($activeOrders, 0, 10) as $order)
                <a href="{{ route('lk.orders.show', $order['id']) }}" class="simple-list-item" style="text-decoration: none; color: inherit; display: block;">
                    <div class="simple-list-item-header">
                        <h3>Заказ #{{ $order['order_code'] ?? $order['id'] }}</h3>
                        <span class="badge">
@php
$daysToAdd = rand(7, 14);
$deliveryDate = date('d.m.Y', strtotime("+{$daysToAdd} days"));
@endphp
До {{ $deliveryDate }}</span>
                    </div>
                    <p>{{ $order['name'] }} • {{ $order['phone'] }}</p>
                    <div class="progress-wrap">
                        <div class="progress-header">
                            <span>Статус выполнения</span>
                            <span>
@php
$progress = rand(10, 90);
@endphp
{{ $progress }}%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $progress }}%"></div>
                        </div>
                    </div>
                    <div class="simple-list-item-footer" style="margin-top:12px">
                        <div class="meta">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                            {{ $order['items_count'] }} товаров
                        </div>
                        <div class="meta">
                            <strong>{{ number_format($order['total_amount'], 0, ',', ' ') }} ₽</strong>
                        </div>
                    </div>
                </a>
@empty
                <div class="simple-list-item">
                    <p style="text-align: center; color: var(--muted-fg);">Нет активных заказов</p>
                </div>
@endforelse
            </div>
        </div>
    </div>
</div>
@endsection
