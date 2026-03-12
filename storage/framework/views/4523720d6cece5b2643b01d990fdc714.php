

<?php $__env->startSection('page-title', 'Данные организации'); ?>

<?php $__env->startSection('content'); ?>

<div class="section">
    <div class="tabs-header">
        <div class="tabs-list" id="tabsList">
            <a href="<?php echo e(route('lk.index')); ?>" class="tab-link">Обзор</a>
            <a href="<?php echo e(route('lk.orders')); ?>" class="tab-link">Заказы</a>
            <a href="<?php echo e(route('lk.organization')); ?>" class="tab-link active">Организация</a>
        </div>
        <div class="tabs-actions">
            <button class="btn btn-ghost">Редактировать</button>
            <button class="btn btn-primary">Сохранить</button>
        </div>
    </div>
</div>

<div class="section">
    <div class="profile-card">
        <div class="profile-card-header">
            <h3>Реквизиты организации</h3>
        </div>
        <div class="profile-card-body">
            <div class="org-info-grid">
                <div class="org-info-item">
                    <label>Полное наименование</label>
                    <p><?php echo e($organization->name_full); ?></p>
                </div>

<?php if($organization->name_short): ?>
                <div class="org-info-item">
                    <label>Краткое наименование</label>
                    <p><?php echo e($organization->name_short); ?></p>
                </div>
<?php endif; ?>

                <div class="org-info-item">
                    <label>ИНН</label>
                    <p><?php echo e($organization->inn); ?></p>
                </div>

<?php if($organization->kpp): ?>
                <div class="org-info-item">
                    <label>КПП</label>
                    <p><?php echo e($organization->kpp); ?></p>
                </div>
<?php endif; ?>

<?php if($organization->ogrn): ?>
                <div class="org-info-item">
                    <label>ОГРН</label>
                    <p><?php echo e($organization->ogrn); ?></p>
                </div>
<?php endif; ?>

<?php if($organization->opf): ?>
                <div class="org-info-item">
                    <label>Организационно-правовая форма</label>
                    <p><?php echo e($organization->opf); ?></p>
                </div>
<?php endif; ?>

<?php if($organization->legal_address): ?>
                <div class="org-info-item org-info-item-full">
                    <label>Юридический адрес</label>
                    <p><?php echo e($organization->legal_address); ?></p>
                </div>
<?php endif; ?>

<?php if($organization->postal_address && $organization->postal_address !== $organization->legal_address): ?>
                <div class="org-info-item org-info-item-full">
                    <label>Почтовый адрес</label>
                    <p><?php echo e($organization->postal_address); ?></p>
                </div>
<?php endif; ?>

<?php if($organization->director_name): ?>
                <div class="org-info-item">
                    <label>Руководитель</label>
                    <p><?php echo e($organization->director_name); ?></p>
                </div>
<?php endif; ?>

<?php if($organization->director_position): ?>
                <div class="org-info-item">
                    <label>Должность руководителя</label>
                    <p><?php echo e($organization->director_position); ?></p>
                </div>
<?php endif; ?>

<?php if($organization->phone): ?>
                <div class="org-info-item">
                    <label>Телефон</label>
                    <p><?php echo e($organization->phone); ?></p>
                </div>
<?php endif; ?>

<?php if($organization->email): ?>
                <div class="org-info-item">
                    <label>Email</label>
                    <p><?php echo e($organization->email); ?></p>
                </div>
<?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if($organization->bank_name || $organization->bank_bik || $organization->bank_account): ?>
<div class="section">
    <div class="profile-card">
        <div class="profile-card-header">
            <h3>Банковские реквизиты</h3>
        </div>
        <div class="profile-card-body">
            <div class="org-info-grid">
<?php if($organization->bank_name): ?>
                <div class="org-info-item org-info-item-full">
                    <label>Наименование банка</label>
                    <p><?php echo e($organization->bank_name); ?></p>
                </div>
<?php endif; ?>

<?php if($organization->bank_bik): ?>
                <div class="org-info-item">
                    <label>БИК</label>
                    <p><?php echo e($organization->bank_bik); ?></p>
                </div>
<?php endif; ?>

<?php if($organization->bank_account): ?>
                <div class="org-info-item">
                    <label>Расчетный счет</label>
                    <p><?php echo e($organization->bank_account); ?></p>
                </div>
<?php endif; ?>

<?php if($organization->bank_corr_account): ?>
                <div class="org-info-item">
                    <label>Корреспондентский счет</label>
                    <p><?php echo e($organization->bank_corr_account); ?></p>
                </div>
<?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="section">
    <div class="profile-card">
        <div class="profile-card-header">
            <h3>Статус организации</h3>
        </div>
        <div class="profile-card-body">
            <div class="org-status">
<?php if($organization->isActive()): ?>
                <span class="badge badge-blue">Активна</span>
<?php else: ?>
                <span class="badge badge-red"><?php echo e(ucfirst($organization->status)); ?></span>
<?php endif; ?>
                <p style="margin-top: 12px; font-size: 14px; color: var(--muted-fg);">
                    Данные обновлены: <?php echo e($organization->updated_at->format('d.m.Y H:i')); ?>

                </p>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
<style>
.org-info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 24px;
}

.org-info-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.org-info-item-full {
    grid-column: 1 / -1;
}

.org-info-item label {
    font-size: 13px;
    font-weight: 500;
    color: var(--muted-fg);
}

.org-info-item p {
    font-size: 15px;
    font-weight: 500;
    color: var(--fg);
}

.org-status {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

@media (max-width: 768px) {
    .org-info-grid {
        grid-template-columns: 1fr;
    }
    
    .org-info-item-full {
        grid-column: auto;
    }
}
</style>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('wholesale.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\OS\home\sfera\resources\views/wholesale/organization.blade.php ENDPATH**/ ?>