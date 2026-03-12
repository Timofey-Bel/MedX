@extends('wholesale.layout')

@section('page-title', 'Данные организации')

@section('content')
{{-- Навигационные табы --}}
<div class="section">
    <div class="tabs-header">
        <div class="tabs-list" id="tabsList">
            <a href="{{ route('lk.index') }}" class="tab-link">Обзор</a>
            <a href="{{ route('lk.orders') }}" class="tab-link">Заказы</a>
            <a href="{{ route('lk.organization') }}" class="tab-link active">Организация</a>
        </div>
        <div class="tabs-actions">
            <button class="btn btn-ghost">Редактировать</button>
            <button class="btn btn-primary">Сохранить</button>
        </div>
    </div>
</div>

<div class="section">
    <div class="profile-card">
        <div class="profile-card-header">
            <h3>Реквизиты организации</h3>
        </div>
        <div class="profile-card-body">
            <div class="org-info-grid">
                <div class="org-info-item">
                    <label>Полное наименование</label>
                    <p>{{ $organization->name_full }}</p>
                </div>

@if($organization->name_short)
                <div class="org-info-item">
                    <label>Краткое наименование</label>
                    <p>{{ $organization->name_short }}</p>
                </div>
@endif

                <div class="org-info-item">
                    <label>ИНН</label>
                    <p>{{ $organization->inn }}</p>
                </div>

@if($organization->kpp)
                <div class="org-info-item">
                    <label>КПП</label>
                    <p>{{ $organization->kpp }}</p>
                </div>
@endif

@if($organization->ogrn)
                <div class="org-info-item">
                    <label>ОГРН</label>
                    <p>{{ $organization->ogrn }}</p>
                </div>
@endif

@if($organization->opf)
                <div class="org-info-item">
                    <label>Организационно-правовая форма</label>
                    <p>{{ $organization->opf }}</p>
                </div>
@endif

@if($organization->legal_address)
                <div class="org-info-item org-info-item-full">
                    <label>Юридический адрес</label>
                    <p>{{ $organization->legal_address }}</p>
                </div>
@endif

@if($organization->postal_address && $organization->postal_address !== $organization->legal_address)
                <div class="org-info-item org-info-item-full">
                    <label>Почтовый адрес</label>
                    <p>{{ $organization->postal_address }}</p>
                </div>
@endif

@if($organization->director_name)
                <div class="org-info-item">
                    <label>Руководитель</label>
                    <p>{{ $organization->director_name }}</p>
                </div>
@endif

@if($organization->director_position)
                <div class="org-info-item">
                    <label>Должность руководителя</label>
                    <p>{{ $organization->director_position }}</p>
                </div>
@endif

@if($organization->phone)
                <div class="org-info-item">
                    <label>Телефон</label>
                    <p>{{ $organization->phone }}</p>
                </div>
@endif

@if($organization->email)
                <div class="org-info-item">
                    <label>Email</label>
                    <p>{{ $organization->email }}</p>
                </div>
@endif
            </div>
        </div>
    </div>
</div>

@if($organization->bank_name || $organization->bank_bik || $organization->bank_account)
<div class="section">
    <div class="profile-card">
        <div class="profile-card-header">
            <h3>Банковские реквизиты</h3>
        </div>
        <div class="profile-card-body">
            <div class="org-info-grid">
@if($organization->bank_name)
                <div class="org-info-item org-info-item-full">
                    <label>Наименование банка</label>
                    <p>{{ $organization->bank_name }}</p>
                </div>
@endif

@if($organization->bank_bik)
                <div class="org-info-item">
                    <label>БИК</label>
                    <p>{{ $organization->bank_bik }}</p>
                </div>
@endif

@if($organization->bank_account)
                <div class="org-info-item">
                    <label>Расчетный счет</label>
                    <p>{{ $organization->bank_account }}</p>
                </div>
@endif

@if($organization->bank_corr_account)
                <div class="org-info-item">
                    <label>Корреспондентский счет</label>
                    <p>{{ $organization->bank_corr_account }}</p>
                </div>
@endif
            </div>
        </div>
    </div>
</div>
@endif

<div class="section">
    <div class="profile-card">
        <div class="profile-card-header">
            <h3>Статус организации</h3>
        </div>
        <div class="profile-card-body">
            <div class="org-status">
@if($organization->isActive())
                <span class="badge badge-blue">Активна</span>
@else
                <span class="badge badge-red">{{ ucfirst($organization->status) }}</span>
@endif
                <p style="margin-top: 12px; font-size: 14px; color: var(--muted-fg);">
                    Данные обновлены: {{ $organization->updated_at->format('d.m.Y H:i') }}
                </p>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.org-info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 24px;
}

.org-info-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.org-info-item-full {
    grid-column: 1 / -1;
}

.org-info-item label {
    font-size: 13px;
    font-weight: 500;
    color: var(--muted-fg);
}

.org-info-item p {
    font-size: 15px;
    font-weight: 500;
    color: var(--fg);
}

.org-status {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

@media (max-width: 768px) {
    .org-info-grid {
        grid-template-columns: 1fr;
    }
    
    .org-info-item-full {
        grid-column: auto;
    }
}
</style>
@endpush
@endsection
