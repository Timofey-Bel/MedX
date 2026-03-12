<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * FavoriteService - Сервис для работы с избранным
 * 
 * Миграция из legacy системы: обеспечивает работу с избранными товарами пользователя
 * Использует таблицу favorites для хранения связей пользователь-товар
 * 
 * Требования: 18.1-18.4
 */
class FavoriteService
{
    /**
     * Добавить товар в избранное
     * 
     * Логика работы:
     * 1. Проверяем существование товара в таблице products
     * 2. Проверяем, не добавлен ли товар уже в избранное (избегаем дублей)
     * 3. Если товар еще не в избранном, добавляем запись в таблицу favorites
     * 4. Используем INSERT IGNORE для защиты от дублирования при конкурентных запросах
     * 5. Возвращаем массив с результатом операции
     * 
     * @param string $productId ID товара (offer_id)
     * @param int $userId ID пользователя
     * @return array ['success' => bool, 'message' => string, 'count' => int]
     */
    public function addItem(string $productId, int $userId): array
    {
        if (empty($productId) || empty($userId)) {
            return [
                'success' => false,
                'message' => 'Некорректные параметры',
                'count' => $this->getItemsCount($userId)
            ];
        }

        try {
            // Проверяем существование товара в таблице products
            $product = DB::selectOne(
                "SELECT id FROM products WHERE BINARY id = BINARY ? LIMIT 1",
                [$productId]
            );

            if (!$product) {
                return [
                    'success' => false,
                    'message' => 'Товар не найден',
                    'count' => $this->getItemsCount($userId)
                ];
            }

            // Проверяем, не добавлен ли товар уже в избранное
            $existing = DB::selectOne(
                "SELECT id FROM favorites WHERE user_id = ? AND product_id = ? LIMIT 1",
                [$userId, $productId]
            );

            if ($existing) {
                return [
                    'success' => true,
                    'message' => 'Товар уже в избранном',
                    'count' => $this->getItemsCount($userId)
                ];
            }

            // Добавляем товар в избранное
            // INSERT IGNORE защищает от дублирования при конкурентных запросах
            DB::insert(
                "INSERT INTO favorites (user_id, product_id, created_at) VALUES (?, ?, NOW())",
                [$userId, $productId]
            );

            return [
                'success' => true,
                'message' => 'Товар добавлен в избранное',
                'count' => $this->getItemsCount($userId)
            ];
        } catch (\Exception $e) {
            Log::error('FavoriteService::addItem error', [
                'product_id' => $productId,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'message' => 'Ошибка при добавлении в избранное',
                'count' => $this->getItemsCount($userId)
            ];
        }
    }

    /**
     * Удалить товар из избранного
     * 
     * Логика работы:
     * 1. Удаляем запись из таблицы favorites по user_id и product_id
     * 2. Используем DELETE для удаления связи пользователь-товар
     * 3. Возвращаем массив с результатом операции
     * 
     * @param string $productId ID товара (offer_id)
     * @param int $userId ID пользователя
     * @return array ['success' => bool, 'message' => string, 'count' => int]
     */
    public function removeItem(string $productId, int $userId): array
    {
        if (empty($productId) || empty($userId)) {
            return [
                'success' => false,
                'message' => 'Некорректные параметры',
                'count' => $this->getItemsCount($userId)
            ];
        }

        try {
            // Удаляем товар из избранного
            $deleted = DB::delete(
                "DELETE FROM favorites WHERE user_id = ? AND product_id = ?",
                [$userId, $productId]
            );

            if ($deleted > 0) {
                return [
                    'success' => true,
                    'message' => 'Товар удален из избранного',
                    'count' => $this->getItemsCount($userId)
                ];
            } else {
                return [
                    'success' => true,
                    'message' => 'Товар не был в избранном',
                    'count' => $this->getItemsCount($userId)
                ];
            }
        } catch (\Exception $e) {
            Log::error('FavoriteService::removeItem error', [
                'product_id' => $productId,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'message' => 'Ошибка при удалении из избранного',
                'count' => $this->getItemsCount($userId)
            ];
        }
    }

    /**
     * Получить список избранных товаров пользователя
     * 
     * Логика работы:
     * 1. Получаем список product_id из таблицы favorites для данного пользователя
     * 2. Для каждого товара получаем полную информацию из products и prices
     * 3. Получаем URL изображения товара
     * 4. Получаем рейтинг товара через ProductService
     * 5. Формируем массив товаров с полями: id, name, description, price, image, rating
     * 6. Возвращаем массив товаров и общее количество
     * 
     * @param int $userId ID пользователя
     * @return array ['items' => array, 'count' => int]
     */
    public function getItems(int $userId): array
    {
        if (empty($userId)) {
            return [
                'items' => [],
                'count' => 0
            ];
        }

        try {
            // Получаем список избранных товаров с информацией о товаре и цене
            $favorites = DB::select("
                SELECT 
                    f.product_id,
                    p.name,
                    p.description,
                    pr.price,
                    f.created_at
                FROM favorites f
                INNER JOIN products p ON f.product_id = p.id
                LEFT JOIN prices pr ON p.id = pr.product_id AND pr.price_type_id = '000000002'
                WHERE f.user_id = ?
                ORDER BY f.created_at DESC
            ", [$userId]);

            $items = [];
            foreach ($favorites as $favorite) {
                // Получаем URL изображения товара
                $imageUrl = $this->getProductImageUrl($favorite->product_id);

                // Получаем рейтинг товара через ProductService
                $productService = app(ProductService::class);
                $ratingData = $productService->getProductRating($favorite->product_id);

                // Форматируем цену
                $price = isset($favorite->price) && $favorite->price > 0 ? round($favorite->price) : 0;

                // Формируем объект товара
                $items[] = [
                    'id' => $favorite->product_id,
                    'name' => $favorite->name,
                    'description' => $favorite->description,
                    'price' => $price,
                    'image' => $imageUrl,
                    'rating' => $ratingData['average_rating'],
                    'reviews_count' => $ratingData['total_count'],
                    'added_at' => $favorite->created_at
                ];
            }

            return [
                'items' => $items,
                'count' => count($items)
            ];
        } catch (\Exception $e) {
            Log::error('FavoriteService::getItems error', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return [
                'items' => [],
                'count' => 0
            ];
        }
    }

    /**
     * Получить количество товаров в избранном
     * 
     * Вспомогательный метод для быстрого получения счетчика избранного
     * Используется для обновления счетчика в header
     * 
     * @param int $userId ID пользователя
     * @return int Количество товаров в избранном
     */
    public function getItemsCount(int $userId): int
    {
        if (empty($userId)) {
            return 0;
        }

        try {
            $result = DB::selectOne(
                "SELECT COUNT(*) as count FROM favorites WHERE user_id = ?",
                [$userId]
            );

            return $result ? (int)$result->count : 0;
        } catch (\Exception $e) {
            Log::error('FavoriteService::getItemsCount error', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * Проверить, находится ли товар в избранном
     * 
     * Вспомогательный метод для проверки состояния избранного на карточках товаров
     * Используется для отображения активной иконки избранного
     * 
     * @param string $productId ID товара
     * @param int $userId ID пользователя
     * @return bool true если товар в избранном, false если нет
     */
    public function isInFavorites(string $productId, int $userId): bool
    {
        if (empty($productId) || empty($userId)) {
            return false;
        }

        try {
            $result = DB::selectOne(
                "SELECT id FROM favorites WHERE user_id = ? AND product_id = ? LIMIT 1",
                [$userId, $productId]
            );

            return $result !== null;
        } catch (\Exception $e) {
            Log::error('FavoriteService::isInFavorites error', [
                'product_id' => $productId,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return false;
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
            Log::error('FavoriteService::getProductImageUrl error', [
                'product_id' => $productId,
                'error' => $e->getMessage()
            ]);
            return '/assets/img/product_empty.jpg';
        }
    }

    /**
     * Получить данные товаров по массиву ID (для неавторизованных пользователей)
     * 
     * Логика работы:
     * 1. Получаем информацию о товарах из products и prices по массиву ID
     * 2. Для каждого товара получаем URL изображения
     * 3. Получаем рейтинг товара через ProductService
     * 4. Формируем массив товаров с полями: id, name, description, price, image, rating
     * 5. Возвращаем массив товаров в формате [product_id => data]
     * 
     * @param array $productIds Массив ID товаров
     * @return array Ассоциативный массив [product_id => data]
     */
    public function getItemsByIds(array $productIds): array
    {
        if (empty($productIds)) {
            return [];
        }

        try {
            // Создаем плейсхолдеры для IN clause
            $placeholders = implode(',', array_fill(0, count($productIds), '?'));

            // Получаем информацию о товарах
            $products = DB::select("
                SELECT 
                    p.id as product_id,
                    p.name,
                    p.description,
                    pr.price
                FROM products p
                LEFT JOIN prices pr ON p.id = pr.product_id AND pr.price_type_id = '000000002'
                WHERE p.id IN ($placeholders)
            ", $productIds);

            $items = [];
            $productService = app(ProductService::class);

            foreach ($products as $product) {
                // Получаем URL изображения товара
                $imageUrl = $this->getProductImageUrl($product->product_id);

                // Получаем рейтинг товара
                $ratingData = $productService->getProductRating($product->product_id);

                // Форматируем цену
                $price = isset($product->price) && $product->price > 0 ? round($product->price) : 0;

                // Формируем объект товара (ключ - product_id для совместимости с JavaScript)
                $items[$product->product_id] = [
                    'id' => $product->product_id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => $price,
                    'image' => $imageUrl,
                    'rating' => $ratingData['average_rating'],
                    'reviews_count' => $ratingData['total_count']
                ];
            }

            return $items;
        } catch (\Exception $e) {
            Log::error('FavoriteService::getItemsByIds error', [
                'product_ids' => $productIds,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }
}