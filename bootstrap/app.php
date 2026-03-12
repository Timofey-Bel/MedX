<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Исключаем API endpoints из проверки CSRF токена
        // Это AJAX endpoints для операций с корзиной и избранным
        // 
        // ВАЖНО: Добавляйте сюда все новые API endpoints, которые вызываются через AJAX
        // без передачи CSRF токена в заголовках
        $middleware->validateCsrfTokens(except: [
            'api/cart',
            'api/favorites/add',
            'api/favorites/remove',
            'api/favorites',
            'api/cities',
            'api/cities/search',
            'api/cities/select',
            'admin/section_builder', // Временно для отладки
        ]);
        
        // Регистрируем middleware aliases
        $middleware->alias([
            'admin.auth' => \App\Http\Middleware\AdminAuth::class,
            'retail' => \App\Http\Middleware\CheckRetailUser::class,
            'wholesale' => \App\Http\Middleware\CheckWholesaleUser::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
