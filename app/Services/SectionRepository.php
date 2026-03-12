<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/**
 * Репозиторий для работы с секциями страниц
 * 
 * Управляет получением и кешированием секций из таблицы page_sections.
 * Секции используются для вставки предверстанных блоков в контент страниц.
 */
class SectionRepository
{
    /**
     * Получение секции по GUID
     * 
     * Возвращает HTML, CSS и JS секции из таблицы page_sections.
     * HTML оборачивается в контейнер с data-section-guid для изоляции.
     * CSS автоматически получает префикс для изоляции стилей.
     * JS оборачивается в IIFE для изоляции переменных.
     * 
     * Кеширование на 1 час для производительности.
     * 
     * @param string $guid - GUID секции (8 символов)
     * @return array|null - ['html' => string, 'css' => string|null, 'js' => string|null] или null
     */
    public function getByGuid(string $guid): ?array
    {
        $cacheKey = "page_section_{$guid}";
        
        return Cache::remember($cacheKey, 3600, function () use ($guid) {
            $section = DB::selectOne("
                SELECT html, css, js
                FROM page_sections
                WHERE guid = ? AND active = 1
                LIMIT 1
            ", [$guid]);
            
            if (!$section) {
                return null;
            }
            
            return [
                'html' => $this->wrapHtml($section->html ?? '', $guid),
                'css' => $this->scopeCss($section->css ?? '', $guid),
                'js' => $this->scopeJs($section->js ?? '', $guid)
            ];
        });
    }
    
    /**
     * Оборачивание HTML в контейнер с data-атрибутом
     * 
     * Контейнер используется для изоляции CSS и JS секции.
     * 
     * @param string $html - Исходный HTML
     * @param string $guid - GUID секции
     * @return string - Обернутый HTML
     */
    private function wrapHtml(string $html, string $guid): string
    {
        if (empty($html)) {
            return '';
        }
        
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
     * Примечание: Это базовая реализация. Для production рекомендуется
     * использовать полноценный CSS-парсер для корректной обработки
     * медиа-запросов, @keyframes и других at-rules.
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
        $lines = explode("\n", $css);
        $scopedCss = [];
        $inComment = false;
        
        foreach ($lines as $line) {
            $trimmed = trim($line);
            
            // Обработка многострочных комментариев
            if (strpos($trimmed, '/*') !== false) {
                $inComment = true;
            }
            if ($inComment) {
                $scopedCss[] = $line;
                if (strpos($trimmed, '*/') !== false) {
                    $inComment = false;
                }
                continue;
            }
            
            // Пропускаем пустые строки и однострочные комментарии
            if (empty($trimmed) || strpos($trimmed, '//') === 0) {
                $scopedCss[] = $line;
                continue;
            }
            
            // Пропускаем at-rules (@media, @keyframes и т.д.)
            if (strpos($trimmed, '@') === 0) {
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
                // Строка с CSS-правилами или закрывающая скобка
                $scopedCss[] = $line;
            }
        }
        
        return implode("\n", $scopedCss);
    }
    
    /**
     * Изоляция JavaScript через IIFE
     * 
     * Оборачивает код в IIFE (Immediately Invoked Function Expression),
     * если он еще не обернут. Добавляет получение контейнера секции в начало.
     * 
     * Это обеспечивает:
     * - Изоляцию переменных (нет глобальных переменных)
     * - Привязку к контейнеру секции
     * - Защиту от конфликтов с другими скриптами
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
            "    \n" .
            "    // Получаем контейнер секции\n" .
            "    const section = document.querySelector('[data-section-guid=\"%s\"]');\n" .
            "    if (!section) {\n" .
            "        console.warn('Section container not found: %s');\n" .
            "        return;\n" .
            "    }\n" .
            "    \n" .
            "    // Код секции\n" .
            "%s\n" .
            "})();",
            $guid,
            $guid,
            $this->indentCode($js, 4)
        );
    }
    
    /**
     * Добавление отступов к коду
     * 
     * Вспомогательный метод для форматирования JavaScript-кода.
     * 
     * @param string $code - Исходный код
     * @param int $spaces - Количество пробелов для отступа
     * @return string - Код с отступами
     */
    private function indentCode(string $code, int $spaces): string
    {
        $indent = str_repeat(' ', $spaces);
        $lines = explode("\n", $code);
        
        return implode("\n", array_map(function($line) use ($indent) {
            return empty(trim($line)) ? $line : $indent . $line;
        }, $lines));
    }
    
    /**
     * Очистка кеша секции
     * 
     * Вызывается при обновлении секции в админ-панели.
     * Позволяет сразу увидеть изменения без ожидания истечения кеша.
     * 
     * @param string $guid - GUID секции
     * @return void
     */
    public function clearCache(string $guid): void
    {
        Cache::forget("page_section_{$guid}");
    }
    
    /**
     * Очистка кеша всех секций
     * 
     * Используется при массовых обновлениях секций.
     * 
     * @return void
     */
    public function clearAllCache(): void
    {
        // Получаем все GUID секций
        $guids = DB::select("SELECT guid FROM page_sections WHERE guid IS NOT NULL");
        
        foreach ($guids as $row) {
            $this->clearCache($row->guid);
        }
    }
}
