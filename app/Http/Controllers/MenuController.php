<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/**
 * MenuController - Контроллер для работы с меню каталога
 * 
 * Этот контроллер заменяет legacy класс catalog_menu из:
 * legacy/site/modules/sfera/catalog_menu/catalog_menu.class.php
 * 
 * ОСНОВНЫЕ ИЗМЕНЕНИЯ ПРИ МИГРАЦИИ:
 * 
 * 1. ВМЕСТО rows() → DB::table()
 *    Legacy: $categories = rows("SELECT * FROM tree WHERE parent_id IS NULL");
 *    Laravel: $categories = DB::table('tree')->whereNull('parent_id')->get();
 * 
 * 2. ВМЕСТО $_SESSION['smarty']->assign() → return view()
 *    Legacy: $_SESSION['smarty']->assign('categories', $categories);
 *            print $_SESSION['smarty']->fetch('catalog_menu.tpl');
 *    Laravel: return view('components.catalog-menu', compact('categories'));
 * 
 * 3. ВМЕСТО noSQL() → Laravel Query Builder (автоматическая защита от SQL-инъекций)
 *    Legacy: $parent_id_safe = noSQL($parent_id);
 *            $query = "SELECT * FROM tree WHERE parent_id = '$parent_id_safe'";
 *    Laravel: DB::table('tree')->where('parent_id', $parentId)->get();
 * 
 * 4. ДОБАВЛЕНО кэширование для оптимизации
 *    Cache::remember('catalog_menu', 3600, ...) - кэш на 1 час
 */
class MenuController extends Controller
{
    /**
     * Получить данные меню каталога для рендеринга
     * 
     * LEGACY АНАЛОГ:
     * function execute($arr) в catalog_menu.class.php
     * 
     * ЧТО ДЕЛАЕТ:
     * 1. Загружает корневые категории из таблицы tree
     * 2. Для каждой категории рекурсивно загружает детей
     * 3. Возвращает view с данными категорий
     * 
     * ОТЛИЧИЯ ОТ LEGACY:
     * - Использует кэширование (Cache::remember) для ускорения
     * - Возвращает view вместо print $_SESSION['smarty']->fetch()
     * - Использует Laravel Query Builder вместо прямых SQL-запросов
     * 
     * @return \Illuminate\View\View
     */
    public function getCatalogMenu()
    {
        // Кэшируем меню на 1 час (3600 секунд) для улучшения производительности
        // В legacy системе кэширования не было, каждый раз делался запрос к БД
        $categories = Cache::remember('catalog_menu', 3600, function () {
            return $this->getRootCategories();
        });
        
        // Возвращаем view с данными
        // Legacy: $_SESSION['smarty']->assign('categories', $categories);
        //         print $_SESSION['smarty']->fetch('sfera/catalog_menu/catalog_menu.tpl');
        // Laravel: return view('components.catalog-menu', compact('categories'));
        return view('components.catalog-menu', compact('categories'));
    }
    
    /**
     * Получить корневые категории из таблицы tree
     * 
     * LEGACY АНАЛОГ:
     * Часть метода execute() в catalog_menu.class.php:
     * $query = "SELECT id, name, parent_id FROM tree 
     *           WHERE (parent_id = '' OR parent_id IS NULL)
     *           ORDER BY name ASC";
     * $root_categories = rows($query);
     * 
     * ЧТО ДЕЛАЕТ:
     * 1. Запрашивает категории где parent_id IS NULL или parent_id = ''
     * 2. Сортирует по имени (name ASC)
     * 3. Для каждой категории вызывает getChildren() для загрузки подкатегорий
     * 4. Возвращает массив категорий с их детьми
     * 
     * ОТЛИЧИЯ ОТ LEGACY:
     * - whereNull('parent_id')->orWhere('parent_id', '') вместо WHERE (parent_id = '' OR parent_id IS NULL)
     * - DB::table('tree') вместо rows("SELECT ... FROM tree")
     * - ->get() возвращает коллекцию Laravel вместо обычного массива
     * - Автоматическая защита от SQL-инъекций (не нужен noSQL())
     * 
     * @return array Массив корневых категорий с их детьми
     */
    private function getRootCategories()
    {
        // Получаем корневые категории из таблицы tree
        // Legacy: $query = "SELECT id, name, parent_id FROM tree 
        //                   WHERE (parent_id = '' OR parent_id IS NULL)
        //                   ORDER BY name ASC";
        //         $root_categories = rows($query);
        // 
        // Laravel: используем Query Builder для безопасных запросов
        $rootCategories = DB::table('tree')
            ->whereNull('parent_id')           // WHERE parent_id IS NULL
            ->orWhere('parent_id', '')         // OR parent_id = ''
            ->orderBy('name', 'asc')           // ORDER BY name ASC
            ->get();                           // Выполнить запрос и получить результаты
        
        // Строим массив категорий с их детьми
        // Точно так же, как в legacy системе
        $categories = [];
        foreach ($rootCategories as $category) {
            $categories[] = [
                'id' => $category->id,
                'name' => $category->name,
                // Рекурсивно загружаем детей этой категории
                // Legacy: $category_data['children'] = $this->getChildren($category['id']);
                // Laravel: то же самое, но с объектом вместо массива
                'children' => $this->getChildren($category->id)
            ];
        }
        
        return $categories;
    }
    
