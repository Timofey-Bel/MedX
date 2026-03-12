<div class="catalog-main-categories">
<?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <a href="<?php echo e(route('catalog.category', ['category_id' => $category['id']])); ?>" class="catalog-category-item" data-category-id="<?php echo e($category['id']); ?>" <?php if(!empty($category['children'])): ?> aria-expanded="false" aria-label="Развернуть подкатегории" <?php endif; ?>>
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
            <path d="M4 3h12a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z" stroke="currentColor" stroke-width="1.5"/>
            <path d="M7 7h6M7 11h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        <span><?php echo e($category['name']); ?></span>
<?php if(!empty($category['children']) && count($category['children']) > 0): ?>
        <svg class="catalog-arrow" width="16" height="16" viewBox="0 0 16 16" fill="none">
            <path d="M6 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
<?php endif; ?>
    </a>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<div class="catalog-subcategories">
<?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php if(!empty($category['children']) && count($category['children']) > 0): ?>
    <div class="catalog-subcategory-group" data-subcategory="<?php echo e($category['id']); ?>">
        <div class="catalog-subcategory-column">
            <div class="catalog-subcategory-section">
                <h4><?php echo e($category['name']); ?></h4>
<?php $__currentLoopData = $category['children']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subcategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php if($loop->index < 8): ?>
                <a href="<?php echo e(route('catalog.category', ['category_id' => $subcategory['id']])); ?>"><?php echo e($subcategory['name']); ?></a>
<?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
<?php if(count($category['children']) > 8): ?>
        <div class="catalog-subcategory-column">
            <div class="catalog-subcategory-section">
                <h4>Дополнительные категории</h4>
<?php $__currentLoopData = $category['children']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subcategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php if($loop->index >= 8): ?>
                <a href="<?php echo e(route('catalog.category', ['category_id' => $subcategory['id']])); ?>"><?php echo e($subcategory['name']); ?></a>
<?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
<?php endif; ?>
    </div>
<?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php /**PATH C:\OS\home\sfera\resources\views/components/catalog-menu.blade.php ENDPATH**/ ?>