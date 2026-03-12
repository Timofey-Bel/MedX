<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Сервис для работы с товарами
 * 
 * Миграция из legacy: legacy/site/modules/sfera/top10_products/top10_products.class.php
 * и legacy/site/modules/sfera/catalog/catalog.class.php
 * 
 * Предоставляет методы для получения рейтингов и изображений товаров
 */
class ProductService
{
    /**
     * Получить рейтинг и статистику отзывов для товара
     * 
     * Логика работы:
     * 1. Ищем SKU товара в таблице v_products_o_products по offer_id
     * 2. Если SKU найден, получаем все отзывы с рейтингом из o_reviews
     * 3. Вычисляем средний рейтинг, общее количество отзывов
     * 4. Вычисляем распределение по звездам (1-5) с количеством и процентами
     * 5. Возвращаем массив с полной статистикой
     * 
     * @param string $productId ID товара (offer_id)
     * @return array ['average_rating' => float, 'total_count' => int, 'rating_distribution' => array]
     */
    public function getProductRating($productId)
    {
        if (empty($productId)) {
            return [
                'average_rating' => 0,
                'total_count' => 0,
                'rating_distribution' => [
                    5 => ['count' => 0, 'percent' => 0],
                    4 => ['count' => 0, 'percent' => 0],
                    3 => ['count' => 0, 'percent' => 0],
                    2 => ['count' => 0, 'percent' => 0],
                    1 => ['count' => 0, 'percent' => 0]
                ]
            ];
        }

        try {
            // Получаем SKU товара из v_products_o_products
            $ozonProduct = DB::selectOne(
                "SELECT sku FROM v_products_o_products WHERE offer_id = ? LIMIT 1",
                [$productId]
            );

            if (!$ozonProduct || empty($ozonProduct->sku)) {
                return [
                    'average_rating' => 0,
                    'total_count' => 0,
                    'rating_distribution' => [
                        5 => ['count' => 0, 'percent' => 0],
                        4 => ['count' => 0, 'percent' => 0],
                        3 => ['count' => 0, 'percent' => 0],
                        2 => ['count' => 0, 'percent' => 0],
                        1 => ['count' => 0, 'percent' => 0]
                    ]
                ];
            }

            // Получаем все отзывы с рейтингом
            $allReviews = DB::select(
                "SELECT rating FROM o_reviews WHERE sku = ? AND rating IS NOT NULL",
                [$ozonProduct->sku]
            );

            $totalCount = count($allReviews);

            if ($totalCount === 0) {
                return [
                    'average_rating' => 0,
                    'total_count' => 0,
                    'rating_distribution' => [
                        5 => ['count' => 0, 'percent' => 0],
                        4 => ['count' => 0, 'percent' => 0],
                        3 => ['count' => 0, 'percent' => 0],
                        2 => ['count' => 0, 'percent' => 0],
                        1 => ['count' => 0, 'percent' => 0]
                    ]
                ];
            }

            // Вычисляем статистику
            $sumRating = 0;
            $ratingCounts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];

            foreach ($allReviews as $review) {
                $rating = (int)$review->rating;
                if ($rating >= 1 && $rating <= 5) {
                    $sumRating += $rating;
                    $ratingCounts[$rating]++;
                }
            }

            // Средний рейтинг
            $averageRating = round($sumRating / $totalCount, 1);

            // Распределение по звездам с процентами
            $ratingDistribution = [];
            foreach ($ratingCounts as $stars => $count) {
                $ratingDistribution[$stars] = [
                    'count' => $count,
                    'percent' => round(($count / $totalCount) * 100, 1)
                ];
            }

            return [
                'average_rating' => $averageRating,
                'total_count' => $totalCount,
                'rating_distribution' => $ratingDistribution
            ];
        } catch (\Exception $e) {
            Log::error('ProductService::getProductRating error', [
                'product_id' => $productId,
                'error' => $e->getMessage()
            ]);
            return [
                'average_rating' => 0,
                'total_count' => 0,
                'rating_distribution' => [
                    5 => ['count' => 0, 'percent' => 0],
                    4 => ['count' => 0, 'percent' => 0],
                    3 => ['count' => 0, 'percent' => 0],
                    2 => ['count' => 0, 'percent' => 0],
                    1 => ['count' => 0, 'percent' => 0]
                ]
            ];
        }
    }

    /**
     * Получить товар по slug или ID
     * 
     * Логика работы:
     * 1. Получаем товар из таблицы products по ID
     * 2. Получаем цену из таблицы prices (price_type_id = '000000002')
     * 3. Формируем объект товара с полями: id, name, description, category_id, price, old_price, discount_percent, in_stock, brand, sku
     * 4. Возвращаем null если товар не найден
     * 
     * @param string $slug ID или slug товара
     * @return object|null Объект товара или null если не найден
     */
    public function getProductBySlug(string $slug): ?object
    {
        if (empty($slug)) {
            return null;
        }

        try {
            // Получаем товар с ценой из таблиц products и prices
            $product = DB::selectOne("
                SELECT 
                    p.id,
                    p.name,
                    p.description,
                    p.category_id,
                    p.picture,
                    pr.price as price
                FROM products p
                LEFT JOIN prices pr ON p.id = pr.product_id AND pr.price_type_id = '000000002'
                WHERE BINARY p.id = BINARY ?
            ", [$slug]);

            if (!$product) {
                return null;
            }

            // Форматируем цену
            $product->price = isset($product->price) && $product->price > 0 ? round($product->price) : 0;
            
            // Добавляем дополнительные поля (пока заглушки, можно расширить позже)
            $product->old_price = null;
            $product->discount_percent = null;
            $product->in_stock = true; // TODO: реализовать проверку наличия
            $product->brand = null; // TODO: получить из атрибутов
            $product->sku = null; // TODO: получить из v_products_o_products

            return $product;
        } catch (\Exception $e) {
            Log::error('ProductService::getProductBySlug error', [
                'slug' => $slug,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Получить характеристики товара
     * 
     * Логика работы:
     * 1. Получаем атрибуты из таблицы attributes
     * 2. Исключаем служебные атрибуты: Новинка, Отгружать упаковками, Отображать, Стандарт, Тип обложки
     * 3. Для атрибутов "Серия", "Тематика", "Тип товара" получаем ID из соответствующих view
     * 4. Возвращаем массив объектов с полями: name, value, unit, seriya_id, topic_id, product_type_id
     * 
     * @param string $productId ID товара
     * @return array Массив объектов атрибутов
     */
    public function getProductAttributes(string $productId): array
    {
        if (empty($productId)) {
            return [];
        }

        try {
            // Список служебных атрибутов для исключения
            $excludedAttributes = [
                'Новинка',
                'Отгружать упаковками',
                'Отображать',
                'Стандарт',
                'Тип обложки'
            ];

            // Получаем атрибуты из таблицы attributes
            $placeholders = implode(',', array_fill(0, count($excludedAttributes), '?'));
            $attributes = DB::select("
                SELECT name, value
                FROM attributes
                WHERE product_id = ? 
                AND name NOT IN ($placeholders)
                ORDER BY name ASC
            ", array_merge([$productId], $excludedAttributes));

            // Обрабатываем каждый атрибут
            foreach ($attributes as $attr) {
                // Инициализируем дополнительные поля
                $attr->unit = null;
                $attr->seriya_id = null;
                $attr->topic_id = null;
                $attr->product_type_id = null;

                // Для атрибута "Серия" получаем ID из v_seriya
                if ($attr->name === 'Серия' && !empty($attr->value)) {
                    $seriyaInfo = DB::selectOne(
                        "SELECT id FROM v_seriya WHERE BINARY value = BINARY ? LIMIT 1",
                        [$attr->value]
                    );
                    if ($seriyaInfo && isset($seriyaInfo->id)) {
                        $attr->seriya_id = (int)$seriyaInfo->id;
                    }
                }

                // Для атрибута "Тематика" получаем ID из v_tematika
                if ($attr->name === 'Тематика' && !empty($attr->value)) {
                    $tematikaInfo = DB::selectOne(
                        "SELECT id FROM v_tematika WHERE BINARY value = BINARY ? LIMIT 1",
                        [$attr->value]
                    );
                    if ($tematikaInfo && isset($tematikaInfo->id)) {
                        $attr->topic_id = (int)$tematikaInfo->id;
                    }
                }

                // Для атрибута "Тип товара" получаем ID из v_tip_tovara
                if ($attr->name === 'Тип товара' && !empty($attr->value)) {
                    $tipTovaraInfo = DB::selectOne(
                        "SELECT id FROM v_tip_tovara WHERE BINARY value = BINARY ? LIMIT 1",
                        [$attr->value]
                    );
                    if ($tipTovaraInfo && isset($tipTovaraInfo->id)) {
                        $attr->product_type_id = (int)$tipTovaraInfo->id;
                    }
                }
            }

            return $attributes;
        } catch (\Exception $e) {
            Log::error('ProductService::getProductAttributes error', [
                'product_id' => $productId,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Получить отзывы товара
     * 
     * Логика работы:
     * 1. Получаем SKU товара из v_products_o_products
     * 2. Получаем отзывы из o_reviews с текстом и рейтингом
     * 3. Форматируем даты на русском языке (например: "15 января 2024")
     * 4. Возвращаем массив объектов с полями: review_id, rating, text, date, formatted_date, first_letter
     * 
     * @param string $productId ID товара
     * @return array Массив отзывов с форматированными данными
     */
    public function getProductReviews(string $productId): array
    {
        if (empty($productId)) {
            return [];
        }

        try {
            // Получаем SKU товара из v_products_o_products
            $ozonProduct = DB::selectOne(
                "SELECT sku FROM v_products_o_products WHERE offer_id = ? LIMIT 1",
                [$productId]
            );

            if (!$ozonProduct || empty($ozonProduct->sku)) {
                return [];
            }

            // Получаем отзывы с текстом из o_reviews
            $reviewsRaw = DB::select("
                SELECT review_id, rating, text, date, state
                FROM o_reviews
                WHERE sku = ? 
                AND rating IS NOT NULL 
                AND text IS NOT NULL 
                AND text != '' 
                AND TRIM(text) != ''
                ORDER BY date DESC
            ", [$ozonProduct->sku]);

            // Массив месяцев на русском языке
            $months = [
                1 => 'января', 2 => 'февраля', 3 => 'марта', 4 => 'апреля',
                5 => 'мая', 6 => 'июня', 7 => 'июля', 8 => 'августа',
                9 => 'сентября', 10 => 'октября', 11 => 'ноября', 12 => 'декабря'
            ];

            $reviews = [];
            foreach ($reviewsRaw as $review) {
                // Форматируем дату на русском языке
                $formattedDate = '';
                if (!empty($review->date)) {
                    $dateObj = \DateTime::createFromFormat('Y-m-d H:i:s', $review->date);
                    if ($dateObj) {
                        $day = $dateObj->format('j');
                        $month = (int)$dateObj->format('n');
                        $year = $dateObj->format('Y');
                        $formattedDate = $day . ' ' . ($months[$month] ?? '') . ' ' . $year;
                    }
                }

                // Получаем первую букву из текста для аватара
                $firstLetter = mb_substr(trim($review->text), 0, 1, 'UTF-8');
                $firstLetter = mb_strtoupper($firstLetter, 'UTF-8');

                $reviews[] = (object)[
                    'review_id' => $review->review_id,
                    'rating' => (int)$review->rating,
                    'text' => $review->text,
                    'date' => $review->date,
                    'formatted_date' => $formattedDate,
                    'first_letter' => $firstLetter,
                    'state' => $review->state
                ];
            }

            return $reviews;
        } catch (\Exception $e) {
            Log::error('ProductService::getProductReviews error', [
                'product_id' => $productId,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Получить похожие товары
     * 
     * Логика работы:
     * 1. Получаем категорию текущего товара
     * 2. Выбираем товары из той же категории (исключая текущий)
     * 3. Ограничиваем результат от 4 до 12 товаров
     * 4. Возвращаем массив объектов с полями: id, name, image, price, in_stock
     * 
     * @param string $productId ID товара
     * @param int $limit Максимальное количество товаров (по умолчанию 12)
     * @return array Массив похожих товаров
     */
    public function getRelatedProducts(string $productId, int $limit = 12): array
    {
        if (empty($productId)) {
            return [];
        }

        try {
            // Ограничиваем лимит от 4 до 12
            $limit = max(4, min(12, $limit));

            // Получаем категорию текущего товара
            $currentProduct = DB::selectOne(
                "SELECT category_id FROM products WHERE BINARY id = BINARY ?",
                [$productId]
            );

            if (!$currentProduct || empty($currentProduct->category_id)) {
                return [];
            }

            // Выбираем товары из той же категории, исключая текущий
            $relatedProducts = DB::select("
                SELECT 
                    p.id,
                    p.name,
                    p.picture,
                    pr.price
                FROM products p
                LEFT JOIN prices pr ON p.id = pr.product_id AND pr.price_type_id = '000000002'
                WHERE p.category_id = ? 
                AND BINARY p.id != BINARY ?
                ORDER BY RAND()
                LIMIT ?
            ", [$currentProduct->category_id, $productId, $limit]);

            $result = [];
            foreach ($relatedProducts as $product) {
                // Получаем URL изображения через существующий метод
                $imageUrl = $this->getProductImageUrl($product->id);

                $result[] = (object)[
                    'id' => $product->id,
                    'name' => $product->name,
                    'image' => $imageUrl,
                    'price' => isset($product->price) && $product->price > 0 ? round($product->price) : 0,
                    'in_stock' => true // TODO: реализовать проверку наличия
                ];
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('ProductService::getRelatedProducts error', [
                'product_id' => $productId,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Получить изображения товара
     * 
     * Логика работы:
     * 1. Проверяем наличие товара в v_products_o_products
     * 2. Если найден, получаем изображения из o_images упорядоченные по image_order
     * 3. Формируем URL в формате /o_images/{product_id}/{image_order}.jpg
     * 4. Если не найден, используем стандартный путь /import_files/{product_id}b.jpg
     * 5. Возвращаем массив объектов с полем url
     * 
     * @param string $productId ID товара
     * @return array Массив URL изображений
     */
    public function getProductImages(string $productId): array
    {
        if (empty($productId)) {
            return [];
        }

        try {
            // Проверяем наличие товара в v_products_o_products
            $ozonProduct = DB::selectOne(
                "SELECT product_id FROM v_products_o_products WHERE offer_id = ? LIMIT 1",
                [$productId]
            );

            if ($ozonProduct && !empty($ozonProduct->product_id)) {
                // Товар найден в v_products_o_products, получаем изображения из o_images
                $images = DB::select(
                    "SELECT product_id, image_order FROM o_images WHERE product_id = ? ORDER BY image_order ASC",
                    [$ozonProduct->product_id]
                );

                $result = [];
                foreach ($images as $image) {
                    $result[] = (object)[
                        'url' => "/o_images/" . $image->product_id . "/" . $image->image_order . ".jpg"
                    ];
                }

                return $result;
            }

            // Если товара нет в v_products_o_products, используем стандартный путь
            return [
                (object)['url' => '/import_files/' . $productId . 'b.jpg']
            ];
        } catch (\Exception $e) {
            Log::error('ProductService::getProductImages error', [
                'product_id' => $productId,
                'error' => $e->getMessage()
            ]);
            return [];
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
    public function getProductImageUrl($productId)
    {
        if (empty($productId)) {
            return '/assets/img/product_empty.jpg';
        }

        try {
            // Проверяем наличие товара в v_products_o_products
            $ozonProduct = DB::selectOne(
                "SELECT product_id FROM v_products_o_products WHERE offer_id = ?",
                [$productId]
            );

            if ($ozonProduct && !empty($ozonProduct->product_id)) {
                // Товар найден в v_products_o_products, получаем primary_image из o_images
                $oImage = DB::selectOne(
                    "SELECT * FROM o_images WHERE product_id = ? AND image_order = 0",
                    [$ozonProduct->product_id]
                );

                if ($oImage) {
                    // Возвращаем адрес изображения из o_images
                    return "/o_images/" . $oImage->product_id . "/0.jpg";
                }
            }

            // Если товара нет в v_products_o_products, используем стандартный путь
            return '/import_files/' . $productId . 'b.jpg';
        } catch (\Exception $e) {
            Log::error('ProductService::getProductImageUrl error', [
                'product_id' => $productId,
                'error' => $e->getMessage()
            ]);
            return '/assets/img/product_empty.jpg';
        }
    }
}
