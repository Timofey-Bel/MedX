<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedX учись практикуй расти</title>
    <link rel="stylesheet" href="/assets/medx/main_styles.css">
    <link rel="stylesheet" href="/assets/medx/css/main_header.css">
    <link rel="stylesheet" href="/assets/medx/css/main_settings.css">
</head>
<style>
    .faq-section {
        margin-bottom: var(--space-xl);
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
        margin-left: 20px;
        margin-top: 24px;
    }

    /*.accordion {*/
    /*    background-color: var(--white);*/
    /*    border-radius: var(--radius-lg);*/
    /*    box-shadow: var(--shadow-sm);*/
    /*    overflow: hidden;*/
    /*}*/

    .accordion-item {
        border-bottom: 1px solid var(--light-blue);
        margin: 0 20px;
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
        width: 24px;
        height: 24px;
        position: relative;
        flex-shrink: 0;
        margin-left: var(--space-md);
    }

    .accordion-toggle::before,
    .accordion-toggle::after {
        content: '';
        position: absolute;
        background-color: #74B5BC;
        transition: transform 0.3s;
    }

    .accordion-toggle::before {
        width: 24px;
        height: 2px;
        top: 11px;
        left: 0;
    }

    .accordion-toggle::after {
        width: 2px;
        height: 24px;
        top: 0;
        left: 11px;
    }

    .accordion-item.active .accordion-toggle::after {
        transform: rotate(90deg);
    }

    .accordion-content {
        padding: 0 var(--space-lg);
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

</style>
<body>
~~include file="medx/tpl/main_header.tpl"~
~~include file="medx/tpl/main_mobile_menu.tpl"~
<!-- Затемняющий фон -->
<div class="overlay" id="overlay"></div>
<!-- Модальное окно (скрыто по умолчанию) -->

<main>
    <section>
        <h1 class="settings-title">НАСТРОЙКИ</h1>
        <nav class="breadcrumbs">
            <a href="/main_settings/">МОЙ ПРОФИЛЬ</a>
            <a href="/design_settings/">ОФОРМЛЕНИЕ</a>
            <a class="active" href="/support_settings/">ПОДДЕРЖКА</a>
            <a href="#">Q&A</a>
            <a href="#">О НАС</a>
            <a href="#">КОНТАКТЫ</a>
        </nav>
    </section>
    <section>
        <h2>Собрали вопросы в одном месте
            <a href="#">на странице FAQ.</a>
            Возможно, там найдётся ответ и на твой вопрос:)</h2>
    </section>
    <section class="faq-section">
        <h2 class="section-title">Самые популярные из них:</h2>

        <div class="accordion">
            <div class="accordion-item">
                <div class="accordion-header">
                    <div class="accordion-question">Как устроено взаимодействие внутри сообщества MedX?</div>
                    <div class="accordion-toggle"></div>
                </div>
                <div class="accordion-content">
                    <div class="accordion-answer">
                        Внутри сообщества MEDX взаимодействие строится на открытости и взаимопомощи:
                        участники общаются на форуме, в тематических чатах, участвуют в вебинарах, задают вопросы экспертам и обмениваются опытом.
                        Регулярно проходят онлайн-встречи и дискуссии.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <div class="accordion-header">
                    <div class="accordion-question">Можно ли делиться успехами и результатами в MedX?</div>
                    <div class="accordion-toggle"></div>
                </div>
                <div class="accordion-content">
                    <div class="accordion-answer">
                        Да, делиться успехами и результатами не только можно, но и приветствуется!
                        В сообществе создана поддерживающая среда: есть специальные рубрики (например, «Истории успеха»),
                        где участники рассказывают о своих достижениях, вдохновляя других.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <div class="accordion-header">
                    <div class="accordion-question">Как пользователи могут стать волонтёрами MedX?</div>
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
                    <div class="accordion-question">Есть ли у MedX оффлайн-встречи и мероприятия?</div>
                    <div class="accordion-toggle"></div>
                </div>
                <div class="accordion-content">
                    <div class="accordion-answer">
                        Да, MEDX проводит офлайн-встречи и мероприятия в разных городах, а также участвует в профильных конференциях.
                        Анонсы публикуются в календаре на сайте и в соцсетях. Участники могут предлагать свои города для встреч.
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="questions-block">
        <!-- Декоративные линии -->
        <div class="vector-26"></div>
        <div class="vector-27"></div>
        <h2 class="questions-title">НЕ НАШЛОСЬ ОТВЕТА НА ТВОЙ ВОПРОС?</h2>
        <p class="questions-description">Напишите нам в чат поддержки наша команда с радостью поможет!</p>
        <a href="#" class="btn-support">
            <i class="fab fa-telegram"></i>
            ЧАТ ПОДДЕРЖКИ
        </a>
    </section>
</main>
~~*<script src="/assets/medx/main_script.js"></script>*~
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
