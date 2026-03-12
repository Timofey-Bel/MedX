<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product; // Предполагаем, что у вас есть модель Product
use App\Models\Category; // Предполагаем, что у вас есть модель Category (или Tree)
use Illuminate\Support\Facades\DB;
use App\Services\FilterService; // Для сложных запросов, пока не перейдем на Eloquent полностью

class CatalogController extends Controller
{
    private $filterService;

    public function __construct(FilterService $filterService)
    {
        $this->filterService = $filterService;
    }
    public function index(Request $request, $category_id = null)
    {
        // Инициализация переменных для фильтрации и пагинации
        $page = $request->input('page', 1);
        $limit = 20; // Лимит товаров на страницу, как в старом коде
        $offset = ($page - 1) * $limit;

        // Получение данных категории
        $category = null;
        if ($category_id) {
            // В старом коде category_id мог быть строкой, поэтому ищем по id
            $category = Category::where('id', $category_id)->first(); // Предполагаем, что ваша модель Category соответствует таблице tree
            if (!$category) {
                abort(404); // Если категория не найдена, возвращаем 404
            }
        }

        // Получение фильтров из GET параметров
        // Laravel автоматически обрабатывает массивы, поэтому 'author[]' будет доступен как $request->input('author')
        $filter_authors = $request->input('author', []);
        $filter_ages = $request->input('age', []);
        $filter_series_ids = $request->input('seriya', []);
        $filter_product_type_ids = $request->input('product_type', []);
        $filter_topic_ids = $request->input('topic', []);

        // Конвертируем ID в значения для фильтрации
        $filter_series = $this->convertIdsToValues($filter_series_ids, 'v_seriya');
        $filter_product_types = $this->convertIdsToValues($filter_product_type_ids, 'v_tip_tovara');
        $filter_topics = $this->convertIdsToValues($filter_topic_ids, 'v_tematika');

        // Здесь будет логика получения товаров и их количества
        // Пока используем заглушки
        $products = $this->getProducts($page, $limit, $category_id, $filter_authors, $filter_ages, $filter_series, $filter_product_types, $filter_topics);
        $total = $this->getProductsCount($category_id, $filter_authors, $filter_ages, $filter_series, $filter_product_types, $filter_topics);

        // Расчет пагинации
        $pages = $total > 0 ? ceil($total / $limit) : 1;
        $pagesPerGroup = 5;
        $currentGroup = ceil($page / $pagesPerGroup);
        $startPage = ($currentGroup - 1) * $pagesPerGroup + 1;
        $endPage = min($currentGroup * $pagesPerGroup, $pages);
        $nextGroupStart = $endPage + 1;
        $prevGroup = $currentGroup - 1;
        $prevGroupEnd = $prevGroup > 0 ? min($prevGroup * $pagesPerGroup, $pages) : 1;

        // Получение подкатегорий для меню
        // Временно отключено для диагностики производительности
        // $categories = $this->getCategories($category_id);
        $categories = [];

        // Получение авторов для фильтра
        $authors = $this->filterService->getAuthors();

        // Получение возрастов для фильтра
        $ages = $this->filterService->getAges();

        // Получение серий для фильтра
        $series = $this->filterService->getSeries();

        // Получение типов товаров для фильтра
        $productTypes = $this->filterService->getProductTypes();

        // Получение тематик для фильтра
        $topics = $this->filterService->getTopics();

        // TODO: Интеграция с корзиной и избранным (через сессии или другие механизмы Laravel)
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

        return view('catalog.index', [
            'products' => $products,
            'categories' => $categories,
            'authors' => $authors, // Добавляем авторов для фильтра
            'ages' => $ages, // Добавляем возрасты для фильтра
            'series' => $series, // Добавляем серии для фильтра
            'productTypes' => $productTypes, // Добавляем типы товаров для фильтра
            'topics' => $topics, // Добавляем тематики для фильтра
            'category' => $category ?? ['id' => '', 'name' => 'Каталог'], // Передаем объект или массив
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
            'filter_authors' => $filter_authors, // Передаем выбранных авторов в представление
            'filter_ages' => $filter_ages, // Передаем выбранные возрасты в представление
            'filter_series' => $filter_series_ids, // Передаем выбранные ID серий в представление
            'filter_product_types' => $filter_product_type_ids, // Передаем выбранные ID типов товаров в представление
            'filter_topics' => $filter_topic_ids, // Передаем выбранные ID тематик в представление
        ]);
    }

