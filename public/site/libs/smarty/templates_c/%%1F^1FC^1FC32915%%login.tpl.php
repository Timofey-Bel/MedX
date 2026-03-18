<?php /* Smarty version 2.6.11, created on 2026-02-24 16:23:51
         compiled from medx/login/login.tpl */ ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>MedX — Вход в личный кабинет</title>
    <link rel="stylesheet" href="/assets/medx/styles.css">
    <link rel="stylesheet" href="/assets/medx/css/login.css">
    <link rel="stylesheet" href="/assets/medx/css/header.css">
</head>
<body>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "medx/tpl/header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<main class="main">
    <section class="login">
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

                <button class="login__submit" type="submit">
                    <span class="login__submit-text">войти</span>
                </button>
            </form>

            <p class="login__register">
                Ещё нет аккаунта?
                <a href="/register/" class="login__register-link">Зарегестрироваться</a>
            </p>
        </div>
    </section>
</main>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "medx/tpl/footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

</body>
</html>
