@extends('layouts.app')

@section('title', 'Оформление заказа - Творческий Центр СФЕРА')

@section('content')
<div class="container" style="padding: 40px 20px; max-width: 1000px; margin: 0 auto;">
    <h1 style="font-size: 32px; margin-bottom: 30px; color: #333;">Оформление заказа</h1>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
        <!-- Форма заказа -->
        <div class="checkout-form">
            <h2 style="font-size: 24px; margin-bottom: 20px; color: #333;">Контактные данные</h2>
            
            <form action="{{ route('checkout') }}" method="POST">
                @csrf
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">
                        Имя <span style="color: #e74c3c;">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
                    @error('name')
                        <span style="color: #e74c3c; font-size: 14px;">{{ $message }}</span>
                    @enderror
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">
                        Телефон <span style="color: #e74c3c;">*</span>
                    </label>
                    <input type="tel" name="phone" value="{{ old('phone', $user->phone ?? '') }}" required
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;"
                           placeholder="+7 (___) ___-__-__">
                    @error('phone')
                        <span style="color: #e74c3c; font-size: 14px;">{{ $message }}</span>
                    @enderror
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">
                        Email <span style="color: #e74c3c;">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
                    @error('email')
                        <span style="color: #e74c3c; font-size: 14px;">{{ $message }}</span>
                    @enderror
                </div>
                
                <div style="margin-bottom: 30px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">
                        Адрес доставки <span style="color: #e74c3c;">*</span>
                    </label>
                    <textarea name="address" required rows="3"
                              style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px; resize: vertical;">{{ old('address', $user->address ?? '') }}</textarea>
                    @error('address')
                        <span style="color: #e74c3c; font-size: 14px;">{{ $message }}</span>
                    @enderror
                </div>
                
                <button type="submit" style="width: 100%; padding: 15px; background: var(--cbPrimaryColor); color: white; border: none; cursor: pointer; border-radius: 4px; font-size: 18px; font-weight: 500;">
                    Оформить заказ
                </button>
            </form>
        </div>
        
        <!-- Сводка заказа -->
        <div class="checkout-sidebar">
            <div class="order-summary">
                <h2 style="font-size: 24px; margin-bottom: 20px; color: #333;">Ваш заказ</h2>
                
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <!-- Список товаров (только selected) -->
                    <div data-bind="foreach: selectedItems">
                        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #e0e0e0;">
                            <div style="flex: 1;">
                                <div style="font-weight: 500; margin-bottom: 5px;" data-bind="text: name"></div>
                                <div style="color: #666; font-size: 14px;">
                                    <span data-bind="text: formattedQuantity"></span> × <span data-bind="text: formattedPrice"></span>
                                </div>
                            </div>
                            <div style="font-weight: 500; white-space: nowrap; margin-left: 15px;" data-bind="text: formattedTotal"></div>
                        </div>
                    </div>
                    
                    <!-- Итого -->
                    <div style="display: flex; justify-content: space-between; padding-top: 20px; margin-top: 20px; border-top: 2px solid #333;">
                        <span style="font-size: 20px; font-weight: 700;">Итого:</span>
                        <span style="font-size: 24px; font-weight: 700; color: var(--cbPrimaryColor);" data-bind="text: formattedGrandTotal"></span>
                    </div>
                </div>
                
                <div style="margin-top: 20px; padding: 15px; background: #e8f4fd; border-radius: 8px; font-size: 14px; color: #333;">
                    <strong>Обратите внимание:</strong> После оформления заказа с вами свяжется наш менеджер для подтверждения деталей доставки.
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
@media (max-width: 768px) {
    .container > div {
        grid-template-columns: 1fr !important;
    }
}
</style>
@endpush
@endsection
