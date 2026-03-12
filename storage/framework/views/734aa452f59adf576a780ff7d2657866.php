<?php $__env->startSection('title', 'Регистрация'); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('assets/sfera/css/register.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<main class="register-page">
    <div class="register-container">
        <div class="register-content">
            
            <div class="register-form-section">
                <div class="form-header">
                    <h1>Регистрация</h1>
                    <p>Создайте аккаунт и начните покупки</p>
                </div>

                
                <div class="social-login">
                    <button type="button" class="social-btn google-btn">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M18.2 10.2c0-.6-.1-1.2-.2-1.8H10v3.4h4.6c-.2 1-.8 1.9-1.7 2.5v2.2h2.7c1.6-1.5 2.6-3.7 2.6-6.3z" fill="#4285F4"/>
                            <path d="M10 19c2.4 0 4.5-.8 6-2.2l-2.7-2.2c-.8.5-1.8.8-3.3.8-2.5 0-4.7-1.7-5.4-4H1.8v2.3C3.3 16.8 6.5 19 10 19z" fill="#34A853"/>
                            <path d="M4.6 11.4c-.2-.5-.3-1.1-.3-1.7s.1-1.2.3-1.7V5.7H1.8C1.1 7.1.7 8.5.7 10s.4 2.9 1.1 4.3l2.8-2.9z" fill="#FBBC05"/>
                            <path d="M10 4c1.4 0 2.7.5 3.7 1.4l2.8-2.8C14.5.9 12.4 0 10 0 6.5 0 3.3 2.2 1.8 5.7l2.8 2.2c.7-2.3 2.9-4 5.4-4z" fill="#EA4335"/>
                        </svg>
                        Регистрация через Google
                    </button>
                    <button type="button" class="social-btn vk-btn">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M10 0C4.48 0 0 4.48 0 10s4.48 10 10 10 10-4.48 10-10S15.52 0 10 0zm4.5 11.5c.5.5 1 1 1.5 1.4.2.2.4.4.5.7.2.3 0 .7-.3.7l-1.8.1c-.5 0-1-.2-1.4-.6-.3-.3-.6-.6-.9-.9-.1-.1-.3-.3-.5-.3-.3-.1-.6 0-.7.3-.1.3-.1.6-.2.9 0 .4-.2.5-.6.5-1 .1-1.9-.1-2.8-.6-.7-.4-1.3-1-1.8-1.6-.9-1.1-1.7-2.3-2.3-3.6-.2-.4-.1-.6.3-.6h1.8c.3 0 .5.2.6.4.4.9.8 1.7 1.4 2.5.2.2.3.5.6.6.3.1.5 0 .6-.3.1-.2.1-.4.1-.6.1-.6.1-1.2 0-1.8-.1-.4-.3-.6-.7-.7-.2 0-.2-.1-.1-.2.2-.2.4-.3.7-.3h2.6c.4.1.5.3.5.7v2.4c0 .2.1.8.4.9.2.1.4 0 .6-.2.6-.6 1-1.3 1.4-2 .2-.3.3-.7.5-1 .1-.2.3-.4.6-.4h2c.1 0 .2 0 .3.1.4.1.5.3.4.6-.2.6-.6 1.1-.9 1.6-.4.5-.8 1-1.1 1.5-.3.4-.3.6 0 1z" fill="#0077FF"/>
                        </svg>
                        Регистрация через VK
                    </button>
                    <button type="button" class="social-btn telegram-btn">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M10 0C4.48 0 0 4.48 0 10s4.48 10 10 10 10-4.48 10-10S15.52 0 10 0zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69.01-.03.01-.14-.07-.2s-.16-.04-.23-.02c-.09.02-1.63 1.03-4.61 3.03-.44.3-.83.45-1.18.44-.39-.01-1.13-.22-1.69-.4-.68-.23-1.22-.35-1.17-.74.02-.2.29-.41.78-.62 3.04-1.33 5.08-2.21 6.11-2.64 2.91-1.21 3.51-1.42 3.91-1.43.09 0 .28.02.41.13.1.09.13.21.14.3.01.06.02.21.01.32z" fill="#0088CC"/>
                        </svg>
                        Регистрация через Telegram
                    </button>
                </div>

                <div class="divider">
                    <span>или</span>
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

                
                <form class="register-form" method="POST" action="<?php echo e(route('register')); ?>" id="registerForm">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="user_type" id="user_type" value="retail">

                    <div class="form-group
<?php $__errorArgs = ['name'];
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
                        <label for="name">Имя</label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            value="<?php echo e(old('name')); ?>" 
                            required 
                            autofocus
                            placeholder="Введите ваше имя"
                        >
<?php $__errorArgs = ['name'];
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
<?php $__errorArgs = ['phone'];
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
                        <label for="phone">Телефон</label>
                        <input 
                            type="tel" 
                            id="phone" 
                            name="phone" 
                            value="<?php echo e(old('phone')); ?>" 
                            required
                            placeholder="+7 (___) ___-__-__"
                        >
<?php $__errorArgs = ['phone'];
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
                            value="<?php echo e(old('email')); ?>" 
                            required
                            placeholder="example@mail.ru"
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
                        <div class="password-input">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required
                                placeholder="Придумайте пароль"
                            >
                            <button type="button" class="toggle-password" data-target="password">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" class="eye-icon">
                                    <path d="M10 4C5 4 1.73 7.11 1 10c.73 2.89 4 6 9 6s8.27-3.11 9-6c-.73-2.89-4-6-9-6zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5z" fill="currentColor"/>
                                </svg>
                            </button>
                        </div>
                        <div class="password-strength">
                            <div class="strength-bar">
                                <div class="strength-fill" id="strengthFill"></div>
                            </div>
                            <span class="strength-text" id="strengthText"></span>
                        </div>
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

                    <div class="form-group
<?php $__errorArgs = ['password_confirmation'];
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
                        <label for="password_confirmation">Подтверждение пароля</label>
                        <div class="password-input">
                            <input 
                                type="password" 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                required
                                placeholder="Повторите пароль"
                            >
                            <button type="button" class="toggle-password" data-target="password_confirmation">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" class="eye-icon">
                                    <path d="M10 4C5 4 1.73 7.11 1 10c.73 2.89 4 6 9 6s8.27-3.11 9-6c-.73-2.89-4-6-9-6zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5z" fill="currentColor"/>
                                </svg>
                            </button>
                        </div>
<?php $__errorArgs = ['password_confirmation'];
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

                    
                    <div class="form-group checkbox-group org-registration-checkbox">
                        <label class="checkbox-label">
                            <input type="checkbox" id="is_organization" name="is_organization">
                            <span>Регистрация организации</span>
                        </label>
                    </div>

                    
                    <div class="organization-fields" id="organization-fields">
                        <h3>Данные организации</h3>
                        
                        <div class="info-block">
                            <div class="info-block-title">Проверка ИНН</div>
                            <div class="info-block-text">
                                Введите ИНН организации и нажмите "Проверить ИНН". 
                                Данные организации будут автоматически загружены из базы данных ФНС.
                            </div>
                        </div>

                        <div class="inn-check-group">
                            <div class="form-group
<?php $__errorArgs = ['inn'];
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
                                <label for="inn">ИНН организации</label>
                                <input 
                                    type="text" 
                                    id="inn" 
                                    name="inn" 
                                    value="<?php echo e(old('inn')); ?>"
                                    placeholder="1234567890"
                                    maxlength="12"
                                >
<?php $__errorArgs = ['inn'];
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
                            <button type="button" id="check-inn-btn">Проверить ИНН</button>
                        </div>

                        <div class="form-group
<?php $__errorArgs = ['org_name_full'];
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
                            <label for="org_name_full">Полное наименование организации</label>
                            <input 
                                type="text" 
                                id="org_name_full" 
                                name="org_name_full" 
                                value="<?php echo e(old('org_name_full')); ?>"
                                placeholder="Будет заполнено автоматически"
                            >
<?php $__errorArgs = ['org_name_full'];
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

                        <div class="form-group">
                            <label for="org_name_short">Краткое наименование</label>
                            <input 
                                type="text" 
                                id="org_name_short" 
                                name="org_name_short" 
                                value="<?php echo e(old('org_name_short')); ?>"
                                placeholder="Будет заполнено автоматически"
                            >
                        </div>

                        <div class="form-group">
                            <label for="org_kpp">КПП</label>
                            <input 
                                type="text" 
                                id="org_kpp" 
                                name="org_kpp" 
                                value="<?php echo e(old('org_kpp')); ?>"
                                placeholder="Будет заполнено автоматически"
                            >
                        </div>

                        <div class="form-group">
                            <label for="org_ogrn">ОГРН</label>
                            <input 
                                type="text" 
                                id="org_ogrn" 
                                name="org_ogrn" 
                                value="<?php echo e(old('org_ogrn')); ?>"
                                placeholder="Будет заполнено автоматически"
                            >
                        </div>

                        <div class="form-group">
                            <label for="org_legal_address">Юридический адрес</label>
                            <textarea 
                                id="org_legal_address" 
                                name="org_legal_address" 
                                rows="2"
                                placeholder="Будет заполнено автоматически"
                            ><?php echo e(old('org_legal_address')); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="org_director_name">ФИО руководителя</label>
                            <input 
                                type="text" 
                                id="org_director_name" 
                                name="org_director_name" 
                                value="<?php echo e(old('org_director_name')); ?>"
                                placeholder="Будет заполнено автоматически"
                            >
                        </div>

                        <div class="form-group">
                            <label for="org_director_position">Должность руководителя</label>
                            <input 
                                type="text" 
                                id="org_director_position" 
                                name="org_director_position" 
                                value="<?php echo e(old('org_director_position')); ?>"
                                placeholder="Будет заполнено автоматически"
                            >
                        </div>

                        <div class="form-group">
                            <label for="org_opf">Организационно-правовая форма</label>
                            <input 
                                type="text" 
                                id="org_opf" 
                                name="org_opf" 
                                value="<?php echo e(old('org_opf')); ?>"
                                placeholder="Будет заполнено автоматически"
                                readonly
                            >
                        </div>

                        <input type="hidden" id="org_status" name="org_status" value="">
                        <input type="hidden" id="dadata_json" name="dadata_json" value="">
                    </div>

                    <div class="form-group checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" id="terms" name="terms" required>
                            <span>Я принимаю <a href="#" class="link">условия пользовательского соглашения</a> и даю согласие на обработку моих <a href="#" class="link">персональных данных</a></span>
                        </label>
                    </div>

                    <div class="form-group checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" id="newsletter" name="newsletter">
                            <span>Хочу получать информацию о скидках и акциях</span>
                        </label>
                    </div>

                    <button type="submit" class="btn-submit">Зарегистрироваться</button>

                    <p class="form-footer">
                        Уже есть аккаунт? <a href="<?php echo e(route('login')); ?>" class="link">Войти</a>
                    </p>
                </form>
            </div>

            
            <div class="register-benefits">
                <h2>Преимущества покупок в издательстве</h2>

                <div class="benefit-item">
                    <div class="benefit-icon">
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none">
                            <circle cx="20" cy="20" r="18" fill="#E6F0FF"/>
                            <path d="M12 20l6 6 10-12" stroke="#005BFF" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="benefit-content">
                        <h3>Быстрая доставка</h3>
                        <p>Доставим ваш заказ завтра или в удобное для вас время</p>
                    </div>
                </div>

                <div class="benefit-item">
                    <div class="benefit-icon">
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none">
                            <circle cx="20" cy="20" r="18" fill="#E6F0FF"/>
                            <path d="M20 12v8l6 3" stroke="#005BFF" stroke-width="2.5" stroke-linecap="round"/>
                            <circle cx="20" cy="20" r="8" stroke="#005BFF" stroke-width="2.5"/>
                        </svg>
                    </div>
                    <div class="benefit-content">
                        <h3>Гарантия качества</h3>
                        <p>Проверяем каждый товар перед отправкой покупателю</p>
                    </div>
                </div>

                <div class="benefit-item">
                    <div class="benefit-icon">
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none">
                            <circle cx="20" cy="20" r="18" fill="#E6F0FF"/>
                            <path d="M16 18l4 4 8-8" stroke="#005BFF" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 20v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8" stroke="#005BFF" stroke-width="2.5" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div class="benefit-content">
                        <h3>Простой возврат</h3>
                        <p>30 дней на возврат товара без объяснения причин</p>
                    </div>
                </div>

                <div class="benefit-item">
                    <div class="benefit-icon">
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none">
                            <circle cx="20" cy="20" r="18" fill="#E6F0FF"/>
                            <path d="M20 10v10l6 3" stroke="#005BFF" stroke-width="2.5" stroke-linecap="round"/>
                            <path d="M26 16c1.5 1.5 2 4 2 6 0 4.5-3.5 8-8 8s-8-3.5-8-8c0-2 .5-4.5 2-6" stroke="#005BFF" stroke-width="2.5" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div class="benefit-content">
                        <h3>Выгодные цены</h3>
                        <p>Регулярные акции и скидки до 30% на популярные товары</p>
                    </div>
                </div>

                <div class="benefit-item">
                    <div class="benefit-icon">
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none">
                            <circle cx="20" cy="20" r="18" fill="#E6F0FF"/>
                            <path d="M15 25c0 3 2 5 5 5s5-2 5-5" stroke="#005BFF" stroke-width="2.5" stroke-linecap="round"/>
                            <circle cx="16" cy="18" r="1.5" fill="#005BFF"/>
                            <circle cx="24" cy="18" r="1.5" fill="#005BFF"/>
                            <circle cx="20" cy="20" r="8" stroke="#005BFF" stroke-width="2.5"/>
                        </svg>
                    </div>
                    <div class="benefit-content">
                        <h3>Поддержка</h3>
                        <p>Наша команда всегда готова помочь с любым вопросом</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('assets/sfera/js/auth-register.js')); ?>"></script>
<script src="<?php echo e(asset('assets/sfera/js/dadata-inn-check.js')); ?>"></script>
<script src="<?php echo e(asset('assets/sfera/js/auth-register-new.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\OS\home\sfera\resources\views/auth/register.blade.php ENDPATH**/ ?>