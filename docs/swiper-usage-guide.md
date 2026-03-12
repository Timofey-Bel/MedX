# Руководство по использованию Swiper.js в проекте

## Обзор

Swiper.js v11.x используется в проекте для создания слайдеров и галерей изображений. Библиотека обеспечивает единообразный подход к реализации каруселей по всему сайту.

## Установка

Swiper.js установлен локально в `/public/assets/libs/swiper/`:
- `swiper-bundle.min.css` - стили
- `swiper-bundle.min.js` - JavaScript

## Где используется

### 1. Галерея товара (Product Page Gallery)

**Файлы:**
- HTML: `resources/views/product/show.blade.php`
- CSS: `public/assets/sfera/css/product-gallery.css`
- JS: `public/assets/sfera/js/product-gallery.js`

**Особенности:**
- Вертикальная карусель миниатюр слева
- Основное изображение справа (статичное)
- Lightbox модальное окно с zoom
- Keyboard navigation и accessibility
- Lazy loading изображений

**Модули Swiper:**
- Navigation (кнопки ▲ ▼)
- Thumbs (связь миниатюр с основным изображением)
- Keyboard (управление клавиатурой)
- Zoom (увеличение в lightbox)
- Lazy (отложенная загрузка)

## Базовая структура HTML

```html
<div class="swiper-container">
    <div class="swiper-wrapper">
        <div class="swiper-slide">
            <!-- Контент слайда -->
        </div>
        <div class="swiper-slide">
            <!-- Контент слайда -->
        </div>
    </div>
    
    <!-- Опционально: навигация -->
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
    
    <!-- Опционально: пагинация -->
    <div class="swiper-pagination"></div>
</div>
```

## Базовая инициализация JavaScript

```javascript
const swiper = new Swiper('.swiper-container', {
    // Основные параметры
    direction: 'horizontal', // или 'vertical'
    slidesPerView: 1,
    spaceBetween: 10,
    
    // Модули
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
    
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
    },
    
    // Responsive breakpoints
    breakpoints: {
        768: {
            slidesPerView: 2,
        },
        1024: {
            slidesPerView: 3,
        }
    }
});
```

## Рекомендуемые модули для разных сценариев

### Простая карусель (контентные блоки, главная)
```javascript
{
    slidesPerView: 'auto',
    spaceBetween: 16,
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
    }
}
```

### Галерея с миниатюрами
```javascript
// Миниатюры
const thumbsSwiper = new Swiper('.thumbs-swiper', {
    direction: 'vertical',
    slidesPerView: 4,
    freeMode: true,
    watchSlidesProgress: true,
});

// Основной слайдер
const mainSwiper = new Swiper('.main-swiper', {
    thumbs: {
        swiper: thumbsSwiper
    }
});
```

### Карусель с автопрокруткой
```javascript
{
    autoplay: {
        delay: 3000,
        disableOnInteraction: false,
    },
    loop: true,
}
```

## Performance оптимизации

### Lazy Loading
```javascript
{
    lazy: {
        loadPrevNext: true,
        loadPrevNextAmount: 2
    },
    preloadImages: false,
}
```

### Виртуальные слайды (для больших списков)
```javascript
{
    virtual: {
        slides: arrayOfSlides,
    }
}
```

## Accessibility

### ARIA атрибуты в HTML
```html
<div class="swiper-container" role="region" aria-label="Карусель товаров">
    <div class="swiper-wrapper">
        <div class="swiper-slide" role="button" tabindex="0" aria-label="Слайд 1">
            <!-- Контент -->
        </div>
    </div>
    <button class="swiper-button-next" aria-label="Следующий слайд"></button>
    <button class="swiper-button-prev" aria-label="Предыдущий слайд"></button>
</div>
```

### Keyboard navigation
```javascript
{
    keyboard: {
        enabled: true,
        onlyInViewport: false
    }
}
```

## Responsive дизайн

### Breakpoints
```javascript
{
    breakpoints: {
        // При ширине >= 320px
        320: {
            slidesPerView: 1,
            spaceBetween: 10
        },
        // При ширине >= 768px
        768: {
            slidesPerView: 2,
            spaceBetween: 20
        },
        // При ширине >= 1024px
        1024: {
            slidesPerView: 3,
            spaceBetween: 30
        }
    }
}
```

## Подключение в Blade

**КРИТИЧНО:** Всегда используйте `@push`, НЕ `@section`!

```php
@push('styles')
    <link rel="stylesheet" href="/assets/libs/swiper/swiper-bundle.min.css">
    <link rel="stylesheet" href="/assets/sfera/css/your-slider.css">
@endpush

@push('scripts')
    <script src="/assets/libs/swiper/swiper-bundle.min.js"></script>
    <script src="/assets/sfera/js/your-slider.js"></script>
@endpush
```

## Примеры использования в проекте

### 1. Галерея товара
- Файл: `public/assets/sfera/js/product-gallery.js`
- Модули: Navigation, Thumbs, Keyboard, Zoom, Lazy
- Особенности: Вертикальные миниатюры, lightbox

### 2. Карусель на главной (будущее)
- Модули: Navigation, Pagination, Autoplay
- Особенности: Автопрокрутка, loop

### 3. Слайдер в контентных блоках (будущее)
- Модули: Navigation, Pagination
- Особенности: Адаптивный slidesPerView

## Отладка

### Swiper не инициализируется
1. Проверьте, что `swiper-bundle.min.js` загружен (DevTools → Network)
2. Проверьте консоль на ошибки JavaScript
3. Убедитесь, что HTML структура правильная (swiper-container → swiper-wrapper → swiper-slide)

### Слайды не видны
1. Проверьте CSS - контейнер должен иметь высоту
2. Убедитесь, что `swiper-bundle.min.css` загружен
3. Проверьте `overflow: hidden` на контейнере

### Навигация не работает
1. Проверьте селекторы в `navigation: { nextEl, prevEl }`
2. Убедитесь, что кнопки существуют в DOM
3. Проверьте, что кнопки не скрыты CSS

## Полезные ссылки

- [Официальная документация Swiper.js](https://swiperjs.com/)
- [API Reference](https://swiperjs.com/swiper-api)
- [Demos](https://swiperjs.com/demos)
- Пример в проекте: `public/assets/sfera/js/product-gallery.js`

## История

- 2026-02-28: Создан документ после реализации галереи товара
- Swiper v11.x установлен локально
- Реализована галерея с вертикальными миниатюрами и lightbox
