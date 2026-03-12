<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WindowState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WindowStateController extends Controller
{
    /**
     * Сохранить состояние окна
     */
    public function save(Request $request)
    {
        $validated = $request->validate([
            'window_id' => 'required|string|max:100',
            'x' => 'required|integer',
            'y' => 'required|integer',
            'width' => 'required|integer|min:200',
            'height' => 'required|integer|min:150',
            'maximized' => 'boolean',
        ]);

        // Получаем пользователя из сессии (кастомная админ-аутентификация)
        $adminUser = session('admin_user');
        
        if (!$adminUser || !isset($adminUser['id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь не авторизован',
            ], 401);
        }

        $state = WindowState::updateOrCreate(
            [
                'user_id' => $adminUser['id'],
                'window_id' => $validated['window_id'],
            ],
            [
                'x' => $validated['x'],
                'y' => $validated['y'],
                'width' => $validated['width'],
                'height' => $validated['height'],
                'maximized' => $validated['maximized'] ?? false,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Состояние окна сохранено',
            'data' => $state,
        ]);
    }

    /**
     * Получить состояние окна
     */
    public function get(Request $request, $windowId)
    {
        // Получаем пользователя из сессии
        $adminUser = session('admin_user');
        
        if (!$adminUser || !isset($adminUser['id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь не авторизован',
            ], 401);
        }
        
        $state = WindowState::where('user_id', $adminUser['id'])
            ->where('window_id', $windowId)
            ->first();

        if (!$state) {
            return response()->json([
                'success' => false,
                'message' => 'Состояние окна не найдено',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $state,
        ]);
    }

    /**
     * Получить все состояния окон пользователя
     */
    public function getAll()
    {
        // Получаем пользователя из сессии
        $adminUser = session('admin_user');
        
        if (!$adminUser || !isset($adminUser['id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь не авторизован',
            ], 401);
        }
        
        $states = WindowState::where('user_id', $adminUser['id'])->get();

        return response()->json([
            'success' => true,
            'data' => $states,
        ]);
    }
}
