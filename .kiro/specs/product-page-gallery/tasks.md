# Implementation Plan: Product Page Gallery Enhancement

## Overview

Реализация улучшенной галереи изображений для страницы товара с вертикальной прокруткой миниатюр и полноэкранным модальным просмотром. Галерея будет реализована с использованием библиотеки **Swiper.js** - проверенного решения для слайдеров с полной поддержкой touch/swipe на всех устройствах.

**Преимущества использования Swiper:**
- Стандартизация подхода к слайдерам по всему сайту
- Готовая поддержка touch/swipe на мобильных устройствах
- Модульная архитектура (подключаем только нужные модули)
- Можно использовать в контентных блоках, на главной странице, в каталоге
- Активная поддержка и большое сообщество

## Tasks

- [ ] 1. Установить и подключить Swiper.js
  - Создать файл `/public/assets/sfera/css/product-gallery.css`
  - Создать файл `/public/assets/sfera/js/product-gallery.js`
  - Обновить разметку в `resources/views/product/show.blade.php` для поддержки новой структуры галереи
  - Добавить подключение CSS и JS файлов через `@push('styles')` и `@push('scripts')`
  - _Requirements: 1.5, 14.1-14.4_

- [ ] 2. Реализовать базовые CSS стили галереи
  - [ ] 2.1 Создать CSS переменные и базовую структуру
    - Определить CSS переменные для размеров, отступов, цветов и анимаций
    - Реализовать BEM-структуру классов (.product-gallery, .product-gallery__thumbnails, и т.д.)
    - Создать layout с вертикальными миниатюрами слева и основным изображением справа
    - _Requirements: 1.5, 13.1-13.2_
  
  - [ ] 2.2 Стилизовать миниатюры и состояния
    - Стили для обычного состояния миниатюр
    - Стили для активной миниатюры (border, shadow)
    - Стили для hover и focus состояний
    - _Requirements: 1.2, 11.1_
  
  - [ ] 2.3 Стилизовать кнопки прокрутки
    - Стили для кнопок прокрутки вверх/вниз
    - Стили для disabled состояния кнопок
    - Позиционирование кнопок относительно контейнера миниатюр
    - _Requirements: 2.1, 2.4, 2.5_
  
  - [ ] 2.4 Создать стили модального окна
    - Overlay с полупрозрачным фоном
    - Контейнер модального окна с центрированием
    - Стили для основного изображения в модальном окне
    - Стили для навигационных стрелок и кнопки закрытия
    - Стили для горизонтальной полосы миниатюр внизу
    - _Requirements: 3.1, 3.4, 3.6, 7.1, 7.4_
  
  - [ ] 2.5 Добавить CSS анимации и transitions
    - Fade-in/fade-out для модального окна
    - Slide transitions для смены изображений
    - Smooth scroll для миниатюр
    - Hover эффекты для интерактивных элементов
    - _Requirements: 1.3, 2.6, 3.4, 4.5, 8.4_
  
  - [ ] 2.6 Реализовать responsive стили
    - Media queries для мобильных устройств (< 768px)
    - Адаптация размеров миниатюр
    - Скрытие полосы миниатюр в модальном окне на мобильных
    - _Requirements: 13.1-13.3_

- [ ] 3. Реализовать класс ThumbnailCarousel
  - [ ] 3.1 Создать конструктор и метод init()
    - Инициализация DOM элементов (контейнер, кнопки, миниатюры)
    - Установка начального состояния
    - Привязка event listeners для кликов по миниатюрам
    - Привязка event listeners для кнопок прокрутки
    - _Requirements: 1.1, 2.1-2.3_
  
  - [ ] 3.2 Реализовать методы прокрутки scrollUp() и scrollDown()
    - Вычисление новой позиции прокрутки
    - Анимация прокрутки с использованием smooth scroll
    - Обновление состояния кнопок после прокрутки
    - _Requirements: 2.2, 2.3, 2.6_
  
  - [ ] 3.3 Реализовать метод setActive(index)
    - Удаление класса active с предыдущей миниатюры
    - Добавление класса active на новую миниатюру
    - Прокрутка миниатюры в видимую область при необходимости
    - _Requirements: 1.2, 1.4, 7.5_
  
  - [ ] 3.4 Реализовать метод updateScrollButtons()
    - Проверка позиции прокрутки (top/bottom)
    - Установка disabled состояния для кнопок
    - Показ/скрытие кнопок в зависимости от количества миниатюр
    - _Requirements: 2.4, 2.5, 2.7_
  
  - [ ]* 3.5 Написать property test для ThumbnailCarousel
    - **Property 2: Single Active Thumbnail Invariant**
    - **Property 4: Scroll Button Visibility Threshold**
    - **Property 5: Scroll Position Bounds Invariant**
    - **Validates: Requirements 1.2, 1.4, 2.1, 2.2, 2.3, 2.4, 2.5, 2.7**

