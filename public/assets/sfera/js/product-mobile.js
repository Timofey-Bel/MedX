/**
 * Product Page Mobile Interactions
 * Обработка touch-навигации и swipe жестов для мобильных устройств
 */

(function() {
    'use strict';

    // Проверка на мобильное устройство
    const isMobile = window.innerWidth <= 767;
    
    if (!isMobile) {
        return; // Не инициализируем на десктопе
    }

    /**
     * TASK 9.1: Touch-навигация для галереи изображений
     */
    function initGallerySwipe() {
        const gallery = document.querySelector('.gallery-main');
        const mainImage = document.getElementById('mainImage');
        const thumbnails = document.querySelectorAll('.thumbnail-vertical');
        
        if (!gallery || !mainImage || thumbnails.length <= 1) {
            return;
        }

        let currentIndex = 0;
        let touchStartX = 0;
        let touchEndX = 0;
        const minSwipeDistance = 50;

        // Touch start
        gallery.addEventListener('touchstart', function(e) {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        // Touch end
        gallery.addEventListener('touchend', function(e) {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        }, { passive: true });

        function handleSwipe() {
            const swipeDistance = touchEndX - touchStartX;
            
            if (Math.abs(swipeDistance) < minSwipeDistance) {
                return; // Слишком короткий свайп
            }

            if (swipeDistance > 0) {
                // Свайп вправо - предыдущее изображение
                showPreviousImage();
            } else {
                // Свайп влево - следующее изображение
                showNextImage();
            }
        }

        function showPreviousImage() {
            currentIndex = (currentIndex - 1 + thumbnails.length) % thumbnails.length;
            updateImage();
        }

        function showNextImage() {
            currentIndex = (currentIndex + 1) % thumbnails.length;
            updateImage();
        }

        function updateImage() {
            const thumbnail = thumbnails[currentIndex];
            const imageUrl = thumbnail.getAttribute('data-image');
            
            // Обновляем главное изображение
            mainImage.src = imageUrl;
            mainImage.setAttribute('data-zoom', imageUrl);
            
            // Обновляем активную миниатюру
            thumbnails.forEach(t => t.classList.remove('active'));
            thumbnail.classList.add('active');
            
            // Прокручиваем к активной миниатюре
            thumbnail.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
        }

        // Обработка кликов на стрелки навигации
        const prevBtn = document.querySelector('.gallery-prev');
        const nextBtn = document.querySelector('.gallery-next');
        
        if (prevBtn) {
            prevBtn.addEventListener('click', function(e) {
                e.preventDefault();
                showPreviousImage();
            });
        }
        
        if (nextBtn) {
            nextBtn.addEventListener('click', function(e) {
                e.preventDefault();
                showNextImage();
            });
        }

        // Обработка кликов на миниатюры
        thumbnails.forEach((thumbnail, index) => {
            thumbnail.addEventListener('click', function() {
                currentIndex = index;
                updateImage();
            });
        });
    }

    /**
     * TASK 9.2: Sticky positioning для блока покупки при скролле
     */
    function initPurchaseBlockSticky() {
        const purchaseBlock = document.querySelector('.purchase-sticky-content');
        
        if (!purchaseBlock) {
            return;
        }

        let lastScrollTop = 0;
        const scrollThreshold = 300; // Порог скролла для активации компактного режима

        window.addEventListener('scroll', function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            if (scrollTop > scrollThreshold) {
                purchaseBlock.classList.add('scrolled');
            } else {
                purchaseBlock.classList.remove('scrolled');
            }
            
            lastScrollTop = scrollTop;
        }, { passive: true });
    }

    /**
     * TASK 9.3: Touch-friendly переключение табов
     */
    function initTabsTouch() {
        const tabsHeader = document.querySelector('.tabs-header');
        const tabButtons = document.querySelectorAll('.tab-btn');
        
        if (!tabsHeader || tabButtons.length === 0) {
            return;
        }

        // Прокрутка к активному табу при загрузке
        const activeTab = tabsHeader.querySelector('.tab-btn.active');
        if (activeTab) {
            activeTab.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
        }

        // Прокрутка к активному табу при переключении
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                setTimeout(() => {
                    this.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
                }, 100);
            });
        });

        // Swipe для переключения табов
        let touchStartX = 0;
        let touchEndX = 0;
        const minSwipeDistance = 80;
        const tabsContent = document.querySelector('.tabs-content');
        
        if (tabsContent) {
            tabsContent.addEventListener('touchstart', function(e) {
                touchStartX = e.changedTouches[0].screenX;
            }, { passive: true });

            tabsContent.addEventListener('touchend', function(e) {
                touchEndX = e.changedTouches[0].screenX;
                handleTabSwipe();
            }, { passive: true });
        }

        function handleTabSwipe() {
            const swipeDistance = touchEndX - touchStartX;
            
            if (Math.abs(swipeDistance) < minSwipeDistance) {
                return;
            }

            const activeButton = document.querySelector('.tab-btn.active');
            const activeIndex = Array.from(tabButtons).indexOf(activeButton);
            
            if (swipeDistance > 0 && activeIndex > 0) {
                // Свайп вправо - предыдущий таб
                tabButtons[activeIndex - 1].click();
            } else if (swipeDistance < 0 && activeIndex < tabButtons.length - 1) {
                // Свайп влево - следующий таб
                tabButtons[activeIndex + 1].click();
            }
        }
    }

    /**
     * TASK 9.4: Swipe жесты для карусели похожих товаров
     */
    function initRelatedProductsSwipe() {
        const carouselTrack = document.querySelector('.carousel-track');
        const carouselDots = document.querySelectorAll('.carousel-dot');
        
        if (!carouselTrack) {
            return;
        }

        // Обновление активной точки при скролле
        carouselTrack.addEventListener('scroll', function() {
            updateActiveDot();
        }, { passive: true });

        function updateActiveDot() {
            if (carouselDots.length === 0) {
                return;
            }

            const scrollLeft = carouselTrack.scrollLeft;
            const cardWidth = carouselTrack.querySelector('.product-card')?.offsetWidth || 160;
            const gap = 12;
            const currentIndex = Math.round(scrollLeft / (cardWidth + gap));
            
            carouselDots.forEach((dot, index) => {
                if (index === currentIndex) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });
        }

        // Клик на точки навигации
        carouselDots.forEach((dot, index) => {
            dot.addEventListener('click', function() {
                const cardWidth = carouselTrack.querySelector('.product-card')?.offsetWidth || 160;
                const gap = 12;
                const scrollPosition = index * (cardWidth + gap);
                
                carouselTrack.scrollTo({
                    left: scrollPosition,
                    behavior: 'smooth'
                });
            });
        });

        // Инициализация активной точки
        updateActiveDot();

        // Улучшенный snap-эффект для карусели
        let isScrolling;
        carouselTrack.addEventListener('scroll', function() {
            clearTimeout(isScrolling);
            
            isScrolling = setTimeout(function() {
                // Выравнивание к ближайшей карточке после окончания скролла
                const scrollLeft = carouselTrack.scrollLeft;
                const cardWidth = carouselTrack.querySelector('.product-card')?.offsetWidth || 160;
                const gap = 12;
                const nearestIndex = Math.round(scrollLeft / (cardWidth + gap));
                const targetScroll = nearestIndex * (cardWidth + gap);
                
                if (Math.abs(scrollLeft - targetScroll) > 5) {
                    carouselTrack.scrollTo({
                        left: targetScroll,
                        behavior: 'smooth'
                    });
                }
            }, 150);
        }, { passive: true });
    }

    /**
     * Улучшение touch-взаимодействий для кнопок
     */
    function initTouchFeedback() {
        // Добавляем визуальную обратную связь для всех кнопок
        const buttons = document.querySelectorAll('button, .btn-add-to-cart, .btn-add-to-cart-mini, .tab-btn, .thumbnail-vertical');
        
        buttons.forEach(button => {
            button.addEventListener('touchstart', function() {
                this.style.opacity = '0.7';
            }, { passive: true });
            
            button.addEventListener('touchend', function() {
                this.style.opacity = '1';
            }, { passive: true });
            
            button.addEventListener('touchcancel', function() {
                this.style.opacity = '1';
            }, { passive: true });
        });
    }

    /**
     * Предотвращение двойного тапа для зума на iOS
     */
    function preventDoubleTapZoom() {
        let lastTouchEnd = 0;
        
        document.addEventListener('touchend', function(e) {
            const now = Date.now();
            if (now - lastTouchEnd <= 300) {
                e.preventDefault();
            }
            lastTouchEnd = now;
        }, { passive: false });
    }

    /**
     * Инициализация всех мобильных функций
     */
    function init() {
        // Ждем полной загрузки DOM
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                initGallerySwipe();
                initPurchaseBlockSticky();
                initTabsTouch();
                initRelatedProductsSwipe();
                initTouchFeedback();
                preventDoubleTapZoom();
            });
        } else {
            initGallerySwipe();
            initPurchaseBlockSticky();
            initTabsTouch();
            initRelatedProductsSwipe();
            initTouchFeedback();
            preventDoubleTapZoom();
        }
    }

    // Запуск инициализации
    init();

    // Переинициализация при изменении ориентации
    window.addEventListener('orientationchange', function() {
        setTimeout(function() {
            location.reload();
        }, 200);
    });

})();
