<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

/**
 * Контроллер профиля пользователя
 * 
 * Управление данными пользователя и отображение истории заказов
 */
class ProfileController extends Controller
{
    /**
     * Отображение страницы профиля пользователя
     * 
     * Показывает данные пользователя и историю заказов
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        
        // Получаем историю заказов пользователя по номеру телефона (как в OrdersController)
        // В базе заказы привязаны к телефону, а не к user_id
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
            WHERE o.phone = ?
            GROUP BY o.id, o.order_code, o.date_init, o.status, o.full_sum, o.name, o.phone, o.email
            ORDER BY o.date_init DESC
        ", [$user->phone]);
        
        return view('profile.index', [
            'user' => $user,
            'orders' => $orders
        ]);
    }

    /**
     * Обновление данных пользователя
     * 
     * Валидирует и сохраняет изменения профиля
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        // Валидация входных данных
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'current_password' => ['nullable', 'string'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Введите имя',
            'name.max' => 'Имя не должно превышать 255 символов',
            'email.required' => 'Введите email',
            'email.email' => 'Введите корректный email',
            'email.unique' => 'Пользователь с таким email уже существует',
            'password.min' => 'Пароль должен содержать минимум 6 символов',
            'password.confirmed' => 'Пароли не совпадают',
        ]);

        // Если пользователь хочет изменить пароль
        if ($request->filled('password')) {
            // Проверяем текущий пароль
            if (!$request->filled('current_password')) {
                return back()->withErrors([
                    'current_password' => 'Введите текущий пароль для изменения'
                ]);
            }
            
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors([
                    'current_password' => 'Неверный текущий пароль'
                ]);
            }
            
            // Обновляем данные с новым паролем через SQL-запрос
            DB::update("
                UPDATE users 
                SET name = ?, email = ?, password = ?, password_reset_required = FALSE, updated_at = NOW()
                WHERE id = ?
            ", [
                $validated['name'],
                $validated['email'],
                Hash::make($validated['password']),
                $user->id
            ]);
        } else {
            // Обновляем только имя и email через SQL-запрос
            DB::update("
                UPDATE users 
                SET name = ?, email = ?, updated_at = NOW()
                WHERE id = ?
            ", [
                $validated['name'],
                $validated['email'],
                $user->id
            ]);
        }

        return back()->with('success', 'Данные профиля успешно обновлены!');
    }

    /**
     * Отображение избранных товаров
     * 
     * DEPRECATED: Используйте FavoriteController::index()
     * Этот метод оставлен для обратной совместимости
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function favorites()
    {
        return redirect()->route('favorites');
    }
    
    /**
     * Получить URL изображения товара
     * 
     * ЛОГИКА:
     * 1. Проверяем v_products_o_products (связь с Ozon)
     * 2. Если есть - ищем в o_images
     * 3. Если нет - используем /import_files/{id}b.jpg
     * 4. Fallback - product_empty.jpg
     * 
     * @param string $product_id - ID товара
     * @return string - URL изображения
     */
    private function getProductImageUrl($product_id)
    {
        if (empty($product_id)) {
            return '/assets/img/product_empty.jpg';
        }
        
        $ozonProduct = DB::table('v_products_o_products')
            ->where('offer_id', $product_id)
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
        
        return "/import_files/{$product_id}b.jpg";
    }
    
    /**
     * Получить рейтинг и количество отзывов для товара
     * 
     * ЛОГИКА:
     * 1. Проверяем v_products_o_products для получения SKU
     * 2. Если SKU найден - получаем средний рейтинг и количество отзывов из o_reviews
     * 3. Если данных нет - возвращаем 0
     * 
     * @param string $product_id - ID товара (offer_id)
     * @return array - ['rating' => float, 'reviews_count' => int]
     */
    private function getProductRating($product_id)
    {
        if (empty($product_id)) {
            return ['rating' => 0, 'reviews_count' => 0];
        }
        
        $ozonProduct = DB::table('v_products_o_products')
            ->where('offer_id', $product_id)
            ->first();
        
        if ($ozonProduct && !empty($ozonProduct->sku)) {
            $ratingData = DB::table('o_reviews')
                ->where('sku', intval($ozonProduct->sku))
                ->whereNotNull('rating')
                ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total_count')
                ->first();
            
            if ($ratingData) {
                return [
                    'rating' => round($ratingData->avg_rating, 1),
                    'reviews_count' => intval($ratingData->total_count)
                ];
            }
        }
        
        return ['rating' => 0, 'reviews_count' => 0];
    }
}
