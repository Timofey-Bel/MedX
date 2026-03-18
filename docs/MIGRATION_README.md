# Миграция MedX из Legacy в Laravel

## Требования

- PHP 8.5 или выше
- Laravel 12.x
- Composer
- OSPanel (для локальной разработки)

## Что было сделано

Успешно перенесены страницы из legacy приложения в Laravel проект:

### Страницы
1. **Главная страница (/)** - showcase
2. **Страница входа (/login)** - login
3. **База знаний (/main_showcase)** - main_showcase

### Структура файлов

#### Views (resources/views/)
- `layouts/app.blade.php` - основной layout
- `showcase.blade.php` - главная страница
- `login.blade.php` - страница входа
- `main_showcase.blade.php` - страница базы знаний
- `partials/header.blade.php` - хедер для showcase и login
- `partials/footer.blade.php` - футер
- `partials/main_header.blade.php` - хедер для main_showcase
- `partials/main_mobile_menu.blade.php` - мобильное меню для main_showcase

#### Controllers (app/Http/Controllers/)
- `ShowcaseController.php` - контроллер главной страницы
- `LoginController.php` - контроллер страницы входа
- `MainShowcaseController.php` - контроллер базы знаний

#### Routes (routes/web.php)
```php
Route::get('/', [ShowcaseController::class, 'index'])->name('showcase');
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::get('/main_showcase', [MainShowcaseController::class, 'index'])->name('main_showcase');
```

#### Статические файлы (public/)
Скопированы из legacy:
- `public/assets/` - CSS, JS, изображения
- `public/js/` - JavaScript библиотеки
- `public/site/` - изображения и стили модулей

## Настройка OSPanel

### Для работы с доменом https://medx

1. Откройте OSPanel
2. Перейдите в настройки доменов
3. Добавьте домен `medx` с указанием на папку `public` вашего Laravel проекта
4. Перезапустите OSPanel

### Альтернативный способ (через hosts)

1. Откройте файл `C:\Windows\System32\drivers\etc\hosts` от имени администратора
2. Добавьте строку:
   ```
   127.0.0.1 medx
   ```
3. Настройте виртуальный хост в Apache/Nginx OSPanel

## Доступные URL

После настройки OSPanel:
- `https://medx` - главная страница (showcase)
- `https://medx/login` - страница входа
- `https://medx/main_showcase` - база знаний

## Что работает

✅ HTML структура всех страниц
✅ CSS стили (подключены из legacy)
✅ JavaScript функционал (модальные окна, меню, анимации)
✅ Изображения и иконки
✅ Навигация между страницами
✅ Адаптивный дизайн

## Примечания

- Все пути к статическим файлам используют Laravel helper `asset()`
- Все ссылки используют Laravel helper `url()`
- Шаблоны используют Blade синтаксис (@extends, @section, @include)
- JavaScript код встроен в секцию @section('scripts')
- CSS подключается через @section('styles')

## Следующие шаги

Для полной функциональности потребуется:
1. Настроить аутентификацию пользователей
2. Подключить базу данных
3. Реализовать API для динамического контента
4. Добавить остальные страницы (register, faq, about и т.д.)
