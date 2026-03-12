# Implementation Plan: Product Page Gallery with Swiper.js

## Overview

Реализация улучшенной галереи изображений для страницы товара с использованием библиотеки **Swiper.js**. Swiper обеспечивает готовую поддержку touch/swipe, вертикальной прокрутки миниатюр и модального просмотра.

**Преимущества Swiper:**
- Стандартизация подхода к слайдерам по всему сайту
- Готовая поддержка touch/swipe на всех устройствах
- Модульная архитектура
- Можно использовать в контентных блоках, на главной, в каталоге

## Tasks

- [x] 1. Установить и подключить Swiper.js
  - Скачать Swiper.js v11.x с https://swiperjs.com/
  - Создать `/public/assets/libs/swiper/`
  - Разместить `swiper-bundle.min.css` и `swiper-bundle.min.js`
  - Создать `/public/assets/sfera/css/product-gallery.css`
  - Создать `/public/assets/sfera/js/product-gallery.js`

- [x] 2. Обновить разметку страницы товара
  - [x] 2.1 Создать HTML структуру для Swiper в `resources/views/product/show.blade.php`
    - Контейнер для основного Swiper (.product-gallery-main)
    - Контейнер для миниатюр (.product-gallery-thumbs) с вертикальной ориентацией
    - Обернуть изображения в .swiper-slide
    - Добавить навигационные кнопки
  
  - [x] 2.2 Подключить Swiper через @push
    - `@push('styles')` - swiper-bundle.min.css
    - `@push('styles')` - product-gallery.css
    - `@push('scripts')` - swiper-bundle.min.js  
    - `@push('scripts')` - product-gallery.js
    - **КРИТИЧНО**: Использовать @push, НЕ @section

- [x] 3. Создать CSS стили
  - [x] 3.1 Базовый layout и переменные
    - CSS переменные для размеров, цветов
    - Layout: миниатюры слева (вертикально), основное изображение справа
  
  - [x] 3.2 Кастомизация Swiper для миниатюр
    - Стили для .swiper-slide-thumb-active
    - Hover и focus состояния
    - Вертикальная ориентация
  
  - [x] 3.3 Стили для lightbox
    - Overlay с полупрозрачным фоном
    - Контейнер модального окна
    - Кнопка закрытия и навигация
  
  - [x] 3.4 Responsive стили
    - Media queries для < 768px
    - Адаптация размеров миниатюр

- [x] 4. Инициализировать Swiper для галереи
  - [x] 4.1 Создать класс ProductGallery
    - Конструктор и метод init()
    - Хранение Swiper инстансов
  
  - [x] 4.2 Инициализировать Swiper для миниатюр
    - direction: 'vertical'
    - slidesPerView: 5
    - freeMode: true
    - watchSlidesProgress: true
  
  - [x] 4.3 Инициализировать основной Swiper
    - Модули: Navigation, Pagination, Thumbs
    - thumbs: { swiper: thumbsSwiper }
    - Настроить навигацию и переходы

- [x] 5. Checkpoint - Базовая галерея работает
  - Миниатюры слева с вертикальной прокруткой
  - Основное изображение справа (статичное, без свайпа)
  - Навигация через кнопки ▲ и ▼
  - Клик по миниатюре переключает основное изображение
  - Адаптировано из CodePen примера: https://codepen.io/hqdrone/pen/dypEyNq

- [x] 6. Реализовать lightbox модальное окно
  - [x] 6.1 Создать HTML структуру lightbox
    - Overlay и контейнер
    - Swiper контейнер внутри модального окна
    - Кнопка закрытия
  
  - [x] 6.2 Реализовать openLightbox(index)
    - Создание Swiper с модулями: Navigation, Pagination, Zoom, Keyboard
    - initialSlide: index
    - Показать модальное окно
    - Заблокировать прокрутку body
  
  - [x] 6.3 Настроить Zoom модуль
    - zoom: { maxRatio: 3, minRatio: 1 }
    - Double-tap и pinch-to-zoom
  
  - [x] 6.4 Настроить Keyboard модуль
    - keyboard: { enabled: true }
    - Обработчик Escape для закрытия
  
  - [x] 6.5 Реализовать closeLightbox()
    - Скрыть модальное окно
    - Уничтожить Swiper (destroy)
    - Восстановить прокрутку body
  
  - [x] 6.6 Добавить обработчики закрытия
    - Клик по кнопке закрытия
    - Клик по overlay
    - Нажатие Escape

- [x] 7. Checkpoint - Модальное окно работает
  - Открывается по клику на основное изображение
  - Навигация стрелками и клавиатурой
  - Закрытие по Escape, клику на overlay или кнопку ×
  - Zoom поддержка (maxRatio: 3)
  - Pagination показывает текущий слайд