    /**
     * Рекурсивно получить дочерние категории
     * 
     * LEGACY АНАЛОГ:
     * private function getChildren($parent_id) в catalog_menu.class.php:
     * {
     *     $parent_id_safe = noSQL($parent_id);
     *     $query = "SELECT id, name, parent_id FROM tree 
     *               WHERE parent_id = '$parent_id_safe'
     *               ORDER BY name ASC";
     *     $children = rows($query);
     *     
     *     $result = [];
     *     foreach ($children as $child) {
     *         $child_data = [
     *             'id' => $child['id'],
     *             'name' => $child['name'],
     *             'children' => $this->getChildren($child['id'])
     *         ];
     *         $result[] = $child_data;
     *     }
     *     return $result;
     * }
     * 
     * ЧТО ДЕЛАЕТ:
     * 1. Находит все категории где parent_id = $parentId
     * 2. Для каждой найденной категории рекурсивно вызывает себя
     * 3. Строит дерево категорий любой глубины
     * 
     * ОТЛИЧИЯ ОТ LEGACY:
     * - DB::table('tree')->where('parent_id', $parentId) вместо "WHERE parent_id = '$parent_id_safe'"
     * - Не нужен noSQL() - Laravel автоматически защищает от SQL-инъекций
     * - ->get() вместо rows()
     * - $category->id вместо $category['id'] (объект вместо массива)
     * 
     * ВАЖНО: Логика работы ПОЛНОСТЬЮ ИДЕНТИЧНА legacy системе!
     * Меняется только синтаксис, но алгоритм тот же самый.
     * 
     * @param string|int $parentId ID родительской категории
     * @return array Массив дочерних категорий с их детьми
     */
    private function getChildren($parentId)
    {
        // Получаем дочерние категории
        // Legacy: $parent_id_safe = noSQL($parent_id);
        //         $query = "SELECT id, name, parent_id FROM tree 
        //                   WHERE parent_id = '$parent_id_safe'
        //                   ORDER BY name ASC";
        //         $children = rows($query);
        // 
        // Laravel: Query Builder автоматически экранирует параметры
        $children = DB::table('tree')
            ->where('parent_id', $parentId)    // WHERE parent_id = ? (безопасно!)
            ->orderBy('name', 'asc')           // ORDER BY name ASC
            ->get();                           // Выполнить запрос
        
        // Строим массив детей с их подкатегориями
        // Точно так же, как в legacy
        $result = [];
        foreach ($children as $child) {
            $result[] = [
                'id' => $child->id,
                'name' => $child->name,
                // РЕКУРСИЯ: для каждого ребенка загружаем его детей
                // Это позволяет построить дерево любой глубины
                // Legacy: 'children' => $this->getChildren($child['id'])
                // Laravel: то же самое
                'children' => $this->getChildren($child->id)
            ];
        }
        
        return $result;
    }
    
    /**
     * AJAX endpoint для динамической загрузки подкатегорий
     * 
     * НОВЫЙ ФУНКЦИОНАЛ (не было в legacy)
     * 
     * ЧТО ДЕЛАЕТ:
     * Позволяет загружать подкатегории по требованию через AJAX
     * Это улучшает производительность - не нужно загружать все дерево сразу
     * 
     * ИСПОЛЬЗОВАНИЕ:
     * GET /api/menu/subcategories/{categoryId}
     * 
     * ОТВЕТ:
     * {
     *   "success": true,
     *   "categories": [
     *     {
     *       "id": "123",
     *       "name": "Подкатегория 1",
     *       "children": [...]
     *     }
     *   ]
     * }
     * 
     * @param Request $request HTTP запрос
     * @param string|int $categoryId ID категории
     * @return \Illuminate\Http\JsonResponse JSON ответ с подкатегориями
     */
    public function getSubcategories(Request $request, $categoryId)
    {
        // Используем тот же метод getChildren(), что и для основного меню
        // Это гарантирует консистентность данных
        $children = $this->getChildren($categoryId);
        
        // Возвращаем JSON ответ
        // В legacy такого не было, но это стандартный подход в Laravel
        return response()->json([
            'success' => true,
            'categories' => $children
        ]);
    }
}
