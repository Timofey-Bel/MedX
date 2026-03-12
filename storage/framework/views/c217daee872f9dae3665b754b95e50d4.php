<!-- Header -->
<header class="header">

    <?php echo $__env->make('components.location-bar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="header-main">
        <div class="header-container">
            <!-- Mobile Menu Button -->
            <button class="mobile-menu-button" id="mobileMenuButton" aria-label="Открыть меню">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M3 6h18M3 12h18M3 18h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>

            <!-- Logo -->
            <div class="header-logo">
                <a href="<?php echo e(route('home')); ?>"><img src="<?php echo e(asset('assets/sfera/img/logo/logo_dark.svg')); ?>" alt="Творческий Центр СФЕРА" height="100"></a>
            </div>

            <!-- Catalog Button -->
            <button class="catalog-button" id="catalogButton" aria-label="Открыть каталог">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <path d="M2 5h16M2 10h16M2 15h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <span>Каталог</span>
            </button>

            <!-- Catalog Dropdown Menu -->
            <div class="catalog-dropdown" id="catalogDropdown">
                <div class="catalog-overlay" id="catalogOverlay"></div>
                <div class="catalog-menu">
                    <?php echo $__env->make('components.catalog-menu', ['categories' => $categories ?? []], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="search-bar-wrapper">
                <form class="search-bar" action="<?php echo e(route('search')); ?>" method="get">
                    <input type="text" name="query" placeholder="Искать" class="search-input" value="<?php echo e(session('search_query', '')); ?>" autocomplete="off" aria-label="Поиск">
                    <button type="submit" class="search-submit">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="2"/>
                            <path d="M12.5 12.5l4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </button>
                </form>
                <div class="search-suggestions" id="searchSuggestions"></div>
            </div>

            <!-- User Actions -->
            <nav class="header-actions">
                <?php if(auth()->guard()->check()): ?>
                    <a href="
<?php if(auth()->user()->isWholesale()): ?>
<?php echo e(route('lk.index')); ?>

<?php else: ?>
<?php echo e(route('my.profile')); ?>

<?php endif; ?>
" class="header-action" aria-label="Профиль">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="2"/>
                            <path d="M5 20c0-4 3-7 7-7s7 3 7 7" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        <span><?php echo e(Auth::user()->name); ?></span>
                    </a>
                <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>" class="header-action" aria-label="Войти">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="2"/>
                            <path d="M5 20c0-4 3-7 7-7s7 3 7 7" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        <span>Войти</span>
                    </a>
                <?php endif; ?>
                <?php if(auth()->guard()->check()): ?>
                <a href="
<?php if(auth()->user()->isWholesale()): ?>
<?php echo e(route('lk.orders')); ?>

<?php else: ?>
<?php echo e(route('my.orders')); ?>

<?php endif; ?>
" class="header-action" aria-label="Заказы">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect x="3" y="6" width="18" height="14" rx="2" stroke="currentColor" stroke-width="2"/>
                        <path d="M3 10h18M8 14h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <span>Заказы</span>
                </a>
                <?php endif; ?>
                <a href="<?php echo e(route('favorites')); ?>" class="header-action header-action-favorites" aria-label="Избранное">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    <span>Избранное</span>
                    <span class="mobile-nav-badge favorites-counter" style="right:20%;top:-10px;" data-bind="text: formattedCount, visible: isVisible"></span>
                </a>
                <a href="<?php echo e(route('cart')); ?>" class="header-action header-action-cart" aria-label="Корзина">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M3 3h2l1 4m0 0l3 11h10l3-11H6z" stroke="currentColor" stroke-width="2"/>
                        <circle cx="9" cy="20" r="1" fill="currentColor"/>
                        <circle cx="17" cy="20" r="1" fill="currentColor"/>
                    </svg>
                    <span>Корзина</span>
                    <span class="mobile-nav-badge cart-counter" style="right:20%;top:-10px;" data-bind="text: formattedCount, visible: isVisible"></span>
                </a>

            </nav>
        </div>
    </div>

    <!-- Secondary Navigation -->
    <div class="header-secondary">
        <div class="header-container">
            <?php echo $__env->make('components.secondary-nav', ['menu_items' => $menu_items ?? []], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </div>
</header>
<?php /**PATH C:\OS\home\sfera\resources\views/components/header.blade.php ENDPATH**/ ?>