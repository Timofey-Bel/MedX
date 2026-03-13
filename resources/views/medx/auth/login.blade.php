@extends('layouts.medx')

@section('title', 'Вход - MedX')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/medx/css/auth.css') }}">
@endpush

@section('content')
<section class="auth-section">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1 class="auth-title">Вход в MedX</h1>
                <p class="auth-subtitle">Добро пожаловать! Войдите в свой аккаунт</p>
            </div>
            
            <form class="auth-form" method="POST" action="{{ route('medx.login') }}">
@csrf
                
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
                
                <div class="form-options">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember" />
                        <span>Запомнить меня</span>
                    </label>
                    <a href="#" class="link-forgot">Забыли пароль?</a>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    Войти
                </button>
            </form>
            
            <div class="auth-footer">
                <p>Нет аккаунта? <a href="{{ route('medx.register') }}" class="link-register">Зарегистрироваться</a></p>
            </div>
        </div>
    </div>
</section>
@endsection
