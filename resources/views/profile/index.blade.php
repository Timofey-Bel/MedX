{{-- Страница профиля пользователя --}}
@extends('layouts.app')

@section('title', 'Профиль пользователя')

@push('styles')
<style>
.profile-container {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px;
}

.profile-header {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    padding: 30px;
    margin-bottom: 30px;
}

.profile-header h1 {
    font-size: 28px;
    font-weight: 500;
    margin-bottom: 10px;
    color: #333;
}

.profile-header .user-email {
    color: #777;
    font-size: 16px;
}

.profile-content {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 30px;
}

.profile-sidebar {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    padding: 30px;
}

.profile-main {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    padding: 30px;
}

.section-title {
    font-size: 22px;
    font-weight: 500;
    margin-bottom: 20px;
    color: #333;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #555;
}

.form-group input {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
    transition: border-color 0.3s;
}

.form-group input:focus {
    outline: none;
    border-color: var(--cbPrimaryColor);
}

.form-group.error input {
    border-color: #dc3545;
}

.error-message {
    color: #dc3545;
    font-size: 14px;
    margin-top: 5px;
}

.success-message {
    background: #d4edda;
    color: #155724;
    padding: 12px 16px;
    border-radius: 4px;
    margin-bottom: 20px;
}

.btn-submit {
    padding: 12px 24px;
    background: var(--cbPrimaryColor);
    color: #fff;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.3s;
}

.btn-submit:hover {
    background: #0077cc;
}

.btn-logout {
    width: 100%;
    padding: 12px;
    background: #dc3545;
    color: #fff;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.3s;
    margin-top: 20px;
}

.btn-logout:hover {
    background: #c82333;
}

.orders-list {
    margin-top: 20px;
}

.order-item {
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    padding: 20px;
    margin-bottom: 15px;
    transition: box-shadow 0.3s;
}

.order-item:hover {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.order-number {
    font-weight: 500;
    font-size: 18px;
    color: #333;
}

.order-status {
    padding: 4px 12px;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 500;
}

.order-status.new {
    background: #fff3cd;
    color: #856404;
}

.order-status.processing {
    background: #cce5ff;
    color: #004085;
}

.order-status.completed {
    background: #d4edda;
    color: #155724;
}

.order-status.cancelled {
    background: #f8d7da;
    color: #721c24;
}

.order-details {
    color: #666;
    font-size: 14px;
    line-height: 1.6;
}

.order-details div {
    margin-bottom: 5px;
}

.empty-orders {
    text-align: center;
    padding: 40px;
    color: #999;
}

.password-hint {
    font-size: 13px;
    color: #777;
    margin-top: 5px;
}

@media (max-width: 768px) {
    .profile-content {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@section('content')
<div class="profile-container">
@if (session('warning'))
    <div class="alert alert-warning" style="background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
        <strong>⚠️ Внимание!</strong> {{ session('warning') }}
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success" style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
        <strong>✓</strong> {{ session('success') }}
    </div>
@endif

@if (auth()->user()->password_reset_required ?? false)
    <div class="alert alert-warning" style="background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
        <strong>🔐 Требуется смена пароля!</strong> 
        Вы используете временный пароль. Пожалуйста, смените его на новый в форме ниже для обеспечения безопасности вашего аккаунта.
    </div>
@endif
    <div class="profile-header">
        <h1>Профиль пользователя</h1>
        <div class="user-email">{{ $user->email }}</div>
    </div>

    <div class="profile-content">
        <!-- Sidebar -->
        <div class="profile-sidebar">
            <h2 class="section-title">Меню</h2>
            <nav>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 10px;">
                        <a href="#profile-data" style="color: var(--cbPrimaryColor); text-decoration: none;">Мои данные</a>
                    </li>
                    <li style="margin-bottom: 10px;">
                        <a href="#orders" style="color: var(--cbPrimaryColor); text-decoration: none;">История заказов</a>
                    </li>
                    <li style="margin-bottom: 10px;">
                        <a href="{{ route('favorites') }}" style="color: var(--cbPrimaryColor); text-decoration: none;">Избранное</a>
                    </li>
                </ul>
            </nav>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">Выйти</button>
            </form>
        </div>

        <!-- Main Content -->
        <div class="profile-main">
            <!-- Profile Data Section -->
            <section id="profile-data">
                <h2 class="section-title">Мои данные</h2>

                @if (session('success'))
                    <div class="success-message">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="error-message" style="margin-bottom: 20px; padding: 12px; background: #f8d7da; border-radius: 4px;">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf

                    <div class="form-group @error('name') error @enderror">
                        <label for="name">Имя</label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            value="{{ old('name', $user->name) }}" 
                            required
                        >
                        @error('name')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group @error('email') error @enderror">
                        <label for="email">Email</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="{{ old('email', $user->email) }}" 
                            required
                        >
                        @error('email')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr style="margin: 30px 0; border: none; border-top: 1px solid #e0e0e0;">

                    <h3 style="font-size: 18px; margin-bottom: 15px; color: #555;">Изменить пароль (необязательно)</h3>

                    <div class="form-group @error('current_password') error @enderror">
                        <label for="current_password">Текущий пароль</label>
                        <input 
                            type="password" 
                            id="current_password" 
                            name="current_password"
                            placeholder="Введите текущий пароль"
                        >
                        @error('current_password')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group @error('password') error @enderror">
                        <label for="password">Новый пароль</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password"
                            placeholder="Минимум 6 символов"
                        >
                        <div class="password-hint">Оставьте пустым, если не хотите менять пароль</div>
                        @error('password')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group @error('password_confirmation') error @enderror">
                        <label for="password_confirmation">Подтверждение нового пароля</label>
                        <input 
                            type="password" 
                            id="password_confirmation" 
                            name="password_confirmation"
                            placeholder="Повторите новый пароль"
                        >
                        @error('password_confirmation')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn-submit">Сохранить изменения</button>
                </form>
            </section>

            <hr style="margin: 40px 0; border: none; border-top: 1px solid #e0e0e0;">

            <!-- Orders History Section -->
            <section id="orders">
                <h2 class="section-title">История заказов</h2>

                @if (count($orders) > 0)
                    <div class="orders-list">
                        @foreach ($orders as $order)
                            <div class="order-item">
                                <div class="order-header">
                                    <div class="order-number">Заказ №{{ $order->id }}</div>
                                    <div class="order-status {{ $order->status }}">
                                        @switch($order->status)
                                            @case('new')
                                                Новый
                                                @break
                                            @case('processing')
                                                В обработке
                                                @break
                                            @case('completed')
                                                Выполнен
                                                @break
                                            @case('cancelled')
                                                Отменен
                                                @break
                                            @default
                                                {{ $order->status }}
                                        @endswitch
                                    </div>
                                </div>
                                <div class="order-details">
                                    <div><strong>Дата:</strong> {{ date('d.m.Y H:i', strtotime($order->created_at)) }}</div>
                                    <div><strong>Сумма:</strong> {{ number_format($order->total_amount, 0, ',', ' ') }} ₽</div>
                                    <div><strong>Товаров:</strong> {{ $order->items_count }}</div>
@if ($order->phone)
                                    <div><strong>Телефон:</strong> {{ $order->phone }}</div>
@endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-orders">
                        <p>У вас пока нет заказов</p>
                        <a href="{{ route('catalog.index') }}" style="color: var(--cbPrimaryColor); text-decoration: none;">Перейти в каталог</a>
                    </div>
                @endif
            </section>
        </div>
    </div>
</div>
@endsection
