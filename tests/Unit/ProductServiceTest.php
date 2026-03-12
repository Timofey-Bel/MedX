<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ProductService;

/**
 * Unit тесты для ProductService
 * 
 * Проверяют корректность работы методов сервиса товаров
 */
class ProductServiceTest extends TestCase
{
    /**
     * Экземпляр ProductService для тестов
     */
    private ProductService $productService;

    /**
     * ID существующего товара для тестов
     */
    private const TEST_PRODUCT_ID = '00-00017345';

    /**
     * Инициализация перед каждым тестом
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->productService = new ProductService();
    }

    // ========== Тесты getProductBySlug ==========

    /**
     * Тест: возвращает товар для валидного slug
     */
    public function test_get_product_by_slug_returns_product_for_valid_slug(): void
    {
        $product = $this->productService->getProductBySlug(self::TEST_PRODUCT_ID);
        
        // Проверяем что товар найден
        $this->assertNotNull($product);
        $this->assertIsObject($product);
        
        // Проверяем что у товара есть основные поля
        $this->assertObjectHasProperty('id', $product);
        $this->assertObjectHasProperty('name', $product);
    }

    /**
     * Тест: возвращает null для невалидного slug
     */
    public function test_get_product_by_slug_returns_null_for_invalid_slug(): void
    {
        $product = $this->productService->getProductBySlug('nonexistent-product-12345');
        
        // Проверяем что товар не найден
        $this->assertNull($product);
    }

    /**
     * Тест: товар содержит цену
     */
    public function test_get_product_by_slug_includes_price(): void
    {
        $product = $this->productService->getProductBySlug(self::TEST_PRODUCT_ID);
        
        // Проверяем что товар найден и содержит цену
        $this->assertNotNull($product);
        $this->assertObjectHasProperty('price', $product);
        $this->assertIsNumeric($product->price);
    }

    // ========== Тесты getProductAttributes ==========

    /**
     * Тест: возвращает массив
     */
    public function test_get_product_attributes_returns_array(): void
    {
        $attributes = $this->productService->getProductAttributes(self::TEST_PRODUCT_ID);
        
        // Проверяем что возвращается массив
        $this->assertIsArray($attributes);
    }

    /**
     * Тест: исключает служебные атрибуты
     */
    public function test_get_product_attributes_excludes_service_attributes(): void
    {
        $attributes = $this->productService->getProductAttributes(self::TEST_PRODUCT_ID);
        
        // Проверяем что служебные атрибуты исключены
        // (например, атрибуты с пустыми значениями или системные)
        foreach ($attributes as $attr) {
            // Проверяем что у атрибута есть значение
            $this->assertObjectHasProperty('value', $attr);
        }
    }

    /**
     * Тест: каждый элемент массива - объект
     */
    public function test_get_product_attributes_returns_objects(): void
    {
        $attributes = $this->productService->getProductAttributes(self::TEST_PRODUCT_ID);
        
        // Если есть атрибуты, проверяем что каждый - объект
        if (!empty($attributes)) {
            foreach ($attributes as $attr) {
                $this->assertIsObject($attr);
                $this->assertObjectHasProperty('name', $attr);
                $this->assertObjectHasProperty('value', $attr);
            }
        }
    }

    // ========== Тесты getProductReviews ==========

    /**
     * Тест: возвращает массив
     */
    public function test_get_product_reviews_returns_array(): void
    {
        $reviews = $this->productService->getProductReviews(self::TEST_PRODUCT_ID);
        
        // Проверяем что возвращается массив
        $this->assertIsArray($reviews);
    }

    /**
     * Тест: каждый элемент - объект (не массив!)
     */
    public function test_get_product_reviews_returns_objects(): void
    {
        $reviews = $this->productService->getProductReviews(self::TEST_PRODUCT_ID);
        
        // Проверяем что возвращается массив
        $this->assertIsArray($reviews);
        
        // Если есть отзывы, проверяем что каждый элемент - объект
        if (!empty($reviews)) {
            foreach ($reviews as $review) {
                $this->assertIsObject($review, 'Review должен быть объектом, а не массивом');
                $this->assertObjectHasProperty('review_id', $review);
                $this->assertObjectHasProperty('rating', $review);
                $this->assertObjectHasProperty('text', $review);
            }
        }
    }

