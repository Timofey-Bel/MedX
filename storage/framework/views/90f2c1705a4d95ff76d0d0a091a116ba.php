<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="format-detection" content="telephone=no">
    
    <title><?php echo $__env->yieldContent('title', 'Творческий Центр СФЕРА'); ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- CSS Variables -->
    <style>
        :root {
            --mainFont: 'Roboto', sans-serif;
            --cbPrimaryColor: #0096ff;
            --iconColor: #0096ff;
        }
    </style>
    
    <!-- CSS Assets -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/sfera/styles.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/sfera/css/header.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/sfera/css/city-modal.css')); ?>">
    
    <?php echo $__env->yieldPushContent('styles'); ?>
    <?php echo $__env->yieldPushContent('head'); ?>
</head>
<body>
    <!-- Header Component -->
    <?php echo $__env->make('components.header', [
        'categories' => [], // Временно отключено для диагностики производительности
        'menu_items' => $menu_items ?? []
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
    <!-- Main Content -->
    <main class="l-content">
        <?php echo $__env->yieldContent('content'); ?>
    </main>
    
    <!-- Footer Component -->
    <?php echo $__env->make('components.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
    <!-- Mobile Menu Component -->
    <?php echo $__env->make('components.mobile-menu', [
        'categories' => [] // Временно отключено для диагностики производительности
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
    <!-- City Modal Component -->
    <?php echo $__env->make('components.city-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
    <!-- JavaScript Assets -->
    <script src="<?php echo e(asset('js/jquery.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/knockoutjs/dist/knockout.js')); ?>"></script>
    <script src="<?php echo e(asset('js/knockout-mapping/knockout.mapping.js')); ?>"></script>
    
    <!-- Инициализация глобальной переменной server_cart.data для Knockout.js модели корзины -->
    <!-- Миграция из legacy системы: данные корзины передаются из PHP в JavaScript -->
    <?php
        $defaultCartData = [
            'items' => [],
            'total_cart_sum' => 0,
            'total_cart_amount' => 0,
            'cart_sum' => 0,
            'cart_discount' => 0,
            'promocode' => ''
        ];
    ?>
    <script>
        var server_cart = {
            data: <?php echo json_encode($cart_data ?? $defaultCartData, 15, 512) ?>
        };
    </script>
    
    <!-- Подключение Knockout.js модели корзины -->
    <!-- Миграция из legacy системы: модульная модель корзины с AJAX запросами -->
    <?php echo $__env->make('js.models.cart_new.model_cart', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
    <!-- Подключение ViewModel для счетчиков корзины -->
    <!-- Отдельная ViewModel для избежания конфликтов с множественными applyBindings -->
    <?php echo $__env->make('js.models.cart_new.cart_counter_viewmodel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
    <!-- Подключение ViewModel для счетчика избранного -->
    <!-- Глобальная ViewModel для отображения счетчика избранного в header и mobile nav -->
    <script src="<?php echo e(asset('assets/sfera/js/favorites-counter.js')); ?>"></script>
    
    <!-- Инициализация обработчиков кнопок "В корзину" -->
    <script src="<?php echo e(asset('js/cart-init.js')); ?>"></script>
    
    <script src="<?php echo e(asset('assets/sfera/script.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/sfera/js/header.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/sfera/js/city-modal.js')); ?>"></script>
    
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\OS\home\sfera\resources\views/layouts/app.blade.php ENDPATH**/ ?>