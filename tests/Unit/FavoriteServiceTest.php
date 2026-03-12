<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\FavoriteService;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Юнит-тесты для FavoriteService
 * 
 * Тестируют методы работы с избранным:
 * - addItem() - добавление товара в избранное
 * - removeItem() - удаление товара из избранного
 * - getItems() - получение списка избранного
 * - getItemsCount() - получение количества товаров
 * - isInFavorites() - проверка наличия товара в избранном
 * 
 * ВАЖНО: Используются транзакции для безопасной работы с production БД
 * Все тестовые данные откатываются после выполнения тестов
 */
class FavoriteServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected FavoriteService $favoriteService;
    protected int $testUserId;
    protected string $testProductId;

    /**
     * Подготовка перед каждым тестом
     * 
     * Создаем тестового пользователя и тестовый товар
     * Используем префикс test_ для идентификации тестовых данных
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->favoriteService = new FavoriteService();
        
        // Создаем тестового пользователя
        DB::insert(
            "INSERT INTO users (name, email, password) VALUES (?, ?, ?)",
            ['test_user_favorites', 'test_favorites@example.com', bcrypt('password')]
        );
        
        $user = DB::selectOne(
            "SELECT id FROM users WHERE email = ? LIMIT 1",
            ['test_favorites@example.com']
        );
        $this->testUserId = $user->id;
        
        // Создаем тестовый товар
        DB::insert(
            "INSERT INTO products (id, name, description) VALUES (?, ?, ?)",
            ['test_product_001', 'Тестовый товар для избранного', 'Описание тестового товара']
        );
        $this->testProductId = 'test_product_001';
    }

    /**
     * Тест: Добавление товара в избранное
     * 
     * Проверяет, что товар успешно добавляется в избранное
     * и возвращается корректный ответ
     */
    public function test_add_item_to_favorites(): void
    {
        // Добавляем товар в избранное
        $result = $this->favoriteService->addItem($this->testProductId, $this->testUserId);
        
        // Проверяем успешность операции
        $this->assertTrue($result['success']);
        $this->assertEquals('Товар добавлен в избранное', $result['message']);
        $this->assertEquals(1, $result['count']);
        
        // Проверяем, что запись создана в БД
        $favorite = DB::selectOne(
            "SELECT * FROM favorites WHERE user_id = ? AND product_id = ?",
            [$this->testUserId, $this->testProductId]
        );
        $this->assertNotNull($favorite);
    }

    /**
     * Тест: Добавление товара, который уже в избранном
     * 
     * Проверяет, что повторное добавление не создает дубликат
     */
    public function test_add_item_already_in_favorites(): void
    {
        // Добавляем товар первый раз
        $this->favoriteService->addItem($this->testProductId, $this->testUserId);
        
        // Пытаемся добавить второй раз
        $result = $this->favoriteService->addItem($this->testProductId, $this->testUserId);
        
        // Проверяем, что операция успешна, но товар уже был в избранном
        $this->assertTrue($result['success']);
        $this->assertEquals('Товар уже в избранном', $result['message']);
        $this->assertEquals(1, $result['count']);
        
        // Проверяем, что в БД только одна запись
        $count = DB::selectOne(
            "SELECT COUNT(*) as count FROM favorites WHERE user_id = ? AND product_id = ?",
            [$this->testUserId, $this->testProductId]
        );
        $this->assertEquals(1, $count->count);
    }

    /**
     * Тест: Добавление несуществующего товара
     * 
     * Проверяет, что система корректно обрабатывает попытку
     * добавить товар, которого нет в БД
     */
    public function test_add_nonexistent_product(): void
    {
        $result = $this->favoriteService->addItem('nonexistent_product', $this->testUserId);
        
        $this->assertFalse($result['success']);
        $this->assertEquals('Товар не найден', $result['message']);
    }

    /**
     * Тест: Удаление товара из избранного
     * 
     * Проверяет, что товар успешно удаляется из избранного
     */
    public function test_remove_item_from_favorites(): void
    {
        // Сначала добавляем товар
        $this->favoriteService->addItem($this->testProductId, $this->testUserId);
        
        // Удаляем товар
        $result = $this->favoriteService->removeItem($this->testProductId, $this->testUserId);
        
        // Проверяем успешность операции
        $this->assertTrue($result['success']);
        $this->assertEquals('Товар удален из избранного', $result['message']);
        $this->assertEquals(0, $result['count']);
        
        // Проверяем, что запись удалена из БД
        $favorite = DB::selectOne(
            "SELECT * FROM favorites WHERE user_id = ? AND product_id = ?",
            [$this->testUserId, $this->testProductId]
        );
        $this->assertNull($favorite);
    }

    /**
     * Тест: Удаление товара, которого нет в избранном
     * 
     * Проверяет, что система корректно обрабатывает попытку
     * удалить товар, которого нет в избранном
     */
    public function test_remove_item_not_in_favorites(): void
    {
        $result = $this->favoriteService->removeItem($this->testProductId, $this->testUserId);
        
        $this->assertTrue($result['success']);
        $this->assertEquals('Товар не был в избранном', $result['message']);
        $this->assertEquals(0, $result['count']);
    }

    /**
     * Тест: Получение списка избранного
     * 
     * Проверяет, что метод getItems() возвращает корректный список
     * товаров с полными данными
     */
    public function test_get_items(): void
    {
        // Добавляем товар в избранное
        $this->favoriteService->addItem($this->testProductId, $this->testUserId);
        
        // Получаем список избранного
        $result = $this->favoriteService->getItems($this->testUserId);
        
        // Проверяем структуру ответа
        $this->assertIsArray($result);
        $this->assertArrayHasKey('items', $result);
        $this->assertArrayHasKey('count', $result);
        $this->assertEquals(1, $result['count']);
        
        // Проверяем данные товара
        $this->assertCount(1, $result['items']);
        $item = $result['items'][0];
        $this->assertEquals($this->testProductId, $item['id']);
        $this->assertEquals('Тестовый товар для избранного', $item['name']);
        $this->assertArrayHasKey('price', $item);
        $this->assertArrayHasKey('image', $item);
        $this->assertArrayHasKey('rating', $item);
    }

    /**
     * Тест: Получение пустого списка избранного
     * 
     * Проверяет, что для пользователя без избранного
     * возвращается пустой список
     */
    public function test_get_empty_items(): void
    {
        $result = $this->favoriteService->getItems($this->testUserId);
        
        $this->assertIsArray($result);
        $this->assertEquals(0, $result['count']);
        $this->assertEmpty($result['items']);
    }

    /**
     * Тест: Получение количества товаров в избранном
     * 
     * Проверяет, что метод getItemsCount() возвращает
     * корректное количество товаров
     */
    public function test_get_items_count(): void
    {
        // Изначально 0 товаров
        $count = $this->favoriteService->getItemsCount($this->testUserId);
        $this->assertEquals(0, $count);
        
        // Добавляем товар
        $this->favoriteService->addItem($this->testProductId, $this->testUserId);
        
        // Проверяем, что счетчик увеличился
        $count = $this->favoriteService->getItemsCount($this->testUserId);
        $this->assertEquals(1, $count);
    }

    /**
     * Тест: Проверка наличия товара в избранном
     * 
     * Проверяет, что метод isInFavorites() корректно
     * определяет наличие товара в избранном
     */
    public function test_is_in_favorites(): void
    {
        // Изначально товара нет в избранном
        $isInFavorites = $this->favoriteService->isInFavorites($this->testProductId, $this->testUserId);
        $this->assertFalse($isInFavorites);
        
        // Добавляем товар
        $this->favoriteService->addItem($this->testProductId, $this->testUserId);
        
        // Проверяем, что товар теперь в избранном
        $isInFavorites = $this->favoriteService->isInFavorites($this->testProductId, $this->testUserId);
        $this->assertTrue($isInFavorites);
    }

    /**
     * Тест: Получение данных товаров по массиву ID
     * 
     * Проверяет, что метод getItemsByIds() возвращает
     * корректные данные для неавторизованных пользователей
     */
    public function test_get_items_by_ids(): void
    {
        // Создаем второй тестовый товар
        DB::insert(
            "INSERT INTO products (id, name, description) VALUES (?, ?, ?)",
            ['test_product_002', 'Второй тестовый товар', 'Описание второго товара']
        );
        
        // Получаем данные товаров по ID
        $result = $this->favoriteService->getItemsByIds([
            $this->testProductId,
            'test_product_002'
        ]);
        
        // Проверяем структуру ответа
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        
        // Проверяем, что данные в формате [product_id => data]
        $this->assertArrayHasKey($this->testProductId, $result);
        $this->assertArrayHasKey('test_product_002', $result);
        
        // Проверяем данные первого товара
        $item1 = $result[$this->testProductId];
        $this->assertEquals($this->testProductId, $item1['id']);
        $this->assertEquals('Тестовый товар для избранного', $item1['name']);
        $this->assertArrayHasKey('price', $item1);
        $this->assertArrayHasKey('image', $item1);
        
        // Удаляем второй тестовый товар
        DB::delete("DELETE FROM products WHERE id = ?", ['test_product_002']);
    }

    /**
     * Тест: Валидация параметров
     * 
     * Проверяет, что методы корректно обрабатывают
     * некорректные параметры
     */
    public function test_validation(): void
    {
        // Пустой product_id
        $result = $this->favoriteService->addItem('', $this->testUserId);
        $this->assertFalse($result['success']);
        $this->assertEquals('Некорректные параметры', $result['message']);
        
        // Пустой user_id
        $result = $this->favoriteService->addItem($this->testProductId, 0);
        $this->assertFalse($result['success']);
        $this->assertEquals('Некорректные параметры', $result['message']);
    }
}
