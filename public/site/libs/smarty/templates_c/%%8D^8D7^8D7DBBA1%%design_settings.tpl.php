<?php /* Smarty version 2.6.11, created on 2026-03-09 18:32:22
         compiled from medx/design_settings/design_settings.tpl */ ?>
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
    .topics-section {
        position: relative;
        padding: 28px;
        background-color: var(--white);
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        max-width: 1280px;
        height: 383px;
    }

    .topics-section h2 {
        font-size: 20px;
        font-family: "Involve-Bold", Helvetica;
        color: #027B8E;

    }

    .topics-section h2 {
        font-size: 20px;
        font-family: "Involve-Bold", Helvetica;
        color: #027B8E;

    }

    .topics {
        display: flex;
        gap: 56px;
    }

    .topic {
        display: inline-grid;
    }

    .topic img{
        width: 372px;
        height: 215px;
        border-radius: 4px;
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

<main>
    <section>
        <h1 class="settings-title">НАСТРОЙКИ</h1>
        <nav class="breadcrumbs">
            <a href="/main_settings/">МОЙ ПРОФИЛЬ</a>
            <a class="active" href="/design_settings/">ОФОРМЛЕНИЕ</a>
            <a href="/support_settings/">ПОДДЕРЖКА</a>
            <a href="#">Q&A</a>
            <a href="#">О НАС</a>
            <a href="#">КОНТАКТЫ</a>
        </nav>
    </section>
    <section class="topics-section">
        <h2>Выбор темы:</h2>
        <div class="topics">
            <div class="topic">
                <img class="light" alt="light_topic" src="/assets/medx/img/main/settings/light_topic.png">
                <input type="checkbox">светлая
            </div>
            <div class="topic">
                <img class="dark" alt="dark_topic" src="/assets/medx/img/main/settings/dark_topic.png">
                <input type="checkbox">тёмная
            </div>
            <div class="topic">
                <img class="auto" alt="auto_topic" src="/assets/medx/img/main/settings/auto_topic.png">
                <input type="checkbox">автоматическая
            </div>
        </div>
    </section>
</main>
<script src="/assets/medx/main_script.js"></script>
</body>
</html>