# Использование системы контентных блоков

## Быстрый старт

### 1. Вставка секции в контент страницы

В админ-панели при редактировании страницы используйте shortcode:

```
[section guid="kawsfk9k"]
```

Где `kawsfk9k` - это GUID секции из таблицы `page_sections`.

### 2. Создание новой секции

Добавьте запись в таблицу `page_sections`:

```sql
INSERT INTO page_sections (guid, name, slug, html, css, js, active)
VALUES (
    'test123',                          -- Уникальный GUID (8 символов)
    'Тестовая секция',                  -- Название
    'test-section',                     -- Slug
    '<div class="test">Контент</div>',  -- HTML
    '.test { color: red; }',            -- CSS (опционально)
    'console.log("test");',             -- JS (опционально)
    1                                   -- Активна
);
```

Затем в контенте страницы:

```
[section guid="test123"]
```

### 3. Миграция legacy контента

Если у вас есть страницы со старым синтаксисом:

```
~~mod path="sfera/" mod_name="section" guid="kawsfk9k"~
```

Выполните команду миграции:

```bash
# Просмотр изменений без применения
php artisan migrate:shortcodes --dry-run

# Применить миграцию
php artisan migrate:shortcodes

# Мигрировать только одну страницу
php artisan migrate:shortcodes --page-id=13
```

## Примеры использования

### Простая секция

**В БД (page_sections):**
```sql
guid: 'hero001'
html: '<div class="hero">
    <h1>Добро пожаловать!</h1>
    <p>Лучшие книги для детей</p>
</div>'
```

**В контенте страницы:**
```
<p>Вступительный текст</p>

[section guid="hero001"]

<p>Продолжение текста</p>
```

### Секция с CSS и JS

**В БД (page_sections):**
```sql
guid: 'slider1'
html: '<div class="custom-slider">
    <div class="slide">Слайд 1</div>
    <div class="slide">Слайд 2</div>
</div>'
css: '.custom-slider { width: 100%; }
.slide { padding: 20px; }'
js: 'document.querySelector(".custom-slider").addEventListener("click", function() {
    console.log("Slider clicked");
});'
```

**В контенте страницы:**
```
[section guid="slider1"]
```

CSS автоматически добавится в `<head>`, JS - перед `</body>`.

### Несколько секций подряд

```
[section guid="header1"]
[section guid="content1"]
[section guid="footer1"]
```

## Кеширование

Секции кешируются на 1 час для производительности.

### Очистка кеша одной секции

```php
use App\Services\SectionRepository;

$repository = app(SectionRepository::class);
$repository->clearCache('kawsfk9k');
```

### Очистка кеша всех секций

```php
use App\Services\SectionRepository;

$repository = app(SectionRepository::class);
$repository->clearAllCache();
```

## Отладка

Если секция не найдена, в HTML будет комментарий:

```html
<!-- Section not found: kawsfk9k -->
```

Проверьте:
1. Правильность GUID
2. Поле `active = 1` в таблице `page_sections`
3. Наличие записи в БД

## Расширение функционала

### Добавление параметров в shortcode

В будущем можно добавить поддержку параметров:

```
[section guid="kawsfk9k" class="my-class" style="margin-top: 20px"]
```

Для этого нужно обновить регулярное выражение в `ShortcodeParser::parse()`.

### Добавление других типов блоков

Можно добавить поддержку других shortcode:

```
[product id="00-00006779"]
[banner id="main-promo"]
[carousel type="bestsellers"]
```

Для этого создайте отдельные парсеры или расширьте `ShortcodeParser`.

## Безопасность

- GUID валидируется регулярным выражением (только буквы, цифры, дефис, подчеркивание)
- HTML из секций выводится через `{!! !!}` (без экранирования)
- Убедитесь, что только доверенные пользователи могут редактировать секции

## Производительность

- Секции кешируются на 1 час
- Парсинг shortcode выполняется один раз при загрузке страницы
- CSS и JS собираются в один тег для минимизации запросов

## Совместимость

Система полностью совместима с:
- Laravel 12
- PHP 8.5
- Blade шаблонами
- Существующими страницами

## Поддержка

При возникновении проблем:
1. Проверьте логи Laravel (`storage/logs/laravel.log`)
2. Проверьте структуру таблицы `page_sections`
3. Убедитесь, что кеш работает корректно
4. Проверьте права доступа к БД
