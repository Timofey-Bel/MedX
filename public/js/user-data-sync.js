// Синхронизация данных пользователя с БД
(function() {
    'use strict';
    
    const SYNC_DEBOUNCE_MS = 1000; // Задержка перед сохранением
    let syncTimers = {};
    
    // Получить CSRF токен
    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    }
    
    // Загрузить данные из БД
    async function loadFromDB() {
        try {
            const response = await fetch('/api/user-data/', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                }
            });
            
            if (!response.ok) throw new Error('Failed to load data');
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error loading data from DB:', error);
            return null;
        }
    }
    
    // Сохранить данные календаря
    async function saveCalendarToDB(data) {
        try {
            const response = await fetch('/api/user-data/calendar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify(data)
            });
            
            if (!response.ok) throw new Error('Failed to save calendar');
            
            return await response.json();
        } catch (error) {
            console.error('Error saving calendar to DB:', error);
            return null;
        }
    }
    
    // Сохранить достижения
    async function saveAchievementsToDB(data) {
        try {
            const response = await fetch('/api/user-data/achievements', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify(data)
            });
            
            if (!response.ok) throw new Error('Failed to save achievements');
            
            return await response.json();
        } catch (error) {
            console.error('Error saving achievements to DB:', error);
            return null;
        }
    }
    
    // Сохранить состояние помодоро
    async function savePomodoroToDB(data) {
        try {
            const response = await fetch('/api/user-data/pomodoro', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify(data)
            });
            
            if (!response.ok) throw new Error('Failed to save pomodoro');
            
            return await response.json();
        } catch (error) {
            console.error('Error saving pomodoro to DB:', error);
            return null;
        }
    }
    
    // Синхронизация с debounce
    function debouncedSync(key, syncFunction, data) {
        if (syncTimers[key]) {
            clearTimeout(syncTimers[key]);
        }
        
        syncTimers[key] = setTimeout(() => {
            syncFunction(data);
        }, SYNC_DEBOUNCE_MS);
    }
    
    // Инициализация: миграция данных из localStorage в БД и загрузка обратно
    async function initSync() {
        // Сначала проверяем есть ли данные в localStorage
        const hasLocalData = localStorage.getItem('medx_visited_days') || 
                            localStorage.getItem('medx_achievements') ||
                            localStorage.getItem('medx_first_visit_date');
        
        // Если есть данные в localStorage - мигрируем их в БД
        if (hasLocalData) {
            // Мигрируем календарь
            const visitedDays = localStorage.getItem('medx_visited_days');
            const firstVisitDate = localStorage.getItem('medx_first_visit_date');
            const freezeCount = localStorage.getItem('medx_freeze_count');
            const usedFreezes = localStorage.getItem('medx_used_freezes');
            
            if (visitedDays || firstVisitDate) {
                let parsedUsedFreezes = {};
                if (usedFreezes) {
                    try {
                        parsedUsedFreezes = JSON.parse(usedFreezes);
                        // Если это массив, конвертируем в объект
                        if (Array.isArray(parsedUsedFreezes)) {
                            parsedUsedFreezes = {};
                        }
                    } catch (e) {
                        parsedUsedFreezes = {};
                    }
                }
                
                await saveCalendarToDB({
                    visited_days: visitedDays ? JSON.parse(visitedDays) : [],
                    first_visit_date: firstVisitDate,
                    freeze_count: freezeCount ? parseInt(freezeCount) : 5,
                    used_freezes: parsedUsedFreezes
                });
            }
            
            // Мигрируем достижения
            const achievements = localStorage.getItem('medx_achievements');
            if (achievements) {
                await saveAchievementsToDB({
                    achievements: JSON.parse(achievements)
                });
            }
            
            // Мигрируем помодоро
            const pomodoroState = localStorage.getItem('medx_pomodoro_state');
            if (pomodoroState) {
                await savePomodoroToDB({
                    pomodoro_state: JSON.parse(pomodoroState)
                });
            }
        }
        
        // Теперь загружаем данные из БД
        const dbData = await loadFromDB();
        
        if (!dbData) {
            return;
        }
        
        // Синхронизируем календарь
        if (dbData.visited_days) {
            localStorage.setItem('medx_visited_days', JSON.stringify(dbData.visited_days));
        }
        if (dbData.first_visit_date) {
            localStorage.setItem('medx_first_visit_date', dbData.first_visit_date);
        }
        if (dbData.freeze_count !== null && dbData.freeze_count !== undefined) {
            localStorage.setItem('medx_freeze_count', dbData.freeze_count);
        }
        if (dbData.used_freezes) {
            localStorage.setItem('medx_used_freezes', JSON.stringify(dbData.used_freezes));
        }
        
        // Синхронизируем достижения
        if (dbData.achievements) {
            localStorage.setItem('medx_achievements', JSON.stringify(dbData.achievements));
        }
        
        // Синхронизируем помодоро
        if (dbData.pomodoro_state) {
            localStorage.setItem('medx_pomodoro_state', JSON.stringify(dbData.pomodoro_state));
        }
    }
    
    // Глобальные функции для использования из других скриптов
    window.UserDataSync = {
        init: initSync,
        
        // Сохранить календарь
        saveCalendar: function() {
            const data = {
                visited_days: JSON.parse(localStorage.getItem('medx_visited_days') || '[]'),
                first_visit_date: localStorage.getItem('medx_first_visit_date'),
                freeze_count: parseInt(localStorage.getItem('medx_freeze_count') || '5'),
                used_freezes: JSON.parse(localStorage.getItem('medx_used_freezes') || '{}')
            };
            
            debouncedSync('calendar', saveCalendarToDB, data);
        },
        
        // Сохранить достижения
        saveAchievements: function() {
            const data = {
                achievements: JSON.parse(localStorage.getItem('medx_achievements') || '[]')
            };
            
            debouncedSync('achievements', saveAchievementsToDB, data);
        },
        
        // Сохранить помодоро
        savePomodoro: function() {
            const data = {
                pomodoro_state: JSON.parse(localStorage.getItem('medx_pomodoro_state') || 'null')
            };
            
            debouncedSync('pomodoro', savePomodoroToDB, data);
        }
    };
    
    // Автоматическая инициализация при загрузке страницы
    // ВРЕМЕННО ОТКЛЮЧЕНО для восстановления данных
    /*
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSync);
    } else {
        initSync();
    }
    */
    
    // console.log('User data sync temporarily disabled for data recovery');
    
})();
