const sidebar = document.getElementById('sidebar');
const toggleBtn = document.getElementById('toggleBtn');
const overlay = document.getElementById('overlay');

function openSidebar() {
    sidebar.classList.add('expanded');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden'; // Блокируем скролл
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

// Закрытие кликом на оверлей или вне сайдбара
overlay.addEventListener('click', closeSidebar);
document.addEventListener('click', (e) => {
    if (!sidebar.contains(e.target) && sidebar.classList.contains('expanded')) {
        closeSidebar();
    }
});

// Закрытие на ESC
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && sidebar.classList.contains('expanded')) {
        closeSidebar();
    }
});


// Сердечко
const favoriteBtn = document.getElementById('favorite-btn');
if (favoriteBtn) {
    favoriteBtn.addEventListener('click', function () {
        const heart = document.getElementById('heartIcon');
        heart.classList.toggle('active');
    });
}

// Меню навигация
const navItems = document.querySelectorAll('.nav-item');
navItems.forEach(item => {
    item.addEventListener('click', function () {
        navItems.forEach(nav => nav.classList.remove('active'));
        this.classList.add('active');
    });
});


document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('oopsModal');
    const closeBtn = document.getElementById('closeOopsModal');

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
    modal.querySelector('.oops-modal-overlay').addEventListener('click', closeModal);

    // ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.classList.contains('active')) {
            closeModal();
        }
    });
});
