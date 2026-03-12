<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\ProductController;
use App\Services\CartService;
use App\Services\FavoriteService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Mockery;

/**
 * Unit тесты для ProductController
 * 
 * Проверяют корректность работы контроллера товаров
 */
class ProductControllerTest extends TestCase
{
    /**
     * ID существующего товара для тестов
     */
    private const TEST_PRODUCT_SLUG = '00-00017345';

    /**
     * Очистка после каждого теста
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Тест: возвращает view для валидного товара
     */
    public function test_show_returns_view_for_valid_product(): void
    {
        // Создаем реальный контроллер с реальными сервисами
        $controller = new ProductController(
            new CartService(),
            new FavoriteService(),
            new ProductService()
        );

        $request = new Request();
        $response = $controller->show($request, self::TEST_PRODUCT_SLUG);

        // Проверяем что возвращается view
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $this->assertEquals('product.show', $response->name());
    }

    /**
     * Тест: возвращает 404 для невалидного товара
     */
    public function test_show_returns_404_for_invalid_product(): void
    {
        // Ожидаем исключение 404 (HttpException с кодом 404)
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        // Создаем реальный контроллер с реальными сервисами
        $controller = new ProductController(
            new CartService(),
            new FavoriteService(),
            new ProductService()
        );

        $request = new Request();
        $controller->show($request, 'nonexistent-product-12345');
    }

    /**
     * Тест: передает данные товара в view
     */
    public function test_show_passes_product_data_to_view(): void
    {
        // Создаем реальный контроллер с реальными сервисами
        $controller = new ProductController(
            new CartService(),
            new FavoriteService(),
            new ProductService()
        );

        $request = new Request();
        $response = $controller->show($request, self::TEST_PRODUCT_SLUG);

        // Проверяем что view содержит данные товара
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        
        $viewData = $response->getData();
        
        // Проверяем наличие основных данных
        $this->assertArrayHasKey('product', $viewData);
        $this->assertIsObject($viewData['product']);
        $this->assertObjectHasProperty('id', $viewData['product']);
        $this->assertObjectHasProperty('name', $viewData['product']);
        $this->assertObjectHasProperty('price', $viewData['product']);
    }

    /**
     * Тест: передает breadcrumbs в view
     */
    public function test_show_passes_breadcrumbs_to_view(): void
    {
        // Создаем реальный контроллер с реальными сервисами
        $controller = new ProductController(
            new CartService(),
            new FavoriteService(),
            new ProductService()
        );

        $request = new Request();
        $response = $controller->show($request, self::TEST_PRODUCT_SLUG);

        // Проверяем что view содержит breadcrumbs
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        
        $viewData = $response->getData();
        
        // Проверяем наличие breadcrumbs
        $this->assertArrayHasKey('breadcrumbs', $viewData);
        $this->assertIsArray($viewData['breadcrumbs']);
        $this->assertNotEmpty($viewData['breadcrumbs']);
        
        // Проверяем структуру breadcrumbs
        foreach ($viewData['breadcrumbs'] as $crumb) {
            $this->assertIsArray($crumb);
            $this->assertArrayHasKey('title', $crumb);
        }
    }

    /**
     * Тест: передает SEO данные в view
     */
    public function test_show_passes_seo_data_to_view(): void
    {
        // Создаем реальный контроллер с реальными сервисами
        $controller = new ProductController(
            new CartService(),
            new FavoriteService(),
            new ProductService()
        );

        $request = new Request();
        $response = $controller->show($request, self::TEST_PRODUCT_SLUG);

        // Проверяем что view содержит SEO данные
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        
        $viewData = $response->getData();
        
        // Проверяем наличие SEO данных
        $this->assertArrayHasKey('seoData', $viewData);
        $this->assertIsArray($viewData['seoData']);
        
        // Проверяем основные SEO поля
        $this->assertArrayHasKey('title', $viewData['seoData']);
        $this->assertArrayHasKey('description', $viewData['seoData']);
        $this->assertArrayHasKey('og_title', $viewData['seoData']);
        $this->assertArrayHasKey('og_image', $viewData['seoData']);
        $this->assertArrayHasKey('og_url', $viewData['seoData']);
        $this->assertArrayHasKey('schema', $viewData['seoData']);
        
        // Проверяем что значения не пустые
        $this->assertNotEmpty($viewData['seoData']['title']);
        $this->assertNotEmpty($viewData['seoData']['og_title']);
    }
}
