# 🏗️ Структура проекта MedX

## Обзор

Проект MedX построен на Laravel 12.x с использованием PHP 8.5 и следует стандартной структуре Laravel с дополнительными папками для legacy кода и документации.

## Корневая структура

```
MedX/
├── app/                      # Основной код приложения
├── bootstrap/                # Файлы загрузки Laravel
├── config/                   # Конфигурационные файлы
├── database/                 # Миграции, фабрики, сиды
├── docs/                     # 📚 Документация проекта
├── legacy/                   # Старый код (для справки)
├── public/                   # Публичные файлы (точка входа)
├── resources/                # Views, CSS, JS (исходники)
├── routes/                   # Маршруты приложения
├── storage/                  # Логи, кэш, загрузки
├── tests/                    # Тесты
├── vendor/                   # Зависимости Composer
├── .env                      # Переменные окружения
├── composer.json             # Зависимости PHP
├── artisan                   # CLI Laravel
└── README.md                 # Главный README
```

## Детальная структура

### 📁 app/

Основной код приложения

```
app/
├── Http/
│   └── Controllers/
│       ├── Controller.php              # Базовый контроллер
│       ├── ShowcaseController.php      # Главная страница
│       ├── LoginController.php         # Страница входа
│       └── MainShowcaseController.php  # База знаний
├── Models/
│   └── User.php                        # Модель пользователя
└── Providers/
    └── AppServiceProvider.php          # Сервис-провайдер
```

### 📁 resources/

Ресурсы приложения (views, assets)

```
resources/
└── views/
    ├── layouts/
    │   └── app.blade.php               # Основной layout
    ├── partials/
    │   ├── header.blade.php            # Хедер (showcase/login)
    │   ├── footer.blade.php            # Футер
    │   ├── main_header.blade.php       # Хедер (main_showcase)
    │   └── main_mobile_menu.blade.php  # Мобильное меню
    ├── showcase.blade.php              # Главная страница
    ├── login.blade.php                 # Страница входа
    └── main_showcase.blade.php         # База знаний
```

### 📁 public/

Публичные файлы (доступны через веб-сервер)

```
public/
├── assets/                   # Статические ресурсы из legacy
│   ├── medx/
│   │   ├── css/             # CSS файлы
│   │   ├── js/              # JavaScript файлы
│   │   └── img/             # Изображения
│   ├── admin/               # Админ-панель
│   ├── emart/               # E-commerce
│   └── favicon/             # Иконки
├── js/                      # JavaScript библиотеки
│   ├── jquery.min.js
│   ├── knockoutjs/
│   └── ...
├── site/                    # Модули из legacy
│   └── modules/
│       └── medx/
│           ├── showcase/    # Ресурсы главной страницы
│           ├── login/       # Ресурсы страницы входа
│           └── ...
├── index.php                # Точка входа Laravel
└── .htaccess                # Конфигурация Apache
```

### 📁 routes/

Маршруты приложения

```
routes/
├── web.php                  # Веб-маршруты
├── api.php                  # API маршруты (не используется)
└── console.php              # Консольные команды
```

**Содержимое web.php:**
```php
Route::get('/', [ShowcaseController::class, 'index'])->name('showcase');
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::get('/main_showcase', [MainShowcaseController::class, 'index'])->name('main_showcase');
```

### 📁 docs/

Документация проекта (эта папка!)

```
docs/
├── README.md                 # Главная страница документации
├── DOCS_INDEX.md            # Полная навигация
├── QUICK_START.md           # Быстрый старт
├── README_PHP85.md          # Полное руководство
├── SUMMARY.md               # Краткое резюме
├── MIGRATION_README.md      # Детали миграции
├── OSPANEL_SETUP.md         # Настройка OSPanel
├── PHP_8.5_UPGRADE.md       # Обновление PHP
├── CHANGELOG_PHP85.md       # История изменений
├── CONTRIBUTING.md          # Руководство по документации
└── PROJECT_STRUCTURE.md     # Этот файл
```

### 📁 legacy/

Старый код для справки (не используется в production)

```
legacy/
├── assets/                  # Статические файлы (скопированы в public/)
├── site/                    # Модули (скопированы в public/)
├── js/                      # JavaScript (скопирован в public/)
└── index.php                # Старая точка входа
```

### 📁 config/

Конфигурационные файлы Laravel

```
config/
├── app.php                  # Основная конфигурация
├── database.php             # Настройки БД
├── auth.php                 # Аутентификация
└── ...
```

