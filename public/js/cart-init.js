/**
 * Cart Initialization Script
 * 
 * Глобальные обработчики для кнопок "В корзину"
 * Миграция из legacy системы: интеграция с Knockout.js моделью корзины
 */

$(document).ready(function() {
    /**
     * Обработчик для кнопок "В корзину"
     * Использует делегирование событий для динамически добавленных кнопок
     */
    $(document).on('click', '.btn-add-to-cart', function(e) {
        e.preventDefault();
        
        var $button = $(this);
        var productId = $button.data('product-id');
        var quantity = $button.data('quantity') || 1;
        
        if (!productId) {
            console.error('Product ID not found');
            return;
        }
        
        // Проверяем, что модель корзины инициализирована
        if (typeof model_cart === 'undefined' || !model_cart.put_into_cart) {
            console.error('Cart model not initialized');
            return;
        }
        
        // Добавляем товар в корзину через Knockout.js модель
        model_cart.put_into_cart({
            id: productId,
            product_id: productId,
            guid: productId,
            quantity: quantity,
            product_amount: quantity
        });
        
        // Показываем уведомление о добавлении
        showNotification('Товар добавлен в корзину');
    });
    
    /**
     * Показать уведомление
     */
    function showNotification(message) {
        // Проверяем, есть ли уже уведомление
        var $existingNotification = $('.cart-notification');
        if ($existingNotification.length > 0) {
            $existingNotification.remove();
        }
        
        // Создаем уведомление
        var $notification = $('<div class="cart-notification"></div>');
        $notification.text(message);
        $notification.css({
            position: 'fixed',
            top: '20px',
            right: '20px',
            padding: '15px 20px',
            backgroundColor: '#4CAF50',
            color: 'white',
            borderRadius: '4px',
            zIndex: 10000,
            boxShadow: '0 2px 5px rgba(0,0,0,0.2)',
            fontSize: '14px',
            fontFamily: 'var(--mainFont)',
            opacity: 0
        });
        
        $('body').append($notification);
        
        // Анимация появления
        $notification.animate({ opacity: 1 }, 300);
        
        // Автоматическое скрытие через 3 секунды
        setTimeout(function() {
            $notification.animate({ opacity: 0 }, 300, function() {
                $notification.remove();
            });
        }, 3000);
    }
});
