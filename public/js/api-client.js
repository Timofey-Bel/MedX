// API клиент для работы с БД
(function() {
    'use strict';
    
    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    }
    
    async function apiRequest(url, method = 'GET', data = null) {
        const options = {
            method,
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            }
        };
        
        if (data && method !== 'GET') {
            options.headers['Content-Type'] = 'application/json';
            options.body = JSON.stringify(data);
        }
        
        try {
            const response = await fetch(url, options);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return await response.json();
        } catch (error) {
            console.error('API request failed:', error);
            throw error;
        }
    }
    
    window.API = {
        // Календарь
        calendar: {
            getData: () => apiRequest('/api/calendar/'),
            markToday: () => apiRequest('/api/calendar/mark-today', 'POST')
        },
        
        // Достижения
        achievements: {
            getAll: () => apiRequest('/api/achievements/'),
            unlock: (achievementId) => apiRequest('/api/achievements/unlock', 'POST', { achievement_id: achievementId }),
            checkStreak: (streak) => apiRequest('/api/achievements/check-streak', 'POST', { streak })
        }
    };
    
})();
