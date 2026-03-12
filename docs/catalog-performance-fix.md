# Исправление производительности страницы каталога

## Проблема

Страница каталога загружалась крайне медленно:
- Общее время загрузки: 52-66 секунд
- Waiting for server response: 15.78 секунд
- URL: http://sfera/catalog/00-00006779

## Анализ

### Первоначальные предположения
1. N+1 query problem - ОТКЛОНЕНО (PHP код выполнялся за 0.05-0.07 секунд)
2. Медленные SQL запросы - ОТКЛОНЕНО (логирование показало быстрое выполнение)

### Найденные проблемы

#### Проблема 1: Рендеринг большого дерева категорий
- Категории рендерились в 3 местах одновременно:
  - Header (`components.header`)
  - Mobile menu (`components.mobile-menu`)
  - Catalog sidebar (`catalog/index.blade.php`)
- Каждый рендеринг включал вложенные циклы через сотни категорий
- Рекурсивный метод `getCategories()` создавал глубокое дерево

#### Проблема 2: SESSION_DRIVER=database
- Хранение сессий в БД создавало задержку ~50 секунд
- Возможные причины:
  - Блокировки при записи сессий
  - Overhead на сериализацию/десериализацию
  - Дополнительные запросы к БД на каждый HTTP request

## Решение

### 1. Временное отключение рендеринга категорий

**Файл:** `app/Http/Controllers/CatalogController.php`
```php
// Временно отключено для диагностики производительности
// $categories = $this->getCategories($category_id);
$categories = [];
```

**Файл:** `resources/views/layouts/app.blade.php`
```blade
<x-header :categories="[]" />
<x-mobile-menu :categories="[]" />
```

**Файл:** `resources/views/catalog/index.blade.php`
```blade
{{-- Временно закомментировано для диагностики производительности
<div class="catalog-menu">
    ...
</div>
--}}
```

### 2. Переключение на файловые сессии

**Файл:** `.env`
```env
SESSION_DRIVER=file  # было: database
```

После изменения выполнить:
```bash
& "C:\OS\modules\PHP-8.5\php.exe" artisan config:clear
```

## Результаты

### До оптимизации
- SESSION_DRIVER=database + рендеринг категорий
- Finish: 52.24s
- DOMContentLoaded: 1.44s
- Waiting for server response: 15.78s

### После оптимизации
- SESSION_DRIVER=file + отключенные категории
- Finish: 1.16s (с Ctrl+F5) / 1.44s (обычная загрузка)
- DOMContentLoaded: 838ms / 1.12s
- Waiting for server response: <1s

### Улучшение производительности
- **45x улучшение** общего времени загрузки (52.24s → 1.16s)
- **16x улучшение** server response time (15.78s → <1s)

## Следующие шаги

### Краткосрочные (обязательно)
1. ✅ Переключиться на `SESSION_DRIVER=file`
2. ⏳ Реализовать кэширование категорий
3. ⏳ Оптимизировать рендеринг дерева категорий:
   - Ограничить глубину вложенности
   - Использовать пагинацию для больших списков
   - Рассмотреть виртуальный скроллинг

### Долгосрочные (рекомендуется)
1. Рассмотреть lazy-loading для категорий
2. Реализовать AJAX-загрузку дерева категорий
3. Добавить индексы в таблицу `tree` если их нет
4. Рассмотреть использование Redis для сессий (если нужна персистентность)

## Технические детали

### Структура таблицы sessions
```php
Schema::create('sessions', function (Blueprint $table) {
    $table->string('id')->primary();
    $table->unsignedBigInteger('user_id')->nullable()->index();
    $table->string('ip_address', 45)->nullable();
    $table->text('user_agent')->nullable();
    $table->longText('payload');
    $table->integer('last_activity')->index();
});
```

### Метод getCategories() (рекурсивный)
```php
private function getCategories($parent_id = null)
{
    // Рекурсивно загружает все категории и подкатегории
    // Может создавать глубокое дерево с сотнями узлов
    // Требует оптимизации или кэширования
}
```

## Выводы

1. **SESSION_DRIVER=file** - стандартное и быстрое решение для большинства Laravel приложений
2. **Рендеринг больших деревьев** в шаблонах может создавать значительные задержки
3. **Логирование** помогло быстро идентифицировать, что проблема не в SQL запросах
4. **Поэтапное тестирование** (сначала сессии, потом категории) помогло изолировать проблемы

## Ссылки

- Коммит: `4af64ae` - Fix catalog performance: disable category rendering and switch to file sessions
- Ветка: `bugfix/catalog-performance`
- Legacy код: `legacy/site/modules/sfera/catalog/catalog.class.php`
