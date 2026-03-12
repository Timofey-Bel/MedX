<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Команда для миграции legacy Smarty shortcode в новый формат
 * 
 * Конвертирует старый синтаксис:
 * ~~mod path="sfera/" mod_name="section" guid="kawsfk9k"~
 * 
 * В новый синтаксис:
 * [section guid="kawsfk9k"]
 * 
 * Использование:
 * php artisan migrate:shortcodes
 * php artisan migrate:shortcodes --dry-run  # Просмотр без изменений
 */
class MigrateLegacyShortcodes extends Command
{
    /**
     * Имя и сигнатура команды
     *
     * @var string
     */
    protected $signature = 'migrate:shortcodes 
                            {--dry-run : Показать изменения без применения}
                            {--page-id= : Мигрировать только конкретную страницу}';

    /**
     * Описание команды
     *
     * @var string
     */
    protected $description = 'Migrate legacy Smarty shortcodes to new format';

    /**
     * Выполнение команды
     *
     * @return int
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $pageId = $this->option('page-id');
        
        $this->info('🔄 Migrating legacy shortcodes...');
        $this->newLine();
        
        if ($dryRun) {
            $this->warn('⚠️  DRY RUN MODE - No changes will be made');
            $this->newLine();
        }
        
        // Получаем все страницы с legacy shortcode
        $query = "
            SELECT id, name, title, content
            FROM pages
            WHERE content LIKE '%~~mod%'
        ";
        
        if ($pageId) {
            $query .= " AND id = " . intval($pageId);
        }
        
        $pages = DB::select($query);
        
        if (empty($pages)) {
            $this->info('✅ No pages with legacy shortcodes found');
            return Command::SUCCESS;
        }
        
        $this->info("Found " . count($pages) . " page(s) with legacy shortcodes");
        $this->newLine();
        
        $migratedCount = 0;
        $errorCount = 0;
        
        foreach ($pages as $page) {
            try {
                $newContent = $this->convertShortcodes($page->content);
                
                // Проверяем, были ли изменения
                if ($newContent === $page->content) {
                    $this->line("⏭️  Page ID {$page->id} ({$page->name}): No changes needed");
                    continue;
                }
                
                // Показываем diff
                $this->showDiff($page, $newContent);
                
                if (!$dryRun) {
                    DB::update("
                        UPDATE pages
                        SET content = ?
                        WHERE id = ?
                    ", [$newContent, $page->id]);
                    
                    $this->info("✅ Migrated page ID: {$page->id} ({$page->name})");
                } else {
                    $this->info("🔍 Would migrate page ID: {$page->id} ({$page->name})");
                }
                
                $migratedCount++;
                $this->newLine();
                
            } catch (\Exception $e) {
                $this->error("❌ Error migrating page ID {$page->id}: " . $e->getMessage());
                $errorCount++;
            }
        }
        
        $this->newLine();
        $this->info("📊 Migration Summary:");
        $this->line("   Pages processed: " . count($pages));
        $this->line("   Pages migrated: {$migratedCount}");
        
        if ($errorCount > 0) {
            $this->error("   Errors: {$errorCount}");
        }
        
        if ($dryRun) {
            $this->newLine();
            $this->warn('⚠️  This was a DRY RUN - no changes were made');
            $this->info('💡 Run without --dry-run to apply changes');
        } else {
            $this->newLine();
            $this->info('✅ Migration complete!');
        }
        
        return Command::SUCCESS;
    }
    
    /**
     * Конвертация legacy shortcode в новый формат
     * 
     * Поддерживает несколько вариантов legacy синтаксиса:
     * - ~~mod path="sfera/" mod_name="section" guid="kawsfk9k"~
     * - ~~mod mod_name="section" guid="kawsfk9k" path="sfera/"~
     * - ~~mod guid="kawsfk9k" mod_name="section"~
     * 
     * @param string $content - Исходный контент
     * @return string - Контент с новым синтаксисом
     */
    private function convertShortcodes(string $content): string
    {
        // Паттерн для поиска legacy shortcode
        // Ищем ~~mod с любым порядком атрибутов, но обязательно с guid
        $pattern = '/~~mod\s+(?:[^~]*\s+)?guid=["\']?([a-zA-Z0-9_-]+)["\']?(?:\s+[^~]*)?\s*~/';
        
        // Заменяем на новый формат
        $newContent = preg_replace($pattern, '[section guid="$1"]', $content);
        
        return $newContent;
    }
    
    /**
     * Показать diff между старым и новым контентом
     * 
     * @param object $page - Объект страницы
     * @param string $newContent - Новый контент
     * @return void
     */
    private function showDiff($page, string $newContent): void
    {
        // Находим все изменения
        preg_match_all('/~~mod[^~]+~/', $page->content, $oldMatches);
        preg_match_all('/\[section guid="[^"]+"\]/', $newContent, $newMatches);
        
        if (!empty($oldMatches[0])) {
            $this->line("📄 Page: {$page->name} (ID: {$page->id})");
            $this->line("   Title: {$page->title}");
            $this->line("   Changes:");
            
            foreach ($oldMatches[0] as $index => $oldShortcode) {
                $newShortcode = $newMatches[0][$index] ?? 'N/A';
                $this->line("   - OLD: " . $this->truncate($oldShortcode, 60));
                $this->line("   + NEW: {$newShortcode}");
            }
        }
    }
    
    /**
     * Обрезать строку до указанной длины
     * 
     * @param string $str - Строка
     * @param int $length - Максимальная длина
     * @return string
     */
    private function truncate(string $str, int $length): string
    {
        if (strlen($str) <= $length) {
            return $str;
        }
        
        return substr($str, 0, $length) . '...';
    }
}
