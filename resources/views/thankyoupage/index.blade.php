@extends('layouts.app')

@section('title', 'Спасибо за заказ!')

@push('styles')
    <style>
        .thankyou-container {
            max-width: 800px;
            margin: 60px auto;
            padding: 40px 20px;
            text-align: center;
        }

        .thankyou-icon {
            font-size: 80px;
            color: #27ae60;
            margin-bottom: 20px;
        }

        .thankyou-title {
            font-size: 36px;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }

        .thankyou-message {
            font-size: 18px;
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .order-number {
            display: inline-block;
            padding: 15px 30px;
            background-color: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 40px;
        }

        .order-number-label {
            font-size: 14px;
            color: #777;
            margin-bottom: 5px;
        }

        .order-number-value {
            font-size: 24px;
            font-weight: 700;
            color: #27ae60;
        }

        .thankyou-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 14px 30px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-block;
        }

        .btn-primary {
            background-color: #3498db;
            color: #fff;
        }

        .btn-primary:hover {
            background-color: #2980b9;
        }

        .btn-secondary {
            background-color: #95a5a6;
            color: #fff;
        }

        .btn-secondary:hover {
            background-color: #7f8c8d;
        }

        .thankyou-info {
            margin-top: 40px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            text-align: left;
        }

        .info-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
        }

        .info-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .info-list li {
            padding: 8px 0;
            color: #666;
            line-height: 1.6;
        }

        .info-list li:before {
            content: "✓";
            color: #27ae60;
            font-weight: bold;
            margin-right: 10px;
        }

        @media (max-width: 768px) {
            .thankyou-container {
                margin: 30px auto;
                padding: 20px;
            }

            .thankyou-title {
                font-size: 28px;
            }

            .thankyou-message {
                font-size: 16px;
            }

            .order-number-value {
                font-size: 20px;
            }

            .thankyou-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
<div class="thankyou-container">
    <!-- Иконка успеха -->
    <div class="thankyou-icon">✓</div>

    <!-- Заголовок -->
    <h1 class="thankyou-title">Спасибо за заказ!</h1>

    <!-- Сообщение -->
    <p class="thankyou-message">
        Ваш заказ успешно оформлен и принят в обработку.<br>
        Мы свяжемся с вами в ближайшее время для подтверждения.
    </p>

    <!-- Номер заказа -->
    @if(session('order_num'))
    <div class="order-number">
        <div class="order-number-label">Номер вашего заказа:</div>
        <div class="order-number-value">#{{ session('order_num') }}</div>
    </div>
    @endif

    <!-- Кнопки действий -->
    <div class="thankyou-actions">
        <a href="{{ route('home') }}" class="btn btn-primary">Вернуться на главную</a>
        <a href="{{ route('catalog.index') }}" class="btn btn-secondary">Продолжить покупки</a>
    </div>

    <!-- Дополнительная информация -->
    <div class="thankyou-info">
        <div class="info-title">Что дальше?</div>
        <ul class="info-list">
            <li>Мы отправили подтверждение заказа на вашу электронную почту</li>
            <li>Наш менеджер свяжется с вами для уточнения деталей доставки</li>
            <li>Вы можете отслеживать статус заказа в личном кабинете</li>
            <li>При возникновении вопросов обращайтесь в службу поддержки</li>
        </ul>
    </div>
</div>
@endsection
