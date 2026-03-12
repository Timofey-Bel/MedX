

<?php $__env->startSection('page-title', 'Заказы организации'); ?>

<?php $__env->startSection('content'); ?>

<div class="section">
    <div class="tabs-header">
        <div class="tabs-list" id="tabsList">
            <a href="<?php echo e(route('lk.index')); ?>" class="tab-link">Обзор</a>
            <a href="<?php echo e(route('lk.orders')); ?>" class="tab-link active">Заказы</a>
            <a href="<?php echo e(route('lk.organization')); ?>" class="tab-link">Организация</a>
        </div>
        <div class="tabs-actions">
            <button class="btn btn-ghost">Фильтры</button>
            <button class="btn btn-primary">Новый заказ</button>
        </div>
    </div>
</div>

<div class="section">
    <div class="section-header">
        <h2>Все заказы</h2>
    </div>
    
<?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <a href="<?php echo e(route('lk.orders.show', $order['id'])); ?>" style="text-decoration: none; color: inherit; display: block;">
        <div class="card" style="margin-bottom: 16px; cursor: pointer; transition: transform 0.2s ease, box-shadow 0.2s ease;">
            <div class="card-header">
                <div class="card-header-row">
                    <div>
                        <h3 style="font-size: 18px; margin-bottom: 4px;">Заказ #<?php echo e($order['order_code'] ?? $order['id']); ?></h3>
                        <p style="font-size: 14px; color: var(--muted-fg);"><?php echo e(date('d.m.Y H:i', strtotime($order['created_at']))); ?></p>
                    </div>
                    <span class="badge badge-blue"><?php echo e($order['status']); ?></span>
                </div>
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-top: 12px;">
                    <div>
                        <label style="font-size: 13px; color: var(--muted-fg); display: block; margin-bottom: 4px;">Покупатель</label>
                        <p style="font-size: 14px; font-weight: 500;"><?php echo e($order['name']); ?></p>
                        <p style="font-size: 13px; color: var(--muted-fg);"><?php echo e($order['phone']); ?></p>
                    </div>
                    <div>
                        <label style="font-size: 13px; color: var(--muted-fg); display: block; margin-bottom: 4px;">Товаров</label>
                        <p style="font-size: 14px; font-weight: 500;"><?php echo e($order['items_count']); ?> шт.</p>
                    </div>
                    <div>
                        <label style="font-size: 13px; color: var(--muted-fg); display: block; margin-bottom: 4px;">Сумма</label>
                        <p style="font-size: 18px; font-weight: 700;"><?php echo e(number_format($order['total_amount'], 0, ',', ' ')); ?> ₽</p>
                    </div>
                </div>
            </div>
        </div>
    </a>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="card">
        <div class="card-body" style="text-align: center; padding: 48px 16px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--muted-fg); margin-bottom: 16px;"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
            <h3 style="margin-bottom: 8px;">Заказов пока нет</h3>
            <p style="color: var(--muted-fg); margin-bottom: 24px;">Начните делать заказы в каталоге</p>
            <a href="<?php echo e(route('catalog.index')); ?>" class="btn btn-primary">Перейти в каталог</a>
        </div>
    </div>
<?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('wholesale.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\OS\home\sfera\resources\views/wholesale/orders.blade.php ENDPATH**/ ?>