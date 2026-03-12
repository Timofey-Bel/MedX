<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedX учись практикуй расти</title>
    <link rel="stylesheet" href="/assets/medx/main_styles.css">
    <link rel="stylesheet" href="/assets/medx/css/main_header.css">
{*    <link rel="stylesheet" href="/assets/medx/css/main_settings.css">*}
</head>
<style>

</style>
<body>
~~include file="medx/tpl/main_header.tpl"~
~~include file="medx/tpl/main_mobile_menu.tpl"~
<!-- Затемняющий фон -->
<div class="overlay" id="overlay"></div>
<!-- Модальное окно (скрыто по умолчанию) -->

<main>
    <section>
        <h1 class="settings-title">СООБЩЕСТВО</h1>
        <nav class="breadcrumbs">
            <a class="active" href="/main_community/">ПРО СООБЩЕСТВО</a>
            <a href="/main_rules/">ПРАВИЛА</a>
            <a href="/main_confidentiality/">КОНФИДЕЦИАЛЬНОСТЬ</a>
        </nav>
    </section>
    <section>
        <h2>MedX – это не только образовательная платформа с материалами для студентов-медиков, но и сообщество молодых,
            замотивированных и амбициозных  специалистов, которые готовы поделиться своим опытом.</h2>
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
