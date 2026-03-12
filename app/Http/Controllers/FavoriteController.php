<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FavoriteService;
use Illuminate\Support\Facades\Auth;

/**
 * FavoriteController - Контроллер для работы с избранным
 * 
 * Миграция из legacy системы: site/modules/sfera/favorites/
 * Обеспечивает отображение страницы избранного и AJAX API для добавления/удаления товаров
 * 
 * Требования: 18.3, 18.7
 */
class FavoriteController extends Controller
{
    protected $favoriteService;

    public function __construct(FavoriteService $favoriteService)
    {
        $this->favoriteService = $favoriteService;
    }

    /**
     * Отображение страницы избранного
     * 
     * Логика работы:
     * 1. Для авторизованных пользователей - получаем избранное из БД
     * 2. Для неавторизованных - получаем ID из сессии, загружаем полные данные через getItemsByIds()
     * 3. Передаем данные в view для отображения карточек товаров
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $items = [];
        $count = 0;

        // Для авторизованных пользователей получаем из БД
        if (Auth::check()) {
            $userId = Auth::id();
            $favoritesData = $this->favoriteService->getItems($userId);
            $items = $favoritesData['items'];
            $count = $favoritesData['count'];
        } else {
            // Для неавторизованных - получаем ID из сессии и загружаем полные данные
            $sessionFavorites = session('favorites', []);
            if (!empty($sessionFavorites)) {
                $items = $this->favoriteService->getItemsByIds($sessionFavorites);
                $count = count($items);
            }
        }

        return view('favorites.index', [
            'items' => $items,
            'count' => $count,
            'isEmpty' => $count === 0
        ]);
    }

    /**
     * Обработка AJAX запросов избранного через API
     * Используется из routes/api.php - НЕ требует CSRF токен
     * Единый endpoint для всех операций с избранным (аналогично корзине)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleAjax(Request $request)
    {
        $task = $request->input('task');

        switch ($task) {
            case 'get_favorites':
                return $this->getFavorites($request);
            
            case 'add_item':
                return $this->add($request);
            
            case 'remove_item':
                return $this->remove($request);
            
            default:
                return response()->json([
                    'error' => 'Unknown task',
                    'task' => $task
                ], 400);
        }
    }

    /**
     * Получить список избранного (task=get_favorites)
     * 
     * Логика работы:
     * 1. Для авторизованных - получаем полные данные товаров из БД через FavoriteService
     * 2. Для неавторизованных - получаем ID из сессии, затем загружаем полные данные через getItemsByIds()
     * 3. Возвращаем данные в формате {items: {id: data}, count: N} для совместимости с JavaScript
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function getFavorites(Request $request)
    {
        // Для авторизованных пользователей - получаем из БД
        if (Auth::check()) {
            $userId = Auth::id();
            $data = $this->favoriteService->getItems($userId);
            
            // Преобразуем массив items в объект с ключами по product_id для JavaScript
            $itemsObject = [];
            foreach ($data['items'] as $item) {
                $itemsObject[$item['id']] = $item;
            }
            
            return response()->json([
                'items' => $itemsObject,
                'count' => $data['count']
            ]);
        }
        
        // Для неавторизованных - получаем из сессии
        // Важно: используем $request->session() для доступа к той же сессии
        if (!$request->session()->isStarted()) {
            $request->session()->start();
        }
        
        $sessionFavorites = $request->session()->get('favorites', []);
        
        // Загружаем полные данные товаров по ID из сессии
        $items = $this->favoriteService->getItemsByIds($sessionFavorites);
        
        return response()->json([
            'items' => $items,  // Уже в формате [product_id => data]
            'count' => count($items)
        ]);
    }

    /**
     * AJAX добавление товара в избранное
     * 
     * Логика работы:
     * 1. Для авторизованных - сохраняем в БД через FavoriteService
     * 2. Для неавторизованных - сохраняем в сессии
     * 3. Валидируем параметр product_id
     * 4. Возвращаем JSON-ответ с результатом операции и обновленным счетчиком
     * 
     * Формат запроса: POST /api/favorites/add
     * Параметры: product_id (string)
     * 
     * Формат ответа:
     * {
     *   "success": true,
     *   "message": "Товар добавлен в избранное",
     *   "count": 5
     * }
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(Request $request)
    {
        // Валидируем параметры
        $productId = $request->input('product_id');
        if (empty($productId)) {
            return response()->json([
                'success' => false,
                'message' => 'Не указан ID товара',
                'count' => 0
            ], 400);
        }

        // Для авторизованных пользователей - сохраняем в БД
        if (Auth::check()) {
            $userId = Auth::id();
            $result = $this->favoriteService->addItem($productId, $userId);
            return response()->json($result);
        }

        // Для неавторизованных - сохраняем в сессии
        // Важно: используем session()->start() чтобы гарантировать создание сессии
        if (!$request->session()->isStarted()) {
            $request->session()->start();
        }
        
        $favorites = $request->session()->get('favorites', []);
        
        if (!in_array($productId, $favorites)) {
            $favorites[] = $productId;
            $request->session()->put('favorites', $favorites);
            
            // Принудительно сохраняем сессию и регенерируем токен для создания cookie
            $request->session()->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Товар добавлен в избранное',
            'count' => count($favorites)
        ]);
    }

    /**
     * AJAX удаление товара из избранного
     * 
     * Логика работы:
     * 1. Для авторизованных - удаляем из БД через FavoriteService
     * 2. Для неавторизованных - удаляем из сессии
     * 3. Валидируем параметр product_id
     * 4. Возвращаем JSON-ответ с результатом операции и обновленным счетчиком
     * 
     * Формат запроса: POST /api/favorites/remove
     * Параметры: product_id (string)
     * 
     * Формат ответа:
     * {
     *   "success": true,
     *   "message": "Товар удален из избранного",
     *   "count": 4
     * }
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function remove(Request $request)
    {
        // Валидируем параметры
        $productId = $request->input('product_id');
        if (empty($productId)) {
            return response()->json([
                'success' => false,
                'message' => 'Не указан ID товара',
                'count' => 0
            ], 400);
        }

        // Для авторизованных пользователей - удаляем из БД
        if (Auth::check()) {
            $userId = Auth::id();
            $result = $this->favoriteService->removeItem($productId, $userId);
            return response()->json($result);
        }

        // Для неавторизованных - удаляем из сессии
        // Важно: используем $request->session() для доступа к той же сессии
        if (!$request->session()->isStarted()) {
            $request->session()->start();
        }
        
        $favorites = $request->session()->get('favorites', []);
        $favorites = array_values(array_filter($favorites, function($id) use ($productId) {
            return $id !== $productId;
        }));
        $request->session()->put('favorites', $favorites);
        $request->session()->save();

        return response()->json([
            'success' => true,
            'message' => 'Товар удален из избранного',
            'count' => count($favorites)
        ]);
    }
}
