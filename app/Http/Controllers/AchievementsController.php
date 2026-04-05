<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AchievementsController extends Controller
{
    // Список всех достижений
    private $achievements = [
        'first_login' => ['name' => 'Первые шаги', 'description' => 'Добро пожаловать в MedX! Начните свой путь к знаниям', 'icon' => '🎯'],
        'first_test' => ['name' => 'Начинающий практик', 'description' => 'Пройдите свой первый тест', 'icon' => '📝'],
        'first_article' => ['name' => 'Любознательный читатель', 'description' => 'Прочитайте первую статью', 'icon' => '📚'],
        'pomodoro_cycle' => ['name' => 'Мастер концентрации', 'description' => 'Завершите полный цикл помодоро (4 раунда)', 'icon' => '🍅'],
        'streak_3' => ['name' => 'Постоянство', 'description' => 'Заходите 3 дня подряд', 'icon' => '🔥'],
        'streak_7' => ['name' => 'Неделя силы', 'description' => 'Заходите 7 дней подряд', 'icon' => '💪'],
        'streak_14' => ['name' => 'Две недели упорства', 'description' => 'Заходите 14 дней подряд', 'icon' => '⚡'],
        'streak_30' => ['name' => 'Месяц дисциплины', 'description' => 'Заходите 30 дней подряд', 'icon' => '🏆'],
        'streak_50' => ['name' => 'Золотой юбилей', 'description' => 'Заходите 50 дней подряд', 'icon' => '👑'],
        'streak_75' => ['name' => 'Платиновая серия', 'description' => 'Заходите 75 дней подряд', 'icon' => '💎'],
        'streak_125' => ['name' => 'Легенда постоянства', 'description' => 'Заходите 125 дней подряд', 'icon' => '🌟'],
        'streak_180' => ['name' => 'Полгода совершенства', 'description' => 'Заходите 180 дней подряд', 'icon' => '🎖️'],
    ];
    
    // Получить все достижения с статусом
    public function getAll()
    {
        $user = Auth::user();
        $unlocked = $user->achievements ?? [];
        
        $result = [];
        foreach ($this->achievements as $id => $achievement) {
            $result[] = [
                'id' => $id,
                'name' => $achievement['name'],
                'description' => $achievement['description'],
                'icon' => $achievement['icon'],
                'unlocked' => in_array($id, $unlocked)
            ];
        }
        
        return response()->json($result);
    }
    
    // Разблокировать достижение
    public function unlock(Request $request)
    {
        $achievementId = $request->input('achievement_id');
        $user = Auth::user();
        
        $unlocked = $user->achievements ?? [];
        
        // Если уже разблокировано
        if (in_array($achievementId, $unlocked)) {
            return response()->json(['success' => false, 'message' => 'Already unlocked']);
        }
        
        // Проверяем что достижение существует
        if (!isset($this->achievements[$achievementId])) {
            return response()->json(['success' => false, 'message' => 'Achievement not found'], 404);
        }
        
        $unlocked[] = $achievementId;
        $user->achievements = $unlocked;
        $user->save();
        
        return response()->json([
            'success' => true,
            'achievement' => array_merge(['id' => $achievementId], $this->achievements[$achievementId])
        ]);
    }
    
    // Проверить и выдать достижения за streak
    public function checkStreak(Request $request)
    {
        $streak = $request->input('streak');
        $user = Auth::user();
        $unlocked = $user->achievements ?? [];
        $newAchievements = [];
        
        $streakAchievements = [
            3 => 'streak_3',
            7 => 'streak_7',
            14 => 'streak_14',
            30 => 'streak_30',
            50 => 'streak_50',
            75 => 'streak_75',
            125 => 'streak_125',
            180 => 'streak_180',
        ];
        
        foreach ($streakAchievements as $requiredStreak => $achievementId) {
            if ($streak >= $requiredStreak && !in_array($achievementId, $unlocked)) {
                $unlocked[] = $achievementId;
                $newAchievements[] = array_merge(['id' => $achievementId], $this->achievements[$achievementId]);
            }
        }
        
        if (count($newAchievements) > 0) {
            $user->achievements = $unlocked;
            $user->save();
        }
        
        return response()->json([
            'success' => true,
            'new_achievements' => $newAchievements
        ]);
    }
}
