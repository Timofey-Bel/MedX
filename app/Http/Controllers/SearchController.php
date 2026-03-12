<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * SearchController - Контроллер поиска товаров
 * 
 * Мигрировано из: legacy/site/modules/sfera/search/search.class.php
 * 
 * ОСНОВНЫЕ ИЗМЕНЕНИЯ ПРИ МИГРАЦИИ:
 * - $_SESSION['search'] → session(['search_query' => $query])
 * - rows() → DB::table()
 * - $_SESSION['smarty']->assign() → return view()
 * - Прямые SQL запросы → Laravel Query Builder
 */
class SearchController extends Controller
{
    /**
     * index() - Главная страница поиска с результатами
     * 
     * LEGACY: execute() метод в search.class.php
     * 
     * ЧТО ИЗМЕНИЛОСЬ:
     * - Request $request вместо глобальных $_GET/$_POST
     * - session() вместо $_SESSION['search']
     * - return view() вместо $_SESSION['smarty']->fetch()
     * 
     * @param Request $request - Laravel Request объект (вместо $_GET)
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Получаем поисковый запрос из параметров
        // LEGACY: $query = $_GET['query'] ?? '';
        $query = $request->input('query', '');
        
        // Сохраняем запрос в сессию (если не пустой)
        // LEGACY: $_SESSION['search'] = $query;
        if (!empty($query)) {
            session(['search_query' => $query]);
        } else {
            // Если запрос пустой, удаляем из сессии
            session()->forget('search_query');
            // Пытаемся восстановить из сессии
            $query = session('search_query', '');
        }
        
        // Получаем номер страницы для пагинации
        // LEGACY: $page = $_GET['page'] ?? 1;
        $page = $request->input('page', 1);
        $limit = 20; // 20 товаров на страницу (как в legacy)
        
        // Получаем фильтры (автор и возраст)
        // LEGACY: $filterAuthors = $_GET['author'] ?? [];
        $filterAuthors = $request->input('author', []);
        $filterAges = $request->input('age', []);
        
        // Получаем товары и общее количество
        // LEGACY: $products = $this->getProducts(...);
        $products = $this->getProducts($page, $limit, $query, $filterAuthors, $filterAges);
        $total = $this->getProductsCount($query, $filterAuthors, $filterAges);
        
        // Получаем корзину и избранное из сессии
        $cart = session('cart', ['items' => []]);
        
        // Получаем избранное и преобразуем в правильный формат
        // Для неавторизованных: session('favorites') = ['00-00006779', '00-00006780']
        // Нужно преобразовать в: ['items' => ['00-00006779' => true, '00-00006780' => true]]
        $sessionFavorites = session('favorites', []);
        $favorites = ['items' => []];
        
        if (is_array($sessionFavorites)) {
            // Если это массив ID (для неавторизованных)
            if (isset($sessionFavorites[0]) && !isset($sessionFavorites['items'])) {
                foreach ($sessionFavorites as $productId) {
                    $favorites['items'][$productId] = true;
                }
            } else {
                // Если уже в правильном формате (для авторизованных)
                $favorites = $sessionFavorites;
            }
        }
        
        // Рассчитываем пагинацию (группы по 5 страниц)
        // LEGACY: Аналогичная логика в search.class.php
        $pages = $total > 0 ? ceil($total / $limit) : 1;
        $pagesPerGroup = 5; // Показываем по 5 страниц за раз
        $currentGroup = ceil($page / $pagesPerGroup);
        $startPage = ($currentGroup - 1) * $pagesPerGroup + 1;
        $endPage = min($currentGroup * $pagesPerGroup, $pages);
        $nextGroupStart = $endPage + 1;
        $prevGroup = $currentGroup - 1;
        $prevGroupEnd = $prevGroup > 0 ? min($prevGroup * $pagesPerGroup, $pages) : 1;
        
        // Возвращаем view с данными
        // LEGACY: $_SESSION['smarty']->assign('products', $products);
        //         print $_SESSION['smarty']->fetch('search.tpl');
        return view('search.index', [
            'products' => $products,
            'search_query' => $query,
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'pages' => $pages,
            'startPage' => $startPage,
            'endPage' => $endPage,
            'nextGroupStart' => $nextGroupStart,
            'prevGroupEnd' => $prevGroupEnd,
            'hasNextGroup' => $nextGroupStart <= $pages,
            'hasPrevGroup' => $currentGroup > 1,
            'cart' => $cart,
            'favorites' => $favorites,
        ]);
    }
    
    /**
     * autocomplete() - AJAX endpoint для автодополнения поиска
     * 
     * НОВЫЙ МЕТОД (не было в legacy)
     * 
     * Возвращает до 10 подсказок для поискового запроса
     * Минимум 2 символа для начала поиска
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function autocomplete(Request $request)
    {
        // Получаем поисковый запрос
        $query = $request->input('query', '');
        
        // Если меньше 2 символов - возвращаем пустой массив
        if (strlen($query) < 2) {
            return response()->json(['suggestions' => []]);
        }
        
        // Ищем товары по имени или ID
        // LEGACY: rows("SELECT * FROM products WHERE name LIKE '%$query%'")
        // НОВОЕ: DB::table() с Query Builder
        $suggestions = DB::table('products as p')
            ->leftJoin('prices as pr', function($join) {
                // Присоединяем цены (price_type_id = '000000002' - розничная цена)
                $join->on('p.id', '=', 'pr.product_id')
                     ->where('pr.price_type_id', '=', '000000002');
            })
            ->where(function($where) use ($query) {
                // Ищем по имени ИЛИ по ID товара
                $where->where('p.name', 'LIKE', "%{$query}%")
                      ->orWhere('p.id', 'LIKE', "%{$query}%");
            })
            ->select('p.id', 'p.name', 'p.picture', 'pr.price')
            ->limit(10) // Максимум 10 подсказок
            ->get();
        
        // Формируем результаты для JSON ответа
        $results = [];
        foreach ($suggestions as $product) {
            $results[] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => round($product->price ?? 0),
                'url' => "/product/{$product->id}/"
            ];
        }
        
        return response()->json(['suggestions' => $results]);
    }
    
    /**
     * getProducts() - Получение товаров с фильтрами и пагинацией
     * 
     * LEGACY: getProducts() в search.class.php
     * 
     * ЧТО ИЗМЕНИЛОСЬ:
     * - rows() → DB::table()
     * - Прямой SQL → Query Builder с методами
     * - Ручные JOIN → leftJoin() и whereExists()
     * - Добавлен рейтинг и количество отзывов (как в CatalogController)
     * 
     * @param int $page - Номер страницы
     * @param int $limit - Количество товаров на странице
     * @param string $query - Поисковый запрос
     * @param array $filterAuthors - Фильтр по авторам
     * @param array $filterAges - Фильтр по возрастам
     * @return array - Массив товаров
     */
    private function getProducts($page, $limit, $query, $filterAuthors, $filterAges)
    {
        // Рассчитываем offset для пагинации
        $offset = ($page - 1) * $limit;
        
        // Если запрос пустой - возвращаем пустой массив
        if (empty($query)) {
            return [];
        }
        
        // Строим запрос к БД
        // LEGACY: $sql = "SELECT p.*, pr.price FROM products p LEFT JOIN prices pr ON..."
        $queryBuilder = DB::table('products as p')
            ->leftJoin('prices as pr', function($join) {
                // LEFT JOIN prices - присоединяем цены
                // LEGACY: LEFT JOIN prices pr ON p.id = pr.product_id AND pr.price_type_id = '000000002'
                $join->on('p.id', '=', 'pr.product_id')
                     ->where('pr.price_type_id', '=', '000000002');
            })
            ->where(function($where) use ($query) {
                // WHERE (name LIKE '%query%' OR id LIKE '%query%')
                // LEGACY: WHERE (p.name LIKE '%$query%' OR p.id LIKE '%$query%')
                $where->where('p.name', 'LIKE', "%{$query}%")
                      ->orWhere('p.id', 'LIKE', "%{$query}%");
            });
        
        // Применяем фильтр по авторам (если указан)
        // LEGACY: AND EXISTS (SELECT 1 FROM authors WHERE product_id = p.id AND author_name IN (...))
        if (!empty($filterAuthors)) {
            $queryBuilder->whereExists(function($subquery) use ($filterAuthors) {
                // Подзапрос для проверки наличия автора
                $subquery->select(DB::raw(1))
                         ->from('authors as a')
                         ->whereColumn('a.product_id', 'p.id')
                         ->whereIn('a.author_name', $filterAuthors);
            });
        }
        
        // Применяем фильтр по возрастам (если указан)
        // LEGACY: AND EXISTS (SELECT 1 FROM ages WHERE product_id = p.id AND age IN (...))
        if (!empty($filterAges)) {
            $queryBuilder->whereExists(function($subquery) use ($filterAges) {
                // Подзапрос для проверки возрастной категории
                $subquery->select(DB::raw(1))
                         ->from('ages as a')
                         ->whereColumn('a.product_id', 'p.id')
                         ->whereIn('a.age', $filterAges);
            });
        }
        
        // Выполняем запрос с пагинацией
        // LEGACY: rows($sql . " LIMIT $offset, $limit")
        $results = $queryBuilder
            ->select('p.*', 'pr.price as product_price', 'p.quantity')
            ->orderBy('p.name', 'asc')
            ->offset($offset)
            ->limit($limit)
            ->get();
        
        // Формируем массив товаров для view
        $products = [];
        foreach ($results as $row) {
            // Получаем рейтинг и количество отзывов (как в CatalogController)
            $ratingData = $this->getProductRating($row->id);
            
            $products[] = [
                'id' => $row->id,
                'name' => $row->name ?? '',
                'description' => $row->description ?? '',
                'image' => $this->getProductImageUrl($row->id),
                'price' => round($row->product_price ?? 0),
                'quantity' => intval($row->quantity ?? 99),
                'rating' => $ratingData['rating'],
                'reviews_count' => $ratingData['reviews_count']
            ];
        }
        
        return $products;
    }
    
