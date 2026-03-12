@extends('wholesale.layout')

@section('page-title', 'Профиль')

@section('content')
<div class="section">
    <div class="profile-header">
        <h2>Профиль</h2>
        <a href="{{ route('lk.profile.edit') }}" class="btn btn-primary">Редактировать профиль</a>
    </div>
</div>

<div class="section">
    <div class="profile-card">
        <div class="profile-card-header">
            <h3>Личные данные</h3>
        </div>
        <div class="profile-card-body">
            <div class="profile-info-grid">
                <div class="profile-info-item">
                    <label>ФИО</label>
                    <p>{{ $user->name }}</p>
                </div>

                <div class="profile-info-item">
                    <label>Email</label>
                    <p>{{ $user->email }}</p>
                </div>

                <div class="profile-info-item">
                    <label>Телефон</label>
                    <p>{{ $user->phone }}</p>
                </div>

@if($user->phone_additional)
                <div class="profile-info-item">
                    <label>Дополнительный телефон</label>
                    <p>{{ $user->phone_additional }}</p>
                </div>
@endif

@if($user->telegram)
                <div class="profile-info-item">
                    <label>Telegram</label>
                    <p>{{ $user->telegram }}</p>
                </div>
@endif

                <div class="profile-info-item">
                    <label>Организация</label>
                    <p>{{ $organization->display_name }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="section">
    <div class="profile-card">
        <div class="profile-card-header">
            <h3>Смена пароля</h3>
        </div>
        <div class="profile-card-body">
            <form method="POST" action="{{ route('lk.profile.update-password') }}" class="password-form">
                @csrf

                <div class="form-group">
                    <label for="current_password">Текущий пароль</label>
                    <input 
                        type="password" 
                        id="current_password" 
                        name="current_password" 
                        class="form-input @error('current_password') error @enderror"
                        required
                    >
@error('current_password')
                    <span class="form-error">{{ $message }}</span>
@enderror
                </div>

                <div class="form-group">
                    <label for="new_password">Новый пароль</label>
                    <input 
                        type="password" 
                        id="new_password" 
                        name="new_password" 
                        class="form-input @error('new_password') error @enderror"
                        required
                        minlength="6"
                    >
@error('new_password')
                    <span class="form-error">{{ $message }}</span>
@enderror
                    <span class="form-hint">Минимум 6 символов</span>
                </div>

                <div class="form-group">
                    <label for="new_password_confirmation">Подтверждение нового пароля</label>
                    <input 
                        type="password" 
                        id="new_password_confirmation" 
                        name="new_password_confirmation" 
                        class="form-input"
                        required
                        minlength="6"
                    >
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Изменить пароль</button>
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

.profile-info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 24px;
}

.profile-info-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.profile-info-item label {
    font-size: 13px;
    font-weight: 500;
    color: var(--muted-fg);
}

.profile-info-item p {
    font-size: 15px;
    font-weight: 500;
    color: var(--fg);
}

.password-form {
    max-width: 500px;
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
}

@media (max-width: 768px) {
    .profile-info-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush
@endsection
