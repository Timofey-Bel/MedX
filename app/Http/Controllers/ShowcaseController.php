<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\ProductService;

/**
 * Контроллер главной страницы (витрины)
 * 
 * Миграция из legacy: legacy/site/modules/sfera/showcase/showcase.class.php
 * 
 * Главная страница содержит:
 * - Тройной карусель (главный + 2 боковых)
 * - Популярные категории
 * - TOP-10 товаров
 * - Отзывы о товарах
 * - Новинки (случайные товары)
 */
class ShowcaseController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Отображение главной страницы
     */
    public function index(Request $request)
    {
        // Получаем данные корзины и избранного из сессии
        // Миграция из legacy: $_SESSION['cart'] и $_SESSION['favorites']
        $cart = $request->session()->get('cart', ['items' => []]);
        
        // КРИТИЧЕСКИ ВАЖНО: Преобразование формата избранного для неавторизованных пользователей
        // В FavoriteController::add() для неавторизованных избранное сохраняется как простой массив ID:
        // session(['favorites' => ['00-00006779', '00-00006780']])
        // 
        // Но в шаблонах проверяется структура $favorites['items'][$product['id']], то есть ожидается:
        // ['items' => ['00-00006779' => true, '00-00006780' => true]]
        //
        // Поэтому ОБЯЗАТЕЛЬНО преобразуем формат:
        $sessionFavorites = $request->session()->get('favorites', []);
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

        // Получаем данные для всех модулей
        $mainBanners = $this->getMainCarouselBanners();
        $productCarousel = $this->getProductCarouselData();
        $promoCarousel = $this->getPromoCarouselData();
        $popularCategories = $this->getPopularCategories();
        $top10Products = $this->getTop10Products();
        $productReviews = $this->getProductReviews();
        $randomProducts = $this->getRandomProducts();

        return view('showcase.index', [
            'mainBanners' => $mainBanners,
            'productCarousel' => $productCarousel,
            'promoCarousel' => $promoCarousel,
            'popularCategories' => $popularCategories,
            'top10Products' => $top10Products,
            'productReviews' => $productReviews,
            'randomProducts' => $randomProducts,
            'cart' => $cart,
            'favorites' => $favorites
        ]);
    }

    /**
     * Получить баннеры для главного карусели
     * Миграция из legacy: legacy/site/modules/sfera/main_carousel/main_carousel.class.php
     * 
     * @return \Illuminate\Support\Collection
     */
    protected function getMainCarouselBanners()
    {
        try {
            return DB::table('banners')
                ->orderBy('sort', 'asc')
                ->get();
        } catch (\Exception $e) {
            Log::error('ShowcaseController::getMainCarouselBanners error', [
                'error' => $e->getMessage()
            ]);
            return collect([]);
        }
    }

    /**
     * Получить товары для карусели товаров
     * Миграция из legacy: legacy/site/modules/sfera/product_carousel/product_carousel.class.php
     * 
     * @return \Illuminate\Support\Collection
     */
    protected function getProductCarouselData()
    {
        try {
            $products = DB::table('products as p')
                ->leftJoin('prices as pr', function($join) {
                    $join->on('p.id', '=', 'pr.product_id')
                         ->where('pr.price_type_id', '=', '000000002');
                })
                ->where('p.is_new', 1)
                ->select('p.id', 'p.name', 'p.picture', 'pr.price as product_price')
                ->inRandomOrder()
                ->limit(3)
                ->get();

            // Добавляем изображения для каждого товара
            foreach ($products as $product) {
                $product->image = $this->productService->getProductImageUrl($product->id);
            }

            return $products;
        } catch (\Exception $e) {
            Log::error('ShowcaseController::getProductCarouselData error', [
                'error' => $e->getMessage()
            ]);
            return collect([]);
        }
    }

    /**
     * Получить данные для промо-карусели
     * Миграция из legacy: legacy/site/modules/sfera/promo_carousel/promo_carousel.class.php
     * 
     * Примечание: Используем те же товары, что и в product_carousel
     * 
     * @return \Illuminate\Support\Collection
     */
    protected function getPromoCarouselData()
    {
        try {
            $products = DB::table('products as p')
                ->leftJoin('prices as pr', function($join) {
                    $join->on('p.id', '=', 'pr.product_id')
                         ->where('pr.price_type_id', '=', '000000002');
                })
                ->where('p.is_new', 1)
                ->select('p.id', 'p.name', 'p.picture', 'pr.price as product_price')
                ->inRandomOrder()
                ->limit(3)
                ->get();

            // Добавляем изображения для каждого товара
            foreach ($products as $product) {
                $product->image = $this->productService->getProductImageUrl($product->id);
            }

            return $products;
        } catch (\Exception $e) {
            Log::error('ShowcaseController::getPromoCarouselData error', [
                'error' => $e->getMessage()
            ]);
            return collect([]);
        }
    }

    /**
     * Получить популярные категории
     * Миграция из legacy: legacy/site/modules/sfera/popular_categories/popular_categories.class.php
     * 
     * @return \Illuminate\Support\Collection
     */
    protected function getPopularCategories()
    {
        try {
            $categories = DB::table('popular_categories as pc')
                ->leftJoin('tree as t', 'pc.category_id', '=', 't.id')
                ->where('pc.active', 1)
                ->select('pc.id', 'pc.category_id', 'pc.sort', 'pc.image', 't.name as title', 't.id as guid')
                ->orderBy('pc.sort', 'asc')
                ->get();
            
            // Преобразуем объекты в массивы для совместимости с Blade компонентом
            // ВАЖНО: Явно создаем массив с нужными ключами, так как (array) может не сохранить алиасы
            return $categories->map(function($category) {
                return [
                    'id' => $category->id,
                    'category_id' => $category->category_id,
                    'sort' => $category->sort,
                    'image' => $category->image,
                    'title' => $category->title ?? $category->name ?? 'Категория', // Fallback на name если title не найден
                    'guid' => $category->guid ?? $category->category_id
                ];
            });
        } catch (\Exception $e) {
            Log::error('ShowcaseController::getPopularCategories error', [
                'error' => $e->getMessage()
            ]);
            return collect([]);
        }
    }

    /**
     * Получить TOP-10 товаров
     * Миграция из legacy: legacy/site/modules/sfera/top10_products/top10_products.class.php
     * 
     * @return \Illuminate\Support\Collection
     */
    protected function getTop10Products()
    {
        try {
            $products = DB::select("
                SELECT t.id, t.product_id, t.sort, t.active,
                       p.name as product_name,
                       pr.price as product_price,
                       p.quantity
                FROM top10_products t
                LEFT JOIN products p ON BINARY t.product_id = BINARY p.id
                LEFT JOIN prices pr ON t.product_id = pr.product_id AND pr.price_type_id = '000000002'
                WHERE t.active = 1
                ORDER BY t.sort ASC
                LIMIT 10
            ");

            // Обогащаем данные рейтингами и изображениями
            foreach ($products as $product) {
                $ratingData = $this->productService->getProductRating($product->product_id);
                $product->rating = $ratingData['average_rating'] ?? 0;
                $product->reviews_count = $ratingData['total_count'] ?? 0;
                $product->image_url = $this->productService->getProductImageUrl($product->product_id);
                $product->product_price = $product->product_price ? round($product->product_price) : 0;
                $product->quantity = $product->quantity ?? 99;
            }

            return collect($products);
        } catch (\Exception $e) {
            Log::error('ShowcaseController::getTop10Products error', [
                'error' => $e->getMessage()
            ]);
            return collect([]);
        }
    }

    /**
     * Получить отзывы о товарах
     * Миграция из legacy: legacy/site/modules/sfera/product_reviews/product_reviews.class.php
     * 
     * @return \Illuminate\Support\Collection
     */
    protected function getProductReviews()
    {
        try {
            $reviews = DB::table('product_reviews')
                ->where('active', 1)
                ->orderBy('sort', 'asc')
                ->limit(10)
                ->get();
            
            // Преобразуем объекты в массивы для совместимости с Blade компонентом
            return $reviews->map(function($review) {
                return (array) $review;
            });
        } catch (\Exception $e) {
            Log::error('ShowcaseController::getProductReviews error', [
                'error' => $e->getMessage()
            ]);
            return collect([]);
        }
    }

    /**
     * Получить случайные товары (новинки)
     * Миграция из legacy: legacy/site/modules/sfera/random_products/random_products.class.php
     * 
     * @return \Illuminate\Support\Collection
     */
    protected function getRandomProducts()
    {
        try {
            $products = DB::table('products as p')
                ->leftJoin('prices as pr', function($join) {
                    $join->on('p.id', '=', 'pr.product_id')
                         ->where('pr.price_type_id', '=', '000000002');
                })
                ->where('p.is_new', 1)
                ->select('p.id', 'p.name', 'p.picture', 'p.quantity', 'pr.price')
                ->inRandomOrder()
                ->limit(12)
                ->get();

            // Обогащаем данные рейтингами и изображениями
            foreach ($products as $product) {
                $ratingData = $this->productService->getProductRating($product->id);
                $product->rating = $ratingData['average_rating'] ?? 0;
                $product->reviews_count = $ratingData['total_count'] ?? 0;
                $product->image = $this->productService->getProductImageUrl($product->id);
                $product->price = $product->price ? round($product->price) : 0;
                $product->quantity = $product->quantity ?? 99;
            }

            return $products;
        } catch (\Exception $e) {
            Log::error('ShowcaseController::getRandomProducts error', [
                'error' => $e->getMessage()
            ]);
            return collect([]);
        }
    }
}
