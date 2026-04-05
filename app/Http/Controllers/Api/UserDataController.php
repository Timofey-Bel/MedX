<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDataController extends Controller
{
    // Получить все данные пользователя
    public function getData()
    {
        $user = Auth::user();
        
        return response()->json([
            'visited_days' => $user->visited_days ?? [],
            'first_visit_date' => $user->first_visit_date,
            'freeze_count' => $user->freeze_count ?? 5,
            'used_freezes' => $user->used_freezes ?? [],
            'achievements' => $user->achievements ?? [],
            'pomodoro_state' => $user->pomodoro_state,
        ]);
    }
    
    // Сохранить данные календаря
    public function saveCalendar(Request $request)
    {
        $user = Auth::user();
        
        $user->visited_days = $request->input('visited_days', []);
        $user->first_visit_date = $request->input('first_visit_date');
        $user->freeze_count = $request->input('freeze_count', 5);
        $user->used_freezes = $request->input('used_freezes', []);
        $user->save();
        
        return response()->json(['success' => true]);
    }
    
    // Сохранить достижения
    public function saveAchievements(Request $request)
    {
        $user = Auth::user();
        
        $user->achievements = $request->input('achievements', []);
        $user->save();
        
        return response()->json(['success' => true]);
    }
    
    // Сохранить состояние помодоро
    public function savePomodoro(Request $request)
    {
        $user = Auth::user();
        
        $user->pomodoro_state = $request->input('pomodoro_state');
        $user->save();
        
        return response()->json([
            'success' => true,
            'pomodoro_state' => $user->pomodoro_state
        ]);
    }
}
