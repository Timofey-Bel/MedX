<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\Category;
use App\Services\CartService;
use App\Services\FavoriteService;
use App\Services\ProductService;

class ProductController extends Controller
{
    protected $cartService;
    protected $favoriteService;
    protected $productService;

    public function __construct(
        CartService $cartService, 
        FavoriteService $favoriteService,
        ProductService $productService
    ) {
        $this->cartService = $cartService;
        $this->favoriteService = $favoriteService;
        $this->productService = $productService;
    }

    /**
     * Отображение страницы товара
     * 
     * @param Request $request
     * @param string $slug ID или slug товара
     * @return \Illuminate\View\View
     */
    public function show(Request $request, string $slug)
    {
        try {
            // Получаем товар через сервис
            $product = $this->productService->getProductBySlug($slug);
            
            if (!$product) {
                Log::warning('Product not found', ['slug' => $slug]);
                abort(404);
            }
            
            // Получаем дополнительные данные через сервисы
            $attributes = $this->productService->getProductAttributes($product->id);
            $images = $this->productService->getProductImages($product->id);
            $reviews = $this->productService->getProductReviews($product->id);
            $relatedProducts = $this->productService->getRelatedProducts($product->id);
            $reviewsStats = $this->productService->getProductRating($product->id);
            
            // Формируем breadcrumbs
            $breadcrumbs = $this->buildBreadcrumbs($product);
            
            // Формируем SEO метаданные
            $seoData = $this->buildSeoData($product, $reviewsStats);
            
            // ОБЯЗАТЕЛЬНО получаем корзину и избранное из сессии
            $cart = session('cart', ['items' => []]);
            
            // КРИТИЧЕСКИ ВАЖНО: Преобразование формата избранного для неавторизованных пользователей
            // В FavoriteController::add() для неавторизованных избранное сохраняется как простой массив ID:
            // session(['favorites' => ['00-00006779', '00-00006780']])
            // 
            // Но в шаблонах проверяется структура $favorites['items'][$product['id']], то есть ожидается:
            // ['items' => ['00-00006779' => true, '00-00006780' => true]]
            //
            // Поэтому ОБЯЗАТЕЛЬНО преобразуем формат:
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
            
            return view('product.show', compact(
                'product',
                'attributes',
                'images',
                'reviews',
                'relatedProducts',
                'reviewsStats',
                'breadcrumbs',
                'seoData',
                'cart',
                'favorites'
            ));
        } catch (\Exception $e) {
            Log::error('ProductController::show error', [
                'slug' => $slug,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            abort(500);
        }
    }

    /**
     * Формирование breadcrumbs для товара
     * 
     * @param object $product Объект товара
     * @return array Массив breadcrumbs с полями title и url
     */
    private function buildBreadcrumbs(object $product): array
    {
        $breadcrumbs = [];
        
        // Главная страница
        $breadcrumbs[] = ['title' => 'Главная', 'url' => '/'];
        
        // Каталог
        $breadcrumbs[] = ['title' => 'Каталог', 'url' => '/catalog'];
        
        // Категория товара (если есть)
        if (!empty($product->category_id)) {
            try {
                $category = DB::selectOne(
                    "SELECT id, name FROM tree WHERE BINARY id = BINARY ? LIMIT 1", 
                    [$product->category_id]
                );
                
                if ($category) {
                    $breadcrumbs[] = [
                        'title' => $category->name, 
                        'url' => '/catalog/' . $category->id
                    ];
                }
            } catch (\Exception $e) {
                Log::warning('Failed to get category for breadcrumbs', [
                    'category_id' => $product->category_id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        // Текущий товар (без ссылки)
        $breadcrumbs[] = ['title' => $product->name, 'url' => null];
        
        return $breadcrumbs;
    }

    /**
     * Формирование SEO метаданных
     * 
     * @param object $product Объект товара
     * @param array $reviewsStats Статистика отзывов
     * @return array Массив SEO данных
     */
    private function buildSeoData(object $product, array $reviewsStats): array
    {
        // Название сайта (можно вынести в конфиг)
        $siteName = config('app.name', 'Книжный магазин');
        
        // Title страницы
        $title = $product->name . ' - ' . $siteName;
        
        // Meta description (до 160 символов)
        $description = $product->description ?? $product->name;
        $description = strip_tags($description);
        $description = mb_substr($description, 0, 160, 'UTF-8');
        if (mb_strlen($product->description ?? '', 'UTF-8') > 160) {
            $description .= '...';
        }
        
        // URL товара
        $productUrl = url('/product/' . $product->id);
        
        // Изображение товара
        $productImage = $this->productService->getProductImageUrl($product->id);
        $productImageUrl = url($productImage);
        
        // Open Graph теги
        $ogTitle = $product->name;
        $ogDescription = $description;
        $ogImage = $productImageUrl;
        $ogUrl = $productUrl;
        
        // Schema.org разметка
        $schema = [
            '@context' => 'https://schema.org/',
            '@type' => 'Product',
            'name' => $product->name,
            'image' => $productImageUrl,
            'description' => $description,
            'sku' => $product->sku ?? $product->id,
            'offers' => [
                '@type' => 'Offer',
                'url' => $productUrl,
                'priceCurrency' => 'RUB',
                'price' => $product->price,
                'availability' => $product->in_stock 
                    ? 'https://schema.org/InStock' 
                    : 'https://schema.org/OutOfStock'
            ]
        ];
        
        // Добавляем бренд если есть
        if (!empty($product->brand)) {
            $schema['brand'] = [
                '@type' => 'Brand',
                'name' => $product->brand
            ];
        }
        
        // Добавляем aggregateRating если есть отзывы
        if ($reviewsStats['total_count'] > 0) {
            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $reviewsStats['average_rating'],
                'reviewCount' => $reviewsStats['total_count']
            ];
        }
        
        return [
            'title' => $title,
            'description' => $description,
            'og_title' => $ogTitle,
            'og_description' => $ogDescription,
            'og_image' => $ogImage,
            'og_url' => $ogUrl,
            'schema' => $schema
        ];
    }
}