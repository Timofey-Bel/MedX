const sidebar = document.getElementById('sidebar');
const toggleBtn = document.getElementById('toggleBtn');
const overlay = document.getElementById('overlay');

function openSidebar() {
    sidebar.classList.add('expanded');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeSidebar() {
    sidebar.classList.remove('expanded');
    overlay.classList.remove('active');
    document.body.style.overflow = '';
}

toggleBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    if (sidebar.classList.contains('expanded')) {
        closeSidebar();
    } else {
        openSidebar();
    }
});

overlay.addEventListener('click', closeSidebar);
document.addEventListener('click', (e) => {
    if (!sidebar.contains(e.target) && sidebar.classList.contains('expanded')) {
        closeSidebar();
    }
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && sidebar.classList.contains('expanded')) {
        closeSidebar();
    }
});

// Поиск по страницам
const searchPages = [
    { title: 'Главная', path: '/main_showcase', keywords: ['главная', 'витрина', 'showcase'] },
    { title: 'Настройки', path: '/main_settings', keywords: ['настройки', 'профиль', 'settings'] },
    { title: 'Оформление', path: '/design_settings', keywords: ['оформление', 'тема', 'дизайн', 'design'] },
    { title: 'Поддержка', path: '/support_settings', keywords: ['поддержка', 'помощь', 'support', 'faq'] },
    { title: 'Тесты', path: '/main_tests', keywords: ['тесты', 'экзамены', 'tests'] },
    { title: 'Сообщество', path: '/main_community', keywords: ['сообщество', 'community'] },
    { title: 'Правила сообщества', path: '/main_community_rules', keywords: ['правила', 'rules'] },
    { title: 'Конфиденциальность', path: '/main_confidentiality', keywords: ['конфиденциальность', 'privacy'] },
    { title: 'База знаний', path: '/main_base', keywords: ['база знаний', 'знания', 'материалы', 'knowledge'] },
    { title: 'Клинические дисциплины', path: '/main_base_clinical', keywords: ['клинические', 'clinical'] }
];

const searchInput = document.getElementById('searchInput');
const searchResults = document.getElementById('searchResults');

if (searchInput && searchResults) {
    searchInput.addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase().trim();
        
        if (query.length === 0) {
            searchResults.classList.remove('active');
            searchResults.innerHTML = '';
            return;
        }
        
        const results = searchPages.filter(page => {
            return page.title.toLowerCase().includes(query) || 
                   page.keywords.some(keyword => keyword.includes(query));
        });
        
        if (results.length > 0) {
            searchResults.innerHTML = results.map(page => `
                <div class="search-result-item" onclick="window.location.href='${page.path}'">
                    <div class="search-result-title">${page.title}</div>
                    <div class="search-result-path">${page.path}</div>
                </div>
            `).join('');
            searchResults.classList.add('active');
        } else {
            searchResults.innerHTML = '<div class="search-no-results">Ничего не найдено</div>';
            searchResults.classList.add('active');
        }
    });
    
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.remove('active');
        }
    });
}


// Сердечко
const favoriteBtn = document.getElementById('favorite-btn');
if (favoriteBtn) {
    favoriteBtn.addEventListener('click', function () {
        const heart = document.getElementById('heartIcon');
        heart.classList.toggle('active');
    });
}

// Меню навигация
document.addEventListener('DOMContentLoaded', function() {
    const currentPath = window.location.pathname;
    const navItems = document.querySelectorAll('.nav-item');
    
    // Группы страниц для подсветки родительского пункта
    const pageGroups = {
        '/main_settings': ['/main_settings', '/design_settings', '/support_settings'],
        '/main_base': ['/main_base', '/main_base_clinical'],
        '/main_community': ['/main_community', '/main_community_rules', '/main_confidentiality']
    };
    
    navItems.forEach(item => {
        item.classList.remove('active');
        
        const link = item.querySelector('a');
        if (link) {
            const linkPath = new URL(link.href).pathname;
            
            // Проверяем прямое совпадение
            if (linkPath === currentPath) {
                item.classList.add('active');
            } else {
                // Проверяем группы страниц
                const group = pageGroups[linkPath];
                if (group && group.includes(currentPath)) {
                    item.classList.add('active');
                }
            }
        }
        
        item.addEventListener('click', function(e) {
            if (!e.target.closest('a')) {
                navItems.forEach(nav => nav.classList.remove('active'));
                this.classList.add('active');
            }
        });
    });

    // Аккордеоны
    const accordionItems = document.querySelectorAll('.accordion-item');
    accordionItems.forEach(item => {
        const header = item.querySelector('.accordion-header');
        if (header) {
            header.addEventListener('click', () => {
                accordionItems.forEach(otherItem => {
                    if (otherItem !== item && otherItem.classList.contains('active')) {
                        otherItem.classList.remove('active');
                    }
                });
                item.classList.toggle('active');
            });
        }
    });
});


document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('oopsModal');
    const closeBtn = document.getElementById('closeOopsModal');

    if (!modal || !closeBtn) return;

    // Функция показа модалки
    window.showOopsModal = function () {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    };

    // Функция закрытия
    function closeModal() {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    // События закрытия
    closeBtn.addEventListener('click', closeModal);
    const overlay = modal.querySelector('.oops-modal-overlay');
    if (overlay) {
        overlay.addEventListener('click', closeModal);
    }

    // ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.classList.contains('active')) {
            closeModal();
        }
    });
});


// Кастомный выпадающий список для часовых поясов
document.addEventListener('DOMContentLoaded', function() {
    const customSelect = document.getElementById('customTimeZone');
    if (!customSelect) return;
    
    const trigger = customSelect.querySelector('.custom-select-trigger');
    const options = customSelect.querySelectorAll('.custom-option');
    const hiddenInput = document.getElementById('time-zone');
    
    // Открытие/закрытие списка
    trigger.addEventListener('click', function(e) {
        e.stopPropagation();
        customSelect.classList.toggle('open');
    });
    
    // Выбор опции
    options.forEach(option => {
        option.addEventListener('click', function() {
            const value = this.getAttribute('data-value');
            const text = this.textContent;
            
            // Обновляем текст в trigger
            trigger.querySelector('span').textContent = text;
            
            // Обновляем скрытое поле
            hiddenInput.value = value;
            
            // Убираем selected со всех опций
            options.forEach(opt => opt.classList.remove('selected'));
            
            // Добавляем selected к выбранной опции
            this.classList.add('selected');
            
            // Закрываем список
            customSelect.classList.remove('open');
        });
    });
    
    // Закрытие при клике вне списка
    document.addEventListener('click', function(e) {
        if (!customSelect.contains(e.target)) {
            customSelect.classList.remove('open');
        }
    });
    
    // Закрытие по ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && customSelect.classList.contains('open')) {
            customSelect.classList.remove('open');
        }
    });
});
