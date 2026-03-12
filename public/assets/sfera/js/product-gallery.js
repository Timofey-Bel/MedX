/**
 * Product Gallery with Swiper.js - Vertical Thumbs
 * 
 * Галерея изображений товара с вертикальной каруселью миниатюр
 * Адаптировано из CodePen примера: https://codepen.io/hqdrone/pen/dypEyNq
 * 
 * @requires Swiper.js v11.x
 */

class ProductGallery {
    constructor() {
        this.thumbsSwiper = null;
        this.mainSwiper = null;
        this.lightboxSwiper = null;
        this.lightboxElement = null;
    }

    /**
     * Инициализация галереи
     */
    init() {
        // Проверяем наличие контейнеров
        const thumbsContainer = document.querySelector('.product-gallery__thumbs .swiper-container');
        const mainContainer = document.querySelector('.product-gallery__images .swiper-container');

        if (!thumbsContainer || !mainContainer) {
            console.warn('Product gallery containers not found');
            return;
        }

        // Инициализируем Swiper для миниатюр (сначала thumbs!)
        this.initThumbsSwiper();

        // Инициализируем основной Swiper (после thumbs!)
        this.initMainSwiper();

        // Создаем lightbox структуру
        this.createLightbox();

        // Добавляем обработчик клика на основное изображение
        this.setupLightboxTrigger();

        // Добавляем keyboard navigation для миниатюр
        this.setupKeyboardNavigation();

        console.log('ProductGallery initialized successfully');
    }

    /**
     * Инициализация Swiper для миниатюр
     */
    initThumbsSwiper() {
        this.thumbsSwiper = new Swiper('.product-gallery__thumbs .swiper-container', {
            direction: 'vertical',
            slidesPerView: 4,
            spaceBetween: 16,
            navigation: {
                nextEl: '.product-gallery__next',
                prevEl: '.product-gallery__prev'
            },
            freeMode: true,
            watchSlidesProgress: true,
            // Lazy loading для миниатюр
            lazy: {
                loadPrevNext: true,
                loadPrevNextAmount: 2
            },
            preloadImages: false,
            breakpoints: {
                0: {
                    direction: 'horizontal',
                    slidesPerView: 'auto',
                    spaceBetween: 8
                },
                768: {
                    direction: 'vertical',
                    slidesPerView: 4,
                    spaceBetween: 16
                }
            }
        });
    }

    /**
     * Инициализация основного Swiper
     */
    initMainSwiper() {
        this.mainSwiper = new Swiper('.product-gallery__images .swiper-container', {
            direction: 'vertical',
            slidesPerView: 1,
            spaceBetween: 0,
            allowTouchMove: false, // Отключаем свайп на основном изображении
            mousewheel: false, // Отключаем прокрутку колёсиком
            navigation: {
                nextEl: '.product-gallery__next',
                prevEl: '.product-gallery__prev'
            },
            thumbs: {
                swiper: this.thumbsSwiper
            },
            // Оптимизация анимаций
            speed: 300,
            effect: 'fade',
            fadeEffect: {
                crossFade: true
            },
            // Lazy loading для основных изображений
            lazy: {
                loadPrevNext: true,
                loadPrevNextAmount: 1
            },
            preloadImages: false,
            breakpoints: {
                0: {
                    direction: 'horizontal',
                    allowTouchMove: true, // На мобильных разрешаем свайп
                    effect: 'slide'
                },
                768: {
                    direction: 'vertical',
                    allowTouchMove: false, // На десктопе запрещаем свайп
                    effect: 'fade'
                }
            }
        });
    }

