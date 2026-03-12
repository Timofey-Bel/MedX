<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware для проверки, что пользователь является оптовым покупателем
 */
class CheckWholesaleUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Необходимо авторизоваться');
        }

        if (!auth()->user()->isWholesale()) {
            return redirect()->route('my.profile')->with('error', 'Доступ только для оптовых покупателей');
        }

        return $next($request);
    }
}
