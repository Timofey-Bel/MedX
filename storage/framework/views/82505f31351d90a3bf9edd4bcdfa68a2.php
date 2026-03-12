
<div class="main-carousel-container">
    <div class="main-carousel">
        <?php if(count($banners) > 0): ?>
            <button class="carousel-btn carousel-prev" data-carousel="main">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            <div class="carousel-track" data-carousel="main">
                <?php $__currentLoopData = $banners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="carousel-slide<?php echo e($index === 0 ? ' active' : ''); ?>">
                        <img src="<?php echo e($banner->url); ?>" alt="<?php echo e($banner->title ?? $banner->name); ?>">
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <button class="carousel-btn carousel-next" data-carousel="main">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            <div class="carousel-dots" data-carousel="main">
                <?php $__currentLoopData = $banners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button class="carousel-dot<?php echo e($index === 0 ? ' active' : ''); ?>" data-index="<?php echo e($index); ?>"></button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <div style="padding: 40px; text-align: center; background: #f5f5f5; border-radius: 8px;">
                <p>Баннеры не найдены</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/sfera/css/carousel.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('assets/sfera/js/carousel.js')); ?>"></script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\OS\home\sfera\resources\views/components/showcase/main-carousel.blade.php ENDPATH**/ ?>