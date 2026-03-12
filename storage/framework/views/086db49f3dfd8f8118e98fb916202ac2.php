<!-- Mobile Menu Overlay -->
<div class="mobile-menu-overlay" id="mobileMenuOverlay" aria-hidden="true"></div>

<!-- Mobile Menu -->
<nav class="mobile-menu" id="mobileMenu" aria-hidden="true">
    <div class="mobile-menu-header">
        <img src="<?php echo e(asset('assets/sfera/img/logo/logo_white.svg')); ?>" alt="Творческий Центр СФЕРА" height="50">
        <button class="mobile-menu-close" id="mobileMenuClose" aria-label="Закрыть меню">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                <path d="M5 5l10 10M15 5l-10 10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>
    </div>

    <div class="mobile-menu-section">
        <button class="mobile-menu-location">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M8 1c-2.8 0-5 2.2-5 5 0 3.8 5 9 5 9s5-5.2 5-9c0-2.8-2.2-5-5-5z" stroke="currentColor" stroke-width="1.5"/>
                <circle cx="8" cy="6" r="1.5" fill="currentColor"/>
            </svg>
            <span>Москва</span>
        </button>
    </div>

    <div class="mobile-menu-section">
        <div class="mobile-menu-section-title">Каталог</div>
        <div class="mobile-menu-catalog">
            <?php $__currentLoopData = $categories ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="mobile-catalog-item">
                <?php if(!empty($category['children'])): ?>
                    <button class="mobile-catalog-toggle" aria-expanded="false">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M4 3h12a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z" stroke="currentColor" stroke-width="1.5"/>
                            <path d="M7 7h6M7 11h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                        <span><?php echo e($category['name']); ?></span>
                        <svg class="mobile-catalog-arrow" width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <path d="M6 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <div class="mobile-catalog-submenu">
                        <?php $__currentLoopData = $category['children']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subcategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="mobile-catalog-subitem">
                            <?php if(!empty($subcategory['children'])): ?>
                                <button class="mobile-catalog-subtoggle" aria-expanded="false">
                                    <span><?php echo e($subcategory['name']); ?></span>
                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                        <path d="M5 3l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                                <div class="mobile-catalog-subsubmenu">
                                    <?php $__currentLoopData = $subcategory['children']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subsubcategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <a href="<?php echo e(route('catalog.category', ['category_id' => $subsubcategory['id']])); ?>"><?php echo e($subsubcategory['name']); ?></a>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php else: ?>
                                <a href="<?php echo e(route('catalog.category', ['category_id' => $subcategory['id']])); ?>"><?php echo e($subcategory['name']); ?></a>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <a href="<?php echo e(route('catalog.category', ['category_id' => $category['id']])); ?>" class="mobile-catalog-toggle">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M4 3h12a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z" stroke="currentColor" stroke-width="1.5"/>
                            <path d="M7 7h6M7 11h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                        <span><?php echo e($category['name']); ?></span>
                    </a>
                <?php endif; ?>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>


    <div class="mobile-menu-section">
        <div class="mobile-menu-section-title">Разделы</div>
        <div class="mobile-menu-links">

            <a href="<?php echo e(route('section', ['slug' => 'pedagogam'])); ?>" class="mobile-menu-link">Педагогам детского сада</a>
            <a href="<?php echo e(route('section', ['slug' => 'rukovoditelyam'])); ?>" class="mobile-menu-link">Руководителям ДОО</a>
            <a href="<?php echo e(route('section', ['slug' => 'logopedam'])); ?>" class="mobile-menu-link">Логопедам</a>
            <a href="<?php echo e(route('section', ['slug' => 'roditelyam'])); ?>" class="mobile-menu-link">Родителям</a>
            <a href="<?php echo e(route('section', ['slug' => 'detyam'])); ?>" class="mobile-menu-link">Детям</a>
            <a href="<?php echo e(route('section', ['slug' => 'shkolnikam'])); ?>" class="mobile-menu-link">Школьникам</a>

        </div>
    </div>

    <div class="mobile-menu-section">
        <div class="mobile-menu-section-title">Профиль</div>
        <div class="mobile-menu-links">
            <a href="<?php echo e(route('login')); ?>" class="mobile-menu-link">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <circle cx="10" cy="7" r="3" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M4 17c0-3 3-5 6-5s6 2 6 5" stroke="currentColor" stroke-width="1.5"/>
                </svg>
                Войти
            </a>
            <a href="
<?php if(auth()->guard()->check()): ?>
<?php if(auth()->user()->isWholesale()): ?>
<?php echo e(route('lk.orders')); ?>

<?php else: ?>
<?php echo e(route('my.orders')); ?>

<?php endif; ?>
<?php else: ?>
<?php echo e(route('login')); ?>

<?php endif; ?>
" class="mobile-menu-link">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <rect x="2" y="5" width="16" height="12" rx="2" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M2 9h16M6 13h6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                Заказы
            </a>
            <a href="<?php echo e(route('favorites')); ?>" class="mobile-menu-link">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <path d="M10 17l-5-5c-1.5-1.5-1.5-4 0-5.5 1.5-1.5 4-1.5 5.5 0l.5.5.5-.5c1.5-1.5 4-1.5 5.5 0 1.5 1.5 1.5 4 0 5.5l-5 5z" stroke="currentColor" stroke-width="1.5"/>
                </svg>
                Избранное
            </a>
        </div>
    </div>
</nav>
<?php /**PATH C:\OS\home\sfera\resources\views/components/mobile-menu.blade.php ENDPATH**/ ?>