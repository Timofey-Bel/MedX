<nav class="secondary-nav">
    <?php $__currentLoopData = $menu_items ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php if(!empty($item['submenu'])): ?>
    <div class="secondary-nav-dropdown">
        <button class="secondary-nav-dropdown-trigger">
            <?php echo e($item['title']); ?>

            <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                <path d="M3 4.5l3 3 3-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
        </button>
        <div class="secondary-nav-dropdown-menu">
            <?php $__currentLoopData = $item['submenu']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subitem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e($subitem['link']); ?>"><?php echo e($subitem['title']); ?></a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php else: ?>
    <a href="<?php echo e($item['link']); ?>"><?php echo e($item['title']); ?></a>
    <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</nav>
<?php /**PATH C:\OS\home\sfera\resources\views/components/secondary-nav.blade.php ENDPATH**/ ?>