- [ ] 4. Реализовать класс MainImageDisplay
  - [ ] 4.1 Создать конструктор и метод setImage(index, transition)
    - Инициализация контейнера основного изображения
    - Загрузка и отображение изображения по индексу
    - Применение fade transition при необходимости
    - Обработка ошибок загрузки изображений
    - _Requirements: 1.1, 1.3, 9.3, 10.4_
  
  - [ ] 4.2 Реализовать метод preloadImages()
    - Предзагрузка первых трех изображений при инициализации
    - Предзагрузка соседних изображений при навигации
    - Использование Image() объектов для предзагрузки
    - _Requirements: 9.1, 9.2, 4.6_
  
  - [ ] 4.3 Добавить обработку ошибок загрузки
    - Отображение placeholder при ошибке загрузки
    - Логирование ошибок в консоль
    - Возможность продолжить просмотр других изображений
    - _Requirements: 9.3, 9.4, 10.4, 10.5_
  
  - [ ]* 4.4 Написать property test для MainImageDisplay
    - **Property 15: Image Load Error Fallback**
    - **Property 16: Adjacent Image Preloading**
    - **Validates: Requirements 9.2, 9.3, 10.4**

- [ ] 5. Checkpoint - Базовая галерея работает
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 6. Реализовать класс ModalViewer
  - [ ] 6.1 Создать конструктор и методы open(index) / close()
    - Создание DOM структуры модального окна
    - Метод open: отображение модального окна, блокировка прокрутки body
    - Метод close: скрытие модального окна, восстановление прокрутки
    - Fade-in/fade-out анимации
    - _Requirements: 3.1-3.6, 8.1-8.6_
  
  - [ ] 6.2 Реализовать навигацию next() и prev()
    - Циклическое переключение изображений
    - Slide transitions при смене изображений
    - Предзагрузка соседних изображений
    - Обновление активной миниатюры в полосе
    - _Requirements: 4.1-4.6, 7.2, 7.3_
  
  - [ ] 6.3 Реализовать метод goTo(index)
    - Переход к конкретному изображению по индексу
    - Валидация индекса и clamping к допустимому диапазону
    - Обновление UI (активная миниатюра, основное изображение)
    - _Requirements: 7.2, 10.2, 17_
  
  - [ ] 6.4 Добавить обработку клавиатуры handleKeyboard(event)
    - Arrow Left/Right для навигации
    - Escape для закрытия
    - Home/End для перехода к первому/последнему изображению
    - _Requirements: 5.1-5.5, 8.3_
  
  - [ ] 6.5 Добавить обработку touch событий handleTouch(event)
    - Определение начала и конца touch gesture
    - Вычисление направления и дистанции свайпа
    - Игнорирование вертикальных свайпов и коротких жестов
    - Навигация при валидном горизонтальном свайпе
    - _Requirements: 6.1-6.5_
  
  - [ ] 6.6 Реализовать полосу миниатюр в модальном окне
    - Горизонтальный scrollable контейнер
    - Клики по миниатюрам для быстрого переключения
    - Автоматическая прокрутка активной миниатюры в видимую область
    - Выделение активной миниатюры
    - _Requirements: 7.1-7.5_
  
  - [ ] 6.7 Добавить обработчики закрытия модального окна
    - Клик по кнопке закрытия
    - Клик по overlay (вне изображения)
    - Нажатие Escape (через handleKeyboard)
    - Очистка event listeners при закрытии
    - _Requirements: 8.1-8.6_
  
  - [ ]* 6.8 Написать property tests для ModalViewer
    - **Property 6: Modal Opens with Current Image**
    - **Property 7: Modal Body Scroll Lock State**
    - **Property 9: Cyclic Next Navigation**
    - **Property 10: Cyclic Previous Navigation**
    - **Property 11: Swipe Distance Threshold**
    - **Property 12: Swipe Direction Detection**
    - **Property 18: Modal Open Idempotency**
    - **Validates: Requirements 3.1-3.3, 4.1-4.4, 5.1-5.2, 6.1-6.4, 8.3-8.5**