    /**
     * getProductsCount() - Подсчет общего количества товаров
     * 
     * LEGACY: getProductsCount() в search.class.php
     * 
     * ЧТО ИЗМЕНИЛОСЬ:
     * - rows() → DB::table()->count()
     * - Те же WHERE условия, что и в getProducts()
     * 
     * @param string $query - Поисковый запрос
     * @param array $filterAuthors - Фильтр по авторам
     * @param array $filterAges - Фильтр по возрастам
     * @return int - Количество товаров
     */
    private function getProductsCount($query, $filterAuthors, $filterAges)
    {
        // Если запрос пустой - возвращаем 0
        if (empty($query)) {
            return 0;
        }
        
        // Строим запрос (те же условия, что и в getProducts)
        // LEGACY: $sql = "SELECT COUNT(*) FROM products WHERE..."
        $queryBuilder = DB::table('products as p')
            ->where(function($where) use ($query) {
                $where->where('p.name', 'LIKE', "%{$query}%")
                      ->orWhere('p.id', 'LIKE', "%{$query}%");
            });
        
        // Применяем фильтр по авторам
        if (!empty($filterAuthors)) {
            $queryBuilder->whereExists(function($subquery) use ($filterAuthors) {
                $subquery->select(DB::raw(1))
                         ->from('authors as a')
                         ->whereColumn('a.product_id', 'p.id')
                         ->whereIn('a.author_name', $filterAuthors);
            });
        }
        
        // Применяем фильтр по возрастам
        if (!empty($filterAges)) {
            $queryBuilder->whereExists(function($subquery) use ($filterAges) {
                $subquery->select(DB::raw(1))
                         ->from('ages as a')
                         ->whereColumn('a.product_id', 'p.id')
                         ->whereIn('a.age', $filterAges);
            });
        }
        
        // LEGACY: intval(rows($sql)[0]['count'])
        // НОВОЕ: ->count() возвращает число напрямую
        return $queryBuilder->count();
    }
    
