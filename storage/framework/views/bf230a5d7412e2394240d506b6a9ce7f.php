<?php $__env->startSection('title', $title ?? 'Творческий Центр СФЕРА'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="/assets/sfera/css/page.css">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('head'); ?>
    
    <?php if(!empty($sectionCss)): ?>
    <style>
        /* Стили контентных секций */
        <?php echo $sectionCss; ?>

    </style>
    <?php endif; ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<main class="main-content page-content">
    <div class="container">
        <?php echo $page->content; ?>

    </div>
</main>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    
    <?php if(!empty($sectionJs)): ?>
    <script>
        // JavaScript контентных секций
        <?php echo $sectionJs; ?>

    </script>
    <?php endif; ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\OS\home\sfera\resources\views/page/show.blade.php ENDPATH**/ ?>