/**
 * MedX Showcase Page JavaScript
 */

(function() {
    'use strict';

    // Disciplines tabs functionality
    function initDisciplinesTabs() {
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetTab = this.getAttribute('data-tab');

                // Remove active class from all buttons and contents
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));

                // Add active class to clicked button and corresponding content
                this.classList.add('active');
                const targetContent = document.getElementById(targetTab);
                if (targetContent) {
                    targetContent.classList.add('active');
                }
            });
        });
    }

    // Smooth scroll for anchor links
    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href !== '#' && href.length > 1) {
                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }
            });
        });
    }

    // Conveyor animation (infinite scroll)
    function initConveyorAnimation() {
        const conveyorBar = document.querySelector('.conveyor-bar');
        if (conveyorBar) {
            // Clone elements for seamless loop
            const elements = conveyorBar.innerHTML;
            conveyorBar.innerHTML += elements;
        }
    }

    // Scroll cards horizontal scroll with mouse wheel
    function initScrollCards() {
        const scrollContainer = document.querySelector('.scroll-cards');
        if (scrollContainer) {
            scrollContainer.addEventListener('wheel', function(e) {
                if (e.deltaY !== 0) {
                    e.preventDefault();
                    this.scrollLeft += e.deltaY;
                }
            });
        }
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initDisciplinesTabs();
            initSmoothScroll();
            initConveyorAnimation();
            initScrollCards();
        });
    } else {
        initDisciplinesTabs();
        initSmoothScroll();
        initConveyorAnimation();
        initScrollCards();
    }

})();
