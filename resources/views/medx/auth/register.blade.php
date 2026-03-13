@extends('layouts.medx')

@section('title', 'Регистрация - MedX')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/medx/css/auth.css') }}">
@endpush

@section('content')
<section class="auth-section">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1 class="auth-title">Регистрация в MedX</h1>
                <p class="auth-subtitle">Создайте аккаунт и начните обучение бесплатно</p>
            </div>
            
            <form class="auth-form" method="POST" action="{{ route('medx.register') }}">
@csrf
                
                <div class="form-group">
                    <label for="name" class="form-label">Имя</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        class="form-input" 
                        placeholder="Иван Иванов"
                        required
                    />
                </div>
                
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-input" 
                        placeholder="your@email.com"
                        required
                    />
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Пароль</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-input" 
                        placeholder="••••••••"
                        required
                    />
                </div>
                
                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Подтвердите пароль</label>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        class="form-input" 
                        placeholder="••••••••"
                        required
                    />
                </div>
                
                <div class="form-options">
                    <label class="checkbox-label">
                        <input type="checkbox" name="agree" required />
                        <span>Я согласен с <a href="#" class="link-terms">условиями использования</a></span>
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    Зарегистрироваться
                </button>
            </form>
            
            <div class="auth-footer">
                <p>Уже есть аккаунт? <a href="{{ route('medx.login') }}" class="link-login">Войти</a></p>
            </div>
        </div>
    </div>
</section>
@endsection
