<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Контроллер для MedX платформы
 * 
 * Управляет страницами образовательной платформы для медиков
 */
class MedxController extends Controller
{
    /**
     * Главная страница MedX
     */
    public function showcase()
    {
        return view('medx.showcase.index');
    }

    /**
     * Страница входа
     */
    public function login()
    {
        return view('medx.auth.login');
    }

    /**
     * Страница регистрации
     */
    public function register()
    {
        return view('medx.auth.register');
    }
}
