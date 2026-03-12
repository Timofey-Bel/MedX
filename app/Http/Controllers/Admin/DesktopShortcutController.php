<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DesktopShortcut;
use Illuminate\Http\Request;

class DesktopShortcutController extends Controller
{
    /**
     * Сохранить пользовательское название ярлыка
     */
    public function save(Request $request)
    {
        $request->validate([
            'shortcut_id' => 'required|string|max:100',
            'custom_name' => 'required|string|max:255',
            'original_name' => 'nullable|string|max:255',
            'position_x' => 'nullable|integer',
            'position_y' => 'nullable|integer'
        ]);

        $userId = session('admin_user')['id'] ?? null;
        
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь не авторизован'
            ], 401);
        }

        try {
            $data = [
                'custom_name' => $request->custom_name
            ];
            
            // Добавляем оригинальное название если передано
            if ($request->has('original_name')) {
                $data['original_name'] = $request->original_name;
            }
            
            // Добавляем координаты если переданы
            if ($request->has('position_x')) {
                $data['position_x'] = $request->position_x;
            }
            if ($request->has('position_y')) {
                $data['position_y'] = $request->position_y;
            }
            
            DesktopShortcut::updateOrCreate(
                [
                    'user_id' => $userId,
                    'shortcut_id' => $request->shortcut_id
                ],
                $data
            );

            return response()->json([
                'success' => true,
                'message' => 'Данные ярлыка сохранены'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка сохранения: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получить все пользовательские названия ярлыков
     */
    public function getAll(Request $request)
    {
        $userId = session('admin_user')['id'] ?? null;
        
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь не авторизован'
            ], 401);
        }

        $shortcuts = DesktopShortcut::where('user_id', $userId)
            ->get()
            ->keyBy('shortcut_id')
            ->map(function ($item) {
                return $item->custom_name;
            });

        return response()->json([
            'success' => true,
            'data' => $shortcuts
        ]);
    }
    
    /**
     * Сохранить позицию ярлыка
     */
    public function savePosition(Request $request)
    {
        $request->validate([
            'shortcut_id' => 'required|string|max:100',
            'position_x' => 'required|integer',
            'position_y' => 'required|integer',
            'original_name' => 'nullable|string|max:255'
        ]);

        $userId = session('admin_user')['id'] ?? null;
        
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь не авторизован'
            ], 401);
        }

        try {
            $data = [
                'position_x' => $request->position_x,
                'position_y' => $request->position_y
            ];
            
            // Добавляем оригинальное название если передано
            if ($request->has('original_name')) {
                $data['original_name'] = $request->original_name;
            }
            
            DesktopShortcut::updateOrCreate(
                [
                    'user_id' => $userId,
                    'shortcut_id' => $request->shortcut_id
                ],
                $data
            );

            return response()->json([
                'success' => true,
                'message' => 'Позиция ярлыка сохранена'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка сохранения: ' . $e->getMessage()
            ], 500);
        }
    }
}
