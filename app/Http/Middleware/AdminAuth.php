<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware для проверки авторизации администратора
 */
class AdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Проверяем наличие сессии администратора
        if (!session('admin_user')) {
            return redirect()->route('admin.login');
        }
        
        return $next($request);
    }
}
