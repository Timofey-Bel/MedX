# Стандарты CSS и JavaScript для контентных блоков

## Проблема

Таблица `page_sections` содержит поля `css` и `js` с кодом для каждой секции. Необходимо обеспечить изоляцию стилей и скриптов, чтобы они не влияли на другие части страницы.

## Структура таблицы page_sections

```sql
CREATE TABLE page_sections (
    id INT PRIMARY KEY,
    guid VARCHAR(255) UNIQUE,
    name VARCHAR(255),
    content TEXT,           -- HTML-контент секции
    css TEXT,              -- CSS-стили для секции
    js TEXT,               -- JavaScript-код для секции
    active TINYINT(1),
    sort INT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## Цели

1. **Изоляция CSS**: Стили секции не должны влиять на другие элементы страницы
2. **Изоляция JS**: Скрипты секции не должны конфликтовать с глобальными скриптами
3. **Производительность**: Минимизировать дублирование CSS/JS при использовании одной секции несколько раз
4. **Безопасность**: Предотвратить XSS-атаки через пользовательский CSS/JS
5. **Удобство**: Простой синтаксис для редакторов контента

## Решение 1: CSS Scoping (РЕКОМЕНДУЕТСЯ)

### Принцип

Каждая секция оборачивается в уникальный контейнер с data-атрибутом, CSS использует этот атрибут для изоляции.

### Структура HTML

```html
<div class="content-section" data-section-guid="kawsfk9k">
    <!-- HTML-контент секции -->
    <div class="hero-banner">
        <h1>Заголовок</h1>
        <p>Описание</p>
    </div>
</div>
```

### Структура CSS в БД

**Правило**: Все селекторы ДОЛЖНЫ начинаться с `[data-section-guid="GUID"]`

```css
/* ПРАВИЛЬНО: Изолированные стили */
[data-section-guid="kawsfk9k"] .hero-banner {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 60px 20px;
    text-align: center;
}

[data-section-guid="kawsfk9k"] .hero-banner h1 {
    color: white;
    font-size: 48px;
    margin-bottom: 20px;
}