    /**
     * Создание структуры lightbox
     */
    createLightbox() {
        // Создаем элемент lightbox
        this.lightboxElement = document.createElement('div');
        this.lightboxElement.className = 'product-lightbox';
        this.lightboxElement.innerHTML = `
            <button class="lightbox-close" aria-label="Закрыть">×</button>
            <div class="swiper product-lightbox-swiper">
                <div class="swiper-wrapper"></div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-pagination"></div>
            </div>
        `;

        document.body.appendChild(this.lightboxElement);

        // Обработчик закрытия
        const closeBtn = this.lightboxElement.querySelector('.lightbox-close');
        closeBtn.addEventListener('click', () => this.closeLightbox());

        // Закрытие по клику на overlay
        this.lightboxElement.addEventListener('click', (e) => {
            if (e.target === this.lightboxElement) {
                this.closeLightbox();
            }
        });

        // Закрытие по Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.lightboxElement.classList.contains('active')) {
                this.closeLightbox();
            }
        });
    }

    /**
     * Настройка триггера для открытия lightbox
     */
    setupLightboxTrigger() {
        const mainSlides = document.querySelectorAll('.product-gallery__images .swiper-slide img');
        mainSlides.forEach((img) => {
            img.addEventListener('click', () => {
                const activeIndex = this.mainSwiper.activeIndex;
                this.openLightbox(activeIndex);
            });
        });
    }

    /**
     * Открытие lightbox
     * @param {number} startIndex - Индекс начального слайда
     */
    openLightbox(startIndex = 0) {
        // Получаем все изображения из основного Swiper
        const slides = document.querySelectorAll('.product-gallery__images .swiper-slide');
        const wrapper = this.lightboxElement.querySelector('.swiper-wrapper');

        // Очищаем wrapper
        wrapper.innerHTML = '';

        // Добавляем слайды в lightbox
        slides.forEach((slide) => {
            const img = slide.querySelector('img');
            if (img) {
                const lightboxSlide = document.createElement('div');
                lightboxSlide.className = 'swiper-slide';
                lightboxSlide.innerHTML = `<img src="${img.src}" alt="${img.alt}">`;
                wrapper.appendChild(lightboxSlide);
            }
        });

        // Показываем lightbox
        this.lightboxElement.classList.add('active');
        document.body.classList.add('lightbox-open');

        // Инициализируем Swiper для lightbox
        this.lightboxSwiper = new Swiper('.product-lightbox-swiper', {
            initialSlide: startIndex,
            spaceBetween: 10,
            navigation: {
                nextEl: '.product-lightbox .swiper-button-next',
                prevEl: '.product-lightbox .swiper-button-prev',
            },
            pagination: {
                el: '.product-lightbox .swiper-pagination',
                type: 'fraction',
            },
            keyboard: {
                enabled: true,
                onlyInViewport: false
            },
            zoom: {
                maxRatio: 3,
                minRatio: 1
            },
            loop: true
        });
    }

    /**
     * Закрытие lightbox
     */
    closeLightbox() {
        this.lightboxElement.classList.remove('active');
        document.body.classList.remove('lightbox-open');

        // Уничтожаем Swiper lightbox
        if (this.lightboxSwiper) {
            this.lightboxSwiper.destroy(true, true);
            this.lightboxSwiper = null;
        }
    }

    /**
     * Настройка keyboard navigation для миниатюр
     */
    setupKeyboardNavigation() {
        const thumbSlides = document.querySelectorAll('.product-gallery__thumbs .swiper-slide');
        
        thumbSlides.forEach((slide, index) => {
            // Enter или Space активирует миниатюру
            slide.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.mainSwiper.slideTo(index);
                    slide.click();
                }
            });
        });

        // Keyboard navigation для основного изображения
        const mainImages = document.querySelectorAll('.product-gallery__images .swiper-slide img');
        mainImages.forEach((img) => {
            img.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    const activeIndex = this.mainSwiper.activeIndex;
                    this.openLightbox(activeIndex);
                }
            });
        });
    }

    /**
     * Уничтожение галереи
     */
    destroy() {
        if (this.thumbsSwiper) {
            this.thumbsSwiper.destroy(true, true);
        }
        if (this.mainSwiper) {
            this.mainSwiper.destroy(true, true);
        }
        if (this.lightboxSwiper) {
            this.lightboxSwiper.destroy(true, true);
        }
        if (this.lightboxElement) {
            this.lightboxElement.remove();
        }
    }
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', () => {
    const gallery = new ProductGallery();
    gallery.init();
    
    // Сохраняем ссылку на галерею для доступа из консоли (для отладки)
    window.productGallery = gallery;
});
