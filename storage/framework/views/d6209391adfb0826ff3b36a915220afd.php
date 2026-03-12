
<section class="product-reviews-section">
    <div class="container">
        <h2 class="section-title" style="font-size: 24px;
    line-height: 30px;
    font-weight: 500;
    letter-spacing: -0.01em;
    color: #0d0d0d;
    margin-bottom: 24px;">Обзоры продукции</h2>

        <?php if(count($reviews) > 0): ?>
            <div class="review-blocks-container">
                <!-- First Horizontal Block -->
                <div class="review-block-horizontal">
                    <!-- Main Review Card (Left - 4/6 width) -->
                    <?php if(isset($reviews[0])): ?>
                    <?php $review0 = is_array($reviews[0]) ? $reviews[0] : (array)$reviews[0]; ?>
                    <a href="/page/<?php echo e($review0['id']); ?>/" class="review-card review-card-main">
                        <div class="review-image-container">
                            <?php if(!empty($review0['image'])): ?>
                            <img src="<?php echo e($review0['image']); ?>" alt="<?php echo e($review0['name']); ?>">
                            <?php else: ?>
                            <img src="https://via.placeholder.com/800x400" alt="<?php echo e($review0['name']); ?>">
                            <?php endif; ?>
                            <div class="review-caption">
                                <h3 class="review-caption-title"><?php echo e($review0['name']); ?></h3>
                            </div>
                        </div>
                    </a>
                    <?php endif; ?>

                    <!-- Side Review Grid (Right - 2/6 width, 2x3 grid) -->
                    <div class="review-side-grid">
                        <?php if(isset($reviews[1])): ?>
                        <?php $review1 = is_array($reviews[1]) ? $reviews[1] : (array)$reviews[1]; ?>
                        <a href="/page/<?php echo e($review1['id']); ?>/" class="review-card review-card-small">
                            <div class="review-image-container">
                                <?php if(!empty($review1['image'])): ?>
                                <img src="<?php echo e($review1['image']); ?>" alt="<?php echo e($review1['name']); ?>">
                                <?php else: ?>
                                <img src="https://via.placeholder.com/400x300" alt="<?php echo e($review1['name']); ?>">
                                <?php endif; ?>
                                <div class="review-caption">
                                    <h3 class="review-caption-title"><?php echo e($review1['name']); ?></h3>
                                </div>
                            </div>
                        </a>
                        <?php endif; ?>

                        <?php if(isset($reviews[2])): ?>
                        <?php $review2 = is_array($reviews[2]) ? $reviews[2] : (array)$reviews[2]; ?>
                        <a href="/page/<?php echo e($review2['id']); ?>/" class="review-card review-card-small">
                            <div class="review-image-container">
                                <?php if(!empty($review2['image'])): ?>
                                <img src="<?php echo e($review2['image']); ?>" alt="<?php echo e($review2['name']); ?>">
                                <?php else: ?>
                                <img src="https://via.placeholder.com/400x300" alt="<?php echo e($review2['name']); ?>">
                                <?php endif; ?>
                                <div class="review-caption">
                                    <h3 class="review-caption-title"><?php echo e($review2['name']); ?></h3>
                                </div>
                            </div>
                        </a>
                        <?php endif; ?>

                        <?php if(isset($reviews[3])): ?>
                        <?php $review3 = is_array($reviews[3]) ? $reviews[3] : (array)$reviews[3]; ?>
                        <a href="/page/<?php echo e($review3['id']); ?>/" class="review-card review-card-small">
                            <div class="review-image-container">
                                <?php if(!empty($review3['image'])): ?>
                                <img src="<?php echo e($review3['image']); ?>" alt="<?php echo e($review3['name']); ?>">
                                <?php else: ?>
                                <img src="https://via.placeholder.com/400x300" alt="<?php echo e($review3['name']); ?>">
                                <?php endif; ?>
                                <div class="review-caption">
                                    <h3 class="review-caption-title"><?php echo e($review3['name']); ?></h3>
                                </div>
                            </div>
                        </a>
                        <?php endif; ?>

                        <?php if(isset($reviews[4])): ?>
                        <?php $review4 = is_array($reviews[4]) ? $reviews[4] : (array)$reviews[4]; ?>
                        <a href="/page/<?php echo e($review4['id']); ?>/" class="review-card review-card-small">
                            <div class="review-image-container">
                                <?php if(!empty($review4['image'])): ?>
                                <img src="<?php echo e($review4['image']); ?>" alt="<?php echo e($review4['name']); ?>">
                                <?php else: ?>
                                <img src="https://via.placeholder.com/400x300" alt="<?php echo e($review4['name']); ?>">
                                <?php endif; ?>
                                <div class="review-caption">
                                    <h3 class="review-caption-title"><?php echo e($review4['name']); ?></h3>
                                </div>
                            </div>
                        </a>
                        <?php endif; ?>

                        <?php if(isset($reviews[5])): ?>
                        <?php $review5 = is_array($reviews[5]) ? $reviews[5] : (array)$reviews[5]; ?>
                        <a href="/page/<?php echo e($review5['id']); ?>/" class="review-card review-card-small">
                            <div class="review-image-container">
                                <?php if(!empty($review5['image'])): ?>
                                <img src="<?php echo e($review5['image']); ?>" alt="<?php echo e($review5['name']); ?>">
                                <?php else: ?>
                                <img src="https://via.placeholder.com/400x300" alt="<?php echo e($review5['name']); ?>">
                                <?php endif; ?>
                                <div class="review-caption">
                                    <h3 class="review-caption-title"><?php echo e($review5['name']); ?></h3>
                                </div>
                            </div>
                        </a>
                        <?php endif; ?>

                        <?php if(isset($reviews[6])): ?>
                        <?php $review6 = is_array($reviews[6]) ? $reviews[6] : (array)$reviews[6]; ?>
                        <a href="/page/<?php echo e($review6['id']); ?>/" class="review-card review-card-small">
                            <div class="review-image-container">
                                <?php if(!empty($review6['image'])): ?>
                                <img src="<?php echo e($review6['image']); ?>" alt="<?php echo e($review6['name']); ?>">
                                <?php else: ?>
                                <img src="https://via.placeholder.com/400x300" alt="<?php echo e($review6['name']); ?>">
                                <?php endif; ?>
                                <div class="review-caption">
                                    <h3 class="review-caption-title"><?php echo e($review6['name']); ?></h3>
                                </div>
                            </div>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                

            </div>
        <?php else: ?>
            <div style="padding: 40px; text-align: center; background: #f5f5f5; border-radius: 8px;">
                <p>Обзоры не найдены</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/sfera/css/product-reviews.css')); ?>">
<?php $__env->stopPush(); ?>
<?php /**PATH C:\OS\home\sfera\resources\views/components/showcase/product-reviews.blade.php ENDPATH**/ ?>