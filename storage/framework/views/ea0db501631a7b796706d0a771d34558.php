

<?php $__env->startSection('page-title', 'Повторить заказ #' . ($order['order_code'] ?? $order['id'])); ?>

<?php $__env->startSection('content'); ?>
<div class="section">
    <div class="section-header">
        <h2>Повторить заказ #<?php echo e($order['order_code'] ?? $order['id']); ?></h2>
        <div style="display: flex; gap: 12px;">
            <a href="<?php echo e(route('lk.orders.show', $order['id'])); ?>" class="btn btn-ghost">Назад к заказу</a>
        </div>
    </div>

    <div class="card" style="margin-bottom: 24px;">
        <div class="card-body" style="padding: 16px;">
            <p style="color: var(--muted-fg); margin-bottom: 0;">
                Вы можете изменить количество товаров или удалить ненужные позиции перед оформлением нового заказа.
            </p>
        </div>
    </div>
</div>

<div class="section">
    <div class="section-header">
        <h2>Состав заказа</h2>
        <div class="order-summary">
            <span style="font-size: 14px; color: var(--muted-fg);">Итого:</span>
            <span id="total-amount" style="font-size: 20px; font-weight: 600; margin-left: 8px;"><?php echo e(number_format(array_sum(array_column($items->toArray(), 'total')), 0, ',', ' ')); ?> ₽</span>
        </div>
    </div>
    <div class="file-list">
        <div class="file-list-head">
            <div>Товар</div>
            <div>Цена</div>
            <div>Количество</div>
            <div>Сумма</div>
        </div>
<?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="file-row order-item" data-item-id="<?php echo e($item->id); ?>" data-price="<?php echo e($item->price); ?>" style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 16px;">
            <div class="file-info">
                <div class="file-info-text">
                    <p><?php echo e($item->product_name); ?></p>
                    <span>ID: <?php echo e($item->product_id ?? 'N/A'); ?></span>
                </div>
            </div>
            <div class="file-cell"><?php echo e(number_format($item->price, 0, ',', ' ')); ?> ₽</div>
            <div class="file-cell">
                <div class="quantity-controls">
                    <button type="button" class="btn-quantity btn-quantity-minus" data-action="decrease">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    </button>
                    <input type="number" class="quantity-input" value="<?php echo e($item->quantity); ?>" min="1" max="999" data-original="<?php echo e($item->quantity); ?>">
                    <button type="button" class="btn-quantity btn-quantity-plus" data-action="increase">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    </button>
                </div>
            </div>
            <div class="file-cell" style="display: flex; align-items: center; justify-content: space-between;">
                <strong class="item-total"><?php echo e(number_format($item->total, 0, ',', ' ')); ?> ₽</strong>
                <button type="button" class="btn-icon-sm btn-remove-item" title="Удалить товар">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                </button>
            </div>
        </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>

<div class="section">
    <div style="display: flex; justify-content: flex-end; gap: 12px;">
        <a href="<?php echo e(route('lk.orders.show', $order['id'])); ?>" class="btn btn-outline">Отменить</a>
        <button type="button" id="btn-place-order" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
            Оформить заказ
        </button>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('assets/sfera/js/order-repeat.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('wholesale.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\OS\home\sfera\resources\views/wholesale/order-repeat.blade.php ENDPATH**/ ?>