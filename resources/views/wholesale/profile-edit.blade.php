@extends('wholesale.layout')

@section('page-title', 'Редактирование профиля')

@section('content')
<div class="section">
    <div class="profile-header">
        <h2>Редактирование профиля</h2>
        <a href="{{ route('lk.profile') }}" class="btn btn-ghost">Отмена</a>
    </div>
</div>

<div class="section">
    <div class="profile-card">
        <div class="profile-card-header">
            <h3>Контактные данные</h3>
        </div>
        <div class="profile-card-body">
            <form method="POST" action="{{ route('lk.profile.update') }}" class="profile-form">
                @csrf

                <div class="form-group">
                    <label for="email">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-input @error('email') error @enderror"
                        value="{{ old('email', $user->email) }}"
                        required
                    >
@error('email')
                    <span class="form-error">{{ $message }}</span>
@enderror
                </div>

                <div class="form-group">
                    <label for="phone">Телефон</label>
                    <input 
                        type="tel" 
                        id="phone" 
                        name="phone" 
                        class="form-input @error('phone') error @enderror"
                        value="{{ old('phone', $user->phone) }}"
                        required
                        placeholder="+7 (___) ___-__-__"
                    >
@error('phone')
                    <span class="form-error">{{ $message }}</span>
@enderror
                </div>

                <div class="form-group">
                    <label for="phone_additional">Дополнительный телефон</label>
                    <input 
                        type="tel" 
                        id="phone_additional" 
                        name="phone_additional" 
                        class="form-input @error('phone_additional') error @enderror"
                        value="{{ old('phone_additional', $user->phone_additional) }}"
                        placeholder="+7 (___) ___-__-__"
                    >
@error('phone_additional')
                    <span class="form-error">{{ $message }}</span>
@enderror
                    <span class="form-hint">Необязательное поле</span>
                </div>

                <div class="form-group">
                    <label for="telegram">Telegram</label>
                    <input 
                        type="text" 
                        id="telegram" 
                        name="telegram" 
                        class="form-input @error('telegram') error @enderror"
                        value="{{ old('telegram', $user->telegram) }}"
                        placeholder="@username или номер телефона"
                    >
@error('telegram')
                    <span class="form-error">{{ $message }}</span>
@enderror
                    <span class="form-hint">Укажите ваш username в Telegram (например, @username) или номер телефона</span>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                    <a href="{{ route('lk.profile') }}" class="btn btn-ghost">Отмена</a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
.profile-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.profile-header h2 {
    font-size: 24px;
    font-weight: 600;
    color: var(--fg);
    margin: 0;
}

.profile-form {
    max-width: 600px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-size: 14px;
    font-weight: 500;
    color: var(--fg);
    margin-bottom: 8px;
}

.form-input {
    width: 100%;
    padding: 12px 16px;
    font-size: 15px;
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    background: var(--bg);
    color: var(--fg);
    transition: all 0.2s;
}

.form-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.form-input.error {
    border-color: #ef4444;
}

.form-error {
    display: block;
    margin-top: 6px;
    font-size: 13px;
    color: #ef4444;
}

.form-hint {
    display: block;
    margin-top: 6px;
    font-size: 13px;
    color: var(--muted-fg);
}

.form-actions {
    margin-top: 24px;
    display: flex;
    gap: 12px;
}
</style>
@endpush
@endsection
