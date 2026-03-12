<?php $__env->startSection('title', 'Вход или регистрация'); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('assets/sfera/css/login.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<main class="login-page">
    <div class="container">
        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <h1>Вход или регистрация</h1>
                    <p>Введите email для входа в систему</p>
                </div>

<?php if($errors->any()): ?>
                <div class="alert-error">
<?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo e($error); ?>

<?php if(!$loop->last): ?>
<br>
<?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
<?php endif; ?>

                <form class="login-form" method="POST" action="<?php echo e(route('login')); ?>">
                    <?php echo csrf_field(); ?>

                    <div class="form-group
<?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
error
<?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
">
                        <label for="email">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-input"
                            placeholder="example@mail.ru"
                            value="<?php echo e(old('email')); ?>"
                            required
                            autofocus
                        >
<?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="error-message"><?php echo e($message); ?></span>
<?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="form-group
<?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
error
<?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
">
                        <label for="password">Пароль</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input"
                            placeholder="Введите пароль"
                            required
                        >
<?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="error-message"><?php echo e($message); ?></span>
<?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <button type="submit" class="btn btn-primary btn-full">
                        Войти
                    </button>

                    <div class="login-divider">
                        <span>или войдите через</span>
                    </div>

                    <div class="social-login">
                        <button type="button" class="social-btn">
                            <svg width="24" height="24" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            <span>Google</span>
                        </button>

                        <button type="button" class="social-btn">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="#0077FF">
                                <path d="M15.07 2H8.93C3.33 2 2 3.33 2 8.93v6.14C2 20.67 3.33 22 8.93 22h6.14c5.6 0 6.93-1.33 6.93-6.93V8.93C22 3.33 20.67 2 15.07 2zm-3.16 15.81c-1.63 0-3.06-.53-4.03-1.39-.13-.12-.14-.32-.03-.45l.86-.99c.12-.14.33-.16.47-.04.68.58 1.55.94 2.63.94 1.43 0 2.45-.87 2.45-2.09 0-1.15-.62-1.79-2.31-2.25-2.08-.57-3.37-1.38-3.37-3.44 0-1.85 1.45-3.11 3.51-3.11 1.41 0 2.64.43 3.47 1.14.13.11.14.31.02.44l-.79.96c-.12.15-.33.17-.48.06-.62-.47-1.43-.73-2.32-.73-1.35 0-2.25.77-2.25 1.93 0 1.11.71 1.68 2.45 2.17 2.08.58 3.24 1.43 3.24 3.5 0 2.01-1.56 3.35-3.77 3.35z"/>
                            </svg>
                            <span>Сбер ID</span>
                        </button>

                        <button type="button" class="social-btn">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="#0088CC">
                                <path d="M12 2C6.48 2 2 6.48 2 12c0 5.52 4.48 10 10 10s10-4.48 10-10c0-5.52-4.48-10-10-10zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69.01-.03.01-.14-.07-.2-.08-.06-.19-.04-.27-.02-.12.02-1.96 1.25-5.54 3.67-.52.36-.99.53-1.42.52-.47-.01-1.37-.27-2.03-.49-.82-.27-1.47-.42-1.42-.88.03-.24.37-.49 1.02-.74 4-1.74 6.68-2.88 8.03-3.44 3.82-1.59 4.61-1.87 5.13-1.87.11 0 .37.03.53.17.14.11.18.26.2.37.02.08.03.32.01.5z"/>
                            </svg>
                            <span>Telegram</span>
                        </button>
                    </div>

                    <div class="login-footer">
                        <p class="terms">
                            Продолжая, вы соглашаетесь с <a href="#">условиями программы лояльности</a>
                            и <a href="#">политикой обработки персональных данных</a>
                        </p>
                    </div>
                </form>
            </div>

            <div class="login-benefits">
                <h3>Преимущества покупок в издательстве</h3>
                <ul class="benefits-list">
                    <li>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="#fff">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01" stroke="#fff" stroke-width="2" fill="none"/>
                        </svg>
                        <div>
                            <strong>Быстрая доставка</strong>
                            <p>Доставим за 1-3 дня в любой город России</p>
                        </div>
                    </li>
                    <li>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="#fff">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01" stroke="#fff" stroke-width="2" fill="none"/>
                        </svg>
                        <div>
                            <strong>Тысячи товаров</strong>
                            <p>Широкий ассортимент</p>
                        </div>
                    </li>
                    <li>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="#fff">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01" stroke="#fff" stroke-width="2" fill="none"/>
                        </svg>
                        <div>
                            <strong>Бонусная программа</strong>
                            <p>Копите баллы и оплачивайте ими покупки</p>
                        </div>
                    </li>
                    <li>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="#fff">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01" stroke="#fff" stroke-width="2" fill="none"/>
                        </svg>
                        <div>
                            <strong>Надежная защита</strong>
                            <p>Гарантия качества и защита покупателя</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</main>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('assets/sfera/js/auth-login.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\OS\home\sfera\resources\views/auth/login.blade.php ENDPATH**/ ?>