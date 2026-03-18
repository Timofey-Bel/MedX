<?php /* Smarty version 2.6.11, created on 2026-02-15 21:03:53
         compiled from medx/main_settings/design_settings.tpl */ ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedX учись практикуй расти</title>
    <link rel="stylesheet" href="/assets/medx/main_styles.css">
    <link rel="stylesheet" href="/assets/medx/css/main_header.css">
</head>
<style>
    .page-header {
        text-align: center;
        /*margin: var(--space-lg) auto var(--space-xl) auto;*/
        position: relative;
        padding: 12px;
        background-color: var(--white);
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        max-width: 1280px;
        height: 167px;
        margin: 40px;
    }

    .page-title {
        font-family: 'Onest', sans-serif;
        font-size: 60px;
        font-weight: 700;
        line-height: 1.2;
        color: var(--text-dark);
        text-transform: uppercase;
        margin-bottom: var(--space-sm);
        letter-spacing: -0.02em;
        position: relative;
        z-index: 2;
    }

    .page-info {
        display: flex;
        justify-content: center;
        font-family: "Involve-Medium", Helvetica;
        font-size: 20px;
        gap: 10px;
        z-index: 10000;
    }

    .page-login {
        font-family: "Involve-Medium", Helvetica;
        font-size: 12px;
        z-index: 1;

    }

    .page-subtitle {
        font-family: 'Onest', sans-serif;
        font-size: 44px;
        font-weight: 700;
        line-height: 1.2;
        background: linear-gradient(90deg, var(--primary-blue) 0%, var(--dark-blue) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-transform: uppercase;
        letter-spacing: -0.02em;
        position: relative;
        z-index: 2;
    }

    .page-header-bg-1 {
        position: absolute;
        width: 560px;
        height: 167px;
        left: 0;
        top: 0;
        background-image: url('/assets/medx/img/main/settings/page-header-bg-1.png');
        background-repeat: no-repeat;
        z-index: 0;
        pointer-events: none;

    }

    .page-header-bg-2 {
        position: absolute;
        width: 860px;
        height: 167px;
        left: 430px;
        top: 0;
        background-image: url('/assets/medx/img/main/settings/page-header-bg-2.png');
        background-repeat: no-repeat;
        z-index: 0;
        pointer-events: none;

    }

    /* главная карточка настроек */
    .settings-card {
        max-width: 1040px;
        width: 100%;
        background: #ffffff;
        border-radius: 32px;
        box-shadow: 0 20px 35px -8px rgba(0, 20, 30, 0.15), 0 8px 12px -4px rgba(0,0,0,0.05);
        padding: 2rem 2.25rem;
        transition: all 0.2s ease;
    }

    /* заголовок страницы */
    .settings-title {
        font-size: 2rem;
        font-weight: 600;
        letter-spacing: -0.02em;
        color: #28282F;
        margin: 27px 0;
    }

    /* навигация (лента меню) */
    .breadcrumbs {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem 1.75rem;
        margin-bottom: 2.5rem;
        /*padding: 0.25rem 0;*/
        border-bottom: 2px solid rgba(0, 124, 137, 0.2);
    }

    .breadcrumbs a {
        text-decoration: none;
        font-weight: 500;
        font-size: 12px;
        font-family: "Onest", Helvetica;
        color: rgba(0, 124, 137, 0.4);;
        /*padding: 0.5rem 0;*/
        transition: color 0.2s, box-shadow 0.2s;
        letter-spacing: 0.01em;
        border-bottom: 2px solid transparent;
        /*margin-bottom: -1px;*/
    }

    .breadcrumbs .active {
        font-size: 14px;
        color: #28282F;
        border-bottom: 2px solid #28282F;
    }

    /* активный пункт — для примера выделим "МОЙ ПРОФИЛЬ" (первый) */
    /*.breadcrumbs a:first-child {*/
    /*    color: #0b1e33;*/
    /*    !*font-weight: 600;*!*/
    /*    font-size: 14px;*/
    /*    border-bottom-color: #2b3f5c;*/
    /*}*/

    section {
        margin: 0 40px 40px;
        position: relative;
    }

    /* секции */
    .section-main-information, .section-login-information {
        position: relative;
        padding: 28px;
        background-color: var(--white);
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        max-width: 1280px;
        height: 267px;
    }

    .section-header {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1e2c41;
        margin-bottom: 1.25rem;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }
    .section-header::before {
        content: "";
        position: absolute;
        top: 75px;
        left: 50%;
        transform: translateX(-50%);
        width: 1224px;
        height: 1px;
        background-color: rgba(0, 124, 137, 0.2);
        /*margin: 0 40px 0 0;*/
    }

    /* табличная сетка для информации */
    .info-grid {
        /*background: #f9fcff;*/
        /*border-radius: 20px;*/
        /*border: 1px solid #e6edf5;*/
        overflow: hidden;
        display: flex;
        gap: 40px;
        margin: 35px 0 40px 0;
    }

    .form-section {
        display: inline-grid;
        /*grid-template-columns: repeat(5, 1fr);  !* 5 колонок *!*/
    }

    .form-section input, select {
        border-radius: 8px;
        border: 1px solid rgb(0, 124, 137, 0.2);
        height: 40px;
        width: auto;
        padding: 9.5px 16px;
        color: #28282F;
        font-family: "Involve-Medium", Helvetica;
        font-size: 16px;

    }

    .form-section label, option {
        font-size: 12px;
        font-family: "Involve-Bold", Helvetica;
    }

    #birthdate {

    }
    #birthdate::-webkit-calendar-picker-indicator {
        background-image: url("/assets/medx/img/main/settings/calendar-icon.svg");
        /*border-radius: 50%;*/
        padding: 4px;
        cursor: pointer;
        /*filter: invert(1);*/
    }


    /* особое оформление для звездочек пароля */
    .password-mask {
        font-family: 'Courier New', monospace;
        letter-spacing: 2px;
        font-weight: 600;
        color: #28282F;
    }

    /* группа кнопок */
    .button-group {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-top: 0.25rem;
        margin-bottom: 0.5rem;
    }

    .btn {
        border: none;
        background: transparent;
        font-weight: 550;
        font-size: 0.95rem;
        padding: 0.6rem 1.8rem;
        border-radius: 6px;
        cursor: pointer;
        transition: background 0.15s, box-shadow 0.15s, transform 0.1s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid transparent;
    }

    .btn-primary {
        background: #087e90;
        color: white;
        /*box-shadow: 0 4px 8px rgba(20, 55, 100, 0.2);*/
        width: 160px;
        height: 36px;
    }
    .btn-primary:hover {
        background: #007C89;
        /*box-shadow: 0 8px 14px rgba(20, 55, 100, 0.25);*/
    }

    .btn-secondary {
        background: #CCE5E7;
        color: #087e90;
        width: 140px;
        height: 36px;
    }
    .btn-secondary:hover {
        /*background: #f1f6fc;*/
        border-color: #087e90;
    }


    /* дополнительная адаптация для мобильных */
    @media (max-width: 700px) {
        .settings-card {
            padding: 1.5rem;
        }
        .grid-row {
            grid-template-columns: 1fr;  /* вертикально на маленьких экранах */
            border-bottom: none;
            gap: 1px;
        }
        .grid-row--two {
            grid-template-columns: 1fr;
        }
        .grid-cell {
            border-right: none !important;
            border-bottom: 1px solid #e0e9f2;
        }
        .grid-row .grid-cell:last-child {
            border-bottom: none;
        }
        .grid-cell.label {
            background: #eaf1fa;
        }
        .button-group {
            justify-content: flex-start;
        }
    }

    /* маленькие детали: вольное указание "Имя Фамилия" в данных */
    .value.placeholder {
        color: #4b5e77;
    }


