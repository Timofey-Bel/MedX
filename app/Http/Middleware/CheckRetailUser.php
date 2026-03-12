<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware для проверки, что пользователь является розничным покупателем
 */
class CheckRetailUser
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

        if (!auth()->user()->isRetail()) {
            return redirect()->route('lk.index')->with('error', 'Доступ только для розничных покупателей');
        }

        return $next($request);
    }
}
