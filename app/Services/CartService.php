<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

/**
 * CartService - Сервис для работы с корзиной
 * 
 * Миграция из legacy системы: обеспечивает совместимость с Knockout.js моделью
 * Формирует структуру данных в формате, ожидаемом legacy моделью корзины
 */
class CartService
{
    /**
     * Получить данные корзины в формате для Knockout.js модели
     * 
     * @param array $cart Корзина из сессии (формат: [guid => quantity])
     * @return array Структура данных корзины для модели
     */
    public function getCartData(array $cart = null): array
    {
        if ($cart === null) {
            $cart = Session::get('cart', []);
        }

        // Инициализация пустой структуры
        $cartData = [
            'items' => [],
            'total_cart_sum' => 0,
            'total_cart_amount' => 0,
            'cart_sum' => 0,
            'cart_discount' => Session::get('cart_discount', 0),
            'promocode' => Session::get('cart_promocode', '')
        ];

        if (empty($cart)) {
            return $cartData;
        }

        // Получаем ID товаров из корзины (guid = product_id)
        $productIds = array_keys($cart);

        // Загружаем информацию о товарах из БД
        $products = DB::table('products as p')
            ->leftJoin('prices as pr', function($join) {
                $join->on('p.id', '=', 'pr.product_id')
                     ->where('pr.price_type_id', '=', '000000002');
            })
            ->whereIn('p.id', $productIds)
            ->select('p.id', 'p.name', 'p.description', 'pr.price', 'p.quantity')
            ->get();

        $totalSum = 0;
        $totalAmount = 0;

        // Формируем структуру items в формате {guid: {id, name, cost, product_amount, ...}}
        foreach ($products as $product) {
            $guid = $product->id;
            $quantity = $cart[$guid] ?? 1;
            $price = round($product->price ?? 0);
            
            // Получаем URL изображения товара
            $imgUrl = $this->getProductImageUrl($product->id);

            // Читаем состояние selected из сессии (сохраняется методом updateItemSelected)
            // Если товар не найден в cart_selected, по умолчанию он выбран (true)
            $cartSelected = Session::get('cart_selected', []);

            // Формируем объект товара в формате legacy модели
            $cartData['items'][$guid] = [
                'id' => $product->id,
                'guid' => $guid,
                'name' => $product->name,
                'description' => $product->description,
                'cost' => $price,
                'price' => $price,
                'product_amount' => $quantity,
                'img_url' => $imgUrl,
                'image' => $imgUrl,
                'quantity' => $product->quantity ?? 99, // Максимальное количество на складе
                'max_quantity' => $product->quantity ?? 99,
                'selected' => isset($cartSelected[$guid]) ? $cartSelected[$guid] : true,
                'old_price' => 0 // Старая цена (для скидок)
            ];

            // Учитываем в итоговых суммах только выбранные товары
            $isSelected = isset($cartSelected[$guid]) ? $cartSelected[$guid] : true;
            if ($isSelected) {
                $totalSum += $price * $quantity;
                $totalAmount += $quantity;
            }
        }

        // Вычисляем итоговые суммы
        $cartData['cart_sum'] = $totalSum;
        $cartData['total_cart_sum'] = $totalSum - $cartData['cart_discount'];
        $cartData['total_cart_amount'] = $totalAmount;

        return $cartData;
    }

    /**
     * Добавить товар в корзину
     * 
     * @param string $guid ID товара (GUID)
     * @param int $amount Количество товара
     * @return array Обновленные данные корзины
     */
    public function addItem(string $guid, int $amount = 1): array
    {
        $cart = Session::get('cart', []);

        // Если товар уже в корзине, увеличиваем количество
        if (isset($cart[$guid])) {
            $cart[$guid] += $amount;
        } else {
            $cart[$guid] = $amount;
        }

        Session::put('cart', $cart);

        return $this->getCartData($cart);
    }

    /**
     * Удалить товар из корзины
     * 
     * @param string $guid ID товара (GUID)
     * @return array Обновленные данные корзины
     */
    public function removeItem(string $guid): array
    {
        $cart = Session::get('cart', []);

        if (isset($cart[$guid])) {
            unset($cart[$guid]);
        }

        Session::put('cart', $cart);

        return $this->getCartData($cart);
    }

    /**
     * Изменить количество товара в корзине
     * 
     * @param string $guid ID товара (GUID)
     * @param int $amount Новое количество товара
     * @return array Обновленные данные корзины
     */
    public function updateAmount(string $guid, int $amount): array
    {
        $cart = Session::get('cart', []);

        if ($amount <= 0) {
            // Если количество 0 или меньше, удаляем товар
            return $this->removeItem($guid);
        }

        $cart[$guid] = $amount;
        Session::put('cart', $cart);

        return $this->getCartData($cart);
    }

    /**
     * Обновить выбор товара (selected)
     * 
     * @param string $guid ID товара (GUID)
     * @param bool $selected Выбран ли товар для заказа
     * @return array Обновленные данные корзины
     */
    public function updateItemSelected(string $guid, bool $selected): array
    {
        // Сохраняем состояние selected в отдельной сессионной переменной
        $selectedItems = Session::get('cart_selected', []);
        $selectedItems[$guid] = $selected;
        Session::put('cart_selected', $selectedItems);

        return $this->getCartData();
    }

    /**
     * Применить промокод
     * 
     * @param string $promocode Промокод
     * @return array Результат применения промокода
     */
    public function applyPromocode(string $promocode): array
    {
        // TODO: Реализовать логику проверки и применения промокода
        // Пока просто сохраняем промокод в сессию
        Session::put('cart_promocode', $promocode);
        Session::put('cart_discount', 0); // Скидка по промокоду

        return [
            'success' => false,
            'message' => 'Промокод не найден',
            'cart' => $this->getCartData()
        ];
    }

    /**
     * Отменить промокод
     * 
     * @return array Обновленные данные корзины
     */
    public function cancelPromocode(): array
    {
        Session::forget('cart_promocode');
        Session::forget('cart_discount');

        return $this->getCartData();
    }

    /**
     * Очистить корзину
     * 
     * @return array Пустые данные корзины
     */
    public function clearCart(): array
    {
        Session::forget('cart');
        Session::forget('cart_promocode');
        Session::forget('cart_discount');
        Session::forget('cart_selected');

        return $this->getCartData([]);
    }

    /**
     * Получить URL изображения товара
     * 
     * @param string $productId ID товара
     * @return string URL изображения
     */
    private function getProductImageUrl(string $productId): string
    {
        if (empty($productId)) {
            return '/assets/img/product_empty.jpg';
        }

        // Проверяем связь с Ozon продуктом
        $ozonProduct = DB::table('v_products_o_products')
            ->where('offer_id', $productId)
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

        return "/import_files/{$productId}b.jpg";
    }
}
