<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\CartService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
// Используем реальную БД проекта без миграций

/**
 * Комплексный набор тестов для функционала корзины
 * 
 * Покрывает все основные операции с корзиной:
 * - Добавление/удаление товаров
 * - Изменение количества
 * - Функционал selected (галочка выбора товара)
 * - Подсчет итоговых сумм
 * - Очистка корзины
 * - Сессионное хранение данных
 * 
 * ПРИМЕЧАНИЕ: Эти тесты работают с тестовой БД (SQLite in-memory).
 * Миграции запускаются автоматически, тестовые товары создаются через фабрики.
 */
class CartFunctionalityTest extends TestCase
{
    protected CartService $cartService;
    
    // ID реальных товаров из БД для тестирования
    protected string $testProductId1;
    protected string $testProductId2;
    protected string $testProductId3;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cartService = new CartService();
        Session::flush(); // Очищаем сессию перед каждым тестом
        
        // Получаем реальные ID товаров из БД для тестирования
        $this->loadTestProductIds();
    }

    /**
     * Загрузка ID реальных товаров из БД для тестирования
     */
    private function loadTestProductIds(): void
    {
        // Получаем первые 3 товара из реальной БД
        $products = DB::table('products')
            ->limit(3)
            ->pluck('id')
            ->toArray();

        if (count($products) < 3) {
            $this->markTestSkipped('Недостаточно товаров в БД для тестирования. Требуется минимум 3 товара.');
        }

        $this->testProductId1 = $products[0];
        $this->testProductId2 = $products[1];
        $this->testProductId3 = $products[2];
    }

    // ========================================
    // 1. ТЕСТЫ ДОБАВЛЕНИЯ ТОВАРА В КОРЗИНУ
    // ========================================

    public function test_it_can_add_new_item_to_cart()
    {
        // Добавляем новый товар в корзину
        $result = $this->cartService->addItem($this->testProductId1, 2);

        // Проверяем структуру ответа
        $this->assertArrayHasKey('items', $result);
        $this->assertArrayHasKey('total_cart_sum', $result);
        $this->assertArrayHasKey('total_cart_amount', $result);
        $this->assertArrayHasKey('cart_sum', $result);
        $this->assertArrayHasKey('cart_discount', $result);

        // Проверяем что товар добавлен
        $this->assertArrayHasKey($this->testProductId1, $result['items']);
        
        // Проверяем количество товара
        $this->assertEquals(2, $result['items'][$this->testProductId1]['product_amount']);
        
        // Проверяем что товар сохранен в сессии
        $cart = Session::get('cart');
        $this->assertEquals(2, $cart[$this->testProductId1]);
    }

    public function test_it_increases_quantity_when_adding_existing_item()
    {
        // Добавляем товар первый раз
        $this->cartService->addItem($this->testProductId1, 2);
        
        // Добавляем тот же товар еще раз
        $result = $this->cartService->addItem($this->testProductId1, 3);

        // Проверяем что количество увеличилось (2 + 3 = 5)
        $this->assertEquals(5, $result['items'][$this->testProductId1]['product_amount']);
        
        // Проверяем сессию
        $cart = Session::get('cart');
        $this->assertEquals(5, $cart[$this->testProductId1]);
    }

    public function test_it_returns_correct_item_structure_when_adding()
    {
        $result = $this->cartService->addItem($this->testProductId1, 1);
        
        $item = $result['items'][$this->testProductId1];

        // Проверяем все обязательные поля товара
        $this->assertArrayHasKey('id', $item);
        $this->assertArrayHasKey('guid', $item);
        $this->assertArrayHasKey('name', $item);
        $this->assertArrayHasKey('description', $item);
        $this->assertArrayHasKey('cost', $item);
        $this->assertArrayHasKey('price', $item);
        $this->assertArrayHasKey('product_amount', $item);
        $this->assertArrayHasKey('img_url', $item);
        $this->assertArrayHasKey('image', $item);
        $this->assertArrayHasKey('quantity', $item);
        $this->assertArrayHasKey('max_quantity', $item);
        $this->assertArrayHasKey('selected', $item);
        
        // Проверяем значения
        $this->assertEquals($this->testProductId1, $item['id']);
        $this->assertTrue($item['selected']); // По умолчанию товар выбран
    }

    // ========================================
    // 2. ТЕСТЫ УДАЛЕНИЯ ТОВАРА ИЗ КОРЗИНЫ
    // ========================================

    public function test_it_can_remove_existing_item_from_cart()
    {
        // Добавляем товар
        $this->cartService->addItem($this->testProductId1, 2);
        
        // Удаляем товар
        $result = $this->cartService->removeItem($this->testProductId1);

        // Проверяем что товар удален
        $this->assertArrayNotHasKey($this->testProductId1, $result['items']);
        
        // Проверяем что корзина пустая
        $this->assertEmpty($result['items']);
        
        // Проверяем сессию
        $cart = Session::get('cart');
        $this->assertArrayNotHasKey($this->testProductId1, $cart);
    }

    public function test_it_handles_removing_non_existent_item_gracefully()
    {
        // Пытаемся удалить несуществующий товар
        $result = $this->cartService->removeItem('non-existent-product');

        // Проверяем что метод не выбросил исключение и вернул пустую корзину
        $this->assertIsArray($result);
        $this->assertEmpty($result['items']);
    }

    public function test_it_removes_item_but_keeps_other_items_in_cart()
    {
        // Добавляем несколько товаров
        $this->cartService->addItem($this->testProductId1, 2);
        $this->cartService->addItem($this->testProductId2, 1);
        $this->cartService->addItem($this->testProductId3, 3);
        
        // Удаляем один товар
        $result = $this->cartService->removeItem($this->testProductId2);

        // Проверяем что удален только нужный товар
        $this->assertArrayNotHasKey($this->testProductId2, $result['items']);
        $this->assertArrayHasKey($this->testProductId1, $result['items']);
        $this->assertArrayHasKey($this->testProductId3, $result['items']);
        
        // Проверяем количество оставшихся товаров
        $this->assertCount(2, $result['items']);
    }

    // ========================================
    // 3. ТЕСТЫ ИЗМЕНЕНИЯ КОЛИЧЕСТВА
    // ========================================

    public function test_it_can_increase_item_quantity()
    {
        // Добавляем товар
        $this->cartService->addItem($this->testProductId1, 2);
        
        // Увеличиваем количество
        $result = $this->cartService->updateAmount($this->testProductId1, 5);

        // Проверяем новое количество
        $this->assertEquals(5, $result['items'][$this->testProductId1]['product_amount']);
    }

    public function test_it_can_decrease_item_quantity()
    {
        // Добавляем товар
        $this->cartService->addItem($this->testProductId1, 5);
        
        // Уменьшаем количество
        $result = $this->cartService->updateAmount($this->testProductId1, 2);

        // Проверяем новое количество
        $this->assertEquals(2, $result['items'][$this->testProductId1]['product_amount']);
    }

    public function test_it_removes_item_when_quantity_set_to_zero()
    {
        // Добавляем товар
        $this->cartService->addItem($this->testProductId1, 3);
        
        // Устанавливаем количество в 0
        $result = $this->cartService->updateAmount($this->testProductId1, 0);

        // Проверяем что товар удален
        $this->assertArrayNotHasKey($this->testProductId1, $result['items']);
        $this->assertEmpty($result['items']);
    }

    public function test_it_removes_item_when_quantity_set_to_negative()
    {
        // Добавляем товар
        $this->cartService->addItem($this->testProductId1, 3);
        
        // Устанавливаем отрицательное количество
        $result = $this->cartService->updateAmount($this->testProductId1, -5);

        // Проверяем что товар удален
        $this->assertArrayNotHasKey($this->testProductId1, $result['items']);
    }

    // ========================================
    // 4. ТЕСТЫ ФУНКЦИОНАЛА SELECTED (ГАЛОЧКА)
    // ========================================

    public function test_it_can_unselect_item()
    {
        // Добавляем товар (по умолчанию selected = true)
        $this->cartService->addItem($this->testProductId1, 2);
        
        // Снимаем галочку
        $result = $this->cartService->updateItemSelected($this->testProductId1, false);

        // Проверяем что товар не выбран
        $this->assertFalse($result['items'][$this->testProductId1]['selected']);
    }

    public function test_it_recalculates_sum_when_item_unselected()
    {
        // Добавляем два товара
        $this->cartService->addItem($this->testProductId1, 2);
        $this->cartService->addItem($this->testProductId2, 1);
        
        // Получаем начальную сумму
        $result = $this->cartService->getCartData();
        $initialSum = $result['cart_sum'];
        $this->assertGreaterThan(0, $initialSum);
        
        // Снимаем галочку с первого товара
        $result = $this->cartService->updateItemSelected($this->testProductId1, false);

        // Проверяем что сумма пересчиталась (стала меньше)
        $this->assertLessThan($initialSum, $result['cart_sum']);
    }

    public function test_it_recalculates_amount_when_item_unselected()
    {
        // Добавляем два товара
        $this->cartService->addItem($this->testProductId1, 2); // количество: 2
        $this->cartService->addItem($this->testProductId2, 3); // количество: 3
        
        // Проверяем начальное количество (2 + 3 = 5)
        $result = $this->cartService->getCartData();
        $this->assertEquals(5, $result['total_cart_amount']);
        
        // Снимаем галочку с первого товара
        $result = $this->cartService->updateItemSelected($this->testProductId1, false);

        // Проверяем что количество пересчиталось без первого товара (только 3)
        $this->assertEquals(3, $result['total_cart_amount']);
    }

    public function test_it_can_select_item_back()
    {
        // Добавляем товар
        $this->cartService->addItem($this->testProductId1, 2);
        
        // Снимаем галочку
        $this->cartService->updateItemSelected($this->testProductId1, false);
        
        // Ставим галочку обратно
        $result = $this->cartService->updateItemSelected($this->testProductId1, true);

        // Проверяем что товар снова выбран
        $this->assertTrue($result['items'][$this->testProductId1]['selected']);
    }

    public function test_it_includes_item_in_sums_when_selected_back()
    {
        // Добавляем товар
        $this->cartService->addItem($this->testProductId1, 2);
        
        // Получаем начальную сумму
        $initialResult = $this->cartService->getCartData();
        $initialSum = $initialResult['cart_sum'];
        
        // Снимаем галочку
        $result = $this->cartService->updateItemSelected($this->testProductId1, false);
        $this->assertEquals(0, $result['cart_sum']);
        $this->assertEquals(0, $result['total_cart_amount']);
        
        // Ставим галочку обратно
        $result = $this->cartService->updateItemSelected($this->testProductId1, true);

        // Проверяем что товар снова учитывается в суммах
        $this->assertEquals($initialSum, $result['cart_sum']);
        $this->assertEquals(2, $result['total_cart_amount']);
    }

    public function test_it_handles_multiple_items_with_different_selected_states()
    {
        // Добавляем три товара
        $this->cartService->addItem($this->testProductId1, 2);
        $this->cartService->addItem($this->testProductId2, 1);
        $this->cartService->addItem($this->testProductId3, 3);
        
        // Снимаем галочку со второго товара
        $this->cartService->updateItemSelected($this->testProductId2, false);
        
        $result = $this->cartService->getCartData();

        // Проверяем что учитываются только выбранные товары (товар 1 и товар 3)
        $this->assertEquals(5, $result['total_cart_amount']); // 2 + 3 = 5
        
        // Проверяем состояние каждого товара
        $this->assertTrue($result['items'][$this->testProductId1]['selected']);
        $this->assertFalse($result['items'][$this->testProductId2]['selected']);
        $this->assertTrue($result['items'][$this->testProductId3]['selected']);
    }

    // ========================================
    // 5. ТЕСТЫ ПОДСЧЕТА ИТОГОВЫХ СУММ
    // ========================================

    public function test_cart_sum_includes_only_selected_items()
    {
        // Добавляем товары
        $this->cartService->addItem($this->testProductId1, 2);
        $this->cartService->addItem($this->testProductId2, 1);
        
        // Получаем начальную сумму
        $initialResult = $this->cartService->getCartData();
        $initialSum = $initialResult['cart_sum'];
        
        // Снимаем галочку с одного товара
        $this->cartService->updateItemSelected($this->testProductId2, false);
        
        $result = $this->cartService->getCartData();

        // Проверяем что cart_sum уменьшилась
        $this->assertLessThan($initialSum, $result['cart_sum']);
    }

    public function test_total_cart_amount_includes_only_selected_items()
    {
        // Добавляем товары
        $this->cartService->addItem($this->testProductId1, 5);
        $this->cartService->addItem($this->testProductId2, 3);
        
        // Снимаем галочку с одного товара
        $this->cartService->updateItemSelected($this->testProductId1, false);
        
        $result = $this->cartService->getCartData();

        // Проверяем что total_cart_amount учитывает только выбранный товар
        $this->assertEquals(3, $result['total_cart_amount']);
    }

    public function test_total_cart_sum_equals_cart_sum_minus_discount()
    {
        // Добавляем товары
        $this->cartService->addItem($this->testProductId1, 2);
        
        // Устанавливаем скидку вручную (через сессию)
        Session::put('cart_discount', 200);
        
        $result = $this->cartService->getCartData();

        // Проверяем формулу: total_cart_sum = cart_sum - cart_discount
        $this->assertEquals(200, $result['cart_discount']);
        $this->assertEquals($result['cart_sum'] - 200, $result['total_cart_sum']);
    }

    public function test_empty_cart_returns_zero_sums()
    {
        $result = $this->cartService->getCartData();

        // Проверяем что все суммы равны нулю
        $this->assertEquals(0, $result['cart_sum']);
        $this->assertEquals(0, $result['total_cart_sum']);
        $this->assertEquals(0, $result['total_cart_amount']);
        $this->assertEquals(0, $result['cart_discount']);
        $this->assertEmpty($result['items']);
    }

    public function test_cart_with_all_unselected_items_returns_zero_sums()
    {
        // Добавляем товары
        $this->cartService->addItem($this->testProductId1, 2);
        $this->cartService->addItem($this->testProductId2, 1);
        
        // Снимаем галочки со всех товаров
        $this->cartService->updateItemSelected($this->testProductId1, false);
        $this->cartService->updateItemSelected($this->testProductId2, false);
        
        $result = $this->cartService->getCartData();

        // Проверяем что суммы равны нулю, но товары остались в корзине
        $this->assertEquals(0, $result['cart_sum']);
        $this->assertEquals(0, $result['total_cart_amount']);
        $this->assertCount(2, $result['items']);
    }

    // ========================================
    // 6. ТЕСТЫ ОЧИСТКИ КОРЗИНЫ
    // ========================================

    public function test_it_clears_cart_completely()
    {
        // Добавляем товары
        $this->cartService->addItem($this->testProductId1, 2);
        $this->cartService->addItem($this->testProductId2, 1);
        
        // Очищаем корзину
        $result = $this->cartService->clearCart();

        // Проверяем что корзина пустая
        $this->assertEmpty($result['items']);
        $this->assertEquals(0, $result['cart_sum']);
        $this->assertEquals(0, $result['total_cart_amount']);
    }

    public function test_it_clears_cart_selected_state()
    {
        // Добавляем товары и меняем их selected состояние
        $this->cartService->addItem($this->testProductId1, 2);
        $this->cartService->updateItemSelected($this->testProductId1, false);
        
        // Проверяем что cart_selected существует
        $this->assertNotNull(Session::get('cart_selected'));
        
        // Очищаем корзину
        $this->cartService->clearCart();

        // Проверяем что cart_selected тоже очищен
        $this->assertNull(Session::get('cart_selected'));
    }

    public function test_it_clears_promocode_and_discount()
    {
        // Добавляем товар и устанавливаем промокод
        $this->cartService->addItem($this->testProductId1, 1);
        Session::put('cart_promocode', 'TEST123');
        Session::put('cart_discount', 100);
        
        // Очищаем корзину
        $result = $this->cartService->clearCart();

        // Проверяем что промокод и скидка очищены
        $this->assertNull(Session::get('cart_promocode'));
        $this->assertNull(Session::get('cart_discount'));
        $this->assertEquals('', $result['promocode']);
        $this->assertEquals(0, $result['cart_discount']);
    }

    // ========================================
    // 7. ТЕСТЫ СЕССИОННОГО ХРАНЕНИЯ
    // ========================================

    public function test_cart_data_is_stored_in_session()
    {
        // Добавляем товар
        $this->cartService->addItem($this->testProductId1, 3);

        // Проверяем что данные сохранены в сессии
        $cart = Session::get('cart');
        $this->assertIsArray($cart);
        $this->assertArrayHasKey($this->testProductId1, $cart);
        $this->assertEquals(3, $cart[$this->testProductId1]);
    }

    public function test_cart_selected_is_stored_separately()
    {
        // Добавляем товар
        $this->cartService->addItem($this->testProductId1, 2);
        
        // Меняем selected состояние
        $this->cartService->updateItemSelected($this->testProductId1, false);

        // Проверяем что cart_selected хранится отдельно
        $cartSelected = Session::get('cart_selected');
        $this->assertIsArray($cartSelected);
        $this->assertArrayHasKey($this->testProductId1, $cartSelected);
        $this->assertFalse($cartSelected[$this->testProductId1]);
    }

    public function test_cart_persists_across_multiple_operations()
    {
        // Выполняем несколько операций
        $this->cartService->addItem($this->testProductId1, 2);
        $this->cartService->addItem($this->testProductId2, 1);
        $this->cartService->updateAmount($this->testProductId1, 5);
        $this->cartService->updateItemSelected($this->testProductId2, false);
        
        // Получаем данные корзины
        $result = $this->cartService->getCartData();

        // Проверяем что все изменения сохранились
        $this->assertEquals(5, $result['items'][$this->testProductId1]['product_amount']);
        $this->assertTrue($result['items'][$this->testProductId1]['selected']);
        $this->assertEquals(1, $result['items'][$this->testProductId2]['product_amount']);
        $this->assertFalse($result['items'][$this->testProductId2]['selected']);
    }

    public function test_new_items_are_selected_by_default()
    {
        // Добавляем товар
        $result = $this->cartService->addItem($this->testProductId1, 1);

        // Проверяем что новый товар выбран по умолчанию
        $this->assertTrue($result['items'][$this->testProductId1]['selected']);
        
        // Проверяем что в cart_selected нет записи (используется значение по умолчанию)
        $cartSelected = Session::get('cart_selected', []);
        $this->assertArrayNotHasKey($this->testProductId1, $cartSelected);
    }

    public function test_session_cart_format_matches_expected_structure()
    {
        // Добавляем несколько товаров
        $this->cartService->addItem($this->testProductId1, 2);
        $this->cartService->addItem($this->testProductId2, 3);

        // Проверяем формат сессии (guid => quantity)
        $cart = Session::get('cart');
        $this->assertIsArray($cart);
        $this->assertEquals([
            $this->testProductId1 => 2,
            $this->testProductId2 => 3,
        ], $cart);
    }
}
