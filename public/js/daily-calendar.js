// Календарь ежедневных входов
(function() {
    'use strict';
    
    // ------------------- Переменные -------------------
    let swiper = null;
    let weeksData = [];
    let currentWeekIndex = 5;
    let visitedDays = new Set();
    let firstVisitDate = null; // Дата первого входа пользователя
    
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
        
        // Автоматически отмечаем сегодняшний день
        const today = formatDateKey(new Date());
        if (!visitedDays.has(today)) {
            visitedDays.add(today);
            saveVisitedDays();
        }
    }
    
    function saveVisitedDays() {
        localStorage.setItem('medx_visited_days', JSON.stringify([...visitedDays]));
    }
    
    // Проверка, был ли день посещен
    function isDayVisited(date) {
        return visitedDays.has(formatDateKey(date));
    }
    
    // Проверка, пропущен ли день (прошедший день без посещения ПОСЛЕ первого входа)
    function isDayMissed(date) {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const dateNormalized = new Date(date);
        dateNormalized.setHours(0, 0, 0, 0);
        
        // День пропущен только если:
        // 1. Он в прошлом (раньше сегодня)
        // 2. Он не был посещен
        // 3. Он после или равен дате первого входа
        return dateNormalized < today && 
               !isDayVisited(date) && 
               dateNormalized >= firstVisitDate;
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
            const isMissed = isDayMissed(day);
            
            const todayClass = isToday ? 'today' : '';
            const dayNumber = formatDate(day);
            
            // Определяем класс для иконки
            let dotClass = '';
            if (isVisited) {
                dotClass = 'checked';
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
    
})();