</style>
<body>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "medx/tpl/main_header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "medx/tpl/main_mobile_menu.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<!-- Затемняющий фон -->

<div class="overlay" id="overlay"></div>
<!-- Модальное окно (скрыто по умолчанию) -->
<div class="oops-modal" id="oopsModal">
    <div class="oops-modal-overlay"></div>
    <div class="oops-modal-content">
        <!-- Кнопка закрытия -->
        <button class="oops-modal-close" id="closeOopsModal">
            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_4495_2345)">
                    <path d="M10.9999 10.9999L6 6M6 6L1 1M6 6L11 1M6 6L1 11" stroke="white" stroke-width="2"
                          stroke-linecap="round" stroke-linejoin="round"/>
                </g>
                <defs>
                    <clipPath id="clip0_4495_2345">
                        <rect width="12" height="12" fill="white"/>
                    </clipPath>
                </defs>
            </svg>
        </button>

        <!-- Контент -->
        <div class="oops-modal-body">
            <div class="oops-decoration-line"></div>
            <h3 class="oops-modal-title">Упс!</h3>
            <p class="oops-modal-subtitle">
                Эта функция доступна только для зарегестрированных пользователей.
            </p>
            <div class="oops-button">
                <a href="/register/" class="register-btn">
                    ЗАРЕГЕСТРИРОВАТЬСЯ
                </a>
            </div>
        </div>
    </div>
</div>


<main>
    <section>
        <h2 class="section-header">Выбор темы</h2>
        <div class="topics">
            <div class="topic">
                <img class="light" alt="light_topic">
                <input type="checkbox">
            </div>
            <div class="topic">
                <img class="dark" alt="dark_topic">
                <input type="checkbox">
            </div>
            <div class="topic">
                <img class="auto" alt="auto_topic">
                <input type="checkbox">
            </div>
        </div>
    </section>


</main>
<script>
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggleBtn');
    const overlay = document.getElementById('overlay');

    function openSidebar() {
        sidebar.classList.add('expanded');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden'; // Блокируем скролл
    }

    function closeSidebar() {
        sidebar.classList.remove('expanded');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    toggleBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        if (sidebar.classList.contains('expanded')) {
            closeSidebar();
        } else {
            openSidebar();
        }
    });

    // Закрытие кликом на оверлей или вне сайдбара
    overlay.addEventListener('click', closeSidebar);
    document.addEventListener('click', (e) => {
        if (!sidebar.contains(e.target) && sidebar.classList.contains('expanded')) {
            closeSidebar();
        }
    });

    // Закрытие на ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && sidebar.classList.contains('expanded')) {
            closeSidebar();
        }
    });


    // Сердечко
    const favoriteBtn = document.getElementById('favorite-btn');
    if (favoriteBtn) {
        favoriteBtn.addEventListener('click', function () {
            const heart = document.getElementById('heartIcon');
            heart.classList.toggle('active');
        });
    }

    // Меню навигация
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => {
        item.addEventListener('click', function () {
            navItems.forEach(nav => nav.classList.remove('active'));
            this.classList.add('active');
        });
    });


    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('oopsModal');
        const closeBtn = document.getElementById('closeOopsModal');

        // Функция показа модалки
        window.showOopsModal = function () {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        };

        // Функция закрытия
        function closeModal() {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }

        // События закрытия
        closeBtn.addEventListener('click', closeModal);
        modal.querySelector('.oops-modal-overlay').addEventListener('click', closeModal);

        // ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modal.classList.contains('active')) {
                closeModal();
            }
        });
    });

</script>
</body>
</html>