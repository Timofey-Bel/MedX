# MedX Platform - Структура проекта

## Обзор

MedX - интерактивная образовательная платформа для медиков, интегрированная в Laravel проект.

## Структура файлов

### Контроллеры

```
app/Http/Controllers/
└── MedxController.php          # Основной контроллер MedX
    ├── showcase()              # Главная страница
    ├── login()                 # Страница входа
    └── register()              # Страница регистрации
```

### Views (Blade шаблоны)

```
resources/views/
├── layouts/
│   └── medx.blade.php          # Основной layout для MedX
├── medx/
│   ├── components/
│   │   ├── header.blade.php    # Шапка сайта
│   │   └── footer.blade.php    # Подвал сайта
│   ├── showcase/
│   │   ├── index.blade.php     # Главная страница
│   │   ├── disciplines.blade.php    # Секция дисциплин
│   │   ├── feature-cards.blade.php  # Карточки возможностей
│   │   └── learn-section.blade.php  # Секция обучения
│   └── auth/
│       ├── login.blade.php     # Форма входа
│       └── register.blade.php  # Форма регистрации
```

### CSS файлы

```
public/assets/medx/css/
├── header.css              # Стили шапки
├── footer.css              # Стили подвала
├── showcase.css            # Стили главной страницы
├── disciplines.css         # Стили секции дисциплин
├── features.css            # Стили карточек возможностей
├── learn-section.css       # Стили секции обучения
└── auth.css                # Стили страниц авторизации
```

### JavaScript файлы

```
public/assets/medx/js/
└── showcase.js             # Интерактивность главной страницы
    ├── initDisciplinesTabs()    # Переключение табов дисциплин
    ├── initSmoothScroll()       # Плавная прокрутка
    ├── initConveyorAnimation()  # Анимация бегущей строки
    └── initScrollCards()        # Горизонтальная прокрутка карточек
```

## Роуты

```php
// routes/web.php

Route::prefix('medx')->name('medx.')->group(function () {
    Route::get('/', [MedxController::class, 'showcase'])->name('showcase');
    Route::get('/login', [MedxController::class, 'login'])->name('login');
    Route::get('/register', [MedxController::class, 'register'])->name('register');
});
```

### Доступные URL:

- `http://localhost/medx` - Главная страница MedX
- `http://localhost/medx/login` - Вход
- `http://localhost/medx/register` - Регистрация

## Компоненты главной страницы

### 1. Hero Section (Главный баннер)
- Логотип MedX
- Заголовок и описание
- Кнопка "Начать бесплатно"

### 2. Promo Banner (Бегущая строка)
- Анимированная бегущая строка с промо-текстом
- Автоматическая анимация через CSS

### 3. About Section (О платформе)
- Приветственный текст
- Карточки с примерами материалов

### 4. Disciplines Section (Дисциплины)
- Табы: Фундаментальные / Клинические
- Сетка карточек дисциплин
- Интерактивное переключение через JavaScript

### 5. Features Section (Возможности)
- 4 карточки с основными возможностями:
  - База знаний
  - Интерактивные тесты
  - Практические задания
  - Сообщество

### 6. Learn Section (Обучение)
- Описание преимуществ
- Список возможностей
- Иллюстрация
- CTA кнопка

### 7. CTA Section (Призыв к действию)
- Кнопка регистрации

### 8. Devices Section (Мультиплатформенность)
- Изображение устройств
- Текст о доступности

## Подключение стилей и скриптов

### В layout (layouts/medx.blade.php):

```php
<link rel="stylesheet" href="{{ asset('assets/medx/styles.css') }}">
<link rel="stylesheet" href="{{ asset('assets/medx/css/header.css') }}">
<link rel="stylesheet" href="{{ asset('assets/medx/css/footer.css') }}">

@stack('styles')  // Дополнительные стили из страниц
```

### На странице showcase (showcase/index.blade.php):

```php
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/medx/css/showcase.css') }}">
<link rel="stylesheet" href="{{ asset('assets/medx/css/disciplines.css') }}">
<link rel="stylesheet" href="{{ asset('assets/medx/css/features.css') }}">
<link rel="stylesheet" href="{{ asset('assets/medx/css/learn-section.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('assets/medx/js/showcase.js') }}"></script>
@endpush
```

## Правила разработки

### 1. Blade директивы
- Всегда размещайте директивы на отдельных строках
- Не используйте inline директивы
- См. `.kiro/steering/blade-directives-formatting.md`

### 2. Подключение скриптов и стилей
- Используйте `@push('styles')` и `@push('scripts')`
- НЕ используйте `@section` для скриптов/стилей
- См. `.kiro/steering/blade-scripts-styles.md`

### 3. Структура компонентов
- Разделяйте большие страницы на компоненты
- Используйте `@include()` для подключения компонентов
- Каждый компонент в отдельном файле

### 4. CSS организация
- Один CSS файл на компонент/секцию
- Используйте БЭМ-подобную методологию для классов
- Responsive стили в конце файла

## Цветовая схема

```css
:root {
    --primary-color: #667eea;      /* Основной фиолетовый */
    --primary-dark: #5568d3;       /* Темный фиолетовый */
    --secondary-color: #764ba2;    /* Вторичный фиолетовый */
    --text-dark: #333333;          /* Темный текст */
    --text-light: #666666;         /* Светлый текст */
    --text-muted: rgba(255, 255, 255, 0.7);  /* Приглушенный текст */
    --bg-light: #f8f9fa;           /* Светлый фон */
    --border-color: #e0e0e0;       /* Цвет границ */
}
```

## Типографика

- Основной шрифт: system fonts (sans-serif)
- Заголовки: 700 weight
- Основной текст: 400 weight
- Акценты: 600 weight

## Адаптивность

### Breakpoints:
- Desktop: > 968px
- Tablet: 640px - 968px
- Mobile: < 640px

### Подход:
- Mobile-first не используется
- Desktop-first с media queries для меньших экранов
- Grid и Flexbox для layout

## Следующие шаги

### Для полной интеграции необходимо:

1. **Авторизация**
   - Добавить обработку форм login/register в MedxController
   - Интегрировать с существующей системой auth

2. **База данных**
   - Создать таблицы для дисциплин
   - Создать таблицы для материалов
   - Создать таблицы для тестов

3. **Динамический контент**
   - Загружать дисциплины из БД
   - Загружать материалы из БД
   - Добавить поиск и фильтрацию

4. **Личный кабинет**
   - Страница профиля студента
   - Прогресс обучения
   - История тестов

5. **Тесты и практика**
   - Система тестирования
   - Клинические случаи
   - Отслеживание результатов

## Запуск проекта

```bash
# Очистить кеш
php artisan view:clear
php artisan cache:clear

# Запустить сервер
php artisan serve

# Открыть в браузере
http://localhost:8000/medx
```

## Troubleshooting

### Стили не применяются
1. Проверьте пути к CSS файлам
2. Очистите кеш браузера (Ctrl+Shift+R)
3. Проверьте консоль браузера на ошибки 404

### JavaScript не работает
1. Проверьте консоль браузера на ошибки
2. Убедитесь, что jQuery подключен (если используется)
3. Проверьте порядок подключения скриптов

### Blade ошибки
1. Проверьте правильность директив
2. Очистите кеш: `php artisan view:clear`
3. Проверьте закрытие всех тегов и директив

## Контакты и поддержка

Документация создана: 2026-03-13
Версия: 1.0.0
