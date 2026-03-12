<!-- Mobile Bottom Navigation -->

<style>
    @media (max-width: 894px) {
        .dj5_11 {
            display: none;
        }
        .mobile-bottom-nav {
            display: flex;
        }
    }

    @media (min-width: 895px) {
        .mobile-bottom-nav {
            display: none;
        }
    }

</style>

<nav class="mobile-bottom-nav">
    <a href="<?php echo e(route('home')); ?>" class="mobile-bottom-nav-item <?php echo e(request()->routeIs('home') ? 'active' : ''); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            <polyline points="9 22 9 12 15 12 15 22"></polyline>
        </svg>
        <span>Главная</span>
    </a>

    <a href="<?php echo e(route('catalog.index')); ?>" class="mobile-bottom-nav-item <?php echo e(request()->routeIs('catalog*') ? 'active' : ''); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="7" height="7"></rect>
            <rect x="14" y="3" width="7" height="7"></rect>
            <rect x="14" y="14" width="7" height="7"></rect>
            <rect x="3" y="14" width="7" height="7"></rect>
        </svg>
        <span>Каталог</span>
    </a>

    <a href="<?php echo e(route('favorites')); ?>" class="mobile-bottom-nav-item <?php echo e(request()->routeIs('favorites') ? 'active' : ''); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
        </svg>
        <span>Избранное</span>
        <span class="mobile-nav-badge favorites-counter" data-bind="text: formattedCount, visible: isVisible"></span>
    </a>

    <a href="<?php echo e(route('cart')); ?>" class="mobile-bottom-nav-item <?php echo e(request()->routeIs('cart') ? 'active' : ''); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="9" cy="21" r="1"></circle>
            <circle cx="20" cy="21" r="1"></circle>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
        </svg>
        <span>Корзина</span>
        <span class="mobile-nav-badge mobile-cart-counter" data-bind="text: formattedCount, visible: isVisible"></span>
    </a>

    <a href="
<?php if(auth()->guard()->check()): ?>
<?php if(auth()->user()->isWholesale()): ?>
<?php echo e(route('lk.index')); ?>

<?php else: ?>
<?php echo e(route('my.profile')); ?>

<?php endif; ?>
<?php else: ?>
<?php echo e(route('login')); ?>

<?php endif; ?>
" class="mobile-bottom-nav-item <?php echo e(request()->routeIs('my.*') || request()->routeIs('lk.*') || request()->routeIs('login') ? 'active' : ''); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
        </svg>
        <span>Профиль</span>
    </a>
</nav>
<?php /**PATH C:\OS\home\sfera\resources\views/components/mobile-bottom-nav.blade.php ENDPATH**/ ?>