<?php /* Smarty version 2.6.11, created on 2026-02-13 19:17:48
         compiled from medx/register/register.tpl */ ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedX — Регистрация в личный кабинет</title>
    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "medx/tpl/favicon.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
    <link rel="stylesheet" href="/assets/medx/styles.css">
    <link rel="stylesheet" href="/assets/medx/css/header.css">
    <link rel="stylesheet" href="/assets/medx/css/register.css">
</head>
<body>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "medx/tpl/header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<main class="main">
    <section class="login-mainblock">
        <div class="features-grid">
            <div class="left-column">
                <div class="feature-card card-1">
                    <div class="card-title">100+ авторских материалов</div>
                    <div class="bg-img"></div>
                </div>
                <div class="feature-card card-2">
                    <div class="card-title">попробуй MedX бесплатно</div>
                </div>
                <div class="feature-card card-3">
                    <div class="card-title">3000+ клинических задач и тестов</div>
                </div>
                <div class="feature-card card-4">
                    <div class="card-title">дежурства и волонтёрство</div>
                </div>
                <div class="feature-card card-5">
                    <div class="card-title">Персональные рекомендации (AI)</div>
                </div>
            </div>

            <div class="mid-column">
                <div class="feature-card card-6">
                    <div class="card-title">карточки для повторения</div>
                </div>
            </div>

            <div class="right-column">
                <div class="feature-card card-7">
                    <div class="card-title">Видео-лекции</div>
                </div>
                <div class="feature-card card-8">
                    <div class="card-title">сообще- ства и чаты</div>
                </div>
                <div class="feature-card card-9">
                    <div class="card-title">подкасты</div>
                    <div class="bg-img-1"></div>
                    <div class="bg-img-2"></div>
                </div>
                <div class="feature-card card-10">
                    <div class="card-title">прогресс и рейтинг</div>
                    <div class="bg-img"></div>
                </div>
            </div>
        </div>
        <div class="login">
            <div class="login__card">
                <h1 class="login__title">Личный кабинет</h1>
                <p class="login__subtitle">Войди в личный кабинет удобным способом</p>

                <form class="login__form" action="#" method="post">
                    <label class="login__field">
                        <span class="login__field-label">Логин</span>
                        <input
                                class="login__input"
                                type="email"
                                name="email"
                                placeholder="Введите логин"
                                required
                        />
                    </label>

                    <label class="login__field">
                        <span class="login__field-label">Пароль</span>
                        <input
                                class="login__input"
                                type="password"
                                name="password"
                                placeholder="Введите пароль"
                                required
                        />
                        <button type="button" class="password-toggle" aria-label="Показать пароль">
                            <img src="/site/modules/medx/showcase/img/eye.png" alt="eye" class="password-toggle__icon">
                        </button>
                    </label>

                    <div class="login__divider">
                        <span class="login__divider-line"></span>
                        <span class="login__divider-text">или</span>
                        <span class="login__divider-line"></span>
                    </div>

                    <p class="login__subtitle">Войдите через удобный сервис</p>

                    <div class="login__alternative">
                        <div class="login__alternative-icons">
                            <a href="#" class="login__alternative-icon">
                                <img src="/site/modules/medx/showcase/img/vk.svg" alt="VK"/>
                            </a>
                            <a href="#" class="login__alternative-icon">
                                <img src="/site/modules/medx/showcase/img/google.svg" alt="Google"/>
                            </a>
                            <a href="#" class="login__alternative-icon">
                                <img src="/site/modules/medx/showcase/img/telegram.svg" alt="Telegram"/>
                            </a>
                        </div>
                    </div>
                    <div class="checkbox-container">
                        <div class="custom-checkbox" id="policyCheckbox"></div>
                        <span class="checkbox-text">Нажимая «Зарегестрироваться», я соглашаюсь с
                        <a href="#" class="policy-link">Политикой конфиденциальности</a>.
                    </span>
                    </div>
                    <button class="login__submit" type="submit">
                        <span class="login__submit-text" id="registerBtn">Зарегестрироваться</span>
                    </button>
                </form>

                <p class="login__register">
                    Уже есть аккаунт?
                    <a href="/login/" class="login__register-link">Войти</a>
                </p>
            </div>
        </div>
    </section>
</main>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "medx/tpl/footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<script src="/assets/medx/script.js"></script>
<script src="/assets/medx/js/register.js"></script>
<script>
    const checkbox = document.getElementById('policyCheckbox');
    const registerBtn = document.getElementById('registerBtn');
    const errorMessage = document.getElementById('errorMessage');

    // Обработчик клика по чекбоксу
    checkbox.addEventListener('click', function() {
        this.classList.toggle('checked');
        // Убираем ошибку при клике на чекбокс
        this.classList.remove('error');
        errorMessage.classList.remove('show');
    });

    // Обработчик клика по тексту чекбокса
    document.querySelector('.checkbox-text').addEventListener('click', function() {
        checkbox.click();
    });

    // Обработчик для кнопки
    registerBtn.addEventListener('click', function() {
        // Проверяем, отмечен ли чекбокс
        if (!checkbox.classList.contains('checked')) {
            // Если не отмечен, показываем ошибку
            checkbox.classList.add('error');
            errorMessage.classList.add('show');

            // Анимация ошибки
            checkbox.style.animation = 'none';
            setTimeout(() => {
                checkbox.style.animation = 'pulse 0.5s ease-in-out';
            }, 10);

            // Фокусируем на чекбоксе для удобства
            checkbox.focus();
            return false; // Прекращаем выполнение
        }

        // Если чекбокс отмечен, продолжаем регистрацию
        alert('Регистрация выполнена!');
        // Здесь можно добавить логику регистрации

        // Сбрасываем состояние после успешной регистрации
        checkbox.classList.remove('checked', 'error');
        errorMessage.classList.remove('show');
    });

    // Дополнительно: убираем ошибку при клике на саму ошибку
    errorMessage.addEventListener('click', function() {
        checkbox.click();
    });
</script>
</body>
</html>