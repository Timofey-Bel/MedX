<?php $__env->startSection('title', 'Творческий Центр СФЕРА Интернет-магазин'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/sfera/css/carousel.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/sfera/css/categories.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/sfera/css/top10-slider.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/sfera/css/product-reviews.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/sfera/css/catalog.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<!-- Main Content -->
<main class="main-content">


    
    <!-- Triple Carousel Block -->
    <section class="triple-carousel-section">
        <div class="container">
            <div class="triple-carousel-wrapper">

                <?php echo $__env->make('components.showcase.main-carousel', ['banners' => $mainBanners], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                
        </div>
    </section>        
    


    <!-- Popular Categories Block -->
    <?php echo $__env->make('components.showcase.popular-categories', ['categories' => $popularCategories], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- TOP-10 Slider Block -->
    <?php echo $__env->make('components.showcase.top10-slider', ['products' => $top10Products, 'cart' => $cart, 'favorites' => $favorites], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Product Reviews Section -->
    <?php echo $__env->make('components.showcase.product-reviews', ['reviews' => $productReviews], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Product Grid -->
    <section class="product-grid-section">
        <div class="container">
            <h2 class="section-title" style="font-size: 24px;
    line-height: 30px;
    font-weight: 500;
    letter-spacing: -0.01em;
    color: #0d0d0d;
    margin-bottom: 24px;">Новинки</h2>
            <?php echo $__env->make('components.showcase.random-products', ['products' => $randomProducts, 'cart' => $cart, 'favorites' => $favorites], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </section>
</main>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('assets/sfera/js/carousel.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/sfera/js/top10-slider.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/sfera/js/catalog.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/sfera/js/showcase-init.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\OS\home\sfera\resources\views/showcase/index.blade.php ENDPATH**/ ?>