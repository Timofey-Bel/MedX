// Календарь ежедневных входов
(function() {
    'use strict';
    
    // ------------------- Переменные -------------------
    let swiper = null;
    let weeksData = [];
    let currentWeekIndex = 5;
    let visitedDays = new Set();
    let firstVisitDate = null; // Дата первого входа пользователя
    let freezeCount = 5; // Количество заморозок (по умолчанию 5)
    let usedFreezes = {}; // Объект с использованными заморозками {date: true}
    
    const weekdaysShort = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];
    
    // ------------------- Функции работы с датами -------------------
    
    // Получить понедельник заданной недели
    function getMonday(date) {
        const d = new Date(date);
        const day = d.getDay();
        const diff = (day === 0 ? -6 : 1 - day);
        d.setDate(d.getDate() + diff);
        return new Date(d.setHours(0, 0, 0, 0));
    }
    
    // Создать массив из 7 дней, начиная с понедельника
    function getWeekDays(monday) {
        const week = [];
        for (let i = 0; i < 7; i++) {
            const day = new Date(monday);
            day.setDate(monday.getDate() + i);
            week.push(day);
        }
        return week;
    }
    
    // Форматирование даты: день.месяц (24.03)
    function formatDate(date) {
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        return `${day}.${month}`;
    }
    
    // Ключ для localStorage
    function formatDateKey(date) {
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        return `${year}-${month}-${day}`;
    }
    
    // ------------------- Работа с localStorage -------------------
    
    function loadVisitedDays() {
        const saved = localStorage.getItem('medx_visited_days');
        if (saved) {
            try {
                const parsed = JSON.parse(saved);
                visitedDays = new Set(parsed);
            } catch (e) {
                visitedDays = new Set();
            }
        }
        
        // Загружаем дату первого входа
        const savedFirstVisit = localStorage.getItem('medx_first_visit_date');
        if (savedFirstVisit) {
            firstVisitDate = new Date(savedFirstVisit);
            firstVisitDate.setHours(0, 0, 0, 0);
        } else {
            // Если это первый вход - сохраняем текущую дату
            firstVisitDate = new Date();
            firstVisitDate.setHours(0, 0, 0, 0);
            localStorage.setItem('medx_first_visit_date', firstVisitDate.toISOString());
        }
        
        // Загружаем количество заморозок
        const savedFreezes = localStorage.getItem('medx_freeze_count');
        if (savedFreezes !== null) {
            freezeCount = parseInt(savedFreezes, 10);
        } else {
            freezeCount = 5; // По умолчанию 5 заморозок
            localStorage.setItem('medx_freeze_count', freezeCount);
        }
        
        // Загружаем использованные заморозки
        const savedUsedFreezes = localStorage.getItem('medx_used_freezes');
        if (savedUsedFreezes) {
            try {
                usedFreezes = JSON.parse(savedUsedFreezes);
            } catch (e) {
                usedFreezes = {};
            }
        }
        
        // Автоматически отмечаем сегодняшний день
        const today = formatDateKey(new Date());
        if (!visitedDays.has(today)) {
            visitedDays.add(today);
            saveVisitedDays();
        }
        
        // Проверяем и применяем заморозки для пропущенных дней
        applyFreezesToMissedDays();
    }
    
    function saveVisitedDays() {
        localStorage.setItem('medx_visited_days', JSON.stringify([...visitedDays]));
    }
    
    function saveFreezeCount() {
        localStorage.setItem('medx_freeze_count', freezeCount);
    }
    
    function saveUsedFreezes() {
        localStorage.setItem('medx_used_freezes', JSON.stringify(usedFreezes));
    }
    
    // Применяем заморозки к пропущенным дням
    function applyFreezesToMissedDays() {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        let currentDate = new Date(firstVisitDate);
        currentDate.setHours(0, 0, 0, 0);
        
        while (currentDate < today) {
            const dateKey = formatDateKey(currentDate);
            
            // Если день не посещен и заморозка еще не использована
            if (!visitedDays.has(dateKey) && !usedFreezes[dateKey]) {
                // Если есть доступные заморозки
                if (freezeCount > 0) {
                    usedFreezes[dateKey] = true;
                    freezeCount--;
                    saveFreezeCount();
                    saveUsedFreezes();
                }
            }
            
            currentDate.setDate(currentDate.getDate() + 1);
        }
    }
    
    // Проверка, был ли день посещен
    function isDayVisited(date) {
        return visitedDays.has(formatDateKey(date));
    }
    
    // Проверка, использована ли заморозка для этого дня
    function isDayFrozen(date) {
        return usedFreezes[formatDateKey(date)] === true;
    }
    
    // Проверка, пропущен ли день (прошедший день без посещения ПОСЛЕ первого входа и без заморозки)
    function isDayMissed(date) {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const dateNormalized = new Date(date);
        dateNormalized.setHours(0, 0, 0, 0);
        
        // День пропущен только если:
        // 1. Он в прошлом (раньше сегодня)
        // 2. Он не был посещен
        // 3. Он после или равен дате первого входа
        // 4. На него не была использована заморозка
        return dateNormalized < today && 
               !isDayVisited(date) && 
               dateNormalized >= firstVisitDate &&
               !isDayFrozen(date);
    }
    
    // ------------------- Генерация HTML -------------------
    
    // Генерация HTML для одной недели
    function renderWeek(mondayDate) {
        const weekDays = getWeekDays(mondayDate);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        let html = '<div class="week-days">';
        
        weekDays.forEach((day, idx) => {
            const isToday = day.getTime() === today.getTime();
            const isVisited = isDayVisited(day);
            const isFrozen = isDayFrozen(day);
            const isMissed = isDayMissed(day);
            
            const todayClass = isToday ? 'today' : '';
            const dayNumber = formatDate(day);
            
            // Определяем класс для иконки
            let dotClass = '';
            if (isVisited) {
                dotClass = 'checked';
            } else if (isFrozen) {
                dotClass = 'frozen';
            } else if (isMissed) {
                dotClass = 'missed';
            }
            
            html += `
                <div class="day ${todayClass}" data-date="${formatDateKey(day)}">
                    <div class="day-dot ${dotClass}"></div>
                    <span>${dayNumber}</span>
                </div>
            `;
        });
        
        html += '</div>';
        return html;
    }
    
    // ------------------- Генерация недель для Swiper -------------------
    
    function generateWeeksAround(date) {
        const currentMonday = getMonday(date);
        const weeks = [];
        
        // Создаем 11 недель: 5 до текущей, текущая, 5 после
        const startOffset = -5;
        for (let i = startOffset; i <= 5; i++) {
            const monday = new Date(currentMonday);
            monday.setDate(currentMonday.getDate() + i * 7);
            weeks.push(monday);
        }
        
        return weeks;
    }
    
    // ------------------- Инициализация -------------------
    
    function initCalendar() {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        // Загружаем посещенные дни
        loadVisitedDays();
        
        // Генерируем недели
        weeksData = generateWeeksAround(today);
        
        const weeksContainer = document.querySelector('.days-container');
        if (!weeksContainer) {
            console.error('Контейнер .days-container не найден');
            return;
        }
        
        weeksContainer.innerHTML = '';
        
        // Заполняем слайды
        weeksData.forEach((monday) => {
            const slide = document.createElement('div');
            slide.className = 'swiper-slide';
            slide.innerHTML = renderWeek(monday);
            weeksContainer.appendChild(slide);
        });
        
        // Инициализируем Swiper
        if (swiper) {
            swiper.destroy(true, true);
        }
        
        swiper = new Swiper('.daily-swiper', {
            slidesPerView: 1,
            spaceBetween: 0,
            speed: 300,
            loop: false,
            navigation: {
                nextEl: '.swiper-button-next-daily',
                prevEl: '.swiper-button-prev-daily',
            },
            initialSlide: currentWeekIndex,
            on: {
                slideChange: function() {
                    currentWeekIndex = this.activeIndex;
                }
            }
        });
    }
    
    // ------------------- Инициализация при загрузке -------------------
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCalendar);
    } else {
        initCalendar();
    }
    
    // ------------------- Модальное окно полного календаря -------------------
    
    let currentMonthOffset = 0; // Смещение от текущего месяца
    
    function calculateStreak() {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        let streak = 0;
        let currentDate = new Date(today);
        
        // Считаем дни подряд начиная с сегодня и идя назад
        while (isDayVisited(currentDate)) {
            streak++;
            currentDate.setDate(currentDate.getDate() - 1);
            
            // Защита от бесконечного цикла
            if (currentDate < firstVisitDate) break;
        }
        
        // Проверяем достижения по streak
        if (window.MedXAchievements) {
            window.MedXAchievements.checkStreak(streak);
        }
        
        return streak;
    }
    
    function openFullCalendar() {
        const modal = document.getElementById('calendarModal');
        const container = document.getElementById('fullCalendarContainer');
        
        if (!modal || !container) return;
        
        currentMonthOffset = 0;
        container.innerHTML = generateFullCalendar();
        
        // Обновляем заголовок со streak и заморозками
        const headerTitle = modal.querySelector('.calendar-modal-header-2 h2');
        if (headerTitle) {
            const streak = calculateStreak();
            headerTitle.innerHTML = `
                
                <span style="display: flex; gap: 12px; align-items: center;">
                    <span style="color: var(--primary-blue); font-size: 20px;">
                        🔥 ${streak} ${getDaysWord(streak)} подряд
                    </span>
                    <span style="color: var(--light-blue); font-size: 18px;">
                        ❄️ ${freezeCount}
                    </span>
                </span>
            `;
        }
        
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    function getDaysWord(count) {
        const lastDigit = count % 10;
        const lastTwoDigits = count % 100;
        
        if (lastTwoDigits >= 11 && lastTwoDigits <= 19) {
            return 'дней';
        }
        
        if (lastDigit === 1) {
            return 'день';
        }
        
        if (lastDigit >= 2 && lastDigit <= 4) {
            return 'дня';
        }
        
        return 'дней';
    }
    
    function closeFullCalendar() {
        const modal = document.getElementById('calendarModal');
        if (!modal) return;
        
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    function navigateMonth(direction) {
        currentMonthOffset += direction;
        const container = document.getElementById('fullCalendarContainer');
        if (container) {
            container.innerHTML = generateFullCalendar();
        }
    }
    
    function generateFullCalendar() {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        // Вычисляем месяц для отображения с учетом смещения
        const displayMonth = new Date(today.getFullYear(), today.getMonth() + currentMonthOffset, 1);
        
        let html = '';
        
        const monthNames = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 
                           'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];
        
        const year = displayMonth.getFullYear();
        const month = displayMonth.getMonth();
        const monthName = `${monthNames[month]} ${year}`;
        
        // Кнопки навигации
        html += `
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <button onclick="window.navigateCalendarMonth(-1)" style="padding: 8px 16px; background: var(--primary-blue); color: white; border: none; border-radius: 8px; cursor: pointer; font-family: var(--font-onest);">
                    ← Назад
                </button>
                <span style="font-family: var(--font-onest); font-weight: 600; font-size: 18px; color: var(--primary-blue);">${monthName}</span>
                <button onclick="window.navigateCalendarMonth(1)" style="padding: 8px 16px; background: var(--primary-blue); color: white; border: none; border-radius: 8px; cursor: pointer; font-family: var(--font-onest);">
                    Вперед →
                </button>
            </div>
        `;
        
        html += `<div class="month-block">`;
        html += `<div class="month-weekdays">`;
        html += `<div>Пн</div><div>Вт</div><div>Ср</div><div>Чт</div><div>Пт</div><div>Сб</div><div>Вс</div>`;
        html += `</div>`;
        html += `<div class="month-grid">`;
        
        // Получаем первый день месяца и количество дней
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const daysInMonth = lastDay.getDate();
        
        // Добавляем пустые ячейки до первого дня (понедельник = 0)
        let firstDayOfWeek = firstDay.getDay();
        firstDayOfWeek = firstDayOfWeek === 0 ? 6 : firstDayOfWeek - 1;
        
        for (let i = 0; i < firstDayOfWeek; i++) {
            html += `<div class="month-day" style="opacity: 0;"></div>`;
        }
        
        // Добавляем дни месяца
        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(year, month, day);
            date.setHours(0, 0, 0, 0);
            
            const isVisited = isDayVisited(date);
            const isFrozen = isDayFrozen(date);
            const isMissed = isDayMissed(date);
            const isFuture = date > today;
            const isBeforeReg = date < firstVisitDate;
            const isFirstVisit = date.getTime() === firstVisitDate.getTime();
            
            let className = 'month-day';
            if (isBeforeReg) {
                className += ' before-registration';
            } else if (isFuture) {
                className += ' future';
            } else if (isVisited) {
                className += ' visited';
                if (isFirstVisit) {
                    className += ' first-visit';
                }
            } else if (isFrozen) {
                className += ' frozen';
            } else if (isMissed) {
                className += ' missed';
            }
            
            html += `<div class="${className}">${day}</div>`;
        }
        
        html += `</div></div>`;
        
        return html;
    }
    
    // Глобальная функция для навигации (доступна из inline onclick)
    window.navigateCalendarMonth = function(direction) {
        navigateMonth(direction);
    };
    
    // Обработчики событий для модального окна
    document.addEventListener('DOMContentLoaded', function() {
        const openBtn = document.getElementById('openFullCalendar');
        const closeBtn = document.getElementById('closeCalendarModal');
        const modal = document.getElementById('calendarModal');
        
        if (openBtn) {
            openBtn.addEventListener('click', function(e) {
                e.preventDefault();
                openFullCalendar();
            });
        }
        
        if (closeBtn) {
            closeBtn.addEventListener('click', closeFullCalendar);
        }
        
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeFullCalendar();
                }
            });
        }
    });
    
})();
