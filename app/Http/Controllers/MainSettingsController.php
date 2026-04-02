<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MainSettingsController extends Controller
{
    public function index()
    {
        return view('main_settings');
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
            'birthdate' => ['nullable', 'date'],
            'timezone' => ['nullable', 'string'],
        ]);

        // Делаем первую букву заглавной для имени и фамилии
        if (!empty($validated['first_name'])) {
            $validated['first_name'] = mb_convert_case($validated['first_name'], MB_CASE_TITLE, 'UTF-8');
        }
        
        if (!empty($validated['last_name'])) {
            $validated['last_name'] = mb_convert_case($validated['last_name'], MB_CASE_TITLE, 'UTF-8');
        }

        Auth::user()->update($validated);

        return redirect()->route('main_settings')->with('success', 'Профиль обновлен');
    }
}