/* НЕПРАВИЛЬНО: Глобальные стили (будут отклонены валидатором) */
.hero-banner {
    background: red; /* ❌ Влияет на всю страницу */
}
```

### Структура JavaScript в БД

**Правило**: Весь код ДОЛЖЕН быть обернут в IIFE (Immediately Invoked Function Expression) с использованием data-атрибута для поиска элементов.

```javascript
(function() {
    'use strict';
    
    // Получаем контейнер секции
    const section = document.querySelector('[data-section-guid="kawsfk9k"]');
    if (!section) return;
    
    // Все манипуляции только внутри секции
    const button = section.querySelector('.hero-button');
    const counter = section.querySelector('.counter');
    
    if (button && counter) {
        let count = 0;
        button.addEventListener('click', function() {
            count++;
            counter.textContent = count;
        });
    }
})();
```

## Реализация в SectionRepository

### Обновленный метод getByGuid()

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class SectionRepository
{
    /**
     * Получение секции по GUID с CSS и JS
     * 
     * @param string $guid - GUID секции
     * @return array|null - ['html' => string, 'css' => string, 'js' => string]
     */
    public function getByGuid(string $guid): ?array
    {
        $cacheKey = "section_{$guid}";
        
        return Cache::remember($cacheKey, 3600, function () use ($guid) {
            $section = DB::selectOne("
                SELECT content, css, js
                FROM page_sections
                WHERE guid = ? AND active = 1
                LIMIT 1
            ", [$guid]);
            
            if (!$section) {
                return null;
            }
            
            return [
                'html' => $this->wrapHtml($section->content, $guid),
                'css' => $this->scopeCss($section->css ?? '', $guid),
                'js' => $this->scopeJs($section->js ?? '', $guid)
            ];
        });
    }
    
    /**
     * Оборачивание HTML в контейнер с data-атрибутом
     * 
     * @param string $html - Исходный HTML
     * @param string $guid - GUID секции
     * @return string - Обернутый HTML
     */
    private function wrapHtml(string $html, string $guid): string
    {
        return sprintf(
            '<div class="content-section" data-section-guid="%s">%s</div>',
            htmlspecialchars($guid, ENT_QUOTES, 'UTF-8'),
            $html
        );
    }
    
    /**
     * Изоляция CSS через data-атрибут
     * 
     * Автоматически добавляет префикс [data-section-guid="..."] ко всем селекторам.
     * Если CSS уже содержит правильный префикс - оставляет как есть.
     * 
     * @param string $css - Исходный CSS
     * @param string $guid - GUID секции
     * @return string - Изолированный CSS
     */
    private function scopeCss(string $css, string $guid): string
    {
        if (empty($css)) {
            return '';
        }
        
        $prefix = '[data-section-guid="' . $guid . '"]';
        
        // Если CSS уже содержит правильный префикс - возвращаем как есть
        if (strpos($css, $prefix) !== false) {
            return $css;
        }
        
        // Простая эвристика: добавляем префикс к каждому селектору
        // Это базовая реализация, для production нужен CSS-парсер
        $lines = explode("\n", $css);
        $scopedCss = [];
        
        foreach ($lines as $line) {
            $trimmed = trim($line);
            
            // Пропускаем пустые строки и комментарии
            if (empty($trimmed) || strpos($trimmed, '/*') === 0 || strpos($trimmed, '//') === 0) {
                $scopedCss[] = $line;
                continue;
            }
            
            // Если строка содержит открывающую фигурную скобку - это селектор
            if (strpos($trimmed, '{') !== false) {
                $parts = explode('{', $trimmed, 2);
                $selector = trim($parts[0]);
                $rules = '{' . $parts[1];
                
                // Добавляем префикс к селектору
                $scopedCss[] = $prefix . ' ' . $selector . ' ' . $rules;
            } else {
                $scopedCss[] = $line;
            }
        }
        
        return implode("\n", $scopedCss);
    }
    
    /**
     * Изоляция JavaScript через IIFE
     * 
     * Оборачивает код в IIFE, если он еще не обернут.
     * Добавляет получение контейнера секции в начало.
     * 
     * @param string $js - Исходный JavaScript
     * @param string $guid - GUID секции
     * @return string - Изолированный JavaScript
     */
    private function scopeJs(string $js, string $guid): string
    {
        if (empty($js)) {
            return '';
        }
        
        // Если код уже обернут в IIFE - возвращаем как есть
        if (preg_match('/^\s*\(function\s*\(\)\s*\{/s', $js)) {
            return $js;
        }
        
        // Оборачиваем в IIFE с получением контейнера секции
        return sprintf(
            "(function() {\n" .
            "    'use strict';\n" .
            "    const section = document.querySelector('[data-section-guid=\"%s\"]');\n" .
            "    if (!section) return;\n" .
            "    \n" .
            "    %s\n" .
            "})();",
            $guid,
            $js
        );
    }
}
```

## Обновленный ShortcodeParser

```php
<?php

namespace App\Services;

class ShortcodeParser
{
    private SectionRepository $sectionRepository;
    private array $collectedCss = [];
    private array $collectedJs = [];
    
    public function __construct(SectionRepository $sectionRepository)
    {
        $this->sectionRepository = $sectionRepository;
    }
    
    /**
     * Парсинг shortcode с сбором CSS и JS
     * 
     * @param string $content - Исходный контент
     * @return array - ['html' => string, 'css' => string, 'js' => string]
     */
    public function parse(string $content): array
    {
        $this->collectedCss = [];
        $this->collectedJs = [];
        
        $pattern = '/\[section\s+guid=["\']?([a-zA-Z0-9_-]+)["\']?\]/';
        
        $html = preg_replace_callback($pattern, function ($matches) {
            $guid = $matches[1];
            
            $section = $this->sectionRepository->getByGuid($guid);
            
            if ($section === null) {
                return "<!-- Section not found: {$guid} -->";
            }
            
            // Собираем CSS и JS (дедупликация по GUID)
            if (!empty($section['css'])) {
                $this->collectedCss[$guid] = $section['css'];
            }
            
            if (!empty($section['js'])) {
                $this->collectedJs[$guid] = $section['js'];
            }
            
            return $section['html'];
        }, $content);
        
        return [
            'html' => $html,
            'css' => implode("\n\n", $this->collectedCss),
            'js' => implode("\n\n", $this->collectedJs)
        ];
    }
}
```