    /**
     * Метод для получения списка товаров с фильтрацией и пагинацией.
     * В дальнейшем будет переписан с использованием Eloquent.
     */
    private function getProducts($page, $limit, $category_id, $filter_authors, $filter_ages, $filter_series, $filter_product_types, $filter_topics)
    {
        $offset = ($page - 1) * $limit;

        $where_clauses = [];
        $bindings = [];

        // Category filtering
        if (!empty($category_id)) {
            $category_ids = $this->get_subcategories_ids($category_id);
            $placeholders = implode(',', array_fill(0, count($category_ids), '?'));
            $where_clauses[] = "p.category_id IN ($placeholders)";
            $bindings = array_merge($bindings, $category_ids);
        } else {
            $where_clauses[] = "1=1"; // Базовое условие
        }

        // Authors filter
        if (!empty($filter_authors) && is_array($filter_authors)) {
            $placeholders = implode(',', array_fill(0, count($filter_authors), '?'));
            $where_clauses[] = "EXISTS (SELECT 1 FROM authors a WHERE a.product_id = p.id AND a.author_name IN ($placeholders))";
            $bindings = array_merge($bindings, $filter_authors);
        }

        // Ages filter
        if (!empty($filter_ages) && is_array($filter_ages)) {
            $placeholders = implode(',', array_fill(0, count($filter_ages), '?'));
            $where_clauses[] = "EXISTS (SELECT 1 FROM ages ag WHERE ag.product_id = p.id AND ag.age IN ($placeholders))";
            $bindings = array_merge($bindings, $filter_ages);
        }

        // Series filter
        if (!empty($filter_series) && is_array($filter_series)) {
            $placeholders = implode(',', array_fill(0, count($filter_series), '?'));
            $where_clauses[] = "EXISTS (SELECT 1 FROM attributes a WHERE a.product_id = p.id AND BINARY a.name = 'Серия' AND BINARY a.value IN ($placeholders))";
            $bindings = array_merge($bindings, $filter_series);
        }

        // Product types filter
        if (!empty($filter_product_types) && is_array($filter_product_types)) {
            $placeholders = implode(',', array_fill(0, count($filter_product_types), '?'));
            $where_clauses[] = "EXISTS (SELECT 1 FROM attributes a WHERE a.product_id = p.id AND BINARY a.name = 'Тип товара' AND BINARY a.value IN ($placeholders))";
            $bindings = array_merge($bindings, $filter_product_types);
        }

        // Topics filter
        if (!empty($filter_topics) && is_array($filter_topics)) {
            $placeholders = implode(',', array_fill(0, count($filter_topics), '?'));
            $where_clauses[] = "EXISTS (SELECT 1 FROM attributes a WHERE a.product_id = p.id AND BINARY a.name = 'Тематика' AND BINARY a.value IN ($placeholders))";
            $bindings = array_merge($bindings, $filter_topics);
        }

        $where_sql = implode(' AND ', $where_clauses);

        $query_sql = "SELECT DISTINCT p.*, pr.price as product_price, p.quantity
                      FROM products p
                      LEFT JOIN prices pr ON p.id = pr.product_id AND pr.price_type_id = '000000002'
                      WHERE $where_sql
                      ORDER BY p.name ASC
                      LIMIT ?, ?";

        $bindings[] = $offset;
        $bindings[] = $limit;

        $result = DB::select($query_sql, $bindings);

        // Преобразуем результаты в удобный формат
        $products = [];
        
        foreach ($result as $row) {
            $row_array = (array) $row;
            $product_id = $row_array['id'];

            $rating_data = $this->getProductRating($product_id);
            $image_url = $this->getProductImageUrl($product_id);

            $products[] = [
                'id' => $product_id,
                'name' => $row_array['name'] ?? '',
                'description' => $row_array['description'] ?? '',
                'image' => $image_url,
                'price' => round($row_array['product_price'] ?? 0),
                'quantity' => intval($row_array['quantity'] ?? 99),
                'rating' => $rating_data['rating'],
                'reviews_count' => $rating_data['reviews_count']
            ];
        }

        return $products;
    }

