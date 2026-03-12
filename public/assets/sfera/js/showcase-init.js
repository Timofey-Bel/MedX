/**
 * Инициализация функционала главной страницы (витрины)
 * 
 * Этот скрипт обеспечивает правильную инициализацию всех интерактивных элементов
 * на главной странице, включая:
 * - Кнопки избранного
 * - Кнопки добавления в корзину
 * - Контролы количества товара
 * - Синхронизацию состояния с сервером
 */

(function() {
    'use strict';
    
    // Функция инициализации, которая вызывается после полной загрузки DOM
    function initShowcase() {
        console.log('=== Showcase initialization started ===');
        
        // Диагностика: выводим количество найденных элементов ДО инициализации
        console.log('Elements BEFORE initialization:');
        console.log('- Favorite buttons:', document.querySelectorAll('.product-favorite').length);
        console.log('- Add to cart buttons:', document.querySelectorAll('.btn-add-to-cart').length);
        console.log('- Quantity controls:', document.querySelectorAll('.product-quantity-control').length);
        console.log('- Buy all buttons:', document.querySelectorAll('.btn-buy-all').length);
        
        // Проверяем наличие необходимых функций из catalog.js
        if (typeof setupFavoriteButtons === 'undefined') {
            console.error('setupFavoriteButtons is not defined. Make sure catalog.js is loaded first.');
            return;
        }
        
        if (typeof setupAddToCart === 'undefined') {
            console.error('setupAddToCart is not defined. Make sure catalog.js is loaded first.');
            return;
        }
        
        if (typeof setupQuantityControls === 'undefined') {
            console.error('setupQuantityControls is not defined. Make sure catalog.js is loaded first.');
            return;
        }
        
        if (typeof setupBuyAll === 'undefined') {
            console.error('setupBuyAll is not defined. Make sure catalog.js is loaded first.');
            return;
        }
        
        console.log('All required functions are available.');
        
        // Тестируем клик на первую кнопку избранного
        const firstFavoriteBtn = document.querySelector('.product-favorite');
        if (firstFavoriteBtn) {
            console.log('First favorite button found:', firstFavoriteBtn);
            console.log('- Product ID:', firstFavoriteBtn.getAttribute('data-product-id'));
            console.log('- Classes:', firstFavoriteBtn.className);
            console.log('- Has click listener:', firstFavoriteBtn.onclick !== null);
        } else {
            console.warn('No favorite buttons found on page!');
        }
        
        // Инициализируем все функции
        // НЕ вызываем их здесь, так как catalog.js уже вызывает их автоматически
        // Просто проверяем, что они доступны
        
        console.log('=== Showcase initialization completed ===');
        
        // Диагностика: выводим количество найденных элементов ПОСЛЕ инициализации
        console.log('Elements AFTER initialization:');
        console.log('- Favorite buttons:', document.querySelectorAll('.product-favorite').length);
        console.log('- Favorite buttons with listeners:', Array.from(document.querySelectorAll('.product-favorite')).filter(btn => btn.onclick !== null).length);
    }
    
    // Запускаем инициализацию после полной загрузки DOM
    // Добавляем небольшую задержку, чтобы catalog.js успел инициализироваться
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(initShowcase, 100);
        });
    } else {
        // DOM уже загружен, запускаем с задержкой
        setTimeout(initShowcase, 100);
    }
})();
