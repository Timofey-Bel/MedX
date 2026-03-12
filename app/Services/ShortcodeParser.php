<?php

namespace App\Services;

/**
 * Парсер shortcode для контента страниц
 * 
 * Находит и заменяет shortcode-теги на реальный HTML из таблицы page_sections.
 * Поддерживает синтаксис: [section guid="kawsfk9k"]
 * 
 * Также собирает CSS и JS из секций для подключения в layout.
 */
class ShortcodeParser
{
    private SectionRepository $sectionRepository;
    
    /**
     * Массив CSS стилей из всех секций на странице
     * @var array
     */
    private array $collectedCss = [];
    
    /**
     * Массив JS скриптов из всех секций на странице
     * @var array
     */
    private array $collectedJs = [];
    
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
     * Также собирает CSS и JS из секций для последующего подключения.
     * 
     * @param string $content - Исходный контент с shortcode
     * @return array - ['html' => string, 'css' => string, 'js' => string]
     */
    public function parse(string $content): array
    {
        // Сбрасываем собранные CSS и JS перед парсингом
        $this->collectedCss = [];
        $this->collectedJs = [];
        
        // Регулярное выражение для поиска [section guid="..."]
        // Поддерживает guid длиной от 1 до 20 символов (буквы, цифры, дефис, подчеркивание)
        $pattern = '/\[section\s+guid=["\']?([a-zA-Z0-9_-]{1,20})["\']?\]/';
        
        $html = preg_replace_callback($pattern, function ($matches) {
            $guid = $matches[1];
            
            // Получаем секцию из БД
            $section = $this->sectionRepository->getByGuid($guid);
            
            // Если секция не найдена - возвращаем комментарий для отладки
            if ($section === null) {
                return "<!-- Section not found: {$guid} -->";
            }
            
            // Собираем CSS и JS для последующего подключения (дедупликация по GUID)
            if (!empty($section['css'])) {
                $this->collectedCss[$guid] = $section['css'];
            }
            
            if (!empty($section['js'])) {
                $this->collectedJs[$guid] = $section['js'];
            }
            
            // Возвращаем HTML секции
            return $section['html'];
        }, $content);
        
        return [
            'html' => $html,
            'css' => $this->getCombinedCss(),
            'js' => $this->getCombinedJs()
        ];
    }
    
    /**
     * Получение объединенного CSS всех секций
     * 
     * @return string - CSS-код всех секций, разделенный пустыми строками
     */
    private function getCombinedCss(): string
    {
        if (empty($this->collectedCss)) {
            return '';
        }
        
        // Добавляем комментарии для отладки
        $cssBlocks = [];
        foreach ($this->collectedCss as $guid => $css) {
            $cssBlocks[] = "/* Section: {$guid} */\n{$css}";
        }
        
        return implode("\n\n", $cssBlocks);
    }
    
    /**
     * Получение объединенного JavaScript всех секций
     * 
     * @return string - JavaScript-код всех секций, разделенный пустыми строками
     */
    private function getCombinedJs(): string
    {
        if (empty($this->collectedJs)) {
            return '';
        }
        
        // Добавляем комментарии для отладки
        $jsBlocks = [];
        foreach ($this->collectedJs as $guid => $js) {
            $jsBlocks[] = "// Section: {$guid}\n{$js}";
        }
        
        return implode("\n\n", $jsBlocks);
    }
    
    /**
     * Получение собранных CSS стилей
     * 
     * Возвращает массив CSS стилей из всех секций, найденных при парсинге.
     * Используется для подключения стилей в <head> страницы.
     * 
     * @return array - Массив CSS стилей, где ключ - GUID секции
     */
    public function getCollectedCss(): array
    {
        return $this->collectedCss;
    }
    
    /**
     * Получение собранных JS скриптов
     * 
     * Возвращает массив JS скриптов из всех секций, найденных при парсинге.
     * Используется для подключения скриптов перед </body>.
     * 
     * @return array - Массив JS скриптов, где ключ - GUID секции
     */
    public function getCollectedJs(): array
    {
        return $this->collectedJs;
    }
    
    /**
     * Получение HTML тега <style> со всеми собранными CSS
     * 
     * Объединяет все CSS стили в один тег <style>.
     * Удобно для вставки в <head> страницы.
     * 
     * @return string - HTML тег <style> или пустая строка
     */
    public function getCssTag(): string
    {
        if (empty($this->collectedCss)) {
            return '';
        }
        
        $css = implode("\n\n", $this->collectedCss);
        return "<style>\n{$css}\n</style>";
    }
    
    /**
     * Получение HTML тега <script> со всеми собранными JS
     * 
     * Объединяет все JS скрипты в один тег <script>.
     * Удобно для вставки перед </body>.
     * 
     * @return string - HTML тег <script> или пустая строка
     */
    public function getJsTag(): string
    {
        if (empty($this->collectedJs)) {
            return '';
        }
        
        $js = implode("\n\n", $this->collectedJs);
        return "<script>\n{$js}\n</script>";
    }
}
