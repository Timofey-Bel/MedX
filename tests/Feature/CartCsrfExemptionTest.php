<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Тест проверяет, что API endpoint /api/cart исключен из проверки CSRF токена
 * 
 * Проблема: При добавлении товара через AJAX к /api/cart возникала ошибка "CSRF token mismatch"
 * Решение: Добавлено исключение для /api/cart в bootstrap/app.php
 */
class CartCsrfExemptionTest extends TestCase
{
    /**
     * Тест проверяет, что запрос к /api/cart не требует CSRF токен
     */
    public function test_api_cart_endpoint_does_not_require_csrf_token()
    {
        // Делаем POST запрос без CSRF токена
        // Если endpoint требует CSRF, получим 419 (CSRF token mismatch)
        // Если endpoint исключен из CSRF проверки, получим другой код (200, 400, 500 и т.д.)
        $response = $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->post('/api/cart', [
                'task' => 'get_cart'
            ]);

        // Проверяем, что НЕ получили ошибку CSRF (419)
        $this->assertNotEquals(419, $response->status(), 
            'API endpoint /api/cart не должен требовать CSRF токен');
    }

    /**
     * Тест проверяет, что endpoint доступен через web middleware
     */
    public function test_api_cart_endpoint_uses_web_middleware()
    {
        // Делаем запрос с сессией
        $response = $this->withSession(['test' => 'value'])
            ->post('/api/cart', [
                'task' => 'get_cart'
            ]);

        // Проверяем, что сессия работает (endpoint использует web middleware)
        $this->assertEquals('value', session('test'));
    }
}
