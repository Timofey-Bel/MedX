<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use App\Services\CartService;

class GlobalDataComposer
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Привязка данных к view
     * Этот composer передает $categories, $menu_items и $cart_data во все view
     */
    public function compose(View $view)
    {
        // Пропускаем загрузку данных для админ-панели
        if ($this->isAdminRoute()) {
            return;
        }
        
        // Загружаем категории для меню (с кэшированием)
        $categories = Cache::remember('global_categories', 3600, function () {
            return $this->loadCategories();
        });
        
        // Загружаем пункты вторичного меню (с кэшированием)
        $menu_items = Cache::remember('global_menu_items', 3600, function () {
            return $this->loadMenuItems();
        });
        
        // Загружаем данные корзины из сессии (без кэширования - данные динамические)
        $cart_data = $this->cartService->getCartData();
        
        $view->with('categories', $categories);
        $view->with('menu_items', $menu_items);
        $view->with('cart_data', $cart_data);
    }
    
    /**
     * Проверка, является ли текущий маршрут админским
     */
    private function isAdminRoute()
    {
        $currentRoute = Route::currentRouteName();
        
        // Проверяем префикс маршрута
        if ($currentRoute && str_starts_with($currentRoute, 'admin.')) {
            return true;
        }
        
        // Проверяем URL
        $currentPath = request()->path();
        if (str_starts_with($currentPath, 'admin')) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Загрузка категорий из таблицы tree
     */
    private function loadCategories()
    {
        $rootCategories = DB::table('tree')
            ->whereNull('parent_id')
            ->orWhere('parent_id', '')
            ->orderBy('name', 'asc')
            ->get();
        
        $categories = [];
        foreach ($rootCategories as $category) {
            $categories[] = [
                'id' => $category->id,
                'name' => $category->name,
                'children' => $this->loadChildren($category->id)
            ];
        }
        
        return $categories;
    }
    
    /**
     * Рекурсивная загрузка дочерних категорий
     */
    private function loadChildren($parentId)
    {
        $children = DB::table('tree')
            ->where('parent_id', $parentId)
            ->orderBy('name', 'asc')
            ->get();
        
        $result = [];
        foreach ($children as $child) {
            $result[] = [
                'id' => $child->id,
                'name' => $child->name,
                'children' => $this->loadChildren($child->id)
            ];
        }
        
        return $result;
    }
    
    /**
     * Загрузка пунктов вторичного меню из таблицы pages
     */
    private function loadMenuItems()
    {
        $pages = DB::table('pages')
            ->select('id', 'name', 'title', 'content', 'parent_id', 'sort', 'active')
            ->where('active', 1)
            ->orderBy('parent_id', 'asc')
            ->orderBy('sort', 'asc')
            ->get();
        
        if ($pages->isEmpty()) {
            return [];
        }
        
        $pagesArray = $pages->map(function($page) {
            return (array) $page;
        })->toArray();
        
        return $this->buildMenuTree($pagesArray, 0);
    }
    
    /**
     * Построение дерева меню
     */
    private function buildMenuTree($pages, $parentId = 0)
    {
        $menu = [];
        
        foreach ($pages as $page) {
            $pageParentId = ($page['parent_id'] === null || $page['parent_id'] === '' || intval($page['parent_id']) === 0) 
                ? 0 
                : intval($page['parent_id']);
            
            if ($pageParentId === intval($parentId)) {
                // Проверяем существование маршрута перед его использованием
                $link = '#';
                if (!empty($page['content']) && Route::has('page')) {
                    $link = route('page', ['slug' => $page['id']]);
                }
                $submenu = $this->buildMenuTree($pages, $page['id']);
                
                $menuItem = [
                    'id' => $page['id'],
                    'title' => !empty($page['title']) ? $page['title'] : $page['name'],
                    'link' => $link
                ];
                
                if (!empty($submenu)) {
                    $menuItem['submenu'] = $submenu;
                }
                
                $menu[] = $menuItem;
            }
        }
        
        return $menu;
    }
}
