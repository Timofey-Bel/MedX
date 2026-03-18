@extends('layouts.app')

@section('title', 'MedX учись практикуй расти')

@section('styles')
    <link rel="stylesheet" href="{{ asset('site/modules/medx/showcase/style_fixed.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/medx/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/medx/css/header.css') }}">
@endsection

@section('content')
@include('partials.header')
<main>
    <section class="mainblock">
        <div class="mainblock-bg"></div>
        <div class="textmain">
            <img class="logo-2" src="{{ asset('site/modules/medx/showcase/img/logo-2@3x.png') }}"/>
            <p class="h">ИНТЕРАКТИВНАЯ ОБРАЗОВАТЕЛЬНАЯ ПЛАТФОРМА ДЛЯ МЕДИКОВ</p>
            <p class="h-2">Знания, практика и сообщество <br/>для студентов в один клик</p>
        </div>
        <div class="btn_free">
            <div class="text-wrapper-29">НАЧАТЬ БЕСПЛАТНО</div>
        </div>
    </section>
    <section class="conveyor-container">
        <div class="conveyor-bar">
            <p class="element">РЕГИСТРИРУЙСЯ СЕЙЧАС И ПОЛУЧИ ДОСТУП КО ВСЕМ МАТЕРИАЛАМ БЕСПЛАТНО НА 3 ДНЯ</p>
            <img class="vector-8" src="{{ asset('site/modules/medx/showcase/img/vector-11.png') }}"/>
            <p class="element">РЕГИСТРИРУЙСЯ СЕЙЧАС И ПОЛУЧИ ДОСТУП КО ВСЕМ МАТЕРИАЛАМ БЕСПЛАТНО НА 3 ДНЯ</p>
            <img class="vector-8" src="{{ asset('site/modules/medx/showcase/img/vector-11.png') }}"/>

            <!-- Дублируем контент для бесшовной анимации -->
            <p class="element">РЕГИСТРИРУЙСЯ СЕЙЧАС И ПОЛУЧИ ДОСТУП КО ВСЕМ МАТЕРИАЛАМ БЕСПЛАТНО НА 3 ДНЯ</p>
            <img class="vector-8" src="{{ asset('site/modules/medx/showcase/img/vector-11.png') }}"/>
            <p class="element">РЕГИСТРИРУЙСЯ СЕЙЧАС И ПОЛУЧИ ДОСТУП КО ВСЕМ МАТЕРИАЛАМ БЕСПЛАТНО НА 3 ДНЯ</p>
            <img class="vector-8" src="{{ asset('site/modules/medx/showcase/img/vector-11.png') }}"/>
        </div>
    </section>
    <section class="block-about-us">
        <div class="text">
            <div class="text-2">
                <p class="med-x-2">ДОБРО ПОЖАЛОВАТЬ В MEDX —</p>
                <p class="text-wrapper-17">ТВОЙ ИНСТРУМЕНТ ДЛЯ ЭФФЕКТИВНОГО И ПРОДУКТИВНОГО ОБУЧЕНИЯ</p>
                <p class="text-wrapper-18">Забудь о скучных учебниках, утомительном поиске и хаосе в интернете.</p>
                <p class="text-wrapper-19">
                    MedX — это структурированная база знаний, где эксклюзивные материалы созданы и собраны в одном
                    месте,
                    чтобы ты мог учиться удобно и интересно
                </p>
            </div>
        </div>
        <div class="scroll-cards">
            <div class="card-wrapper">
                <div class="frame-wrapper">
                    <div class="frame-19">
                        <div class="rectangle-4"></div>
                        <div class="frame-20">
                            <div class="text-wrapper-20">21.09.2025</div>
                            <div class="text-wrapper-21">СТРОЕНИЕ ЧЕРЕПА</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-wrapper">
                <div class="card-4">
                    <div class="frame-19">
                        <div class="rectangle-4"></div>
                        <div class="frame-20">
                            <div class="text-wrapper-22">21.09.2025</div>
                            <div class="text-wrapper-21">СТРОЕНИЕ ЧЕРЕПА</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-wrapper">
                <div class="card-5">
                    <div class="frame-19">
                        <div class="rectangle-4"></div>
                        <div class="frame-20">
                            <div class="text-wrapper-22">21.09.2025</div>
                            <div class="text-wrapper-21">СТРОЕНИЕ ЧЕРЕПА</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="block-disciplines">
        <p class="text-wrapper-23">ИЗУЧИ ВСЕ ДИСЦИПЛИНЫ ВМЕСТЕ С НАМИ</p>
        <div class="view-3">
            <div class="text-wrapper-24">ФУНДАМЕНТАЛЬНЫЕ ДИСЦИПЛИНЫ:</div>
            <div class="btns">
                <div class="frame-21">
                    <div class="view-4">
                        <img class="img-2" src="{{ asset('site/modules/medx/showcase/img/ecg-1@2x.png') }}"/>
                        <div class="text-wrapper-25">Патологическая физиология</div>
                    </div>
                    <div class="view-4">
                        <img class="img-2" src="{{ asset('site/modules/medx/showcase/img/pill-1@2x.png') }}"/>
                        <div class="text-wrapper-25">Фармакология</div>
                    </div>
                    <div class="view-4">
                        <img class="img-2" src="{{ asset('site/modules/medx/showcase/img/amoeba-1@2x.png') }}"/>
                        <div class="text-wrapper-25">Медицинская биология</div>
                    </div>
                </div>
                <div class="frame-22">
                    <div class="view-4">
                        <img class="img-2" src="{{ asset('site/modules/medx/showcase/img/dna-2@2x.png') }}"/>
                        <div class="text-wrapper-25">Медицинская генетика</div>
                    </div>
                    <div class="view-4">
                        <img class="img-2" src="{{ asset('site/modules/medx/showcase/img/ultrasound-1@2x.png') }}"/>
                        <div class="text-wrapper-25">Медицинская физика</div>
                    </div>
                </div>
                <div class="view-5">
                    <img class="img-2" src="{{ asset('site/modules/medx/showcase/img/muscle-cell-1@2x.png') }}"/>
                    <div class="text-wrapper-25">Гистология</div>
                </div>
                <div class="view-6">
                    <img class="img-2" src="{{ asset('site/modules/medx/showcase/img/embryo-1@2x.png') }}"/>
                    <div class="text-wrapper-25">Эмбриология</div>
                </div>
                <div class="view-7">
                    <img class="img-2" src="{{ asset('site/modules/medx/showcase/img/follicle-1@2x.png') }}"/>
                    <div class="text-wrapper-25">Цитология</div>
                </div>
                <div class="view-8">
                    <img class="img-2" src="{{ asset('site/modules/medx/showcase/img/red-blood-cells-1@2x.png') }}"/>
                    <div class="text-wrapper-25">Физиология</div>
                </div>
                <div class="view-9">
                    <img class="img-2" src="{{ asset('site/modules/medx/showcase/img/biochemistry@2x.png') }}"/>
                    <div class="text-wrapper-25">Биохимия</div>
                </div>
                <div class="view-10">
                    <img class="img-2" src="{{ asset('site/modules/medx/showcase/img/microbiology-1@2x.png') }}"/>
                    <div class="text-wrapper-25">Микробиология</div>
                </div>
                <div class="view-11">
                    <img class="cell" src="{{ asset('site/modules/medx/showcase/img/cell-1@2x.png') }}"/>
                    <div class="text-wrapper-25">Иммунология</div>
                </div>
                <div class="view-12">
                    <img class="img-2" src="{{ asset('site/modules/medx/showcase/img/biopsy-1@2x.png') }}"/>
                    <div class="text-wrapper-25">Патологическая анатомия</div>
                </div>
                <div class="view-13">
                    <img class="img-2" src="{{ asset('site/modules/medx/showcase/img/human-organs-1@2x.png') }}"/>
                    <div class="text-wrapper-25">Анатомия</div>
                </div>
            </div>
        </div>
        <div class="group">
            <div class="text-wrapper-26">КЛИНИЧЕСКИЕ ДИСЦИПЛИНЫ:</div>
            <div class="btns-2">
                <div class="frame-23">
                    <div class="view-14">
                        <img class="img-2" src="{{ asset('site/modules/medx/showcase/img/heart-3-1@2x.png') }}"/>
                        <div class="text-wrapper-25">Кардиология</div>
                    </div>
                    <div class="view-14">
                        <img class="img-2" src="{{ asset('site/modules/medx/showcase/img/lungs-2@2x.png') }}"/>
                        <div class="text-wrapper-25">Пульмонология</div>
                    </div>
                    <div class="view-14">
                        <img class="img-2" src="{{ asset('site/modules/medx/showcase/img/stomach-2@2x.png') }}"/>
                        <div class="text-wrapper-25">Гастроэнтерология</div>
                    </div>
                    <div class="view-14">
                        <img class="img-2" src="{{ asset('site/modules/medx/showcase/img/thyroid-2@2x.png') }}"/>
                        <div class="text-wrapper-25">Эндокринология</div>
                    </div>
                </div>
                <div class="frame-24">
                    <div class="view-14">
                        <img class="img-2" src="{{ asset('site/modules/medx/showcase/img/kidneys@2x.png') }}"/>
                        <div class="text-wrapper-25">Нефрология</div>
                    </div>
                    <div class="view-14">
                        <img class="img-2" src="{{ asset('site/modules/medx/showcase/img/blood-vessel-1@2x.png') }}"/>
                        <div class="text-wrapper-25">Гематология</div>
                    </div>
                    <div class="view-15">
                        <img class="joint" src="{{ asset('site/modules/medx/showcase/img/joint-1@2x.png') }}"/>
                        <div class="text-wrapper-27">Ревматология</div>
                    </div>
                    <div class="view-16">
                        <img class="img-2" src="{{ asset('site/modules/medx/showcase/img/bacterias-1@2x.png') }}"/>
                        <div class="text-wrapper-25">Инфекционные болезни</div>
                    </div>
                </div>
                <div class="frame-25">
                    <div class="view-14">
                        <img class="img-2" src="{{ asset('site/modules/medx/showcase/img/surgery-1@2x.png') }}"/>
                        <div class="text-wrapper-25">Общая хирургия</div>
                    </div>
                    <div class="view-14">
                        <img class="img-2" src="{{ asset('site/modules/medx/showcase/img/eye@2x.png') }}"/>
                        <div class="text-wrapper-25">Офтальмология</div>
                    </div>
                    <div class="view-14">
                        <img class="img-2" src="{{ asset('site/modules/medx/showcase/img/ear-6326320@2x.png') }}"/>
                        <div class="text-wrapper-25">Оториноларингология</div>
                    </div>
                    <div class="view-14">
                        <img class="img-2" src="{{ asset('site/modules/medx/showcase/img/bladder-2@2x.png') }}"/>
                        <div class="text-wrapper-25">Урология</div>
                    </div>
                </div>
                <div class="frame-26">
                    <div class="view-14">
                        <img class="img-2" src="{{ asset('site/modules/medx/showcase/img/surgery-2@2x.png') }}"/>
                        <div class="text-wrapper-25">Сердечно-сосудистая хирургия</div>
                    </div>
                    <div class="view-14">
                        <img class="img-2" src="{{ asset('site/modules/medx/showcase/img/neurosurgery-2-1@2x.png') }}"/>
                        <div class="text-wrapper-25">Нейрохирургия</div>
                    </div>
                    <div class="view-14">
                        <img class="img-2" src="{{ asset('site/modules/medx/showcase/img/uterus@2x.png') }}"/>
                        <div class="text-wrapper-25">Акушерство и гинекология</div>
                    </div>
                </div>
                <div class="frame-27">
                    <div class="view-14">
                        <img class="img-2" src="{{ asset('site/modules/medx/showcase/img/electrocardiogram-1@2x.png') }}"/>
                        <div class="text-wrapper-25">Анестезиология и реаниматология</div>
                    </div>
                    <div class="view-14">
                        <img class="img-2" src="{{ asset('site/modules/medx/showcase/img/spine-1@2x.png') }}"/>
                        <div class="text-wrapper-25">Травматология и ортопедия</div>
                    </div>
                    <div class="view-14">
                        <img class="img-2" src="{{ asset('site/modules/medx/showcase/img/cancer-cell-1@2x.png') }}"/>
                        <div class="text-wrapper-25">Онкология</div>
                    </div>
                </div>
                <div class="frame-28">
                    <div class="view-14">
                        <img class="img-2" src="{{ asset('site/modules/medx/showcase/img/rib-cage-1@2x.png') }}"/>
                        <div class="text-wrapper-25">Торакальная хирургия</div>
                    </div>
                    <div class="view-14">
                        <img class="img-2" src="{{ asset('site/modules/medx/showcase/img/hair@2x.png') }}"/>
                        <div class="text-wrapper-25">Дерматовенерология</div>
                    </div>
                    <div class="view-14">
                        <img class="img-2" src="{{ asset('site/modules/medx/showcase/img/baby-1@2x.png') }}"/>
                        <div class="text-wrapper-25">Педиатрия</div>
                    </div>
                </div>
                <div class="frame-29">
                    <div class="view-17">
                        <img class="img-3" src="{{ asset('site/modules/medx/showcase/img/neuron-1@2x.png') }}"/>
                        <div class="text-wrapper-28">Неврология</div>
                    </div>
                    <div class="view-17">
                        <img class="img-3" src="{{ asset('site/modules/medx/showcase/img/psychologist-1@2x.png') }}"/>
                        <div class="text-wrapper-28">Психиатрия</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="block">
        <div class="card">
            <img class="vector-5" src="{{ asset('site/modules/medx/showcase/img/vector-17.png') }}"/>
            <div class="text-card">
                <div class="text-wrapper">БУДУЩЕЕ ВРАЧА НАЧИНАЕТСЯ ЗДЕСЬ</div>
                <p class="div">
                    Эксклюзивные материалы для быстрого понимания сложных тем, успешной сдачи экзаменов и
                    профессионального
                    роста.
                </p>
                <button class="btn">
                    <div class="text-wrapper-2">ПОЛУЧИТЬ ДОСТУП</div>
                </button>
            </div>
            <div class="video-card"></div>
        </div>
        <div class="card-2">
            <div class="background-card"></div>
            <img class="vector-6" src="{{ asset('site/modules/medx/showcase/img/vector-18@4x.png') }}"/>
            <div class="video-card-2"></div>
            <div class="text-card-2">
                <div class="text-wrapper-3">ТРЕНИРОВКА ЗНАНИЙ КАЖДЫЙ ДЕНЬ</div>
                <p class="p">
                    Клинические задачи и тесты, которые двигают тебя вперёд, помогают видеть прогресс и
                    достигать новых
                    вершин.
                </p>
                <button class="div-wrapper">
                    <div class="text-wrapper-4">ОТСЛЕЖИВАТЬ СВОЙ ПРОГРЕСС</div>
                </button>
            </div>
        </div>
        <div class="card-3">
            <img class="vector-7" src="{{ asset('site/modules/medx/showcase/img/vector-19.png') }}"/>
            <div class="video-card"></div>
            <div class="text-card-3">
                <p class="text-wrapper-5">СООБЩЕСТВО МЕДИКОВ, КОТОРОЕ ВСЕГДА С ТОБОЙ</p>
                <p class="text-wrapper-6">
                    Общайся с единомышленниками, делись опытом дежурств, находи поддержку и будь частью
                    медицинского
                    сообщества.
                </p>
                <button class="medx-wrapper">
                    <div class="text-wrapper-2">СТАТЬ ЧАСТЬЮ MEDX</div>
                </button>
            </div>
        </div>
    </section>
    <section class="block-learn">
        <div class="text-wrapper-7">УЧИСЬ. ПРАКТИКУЙ. РАСТИ</div>
        <div class="frame">
            <div class="rectangle"></div>
            <div class="frame-2">
                <div class="text-wrapper-8">100+ АВТОРСКИХ МАТЕРИАЛОВ</div>
                <p class="text-wrapper-9">Живые статьи и разборы по всем дисциплинам — от анатомии <br/>до
                    терапии</p>
            </div>
        </div>
        <div class="frame-3">
            <div class="rectangle-2"></div>
            <div class="frame-4">
                <div class="text-wrapper-8">КАРТОЧКИ ДЛЯ ПОВТОРЕНИЯ</div>
                <p class="text-wrapper-9">
                    Повторяй термины и вопросы <br/>в удобной форме — знания остаются с тобой навсегда
                </p>
            </div>
        </div>
        <div class="frame-5">
            <div class="rectangle-2"></div>
            <div class="frame-6">
                <p class="element-3">3000+ КЛИНИЧЕСКИХ ЗАДАЧ И ТЕСТОВ</p>
                <p class="USMLE">
                    <span class="span">Готовься к экзаменам любого уровня, включая </span>
                    <span class="text-wrapper-10">USMLE</span>
                </p>
            </div>
        </div>
        <div class="frame-7">
            <div class="rectangle-3"></div>
            <div class="frame-8">
                <div class="text-wrapper-11">ВИДЕО-ЛЕКЦИИ</div>
                <p class="text-wrapper-12">Глубокие объяснения сложных тем простыми словами</p>
            </div>
        </div>
        <div class="frame-9">
            <div class="rectangle-3"></div>
            <div class="frame-10">
                <div class="text-wrapper-11">ПОДКАСТЫ</div>
                <p class="text-wrapper-9">
                    Слушай врачей и экспертов <br/>в дороге, на пробежке <br/>или во время дежурства
                </p>
            </div>
        </div>
        <div class="frame-11">
            <div class="rectangle"></div>
            <div class="frame-12">
                <div class="text-wrapper-11">СООБЩЕСТВО И ЧАТЫ</div>
                <p class="text-wrapper-13">Задавай вопросы, делись опытом и находи поддержку</p>
            </div>
        </div>
        <div class="frame-13">
            <div class="rectangle"></div>
            <div class="frame-14">
                <div class="text-wrapper-14">ДЕЖУРСТВА <br/>И ВОЛОНТЁРСТВО</div>
                <p class="text-wrapper-15">Обменивайся историями, учись у других, вдохновляйся практикой</p>
            </div>
        </div>
        <div class="frame-15">
            <div class="rectangle-3"></div>
            <div class="frame-16">
                <div class="text-wrapper-11">ПРОГРЕСС И РЕЙТИНГИ</div>
                <p class="text-wrapper-16">Следи за своими результатами <br/>и мотивируй себя челленджами</p>
            </div>
        </div>
        <div class="frame-17">
            <div class="rectangle-2"></div>
            <div class="frame-18">
                <div class="AI">ПЕРСОНАЛЬНЫЕ РЕКОМЕНДАЦИИ (AI)</div>
                <p class="text-wrapper-9">Учись умнее: система подскажет, что повторить, <br/>а что изучить
                    дальше</p>
            </div>
        </div>
    </section>
    <section class="frame-30">
        <div class="text-wrapper-29">НАЧАТЬ БЕСПЛАТНО</div>
    </section>
    <section class="under">
        <p class="med-x">MEDX ВСЕГДА С СОБОЙ.<br/>УЧИСЬ НА ЛЮБОМ УСТРОЙСТВЕ.</p>
        <img class="free-mobile-friendly" src="{{ asset('site/modules/medx/showcase/img/devices.png') }}"/>
    </section>
</main>
@include('partials.footer')
@endsection
