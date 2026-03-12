<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Контроллер для управления заказами пользователя
 */
class OrdersController extends Controller
{
    /**
     * Отображение списка заказов пользователя
     * 
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        // Проверяем авторизацию
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Для просмотра заказов необходимо войти в систему');
        }

        $user = Auth::user();
        
        // Получаем фильтры из запроса
        $statusFilter = $request->input('status', 'all');
        $periodFilter = $request->input('period', 'all');

        // Базовый запрос для получения заказов пользователя по номеру телефона
        $query = "
            SELECT 
                o.id,
                o.order_code,
                o.status,
                o.full_sum as total_amount,
                o.name,
                o.phone,
                o.email,
                o.date_init as created_at
            FROM orders o
            WHERE o.phone = ?
        ";
        
        $params = [$user->phone];

        // Применяем фильтр по статусу
        if ($statusFilter !== 'all') {
            // Статусы: 1 - новый, 2 - в обработке, 3 - доставлен, 4 - отменен
            $statusMap = [
                'active' => [1, 2],
                'delivered' => [3],
                'cancelled' => [4]
            ];
            
            if (isset($statusMap[$statusFilter])) {
                $placeholders = implode(',', array_fill(0, count($statusMap[$statusFilter]), '?'));
                $query .= " AND o.status IN ($placeholders)";
                $params = array_merge($params, $statusMap[$statusFilter]);
            }
        }

        // Применяем фильтр по периоду
        if ($periodFilter === 'month') {
            $query .= " AND o.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        } elseif ($periodFilter === 'year') {
            $query .= " AND o.created_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
        }

        $query .= " ORDER BY o.date_init DESC";

        // Получаем заказы
        $orders = DB::select($query, $params);

        // Для каждого заказа получаем товары
        foreach ($orders as $order) {
            $order->items = DB::select("
                SELECT 
                    op.art as product_id,
                    op.title as product_name,
                    op.pieces as quantity,
                    op.piece_cost as price,
                    op.sum as total
                FROM order_positions op
                WHERE op.order_num = ?
            ", [$order->id]);
        }

        // Получаем данные корзины и избранного для header
        $cart = session('cart', ['items' => []]);
        $sessionFavorites = session('favorites', []);
        $favorites = ['items' => []];
        
        if (is_array($sessionFavorites)) {
            if (isset($sessionFavorites[0]) && !isset($sessionFavorites['items'])) {
                foreach ($sessionFavorites as $productId) {
                    $favorites['items'][$productId] = true;
                }
            } else {
                $favorites = $sessionFavorites;
            }
        }

        return view('orders.index', [
            'orders' => $orders,
            'statusFilter' => $statusFilter,
            'periodFilter' => $periodFilter,
            'cart' => $cart,
            'favorites' => $favorites
        ]);
    }
}

