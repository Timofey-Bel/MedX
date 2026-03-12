<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * SecondaryNavController - Контроллер для вторичного навигационного меню
 * 
 * Этот контроллер заменяет legacy класс secondary_nav из:
 * legacy/site/modules/sfera/secondary_nav/secondary_nav.class.php
 * 
 * ОСНОВНЫЕ ИЗМЕНЕНИЯ ПРИ МИГРАЦИИ:
 * 
 * 1. ВМЕСТО rows() → DB::table()
 *    Legacy: $pages = rows("SELECT * FROM pages WHERE active = 1");
 *    Laravel: $pages = DB::table('pages')->where('active', 1)->get();
 * 
 * 2. ВМЕСТО $_SESSION['smarty']->assign() → return view()
 *    Legacy: $_SESSION['smarty']->assign('menu_items', $menu_items);
 *            print $_SESSION['smarty']->fetch('secondary_nav.tpl');
 *    Laravel: return view('components.secondary-nav', compact('menu_items'));
 * 
 * 3. ВМЕСТО noSQL() → Laravel Query Builder (автоматическая защита от SQL-инъекций)
 *    Legacy: $id_safe = noSQL($id);
 *            $query = "SELECT * FROM pages WHERE id = '$id_safe'";
 *    Laravel: DB::table('pages')->where('id', $id)->get();
 * 
 * 4. СОХРАНЕНА логика построения иерархического дерева
 *    buildMenuTree() работает точно так же, как в legacy системе
 * 
 * НАЗНАЧЕНИЕ:
 * Вторичное навигационное меню отображает дополнительные ссылки на страницы сайта
 * (события, вакансии, поддержка и т.д.) из таблицы pages с поддержкой иерархии.
 */
class SecondaryNavController extends Controller
{
    /**
     * Получить данные вторичного меню для рендеринга
     * 
     * LEGACY АНАЛОГ:
     * function execute($arr) в secondary_nav.class.php
     * 
     * ЧТО ДЕЛАЕТ:
     * 1. Загружает активные страницы из таблицы pages
     * 2. Строит иерархическое дерево меню с помощью buildMenuTree()
     * 3. Возвращает view с данными меню
     * 
     * ОТЛИЧИЯ ОТ LEGACY:
     * - Возвращает view вместо print $_SESSION['smarty']->fetch()
     * - Использует Laravel Query Builder вместо прямых SQL-запросов
     * - Автоматическая защита от SQL-инъекций (не нужен noSQL())
     * 
     * ВАЖНО: Логика построения дерева ПОЛНОСТЬЮ ИДЕНТИЧНА legacy системе!
     * 
     * @return \Illuminate\View\View
     */
    public function getMenuItems()
    {
        // Загружаем меню из базы данных
        // Legacy: $menu_items = $this->loadMenuFromDatabase();
        // Laravel: то же самое, но с новым синтаксисом
        $menu_items = $this->loadMenuFromDatabase();
        
        // Возвращаем view с данными
        // Legacy: $_SESSION['smarty']->assign('menu_items', $menu_items);
        //         print $_SESSION['smarty']->fetch('sfera/secondary_nav/secondary_nav.tpl');
        // Laravel: return view('components.secondary-nav', compact('menu_items'));
        return view('components.secondary-nav', compact('menu_items'));
    }
    
    /**
     * Загрузка меню из таблицы pages
     * 
     * LEGACY АНАЛОГ:
     * private function loadMenuFromDatabase() в secondary_nav.class.php:
     * {
     *     $query = "SELECT id, name, title, parent_id, sort, active 
     *               FROM pages 
     *               WHERE active = 1 
     *               ORDER BY parent_id ASC, sort ASC";
     *     $pages = rows($query);
     *     
     *     if (empty($pages)) {
     *         return array();
     *     }
     *     
     *     return $this->buildMenuTree($pages, 0);
     * }
     * 
     * ЧТО ДЕЛАЕТ:
     * 1. Запрашивает все активные страницы (active = 1) из таблицы pages
     * 2. Сортирует по parent_id ASC, затем по sort ASC
     * 3. Если страниц нет - возвращает пустой массив
     * 4. Строит иерархическое дерево с помощью buildMenuTree()
     * 
     * ОТЛИЧИЯ ОТ LEGACY:
     * - DB::table('pages') вместо rows("SELECT ... FROM pages")
     * - ->where('active', 1) вместо WHERE active = 1
     * - ->orderBy() вместо ORDER BY в SQL
     * - ->get() возвращает коллекцию Laravel вместо обычного массива
     * - Автоматическая защита от SQL-инъекций
     * 
     * ВАЖНО: Порядок сортировки КРИТИЧЕН!
     * Сначала parent_id, потом sort - это обеспечивает правильную группировку
     * родительских и дочерних элементов для buildMenuTree()
     * 
     * @return array Иерархическое дерево меню
     */
    private function loadMenuFromDatabase()
    {
        // Загружаем все активные страницы, отсортированные по parent_id и sort
        // Legacy: $query = "SELECT id, name, title, parent_id, sort, active 
        //                   FROM pages 
        //                   WHERE active = 1 
        //                   ORDER BY parent_id ASC, sort ASC";
        //         $pages = rows($query);
        // 
        // Laravel: используем Query Builder для безопасных запросов
        $pages = DB::table('pages')
            ->select('id', 'name', 'title', 'parent_id', 'sort', 'active')
            ->where('active', 1)                // WHERE active = 1 (только активные)
            ->orderBy('parent_id', 'asc')       // ORDER BY parent_id ASC (группировка по родителю)
            ->orderBy('sort', 'asc')            // ORDER BY sort ASC (порядок внутри группы)
            ->get();                            // Выполнить запрос и получить результаты
        
        // Если страниц нет - возвращаем пустой массив
        // Legacy: if (empty($pages)) { return array(); }
        if ($pages->isEmpty()) {
            return [];
        }
        
        // Преобразуем коллекцию Laravel в обычный массив для buildMenuTree()
        // Legacy работал с массивами, поэтому конвертируем для совместимости
        $pagesArray = $pages->map(function($page) {
            return (array) $page;
        })->toArray();
        
        // Строим дерево меню, начиная с корневого уровня (parent_id = 0)
        // Legacy: return $this->buildMenuTree($pages, 0);
        // Laravel: то же самое
        return $this->buildMenuTree($pagesArray, 0);
    }
    