- [x] 8. Добавить accessibility
  - [x] 8.1 ARIA атрибуты
    - aria-label для кнопок навигации
    - role="region" для галереи
    - aria-label для миниатюр и основного изображения
  
  - [x] 8.2 Keyboard navigation
    - Tab navigation с focus indicators
    - Enter/Space для активации миниатюр
    - Enter/Space для открытия lightbox
  
  - [x] 8.3 Focus management
    - Видимые focus indicators (outline + box-shadow)
    - Кнопки теперь <button> вместо <div>
    - tabindex="0" для интерактивных элементов
  
  - [x] 8.4 Цветовой контраст
    - Focus outline: #005bff (достаточный контраст)
    - Кнопки навигации: синий на белом фоне

- [x] 9. Responsive и mobile support
  - [x] 9.1 Настроить breakpoints в Swiper
    - Адаптивный slidesPerView для миниатюр
    - direction: 'horizontal' на мобильных
    - direction: 'vertical' на десктопе (768px+)
  
  - [x] 9.2 Оптимизировать для мобильных
    - Touch/swipe (встроено в Swiper)
    - Размеры кнопок 48x48px (достаточно для touch)
    - Горизонтальная прокрутка миниатюр на мобильных
  
  - [x] 9.3 Responsive CSS
    - @media (max-width: 768px) для мобильных
    - Адаптация layout (column-reverse)
    - Адаптация размеров миниатюр

- [x] 10. Performance optimizations
  - [x] 10.1 Настроить lazy loading
    - Подключен Lazy модуль Swiper
    - preloadImages: false
    - lazy: { loadPrevNext: true, loadPrevNextAmount: 1-2 }
  
  - [x] 10.2 Оптимизировать анимации
    - CSS transitions (встроено)
    - speed: 300ms
    - effect: 'fade' для плавности на десктопе

- [x] 11. Security measures
  - [x] 11.1 Sanitization и validation
    - Blade автоматически экранирует через {{ }}
    - URL изображений экранированы
    - Alt текст экранирован
    - onerror fallback для безопасной обработки ошибок
  
  - [x] 11.2 HTTPS и CSP compliance
    - Относительные пути для изображений (без протокола)
    - Нет inline стилей в галерее
    - Все стили в отдельном CSS файле

- [x] 12. Checkpoint - Финальная проверка
  - Базовая галерея работает
  - Lightbox работает
  - Accessibility реализована
  - Responsive поддержка есть
  - Performance оптимизирована
  - Security меры применены

- [x] 13. Интеграция и тестирование
  - [x] 13.1 Функциональное тестирование
    - Базовая галерея работает корректно
    - Миниатюры переключают основное изображение
    - Навигация кнопками ▲ и ▼ работает
    - Lightbox открывается и закрывается
    - Keyboard navigation работает
  
  - [ ] 13.2 Тестирование на устройствах
    - Desktop (проверено)
    - Tablet (требуется проверка)
    - Mobile (требуется проверка)
    - Touch/swipe на реальных устройствах
  
  - [ ] 13.3 Accessibility audit
    - Screen reader тестирование (требуется)
    - Keyboard navigation (проверено)
    - Цветовой контраст (проверено)

- [x] 14. Документация использования Swiper
  - [x] 14.1 Создать руководство по использованию
    - Базовая структура HTML
    - Инициализация JavaScript
    - Рекомендуемые модули для разных сценариев
    - Performance оптимизации
    - Accessibility рекомендации
  
  - [x] 14.2 Примеры для других частей сайта
    - Галерея товара (реализовано)
    - Карусель на главной (пример в документации)
    - Слайдер в контентных блоках (пример в документации)
  
  - [x] 14.3 Правила использования Swiper
    - Когда использовать Swiper
    - Какие модули для каких сценариев
    - Примеры конфигураций
    - Отладка распространенных проблем

- [x] 15. Final checkpoint - Готово к production
  - ✅ Базовая галерея работает корректно
  - ✅ Lightbox с zoom и keyboard navigation
  - ✅ Accessibility (ARIA, keyboard, focus indicators)
  - ✅ Responsive дизайн (desktop + mobile)
  - ✅ Performance оптимизации (lazy loading, fade эффект)
  - ✅ Security меры (Blade экранирование, CSP compliance)
  - ✅ Документация создана
  - ⚠️ Требуется тестирование на реальных мобильных устройствах
  - ⚠️ Требуется screen reader тестирование

## Notes

- Swiper значительно упрощает реализацию по сравнению с custom решением
- Touch/swipe поддержка работает out-of-the-box
- Модульная архитектура позволяет подключать только нужный функционал
- Swiper можно использовать по всему сайту для стандартизации
- Все задачи ссылаются на requirements из requirements.md
