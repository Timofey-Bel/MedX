# План системы контентных блоков (Content Blocks System)

## Проблема

В legacy системе использовались Smarty-плагины для вставки предверстанных секций:
```
~~mod path="sfera/" mod_name="section" guid="kawsfk9k"~
```

Эти плагины:
1. Парсились Smarty при рендеринге страницы
2. По `guid` доставали данные из таблицы `sections`
3. Подставляли HTML-контент в место вызова
4. Позволяли вставлять блоки в любом порядке

## Цель

Создать аналогичную систему для Laravel, которая:
- Работает без Smarty
- Использует существующую таблицу `sections`
- Позволяет вставлять блоки через простой синтаксис
- Поддерживает любой порядок блоков
- Легко расширяется новыми типами блоков

## Анализ таблицы sections

Нужно изучить структуру таблицы `sections`:
```sql
DESCRIBE sections;
```

Предполагаемая структура:
- `id` - уникальный идентификатор
- `guid` - GUID для идентификации блока
- `name` - название секции
- `content` - HTML-контент секции
- `active` - активна ли секция
- `sort` - порядок сортировки
- `created_at`, `updated_at` - временные метки

## Варианты решения

### Вариант 1: Shortcode-система (РЕКОМЕНДУЕТСЯ)

Использовать shortcode-синтаксис, похожий на WordPress:

**Синтаксис в контенте страницы:**
```
[section guid="kawsfk9k"]
[section guid="another-guid"]
```

**Преимущества:**
- Простой и понятный синтаксис
- Легко парсится регулярными выражениями
- Не конфликтует с HTML
- Можно передавать параметры

**Реализация:**
1. Создать класс `ShortcodeParser`
2. Парсить контент страницы перед отображением
3. Заменять shortcode на реальный HTML из таблицы `sections`

### Вариант 2: Blade-компоненты

Использовать нативные Blade-компоненты:

**Синтаксис в контенте страницы:**
```blade
@section('kawsfk9k')
@section('another-guid')
```

**Преимущества:**
- Нативный Laravel-подход
- Поддержка IDE
- Типизация параметров

**Недостатки:**
- Контент в БД должен быть валидным Blade
- Сложнее для редакторов контента
- Требует компиляции Blade

### Вариант 3: Markdown-расширения

Использовать Markdown с кастомными расширениями:

**Синтаксис:**
```markdown
{{section:kawsfk9k}}
{{section:another-guid}}
```

**Преимущества:**
- Markdown для основного контента
- Кастомные расширения для блоков

**Недостатки:**
- Требует парсер Markdown
- Дополнительная зависимость

## Рекомендуемое решение: Shortcode-система

### Архитектура

```
┌─────────────────────────────────────────────────────────────┐
│                     PageController                          │
│  1. Получает страницу из БД (pages.content)                │
│  2. Передает контент в ShortcodeParser                      │
│  3. Получает обработанный HTML                              │
│  4. Отдает в view                                           │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                    ShortcodeParser                          │
│  1. Находит все [section guid="..."] в контенте             │
│  2. Для каждого shortcode:                                  │
│     - Извлекает guid                                        │
│     - Вызывает SectionRepository::getByGuid()               │
│     - Заменяет shortcode на HTML из sections.content        │
│  3. Возвращает обработанный HTML                            │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                   SectionRepository                         │
│  1. Кеширует секции по GUID                                 │
│  2. SQL: SELECT content FROM sections                       │
│          WHERE guid = ? AND active = 1                      │
│  3. Возвращает HTML-контент                                 │
└─────────────────────────────────────────────────────────────┘
```

### Структура файлов

```
app/
├── Services/
│   ├── ShortcodeParser.php          # Парсер shortcode
│   └── SectionRepository.php        # Репозиторий для sections
├── Http/
│   └── Controllers/
│       └── PageController.php       # Обновленный контроллер
resources/
└── views/
    └── page/
        └── show.blade.php           # Шаблон страницы
```

### Реализация

