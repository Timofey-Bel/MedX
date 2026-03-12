
<section class="popular-categories-section">
    <div class="container">
        <h2 class="section-title">Популярные категории</h2>
        <?php if(isset($popularCategories) && count($popularCategories) > 0): ?>
            <div class="categories-grid">
                <?php $__currentLoopData = $popularCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="/catalog/<?php echo e($category['guid'] ?? ''); ?>/" class="category-card">
                        <div class="category-image">
                            <?php if($category['image'] ?? null): ?>
                                <img src="<?php echo e(asset($category['image'])); ?>" alt="<?php echo e($category['title'] ?? 'Категория'); ?>">
                            <?php else: ?>
                                <img src="<?php echo e(asset('assets/sfera/img/category-placeholder.jpg')); ?>" alt="<?php echo e($category['title'] ?? 'Категория'); ?>">
                            <?php endif; ?>
                        </div>
                        <div class="category-label">
                            <h3><?php echo e($category['title'] ?? 'Категория'); ?></h3>
                        </div>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <div style="padding: 40px; text-align: center; background: #f5f5f5; border-radius: 8px;">
                <p>Категории не найдены</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/sfera/css/categories.css')); ?>">
<?php $__env->stopPush(); ?>
<?php /**PATH C:\OS\home\sfera\resources\views/components/showcase/popular-categories.blade.php ENDPATH**/ ?>