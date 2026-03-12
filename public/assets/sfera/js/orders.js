// Orders Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    console.log('Orders page initialized');

    // Обработка фильтров
    const statusFilters = document.querySelectorAll('input[name="status"]');
    const periodFilters = document.querySelectorAll('input[name="period"]');

    // Функция для применения фильтров
    function applyFilters() {
        const status = document.querySelector('input[name="status"]:checked')?.value || 'all';
        const period = document.querySelector('input[name="period"]:checked')?.value || 'all';

        // Формируем URL с параметрами
        const url = new URL(window.location.href);
        url.searchParams.set('status', status);
        url.searchParams.set('period', period);

        // Перезагружаем страницу с новыми параметрами
        window.location.href = url.toString();
    }

    // Добавляем обработчики на фильтры
    statusFilters.forEach(filter => {
        filter.addEventListener('change', applyFilters);
    });

    periodFilters.forEach(filter => {
        filter.addEventListener('change', applyFilters);
    });

    // Обработка кнопок действий
    const orderCards = document.querySelectorAll('.order-card');

    orderCards.forEach(card => {
        // Кнопка "Отследить"
        const trackBtn = card.querySelector('.btn-outline');
        if (trackBtn && trackBtn.textContent.includes('Отследить')) {
            trackBtn.addEventListener('click', function() {
                const orderNumber = card.querySelector('.order-number').textContent;
                alert(`Отслеживание заказа ${orderNumber}\n\nФункционал в разработке`);
            });
        }

        // Кнопка "Детали заказа"
        const detailsBtn = card.querySelector('.btn-secondary');
        if (detailsBtn && detailsBtn.textContent.includes('Детали')) {
            detailsBtn.addEventListener('click', function() {
                const orderNumber = card.querySelector('.order-number').textContent;
                alert(`Детали заказа ${orderNumber}\n\nФункционал в разработке`);
            });
        }

        // Кнопка "Повторить заказ"
        if (trackBtn && trackBtn.textContent.includes('Повторить')) {
            trackBtn.addEventListener('click', function() {
                const orderNumber = card.querySelector('.order-number').textContent;
                if (confirm(`Повторить заказ ${orderNumber}?\n\nТовары будут добавлены в корзину`)) {
                    alert('Функционал в разработке');
                }
            });
        }

        // Кнопка "Оставить отзыв"
        if (detailsBtn && detailsBtn.textContent.includes('отзыв')) {
            detailsBtn.addEventListener('click', function() {
                const orderNumber = card.querySelector('.order-number').textContent;
                alert(`Оставить отзыв на заказ ${orderNumber}\n\nФункционал в разработке`);
            });
        }
    });
});