#### 1. SectionRepository (app/Services/SectionRepository.php)

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class SectionRepository
{
    /**
     * Получение секции по GUID
     * 
     * Кеширование на 1 час для производительности.
     * Секции редко меняются, поэтому кеш безопасен.
     * 
     * @param string $guid - GUID секции
     * @return string|null - HTML-контент секции или null
     */
    public function getByGuid(string $guid): ?string
    {
        $cacheKey = "section_{$guid}";
        
        return Cache::remember($cacheKey, 3600, function () use ($guid) {
            $section = DB::selectOne("
                SELECT content
                FROM sections
                WHERE guid = ? AND active = 1
                LIMIT 1
            ", [$guid]);
            
            return $section ? $section->content : null;
        });
    }
    
    /**
     * Очистка кеша секции
     * 
     * Вызывается при обновлении секции в админ-панели
     * 
     * @param string $guid - GUID секции
     */
    public function clearCache(string $guid): void
    {
        Cache::forget("section_{$guid}");
    }
}
```

#### 2. ShortcodeParser (app/Services/ShortcodeParser.php)

```php
<?php

namespace App\Services;

class ShortcodeParser
{
    private SectionRepository $sectionRepository;
    
    public function __construct(SectionRepository $sectionRepository)
    {
        $this->sectionRepository = $sectionRepository;
    }
    
    /**
     * Парсинг shortcode в контенте
     * 
     * Находит все [section guid="..."] и заменяет на HTML из БД.
     * Поддерживает несколько форматов:
     * - [section guid="kawsfk9k"]
     * - [section guid='kawsfk9k']
     * - [section guid=kawsfk9k]
     * 
     * @param string $content - Исходный контент с shortcode
     * @return string - Обработанный HTML
     */
    public function parse(string $content): string
    {
        // Регулярное выражение для поиска [section guid="..."]
        $pattern = '/\[section\s+guid=["\']?([a-zA-Z0-9_-]+)["\']?\]/';
        
        return preg_replace_callback($pattern, function ($matches) {
            $guid = $matches[1];
            
            // Получаем HTML секции из БД
            $sectionHtml = $this->sectionRepository->getByGuid($guid);
            
            // Если секция не найдена - возвращаем комментарий для отладки
            if ($sectionHtml === null) {
                return "<!-- Section not found: {$guid} -->";
            }
            
            return $sectionHtml;
        }, $content);
    }
}
```

#### 3. Обновленный PageController

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\ShortcodeParser;

class PageController extends Controller
{
    private ShortcodeParser $shortcodeParser;
    
    public function __construct(ShortcodeParser $shortcodeParser)
    {
        $this->shortcodeParser = $shortcodeParser;
    }
    
    public function show($slug)
    {
        // ... существующий код получения страницы ...
        
        // НОВОЕ: Парсим shortcode в контенте
        $page->content = $this->shortcodeParser->parse($page->content);
        
        return view('page.show', [
            'page' => $page,
            'title' => $page->title ?: $page->name,
            'cart' => $cart,
            'favorites' => $favorites
        ]);
    }
}
```

### Синтаксис shortcode

#### Базовый синтаксис
```
[section guid="kawsfk9k"]
```

#### Расширенный синтаксис (для будущего)
```
[section guid="kawsfk9k" class="my-custom-class"]
[section guid="kawsfk9k" style="margin-top: 20px"]
```

### Миграция legacy контента

#### Скрипт миграции (artisan command)

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateLegacyShortcodes extends Command
{
    protected $signature = 'migrate:shortcodes';
    protected $description = 'Migrate legacy Smarty shortcodes to new format';
    
    public function handle()
    {
        $this->info('Migrating legacy shortcodes...');
        
        // Получаем все страницы с legacy shortcode
        $pages = DB::select("
            SELECT id, content
            FROM pages
            WHERE content LIKE '%~~mod%'
        ");
        
        foreach ($pages as $page) {
            $newContent = $this->convertShortcodes($page->content);
            
            DB::update("
                UPDATE pages
                SET content = ?
                WHERE id = ?
            ", [$newContent, $page->id]);
            
            $this->info("Migrated page ID: {$page->id}");
        }
        
        $this->info('Migration complete!');
    }
    
    private function convertShortcodes(string $content): string
    {
        // Конвертируем ~~mod path="sfera/" mod_name="section" guid="kawsfk9k"~
        // в [section guid="kawsfk9k"]
        
        $pattern = '/~~mod\s+path="[^"]*"\s+mod_name="section"\s+guid="([^"]+)"~/';
        
        return preg_replace($pattern, '[section guid="$1"]', $content);
    }
}
```

### Преимущества решения

1. **Простота**: Shortcode легко понять и использовать
2. **Производительность**: Кеширование секций на 1 час
3. **Гибкость**: Легко добавить новые типы shortcode
4. **Обратная совместимость**: Скрипт миграции legacy контента
5. **Отладка**: Комментарии для несуществующих секций
6. **Безопасность**: Валидация GUID через регулярное выражение

### Расширения (будущее)

#### Поддержка параметров
```php
// В ShortcodeParser::parse()
$pattern = '/\[section\s+guid=["\']?([a-zA-Z0-9_-]+)["\']?(?:\s+([^\]]+))?\]/';

return preg_replace_callback($pattern, function ($matches) {
    $guid = $matches[1];
    $params = isset($matches[2]) ? $this->parseParams($matches[2]) : [];
    
    $sectionHtml = $this->sectionRepository->getByGuid($guid);
    
    // Применяем параметры (class, style и т.д.)
    if (!empty($params['class'])) {
        $sectionHtml = "<div class=\"{$params['class']}\">{$sectionHtml}</div>";
    }
    
    return $sectionHtml;
}, $content);
```

#### Поддержка других типов блоков
```
[product id="00-00006779"]
[banner id="main-promo"]
[carousel type="bestsellers"]
```

### Тестирование

```php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ShortcodeParser;
use App\Services\SectionRepository;

class ShortcodeParserTest extends TestCase
{
    public function test_parse_single_section()
    {
        $repository = $this->mock(SectionRepository::class);
        $repository->shouldReceive('getByGuid')
            ->with('test-guid')
            ->andReturn('<div>Test Section</div>');
        
        $parser = new ShortcodeParser($repository);
        
        $content = 'Before [section guid="test-guid"] After';
        $result = $parser->parse($content);
        
        $this->assertEquals('Before <div>Test Section</div> After', $result);
    }
    
    public function test_parse_multiple_sections()
    {
        $repository = $this->mock(SectionRepository::class);
        $repository->shouldReceive('getByGuid')
            ->with('guid1')
            ->andReturn('<div>Section 1</div>');
        $repository->shouldReceive('getByGuid')
            ->with('guid2')
            ->andReturn('<div>Section 2</div>');
        
        $parser = new ShortcodeParser($repository);
        
        $content = '[section guid="guid1"] Middle [section guid="guid2"]';
        $result = $parser->parse($content);
        
        $this->assertEquals('<div>Section 1</div> Middle <div>Section 2</div>', $result);
    }
    
    public function test_parse_missing_section()
    {
        $repository = $this->mock(SectionRepository::class);
        $repository->shouldReceive('getByGuid')
            ->with('missing')
            ->andReturn(null);
        
        $parser = new ShortcodeParser($repository);
        
        $content = '[section guid="missing"]';
        $result = $parser->parse($content);
        
        $this->assertStringContainsString('<!-- Section not found: missing -->', $result);
    }
}
```

## План реализации

### Этап 1: Изучение структуры БД
- [ ] Выполнить `DESCRIBE sections`
- [ ] Проверить наличие поля `guid`
- [ ] Проверить наличие поля `active`
- [ ] Изучить примеры данных в таблице

### Этап 2: Создание базовых классов
- [ ] Создать `SectionRepository`
- [ ] Создать `ShortcodeParser`
- [ ] Написать юнит-тесты

### Этап 3: Интеграция в PageController
- [ ] Обновить `PageController::show()`
- [ ] Добавить dependency injection для `ShortcodeParser`
- [ ] Протестировать на тестовой странице

### Этап 4: Миграция legacy контента
- [ ] Создать artisan command `migrate:shortcodes`
- [ ] Протестировать на копии БД
- [ ] Выполнить миграцию на production

### Этап 5: Документация
- [ ] Обновить документацию для редакторов контента
- [ ] Создать примеры использования
- [ ] Добавить в админ-панель подсказки

## Альтернативные подходы

### Если нужна поддержка Blade в секциях

Если секции должны содержать Blade-код (переменные, циклы и т.д.):

```php
public function parse(string $content): string
{
    $pattern = '/\[section\s+guid=["\']?([a-zA-Z0-9_-]+)["\']?\]/';
    
    return preg_replace_callback($pattern, function ($matches) use ($data) {
        $guid = $matches[1];
        $sectionContent = $this->sectionRepository->getByGuid($guid);
        
        if ($sectionContent === null) {
            return "<!-- Section not found: {$guid} -->";
        }
        
        // Компилируем Blade-шаблон
        return view(['template' => $sectionContent], $data)->render();
    }, $content);
}
```

### Если нужна поддержка вложенных shortcode

```php
public function parse(string $content, int $depth = 0): string
{
    if ($depth > 5) {
        return $content; // Защита от бесконечной рекурсии
    }
    
    $pattern = '/\[section\s+guid=["\']?([a-zA-Z0-9_-]+)["\']?\]/';
    
    $content = preg_replace_callback($pattern, function ($matches) use ($depth) {
        $guid = $matches[1];
        $sectionHtml = $this->sectionRepository->getByGuid($guid);
        
        if ($sectionHtml === null) {
            return "<!-- Section not found: {$guid} -->";
        }
        
        // Рекурсивно парсим вложенные shortcode
        return $this->parse($sectionHtml, $depth + 1);
    }, $content);
    
    return $content;
}
```

## Вопросы для уточнения

1. Какая структура таблицы `sections`?
2. Нужна ли поддержка Blade-кода в секциях?
3. Нужна ли поддержка параметров (class, style)?
4. Нужна ли поддержка вложенных shortcode?
5. Сколько страниц содержат legacy shortcode?
6. Есть ли другие типы блоков кроме `section`?

## Заключение

Рекомендуемое решение - shortcode-система с кешированием. Она проста, производительна и легко расширяется. Миграция legacy контента автоматизирована через artisan command.
