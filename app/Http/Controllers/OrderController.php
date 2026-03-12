<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Session;
use Exception;

/**
 * Контроллер для оформления заказов
 * 
 * Обрабатывает отображение формы оформления заказа и создание заказа
 */
class OrderController extends Controller
{
    /**
     * @var OrderService Сервис для работы с заказами
     */
    private OrderService $orderService;

    /**
     * @var CartService Сервис для работы с корзиной
     */
    private CartService $cartService;

    /**
     * Конструктор контроллера
     * 
     * @param OrderService $orderService
     * @param CartService $cartService
     */
    public function __construct(OrderService $orderService, CartService $cartService)
    {
        $this->orderService = $orderService;
        $this->cartService = $cartService;
    }

    /**
     * Отобразить форму оформления заказа (GET /checkout)
     * 
     * @return View
     */
    public function checkout(): View
    {
        // Получаем данные корзины
        $cart = $this->cartService->getCartData();

        // Проверяем, что корзина не пуста
        if (empty($cart['items'])) {
            return redirect()->route('cart')->with('error', 'Корзина пуста');
        }

        // Возвращаем view с данными корзины
        return view('checkout.index', [
            'cart' => $cart
        ]);
    }

    /**
     * Создать заказ (POST /checkout)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function placeOrder(Request $request): JsonResponse
    {
        // Валидация входных данных
        $validated = $request->validate([
            'recipientName' => 'required|string|max:255',
            'recipientPhone' => 'required|string|max:20',
            'recipientEmail' => 'nullable|email|max:255',
            'orderComment' => 'nullable|string|max:1000',
            'delivery' => 'required|string|in:pickup,courier,express',
            'payment' => 'required|string|in:card,cash,sberpay'
        ]);

        // Получаем корзину из сессии
        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Корзина пуста'
            ]);
        }

        // Получаем полные данные корзины
        $cartData = $this->cartService->getCartData($cart);

        // Подготавливаем данные заказа
        $orderData = [
            'name' => $validated['recipientName'],
            'phone' => $validated['recipientPhone'],
            'email' => $validated['recipientEmail'] ?? null,
            'comment_user' => $validated['orderComment'] ?? null,
            'delivery' => $validated['delivery'],
            'payment' => $validated['payment']
        ];

        try {
            // Создаем заказ через OrderService
            $result = $this->orderService->createOrder($orderData, $cartData['items']);

            // Очищаем корзину после успешного создания заказа
            $this->cartService->clearCart();

            // Сохраняем номер заказа в сессии для отображения на странице благодарности
            Session::put('order_num', $result['order_num']);

            // Возвращаем успешный результат
            return response()->json($result);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Отобразить страницу благодарности (GET /thankyoupage)
     * 
     * @return View
     */
    public function thankyoupage(): View
    {
        return view('thankyoupage.index');
    }

    /**
     * Получить список пунктов выдачи в пределах карты (AJAX)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getPickpointList(Request $request): JsonResponse
    {
        $bounds = $request->input('bounds');
        
        if (!$bounds || !is_array($bounds) || count($bounds) < 2) {
            return response()->json([
                'result' => false,
                'message' => 'Неверные границы карты'
            ]);
        }

        // Получаем точки в пределах границ карты
        // bounds[0][0] - минимальная широта, bounds[1][0] - максимальная широта
        // bounds[0][1] - минимальная долгота, bounds[1][1] - максимальная долгота
        $points = \DB::table('points')
            ->select('id', 'la', 'lo')
            ->where('la', '>', $bounds[0][0])
            ->where('la', '<', $bounds[1][0])
            ->where('lo', '>', $bounds[0][1])
            ->where('lo', '<', $bounds[1][1])
            ->get()
            ->toArray();

        return response()->json([
            'result' => true,
            'points' => $points
        ]);
    }

    /**
     * Получить данные пункта выдачи по координатам (AJAX)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getPickpointData(Request $request): JsonResponse
    {
        $coords = $request->input('coords');
        
        if (!$coords || !is_array($coords) || count($coords) < 2) {
            return response()->json([
                'result' => false,
                'message' => 'Неверные координаты'
            ]);
        }

        // Получаем точку по координатам
        $point = \DB::table('points')
            ->where('la', $coords[0])
            ->where('lo', $coords[1])
            ->first();

        if (!$point) {
            return response()->json([
                'result' => false,
                'message' => 'Пункт выдачи не найден'
            ]);
        }

        // Декодируем JSON данные
        $pointData = json_decode($point->json, true);

        // Сохраняем выбранный пункт в сессию
        Session::put('delivery', $point);

        return response()->json([
            'result' => true,
            'point_data' => $pointData
        ]);
    }
}
