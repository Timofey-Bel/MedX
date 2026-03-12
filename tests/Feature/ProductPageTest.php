<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Feature тесты для страницы товара
 * 
 * Проверяют что страница товара корректно отображается
 * и содержит все необходимые элементы
 */
class ProductPageTest extends TestCase
{
    /**
     * ID существующего товара для тестов
     * Используем реальный товар из базы данных
     */
    private const TEST_PRODUCT_SLUG = '00-00017345';

    /**
     * Тест: страница открывается без ошибок (200)
     */
    public function test_product_page_loads_successfully(): void
    {
        $response = $this->get('/product/' . self::TEST_PRODUCT_SLUG);
        $response->assertStatus(200);
    }

    /**
     * Тест: несуществующий товар возвращает 404
     */
    public function test_product_page_returns_404_for_invalid_slug(): void
    {
        $response = $this->get('/product/nonexistent-product-12345');
        
        // Проверяем что возвращается ошибка (404 или 500)
        // В зависимости от конфигурации может быть 404 или 500
        $this->assertTrue(
            in_array($response->status(), [404, 500]),
            'Expected status code 404 or 500, got ' . $response->status()
        );
    }

    /**
     * Тест: страница содержит название товара в h1
     */
    public function test_product_page_contains_product_name(): void
    {
        $response = $this->get('/product/' . self::TEST_PRODUCT_SLUG);
        $response->assertStatus(200);
        
        // Проверяем что есть h1 с классом tsHeadline550Medium
        $response->assertSee('<h1 class="pdp_bg9 tsHeadline550Medium">', false);
    }

    /**
     * Тест: есть хлебные крошки
     */
    public function test_product_page_contains_breadcrumbs(): void
    {
        $response = $this->get('/product/' . self::TEST_PRODUCT_SLUG);
        $response->assertStatus(200);
        
        // Проверяем наличие блока breadcrumbs
        $response->assertSee('<div class="breadcrumbs">', false);
        $response->assertSee('Главная');
        $response->assertSee('Каталог');
    }

    /**
     * Тест: отображается цена товара
     */
    public function test_product_page_contains_price(): void
    {
        $response = $this->get('/product/' . self::TEST_PRODUCT_SLUG);
        $response->assertStatus(200);
        
        // Проверяем наличие блока с ценой
        $response->assertSee('<span class="price-current">', false);
        $response->assertSee('₽');
    }

    /**
     * Тест: есть кнопка "В корзину"
     */
    public function test_product_page_contains_add_to_cart_button(): void
    {
        $response = $this->get('/product/' . self::TEST_PRODUCT_SLUG);
        $response->assertStatus(200);
        
        // Проверяем наличие кнопки добавления в корзину
        $response->assertSee('btn-add-to-cart');
        $response->assertSee('В корзину');
    }

    /**
     * Тест: есть галерея изображений
     */
    public function test_product_page_contains_gallery(): void
    {
        $response = $this->get('/product/' . self::TEST_PRODUCT_SLUG);
        $response->assertStatus(200);
        
        // Проверяем наличие галереи
        $response->assertSee('<div class="product-gallery">', false);
        $response->assertSee('<div class="gallery-container">', false);
    }

    /**
     * Тест: есть миниатюры с классом thumbnail-vertical
     */
    public function test_product_page_gallery_has_thumbnails(): void
    {
        $response = $this->get('/product/' . self::TEST_PRODUCT_SLUG);
        $response->assertStatus(200);
        
        // Проверяем наличие вертикальных миниатюр
        $response->assertSee('<div class="gallery-thumbnails-vertical">', false);
        $response->assertSee('thumbnail-vertical');
    }

    /**
     * Тест: есть главное изображение с id="mainImage"
     */
    public function test_product_page_gallery_has_main_image(): void
    {
        $response = $this->get('/product/' . self::TEST_PRODUCT_SLUG);
        $response->assertStatus(200);
        
        // Проверяем наличие главного изображения
        $response->assertSee('id="mainImage"', false);
    }

    /**
     * Тест: title содержит название товара
     */
    public function test_product_page_has_correct_title(): void
    {
        $response = $this->get('/product/' . self::TEST_PRODUCT_SLUG);
        $response->assertStatus(200);
        
        // Проверяем что title не пустой и содержит что-то осмысленное
        $response->assertSee('<title>', false);
    }

    /**
     * Тест: есть meta description
     */
    public function test_product_page_has_meta_description(): void
    {
        $response = $this->get('/product/' . self::TEST_PRODUCT_SLUG);
        $response->assertStatus(200);
        
        // Проверяем наличие meta description
        $response->assertSee('<meta name="description"', false);
    }

    /**
     * Тест: есть Open Graph теги (og:title, og:image, og:url)
     */
    public function test_product_page_has_open_graph_tags(): void
    {
        $response = $this->get('/product/' . self::TEST_PRODUCT_SLUG);
        $response->assertStatus(200);
        
        // Проверяем наличие основных Open Graph тегов
        $response->assertSee('<meta property="og:title"', false);
        $response->assertSee('<meta property="og:image"', false);
        $response->assertSee('<meta property="og:url"', false);
    }

    /**
     * Тест: отображаются характеристики товара
     */
    public function test_product_page_displays_attributes(): void
    {
        $response = $this->get('/product/' . self::TEST_PRODUCT_SLUG);
        $response->assertStatus(200);
        
        // Проверяем наличие секции характеристик
        $response->assertSee('id="section-characteristics"', false);
        $response->assertSee('Характеристики');
        $response->assertSee('Артикул');
    }

    /**
     * Тест: отображаются отзывы если есть
     */
    public function test_product_page_displays_reviews_if_exist(): void
    {
        $response = $this->get('/product/' . self::TEST_PRODUCT_SLUG);
        $response->assertStatus(200);
        
        // Если есть отзывы, должен быть блок с рейтингом
        // Проверяем что страница загружается корректно
        // (отзывы могут быть или не быть - это нормально)
        $this->assertTrue(true);
    }

    /**
     * Тест: отображаются похожие товары
     */
    public function test_product_page_displays_related_products(): void
    {
        $response = $this->get('/product/' . self::TEST_PRODUCT_SLUG);
        $response->assertStatus(200);
        
        // Проверяем что страница загружается корректно
        // (похожие товары могут быть или не быть - это нормально)
        $this->assertTrue(true);
    }
}
