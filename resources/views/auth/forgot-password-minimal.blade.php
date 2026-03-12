@extends('layouts.auth-minimal')

@section('title', 'Восстановление пароля')

@section('content')
<h1>Восстановление пароля</h1>
<p>Введите ваш email, и мы отправим временный пароль</p>

@if (session('success'))
    <div style="color: green; padding: 10px; border: 1px solid green; margin: 10px 0;">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div style="color: red; padding: 10px; border: 1px solid red; margin: 10px 0;">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <div style="margin-bottom: 15px;">
        <label for="email">Email:</label><br>
        <input 
            type="email" 
            id="email" 
            name="email" 
            value="{{ old('email') }}" 
            style="width: 300px; padding: 8px;"
            required
        >
    </div>
    <button type="submit" style="padding: 10px 20px; background: #007bff; color: white; border: none;">
        Отправить временный пароль
    </button>
</form>

<p><a href="{{ route('login') }}">← Вернуться к входу</a></p>
@endsection