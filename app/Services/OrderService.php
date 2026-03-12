<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use DateTime;
use Exception;

/**
 * Сервис для работы с заказами
 * 
 * Обеспечивает бизнес-логику создания заказов:
 * - Генерация уникальных номеров заказов
 * - Валидация данных заказа
 * - Создание записей в БД (orders, order_positions)
 * - Расчет итоговых сумм
 */
class OrderService
{
    /**
     * Конструктор сервиса
     */
    public function __construct()
    {
        //
    }

    /**
     * Создать заказ
     * 
     * @param array $orderData Данные заказа (name, phone, email, comment_user, delivery, payment)
     * @param array $cartItems Товары из корзины
     * @return array ['success' => bool, 'order_num' => int, 'redirect' => string]
     * @throws Exception
     */
    public function createOrder(array $orderData, array $cartItems): array
    {
        try {
            // Шаг 1: Валидация данных заказа
            $this->validateOrderData($orderData);
            
            // Шаг 2: Создание заказа в транзакции
            $result = DB::transaction(function () use ($orderData, $cartItems) {
                // Шаг 3: Генерация уникального номера заказа
                $orderKeys = $this->generateOrderNumber();
                
                // Шаг 4: Создание позиций заказа
                $totalSum = $this->createOrderPositions(
                    $orderKeys['order_num'],
                    $orderKeys['order_code'],
                    $cartItems
                );
                
                // Шаг 5: Создание записи заказа
                $this->createOrderRecord(
                    $orderKeys['order_num'],
                    $orderKeys['order_code'],
                    $orderData,
                    $totalSum
                );
                
                return [
                    'success' => true,
                    'order_num' => $orderKeys['order_num'],
                    'redirect' => '/thankyoupage/'
                ];
            });
            
            return $result;
            
        } catch (Exception $e) {
            // Логирование ошибки
            Log::error('Ошибка при создании заказа', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'orderData' => $orderData
            ]);
            
            throw $e;
        }
    }

    /**
     * Валидировать данные заказа
     * 
     * @param array $orderData Данные заказа
     * @return void
     * @throws Exception Если данные невалидны
     */
    private function validateOrderData(array $orderData): void
    {
        // Проверка обязательного поля "ФИО получателя"
        if (empty($orderData['name'])) {
            throw new Exception('ФИО получателя обязательно');
        }
        
        // Проверка обязательного поля "Телефон получателя"
        if (empty($orderData['phone'])) {
            throw new Exception('Телефон получателя обязателен');
        }
        
        // Проверка формата email (если указан)
        if (!empty($orderData['email']) && !filter_var($orderData['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Некорректный формат email');
        }
    }

    /**
     * Сгенерировать уникальный номер заказа
     * 
     * @return array ['order_num' => int, 'order_code' => string]
     */
    private function generateOrderNumber(): array
    {
        // Базовая дата для расчета (2025-12-10)
        $baseDate = new DateTime('2025-12-10 00:00:00');
        $currentDate = new DateTime('now');
        
        // Вычисляем разницу в секундах
        $interval = $baseDate->diff($currentDate);
        
        // Преобразуем интервал в секунды
        $seconds = ($interval->days * 86400) + 
                   ($interval->h * 3600) + 
                   ($interval->i * 60) + 
                   $interval->s;
        
        // Добавляем случайную задержку для обеспечения уникальности
        usleep(rand(1, 1000000));
        
        // order_num = количество секунд с базовой даты
        $orderNum = (int)$seconds;
        
        // order_code = base36 представление, разбитое на группы по 3 символа
        $base36 = base_convert($orderNum, 10, 36);
        
        // Разбиваем на группы по 3 символа с разделителем "-"
        $groups = str_split($base36, 3);
        $orderCode = strtoupper(implode('-', $groups));
        
        return [
            'order_num' => $orderNum,
            'order_code' => $orderCode
        ];
    }

    /**
     * Создать позиции заказа
     * 
     * @param int $orderNum Номер заказа
     * @param string $orderCode Код заказа
     * @param array $cartItems Товары из корзины
     * @return float Итоговая сумма всех позиций
     */
    private function createOrderPositions(int $orderNum, string $orderCode, array $cartItems): float
    {
        $totalSum = 0.0;
        
        foreach ($cartItems as $item) {
            // Пропускаем невыбранные товары
            if (empty($item['selected'])) {
                continue;
            }
            
            // Получаем данные товара из БД с ценой
            $product = DB::table('products as p')
                ->leftJoin('prices as pr', function($join) {
                    $join->on('p.id', '=', 'pr.product_id')
                         ->where('pr.price_type_id', '=', '000000002');
                })
                ->where('p.id', $item['guid'])
                ->select('p.*', 'pr.price as cost')
                ->first();
            
            // Если товар не найден - пропускаем
            if (!$product) {
                continue;
            }
            
            // Используем цену из корзины или из БД
            $cost = $item['cost'] ?? $product->cost ?? 0;
            $pieces = intval($item['product_amount'] ?? 1);
            
            // Рассчитываем сумму позиции
            $sum = $cost * $pieces;
            $totalSum += $sum;
            
            // Создаем запись в order_positions
            DB::table('order_positions')->insert([
                'order_num' => $orderNum,
                'order_code' => $orderCode,
                'pieces' => $pieces,
                'bill' => $cost,
                'cost' => $cost,
                'piece_cost' => $cost,
                'amount' => $pieces,
                'sum' => $sum,
                'art' => $product->art ?? '',
                'guid' => $product->id,
                'title' => $product->name ?? '',
                'model' => $product->model ?? null,
                'weight' => floatval($product->weight ?? 0),
                'piece_weight' => floatval($product->weight ?? 0)
            ]);
        }
        
        return $totalSum;
    }

    /**
     * Создать запись заказа в БД
     * 
     * @param int $orderNum Номер заказа
     * @param string $orderCode Код заказа
     * @param array $orderData Данные заказа
     * @param float $totalSum Итоговая сумма позиций
     * @return void
     */
    private function createOrderRecord(int $orderNum, string $orderCode, array $orderData, float $totalSum): void
    {
        // Рассчитываем итоговые суммы
        $fullSum = $totalSum;
        $discountSum = floatval(Session::get('cart_discount', 0));
        $paySum = $fullSum - $discountSum;
        
        // pay_sum не может быть отрицательной
        if ($paySum < 0) {
            $paySum = 0;
        }
        
        // Получаем user_id из сессии (0 для неавторизованных)
        $userId = intval(Session::get('user_id', 0));
        
        // Создаем запись в таблице orders
        DB::table('orders')->insert([
            'id' => $orderNum,
            'order_code' => $orderCode,
            'date_init' => now(),
            'status' => 0,  // 0 = новый заказ
            'full_sum' => $fullSum,
            'discount_sum' => $discountSum,
            'pay_sum' => $paySum,
            'bonus' => 0,
            'cart_weight' => 0,
            'cart_volume' => 0,
            'cart_density' => 0,
            'name' => $orderData['name'],
            'phone' => $orderData['phone'],
            'email' => $orderData['email'] ?? null,
            'comment_user' => $orderData['comment_user'] ?? null,
            'tracking_id' => null,
            'checkoutOrderId' => null,
            'user_id' => $userId,
            'user_role' => null,
            'user_card_code' => null,
            'ip' => $this->getClientIp(),
            'user_agent' => substr(request()->userAgent() ?? '', 0, 255)
        ]);
    }

    /**
     * Получить IP адрес клиента
     * 
     * @return string IP адрес
     */
    private function getClientIp(): string
    {
        // Проверяем заголовки прокси
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            // Берем первый IP из списка (если их несколько)
            $ips = explode(',', $ip);
            $ip = trim($ips[0]);
        } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }
        
        // Валидация IP адреса
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            $ip = '0.0.0.0';
        }
        
        return $ip;
    }
}
