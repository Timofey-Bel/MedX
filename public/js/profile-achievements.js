// Отображение достижений в профиле
(function() {
    'use strict';
    
    function renderAchievements() {
        const container = document.getElementById('achievementsGrid');
        if (!container || !window.MedXAchievements) return;
        
        const allAchievements = window.MedXAchievements.getAll();
        const unlockedSet = window.MedXAchievements.getUnlocked();
        
        let html = '';
        
        // Сортируем: сначала разблокированные, потом заблокированные
        const achievementsList = Object.values(allAchievements);
        const unlocked = achievementsList.filter(a => unlockedSet.has(a.id));
        const locked = achievementsList.filter(a => !unlockedSet.has(a.id));
        
        // Рендерим разблокированные
        unlocked.forEach(achievement => {
            html += `
                <div class="achievement-card unlocked">
                    <div class="achievement-card-icon">${achievement.icon}</div>
                    <div class="achievement-card-content">
                        <div class="achievement-card-name">${achievement.name}</div>
                        <div class="achievement-card-description">${achievement.description}</div>
                    </div>
                    <div class="achievement-card-badge">✓</div>
                </div>
            `;
        });
        
        // Рендерим заблокированные
        locked.forEach(achievement => {
            html += `
                <div class="achievement-card locked">
                    <div class="achievement-card-icon">${achievement.icon}</div>
                    <div class="achievement-card-content">
                        <div class="achievement-card-name">???</div>
                        <div class="achievement-card-description">${achievement.description}</div>
                    </div>
                    <div class="achievement-card-lock">🔒</div>
                </div>
            `;
        });
        
        container.innerHTML = html;
    }
    
    // Переключение табов
    function initTabs() {
        const navItems = document.querySelectorAll('.nav-item');
        const tabContents = document.querySelectorAll('.tab-content');
        
        navItems.forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                const tabId = item.getAttribute('data-tab');
                
                // Убираем active у всех
                navItems.forEach(nav => nav.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));
                
                // Добавляем active к выбранному
                item.classList.add('active');
                const targetTab = document.getElementById(tabId);
                if (targetTab) {
                    targetTab.classList.add('active');
                }
                
                // Если открыли таб достижений - рендерим их
                if (tabId === 'achievements') {
                    renderAchievements();
                }
            });
        });
    }
    
    // Инициализация
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            initTabs();
            // Если сразу открыт таб достижений
            if (window.location.hash === '#achievements') {
                const achievementsTab = document.querySelector('[data-tab="achievements"]');
                if (achievementsTab) {
                    achievementsTab.click();
                }
            }
        });
    } else {
        initTabs();
    }
    
})();