    /**
     * Построение иерархического дерева меню из плоского массива страниц
     * 
     * LEGACY АНАЛОГ:
     * private function buildMenuTree($pages, $parentId = 0) в secondary_nav.class.php:
     * {
     *     $menu = array();
     *     
     *     foreach ($pages as $page) {
     *         // Нормализуем parent_id (NULL, '', 0 - всё корневой уровень)
     *         $pageParentId = ($page['parent_id'] === null || $page['parent_id'] === '' || intval($page['parent_id']) === 0) 
     *             ? 0 
     *             : intval($page['parent_id']);
     *         
     *         if ($pageParentId === intval($parentId)) {
     *             $link = $this->generatePageLink($page);
     *             $submenu = $this->buildMenuTree($pages, $page['id']);
     *             
     *             $menuItem = array(
     *                 'id' => $page['id'],
     *                 'title' => !empty($page['title']) ? $page['title'] : $page['name'],
     *                 'link' => $link
     *             );
     *             
     *             if (!empty($submenu)) {
     *                 $menuItem['submenu'] = $submenu;
     *             }
     *             
     *             $menu[] = $menuItem;
     *         }
     *     }
     *     
     *     return $menu;
     * }
     * 
     * ЧТО ДЕЛАЕТ:
     * 1. Проходит по всем страницам
     * 2. Для каждой страницы проверяет, является ли она дочерней для $parentId
     * 3. Нормализует parent_id (NULL, '', 0 → все означают корневой уровень)
     * 4. Если страница подходит - добавляет её в меню
     * 5. Рекурсивно вызывает себя для поиска дочерних страниц
     * 6. Использует title если есть, иначе fallback на name
     * 7. Генерирует ссылку через generatePageLink()
     * 
     * ОТЛИЧИЯ ОТ LEGACY:
     * - НЕТ ОТЛИЧИЙ! Логика ПОЛНОСТЬЮ ИДЕНТИЧНА!
     * - Единственное изменение: route('page', ...) вместо '/page/{id}/'
     * 
     * ВАЖНО: Это рекурсивная функция!
     * Она вызывает сама себя для построения дерева любой глубины.
     * Например:
     * - Вызов с $parentId = 0 → находит все корневые страницы
     * - Для каждой корневой страницы вызывает себя с $parentId = id страницы
     * - Это находит все дочерние страницы первого уровня
     * - И так далее, пока не будет построено всё дерево
     * 
     * НОРМАЛИЗАЦИЯ parent_id:
     * В БД могут быть разные значения для "корневого уровня":
     * - NULL (нет родителя)
     * - '' (пустая строка)
     * - 0 (ноль)
     * Все эти значения означают одно и то же - страница на корневом уровне.
     * Поэтому мы нормализуем их все в 0 для единообразия.
     * 
     * @param array $pages Плоский массив всех страниц из БД
     * @param int $parentId ID родительской страницы (0 = корневой уровень)
     * @return array Иерархическое дерево меню для данного уровня
     */
    private function buildMenuTree($pages, $parentId = 0)
    {
        // Инициализируем массив меню для текущего уровня
        // Legacy: $menu = array();
        $menu = [];
        
        // Проходим по всем страницам
        // Legacy: foreach ($pages as $page) { ... }
        foreach ($pages as $page) {
            // Нормализуем parent_id (NULL, '', 0 - всё корневой уровень)
            // Legacy: $pageParentId = ($page['parent_id'] === null || $page['parent_id'] === '' || intval($page['parent_id']) === 0) 
            //             ? 0 
            //             : intval($page['parent_id']);
            // 
            // ПОЧЕМУ ЭТО ВАЖНО:
            // В БД parent_id может быть NULL, пустой строкой или 0
            // Все эти значения означают "корневой уровень"
            // Мы приводим их к единому формату (0) для упрощения сравнения
            $pageParentId = ($page['parent_id'] === null || $page['parent_id'] === '' || intval($page['parent_id']) === 0) 
                ? 0 
                : intval($page['parent_id']);
            
            // Проверяем, является ли эта страница дочерней для текущего родителя
            // Legacy: if ($pageParentId === intval($parentId)) { ... }
            if ($pageParentId === intval($parentId)) {
                // Генерируем ссылку на страницу
                // Legacy: $link = $this->generatePageLink($page);
                // Laravel: используем route() вместо прямых URL
                $link = $this->generatePageLink($page);
                
                // РЕКУРСИЯ: ищем дочерние страницы для текущей страницы
                // Legacy: $submenu = $this->buildMenuTree($pages, $page['id']);
                // Это позволяет построить дерево любой глубины
                $submenu = $this->buildMenuTree($pages, $page['id']);
                
                // Формируем элемент меню
                // Legacy: $menuItem = array(
                //             'id' => $page['id'],
                //             'title' => !empty($page['title']) ? $page['title'] : $page['name'],
                //             'link' => $link
                //         );
                // 
                // ВАЖНО: Используем title если есть, иначе fallback на name
                // Это позволяет иметь разные отображаемые названия и внутренние имена
                $menuItem = [
                    'id' => $page['id'],
                    'title' => !empty($page['title']) ? $page['title'] : $page['name'],
                    'link' => $link
                ];
                
                // Если есть подменю - добавляем его
                // Legacy: if (!empty($submenu)) {
                //             $menuItem['submenu'] = $submenu;
                //         }
                // 
                // Это создаёт вложенную структуру:
                // [
                //   'id' => 1,
                //   'title' => 'Родитель',
                //   'link' => '/page/1/',
                //   'submenu' => [
                //     ['id' => 2, 'title' => 'Ребёнок 1', 'link' => '/page/2/'],
                //     ['id' => 3, 'title' => 'Ребёнок 2', 'link' => '/page/3/']
                //   ]
                // ]
                if (!empty($submenu)) {
                    $menuItem['submenu'] = $submenu;
                }
                
                // Добавляем элемент в меню текущего уровня
                // Legacy: $menu[] = $menuItem;
                $menu[] = $menuItem;
            }
        }
        
        // Возвращаем меню текущего уровня
        // Legacy: return $menu;
        return $menu;
    }
    
