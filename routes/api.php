<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CityController;
use App\Services\FavoriteService;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API endpoint для корзины - обрабатывает все AJAX операции
// Использует session middleware для сохранения данных корзины
// Исключен из проверки CSRF токена (настроено в bootstrap/app.php)
Route::post('/cart', [CartController::class, 'handleAjax'])
    ->middleware(['web'])
    ->name('api.cart');

// API endpoint для избранного - обрабатывает все AJAX операции
// Использует session middleware для сохранения данных избранного
// Исключен из проверки CSRF токена (настроено в bootstrap/app.php)
// Аналогично корзине - единый endpoint с параметром task
Route::post('/favorites', [FavoriteController::class, 'handleAjax'])
    ->middleware(['web'])
    ->name('api.favorites');

// Дополнительные endpoints для избранного (для обратной совместимости)
Route::post('/favorites/add', [FavoriteController::class, 'add'])
    ->middleware(['web'])
    ->name('api.favorites.add');

Route::post('/favorites/remove', [FavoriteController::class, 'remove'])
    ->middleware(['web'])
    ->name('api.favorites.remove');

// API endpoints для городов
Route::get('/cities', [CityController::class, 'index'])
    ->middleware(['web'])
    ->name('api.cities');

Route::get('/cities/search', [CityController::class, 'search'])
    ->middleware(['web'])
    ->name('api.cities.search');

Route::post('/cities/select', [CityController::class, 'select'])
    ->middleware(['web'])
    ->name('api.cities.select');