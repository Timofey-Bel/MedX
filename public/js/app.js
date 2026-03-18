import './bootstrap';

// === Promo Carousel ===
(function () {
    const slides = document.querySelector('.promo-slides');
    const dots = document.querySelectorAll('.promo-dot');
    const totalSlides = dots.length;
    let current = 0;
    let autoTimer = null;

    function goTo(index) {
        current = index;
        slides.style.transform = 'translateX(-' + (current * 100) + '%)';
        dots.forEach(function (d, i) {
            d.classList.toggle('active', i === current);
        });
    }

    function next() {
        goTo(current === totalSlides - 1 ? 0 : current + 1);
    }

    function prev() {
        goTo(current === 0 ? totalSlides - 1 : current - 1);
    }

    document.querySelector('.promo-prev').addEventListener('click', function () {
        prev();
        resetAuto();
    });

    document.querySelector('.promo-next').addEventListener('click', function () {
        next();
        resetAuto();
    });

    dots.forEach(function (dot, i) {
        dot.addEventListener('click', function () {
            goTo(i);
            resetAuto();
        });
    });

    function resetAuto() {
        clearInterval(autoTimer);
        autoTimer = setInterval(next, 5000);
    }

    resetAuto();
})();

// === Wishlist Toggle ===
document.querySelectorAll('.wishlist-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
        btn.classList.toggle('liked');
    });
});

// === Add to Cart ===
(function () {
    var cartCount = 3;
    var badge = document.querySelector('.cart-badge');

    document.querySelectorAll('.add-to-cart').forEach(function (btn) {
        btn.addEventListener('click', function () {
            cartCount++;
            badge.textContent = cartCount;
            btn.textContent = 'Добавлено';
            btn.style.background = '#16a34a';
            btn.style.color = '#fff';
            setTimeout(function () {
                btn.innerHTML =
                    '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>' +
                    '<span>В корзину</span>';
                btn.style.background = '';
                btn.style.color = '';
            }, 1500);
        });
    });
})();

// === Search Focus ===
(function () {
    var input = document.querySelector('.search-wrap input');
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            alert('Поиск: ' + input.value);
        }
    });
})();