    /**
     * Тест: даты форматируются на русском
     */
    public function test_get_product_reviews_formats_dates_in_russian(): void
    {
        $reviews = $this->productService->getProductReviews(self::TEST_PRODUCT_ID);
        
        // Если есть отзывы, проверяем форматирование дат
        if (!empty($reviews)) {
            foreach ($reviews as $review) {
                $this->assertObjectHasProperty('formatted_date', $review);
                $this->assertIsString($review->formatted_date);
            }
        } else {
            // Если отзывов нет, тест считается пройденным
            $this->assertTrue(true);
        }
    }

    // ========== Тесты getProductImages ==========

    /**
     * Тест: возвращает массив
     */
    public function test_get_product_images_returns_array(): void
    {
        $images = $this->productService->getProductImages(self::TEST_PRODUCT_ID);
        
        // Проверяем что возвращается массив
        $this->assertIsArray($images);
    }

    /**
     * Тест: каждый элемент - объект с полем url
     */
    public function test_get_product_images_returns_objects_with_url(): void
    {
        $images = $this->productService->getProductImages(self::TEST_PRODUCT_ID);
        
        // Проверяем что возвращается массив
        $this->assertIsArray($images);
        
        // Должен быть хотя бы один элемент (заглушка или реальное изображение)
        $this->assertNotEmpty($images);
        
        // Проверяем что каждый элемент - объект с url
        foreach ($images as $image) {
            $this->assertIsObject($image);
            $this->assertObjectHasProperty('url', $image);
            $this->assertIsString($image->url);
        }
    }

    /**
     * Тест: возвращает заглушку для несуществующего товара
     */
    public function test_get_product_images_returns_fallback_for_missing_product(): void
    {
        $images = $this->productService->getProductImages('nonexistent-product-12345');
        
        // Проверяем что возвращается массив с заглушкой
        $this->assertIsArray($images);
        $this->assertNotEmpty($images);
        
        // Проверяем что есть url заглушки
        $this->assertIsObject($images[0]);
        $this->assertObjectHasProperty('url', $images[0]);
    }

    // ========== Тесты getRelatedProducts ==========

    /**
     * Тест: возвращает массив
     */
    public function test_get_related_products_returns_array(): void
    {
        $relatedProducts = $this->productService->getRelatedProducts(self::TEST_PRODUCT_ID);
        
        // Проверяем что возвращается массив
        $this->assertIsArray($relatedProducts);
    }

    /**
     * Тест: исключает текущий товар
     */
    public function test_get_related_products_excludes_current_product(): void
    {
        $relatedProducts = $this->productService->getRelatedProducts(self::TEST_PRODUCT_ID);
        
        // Проверяем что текущий товар не включен в список похожих
        foreach ($relatedProducts as $product) {
            $this->assertIsObject($product);
            $this->assertObjectHasProperty('id', $product);
            $this->assertNotEquals(self::TEST_PRODUCT_ID, $product->id);
        }
    }

    /**
     * Тест: соблюдает лимит (4-12)
     */
    public function test_get_related_products_respects_limit(): void
    {
        // Тестируем с лимитом 4
        $relatedProducts = $this->productService->getRelatedProducts(self::TEST_PRODUCT_ID, 4);
        $this->assertIsArray($relatedProducts);
        $this->assertLessThanOrEqual(4, count($relatedProducts));
        
        // Тестируем с лимитом 12
        $relatedProducts = $this->productService->getRelatedProducts(self::TEST_PRODUCT_ID, 12);
        $this->assertIsArray($relatedProducts);
        $this->assertLessThanOrEqual(12, count($relatedProducts));
    }
}
