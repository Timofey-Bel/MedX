<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/**
 * Команда для обработки CSS в существующих секциях
 * 
 * Проверяет CSS в таблице page_sections и добавляет префикс [data-section-guid]
 * к селекторам, если его еще нет.
 * 
 * Использование:
 * php artisan sections:wrap-css --dry-run  # Просмотр без изменений
 * php artisan sections:wrap-css            # Применить изменения
 */
class WrapSectionCss extends Command
{
    /**
     * Имя и сигнатура команды
     *
     * @var string
     */
    protected $signature = 'sections:wrap-css 
                            {--dry-run : Показать изменения без применения}
                            {--guid= : Обработать только конкретную секцию}';

    /**
     * Описание команды
     *
     * @var string
     */
    protected $description = 'Добавляет префикс [data-section-guid] к CSS селекторам в существующих секциях';

    /**
     * Выполнение команды
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $specificGuid = $this->option('guid');
        
        $this->info('🔍 Поиск секций с CSS...');
        
        // Получаем секции с CSS
        $query = "SELECT guid, name, css FROM page_sections WHERE css IS NOT NULL AND css != '' AND active = 1";
        $params = [];
        
        if ($specificGuid) {
            $query .= " AND guid = ?";
            $params[] = $specificGuid;
        }
        
        $sections = DB::select($query, $params);
        
        if (empty($sections)) {
            $this->warn('❌ Секции с CSS не найдены');
            return 0;
        }
        
        $this->info("✅ Найдено секций: " . count($sections));
        $this->newLine();
        
        $processed = 0;
        $skipped = 0;
        $errors = 0;
        
        foreach ($sections as $section) {
            $this->line("📦 Секция: {$section->name} (GUID: {$section->guid})");
            
            // Проверяем, нужна ли обработка
            $prefix = '[data-section-guid="' . $section->guid . '"]';
            
            if (strpos($section->css, $prefix) !== false) {
                $this->comment("   ⏭️  Пропущено: CSS уже содержит префикс");
                $skipped++;
                $this->newLine();
                continue;
            }
            
            // Обрабатываем CSS
            try {
                $wrappedCss = $this->wrapCss($section->css, $section->guid);
                
                if ($dryRun) {
                    $this->info("   📝 Изменения (dry-run):");
                    $this->showDiff($section->css, $wrappedCss);
                } else {
                    // Обновляем в БД
                    DB::update("UPDATE page_sections SET css = ? WHERE guid = ?", [
                        $wrappedCss,
                        $section->guid
                    ]);
                    
                    // Очищаем кеш
                    Cache::forget("page_section_{$section->guid}");
                    
                    $this->info("   ✅ Обновлено");
                }
                
                $processed++;
            } catch (\Exception $e) {
                $this->error("   ❌ Ошибка: " . $e->getMessage());
                $errors++;
            }
            
            $this->newLine();
        }
        
        // Итоги
        $this->newLine();
        $this->info('📊 Итоги:');
        $this->table(
            ['Статус', 'Количество'],
            [
                ['Обработано', $processed],
                ['Пропущено', $skipped],
                ['Ошибок', $errors],
            ]
        );
        
        if ($dryRun) {
            $this->warn('⚠️  Это был dry-run. Для применения изменений запустите без --dry-run');
        } else {
            $this->info('✅ Обработка завершена');
        }
        
        return 0;
    }
    
    /**
     * Обертывание CSS селекторов префиксом
     * 
     * @param string $css - Исходный CSS
     * @param string $guid - GUID секции
     * @return string - Обработанный CSS
     */
    private function wrapCss(string $css, string $guid): string
    {
        $prefix = '[data-section-guid="' . $guid . '"]';
        
        $lines = explode("\n", $css);
        $wrappedCss = [];
        $inComment = false;
        
        foreach ($lines as $line) {
            $trimmed = trim($line);
            
            // Обработка многострочных комментариев
            if (strpos($trimmed, '/*') !== false) {
                $inComment = true;
            }
            if ($inComment) {
                $wrappedCss[] = $line;
                if (strpos($trimmed, '*/') !== false) {
                    $inComment = false;
                }
                continue;
            }
            
            // Пропускаем пустые строки и однострочные комментарии
            if (empty($trimmed) || strpos($trimmed, '//') === 0) {
                $wrappedCss[] = $line;
                continue;
            }
            
            // Пропускаем at-rules (@media, @keyframes и т.д.)
            if (strpos($trimmed, '@') === 0) {
                $wrappedCss[] = $line;
                continue;
            }
            
            // Если строка содержит открывающую фигурную скобку - это селектор
            if (strpos($trimmed, '{') !== false) {
                $parts = explode('{', $trimmed, 2);
                $selector = trim($parts[0]);
                $rules = '{' . $parts[1];
                
                // Добавляем префикс к селектору
                $wrappedCss[] = $prefix . ' ' . $selector . ' ' . $rules;
            } else {
                // Строка с CSS-правилами или закрывающая скобка
                $wrappedCss[] = $line;
            }
        }
        
        return implode("\n", $wrappedCss);
    }
    
    /**
     * Показать разницу между оригинальным и обработанным CSS
     * 
     * @param string $original - Оригинальный CSS
     * @param string $wrapped - Обработанный CSS
     */
    private function showDiff(string $original, string $wrapped): void
    {
        $originalLines = explode("\n", $original);
        $wrappedLines = explode("\n", $wrapped);
        
        $maxLines = 10; // Показываем только первые 10 строк
        $shown = 0;
        
        for ($i = 0; $i < min(count($originalLines), count($wrappedLines)); $i++) {
            if ($shown >= $maxLines) {
                $this->comment("      ... (еще " . (count($wrappedLines) - $i) . " строк)");
                break;
            }
            
            if ($originalLines[$i] !== $wrappedLines[$i]) {
                $this->line("      <fg=red>- {$originalLines[$i]}</>");
                $this->line("      <fg=green>+ {$wrappedLines[$i]}</>");
                $shown++;
            }
        }
    }
}
