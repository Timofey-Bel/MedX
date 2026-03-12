<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedX Настройки профиля</title>
    <link rel="stylesheet" href="/assets/medx/main_styles.css">
    <link rel="stylesheet" href="/assets/medx/css/main_header.css">
    <link rel="stylesheet" href="/assets/medx/css/main_settings.css">
</head>
<style>
    .faq-section {
        margin-bottom: var(--space-xl);
        padding: 28px;
        background-color: var(--white);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }

    .section-title {
        font-size: 20px;
        font-weight: 600;
        background: linear-gradient(90deg, var(--primary-blue) 0%, var(--dark-blue) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-transform: uppercase;
        letter-spacing: -0.02em;
        margin-bottom: var(--space-md);
        /*margin-left: 20px;*/
        margin-top: 24px;
    }

    /*.accordion {*/
    /*    background-color: var(--white);*/
    /*    border-radius: var(--radius-lg);*/
    /*    box-shadow: var(--shadow-sm);*/
    /*    overflow: hidden;*/
    /*}*/

    .accordion-item {
        border-bottom: 2px solid var(--dark-blue-20);
        /*margin: 0 20px;*/
    }

    .accordion-item:last-child {
        border-bottom: none;
    }

    .accordion-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: var(--space-md) 0;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .accordion-header:hover {
        background-color: rgba(237, 244, 244, 0.5);
    }

    .accordion-question {
        font-size: 20px;
        font-weight: 600;
        text-transform: uppercase;
        color: var(--text-dark);
        flex: 1;
    }

    .accordion-toggle {
        width: 15px;
        height: 8px;
        position: relative;
        flex-shrink: 0;
        margin-left: var(--space-md);
        background-image: url("/assets/medx/img/main/tests/arrow-down.svg");
        cursor: pointer;
    }

    /*.accordion-toggle::before,*/
    /*.accordion-toggle::after {*/
    /*    content: '';*/
    /*    position: absolute;*/
    /*    background-color: #74B5BC;*/
    /*    transition: transform 0.3s;*/
    /*}*/

    /*.accordion-toggle::before {*/
    /*    width: 24px;*/
    /*    height: 2px;*/
    /*    top: 11px;*/
    /*    left: 0;*/
    /*}*/

    /*.accordion-toggle::after {*/
    /*    width: 2px;*/
    /*    height: 24px;*/
    /*    top: 0;*/
    /*    left: 11px;*/
    /*}*/

    .accordion-item.active .accordion-toggle {
        background-image: url("/assets/medx/img/main/tests/arrow-top.svg");
        cursor: pointer;
    }

    .accordion-content {
        /*padding: 0 var(--space-lg);*/
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s, padding 0.3s;
    }

    .accordion-item.active .accordion-content {
        padding-bottom: var(--space-lg);
        max-height: 500px;
    }

    .accordion-answer {
        padding: var(--space-md) 0;
        font-size: 18px;
        color: var(--text-dark);
        line-height: 1.6;
    }

    /* Блок с вопросами */
    .questions-block {
        background: linear-gradient(180deg, var(--light-blue) 0%, var(--dark-blue) 100%);
        border-radius: var(--radius-xl);
        padding: var(--space-lg) var(--space-lg);
        text-align: center;
        position: relative;
        overflow: hidden;
        height: 254px;
        /*width: 1280px;*/
    }

    .questions-title {
        font-size: 28px;
        font-weight: 600;
        color: var(--white);
        text-transform: uppercase;
    }

    .questions-description {
        font-size: 18px;
        color: var(--white);
        max-width: 600px;
        margin: 0 auto 28px;
        line-height: 1.5;
    }

    .btn-support {
        display: inline-flex;
        align-items: center;
        gap: var(--space-sm);
        padding: 12px 40px;
        background-color: var(--bg-light);
        border-radius: var(--radius-md);
        color: var(--primary-blue);
        font-size: 20px;
        font-weight: 600;
        text-decoration: none;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        transition: all 0.2s;
    }

    .btn-support:hover {
        background-color: var(--white);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-support i {
        font-size: 28px;
    }

    .vector-26 {
        position: absolute;
        width: 560px;
        height: 334px;
        left: 968px;
        top: 0;
        background-image: url('/site/modules/medx/showcase/img/vector-26.png');
        background-repeat: no-repeat;
        z-index: 0;
        pointer-events: none;
    }

    .vector-27 {
        position: absolute;
        width: 560px;
        height: 334px;
        left: 0;
        top: 0;
        background-image: url('/site/modules/medx/showcase/img/vector-27.png');
        background-repeat: no-repeat;
        z-index: 0;
        pointer-events: none;
    }

    .tests-input {
        border-radius: 8px;
        border: 1px solid var(--dark-blue-60);
        padding: 10px 16px;
        font-family: 'Involve-Medium';
        font-weight: 500;
        font-size: 14px;
        color: var(--dark-blue-40); !important;
        width: 534px;
        margin-bottom: 12px;
    }

    .creat-tests {
        border-radius: 6px;
        border: none;
        height: 32px;
        width: 232px;
        /*background-color: var(--gradient-primary);*/
        background-color: var(--dark-blue);
        color: var(--white);
        font-family: 'Onest';
        font-size: 14px;
        cursor: pointer;
    }

    .arrow-down {
        background-image: url("/assets/medx/img/main/tests/arrow-down.svg");
        cursor: pointer;
    }

    .arrow-top {
        background-image: url("/assets/medx/img/main/tests/arrow-top.svg");
        cursor: pointer;
    }

    .checkbox-group {
        display: flex;
        gap: 40px;
    }

    label input {
        accent-color: #419AA4;
    }
    /*var(--primary-blue-60)*/
</style>
<body>
~~include file="medx/tpl/main_header.tpl"~
~~include file="medx/tpl/main_mobile_menu.tpl"~
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
        <h1 class="settings-title">ТЕСТЫ</h1>
        <nav class="breadcrumbs">
            <a class="active" href="/main_tests/">СОЗДАТЬ ТЕСТ</a>
            <a href="#">СТАТИСТИКА</a>
            <a href="#">РЕШЁННЫЕ ТЕСТЫ</a>
            <a href="#">ИЗБРАННОЕ</a>
        </nav>
    </section>
    <section class="faq-section">
        <h2 class="section-title">Название</h2>

        <input class="tests-input" placeholder="Придумайте название теста">

        <div class="accordion">
            <div class="accordion-item">
                <div class="accordion-header">
                    <div class="accordion-question">Статус</div>
                    <div class="accordion-toggle"></div>
                </div>
                <div class="accordion-content">
                    ~~*
                    <div class="accordion-answer">
                        Внутри сообщества MEDX взаимодействие строится на открытости и взаимопомощи:
                        участники общаются на форуме, в тематических чатах, участвуют в вебинарах, задают вопросы экспертам и обмениваются опытом.
                        Регулярно проходят онлайн-встречи и дискуссии.
                    </div>
                    *~
                    <div class="checkbox-group">
                        <label><input type="checkbox"> Отвечены верно</label><br>
                        <label><input type="checkbox"> Отвечены неверно</label><br>
                        <label><input type="checkbox"> Ещё нерешённые</label><br>
                       ~~* <label><input type="checkbox"> Выбрать все</label> *~
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <div class="accordion-header">
                    <div class="accordion-question">Сложность</div>
                    <div class="accordion-toggle"></div>
                </div>
                <div class="accordion-content">
                    ~~*
                    <div class="accordion-answer">
                        Да, делиться успехами и результатами не только можно, но и приветствуется!
                        В сообществе создана поддерживающая среда: есть специальные рубрики (например, «Истории успеха»),
                        где участники рассказывают о своих достижениях, вдохновляя других.
                    </div>
                    *~

                    <div class="checkbox-group">
                        <label><input type="checkbox"> Лёгкие</label><br>
                        <label><input type="checkbox"> Средние</label><br>
                        <label><input type="checkbox"> Сложные</label>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <div class="accordion-header">
                    <div class="accordion-question">Дисциплины</div>
                    <div class="accordion-toggle"></div>
                </div>
                <div class="accordion-content">
                    <div class="accordion-answer">
                        Чтобы стать волонтёром, нужно заполнить анкету на сайте или написать в службу поддержки.
                        Волонтёры помогают в организации мероприятий, модерации, создании контента. Отбор проходит
                        по результатам собеседования или после ознакомительного курса.


                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <div class="accordion-header">
                    <div class="accordion-question">Количество вопросов</div>
                    <div class="accordion-toggle"></div>
                </div>
                <div class="accordion-content">
                    <div class="accordion-answer">
                        Максимальное количество вопросов в тесте - 100
                    </div>
                </div>
            </div>
        </div>
        <button class="creat-tests">Создать тест</button>
    </section>
    <section class="questions-block">
        <!-- Декоративные линии -->
        <div class="vector-26"></div>
        <div class="vector-27"></div>
        <h2 class="questions-title">МОТИВАЦИЯ</h2>
        <p class="questions-description">текст текст текст текст текст текст</p>
    </section>


</main>
<script src="/assets/medx/main_script.js"></script>
<script>
    // Активация аккордеона
    document.addEventListener('DOMContentLoaded', function() {
        const accordionItems = document.querySelectorAll('.accordion-item');

        accordionItems.forEach(item => {
            const header = item.querySelector('.accordion-header');

            header.addEventListener('click', () => {
                // Закрываем все другие аккордеоны
                accordionItems.forEach(otherItem => {
                    if (otherItem !== item && otherItem.classList.contains('active')) {
                        otherItem.classList.remove('active');
                    }
                });

                // Переключаем текущий аккордеон
                item.classList.toggle('active');
            });
        });
    });
</script>
~~*
~~include file="medx/tpl/footer.tpl"~
*~
</body>
</html>
