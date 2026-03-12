<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema; // Добавляем use-инструкцию для Schema
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\AdminDesktopController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\AppInstallerController;
use App\Http\Controllers\Admin\SectionBuilderController;
use App\Http\Controllers\Admin\WindowStateController;
use App\Http\Controllers\Admin\DesktopShortcutController;
use App\Models\AppRoute; // Добавляем модель AppRoute

// Авторизация (без middleware)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Временный тестовый роут для отладки Section Builder (БЕЗ middleware)
Route::get('/test-section-builder', [SectionBuilderController::class, 'index'])->name('test.section_builder');

// Защищенные роуты (с middleware)
Route::middleware(['admin.auth'])->group(function () {
    // Desktop - Windows 10 интерфейс с ExtJS Desktop shell
    // По умолчанию /admin показывает desktop (как в legacy системе)
    Route::get('/', [AdminDesktopController::class, 'index'])->name('desktop');
    Route::get('permissions', [PermissionController::class, 'index'])->name('permissions');
    Route::get('profile', [UserController::class, 'profile'])->name('profile');
    Route::get('users', [UserController::class, 'index'])->name('users');
    Route::get('import', [ImportController::class, 'index'])->name('import');
    Route::get('reviews', [ReviewController::class, 'index'])->name('reviews');
    Route::get('app_installer', [AppInstallerController::class, 'index'])->name('app_installer');
    
    // Section Builder - конструктор секций
    Route::get('section_builder', [SectionBuilderController::class, 'index'])->name('section_builder');
    Route::post('section_builder', [SectionBuilderController::class, 'index'])->name('section_builder.post');

    // API для сохранения состояний окон
    Route::post('window-state/save', [WindowStateController::class, 'save'])->name('window_state.save');
    Route::get('window-state/{windowId}', [WindowStateController::class, 'get'])->name('window_state.get');
    Route::get('window-states', [WindowStateController::class, 'getAll'])->name('window_state.get_all');

    // API для пользовательских названий ярлыков
    Route::post('desktop-shortcut/save', [DesktopShortcutController::class, 'save'])->name('desktop_shortcut.save');
    Route::post('desktop-shortcut/save-position', [DesktopShortcutController::class, 'savePosition'])->name('desktop_shortcut.save_position');
    Route::get('desktop-shortcuts', [DesktopShortcutController::class, 'getAll'])->name('desktop_shortcut.get_all');

    // Динамические маршруты из базы данных (если есть таблица app_routes)
        if (Schema::hasTable('app_routes')) { // Проверяем, существует ли таблица
            AppRoute::where('route_type', 'admin')->get()->each(function ($route) {
                // Предполагаем, что module_path содержит 'Controller@method' или 'Namespace\Controller@method'
                // Пример: 'App\Http\Controllers\Admin\BannersController@index'
                Route::get($route->route_path, $route->module_path);
            });
        }
});