## Обновленный PageController

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
        
        // Парсим shortcode и получаем HTML, CSS, JS
        $parsed = $this->shortcodeParser->parse($page->content);
        
        return view('page.show', [
            'page' => $page,
            'title' => $page->title ?: $page->name,
            'content' => $parsed['html'],
            'sectionCss' => $parsed['css'],
            'sectionJs' => $parsed['js'],
            'cart' => $cart,
            'favorites' => $favorites
        ]);
    }
}
```

## Обновленный шаблон page/show.blade.php

```blade
{{-- Конвертировано из legacy: site/modules/sfera/page/page.tpl --}}
@extends('layouts.app')

@section('title', $title ?? 'Творческий Центр СФЕРА')

@push('styles')
    <link rel="stylesheet" href="/assets/sfera/css/page.css">
    
    @if(!empty($sectionCss))
    <style>
        /* Стили контентных секций */
        {!! $sectionCss !!}
    </style>
    @endif
@endpush

@section('content')
<main class="main-content page-content">
    <div class="container">
        {!! $content !!}
    </div>
</main>
@endsection

@push('scripts')
    @if(!empty($sectionJs))
    <script>
        // JavaScript контентных секций
        {!! $sectionJs !!}
    </script>
    @endif
@endpush
```

## Best Practices для редакторов контента

### CSS Guidelines

#### ✅ ПРАВИЛЬНО

```css
/* Используйте data-атрибут для изоляции */
[data-section-guid="my-section"] .button {
    background: blue;
    color: white;
}

[data-section-guid="my-section"] .button:hover {
    background: darkblue;
}

/* Можно использовать вложенность */
[data-section-guid="my-section"] .container .item {
    margin: 10px;
}
```

#### ❌ НЕПРАВИЛЬНО

```css
/* Глобальные селекторы - влияют на всю страницу */
.button {
    background: blue; /* ❌ */
}

/* ID-селекторы - могут конфликтовать */
#my-button {
    color: red; /* ❌ */
}

/* !important - избегайте если возможно */
[data-section-guid="my-section"] .button {
    background: blue !important; /* ⚠️ Только в крайнем случае */
}
```

### JavaScript Guidelines

#### ✅ ПРАВИЛЬНО

```javascript
(function() {
    'use strict';
    
    const section = document.querySelector('[data-section-guid="my-section"]');
    if (!section) return;
    
    // Все элементы ищем внутри секции
    const button = section.querySelector('.my-button');
    const counter = section.querySelector('.counter');
    
    // Локальные переменные
    let count = 0;
    
    // Event listeners только на элементах секции
    if (button) {
        button.addEventListener('click', function() {
            count++;
            if (counter) {
                counter.textContent = count;
            }
        });
    }
})();
```

#### ❌ НЕПРАВИЛЬНО

```javascript
// Глобальные переменные - конфликтуют с другими скриптами
var count = 0; // ❌

// Поиск элементов без привязки к секции
const button = document.querySelector('.my-button'); // ❌

// Изменение глобальных объектов
window.myFunction = function() { }; // ❌

// Без IIFE - код выполняется в глобальной области
const section = document.querySelector('[data-section-guid="my-section"]');
// ❌ Нет изоляции
```

## Валидация CSS и JS (для админ-панели)

### CSS Validator

```php
<?php

namespace App\Services;