    /**
     * getProductImageUrl() - Получение URL изображения товара
     * 
     * LEGACY: Аналогичная логика в search.class.php
     * 
     * ЛОГИКА:
     * 1. Проверяем v_products_o_products (связь с Ozon)
     * 2. Если есть - ищем в o_images
     * 3. Если нет - используем /import_files/{id}b.jpg
     * 4. Fallback - product_empty.jpg
     * 
     * @param string $productId - ID товара
     * @return string - URL изображения
     */
    private function getProductImageUrl($productId)
    {
        // Если ID пустой - возвращаем заглушку
        if (empty($productId)) {
            return '/assets/img/product_empty.jpg';
        }
        
        // Проверяем связь с Ozon
        // LEGACY: rows("SELECT product_id FROM v_products_o_products WHERE offer_id = '$productId'")
        $ozonProduct = DB::table('v_products_o_products')
            ->where('offer_id', $productId)
            ->first();
        
        // Если товар есть в Ozon - ищем изображение
        if ($ozonProduct && !empty($ozonProduct->product_id)) {
            // LEGACY: rows("SELECT * FROM o_images WHERE product_id = '{$ozonProduct->product_id}' AND image_order = 0")
            $oImage = DB::table('o_images')
                ->where('product_id', $ozonProduct->product_id)
                ->where('image_order', 0) // Первое изображение
                ->first();
            
            if ($oImage) {
                // Возвращаем путь к изображению Ozon
                return "/o_images/{$oImage->product_id}/0.jpg";
            }
        }
        
        // Fallback - стандартный путь к изображению
        // LEGACY: "/import_files/{$productId}b.jpg"
        return "/import_files/{$productId}b.jpg";
    }
    
