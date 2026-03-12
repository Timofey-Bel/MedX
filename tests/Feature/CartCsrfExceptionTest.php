<?php

namespace Tests\Feature;

use Tests\TestCase;

class CartCsrfExceptionTest extends TestCase
{

    /**
     * Тест проверяет, что /api/cart исключен из проверки CSRF токена
     * и может принимать AJAX запросы без токена
     */
    public function test_api_cart_accepts_requests_without_csrf_token(): void
    {
        // Отправляем POST запрос без CSRF токена
        $response = $this->postJson('/api/cart', [
            'task' => 'get_cart',
        ]);

        // Проверяем, что запрос НЕ возвращает ошибку 419 (CSRF token mismatch)
        $response->assertStatus(200);
        
        // Проверяем, что получили корректный JSON ответ
        $response->assertJsonStructure([
            'items',
            'total_cart_sum',
            'total_cart_amount',
            'cart_sum',
            'cart_discount',
            'promocode',
        ]);
    }

    /**
     * Тест проверяет, что можно добавить товар в корзину без CSRF токена
     */
    public function test_can_add_product_to_cart_without_csrf_token(): void
    {
        // Отправляем запрос на добавление товара без CSRF токена
        // Используем task=put_item с правильной структурой данных
        $response = $this->postJson('/api/cart', [
            'task' => 'put_item',
            'item' => json_encode([
                'guid' => '1',
                'product_amount' => 1,
            ]),
        ]);

        // Проверяем, что запрос успешен (не 419 ошибка)
        $this->assertNotEquals(419, $response->status());
        
        // Проверяем, что получили JSON ответ с корректной структурой корзины
        $response->assertJsonStructure([
            'items',
            'total_cart_sum',
            'total_cart_amount',
            'cart_sum',
            'cart_discount',
            'promocode',
        ]);
    }
}