    /**
     * Метод для получения общего количества товаров с учетом фильтров.
     * В дальнейшем будет переписан с использованием Eloquent.
     */
    private function getProductsCount($category_id, $filter_authors, $filter_ages, $filter_series, $filter_product_types, $filter_topics)
    {
        $where_clauses = [];
        $bindings = [];

        // Category filtering
        if (!empty($category_id)) {
            $category_ids = $this->get_subcategories_ids($category_id);
            $placeholders = implode(',', array_fill(0, count($category_ids), '?'));
            $where_clauses[] = "p.category_id IN ($placeholders)";
            $bindings = array_merge($bindings, $category_ids);
        } else {
            $where_clauses[] = "1=1";
        }

        // Authors filter
        if (!empty($filter_authors) && is_array($filter_authors)) {
            $placeholders = implode(',', array_fill(0, count($filter_authors), '?'));
            $where_clauses[] = "EXISTS (SELECT 1 FROM authors a WHERE a.product_id = p.id AND a.author_name IN ($placeholders))";
            $bindings = array_merge($bindings, $filter_authors);
        }

        // Ages filter
        if (!empty($filter_ages) && is_array($filter_ages)) {
            $placeholders = implode(',', array_fill(0, count($filter_ages), '?'));
            $where_clauses[] = "EXISTS (SELECT 1 FROM ages ag WHERE ag.product_id = p.id AND ag.age IN ($placeholders))";
            $bindings = array_merge($bindings, $filter_ages);
        }

        // Series filter
        if (!empty($filter_series) && is_array($filter_series)) {
            $placeholders = implode(',', array_fill(0, count($filter_series), '?'));
            $where_clauses[] = "EXISTS (SELECT 1 FROM attributes a WHERE a.product_id = p.id AND BINARY a.name = 'Серия' AND BINARY a.value IN ($placeholders))";
            $bindings = array_merge($bindings, $filter_series);
        }

        // Product types filter
        if (!empty($filter_product_types) && is_array($filter_product_types)) {
            $placeholders = implode(',', array_fill(0, count($filter_product_types), '?'));
            $where_clauses[] = "EXISTS (SELECT 1 FROM attributes a WHERE a.product_id = p.id AND BINARY a.name = 'Тип товара' AND BINARY a.value IN ($placeholders))";
            $bindings = array_merge($bindings, $filter_product_types);
        }

        // Topics filter
        if (!empty($filter_topics) && is_array($filter_topics)) {
            $placeholders = implode(',', array_fill(0, count($filter_topics), '?'));
            $where_clauses[] = "EXISTS (SELECT 1 FROM attributes a WHERE a.product_id = p.id AND BINARY a.name = 'Тематика' AND BINARY a.value IN ($placeholders))";
            $bindings = array_merge($bindings, $filter_topics);
        }

        $where_sql = implode(' AND ', $where_clauses);

        $query_sql = "SELECT COUNT(DISTINCT p.id) as total
                      FROM products p
                      LEFT JOIN prices pr ON p.id = pr.product_id AND pr.price_type_id = '000000002'
                      WHERE $where_sql";

        $result = DB::select($query_sql, $bindings);

        return $result[0]->total ?? 0;
    }

    /**
     * Получить URL изображения товара
     * Адаптировано из legacy/site/functions/getProductImageUrl.php
     *
     * @param string $product_id ID товара (offer_id)
     * @return string URL изображения товара
     */
    private function getProductImageUrl($product_id)
    {
        if (empty($product_id)) {
            return '/assets/img/product_empty.jpg';
        }

        // Проверяем наличие товара в v_products_o_products
        $ozon_product = DB::selectOne("SELECT product_id FROM v_products_o_products WHERE offer_id = ?", [$product_id]);

        if ($ozon_product && !empty($ozon_product->product_id)) {
            // Товар найден в v_products_o_products, получаем primary_image из o_products
            $o_image = DB::selectOne("SELECT * FROM o_images WHERE product_id = ? AND image_order = 0", [$ozon_product->product_id]);

            if ($o_image) {
                // Возвращаем адрес изображения из o_images
                return "/o_images/".$o_image->product_id."/0.jpg";
            }
        }

        // Если товара нет в v_products_o_products, используем стандартный путь
        return '/import_files/' . $product_id . 'b.jpg';
    }

    /**
     * Получить рейтинг и количество отзывов для товара.
     * Адаптировано из legacy/site/modules/sfera/catalog/catalog.class.php
     *
     * @param string $product_id ID товара (offer_id)
     * @return array ['rating' => float, 'reviews_count' => int]
     */
    private function getProductRating($product_id)
    {
        if (empty($product_id)) {
            return ['rating' => 0, 'reviews_count' => 0];
        }

        $rating = 0;
        $reviews_count = 0;

        // Проверяем наличие товара в v_products_o_products для получения SKU
        $ozon_product = DB::selectOne("SELECT sku FROM v_products_o_products WHERE offer_id = ?", [$product_id]);

        if ($ozon_product && !empty($ozon_product->sku)) {
            $sku = intval($ozon_product->sku);
            $rating_data = DB::selectOne("SELECT AVG(rating) as avg_rating, COUNT(*) as total_count FROM o_reviews WHERE sku = ? AND rating IS NOT NULL", [$sku]);

            if ($rating_data) {
                $rating = round($rating_data->avg_rating, 1);
                $reviews_count = intval($rating_data->total_count);
            }
        }

        return ['rating' => $rating, 'reviews_count' => $reviews_count];
    }