- [ ] 7. Реализовать класс GalleryController
  - [ ] 7.1 Создать конструктор и метод init()
    - Извлечение данных изображений из DOM
    - Инициализация ThumbnailCarousel
    - Инициализация MainImageDisplay
    - Установка event listeners для открытия модального окна
    - Валидация наличия изображений
    - _Requirements: 10.1_
  
  - [ ] 7.2 Реализовать координацию компонентов
    - Синхронизация ThumbnailCarousel и MainImageDisplay
    - Обработка кликов по миниатюрам (обновление обоих компонентов)
    - Обработка клика по основному изображению (открытие модального окна)
    - _Requirements: 1.1, 3.1_
  
  - [ ] 7.3 Реализовать метод openModal(index)
    - Ленивая инициализация ModalViewer при первом вызове
    - Передача текущего индекса в модальное окно
    - Проверка на повторное открытие (idempotency)
    - _Requirements: 3.1, 3.2, 10.3, 18_
  
  - [ ] 7.4 Реализовать метод destroy()
    - Очистка всех event listeners
    - Уничтожение всех компонентов
    - Освобождение ресурсов
    - _Requirements: 8.6, 12.5_
  
  - [ ]* 7.5 Написать property tests для GalleryController
    - **Property 1: Thumbnail Selection Displays Corresponding Image**
    - **Property 17: Index Clamping and Validation**
    - **Validates: Requirements 1.1, 7.2, 10.2, 15.5**

- [ ] 8. Checkpoint - Модальное окно работает
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 9. Добавить accessibility features
  - [ ] 9.1 Добавить ARIA атрибуты
    - aria-label для всех кнопок и интерактивных элементов
    - aria-controls для кнопок прокрутки
    - role="img" для контейнера основного изображения
    - aria-live регион для объявления смены изображений
    - _Requirements: 11.5, 11.6_
  
  - [ ] 9.2 Реализовать keyboard navigation
    - Tab navigation с видимыми focus indicators
    - Enter/Space для активации миниатюр
    - Все клавиатурные shortcuts уже реализованы в ModalViewer
    - _Requirements: 11.1, 11.2_
  
  - [ ] 9.3 Реализовать focus management
    - Перемещение фокуса в модальное окно при открытии
    - Возврат фокуса на элемент, открывший модальное окно, при закрытии
    - Trap focus внутри модального окна
    - _Requirements: 11.3, 11.4_
  
  - [ ] 9.4 Проверить цветовой контраст
    - Убедиться что все UI элементы соответствуют WCAG 2.1 AA
    - Проверить контраст кнопок, границ активных миниатюр
    - _Requirements: 11.7_
  
  - [ ]* 9.5 Написать property tests для accessibility
    - **Property 19: Keyboard Thumbnail Activation**
    - **Property 20: Modal Focus Management**
    - **Property 21: Interactive Elements ARIA Labels**
    - **Property 22: Image Change Announcements**
    - **Validates: Requirements 11.1-11.6**

- [ ] 10. Добавить performance optimizations
  - [ ] 10.1 Реализовать debouncing для scroll events
    - Создать utility функцию debounce
    - Применить к обработчикам прокрутки миниатюр
    - Применить к обработчикам resize событий
    - _Requirements: 12.1_
  
  - [ ] 10.2 Оптимизировать event listeners
    - Использовать event delegation для миниатюр
    - Использовать passive: true для touch событий
    - Убедиться что все listeners удаляются в destroy()
    - _Requirements: 12.2, 12.4, 12.5_
  
  - [ ] 10.3 Оптимизировать анимации
    - Использовать CSS transitions вместо JavaScript где возможно
    - Использовать transform и opacity для GPU acceleration
    - Избегать layout thrashing
    - _Requirements: 12.3_
  
  - [ ]* 10.4 Написать performance tests
    - Измерить время инициализации галереи
    - Измерить FPS во время анимаций
    - Проверить отсутствие memory leaks