class SectionCssValidator
{
    /**
     * Валидация CSS секции
     * 
     * Проверяет, что все селекторы начинаются с data-атрибута.
     * 
     * @param string $css - CSS-код
     * @param string $guid - GUID секции
     * @return array - ['valid' => bool, 'errors' => array]
     */
    public function validate(string $css, string $guid): array
    {
        $errors = [];
        $prefix = '[data-section-guid="' . $guid . '"]';
        
        // Простая проверка: ищем селекторы без префикса
        $lines = explode("\n", $css);
        $lineNumber = 0;
        
        foreach ($lines as $line) {
            $lineNumber++;
            $trimmed = trim($line);
            
            // Пропускаем пустые строки и комментарии
            if (empty($trimmed) || strpos($trimmed, '/*') === 0) {
                continue;
            }
            
            // Если строка содержит открывающую фигурную скобку - это селектор
            if (strpos($trimmed, '{') !== false) {
                $parts = explode('{', $trimmed, 2);
                $selector = trim($parts[0]);
                
                // Проверяем наличие префикса
                if (strpos($selector, $prefix) === false) {
                    $errors[] = "Строка {$lineNumber}: Селектор '{$selector}' должен начинаться с {$prefix}";
                }
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}
```

### JavaScript Validator

```php
<?php

namespace App\Services;

class SectionJsValidator
{
    /**
     * Валидация JavaScript секции
     * 
     * Проверяет базовые правила безопасности.
     * 
     * @param string $js - JavaScript-код
     * @return array - ['valid' => bool, 'errors' => array]
     */
    public function validate(string $js): array
    {
        $errors = [];
        
        // Проверка на опасные конструкции
        $dangerousPatterns = [
            '/eval\s*\(/' => 'Использование eval() запрещено',
            '/document\.write\s*\(/' => 'Использование document.write() запрещено',
            '/innerHTML\s*=/' => 'Прямое присваивание innerHTML небезопасно, используйте textContent',
            '/window\.\w+\s*=/' => 'Изменение глобального объекта window запрещено',
        ];
        
        foreach ($dangerousPatterns as $pattern => $message) {
            if (preg_match($pattern, $js)) {
                $errors[] = $message;
            }
        }
        
        // Проверка на IIFE
        if (!preg_match('/^\s*\(function\s*\(\)\s*\{/s', $js)) {
            $errors[] = 'JavaScript должен быть обернут в IIFE: (function() { ... })();';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}
```

## Производительность

### Дедупликация CSS/JS

Если одна секция используется несколько раз на странице, CSS и JS включаются только один раз:

```php
// В ShortcodeParser::parse()
if (!empty($section['css'])) {
    // Используем GUID как ключ - автоматическая дедупликация
    $this->collectedCss[$guid] = $section['css'];
}
```

### Минификация (опционально)

Для production можно добавить минификацию:

```php
private function minifyCss(string $css): string
{
    // Удаляем комментарии
    $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
    
    // Удаляем пробелы
    $css = str_replace(["\r\n", "\r", "\n", "\t", '  ', '    ', '    '], '', $css);
    
    return $css;
}

private function minifyJs(string $js): string
{
    // Для production используйте JShrink или аналогичную библиотеку
    // Здесь простая реализация
    $js = preg_replace('/\/\*[\s\S]*?\*\/|\/\/.*$/m', '', $js);
    $js = preg_replace('/\s+/', ' ', $js);
    
    return $js;
}
```

## Альтернативное решение: Shadow DOM (для будущего)

Для полной изоляции можно использовать Shadow DOM:

```javascript
(function() {
    'use strict';
    
    const section = document.querySelector('[data-section-guid="my-section"]');
    if (!section) return;
    
    // Создаем Shadow DOM
    const shadow = section.attachShadow({mode: 'open'});
    
    // Добавляем стили в Shadow DOM
    const style = document.createElement('style');
    style.textContent = `
        .button {
            background: blue;
            color: white;
        }
    `;
    shadow.appendChild(style);
    
    // Добавляем контент в Shadow DOM
    const content = document.createElement('div');
    content.innerHTML = section.innerHTML;
    shadow.appendChild(content);
    
    // Очищаем оригинальный контент
    section.innerHTML = '';
})();
```

**Преимущества Shadow DOM:**
- Полная изоляция CSS
- Нет конфликтов селекторов
- Нативная поддержка браузерами

**Недостатки:**
- Сложнее для редакторов контента
- Проблемы с SEO (контент в Shadow DOM)
- Ограниченная поддержка старых браузеров

## Заключение

Рекомендуемый подход:
1. **CSS**: Автоматический scoping через `[data-section-guid]`
2. **JavaScript**: IIFE с привязкой к контейнеру секции
3. **Валидация**: Проверка в админ-панели перед сохранением
4. **Дедупликация**: Автоматическая через использование GUID как ключа

Этот подход обеспечивает баланс между изоляцией, производительностью и удобством использования.