    /**
     * getProductRating() - Получение рейтинга и количества отзывов для товара
     * 
     * НОВЫЙ МЕТОД (скопирован из CatalogController)
     * 
     * ЛОГИКА:
     * 1. Проверяем v_products_o_products для получения SKU
     * 2. Если SKU найден - получаем средний рейтинг и количество отзывов из o_reviews
     * 3. Если данных нет - возвращаем 0
     * 
     * ЗАЧЕМ: Для отображения рейтинга на карточках товаров в результатах поиска
     * 
     * @param string $productId - ID товара (offer_id)
     * @return array - ['rating' => float, 'reviews_count' => int]
     */
    private function getProductRating($productId)
    {
        // Если ID пустой - возвращаем нулевые значения
        if (empty($productId)) {
            return ['rating' => 0, 'reviews_count' => 0];
        }
        
        $rating = 0;
        $reviewsCount = 0;
        
        // Проверяем наличие товара в v_products_o_products для получения SKU
        // SKU используется для связи с отзывами в o_reviews
        $ozonProduct = DB::table('v_products_o_products')
            ->where('offer_id', $productId)
            ->first();
        
        if ($ozonProduct && !empty($ozonProduct->sku)) {
            $sku = intval($ozonProduct->sku);
            
            // Получаем средний рейтинг и количество отзывов
            $ratingData = DB::table('o_reviews')
                ->where('sku', $sku)
                ->whereNotNull('rating')
                ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total_count')
                ->first();
            
            if ($ratingData) {
                $rating = round($ratingData->avg_rating, 1);
                $reviewsCount = intval($ratingData->total_count);
            }
        }
        
        return ['rating' => $rating, 'reviews_count' => $reviewsCount];
    }
}
