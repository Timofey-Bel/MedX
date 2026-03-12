<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\ProductService;

/**
 * AuthorController - Контроллер для работы со страницами авторов
 * 
 * Миграция из legacy системы: site/modules/sfera/authors/ и site/modules/sfera/author/
 * Обеспечивает отображение списка авторов и страниц с книгами конкретного автора
 * 
 * Требования: 1.1-1.7, 2.1-2.4
 */
class AuthorController extends Controller
{
    /**
     * Отобразить список всех авторов
     * 
     * Логика работы:
     * 1. Получаем всех авторов с количеством книг из таблицы authors
     * 2. Группируем авторов по первой букве для удобного отображения
     * 3. Сортируем по алфавиту
     * 4. Передаем данные в Blade шаблон
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            // Получаем всех авторов с количеством книг, отсортированных по алфавиту
            $authors = DB::select("
                SELECT 
                    author_name,
                    COUNT(*) AS cnt
                FROM authors a
                GROUP BY author_name
                ORDER BY author_name ASC
            ");

            // Форматируем данные авторов
            $formattedAuthors = [];
            foreach ($authors as $author) {
                $formattedAuthors[] = [
                    'name' => $author->author_name,
                    'count' => (int)$author->cnt
                ];
            }

            // Группируем авторов по первой букве для удобного отображения
            $groupedAuthors = [];
            foreach ($formattedAuthors as $author) {
                if (!empty($author['name'])) {
                    $firstLetter = mb_strtoupper(mb_substr($author['name'], 0, 1, 'UTF-8'), 'UTF-8');
                    if (!isset($groupedAuthors[$firstLetter])) {
                        $groupedAuthors[$firstLetter] = [];
                    }
                    $groupedAuthors[$firstLetter][] = $author;
                }
            }

            return view('authors.index', [
                'authors' => $formattedAuthors,
                'groupedAuthors' => $groupedAuthors,
                'pageTitle' => 'Авторы'
            ]);
        } catch (\Exception $e) {
            Log::error('AuthorController::index error', [
                'error' => $e->getMessage()
            ]);
            
            return view('authors.index', [
                'authors' => [],
                'groupedAuthors' => [],
                'pageTitle' => 'Авторы'
            ]);
        }
    }

    /**
     * Отобразить страницу автора с его книгами
     * 
     * Логика работы:
     * 1. Получаем имя автора из параметра slug (новый формат) или author_name (старый формат)
     * 2. Проверяем существование автора в БД
     * 3. Получаем все товары этого автора с ценами и изображениями
     * 4. Получаем рейтинг и количество отзывов для каждого товара
     * 5. Реализуем пагинацию (20 товаров на страницу)
     * 6. Передаем данные в Blade шаблон
     * 
     * @param Request $request
     * @param string|null $slug Имя автора из URL (опционально)
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function show(Request $request, ?string $slug = null)
    {
        // Получаем имя автора из slug (новый формат) или GET параметра author_name (старый формат)
        $authorName = $slug ?? $request->query('author_name', '');
        
        // Декодируем URL-encoded строку
        if (!empty($authorName)) {
            $authorName = urldecode($authorName);
        }
        
        if (empty($authorName)) {
            // Если имя автора не указано, перенаправляем на страницу авторов
            return redirect('/authors');
        }
        
        try {
            // Проверяем, существует ли автор
            $authorInfo = DB::selectOne("
                SELECT 
                    author_name,
                    COUNT(*) AS cnt
                FROM authors
                WHERE author_name = ?
                GROUP BY author_name
            ", [$authorName]);
            
            if (!$authorInfo) {
                // Автор не найден - показываем 404
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
                INNER JOIN authors a ON p.id = a.product_id
                WHERE a.author_name = ?
            ", [$authorName]);
            
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
                INNER JOIN authors a ON p.id = a.product_id
                LEFT JOIN prices pr ON p.id = pr.product_id AND pr.price_type_id = '000000002'
                WHERE a.author_name = ?
                ORDER BY p.name ASC
                LIMIT ? OFFSET ?
            ", [$authorName, $limit, $offset]);
            
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
            
            return view('authors.show', [
                'author' => [
                    'name' => $authorInfo->author_name,
                    'count' => (int)$authorInfo->cnt
                ],
                'products' => $formattedProducts,
                'total' => $total,
                'page' => $page,
                'pages' => $pages,
                'startPage' => $startPage,
                'endPage' => $endPage,
                'pageTitle' => 'Книги автора: ' . $authorInfo->author_name,
                'cart' => $cart,
                'favorites' => $favorites
            ]);
        } catch (\Exception $e) {
            Log::error('AuthorController::show error', [
                'author_name' => $authorName,
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
            Log::error('AuthorController::getProductImageUrl error', [
                'product_id' => $productId,
                'error' => $e->getMessage()
            ]);
            return '/assets/img/product_empty.jpg';
        }
    }
}