    /**
     * Рекурсивно получает все ID подкатегорий для заданной категории, включая саму категорию.
     * Адаптировано из legacy/site/modules/sfera/catalog/catalog.class.php -> get_subcategies_ids
     *
     * @param string $category_id ID родительской категории.
     * @return array Массив ID категорий.
     */
    private function get_subcategories_ids($category_id)
    {
        $ids = [$category_id];

        $children = DB::select("SELECT id FROM tree WHERE parent_id = ?", [$category_id]);

        foreach ($children as $child) {
            $ids = array_merge($ids, $this->get_subcategories_ids($child->id));
        }

        return $ids;
    }

    /**
     * Рекурсивно получает список категорий для меню.
     * Адаптировано из legacy/site/modules/sfera/catalog/catalog.class.php -> getCategories
     *
     * @param string|null $parent_id ID родительской категории. Если null, возвращает корневые категории.
     * @return array Массив категорий с их дочерними элементами.
     */
    private function getCategories($parent_id = null)
    {
        $where_clause = "";
        $bindings = [];

        if (empty($parent_id)) {
            // Корневые категории
            $where_clause = "(parent_id = '' OR parent_id IS NULL)";
        } else {
            $where_clause = "parent_id = ?";
            $bindings[] = $parent_id;
        }

        $result = DB::select("SELECT id, name, parent_id FROM tree WHERE $where_clause ORDER BY name ASC", $bindings);

        $categories = [];
        foreach ($result as $row) {
            $category = [
                'id' => $row->id,
                'name' => $row->name,
                'parent_id' => $row->parent_id,
                'children' => $this->getCategories($row->id) // Рекурсивный вызов для дочерних категорий
            ];
            $categories[] = $category;
        }

        return $categories;
    }
    
    /**
     * Отображение списка всех авторов
     */
    public function authors()
    {
        // Получаем всех уникальных авторов из таблицы authors
        $authors = DB::table('authors')
            ->select('author_name')
            ->distinct()
            ->orderBy('author_name', 'ASC')
            ->get();
        
        // Формируем массив авторов с их slug для ссылок
        $authorsList = [];
        foreach ($authors as $author) {
            $authorsList[] = [
                'name' => $author->author_name,
                'slug' => $this->generateSlug($author->author_name)
            ];
        }
        
        return view('authors', [
            'authors' => $authorsList
        ]);
    }
    
    /**
     * Отображение страницы автора с его книгами
     */
    public function author($slug)
    {
        // Декодируем slug обратно в имя автора
        $authorName = $this->decodeSlug($slug);
        
        // Получаем информацию об авторе
        $authorInfo = DB::table('authors')
            ->where('author_name', $authorName)
            ->first();
        
        if (!$authorInfo) {
            abort(404);
        }
        
        // Получаем все книги автора
        $products = DB::table('authors as a')
            ->join('products as p', 'a.product_id', '=', 'p.id')
            ->leftJoin('prices as pr', function($join) {
                $join->on('p.id', '=', 'pr.product_id')
                     ->where('pr.price_type_id', '=', '000000002');
            })
            ->where('a.author_name', $authorName)
            ->select('p.id', 'p.name', 'p.description', 'p.quantity', 'pr.price')
            ->orderBy('p.name', 'ASC')
            ->get();
        
        // Формируем массив книг для отображения
        $booksList = [];
        foreach ($products as $product) {
            $ratingData = $this->getProductRating($product->id);
            
            $booksList[] = [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'image' => $this->getProductImageUrl($product->id),
                'price' => round($product->price ?? 0),
                'quantity' => intval($product->quantity ?? 0),
                'rating' => $ratingData['rating'],
                'reviews_count' => $ratingData['reviews_count']
            ];
        }
        
        return view('author', [
            'authorName' => $authorName,
            'books' => $booksList
        ]);
    }
    
    /**
     * Генерация slug из имени автора
     */
    private function generateSlug($name)
    {
        return urlencode($name);
    }
    
    /**
     * Декодирование slug обратно в имя
     */
    private function decodeSlug($slug)
    {
        return urldecode($slug);
    }

    /**
     * Конвертация массива ID в массив значений из представления
     * 
     * @param array $ids Массив ID
     * @param string $viewName Имя представления (v_seriya, v_tip_tovara, v_tematika)
     * @return array Массив значений
     */
    private function convertIdsToValues(array $ids, string $viewName): array
    {
        if (empty($ids)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $result = DB::select("SELECT value FROM {$viewName} WHERE id IN ({$placeholders})", $ids);

        return array_map(fn($row) => $row->value, $result);
    }
}
