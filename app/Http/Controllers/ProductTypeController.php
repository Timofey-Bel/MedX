<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\ProductService;

/**
 * ProductTypeController - Контроллер для работы со страницами типов товаров
 * 
 * Миграция из legacy системы: site/modules/sfera/product_types/ и site/modules/sfera/product_type/
 * Обеспечивает отображение списка типов товаров и страниц с товарами конкретного типа
 * 
 * Требования: 1.1-1.7, 2.1-2.4
 */
class ProductTypeController extends Controller
{
    /**
     * Отобразить список всех типов товаров
     * 
     * Логика работы:
     * 1. Получаем все типы товаров с количеством товаров из view v_tip_tovara
     * 2. Группируем типы по первой букве для удобного отображения
     * 3. Сортируем по алфавиту
     * 4. Передаем данные в Blade шаблон
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            // Получаем все типы товаров с количеством товаров, отсортированных по алфавиту
            $productTypes = DB::select("
                SELECT 
                    id,
                    value AS name,
                    cnt AS count
                FROM v_tip_tovara
                ORDER BY value ASC
            ");

            // Форматируем данные типов товаров
            $formattedProductTypes = [];
            foreach ($productTypes as $productType) {
                $formattedProductTypes[] = [
                    'id' => (int)$productType->id,
                    'name' => $productType->name,
                    'count' => (int)$productType->count
                ];
            }

            // Группируем типы товаров по первой букве для удобного отображения
            $groupedProductTypes = [];
            foreach ($formattedProductTypes as $productType) {
                if (!empty($productType['name'])) {
                    $firstLetter = mb_strtoupper(mb_substr($productType['name'], 0, 1, 'UTF-8'), 'UTF-8');
                    // Если первая буква не кириллица и не латиница, используем "#"
                    if (!preg_match('/[А-ЯЁA-Z]/u', $firstLetter)) {
                        $firstLetter = '#';
                    }
                    if (!isset($groupedProductTypes[$firstLetter])) {
                        $groupedProductTypes[$firstLetter] = [];
                    }
                    $groupedProductTypes[$firstLetter][] = $productType;
                }
            }

            // Сортируем группы по алфавиту
            ksort($groupedProductTypes);

            return view('product-types.index', [
                'productTypes' => $formattedProductTypes,
                'groupedProductTypes' => $groupedProductTypes,
                'pageTitle' => 'Типы товаров'
            ]);
        } catch (\Exception $e) {
            Log::error('ProductTypeController::index error', [
                'error' => $e->getMessage()
            ]);
            
            return view('product-types.index', [
                'productTypes' => [],
                'groupedProductTypes' => [],
                'pageTitle' => 'Типы товаров'
            ]);
        }
    }

    /**
     * Отобразить страницу типа товара с его товарами
     * 
     * Логика работы:
     * 1. Получаем ID типа товара из параметра slug (новый формат) или product_type_id (старый формат)
     * 2. Проверяем существование типа товара в БД
     * 3. Получаем все товары этого типа с ценами и изображениями
     * 4. Получаем рейтинг и количество отзывов для каждого товара
     * 5. Реализуем пагинацию (20 товаров на страницу)
     * 6. Передаем данные в Blade шаблон
     * 
     * @param Request $request
     * @param string|null $slug ID типа товара из URL (опционально)
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function show(Request $request, ?string $slug = null)
    {
        // Получаем ID типа товара из slug (новый формат) или GET параметра product_type_id (старый формат)
        $productTypeId = (int)($slug ?? $request->query('product_type_id', 0));
        
        if ($productTypeId == 0) {
            // Если ID типа товара не указан, перенаправляем на страницу типов товаров
            return redirect('/product_types');
        }
        
        try {
            // Проверяем, существует ли тип товара
            $productTypeInfo = DB::selectOne("
                SELECT 
                    id,
                    value AS name,
                    cnt AS count
                FROM v_tip_tovara
                WHERE id = ?
                LIMIT 1
            ", [$productTypeId]);
            
            if (!$productTypeInfo) {
                // Тип товара не найден - показываем 404
                abort(404);
            }
            
            // Получение параметров пагинации
            $page = max(1, (int)$request->query('page', 1));
            $limit = 20;
            $offset = ($page - 1) * $limit;
            
            // Получаем общее количество товаров
            $totalResult = DB::selectOne("
                SELECT COUNT(DISTINCT p.id) as total
                FROM products p
                INNER JOIN attributes a ON p.id = a.product_id
                WHERE BINARY a.name = 'Тип товара'
                  AND BINARY a.value = BINARY ?
            ", [$productTypeInfo->name]);
            
            $total = $totalResult ? (int)$totalResult->total : 0;
            
            // Получаем товары с пагинацией
            $products = DB::select("
                SELECT DISTINCT 
                    p.id, 
                    p.name, 
                    p.description, 
                    p.picture, 
                    p.category_id, 
                    p.quantity, 
                    pr.price as product_price
                FROM products p
                INNER JOIN attributes a ON p.id = a.product_id
                LEFT JOIN prices pr ON p.id = pr.product_id AND pr.price_type_id = '000000002'
                WHERE BINARY a.name = 'Тип товара'
                  AND BINARY a.value = BINARY ?
                ORDER BY p.name ASC
                LIMIT ? OFFSET ?
            ", [$productTypeInfo->name, $limit, $offset]);
            
            // Форматируем данные товаров
            $productService = app(ProductService::class);
            $formattedProducts = [];
            
            foreach ($products as $product) {
                $productId = $product->id;
                
                // Получаем рейтинг и количество отзывов
                $ratingData = $productService->getProductRating($productId);
                
                // Получаем URL изображения
                $imageUrl = $this->getProductImageUrl($productId);
                
                $formattedProducts[] = [
                    'id' => $productId,
                    'name' => $product->name ?? '',
                    'description' => $product->description ?? '',
                    'image' => $imageUrl,
                    'category_id' => $product->category_id ?? '',
                    'price' => isset($product->product_price) && $product->product_price > 0 ? round($product->product_price) : 0,
                    'quantity' => isset($product->quantity) ? (int)$product->quantity : 99,
                    'rating' => $ratingData['average_rating'],
                    'reviews_count' => $ratingData['total_count']
                ];
            }
            
            // Вычисляем пагинацию
            $pages = $total > 0 ? ceil($total / $limit) : 1;
            $startPage = max(1, $page - 2);
            $endPage = min($pages, $page + 2);
            
            // Получаем данные корзины и избранного из сессии для отображения состояния
            $cart = session('cart', ['items' => []]);
            
            // Для избранного: получаем массив ID и преобразуем в формат ['items' => [id => true]]
            $favoritesArray = session('favorites', []);
            $favorites = ['items' => []];
            foreach ($favoritesArray as $productId) {
                $favorites['items'][$productId] = true;
            }
            
            return view('product-types.show', [
                'productType' => [
                    'id' => (int)$productTypeInfo->id,
                    'name' => $productTypeInfo->name,
                    'count' => (int)$productTypeInfo->count
                ],
                'products' => $formattedProducts,
                'total' => $total,
                'page' => $page,
                'pages' => $pages,
                'startPage' => $startPage,
                'endPage' => $endPage,
                'pageTitle' => 'Товары типа: ' . $productTypeInfo->name,
                'cart' => $cart,
                'favorites' => $favorites
            ]);
        } catch (\Exception $e) {
            Log::error('ProductTypeController::show error', [
                'product_type_id' => $productTypeId,
                'error' => $e->getMessage()
            ]);
            
            abort(500);
        }
    }

    /**
     * Получить URL изображения товара
     * 
     * Логика работы:
     * 1. Проверяем наличие товара в v_products_o_products
     * 2. Если найден, получаем изображение из o_images (image_order = 0)
     * 3. Если не найден, используем стандартный путь /import_files/
     * 4. Если товар не найден вообще, возвращаем заглушку
     * 
     * @param string $productId ID товара (offer_id)
     * @return string URL изображения
     */
    private function getProductImageUrl(string $productId): string
    {
        if (empty($productId)) {
            return '/assets/img/product_empty.jpg';
        }

        try {
            // Проверяем наличие товара в v_products_o_products
            $ozonProduct = DB::selectOne(
                "SELECT product_id FROM v_products_o_products WHERE offer_id = ? LIMIT 1",
                [$productId]
            );

            if ($ozonProduct && !empty($ozonProduct->product_id)) {
                // Товар найден в v_products_o_products, получаем изображение из o_images
                $oImage = DB::selectOne(
                    "SELECT product_id FROM o_images WHERE product_id = ? AND image_order = 0 LIMIT 1",
                    [$ozonProduct->product_id]
                );

                if ($oImage) {
                    return "/o_images/{$oImage->product_id}/0.jpg";
                }
            }

            // Если товара нет в v_products_o_products, используем стандартный путь
            return "/import_files/{$productId}b.jpg";
        } catch (\Exception $e) {
            Log::error('ProductTypeController::getProductImageUrl error', [
                'product_id' => $productId,
                'error' => $e->getMessage()
            ]);
            return '/assets/img/product_empty.jpg';
        }
    }
}
