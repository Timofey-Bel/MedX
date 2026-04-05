<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CalendarController extends Controller
{
    // Отметить сегодняшний день как посещенный
    public function markToday()
    {
        $user = Auth::user();
        $today = Carbon::today()->format('Y-m-d');
        
        $visitedDays = $user->visited_days ?? [];
        
        if (!in_array($today, $visitedDays)) {
            $visitedDays[] = $today;
            $user->visited_days = $visitedDays;
        }
        
        // Устанавливаем дату первого входа если её нет
        if (!$user->first_visit_date) {
            $user->first_visit_date = $today;
        }
        
        $user->save();
        
        // Применяем заморозки к пропущенным дням
        $this->applyFreezes($user);
        
        // Вычисляем streak
        $streak = $this->calculateStreak($user);
        
        return response()->json([
            'success' => true,
            'streak' => $streak,
            'freeze_count' => $user->freeze_count
        ]);
    }
    
    // Применить заморозки к пропущенным дням
    private function applyFreezes($user)
    {
        $firstVisit = Carbon::parse($user->first_visit_date);
        $today = Carbon::today();
        $visitedDays = $user->visited_days ?? [];
        $usedFreezes = $user->used_freezes ?? [];
        $freezeCount = $user->freeze_count;
        
        $currentDate = $firstVisit->copy();
        
        while ($currentDate->lt($today)) {
            $dateKey = $currentDate->format('Y-m-d');
            
            // Если день не посещен и заморозка еще не использована
            if (!in_array($dateKey, $visitedDays) && !isset($usedFreezes[$dateKey])) {
                if ($freezeCount > 0) {
                    $usedFreezes[$dateKey] = true;
                    $freezeCount--;
                }
            }
            
            $currentDate->addDay();
        }
        
        $user->used_freezes = $usedFreezes;
        $user->freeze_count = $freezeCount;
        $user->save();
    }
    
    // Вычислить streak (дни подряд)
    private function calculateStreak($user)
    {
        $visitedDays = $user->visited_days ?? [];
        $today = Carbon::today();
        $streak = 0;
        $currentDate = $today->copy();
        
        while (in_array($currentDate->format('Y-m-d'), $visitedDays)) {
            $streak++;
            $currentDate->subDay();
            
            // Защита от бесконечного цикла
            if ($user->first_visit_date && $currentDate->lt(Carbon::parse($user->first_visit_date))) {
                break;
            }
        }
        
        return $streak;
    }
    
    // Получить данные календаря
    public function getData()
    {
        $user = Auth::user();
        
        return response()->json([
            'visited_days' => $user->visited_days ?? [],
            'first_visit_date' => $user->first_visit_date,
            'freeze_count' => $user->freeze_count,
            'used_freezes' => $user->used_freezes ?? [],
            'streak' => $this->calculateStreak($user)
        ]);
    }
}