- [ ] 11. Добавить responsive и mobile support
  - [ ] 11.1 Реализовать адаптивные размеры
    - Media query для мобильных устройств (< 768px)
    - Уменьшение размера миниатюр до 60px
    - Уменьшение количества видимых миниатюр до 4
    - _Requirements: 13.1, 13.2_
  
  - [ ] 11.2 Адаптировать модальное окно для мобильных
    - Скрытие полосы миниатюр на мобильных
    - Оптимизация размеров кнопок для touch
    - _Requirements: 13.3_
  
  - [ ] 11.3 Добавить responsive images
    - Использовать srcset для адаптивной загрузки
    - Поддержка разных pixel ratio (1x, 2x, 3x)
    - _Requirements: 13.4_
  
  - [ ] 11.4 Обработать orientation change
    - Слушать событие orientationchange
    - Пересчитывать layout и scroll positions
    - _Requirements: 13.5_
  
  - [ ]* 11.5 Написать property tests для responsive
    - **Property 23: Responsive Image Attributes**
    - **Property 24: Orientation Change Layout Update**
    - **Validates: Requirements 13.4, 13.5**

- [ ] 12. Добавить security measures
  - [ ] 12.1 Реализовать sanitization и validation
    - Экранирование всех URL изображений
    - Sanitization alt текста
    - Валидация всех входных параметров (индексы, опции)
    - _Requirements: 15.1, 15.2, 15.5_
  
  - [ ] 12.2 Обеспечить HTTPS и CSP compliance
    - Проверка что все изображения загружаются по HTTPS
    - Избегание inline стилей и скриптов
    - _Requirements: 15.3, 15.4_
  
  - [ ]* 12.3 Написать security tests
    - **Property 25: Content Sanitization for XSS Prevention**
    - **Property 26: HTTPS Protocol Enforcement**
    - **Property 27: No Inline Styles or Scripts**
    - **Validates: Requirements 15.1-15.4**

- [ ] 13. Checkpoint - Финальная проверка
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 14. Интеграция и финальное тестирование
  - [ ] 14.1 Интегрировать галерею в страницу товара
    - Обновить Blade шаблон с правильной структурой
    - Подключить CSS и JS файлы через @push
    - Убедиться что данные изображений передаются из контроллера
    - _Requirements: 1.5_
  
  - [ ] 14.2 Протестировать в разных браузерах
    - Chrome 90+
    - Firefox 88+
    - Safari 14+
    - Edge 90+
    - _Requirements: 14.1-14.4_
  
  - [ ] 14.3 Протестировать на разных устройствах
    - Desktop (различные разрешения)
    - Tablet (portrait и landscape)
    - Mobile (различные размеры экранов)
    - _Requirements: 13.1-13.5_
  
  - [ ] 14.4 Провести accessibility audit
    - Проверить с помощью screen reader (NVDA/JAWS)
    - Проверить keyboard navigation
    - Проверить цветовой контраст
    - Проверить ARIA атрибуты
    - _Requirements: 11.1-11.7_
  
  - [ ]* 14.5 Написать integration tests
    - Тест полного flow: клик по миниатюре → обновление основного изображения
    - Тест полного flow: клик по основному изображению → открытие модального окна → навигация → закрытие
    - Тест взаимодействия всех компонентов

- [ ] 15. Final checkpoint - Готово к production
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- Задачи, отмеченные `*`, являются опциональными и могут быть пропущены для быстрого MVP
- Каждая задача ссылается на конкретные требования для отслеживаемости
- Checkpoints обеспечивают инкрементальную валидацию
- Property tests валидируют универсальные свойства корректности
- Unit tests валидируют конкретные примеры и edge cases
- Реализация следует архитектурным паттернам проекта (использование @push для скриптов/стилей)
- Все изображения должны загружаться из существующих источников (o_images, import_files)
