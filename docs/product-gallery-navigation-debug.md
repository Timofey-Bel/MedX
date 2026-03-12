# Отладка навигационных кнопок галереи

## Проблема
Навигационные кнопки (стрелки вверх/вниз) не видны у вертикальной карусели миниатюр.

## Шаги отладки

### 1. Проверка в DevTools (F12)

Откройте консоль браузера и выполните:

```javascript
// Проверяем наличие кнопок в DOM
document.querySelectorAll('.product-gallery-thumbs .swiper-button-next')
document.querySelectorAll('.product-gallery-thumbs .swiper-button-prev')

// Проверяем стили кнопок
const btnNext = document.querySelector('.product-gallery-thumbs .swiper-button-next');
const btnPrev = document.querySelector('.product-gallery-thumbs .swiper-button-prev');

console.log('Next button:', btnNext);
console.log('Prev button:', btnPrev);

if (btnNext) {
    console.log('Next button styles:', window.getComputedStyle(btnNext));
    console.log('Display:', window.getComputedStyle(btnNext).display);
    console.log('Visibility:', window.getComputedStyle(btnNext).visibility);
    console.log('Opacity:', window.getComputedStyle(btnNext).opacity);
    console.log('Position:', window.getComputedStyle(btnNext).position);
    console.log('Top:', window.getComputedStyle(btnNext).top);
    console.log('Bottom:', window.getComputedStyle(btnNext).bottom);
}
```

### 2. Проверка HTML структуры

Убедитесь, что в HTML есть кнопки внутри `.product-gallery-thumbs`:

```html
<div class="swiper product-gallery-thumbs">
    <div class="swiper-wrapper">
        <!-- слайды -->
    </div>
    <!-- ДОЛЖНЫ БЫТЬ ЭТИ КНОПКИ: -->
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
</div>
```

### 3. Временное решение для отладки

Добавьте в `resources/views/product/show.blade.php`:

```php
@push('styles')
    <style>
        /* Временные стили для отладки */
        .product-gallery-thumbs .swiper-button-next,
        .product-gallery-thumbs .swiper-button-prev {
            background: red !important;
            opacity: 1 !important;
            z-index: 9999 !important;
            display: flex !important;
        }
    </style>
@endpush
```

Если после этого кнопки видны (красные), значит проблема в основных стилях.

### 4. Проверка инициализации Swiper

В консоли проверьте:

```javascript
// Проверяем объект галереи
window.productGallery

// Проверяем Swiper миниатюр
window.productGallery.thumbsSwiper

// Проверяем параметры навигации
window.productGallery.thumbsSwiper.params.navigation
```

### 5. Возможные причины

1. **Кнопки не созданы в HTML** - проверьте `resources/views/product/show.blade.php`
2. **CSS не загружен** - проверьте Network в DevTools
3. **Swiper не инициализирован с навигацией** - проверьте `product-gallery.js`
4. **Z-index проблема** - кнопки за другими элементами
5. **Overflow hidden** - кнопки обрезаны родительским контейнером

### 6. Быстрое решение

Если ничего не помогает, можно использовать inline стили прямо в HTML:

```html
<div class="swiper-button-next" style="position: absolute; bottom: 0; left: 0; width: 72px; height: 32px; background: rgba(255,255,255,0.9); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #005bff; z-index: 10;"></div>
```

## Ожидаемый результат

Кнопки должны быть видны:
- Кнопка "вверх" (▲) - в самом верху вертикальной карусели
- Кнопка "вниз" (▼) - в самом низу вертикальной карусели
- Ширина кнопок = ширине миниатюр (72px)
- Высота кнопок = 32px
- Цвет фона: белый с прозрачностью
- Цвет стрелки: синий (#005bff)

