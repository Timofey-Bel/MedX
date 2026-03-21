# Работа с изображениями

## Структура

`public/images/` разделена на категории:
- `branding/` - логотипы
- `medical/` - медицинские иконки (38 файлов)
- `ui/` - UI элементы, векторы, кнопки
- `backgrounds/` - фоны
- `social/` - иконки соцсетей
- `main/` - изображения главной страницы (27 файлов)
- `icons/menu/` - иконки меню (8 файлов)
- `icons/settings/` - иконки настроек (6 файлов)
- `icons/tests/` - иконки тестов (2 файла)
- `pages/about/` - страница "О нас"
- `pages/register/` - регистрация
- `pages/footer/` - футер

## Использование

### Компонент
```blade
<x-img category="branding" src="logo-icon.svg" alt="Логотип" />
<x-img category="icons" subcategory="menu" src="home-icon.svg" alt="Главная" />
<x-img category="medical" src="heart-3-1@2x.png" alt="Сердце" class="w-16" />
```

### Helper
```blade
{{ App\Helpers\ImageHelper::branding('logo-icon.svg') }}
{{ App\Helpers\ImageHelper::medical('heart-3-1@2x.png') }}
{{ App\Helpers\ImageHelper::icon('menu', 'home-icon.svg') }}
{{ App\Helpers\ImageHelper::page('about', 'about-bg.png') }}
```

### Asset
```blade
{{ asset('images/branding/logo-icon.svg') }}
```
