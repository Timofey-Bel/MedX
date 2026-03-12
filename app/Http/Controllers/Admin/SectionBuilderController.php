<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Контроллер Page Builder - визуального конструктора страниц
 */
class SectionBuilderController extends Controller
{
    /**
     * Главная страница Page Builder (если нужна отдельная страница)
     */
    public function index(Request $request)
    {
        $action = $request->get('action', 'list');
        
        switch ($action) {
            case 'list':
                return $this->getSectionsList();
            case 'get':
                return $this->getSection($request);
            case 'save':
                return $this->saveSection($request);
            case 'create':
                return $this->createSection($request);
            case 'delete':
                return $this->deleteSection($request);
            case 'duplicate':
                return $this->duplicateSection($request);
            case 'update':
                return $this->updateSection($request);
            case 'update_sort':
                return $this->updateSort($request);
            default:
                return response()->json(['success' => false, 'error' => 'Unknown action']);
        }
    }
    
    /**
     * Получить список всех секций
     */
    private function getSectionsList()
    {
        try {
            \Log::info('SectionBuilder: Attempting to get sections list');
            
            $sections = DB::table('page_sections')
                ->select('id', 'guid', 'name', 'slug', 'category', 'thumbnail', 'sort_order', 'active', 'created_at', 'updated_at')
                ->orderBy('sort_order', 'asc')
                ->orderBy('created_at', 'desc')
                ->get();
            
            \Log::info('SectionBuilder: Successfully retrieved ' . $sections->count() . ' sections');
            
            return response()->json([
                'success' => true,
                'sections' => $sections->toArray()
            ]);
        } catch (\Exception $e) {
            \Log::error('SectionBuilder: Error getting sections list', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Ошибка получения списка секций: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Получить данные конкретной секции
     */
    private function getSection(Request $request)
    {
        $id = $request->get('id');
        
        if (!$id) {
            return response()->json(['success' => false, 'error' => 'ID секции не указан']);
        }
        
        try {
            $section = DB::table('page_sections')
                ->where('id', $id)
                ->first();
            
            if (!$section) {
                return response()->json(['success' => false, 'error' => 'Секция не найдена']);
            }
            
            return response()->json([
                'success' => true,
                'section' => [
                    'id' => $section->id,
                    'name' => $section->name,
                    'html' => $section->html ?? '',
                    'css' => $section->css ?? '',
                    'js' => $section->js ?? ''
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Ошибка получения секции: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Сохранить секцию
     */
    private function saveSection(Request $request)
    {
        $id = $request->get('id');
        $name = $request->get('name');
        $html = $request->get('html', '');
        $css = $request->get('css', '');
        $js = $request->get('js', '');
        
        if (!$id) {
            return response()->json(['success' => false, 'error' => 'ID секции не указан']);
        }
        
        try {
            $updated = DB::table('page_sections')
                ->where('id', $id)
                ->update([
                    'name' => $name,
                    'html' => $html,
                    'css' => $css,
                    'js' => $js,
                    'updated_at' => now()
                ]);
            
            if ($updated) {
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false, 'error' => 'Секция не найдена']);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Ошибка сохранения: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Создать новую секцию
     */
    private function createSection(Request $request)
    {
        $name = $request->get('name');
        
        if (!$name) {
            return response()->json(['success' => false, 'error' => 'Название секции не указано']);
        }
        
        try {
            // Генерируем GUID как в legacy (8 символов, буквы + цифры)
            $guid = $this->generateGuid();
            $slug = $this->generateSlug($name);
            
            // Получаем максимальный sort_order
            $maxSort = DB::table('page_sections')->max('sort_order') ?? 0;
            
            $id = DB::table('page_sections')->insertGetId([
                'guid' => $guid,
                'name' => $name,
                'slug' => $slug,
                'html' => '<div class="section"><div class="container"><h2>' . htmlspecialchars($name) . '</h2><p>Новая секция готова к редактированию.</p></div></div>',
                'css' => '.section { padding: 60px 0; }',
                'js' => '',
                'category' => 'general',
                'active' => 1,
                'sort_order' => $maxSort + 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'id' => $id,
                'guid' => $guid
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Ошибка создания секции: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Удалить секцию
     */
    private function deleteSection(Request $request)
    {
        $id = $request->get('id');
        
        if (!$id) {
            return response()->json(['success' => false, 'error' => 'ID секции не указан']);
        }
        
        try {
            $deleted = DB::table('page_sections')
                ->where('id', $id)
                ->delete();
            
            if ($deleted) {
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false, 'error' => 'Секция не найдена']);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Ошибка удаления: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Дублировать секцию
     */
    private function duplicateSection(Request $request)
    {
        $id = $request->get('id');
        $newName = $request->get('new_name');
        
        if (!$id || !$newName) {
            return response()->json(['success' => false, 'error' => 'Не указаны обязательные параметры']);
        }
        
        try {
            // Получаем оригинальную секцию
            $original = DB::table('page_sections')->where('id', $id)->first();
            
            if (!$original) {
                return response()->json(['success' => false, 'error' => 'Оригинальная секция не найдена']);
            }
            
            // Создаем копию
            $guid = $this->generateGuid();
            $slug = $this->generateSlug($newName);
            $maxSort = DB::table('page_sections')->max('sort_order') ?? 0;
            
            $newId = DB::table('page_sections')->insertGetId([
                'guid' => $guid,
                'name' => $newName,
                'slug' => $slug,
                'html' => $original->html,
                'css' => $original->css,
                'js' => $original->js,
                'category' => $original->category,
                'thumbnail' => $original->thumbnail ?? null,
                'active' => 1,
                'sort_order' => $maxSort + 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'id' => $newId,
                'guid' => $guid
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Ошибка дублирования: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Обновить название секции
     */
    private function updateSection(Request $request)
    {
        $id = $request->get('id');
        $name = $request->get('name');
        
        if (!$id || !$name) {
            return response()->json(['success' => false, 'error' => 'Не указаны обязательные параметры']);
        }
        
        try {
            $slug = $this->generateSlug($name);
            
            $updated = DB::table('page_sections')
                ->where('id', $id)
                ->update([
                    'name' => $name,
                    'slug' => $slug,
                    'updated_at' => now()
                ]);
            
            if ($updated) {
                return response()->json([
                    'success' => true,
                    'slug' => $slug
                ]);
            } else {
                return response()->json(['success' => false, 'error' => 'Секция не найдена']);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Ошибка обновления: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Обновить порядок сортировки секций
     */
    private function updateSort(Request $request)
    {
        // Получаем параметр orders разными способами
        $orders = $request->input('orders') ?? $request->post('orders') ?? $request->get('orders');
        
        if (!$orders) {
            return response()->json(['success' => false, 'error' => 'Данные о порядке не переданы']);
        }
        
        try {
            // Декодируем JSON если пришла строка
            if (is_string($orders)) {
                $orders = json_decode($orders, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return response()->json([
                        'success' => false, 
                        'error' => 'Ошибка декодирования JSON: ' . json_last_error_msg()
                    ]);
                }
            }
            
            if (!is_array($orders) || empty($orders)) {
                return response()->json(['success' => false, 'error' => 'Некорректный формат данных']);
            }
            
            // Обновляем порядок для каждой секции
            foreach ($orders as $order) {
                if (isset($order['id']) && isset($order['sort'])) {
                    DB::table('page_sections')
                        ->where('id', $order['id'])
                        ->update(['sort_order' => $order['sort']]);
                }
            }
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Ошибка обновления порядка: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Генерация уникального GUID (8 символов, буквы + цифры)
     * Как в legacy: использует a-z и 0-9 (всего 36 символов)
     */
    private function generateGuid()
    {
        $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $maxAttempts = 100;
        
        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            $guid = '';
            for ($i = 0; $i < 8; $i++) {
                $guid .= $characters[rand(0, strlen($characters) - 1)];
            }
            
            // Проверяем уникальность
            $exists = DB::table('page_sections')->where('guid', $guid)->exists();
            if (!$exists) {
                return $guid;
            }
        }
        
        // Если не удалось за 100 попыток, добавляем метку времени
        $guid = '';
        for ($i = 0; $i < 6; $i++) {
            $guid .= $characters[rand(0, strlen($characters) - 1)];
        }
        $guid .= substr(time(), -2);
        
        return $guid;
    }
    
    /**
     * Генерация slug из названия с проверкой уникальности
     */
    private function generateSlug($name)
    {
        $slug = Str::slug($name);
        
        // Проверяем уникальность
        $counter = 1;
        $originalSlug = $slug;
        
        while (DB::table('page_sections')->where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
}