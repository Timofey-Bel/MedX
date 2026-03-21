@extends('layouts.app')

@section('title', 'MedX База знаний - Клинические дисциплины')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/main_styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main_header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main_base.css') }}">
@endsection

@section('content')
    @include('partials.main_header')
    @include('partials.main_mobile_menu')

    <div class="overlay" id="overlay"></div>

    <main>
        <section>
            <h1 class="breadcrumbs-title">База знаний</h1>
            <nav class="breadcrumbs">
                <a href="{{ url('/main_base') }}">Фундаментальные дисциплины</a>
                <a class="active" href="{{ url('/main_base_clinical') }}">Клинические дисциплины</a>
                <a href="#">Медфакты</a>
                <a href="#">Изучённые</a>
                <a href="#"><i class="heart-icon-small"></i> Избранное</a>
            </nav>
        </section>

        <section class="disciplines-tags">
            <button class="tag-btn">
                <img src="{{ asset('images/medical/heart-3-1@2x.png') }}" alt="Кардиология" class="tag-icon">
                Кардиология
            </button>
            <button class="tag-btn">
                <img src="{{ asset('images/medical/lungs-2@2x.png') }}" alt="Пульмонология" class="tag-icon">
                Пульмонология
            </button>
            <button class="tag-btn">
                <img src="{{ asset('images/medical/stomach-2@2x.png') }}" alt="Гастроэнтерология" class="tag-icon">
                Гастроэнтерология
            </button>
            <button class="tag-btn">
                <img src="{{ asset('images/medical/dna-2@2x.png') }}" alt="Эндокринология" class="tag-icon">
                Эндокринология
            </button>
            <button class="tag-btn">
                <img src="{{ asset('images/medical/kidneys@2x.png') }}" alt="Нефрология" class="tag-icon">
                Нефрология
            </button>
            <button class="tag-btn">
                <img src="{{ asset('images/medical/red-blood-cells-1@2x.png') }}" alt="Гематология" class="tag-icon">
                Гематология
            </button>
            <button class="tag-btn">
                <img src="{{ asset('images/medical/rib-cage-1@2x.png') }}" alt="Ревматология" class="tag-icon">
                Ревматология
            </button>
            <button class="tag-btn">
                <img src="{{ asset('images/medical/bacterias-1@2x.png') }}" alt="Инфекционные болезни" class="tag-icon">
                Инфекционные болезни
            </button>
            <button class="tag-btn">
                <img src="{{ asset('images/medical/surgery-1@2x.png') }}" alt="Общая хирургия" class="tag-icon">
                Общая хирургия
            </button>
            <button class="tag-btn">
                <img src="{{ asset('images/medical/eye@2x.png') }}" alt="Офтальмология" class="tag-icon">
                Офтальмология
            </button>
            <button class="tag-btn">
                <img src="{{ asset('images/medical/ear-6326320@2x.png') }}" alt="Оториноларингология" class="tag-icon">
                Оториноларингология
            </button>
            <button class="tag-btn">
                <img src="{{ asset('images/medical/bladder-2@2x.png') }}" alt="Урология" class="tag-icon">
                Урология
            </button>
            <button class="tag-btn">
                <img src="{{ asset('images/medical/heart-3-1@2x.png') }}" alt="Сердечно-сосудистая хирургия" class="tag-icon">
                Сердечно-сосудистая хирургия
            </button>
            <button class="tag-btn">
                <img src="{{ asset('images/medical/neuron-1@2x.png') }}" alt="Неврология" class="tag-icon">
                Неврология
            </button>
            <button class="tag-btn">
                <img src="{{ asset('images/medical/psychologist-1@2x.png') }}" alt="Акушерство и гинекология" class="tag-icon">
                Акушерство и гинекология
            </button>
            <button class="tag-btn">
                <img src="{{ asset('images/medical/surgery-2@2x.png') }}" alt="Анестезиология и реаниматология" class="tag-icon">
                Анестезиология и реаниматология
            </button>
            <button class="tag-btn">
                <img src="{{ asset('images/medical/baby-1@2x.png') }}" alt="Травматология и ортопедия" class="tag-icon">
                Травматология и ортопедия
            </button>
            <button class="tag-btn">
                <img src="{{ asset('images/medical/cancer-cell-1@2x.png') }}" alt="Онкология" class="tag-icon">
                Онкология
            </button>
            <button class="tag-btn">
                <img src="{{ asset('images/medical/rib-cage-1@2x.png') }}" alt="Нормальная анатомия" class="tag-icon">
                Торакальная хирургия
            </button>
            <button class="tag-btn">
                <img src="{{ asset('images/medical/hair@2x.png') }}" alt="Дерматовенерология" class="tag-icon">
                Дерматовенерология
            </button>
            <button class="tag-btn">
                <img src="{{ asset('images/medical/thyroid-2@2x.png') }}" alt="Педиатрия" class="tag-icon">
                Педиатрия
            </button>
            <button class="tag-btn">
                <img src="{{ asset('images/medical/neuron-1@2x.png') }}" alt="Неврология" class="tag-icon">
                Неврология
            </button>
            <button class="tag-btn">
                <img src="{{ asset('images/medical/psychologist-1@2x.png') }}" alt="Психиатрия" class="tag-icon">
                Психиатрия
            </button>
        </section>

        <section class="knowledge-grid">
            <div class="knowledge-card">
                <div class="card-image">
                    <img src="{{ asset('images/base/clinical_base/Rectangle-98.png') }}" alt="Психиатрия">
                </div>
                <div class="card-info">
                    <span class="card-category">Психиатрия /</span>
                    <h3 class="card-title">Биологические, психологические и соц. факторы психических расстройств</h3>
                </div>
            </div>

            <div class="knowledge-card">
                <div class="card-image">
                    <img src="{{ asset('images/base/clinical_base/Rectangle-99.png') }}" alt="Гастроэнтерология">
                </div>
                <div class="card-info">
                    <span class="card-category">Гастроэнтерология /</span>
                    <h3 class="card-title">Гепатиты и цирроз печени</h3>
                </div>
            </div>

            <div class="knowledge-card">
                <div class="card-image">
                    <img src="{{ asset('images/base/clinical_base/Rectangle-100.png') }}" alt="Сердечно-сосудистая хирургия">
                </div>
                <div class="card-info">
                    <span class="card-category">Сердечно-сосудистая хирургия /</span>
                    <h3 class="card-title">Основы ССХ: анатомия и физиология сердца и магистральных сосудов...</h3>
                </div>
            </div>

            <div class="knowledge-card">
                <div class="card-image">
                    <img src="{{ asset('images/base/clinical_base/Rectangle-123.png') }}" alt="Акушерство и гинекология">
                </div>
                <div class="card-info">
                    <span class="card-category">Акушерство и гинекология /</span>
                    <h3 class="card-title">Контрацепция и методы планирования семьи: гормональные методы; барьерные...</h3>
                </div>
            </div>

            <div class="knowledge-card">
                <div class="card-image">
                    <img src="{{ asset('images/base/clinical_base/Rectangle-124.png') }}" alt="Кардиология">
                </div>
                <div class="card-info">
                    <span class="card-category">Кардиология /</span>
                    <h3 class="card-title">Врождённые и приобретённые пороки сердца: стенозы и недостаточности...</h3>
                </div>
            </div>

            <div class="knowledge-card">
                <div class="card-image">
                    <img src="{{ asset('images/base/clinical_base/Rectangle-125.png') }}" alt="Дерматовенерология">
                </div>
                <div class="card-info">
                    <span class="card-category">Дерматовенерология /</span>
                    <h3 class="card-title">Гнойничковые заболевания кожи (пиодермии)</h3>
                </div>
            </div>
        </section>

        <div class="load-more">
            <button class="btn-load-more">Загрузить ещё</button>
        </div>
    </main>
@endsection

@section('scripts')
    <script src="{{ asset('assets/medx/main_script.js') }}"></script>
@endsection
