

<?php $__env->startSection('page-title', 'Заказ #' . ($order['order_code'] ?? $order['id'])); ?>

<?php $__env->startSection('content'); ?>
<div class="section">
    <div class="section-header">
        <h2>Заказ #<?php echo e($order['order_code'] ?? $order['id']); ?></h2>
        <div style="display: flex; gap: 12px;">
            <a href="<?php echo e(route('lk.orders')); ?>" class="btn btn-ghost">Назад к заказам</a>
            <a href="<?php echo e(route('lk.orders.repeat', $order['id'])); ?>" class="btn btn-primary">Повторить заказ</a>
        </div>
    </div>

    <div class="grid-2">
        <div class="card">
            <div class="card-header">
                <h3>Информация о заказе</h3>
            </div>
            <div class="card-body">
                <p><strong>Дата:</strong> <?php echo e(date('d.m.Y H:i', strtotime($order['date_init']))); ?></p>
                <p><strong>Статус:</strong> <span class="badge"><?php echo e($order['status']); ?></span></p>
                <p><strong>Сумма:</strong> <?php echo e(number_format($order['full_sum'], 0, ',', ' ')); ?> ₽</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>Контактные данные</h3>
            </div>
            <div class="card-body">
                <p><strong>Имя:</strong> <?php echo e($order['name']); ?></p>
                <p><strong>Телефон:</strong> <?php echo e($order['phone']); ?></p>
<?php if(!empty($order['email'])): ?>
                <p><strong>Email:</strong> <?php echo e($order['email']); ?></p>
<?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="section">
    <div class="section-header">
        <h2>Состав заказа</h2>
    </div>
    <div class="file-list">
        <div class="file-list-head">
            <div>Товар</div>
            <div>Цена</div>
            <div>Количество</div>
            <div>Сумма</div>
        </div>
<?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php if(!empty($item->product_id)): ?>
        <a href="<?php echo e(route('lk.product.show', $item->product_id)); ?>" class="file-row" style="text-decoration: none; color: inherit; display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 16px; cursor: pointer;">
<?php else: ?>
        <div class="file-row" style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 16px;">
<?php endif; ?>
            <div class="file-info">
                <div class="file-info-text">
                    <p><?php echo e($item->product_name); ?></p>
                    <span>ID: <?php echo e($item->product_id ?? 'N/A'); ?></span>
                </div>
            </div>
            <div class="file-cell"><?php echo e(number_format($item->price, 0, ',', ' ')); ?> ₽</div>
            <div class="file-cell"><?php echo e($item->quantity); ?> шт</div>
            <div class="file-cell"><strong><?php echo e(number_format($item->total, 0, ',', ' ')); ?> ₽</strong></div>
<?php if(!empty($item->product_id)): ?>
        </a>
<?php else: ?>
        </div>
<?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('wholesale.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\OS\home\sfera\resources\views/wholesale/order-detail.blade.php ENDPATH**/ ?>