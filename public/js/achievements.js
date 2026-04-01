// Система достижений
(function() {
    'use strict';
    
    // Определение всех достижений
    const ACHIEVEMENTS = {
        first_login: {
            id: 'first_login',
            name: 'Первые шаги',
            description: 'Добро пожаловать в MedX! Начните свой путь к знаниям',
            icon: '🎯',
            condition: () => true // Выдается при первом входе
        },
        first_test: {
            id: 'first_test',
            name: 'Начинающий практик',
            description: 'Пройдите свой первый тест',
            icon: '📝',
            condition: () => false // Проверяется извне
        },
        first_article: {
            id: 'first_article',
            name: 'Любознательный читатель',
            description: 'Прочитайте первую статью',
            icon: '📚',
            condition: () => false // Проверяется извне
        },
        pomodoro_cycle: {
            id: 'pomodoro_cycle',
            name: 'Мастер концентрации',
            description: 'Завершите полный цикл помодоро (4 раунда)',
            icon: '🍅',
            condition: () => false // Проверяется из помодоро таймера
        },
        streak_3: {
            id: 'streak_3',
            name: 'Постоянство',
            description: 'Заходите 3 дня подряд',
            icon: '🔥',
            condition: (streak) => streak >= 3
        },
        streak_7: {
            id: 'streak_7',
            name: 'Неделя силы',
            description: 'Заходите 7 дней подряд',
            icon: '💪',
            condition: (streak) => streak >= 7
        },
        streak_14: {
            id: 'streak_14',
            name: 'Две недели упорства',
            description: 'Заходите 14 дней подряд',
            icon: '⚡',
            condition: (streak) => streak >= 14
        },
        streak_30: {
            id: 'streak_30',
            name: 'Месяц дисциплины',
            description: 'Заходите 30 дней подряд',
            icon: '🏆',
            condition: (streak) => streak >= 30
        },
        streak_50: {
            id: 'streak_50',
            name: 'Золотой юбилей',
            description: 'Заходите 50 дней подряд',
            icon: '👑',
            condition: (streak) => streak >= 50
        },
        streak_75: {
            id: 'streak_75',
            name: 'Платиновая серия',
            description: 'Заходите 75 дней подряд',
            icon: '💎',
            condition: (streak) => streak >= 75
        },
        streak_125: {
            id: 'streak_125',
            name: 'Легенда постоянства',
            description: 'Заходите 125 дней подряд',
            icon: '🌟',
            condition: (streak) => streak >= 125
        },
        streak_180: {
            id: 'streak_180',
            name: 'Полгода совершенства',
            description: 'Заходите 180 дней подряд',
            icon: '🎖️',
            condition: (streak) => streak >= 180
        }
    };
    
    // Загрузка полученных достижений
    function loadUnlockedAchievements() {
        const saved = localStorage.getItem('medx_achievements');
        if (saved) {
            try {
                return new Set(JSON.parse(saved));
            } catch (e) {
                return new Set();
            }
        }
        return new Set();
    }
    
    // Сохранение достижений
    function saveUnlockedAchievements(achievements) {
        localStorage.setItem('medx_achievements', JSON.stringify([...achievements]));
    }
    
    // Проверка и разблокировка достижения
    function unlockAchievement(achievementId) {
        const unlocked = loadUnlockedAchievements();
        
        // Если уже разблокировано - не показываем
        if (unlocked.has(achievementId)) {
            return false;
        }
        
        const achievement = ACHIEVEMENTS[achievementId];
        if (!achievement) {
            console.error('Achievement not found:', achievementId);
            return false;
        }
        
        // Разблокируем достижение
        unlocked.add(achievementId);
        saveUnlockedAchievements(unlocked);
        
        // Показываем уведомление
        showAchievementNotification(achievement);
        
        return true;
    }
    
    // Показ уведомления о достижении
    function showAchievementNotification(achievement) {
        // Создаем элемент уведомления
        const notification = document.createElement('div');
        notification.className = 'achievement-notification';
        notification.innerHTML = `
            <div class="achievement-icon">${achievement.icon}</div>
            <div class="achievement-content">
                <div class="achievement-badge">Достижение разблокировано!</div>
                <div class="achievement-name">${achievement.name}</div>
                <div class="achievement-description">${achievement.description}</div>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Анимация появления
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);
        
        // Автоматическое скрытие через 5 секунд
        setTimeout(() => {
            notification.classList.add('hiding');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 5000);
        
        // Закрытие по клику
        notification.addEventListener('click', () => {
            notification.classList.add('hiding');
            setTimeout(() => {
                notification.remove();
            }, 300);
        });
    }
    
    // Проверка достижений по streak
    function checkStreakAchievements(streak) {
        const streakAchievements = [
            'streak_3', 'streak_7', 'streak_14', 'streak_30',
            'streak_50', 'streak_75', 'streak_125', 'streak_180'
        ];
        
        streakAchievements.forEach(id => {
            const achievement = ACHIEVEMENTS[id];
            if (achievement.condition(streak)) {
                unlockAchievement(id);
            }
        });
    }
    
    // Проверка достижения "Первый вход"
    function checkFirstLogin() {
        const unlocked = loadUnlockedAchievements();
        if (!unlocked.has('first_login')) {
            unlockAchievement('first_login');
        }
    }
    
    // Глобальные функции для использования из других скриптов
    window.MedXAchievements = {
        unlock: unlockAchievement,
        checkStreak: checkStreakAchievements,
        checkFirstLogin: checkFirstLogin,
        getAll: () => ACHIEVEMENTS,
        getUnlocked: loadUnlockedAchievements
    };
    
    // Проверяем первый вход при загрузке
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', checkFirstLogin);
    } else {
        checkFirstLogin();
    }
    
})();
