<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\ProductService;

/**
 * Контроллер личного кабинета оптового покупателя
 */
class WholesaleProfileController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }
    /**
     * Главная страница кабинета оптовика
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $organization = $user->organization;

        // Получаем заказы организации
        $orders = $this->getOrganizationOrders($organization->id);

        // Активные заказы (статусы 0-3: новый, в обработке, подтвержден, собирается)
        $activeOrders = array_filter($orders, function($order) {
            return in_array($order['status'], [0, 1, 2, 3]);
        });

        // Исполненные заказы (статусы 4+: завершен, доставлен, отменен)
        $completedOrders = array_filter($orders, function($order) {
            return !in_array($order['status'], [0, 1, 2, 3]);
        });

        // Статистика
        $stats = [
            'total_orders' => count($orders),
            'total_amount' => array_sum(array_column($orders, 'total_amount')),
            'pending_orders' => count($activeOrders),
        ];

        return view('wholesale.index', [
            'user' => $user,
            'organization' => $organization,
            'orders' => array_slice(array_values($completedOrders), 0, 10),
            'activeOrders' => array_slice(array_values($activeOrders), 0, 10),
            'stats' => $stats,
        ]);
    }

    /**
     * Страница данных организации
     */
    public function organization(Request $request)
    {
        $user = auth()->user();
        $organization = $user->organization;

        return view('wholesale.organization', [
            'user' => $user,
            'organization' => $organization,
        ]);
    }

    /**
     * Страница профиля пользователя
     */
    public function profile(Request $request)
    {
        $user = auth()->user();
        $organization = $user->organization;

        return view('wholesale.profile', [
            'user' => $user,
            'organization' => $organization,
        ]);
    }

    /**
     * Страница редактирования профиля
     */
    public function editProfile(Request $request)
    {
        $user = auth()->user();
        $organization = $user->organization;

        return view('wholesale.profile-edit', [
            'user' => $user,
            'organization' => $organization,
        ]);
    }

    /**
     * Обновление данных профиля
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'phone' => 'required',
            'phone_additional' => 'nullable',
            'telegram' => 'nullable',
        ], [
            'email.required' => 'Введите email',
            'email.email' => 'Введите корректный email',
            'phone.required' => 'Введите номер телефона',
        ]);

        $user = auth()->user();

        // Проверяем уникальность email (если изменился)
        if ($request->email !== $user->email) {
            $existingUser = DB::table('users')
                ->where('email', $request->email)
                ->where('id', '!=', $user->id)
                ->first();

            if ($existingUser) {
                return back()->withErrors(['email' => 'Этот email уже используется другим пользователем']);
            }
        }

        // Обновляем данные пользователя
        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'email' => $request->email,
                'phone' => $request->phone,
                'phone_additional' => $request->phone_additional,
                'telegram' => $request->telegram,
                'updated_at' => now(),
            ]);

        return redirect()->route('lk.profile')->with('success', 'Профиль успешно обновлен');
    }

    /**
     * Обновление пароля пользователя
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ], [
            'current_password.required' => 'Введите текущий пароль',
            'new_password.required' => 'Введите новый пароль',
            'new_password.min' => 'Пароль должен содержать минимум 6 символов',
            'new_password.confirmed' => 'Пароли не совпадают',
        ]);

        $user = auth()->user();

        // Проверяем текущий пароль
        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Неверный текущий пароль']);
        }

        // Обновляем пароль
        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'password' => \Illuminate\Support\Facades\Hash::make($request->new_password),
                'password_reset_required' => false,
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Пароль успешно изменен');
    }

    /**
     * Страница заказов организации
     */
    public function orders(Request $request)
    {
        $user = auth()->user();
        $organization = $user->organization;

        $orders = $this->getOrganizationOrders($organization->id);

        return view('wholesale.orders', [
            'user' => $user,
            'organization' => $organization,
            'orders' => $orders,
        ]);
    }

    /**
     * Просмотр деталей заказа
     */
    public function showOrder(Request $request, $id)
        {
            $user = auth()->user();
            $organization = $user->organization;

            // Получаем заказ
            $order = DB::table('orders')
                ->where('id', $id)
                ->first();

            if (!$order) {
                abort(404, 'Заказ не найден');
            }

            // Проверяем что заказ принадлежит организации
            $userPhones = DB::table('users')
                ->where('org_id', $organization->id)
                ->pluck('phone')
                ->toArray();

            if (!in_array($order->phone, $userPhones)) {
                abort(403, 'Доступ запрещен');
            }

            // Получаем позиции заказа с розничными ценами
            $items = DB::table('order_positions as op')
                ->leftJoin('prices as pr_retail', function($join) {
                    $join->on('op.guid', '=', 'pr_retail.product_id')
                         ->where('pr_retail.price_type_id', '=', '000000002');
                })
                ->where('op.order_num', $id)
                ->select([
                    'op.id',
                    'op.guid as product_id',
                    'op.title as product_name',
                    'op.pieces as quantity',
                    'op.piece_cost as wholesale_price',
                    'pr_retail.price as retail_price',
                    DB::raw('op.pieces * op.piece_cost as wholesale_total'),
                    DB::raw('op.pieces * COALESCE(pr_retail.price, op.piece_cost * 1.25) as retail_total')
                ])
                ->get();

            // Добавляем изображения к каждому товару и рассчитываем скидку
            $totalWholesale = 0;
            $totalRetail = 0;

            foreach ($items as $item) {
                $item->image = $this->getProductImageUrl($item->product_id);

                // Если розничной цены нет, рассчитываем как оптовая + 25%
                if (!$item->retail_price || $item->retail_price <= $item->wholesale_price) {
                    $item->retail_price = round($item->wholesale_price * 1.25);
                    $item->retail_total = $item->retail_price * $item->quantity;
                }

                $totalWholesale += $item->wholesale_total;
                $totalRetail += $item->retail_total;
            }

            // Рассчитываем общую скидку
            $totalDiscount = $totalRetail - $totalWholesale;
            $discountPercent = $totalRetail > 0 ? round(($totalDiscount / $totalRetail) * 100, 1) : 0;

            return view('wholesale.order-detail', [
                'user' => $user,
                'organization' => $organization,
                'order' => (array) $order,
                'items' => $items,
                'totalWholesale' => $totalWholesale,
                'totalRetail' => $totalRetail,
                'totalDiscount' => $totalDiscount,
                'discountPercent' => $discountPercent,
            ]);
        }


    /**
     * Повторить заказ (редактирование состава)
     */
    public function repeatOrder(Request $request, $id)
    {
        $user = auth()->user();
        $organization = $user->organization;

        // Получаем заказ
        $order = DB::table('orders')
            ->where('id', $id)
            ->first();

        if (!$order) {
            abort(404, 'Заказ не найден');
        }

        // Проверяем что заказ принадлежит организации
        $userPhones = DB::table('users')
            ->where('org_id', $organization->id)
            ->pluck('phone')
            ->toArray();

        if (!in_array($order->phone, $userPhones)) {
            abort(403, 'Доступ запрещен');
        }

        // Получаем позиции заказа с розничными ценами
        $items = DB::table('order_positions as op')
            ->leftJoin('prices as pr_retail', function($join) {
                $join->on('op.guid', '=', 'pr_retail.product_id')
                     ->where('pr_retail.price_type_id', '=', '000000002');
            })
            ->where('op.order_num', $id)
            ->select([
                'op.id',
                'op.guid as product_id',
                'op.title as product_name',
                'op.pieces as quantity',
                'op.piece_cost as wholesale_price',
                'pr_retail.price as retail_price',
                DB::raw('op.pieces * op.piece_cost as wholesale_total'),
                DB::raw('op.pieces * COALESCE(pr_retail.price, op.piece_cost * 1.25) as retail_total')
            ])
            ->get();

        // Добавляем изображения к каждому товару и рассчитываем скидку
        $totalWholesale = 0;
        $totalRetail = 0;
        
        foreach ($items as $item) {
            $item->image = $this->getProductImageUrl($item->product_id);
            
            // Если розничной цены нет, рассчитываем как оптовая + 25%
            if (!$item->retail_price || $item->retail_price <= $item->wholesale_price) {
                $item->retail_price = round($item->wholesale_price * 1.25);
                $item->retail_total = $item->retail_price * $item->quantity;
            }
            
            $totalWholesale += $item->wholesale_total;
            $totalRetail += $item->retail_total;
        }
        
        // Рассчитываем общую скидку
        $totalDiscount = $totalRetail - $totalWholesale;
        $discountPercent = $totalRetail > 0 ? round(($totalDiscount / $totalRetail) * 100, 1) : 0;

        return view('wholesale.order-repeat', [
            'user' => $user,
            'organization' => $organization,
            'order' => (array) $order,
            'items' => $items,
            'totalWholesale' => $totalWholesale,
            'totalRetail' => $totalRetail,
            'totalDiscount' => $totalDiscount,
            'discountPercent' => $discountPercent,
        ]);
    }

    /**
     * Просмотр деталей товара
     */
    public function showProduct(Request $request, $id)
    {
        $user = auth()->user();
        $organization = $user->organization;

        // Получаем товар через ProductService
        $product = $this->productService->getProductBySlug($id);

        if (!$product) {
            abort(404, 'Товар не найден');
        }

        // Получаем данные через ProductService (как в ProductController)
        $attributes = $this->productService->getProductAttributes($id);
        $images = $this->productService->getProductImages($id);
        $reviews = $this->productService->getProductReviews($id);
        $relatedProducts = $this->productService->getRelatedProducts($id);
        $reviewsStats = $this->productService->getProductRating($id);

        // Формируем breadcrumbs
        $breadcrumbs = [
            ['title' => 'Главная', 'url' => route('lk.index')],
            ['title' => 'Заказы', 'url' => 'javascript:history.back()'],
            ['title' => $product->name, 'url' => null]
        ];

        // ОБЯЗАТЕЛЬНО получаем корзину и избранное из сессии
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

        // Используем стандартный view товара, но с layout оптового кабинета
        return view('product.show', compact(
            'product',
            'attributes',
            'images',
            'reviews',
            'relatedProducts',
            'reviewsStats',
            'breadcrumbs',
            'cart',
            'favorites'
        ))->with([
            'useWholesaleLayout' => true,
            'user' => $user,
            'organization' => $organization
        ]);
    }


    /**
     * Получить заказы организации
     */
    private function getOrganizationOrders($orgId)
    {
        // Получаем все заказы пользователей этой организации
        $userPhones = DB::table('users')
            ->where('org_id', $orgId)
            ->pluck('phone')
            ->toArray();

        if (empty($userPhones)) {
            return [];
        }

        $orders = DB::select("
            SELECT 
                o.id,
                o.order_code,
                o.date_init as created_at,
                o.status,
                o.full_sum as total_amount,
                o.name,
                o.phone,
                o.email,
                COUNT(op.id) as items_count
            FROM orders o
            LEFT JOIN order_positions op ON op.order_num = o.id
            WHERE o.phone IN ('" . implode("','", $userPhones) . "')
            GROUP BY o.id, o.order_code, o.date_init, o.status, o.full_sum, o.name, o.phone, o.email
            ORDER BY o.date_init DESC
        ");

        return array_map(function($order) {
            return (array) $order;
        }, $orders);
    }

    /**
     * Получение URL изображения товара
     * 
     * ЛОГИКА:
     * 1. Проверяем v_products_o_products (связь с Ozon)
     * 2. Если есть - ищем в o_images
     * 3. Если нет - используем /import_files/{id}b.jpg
     * 4. Fallback - product_empty.jpg
     * 
     * @param string $productId - ID товара
     * @return string - URL изображения
     */
    private function getProductImageUrl($productId)
    {
        if (empty($productId)) {
            return '/assets/img/product_empty.jpg';
        }
        
        $ozonProduct = DB::table('v_products_o_products')
            ->where('offer_id', $productId)
            ->first();
        
        if ($ozonProduct && !empty($ozonProduct->product_id)) {
            $oImage = DB::table('o_images')
                ->where('product_id', $ozonProduct->product_id)
                ->where('image_order', 0)
                ->first();
            
            if ($oImage) {
                return "/o_images/{$oImage->product_id}/0.jpg";
            }
        }
        
        return "/import_files/{$productId}b.jpg";
    }
}
