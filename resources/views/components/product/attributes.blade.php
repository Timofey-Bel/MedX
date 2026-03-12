{{--
    Компонент характеристик товара
    
    Props:
    - $productAttributes (array): Массив атрибутов с полями name, value, unit (опционально)
    - $grouped (bool): Группировать ли атрибуты по категориям (по умолчанию false)
    
    Структура сохраняет все CSS классы из legacy системы
--}}

@php
    $grouped = $grouped ?? false;
@endphp

<div class="product-attributes">
    <h3 class="attributes-title">Характеристики</h3>
    
    @if(count($productAttributes) > 0)
        <div class="attributes-list">
            @foreach($productAttributes as $attribute)
                <div class="attribute-row">
                    <span class="attribute-name">{{ $attribute->name }}</span>
                    <span class="attribute-dots"></span>
                    <span class="attribute-value">
                        @if(isset($attribute->seriya_id) && $attribute->seriya_id)
                            {{-- Для серии делаем ссылку --}}
                            <a href="/seriya/{{ $attribute->seriya_id }}" class="attribute-link">
                                {{ $attribute->value }}
                            </a>
                        @elseif(isset($attribute->topic_id) && $attribute->topic_id)
                            {{-- Для тематики делаем ссылку --}}
                            <a href="/tematika/{{ $attribute->topic_id }}" class="attribute-link">
                                {{ $attribute->value }}
                            </a>
                        @elseif(isset($attribute->product_type_id) && $attribute->product_type_id)
                            {{-- Для типа товара делаем ссылку --}}
                            <a href="/tip-tovara/{{ $attribute->product_type_id }}" class="attribute-link">
                                {{ $attribute->value }}
                            </a>
                        @else
                            {{ $attribute->value }}
                        @endif
                        
                        @if(isset($attribute->unit) && $attribute->unit)
                            {{ $attribute->unit }}
                        @endif
                    </span>
                </div>
            @endforeach
        </div>
    @else
        <p class="no-attributes">Характеристики не указаны</p>
    @endif
</div>
