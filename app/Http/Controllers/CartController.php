<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\CartService;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }
    /**
     * Отображение корзины
     * Загружает товары из сессии и отображает их с общей суммой
     */
    public function index()
    {
        // Получаем корзину из сессии (формат: [product_id => quantity])
        $cart = session('cart', []);
        
        $cartItems = [];
        $totalPrice = 0;
        
        if (!empty($cart)) {
            // Получаем ID товаров из корзины
            $productIds = array_keys($cart);
            
            // Загружаем информацию о товарах из БД
            $products = DB::table('products as p')
                ->leftJoin('prices as pr', function($join) {
                    $join->on('p.id', '=', 'pr.product_id')
                         ->where('pr.price_type_id', '=', '000000002');
                })
                ->whereIn('p.id', $productIds)
                ->select('p.id', 'p.name', 'p.description', 'pr.price')
                ->get();
            
            // Формируем массив товаров для отображения
            foreach ($products as $product) {
                $quantity = $cart[$product->id] ?? 1;
                $price = round($product->price ?? 0);
                $subtotal = $price * $quantity;
                
                $cartItems[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'image' => $this->getProductImageUrl($product->id),
                    'price' => $price,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal
                ];
                
                $totalPrice += $subtotal;
            }
        }
        
        return view('cart', [
            'cartItems' => $cartItems,
            'totalPrice' => $totalPrice,
            'isEmpty' => empty($cartItems)
        ]);
    }
    
    /**
     * Получить URL изображения товара
     */
    private function getProductImageUrl($product_id)
    {
        if (empty($product_id)) {
            return '/assets/img/product_empty.jpg';
        }
        
        $ozonProduct = DB::table('v_products_o_products')
            ->where('offer_id', $product_id)
            ->first();
        
        if ($ozonProduct && !empty($ozonProduct->product_id)) {
            $oImage = DB::table('o_images')
                ->where('product_id', $ozonProduct->product_id)
                ->where('image_order', 0)
                ->first();
            
            if ($oImage) {
                return "/o_images/{$oImage->product_id}/0.jpg";
            }
        }
        
        return "/import_files/{$product_id}b.jpg";
    }

    /**
     * Обработка AJAX запросов корзины через API
     * Используется из routes/api.php - НЕ требует CSRF токен
     * Единый endpoint для всех операций с корзиной (legacy совместимость)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleAjax(Request $request)
    {
        $task = $request->input('task');

        switch ($task) {
            case 'put_item':
                return $this->putItem($request);
            
            case 'delete_product':
                return $this->deleteProduct($request);
            
            case 'update_amount':
                return $this->updateAmount($request);
            
            case 'update_item':
                return $this->updateItem($request);
            
            case 'get_cart':
                return $this->getCart($request);
            
            case 'apply_promocode':
                return $this->applyPromocode($request);
            
            case 'cancel_promocode':
                return $this->cancelPromocode($request);
            
            case 'update_item_selected':
                return $this->updateItemSelected($request);
            
            default:
                return response()->json([
                    'error' => 'Unknown task',
                    'task' => $task
                ], 400);
        }
    }

    /**
     * Добавить товар в корзину (task=put_item)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function putItem(Request $request)
    {
        $itemJson = $request->input('item');
        $item = json_decode($itemJson, true);

        if (!$item || !isset($item['guid'])) {
            return response()->json([
                'error' => 'Invalid item data'
            ], 400);
        }

        $guid = $item['guid'];
        $amount = $item['product_amount'] ?? 1;

        $cartData = $this->cartService->addItem($guid, $amount);

        return response()->json($cartData);
    }

    /**
     * Удалить товар из корзины (task=delete_product)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function deleteProduct(Request $request)
    {
        $guid = $request->input('guid');

        if (!$guid) {
            return response()->json([
                'error' => 'Missing guid parameter'
            ], 400);
        }

        $cartData = $this->cartService->removeItem($guid);

        return response()->json($cartData);
    }

    /**
     * Изменить количество товара (task=update_amount)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function updateAmount(Request $request)
    {
        $guid = $request->input('guid');
        $amount = $request->input('amount', 1);

        if (!$guid) {
            return response()->json([
                'error' => 'Missing guid parameter'
            ], 400);
        }

        $cartData = $this->cartService->updateAmount($guid, $amount);

        return response()->json($cartData);
    }

    /**
     * Обновить товар в корзине (task=update_item)
     * Устанавливает количество товара (не добавляет, а заменяет)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function updateItem(Request $request)
    {
        $itemJson = $request->input('item');
        $item = json_decode($itemJson, true);

        if (!$item || !isset($item['guid'])) {
            return response()->json([
                'error' => 'Invalid item data'
            ], 400);
        }

        $guid = $item['guid'];
        $amount = $item['product_amount'] ?? 1;

        // Используем updateAmount для установки количества
        $cartData = $this->cartService->updateAmount($guid, $amount);

        return response()->json($cartData);
    }

    /**
     * Получить состояние корзины (task=get_cart)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function getCart(Request $request)
    {
        $cartData = $this->cartService->getCartData();

        return response()->json($cartData);
    }

    /**
     * Применить промокод (task=apply_promocode)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function applyPromocode(Request $request)
    {
        $promocode = $request->input('promocode');

        if (!$promocode) {
            return response()->json([
                'error' => 'Missing promocode parameter'
            ], 400);
        }

        $result = $this->cartService->applyPromocode($promocode);

        return response()->json($result);
    }

    /**
     * Отменить промокод (task=cancel_promocode)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function cancelPromocode(Request $request)
    {
        $cartData = $this->cartService->cancelPromocode();

        return response()->json($cartData);
    }

    /**
     * Обновить выбор товара (task=update_item_selected)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function updateItemSelected(Request $request)
    {
        $guid = $request->input('guid');
        $selected = $request->input('selected', true);

        if (!$guid) {
            return response()->json([
                'error' => 'Missing guid parameter'
            ], 400);
        }

        $cartData = $this->cartService->updateItemSelected($guid, $selected);

        return response()->json($cartData);
    }
}
