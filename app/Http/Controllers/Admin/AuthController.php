<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Контроллер авторизации администраторов
 */
class AuthController extends Controller
{
    /**
     * Показать форму входа
     */
    public function showLoginForm()
    {
        // Если уже авторизован - перенаправляем на desktop
        if (session('admin_user')) {
            return redirect()->route('admin.desktop');
        }
        
        return $this->generateLoginForm();
    }
    
    /**
     * Генерация формы логина с новыми случайными именами полей
     */
    private function generateLoginForm()
    {
        // Генерируем случайные имена и ID полей для предотвращения автозаполнения
        $loginFieldName = 'l' . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
        $passwordFieldName = 'p' . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
        
        // ID полностью нейтральные без ключевых слов
        $loginFieldId = 'f' . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
        $passwordFieldId = 'f' . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
        
        return view('admin.auth.login', [
            'loginFieldName' => $loginFieldName,
            'passwordFieldName' => $passwordFieldName,
            'loginFieldId' => $loginFieldId,
            'passwordFieldId' => $passwordFieldId
        ]);
    }
    
    /**
     * Обработка входа
     */
    public function login(Request $request)
    {
        // Находим поля логина и пароля по шаблону имён
        $loginField = null;
        $passwordField = null;
        
        foreach ($request->all() as $key => $value) {
            if (preg_match('/^l\d{8}$/', $key)) {
                $loginField = $key;
            }
            if (preg_match('/^p\d{8}$/', $key)) {
                $passwordField = $key;
            }
        }
        
        // Если не найдены поля - пробуем стандартные имена (fallback)
        if (!$loginField || !$passwordField) {
            $loginField = 'login';
            $passwordField = 'password';
        }
        
        try {
            $request->validate([
                $loginField => 'required|string',
                $passwordField => 'required|string'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->generateLoginForm()->with('error', 'Заполните все поля');
        }
        
        $login = $request->input($loginField);
        $password = $request->input($passwordField);
        
        // Ищем пользователя в БД
        $user = DB::table('admin_users')
            ->where('login', $login)
            ->where('active', 1)
            ->first();
        
        if (!$user) {
            return $this->generateLoginForm()->with('error', 'Неверный логин или пароль');
        }
        
        // Проверяем пароль
        if (!Hash::check($password, $user->password)) {
            return $this->generateLoginForm()->with('error', 'Неверный логин или пароль');
        }
        
        // Сохраняем данные пользователя в сессию
        session([
            'admin_user' => [
                'id' => $user->id,
                'login' => $user->login,
                'name' => $user->name,
                'email' => $user->email
            ]
        ]);
        
        // Обновляем время последнего входа
        DB::table('admin_users')
            ->where('id', $user->id)
            ->update(['last_login' => now()]);
        
        return redirect()->route('admin.desktop');
    }
    
    /**
     * Выход из системы
     */
    public function logout()
    {
        // Очищаем данные администратора из сессии
        session()->forget('admin_user');
        
        // Полностью очищаем сессию для безопасности
        session()->flush();
        
        // Регенерируем ID сессии
        session()->regenerate();
        
        // Перенаправляем на страницу входа
        return redirect()->route('admin.login');
    }
}
