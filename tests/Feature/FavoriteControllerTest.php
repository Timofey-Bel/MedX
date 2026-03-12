<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Интеграционные тесты для FavoriteController
 * 
 * Тестируют HTTP-маршруты и AJAX-endpoints:
 * - GET /favorites - страница избранного
 * - POST /api/favorites/add - добавление товара
 * - POST /api/favorites/remove - удаление товара
 * - POST /api/favorites (task=get_favorites) - получение списка
 * 
 * ВАЖНО: Используются транзакции для безопасной работы с production БД
 * Все тестовые данные откатываются после выполнения тестов
 */
class FavoriteControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected int $testUserId;
    protected string $testProductId;

    /**
     * Подготовка перед каждым тестом
     * 
     * Создаем тестового пользователя и тестовый товар
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Создаем тестового пользователя
        DB::insert(
            "INSERT INTO users (name, email, password) VALUES (?, ?, ?)",
            ['test_user_favorites_http', 'test_favorites_http@example.com', bcrypt('password')]
        );
        
        $user = DB::selectOne(
            "SELECT id FROM users WHERE email = ? LIMIT 1",
            ['test_favorites_http@example.com']
        );
        $this->testUserId = $user->id;
        
        // Создаем тестовый товар
        DB::insert(
            "INSERT INTO products (id, name, description) VALUES (?, ?, ?)",
            ['test_product_http_001', 'Тестовый товар HTTP', 'Описание тестового товара HTTP']
        );
        $this->testProductId = 'test_product_http_001';
    }

    /**
     * Тест: Страница избранного доступна
     * 
     * Проверяет, что страница /favorites отображается корректно
     */
    public function test_favorites_page_is_accessible(): void
    {
        $response = $this->get('/favorites');
        
        $response->assertStatus(200);
        $response->assertViewIs('favorites.index');
        $response->assertViewHas('items');
        $response->assertViewHas('count');
    }

    /**
     * Тест: AJAX добавление товара в избранное (авторизованный пользователь)
     * 
     * Проверяет, что авторизованный пользователь может
     * добавить товар в избранное через AJAX
     */
    public function test_add_item_ajax_authenticated(): void
    {
        // Получаем тестового пользователя как модель User
        $user = \App\Models\User::find($this->testUserId);
        
        // Авторизуемся как тестовый пользователь
        $this->actingAs($user);
        
        // Отправляем AJAX-запрос на добавление
        $response = $this->post('/api/favorites/add', [
            'product_id' => $this->testProductId
        ], [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);
        
        // Проверяем ответ
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'count' => 1
        ]);
        
        // Проверяем, что товар добавлен в БД
        $favorite = DB::selectOne(
            "SELECT * FROM favorites WHERE user_id = ? AND product_id = ?",
            [$this->testUserId, $this->testProductId]
        );
        $this->assertNotNull($favorite);
    }

    /**
     * Тест: AJAX добавление товара в избранное (неавторизованный пользователь)
     * 
     * Проверяет, что неавторизованный пользователь может
     * добавить товар в избранное через сессию
     */
    public function test_add_item_ajax_guest(): void
    {
        // Отправляем AJAX-запрос без авторизации
        $response = $this->post('/api/favorites/add', [
            'product_id' => $this->testProductId
        ], [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);
        
        // Проверяем ответ
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'count' => 1
        ]);
        
        // Проверяем, что товар добавлен в сессию
        $this->assertContains($this->testProductId, session('favorites', []));
    }

    /**
     * Тест: AJAX удаление товара из избранного (авторизованный пользователь)
     * 
     * Проверяет, что авторизованный пользователь может
     * удалить товар из избранного через AJAX
     */
    public function test_remove_item_ajax_authenticated(): void
    {
        // Получаем тестового пользователя как модель User
        $user = \App\Models\User::find($this->testUserId);
        
        // Авторизуемся и добавляем товар в избранное
        $this->actingAs($user);
        DB::insert(
            "INSERT INTO favorites (user_id, product_id, created_at) VALUES (?, ?, NOW())",
            [$this->testUserId, $this->testProductId]
        );
        
        // Отправляем AJAX-запрос на удаление
        $response = $this->post('/api/favorites/remove', [
            'product_id' => $this->testProductId
        ], [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);
        
        // Проверяем ответ
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'count' => 0
        ]);
        
        // Проверяем, что товар удален из БД
        $favorite = DB::selectOne(
            "SELECT * FROM favorites WHERE user_id = ? AND product_id = ?",
            [$this->testUserId, $this->testProductId]
        );
        $this->assertNull($favorite);
    }

    /**
     * Тест: AJAX удаление товара из избранного (неавторизованный пользователь)
     * 
     * Проверяет, что неавторизованный пользователь может
     * удалить товар из избранного через сессию
     */
    public function test_remove_item_ajax_guest(): void
    {
        // Добавляем товар в сессию
        session(['favorites' => [$this->testProductId]]);
        
        // Отправляем AJAX-запрос на удаление
        $response = $this->post('/api/favorites/remove', [
            'product_id' => $this->testProductId
        ], [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);
        
        // Проверяем ответ
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'count' => 0
        ]);
        
        // Проверяем, что товар удален из сессии
        $this->assertNotContains($this->testProductId, session('favorites', []));
    }

    /**
     * Тест: API endpoint получения списка избранного (task=get_favorites)
     * 
     * Проверяет, что единый API endpoint корректно возвращает
     * список избранного для авторизованных пользователей
     */
    public function test_get_favorites_api_authenticated(): void
    {
        // Получаем тестового пользователя как модель User
        $user = \App\Models\User::find($this->testUserId);
        
        // Авторизуемся и добавляем товар в избранное
        $this->actingAs($user);
        DB::insert(
            "INSERT INTO favorites (user_id, product_id, created_at) VALUES (?, ?, NOW())",
            [$this->testUserId, $this->testProductId]
        );
        
        // Отправляем запрос на получение списка
        $response = $this->post('/api/favorites', [
            'task' => 'get_favorites'
        ], [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);
        
        // Проверяем ответ
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'items',
            'count'
        ]);
        
        $data = $response->json();
        $this->assertEquals(1, $data['count']);
        $this->assertArrayHasKey($this->testProductId, $data['items']);
    }

    /**
     * Тест: API endpoint получения списка избранного для гостя
     * 
     * Проверяет, что API endpoint корректно возвращает
     * полные данные товаров для неавторизованных пользователей
     */
    public function test_get_favorites_api_guest(): void
    {
        // Добавляем товар в сессию
        session(['favorites' => [$this->testProductId]]);
        
        // Отправляем запрос на получение списка
        $response = $this->post('/api/favorites', [
            'task' => 'get_favorites'
        ], [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);
        
        // Проверяем ответ
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'items',
            'count'
        ]);
        
        $data = $response->json();
        $this->assertEquals(1, $data['count']);
        $this->assertArrayHasKey($this->testProductId, $data['items']);
        
        // Проверяем, что возвращаются полные данные товара
        $item = $data['items'][$this->testProductId];
        $this->assertArrayHasKey('id', $item);
        $this->assertArrayHasKey('name', $item);
        $this->assertArrayHasKey('price', $item);
        $this->assertArrayHasKey('image', $item);
    }

    /**
     * Тест: Валидация - добавление без product_id
     * 
     * Проверяет, что система корректно обрабатывает
     * запрос без обязательного параметра
     */
    public function test_add_item_without_product_id(): void
    {
        $response = $this->post('/api/favorites/add', [], [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);
        
        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'message' => 'Не указан ID товара'
        ]);
    }

    /**
     * Тест: Валидация - удаление без product_id
     * 
     * Проверяет, что система корректно обрабатывает
     * запрос без обязательного параметра
     */
    public function test_remove_item_without_product_id(): void
    {
        $response = $this->post('/api/favorites/remove', [], [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);
        
        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'message' => 'Не указан ID товара'
        ]);
    }

    /**
     * Тест: API endpoint с неизвестной задачей
     * 
     * Проверяет, что система корректно обрабатывает
     * запрос с неизвестным параметром task
     */
    public function test_handle_ajax_unknown_task(): void
    {
        $response = $this->post('/api/favorites', [
            'task' => 'unknown_task'
        ], [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);
        
        $response->assertStatus(400);
        $response->assertJson([
            'error' => 'Unknown task'
        ]);
    }

    /**
     * Тест: Множественное добавление и удаление
     * 
     * Проверяет, что система корректно обрабатывает
     * последовательные операции добавления и удаления
     */
    public function test_multiple_add_and_remove(): void
    {
        // Создаем второй тестовый товар
        DB::insert(
            "INSERT INTO products (id, name, description) VALUES (?, ?, ?)",
            ['test_product_http_002', 'Второй тестовый товар HTTP', 'Описание']
        );
        
        // Добавляем первый товар
        $response1 = $this->post('/api/favorites/add', [
            'product_id' => $this->testProductId
        ]);
        $response1->assertJson(['success' => true, 'count' => 1]);
        
        // Добавляем второй товар
        $response2 = $this->post('/api/favorites/add', [
            'product_id' => 'test_product_http_002'
        ]);
        $response2->assertJson(['success' => true, 'count' => 2]);
        
        // Удаляем первый товар
        $response3 = $this->post('/api/favorites/remove', [
            'product_id' => $this->testProductId
        ]);
        $response3->assertJson(['success' => true, 'count' => 1]);
        
        // Проверяем, что в сессии остался только второй товар
        $favorites = session('favorites', []);
        $this->assertCount(1, $favorites);
        $this->assertContains('test_product_http_002', $favorites);
        
        // Удаляем второй тестовый товар
        DB::delete("DELETE FROM products WHERE id = ?", ['test_product_http_002']);
    }
}
