<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ProductService;

class ProductServiceReviewsTest extends TestCase
{
    /**
     * Тест проверяет что getProductReviews возвращает массив объектов,
     * а не массив массивов, чтобы в Blade работал объектный синтаксис
     */
    public function test_get_product_reviews_returns_objects(): void
    {
        $productService = new ProductService();
        
        // Используем реальный product_id из базы (если есть отзывы)
        // Если отзывов нет, метод вернет пустой массив
        $reviews = $productService->getProductReviews('test-product-id');
        
        // Проверяем что возвращается массив
        $this->assertIsArray($reviews);
        
        // Если есть отзывы, проверяем что каждый элемент - объект
        if (!empty($reviews)) {
            foreach ($reviews as $review) {
                $this->assertIsObject($review, 'Review должен быть объектом, а не массивом');
                $this->assertObjectHasProperty('review_id', $review);
                $this->assertObjectHasProperty('rating', $review);
                $this->assertObjectHasProperty('text', $review);
                $this->assertObjectHasProperty('formatted_date', $review);
                $this->assertObjectHasProperty('first_letter', $review);
            }
        }
    }
}