    /**
     * Генерация ссылки на страницу
     * 
     * LEGACY АНАЛОГ:
     * private function generatePageLink($page) в secondary_nav.class.php:
     * {
     *     if (!empty($page['content'])) {
     *         return '/page/' . $page['id'] . '/';
     *     }
     *     
     *     return '#';
     * }
     * 
     * ЧТО ДЕЛАЕТ:
     * 1. Проверяет, есть ли у страницы контент
     * 2. Если есть - генерирует ссылку на страницу
     * 3. Если нет - возвращает placeholder (#)
     * 
     * ОТЛИЧИЯ ОТ LEGACY:
     * - route('page', ['pageId' => $page['id']]) вместо '/page/' . $page['id'] . '/'
     * - Использует Laravel route helper для генерации URL
     * - Это позволяет изменить структуру URL в одном месте (routes/web.php)
     * 
     * ПОЧЕМУ PLACEHOLDER (#)?
     * Некоторые страницы могут быть "заголовками" для подменю без собственного контента.
     * Например, "Помощь" может быть просто заголовком для "Доставка", "Оплата" и т.д.
     * В таком случае ссылка не нужна, используется # как placeholder.
     * 
     * @param array $page Данные страницы из БД
     * @return string URL страницы или '#' если контента нет
     */
    private function generatePageLink($page)
    {
        // Проверяем, есть ли у страницы контент
        // Legacy: if (!empty($page['content'])) {
        //             return '/page/' . $page['id'] . '/';
        //         }
        // 
        // Laravel: используем route() для генерации URL
        // Это позволяет изменить структуру URL в routes/web.php
        // без изменения кода контроллера
        if (!empty($page['content'])) {
            return route('page', ['pageId' => $page['id']]);
        }
        
        // Если контента нет - возвращаем placeholder
        // Legacy: return '#';
        // Laravel: то же самое
        // 
        // ПРИМЕЧАНИЕ: Страницы без контента обычно являются заголовками
        // для подменю и не должны быть кликабельными
        return '#';
    }
}