### 📁 database/

База данных

```
database/
├── migrations/              # Миграции БД
├── factories/               # Фабрики для тестов
├── seeders/                 # Сиды (начальные данные)
└── database.sqlite          # SQLite БД (для разработки)
```

## Важные файлы

### composer.json
Зависимости PHP и скрипты

```json
{
    "require": {
        "php": "^8.5",
        "laravel/framework": "^12.0"
    }
}
```

### .env
Переменные окружения (не в git!)

```env
APP_NAME=MedX
APP_ENV=local
APP_KEY=...
APP_DEBUG=true
APP_URL=http://medx

DB_CONNECTION=sqlite
```

### .gitignore
Игнорируемые файлы

```
/vendor/
/node_modules/
.env
/storage/*.key
/public/storage
```

## Потоки данных

### Запрос к главной странице

```
1. Браузер → http://medx/
2. public/index.php (точка входа)
3. routes/web.php → Route::get('/')
4. ShowcaseController@index
5. resources/views/showcase.blade.php
6. Рендеринг с partials (header, footer)
7. HTML → Браузер
```

### Загрузка статических файлов

```
1. Браузер → http://medx/assets/medx/styles.css
2. public/assets/medx/styles.css
3. CSS → Браузер
```

## Соглашения

### Именование

- **Контроллеры:** `PascalCase` + `Controller` (ShowcaseController)
- **Views:** `snake_case.blade.php` (main_showcase.blade.php)
- **Routes:** `kebab-case` (/main-showcase)
- **CSS классы:** `kebab-case` (.main-header)
- **JavaScript:** `camelCase` (showOopsModal)

### Структура View

```blade
@extends('layouts.app')

@section('title', 'Заголовок страницы')

@section('styles')
    <link rel="stylesheet" href="{{ asset('path/to/style.css') }}">
@endsection

@section('content')
    @include('partials.header')
    
    <!-- Контент страницы -->
    
    @include('partials.footer')
@endsection

@section('scripts')
    <script>
        // JavaScript код
    </script>
@endsection
```

### Структура Controller

```php
<?php

namespace App\Http\Controllers;

class ShowcaseController extends Controller
{
    public function index()
    {
        return view('showcase');
    }
}
```

## Пути и URL

### Laravel Helpers

```php
// Путь к статическим файлам
asset('assets/medx/styles.css')
// → http://medx/assets/medx/styles.css

// URL маршрута
url('/login')
// → http://medx/login

// Именованный маршрут
route('showcase')
// → http://medx/
```

### В Blade шаблонах

```blade
<!-- Статические файлы -->
<link href="{{ asset('assets/medx/styles.css') }}" rel="stylesheet">
<img src="{{ asset('site/modules/medx/showcase/img/logo.png') }}" alt="Logo">

<!-- Ссылки -->
<a href="{{ url('/login') }}">Войти</a>
<a href="{{ route('showcase') }}">Главная</a>
```

## Расширение проекта

### Добавление новой страницы

1. **Создать контроллер:**
   ```bash
   php artisan make:controller NewPageController
   ```

2. **Создать view:**
   ```bash
   touch resources/views/new_page.blade.php
   ```

3. **Добавить маршрут:**
   ```php
   // routes/web.php
   Route::get('/new-page', [NewPageController::class, 'index'])->name('new.page');
   ```

4. **Скопировать статические файлы:**
   ```bash
   cp -r legacy/assets/new_page public/assets/
   ```

### Добавление новой документации

1. **Создать файл:**
   ```bash
   touch docs/NEW_FEATURE.md
   ```

2. **Обновить индекс:**
   - Добавить ссылку в `docs/DOCS_INDEX.md`
   - Обновить `docs/README.md` (если нужно)

3. **Следовать шаблону:**
   - См. `docs/CONTRIBUTING.md`

## Полезные команды

```bash
# Просмотр структуры
tree -L 2 -I 'vendor|node_modules'

# Размер папок
du -sh app/ resources/ public/ docs/

# Количество файлов
find app/ -type f | wc -l

# Поиск файлов
find . -name "*.blade.php"
```

## См. также

- [README.md](README.md) - Главная документация
- [CONTRIBUTING.md](CONTRIBUTING.md) - Руководство по документации
- [README_PHP85.md](README_PHP85.md) - Полное руководство

---

**Последнее обновление:** 18 марта 2024  
**Версия:** 1.0.0

**Вернуться:** [← Документация](README.md) | [← Главная](../README.md)
